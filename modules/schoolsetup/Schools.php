<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  openSIS is  web-based, open source, and comes packed with features that 
#  include student demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  This program is released under the terms of the GNU General Public License as  
#  published by the Free Software Foundation, version 2 of the License. 
#  See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#***************************************************************************************
include('../../RedirectModulesInc.php');
unset($_SESSION['_REQUEST_vars']['values']);unset($_SESSION['_REQUEST_vars']['modfunc']);
DrawBC("School Setup > ".ProgramTitle());
// --------------------------------------------------------------- Test SQL ------------------------------------------------------------------ //

// --------------------------------------------------------------- Tset SQL ------------------------------------------------------------------ //

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update' && (clean_param($_REQUEST['button'],PARAM_ALPHAMOD)=='Save' || clean_param($_REQUEST['button'],PARAM_ALPHAMOD)=='Update' || clean_param($_REQUEST['button'],PARAM_ALPHAMOD)==''))
{
	if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && $_POST['values'] && User('PROFILE')=='admin')
	{
		if($_REQUEST['new_school']!='true')
		{
			$sql = 'UPDATE schools SET ';

			foreach($_REQUEST['values'] as $column=>$value)
			{
                            if(substr($column,0,6)=='CUSTOM'){
                                                                                $custom_id=str_replace("CUSTOM_","",$column);
                                                                                $custom_RET=DBGet(DBQuery("SELECT TITLE,TYPE FROM school_custom_fields WHERE ID=".$custom_id));

                                                                                $custom=DBGet(DBQuery("SHOW COLUMNS FROM schools WHERE FIELD='".$column."'"));
                                                                                $custom=$custom[1];
                                                                                if($custom['NULL']=='NO' && trim($value)=='' && $custom['DEFAULT']){
                                                                                    $value=$custom['DEFAULT'];
                                                                                }else if($custom['NULL']=='NO' && $value==''){
                                                                                    $custom_TITLE=$custom_RET[1]['TITLE'];
                                                                                    echo "<font color=red><b>Unable to save data, because ".$custom_TITLE.' is required.</b></font><br/>';
                                                                                    $error=true;
                                                                                }else if($custom_RET[1]['TYPE']=='numeric' &&  (!is_numeric($value) && $value!='')){
                                                                                    $custom_TITLE=$custom_RET[1]['TITLE'];
                                                                                    echo "<font color=red><b>Unable to save data, because ".$custom_TITLE.' is Numeric type.</b></font><br/>';
                                                                                    $error=true;
                                                                                }else{
                                                                                    $m_custom_RET=DBGet(DBQuery("select ID,TITLE,TYPE from school_custom_fields WHERE ID='".$custom_id."' AND TYPE='multiple'"));
                                                                                    if($m_custom_RET)
                                                                                    {
                                                                                        $str="";
                                                                                        foreach($value as $m_custom_val)
                                                                                        {
                                                                                            if($m_custom_val)
                                                                                            $str.="||".$m_custom_val;
                                                                                        }
                                                                                        if($str)
                                                                                        $value=$str."||";
                                                                                        else {
                                                                                             $value='';
                                                                                    }
                                                                                    }

                                                                                    
                                                                                }  
					
				}  ###Custom Ends#####
                                $value=paramlib_validation($column,trim($value));
                                if(stripos($_SERVER['SERVER_SOFTWARE'], 'linux'))
                                {
                                $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",trim($value))).'\',';
                             
                                }
                                else
                                {
				$sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",trim($value))).'\',';
                                }

                        }
			$sql = substr($sql,0,-1) . ' WHERE ID=\''.UserSchool().'\'';

			DBQuery($sql);
			echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>'; 
			$note[] = 'This school has been modified.';
                                                      $_REQUEST['modfunc'] = '';
		}
		else
		{
			$fields = $values = '';

			foreach($_REQUEST['values'] as $column=>$value)
				if($column!='ID' && $value)
				{
                    $value=paramlib_validation($column,trim($value));
					$fields .= ','.$column;
					$values .= ",\"".str_replace("'","''",str_replace("\'","''",trim($value)))." \"";
				}

			if($fields && $values)
			{
				
				
                        $id = DBGet(DBQuery('SHOW TABLE STATUS LIKE \'schools\''));
                        $id = $id[1]['AUTO_INCREMENT'];
				
				$sql = 'INSERT INTO schools (SYEAR'.$fields.') values('.UserSyear().''.$values.')';
				
				DBQuery($sql);
                            DBQuery('INSERT INTO  staff_school_relationship(staff_id,school_id,syear) VALUES ('.  UserID().','.$id.','.  UserSyear().')');
                            if(User('PROFILE_ID')!=0)   
                            {
                                $super_id=  DBGet(DBQuery('SELECT STAFF_ID FROM staff WHERE PROFILE_ID=0 AND PROFILE=\'admin\''));
                                DBQuery('INSERT INTO  staff_school_relationship(staff_id,school_id,syear) VALUES ('.$super_id[1]['STAFF_ID'].','.$id.','.  UserSyear().')');
                            }
                            DBQuery('INSERT INTO school_years (MARKING_PERIOD_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT fn_marking_period_seq(),SYEAR,\''.$id.'\' AS SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM school_years WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' ORDER BY MARKING_PERIOD_ID');
				DBQuery('INSERT INTO system_preference(school_id, full_day_minute, half_day_minute) VALUES ('.$id.', NULL, NULL)');
				
				DBQuery('INSERT INTO program_config (SCHOOL_ID,SYEAR,PROGRAM,TITLE,VALUE) VALUES(\''.$id.'\',\''.UserSyear().'\',\'MissingAttendance\',\'LAST_UPDATE\',\''.date('Y-m-d').'\')');
				$_SESSION['UserSchool'] = $id;
				
//                                                                        echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>'; 
				
				unset($_REQUEST['new_school']);
			}
	echo '<FORM action=Modules.php?modname='.strip_tags(trim($_REQUEST['modname'])).' method=POST>';
	echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
	echo "<br><br>";
	DrawHeaderHome('<IMG SRC=assets/check.gif> &nbsp; A new school called <strong>'.  GetSchool(UserSchool()).'</strong> has been created. To finish the operation, click OK button.','<INPUT  type=submit value=OK class="btn_medium">');
	echo '<input type="hidden" name="copy" value="done"/>';
	echo '</FORM>';
		}
	}else
        {
            $_REQUEST['modfunc'] = '';
	}


	unset($_SESSION['_REQUEST_vars']['values']);
	unset($_SESSION['_REQUEST_vars']['modfunc']);

}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update' && clean_param($_REQUEST['button'],PARAM_ALPHAMOD)=='Delete' && User('PROFILE')=='admin')
{
	if(DeletePrompt('school'))
	{
			if(BlockDelete('school'))
			{
				DBQuery('DELETE FROM schools WHERE ID=\''.UserSchool().'\'');
				DBQuery('DELETE FROM school_gradelevels WHERE SCHOOL_ID=\''.UserSchool().'\'');
				DBQuery('DELETE FROM attendance_calendar WHERE SCHOOL_ID=\''.UserSchool().'\'');
				DBQuery('DELETE FROM school_periods WHERE SCHOOL_ID=\''.UserSchool().'\'');
				DBQuery('DELETE FROM school_years WHERE SCHOOL_ID=\''.UserSchool().'\'');
				DBQuery('DELETE FROM school_semesters WHERE SCHOOL_ID=\''.UserSchool().'\'');
				DBQuery('DELETE FROM school_quarters WHERE SCHOOL_ID=\''.UserSchool().'\'');
				DBQuery('DELETE FROM school_progress_periods WHERE SCHOOL_ID=\''.UserSchool().'\'');
				DBQuery('UPDATE staff SET CURRENT_SCHOOL_ID=NULL WHERE CURRENT_SCHOOL_ID=\''.UserSchool().'\'');
				DBQuery('UPDATE staff SET SCHOOLS=replace(SCHOOLS,\','.UserSchool().',\',\',\')');
		
				unset($_SESSION['UserSchool']);
				echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
				unset($_REQUEST);
				$_REQUEST['modname'] = "schoolsetup/Schools.php?new_school=true";
				$_REQUEST['new_school'] = true;
				unset($_REQUEST['modfunc']);
				echo '
				<SCRIPT language="JavaScript">
				window.location="Side.php?school_id=new&modcat='.strip_tags(trim($_REQUEST['modcat'])).'";
				</SCRIPT>
				';
			}
	}
}
if(clean_param($_REQUEST['copy'],PARAM_ALPHAMOD)=='done'){
echo '<br><strong>School has been created successfully.</strong>';
}  else {
if(!$_REQUEST['modfunc'])
{
	if(!$_REQUEST['new_school'])
	{
		$schooldata = DBGet(DBQuery('SELECT * FROM schools WHERE ID=\''.UserSchool().'\''));
		$schooldata = $schooldata[1];
		$school_name = GetSchool(UserSchool());
	}
	else
		$school_name = 'Add a School';
		
	echo "<FORM name=school  id=school  enctype='multipart/form-data'  METHOD='POST' ACTION='Modules.php?modname=".strip_tags(trim($_REQUEST['modname']))."&modfunc=update&btn=".$_REQUEST['button']."&new_school=$_REQUEST[new_school]'>";

	PopTable_wo_header('header');

	echo "<table border=0 align=center><tr><td>";
     

        echo "<TABLE align=center>";

	echo "<TR><TD class='label'><b>School Name:</b></td><td>".TextInput($schooldata['TITLE'],'values[TITLE]', '','class=cell_floating size=24')."</td></TR>";
	echo "<TR ALIGN=LEFT><TD class='label'><b>Address:</b></TD><td>".TextInput($schooldata['ADDRESS'],'values[ADDRESS]','','class=cell_floating maxlength=100 size=24')."</td></TR>";
	echo "<TR ALIGN=LEFT><TD class='label'><b>City:</b></TD><td>".TextInput($schooldata['CITY'],'values[CITY]','','maxlength=100, class=cell_floating size=24')."</td></TR>";
	echo "<TR ALIGN=LEFT><TD class='label'><b>State:</b></TD><td>".TextInput($schooldata['STATE'],'values[STATE]','','maxlength=10 class=cell_floating size=24')."</td></TR>";
	echo "<TR ALIGN=LEFT><TD class='label'><b>Zip/Postal Code:</b></TD><td>".TextInput($schooldata['ZIPCODE'],'values[ZIPCODE]','','maxlength=10 class=cell_floating size=24')."</td></TR>";

	echo "<TR ALIGN=LEFT><TD  class='label'><b>Telephone:</b></td><td>".TextInput($schooldata['PHONE'],'values[PHONE]','','class=cell_floating size=24')."</TD></TR>";
	echo "<TR ALIGN=LEFT><TD class='label'><b>Principal:</b></td><td>".TextInput($schooldata['PRINCIPAL'],'values[PRINCIPAL]','','class=cell_floating size=24')."</TD></TR>";
        echo "<TR ALIGN=LEFT><TD class='label'><b>Base Grading Scale:</b></td><td>".TextInput($schooldata['REPORTING_GP_SCALE'],'values[REPORTING_GP_SCALE]','','class=cell_floating maxlength=10 size=24')."</TD></TR>";
        echo "<TR ALIGN=LEFT><TD class='label'><b>E-Mail:</b></td><td>".TextInput($schooldata['E_MAIL'],'values[E_MAIL]','','class=cell_floating maxlength=100 size=24')."</TD></TR>";
//        echo "<TR ALIGN=LEFT><TD class='label'><b>CEEB:</b></td><td>".TextInput($schooldata['CEEB'],'values[CEEB]','','class=cell_floating maxlength=100 size=24')."</TD></TR>";
       
        if($school_name!='Add a School')
        include('modules/schoolsetup/includes/SchoolcustomfieldsInc.php');
	if(AllowEdit() || !$schooldata['WWW_ADDRESS'])
        {
            
		echo "<TR ALIGN=LEFT><TD class='label'><b>Website:</b></td><td>".TextInput($schooldata['WWW_ADDRESS'],'values[WWW_ADDRESS]','','class=cell_floating size=24')."</TD></TR>";
        }
                else
		{
		echo "<TR ALIGN=LEFT><TD class='label'><b>Website:</b></td><td><A HREF=http://$schooldata[WWW_ADDRESS] target=_blank>$schooldata[WWW_ADDRESS]</A></TD></TR>";
                }

        $uploaded_sql=DBGet(DBQuery("SELECT VALUE FROM program_config WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR IS NULL AND TITLE='PATH'"));
        $_SESSION['logo_path']=$uploaded_sql[1]['VALUE'];
        if(!$_REQUEST['new_school'] && file_exists($uploaded_sql[1]['VALUE']))
        echo "<TR ALIGN=LEFT><TD class='label'><b>School Logo: </b></td><td><table><tr><td><div align=center>".(AllowEdit()!=false?"<a href ='Modules.php?modname=schoolsetup/UploadLogo.php&modfunc=edit'>":'')."<img src='".$uploaded_sql[1]['VALUE']."' width=70 class=pic />".(AllowEdit()!=false?"</a>":'')."</div></td><td>".(AllowEdit()!=false?"<a href ='Modules.php?modname=schoolsetup/UploadLogo.php&modfunc=edit'>Click here to <br />change logo</a>":'')."</td></tr></table></TD></TR>";
        else if(!$_REQUEST['new_school'])
        echo "<TR ALIGN=LEFT><TD class='label'><b>School Logo: </b></td><td>".(AllowEdit()!=false?"<a href ='Modules.php?modname=schoolsetup/UploadLogo.php'>Click here to upload logo</a>":'-')."</TD></TR>";

#
	echo "</TABLE>";
	if(User('PROFILE')=='admin' && AllowEdit())
	{
	 	if($_REQUEST['new_school'])
		{
			DrawHeader('','',"<CENTER><INPUT TYPE=submit name=button id=button class=btn_medium VALUE='Save' onclick='return formcheck_school_setup_school();'></CENTER>");
		}
		else
		{

			DrawHeader('','',"<CENTER><INPUT TYPE=submit name=button id=button class=btn_medium VALUE='Update' onclick='return formcheck_school_setup_school();'></CENTER>");
		}
	}
	
	echo "</td></tr></table>";
	
	PopTable('footer');

	echo "</FORM>";
}

}

?>
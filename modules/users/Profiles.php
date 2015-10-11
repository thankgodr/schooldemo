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
DrawBC("users >> ".ProgramTitle());

include 'Menu.php';

if(is_numeric(clean_param($_REQUEST['profile_id'],PARAM_INT)))
{
	$exceptions_RET = DBGet(DBQuery('SELECT PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT FROM profile_exceptions WHERE PROFILE_ID=\''.$_REQUEST[profile_id].'\''),array(),array('MODNAME'));
	$profile_RET = DBGet(DBQuery('SELECT PROFILE FROM user_profiles WHERE ID=\''.$_REQUEST[profile_id].'\''));
	$xprofile = $profile_RET[1]['PROFILE'];
	if($xprofile=='student')
	{
		$xprofile = 'parent';
		unset($menu['users']);
	}
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete' && AllowEdit())
{
	$profile_RET = DBGet(DBQuery('SELECT TITLE FROM user_profiles WHERE ID=\''.$_REQUEST[profile_id].'\''));


        $profile=$profile_RET[1]['TITLE'];
        if(DeletePromptBigString("delete <i>$profile</i>?,<br/>users of that profile will retain their permissions as a custom set which can be modified on a per-user basis through the User Permissions program."))
	{
            $existStaff=  DBGet(DBQuery("select * from staff where profile_id=$_REQUEST[profile_id]"));
            if(count($existStaff)==0)
            {
		DBQuery('DELETE FROM user_profiles WHERE ID=\''.$_REQUEST['profile_id'].'\'');
		
		DBQuery('DELETE FROM profile_exceptions WHERE PROFILE_ID=\''.$_REQUEST['profile_id'].'\'');
		unset($_REQUEST['modfunc']);
		unset($_REQUEST['profile_id']);
            }
            else 
            {
                 echo '<BR>';
		PopTable('header','Alert Message');
		echo "<CENTER><h4>Cannot delete because profile is associated with staff.</h4><br><FORM action=$PHP_tmp_SELF METHOD=POST><INPUT type=button class=btn_medium name=delete_cancel value=OK onclick='window.location=\"Modules.php?modname=$_REQUEST[modname] \"'></FORM></CENTER>";
		PopTable('footer');
		return false;
            }
	}
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update' && AllowEdit() && !$_REQUEST['new_profile_title'])
{
	$tmp_menu = $menuprof;
	$categories_RET = DBGet(DBQuery('SELECT ID,TITLE FROM student_field_categories'));
	foreach($categories_RET as $category)
	{
		$file = 'students/Student.php&category_id='.$category['ID'];
		$tmp_menu['students'][$xprofile][$file] = ' &nbsp; &nbsp; &rsaquo; '.$category['TITLE'];
	}
	$categories_RET = DBGet(DBQuery('SELECT ID,TITLE FROM people_field_categories'));
	foreach($categories_RET as $category)
	{
		$file = 'users/User.php&category_id='.$category['ID'];
		$tmp_menu['users'][$xprofile][$file] = ' &nbsp; &nbsp; &rsaquo; '.$category['TITLE'];
	}
        $categories_RET = DBGet(DBQuery('SELECT ID,TITLE FROM  staff_field_categories'));
	foreach($categories_RET as $category)
	{
		$file = 'users/Staff.php&category_id='.$category['ID'];
		$tmp_menu['users'][$xprofile][$file] = ' &nbsp; &nbsp; &rsaquo; '.$category['TITLE'];
	}

	foreach($tmp_menu as $modcat=>$profiles)
	{
		$values = $profiles[$xprofile];
		foreach($values as $modname=>$title)
		{
			if(!is_numeric($modname))
			{
				if(!count($exceptions_RET[$modname]) && ($_REQUEST['can_edit'][str_replace('.','_',$modname)] || $_REQUEST['can_use'][str_replace('.','_',$modname)]))
                                {
//                                    
                                        DBQuery('INSERT INTO profile_exceptions (PROFILE_ID,MODNAME) values(\''.$_REQUEST[profile_id].'\',\''.$modname.'\')');
                                }
				elseif(count($exceptions_RET[$modname]) && !$_REQUEST['can_edit'][str_replace('.','_',$modname)] && !$_REQUEST['can_use'][str_replace('.','_',$modname)])
					DBQuery('DELETE FROM profile_exceptions WHERE PROFILE_ID=\''.$_REQUEST[profile_id].'\' AND MODNAME=\''.$modname.'\'');

				if($_REQUEST['can_edit'][str_replace('.','_',$modname)] || $_REQUEST['can_use'][str_replace('.','_',$modname)])
				{
					$update = 'UPDATE profile_exceptions SET ';
					if($_REQUEST['can_edit'][str_replace('.','_',$modname)])
						$update .= 'CAN_EDIT=\''.'Y'.'\',';
					else
						$update .= 'CAN_EDIT=NULL,';
					if($_REQUEST['can_use'][str_replace('.','_',$modname)])
						$update .= 'CAN_USE=\''.'Y'.'\'';
					else
						$update .= 'CAN_USE=NULL';
					$update .= ' WHERE PROFILE_ID=\''.$_REQUEST[profile_id].'\' AND MODNAME=\''.$modname.'\';';
					DBQuery($update);
				}
			}
		}
	}
	$exceptions_RET = DBGet(DBQuery('SELECT MODNAME,CAN_USE,CAN_EDIT FROM profile_exceptions WHERE PROFILE_ID=\''.$_REQUEST[profile_id].'\''),array(),array('MODNAME'));
	unset($tmp_menu);
	unset($_REQUEST['modfunc']);
	unset($_REQUEST['can_edit']);
	unset($_REQUEST['can_use']);
}

if(clean_param($_REQUEST['new_profile_title'],PARAM_NOTAGS) && AllowEdit())
{     
	
        $id = DBGet(DBQuery('SHOW TABLE STATUS LIKE \''.'user_profiles'.'\''));
        $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
	$id = $id[1]['ID'];
	$exceptions_RET = array();
        $_REQUEST['new_profile_title']=str_replace("'","''", $_REQUEST['new_profile_title']);
	DBQuery('INSERT INTO user_profiles (TITLE,PROFILE) values(\''.clean_param($_REQUEST['new_profile_title'],PARAM_NOTAGS).'\',\''.clean_param($_REQUEST['new_profile_type'],PARAM_ALPHA).'\')');
	$_REQUEST['profile_id'] = $id;
	$xprofile = $_REQUEST['new_profile_type'];
	unset($_REQUEST['new_profile_title']);
	unset($_REQUEST['new_profile_type']);
	unset($_SESSION['_REQUEST_vars']['new_profile_title']);
	unset($_SESSION['_REQUEST_vars']['new_profile_type']);
}

if($_REQUEST['modfunc']!='delete')
{

PopTable('header','Permissions');
	echo "<FORM name=pref_form id=pref_form action=Modules.php?modname=$_REQUEST[modname]&modfunc=update&profile_id=$_REQUEST[profile_id] method=POST>";
	DrawHeaderHome('Select the programs that users of this profile can use and which programs those users can use to save information.');
	echo '<BR>';
	echo '<TABLE width=100%><TR><TD valign=top width=26%>';
	echo '<TABLE border=0 cellpadding=0 cellspacing=0>';
	$style = ' style="border:1px; border-style: none none none none; padding:4px;"';
	$style1 = ' style="border:1px; border-style: solid none none none;"';
	
        if(User('PROFILE_ID')==1)
        $where_ex=' WHERE ID<>0 ';    
	$profiles_RET = DBGet(DBQuery('SELECT ID,TITLE,PROFILE,IF(ID=4,-1,ID) as ORDER_ID FROM user_profiles '.$where_ex.' ORDER BY ORDER_ID'),array(),array('PROFILE','ID'));
	echo '<TR><TD colspan=3 style="border:1px; border-style: none none solid none;"><b>Profiles</b></TD></TR>';
	foreach(array('admin','teacher','parent','student') as $profiles)
	{
	foreach($profiles_RET[$profiles] as $id=>$profile)
	{
		if($_REQUEST['profile_id']!='' && $id==$_REQUEST['profile_id'])
			echo '<TR id=selected_tr onmouseover="" onmouseout="" bgcolor="'.Preferences('HIGHLIGHT').'"; this.style.color="white";\'><TD width=20 align=right'.$style.'>'.(AllowEdit()&&$id>4&&$id!=0?button('remove','',"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&profile_id=$id",20):'').'</TD><TD '.$style.' onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&profile_id='.$id.'\';">';
		else
			echo '<TR onmouseover=\'this.style.backgroundColor="'.Preferences('HIGHLIGHT').'"; this.style.color="white";\' onmouseout=\'this.style.cssText="background-color:transparent; color:black;";\'><TD width=20 align=right'.$style.'>'.(AllowEdit()&&$id>4&&$id!=0?button('remove','',"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&profile_id=$id",15):'').'</TD><TD'.$style.' onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&profile_id='.$id.'\';">';
		echo '<b><a style="cursor: pointer; cursor:hand; text-decoration:none;">'.($id>4?'':'<b>').$profile[1]['TITLE'].($id>4?'':'</b>').'</a></b> &nbsp;';
		echo '</TD>';
		echo '<TD'.$style.'><A style="cursor: pointer;"><IMG SRC=assets/arrow_right.gif></A></TD>';
		echo '</TR>';
	}
	}
	if($_REQUEST['profile_id']=='')
		echo '<TR id=selected_tr><TD height=0></TD></TR>';

	if(AllowEdit())
	{
	echo '<TR id=new_tr><TD colspan=3'.$style1.'>';
	echo '<a style="cursor: pointer;" onclick=\'document.getElementById("selected_tr").onmouseover="this.style.backgroundColor=\"'.Preferences('HIGHLIGHT').'\"; this.style.color=\"white\";"; document.getElementById("selected_tr").onmouseout="this.style.cssText=\"background-color:transparent; color:black;\";"; document.getElementById("selected_tr").style.cssText="background-color:transparent; color:black;"; changeHTML({"new_id_div":"new_id_content"},["main_div"]);document.getElementById("new_tr").onmouseover="";document.getElementById("new_tr").onmouseout="";this.onclick="";\'><b> Add a User Profile<BR></a><br><DIV id=new_id_div></DIV> </b>';
	echo '</TD>';
	
	echo '</TR>';
	}
        if($_REQUEST['profile_id']==3)
        {
            unset($menuprof['users']);
        }
	echo '</TABLE>';
	echo '</TD><TD width=20 class=vbreak></TD><TD>';
	echo '<DIV id=main_div>';
	if($_REQUEST['profile_id']!='')
	{
		
		echo '<TABLE border=0 cellspacing=0>';
		echo '<TR><TD colspan=5 style="border:1px; border-style: none none solid none;"><b>Permissions</b></TD></TR>';
		foreach($menuprof as $modcat=>$profiles)
		{
			$values = $profiles[$xprofile];
                        if($modcat=='eligibility')
                            $modcat='Extracurricular';
			echo '<TR><TD valign=top class=grid align=right style="white-space: nowrap; padding:6px 2px 2px 6px" >';
			echo "<b>".ucwords(str_replace('_',' ',$modcat))."</b></TD><TD width=3 class=grid>&nbsp;</TD>";
			echo "<td class=grid style='white-space: nowrap;  padding:2px 2px 2px 6px;'>Can Use".(AllowEdit()?"<INPUT type=checkbox name=can_use_$modcat onclick='checkAll(this.form,this.form.can_use_$modcat.checked,\"can_use[$modcat\");'>":'')."</td>";
			if(($xprofile=='admin' || $modcat=='students') && $_REQUEST['profile_id']!=4)
				echo"<td class=grid style='white-space: nowrap; padding:2px 2px 2px 6px;' > &nbsp;Can Edit".(AllowEdit()?"<INPUT type=checkbox name=can_edit_$modcat onclick='checkAll(this.form,this.form.can_edit_$modcat.checked,\"can_edit[$modcat\");'>":'')."</td>";
			else
				echo"<td class=grid></td>";
			echo "<td class=grid></td></TR>";
			if(count($values))
			{
				foreach($values as $file=>$title)
				{
                                    if($_REQUEST['profile_id']!=0 && $xprofile=='admin' && $modcat=='tools' && ($title=='Backup Database' || $title=='Reports' || $title=='At a Glance' || $title=='Institute Reports' || $title=='Institute Custom Field Reports'))
                                        continue;
					if(!is_numeric($file))
					{
						$can_use = $exceptions_RET[$file][1]['CAN_USE'];
						$can_edit = $exceptions_RET[$file][1]['CAN_EDIT'];

						echo "<TR><TD></TD><TD ></TD>";

						echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_use[".str_replace('.','_',$file)."] value=true".($can_use=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
						if($xprofile=='admin')
                            echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_edit[".str_replace('.','_',$file)."] value=true".($can_edit=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
						elseif($xprofile=='parent' && $file=='scheduling/Requests.php')
                            echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_edit[".str_replace('.','_',$file)."] value=true".($can_edit=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
                       
                                                else
                            echo "<TD align=center></TD>";
						echo "<TD > &nbsp; &nbsp;$title</TD></TR><TR><TD></TD><TD></TD><TD colspan=3 class=break></TR>";

						if($modcat=='students' && $file=='students/Student.php')
						{
							$categories_RET = DBGet(DBQuery('SELECT ID,TITLE FROM student_field_categories ORDER BY SORT_ORDER,TITLE'));
							foreach($categories_RET as $category)
							{
								$file = 'students/Student.php&category_id='.$category['ID'];
								$title = ' &nbsp; &nbsp; &rsaquo; '.$category['TITLE'];
								$can_use = $exceptions_RET[$file][1]['CAN_USE'];
								$can_edit = $exceptions_RET[$file][1]['CAN_EDIT'];

								echo "<TR><TD></TD><TD></TD>";
								echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_use[".str_replace('.','_',$file)."] value=true".($can_use=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
								if($_REQUEST['profile_id']!=4)
                                                                echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_edit[".str_replace('.','_',$file)."] value=true".($can_edit=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
                                                            else 
                                                              echo "<TD></TD>";  
                                                            
                                                                echo "<TD >$title</TD></TR><TR><TD></TD><TD></TD><TD colspan=3 class=break_headers></TR>";
							}
						}
						elseif($modcat=='users' && $file=='users/User.php' )
						{
							$categories_RET = DBGet(DBQuery('SELECT ID,TITLE FROM people_field_categories ORDER BY SORT_ORDER,TITLE'));
							foreach($categories_RET as $category)
							{
								$file = 'users/User.php&category_id='.$category['ID'];
								$title = ' &nbsp; &nbsp; &rsaquo; '.$category['TITLE'];
								$can_use = $exceptions_RET[$file][1]['CAN_USE'];
								$can_edit = $exceptions_RET[$file][1]['CAN_EDIT'];

								echo "<TR><TD></TD><TD></TD>";
								echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_use[".str_replace('.','_',$file)."] value=true".($can_use=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
								
                                                                echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_edit[".str_replace('.','_',$file)."] value=true".($can_edit=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
								echo "<TD style='white-space: nowrap;'> &nbsp; &nbsp;$title</TD></TR><TR><TD></TD><TD></TD><TD colspan=3 class=break_headers></TR>";
							}
						}
                                                elseif($modcat=='users' && $file=='users/Staff.php')
						{
							$categories_RET = DBGet(DBQuery('SELECT ID,TITLE FROM  staff_field_categories ORDER BY SORT_ORDER,TITLE'));
							foreach($categories_RET as $category)
							{
								$file = 'users/Staff.php&category_id='.$category['ID'];
								$title = ' &nbsp; &nbsp; &rsaquo; '.$category['TITLE'];
								$can_use = $exceptions_RET[$file][1]['CAN_USE'];
								$can_edit = $exceptions_RET[$file][1]['CAN_EDIT'];

								echo "<TR><TD></TD><TD></TD>";
								echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_use[".str_replace('.','_',$file)."] value=true".($can_use=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
								echo "<TD align=left style='padding:0px 0px 0px 47px'><INPUT type=checkbox name=can_edit[".str_replace('.','_',$file)."] value=true".($can_edit=='Y'?' CHECKED':'').(AllowEdit()?'':' DISABLED')."></TD>";
								echo "<TD style='white-space: nowrap;'> &nbsp; &nbsp;$title</TD></TR><TR><TD></TD><TD></TD><TD colspan=3 class=break_headers></TR>";
							}
						}
					}
					else
						echo '<TR><TD></TD><TD></TD><TD colspan=3 style=background-color:#bee6f2 align=right><b> '.$title.' </b></TD></TR>';

				}
			}
			echo '<TR><TD colspan=5 align=center height=20></TD></TR>';
		}
		echo '</TABLE>';
		
		echo '<CENTER>'.SubmitButton('Save', '', 'class=btn_medium').'</CENTER>';
	
	}
	echo '</DIV>';
	echo '</TD></TR></TABLE>';
	echo '</FORM>';
	PopTable('footer');
	echo '<DIV id=new_id_content style="position:absolute;visibility:hidden;"><fieldset><legend>Add a User Profile</legend><table><tr><td width=30>Title </td><td><INPUT type=text name=new_profile_title maxlength=20></td></tr>';
	echo '<tr><td width=30>Type </td><td><SELECT name=new_profile_type><OPTION value=admin>Administrator<OPTION value=teacher>Teacher<OPTION value=parent>Parent</SELECT>
	<br></td></tr><tr><td colspan=2 align=center><input type=submit value=save class=btn_medium></td></tr></table></fieldset></DIV>';
}
?>
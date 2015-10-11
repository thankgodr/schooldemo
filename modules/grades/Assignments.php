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
include_once("modules/messaging/fckeditor/fckeditor.php") ;

if(isset($_REQUEST['tables']['new']) && $_REQUEST['tables']['new']['TITLE']=='' && $_REQUEST['table']=='gradebook_assignment_types')
{
    unset($_REQUEST);
    $_REQUEST['modname']='grades/Assignments.php';
    $_REQUEST['assignment_type_id']='new';
    
}
$course_period_id = UserCoursePeriod();
$course_id = DBGet(DBQuery('SELECT COURSE_ID FROM course_periods WHERE COURSE_PERIOD_ID=\''.UserCoursePeriod().'\''));
$course_id = $course_id[1]['COURSE_ID'];

$_openSIS['allow_edit'] = true;
unset($_SESSION['_REQUEST_vars']['assignment_type_id']);
unset($_SESSION['_REQUEST_vars']['assignment_id']);

$config_RET = DBGet(DBQuery('SELECT TITLE,VALUE FROM program_user_config WHERE USER_ID=\''.User('STAFF_ID').'\' AND PROGRAM=\'Gradebook\''),array(),array('TITLE'));
if(count($config_RET))
	foreach($config_RET as $title=>$value)
		$programconfig[$title] = $value[1]['VALUE'];
else
	$programconfig = true;


if(clean_param($_REQUEST['day_tables'],PARAM_NOTAGS) && ($_POST['day_tables'] || $_REQUEST['ajax']))
{
	foreach($_REQUEST['day_tables'] as $id=>$values)
	{
		if($_REQUEST['day_tables'][$id]['DUE_DATE'] && $_REQUEST['month_tables'][$id]['DUE_DATE'] && $_REQUEST['year_tables'][$id]['DUE_DATE'])
			$_REQUEST['tables'][$id]['DUE_DATE'] =date("Y-m-d",strtotime($_REQUEST['day_tables'][$id]['DUE_DATE'].'-'.$_REQUEST['month_tables'][$id]['DUE_DATE'].'-'.$_REQUEST['year_tables'][$id]['DUE_DATE']));
		if($_REQUEST['day_tables'][$id]['ASSIGNED_DATE'] && $_REQUEST['month_tables'][$id]['ASSIGNED_DATE'] && $_REQUEST['year_tables'][$id]['ASSIGNED_DATE'])
			$_REQUEST['tables'][$id]['ASSIGNED_DATE'] = date("Y-m-d",strtotime($_REQUEST['day_tables'][$id]['ASSIGNED_DATE'].'-'.$_REQUEST['month_tables'][$id]['ASSIGNED_DATE'].'-'.$_REQUEST['year_tables'][$id]['ASSIGNED_DATE']));
	}
	$_POST['tables'] = $_REQUEST['tables'];
}

if(clean_param($_REQUEST['tables'],PARAM_NOTAGS) && ($_POST['tables'] || $_REQUEST['ajax']))
{
        $redirect_now='n';
	$table = $_REQUEST['table'];
        $err=false;
	foreach($_REQUEST['tables'] as $id=>$columns)
	{
		if($table=='gradebook_assignment_types' && $programconfig['WEIGHT']=='Y' && $columns['FINAL_GRADE_PERCENT']!='')
        		$columns['FINAL_GRADE_PERCENT'] = ereg_replace('[^0-9.]','',clean_param($columns['FINAL_GRADE_PERCENT'],PARAM_PERCENT)) / 100;

		if($id!='new')
		{
                    if(trim($columns['TITLE'])!="" || !isset($columns['TITLE']))
                    {
			if($columns['ASSIGNMENT_TYPE_ID'] && $columns['ASSIGNMENT_TYPE_ID']!=$_REQUEST['assignment_type_id'])
				$_REQUEST['assignment_type_id'] = $columns['ASSIGNMENT_TYPE_ID'];

			$sql = 'UPDATE '.$table.' SET ';

			if(isset($_REQUEST['tables'][$id]['COURSE_ID']) && $_REQUEST['tables'][$id]['COURSE_ID']=='' && $table=='gradebook_assignments')
				$columns['COURSE_ID'] = 'N';

			foreach($columns as $column=>$value)
			{
				
                                if($column=='DUE_DATE' || $column=='ASSIGNED_DATE')
                                {
                                    
                                    $due_date_sql = DBGet(DBQuery('SELECT ASSIGNED_DATE,DUE_DATE FROM gradebook_assignments WHERE ASSIGNMENT_ID=\''.$_REQUEST[assignment_id].'\''));
                                    if($columns['DUE_DATE'] && $columns['ASSIGNED_DATE'])
                                    {   
                                        if(strtotime($columns['DUE_DATE'])<strtotime($columns['ASSIGNED_DATE']))
                                        {
                                            $err=true;
                                            continue;
                                        }
                                    }
                                    if($columns['DUE_DATE'] && !$columns['ASSIGNED_DATE'])
                                    {   
                                        if(strtotime($columns['DUE_DATE'])<strtotime($due_date_sql[1]['ASSIGNED_DATE']) && $due_date_sql[1]['ASSIGNED_DATE']!='')
                                        {
                                            $err=true;
                                            continue;
                                        }
                                    }
                                    if(!$columns['DUE_DATE'] && $columns['ASSIGNED_DATE'])
                                    {   
                                        if(strtotime($due_date_sql[1]['DUE_DATE'])<strtotime($columns['ASSIGNED_DATE']) && $due_date_sql[1]['DUE_DATE']!='')
                                        {
                                            $err=true;
                                            continue;
                                        }
                                    }
                                }
                                if($column=='DESCRIPTION' && $value!='' && $table=='gradebook_assignments')
                                {
                                    $value=htmlspecialchars($_SESSION['ASSIGNMENT_DESCRIPTION']);
                                }

                                if($column=='COURSE_ID' && $value=='Y' && $table=='gradebook_assignments')
				{
					$value = $course_id;
					$sql .= 'COURSE_PERIOD_ID=NULL,';
				}
				elseif($column=='COURSE_ID' && $table=='gradebook_assignments')
				{
					$column = 'COURSE_PERIOD_ID';
                                        $get_assignment_course_period=DBGet(DBQuery('SELECT gat.COURSE_PERIOD_ID FROM gradebook_assignment_types gat,gradebook_assignments ga WHERE ga.ASSIGNMENT_TYPE_ID=gat.ASSIGNMENT_TYPE_ID AND ga.ASSIGNMENT_ID='.$id));
                                        $value = ($get_assignment_course_period[1]['COURSE_PERIOD_ID']!=''?$get_assignment_course_period[1]['COURSE_PERIOD_ID']:$course_period_id);
                                        $sql .= 'COURSE_ID=NULL,';
                                        if($get_assignment_course_period[1]['COURSE_PERIOD_ID']!=$course_period_id)
                                        $redirect_now='y';
                                        
				}
                                if($column!='DESCRIPTION' && $table=='gradebook_assignments')
                                {
                                 $value= paramlib_validation($column,$value);
                                }

                                                $value=str_replace("'","''",$value);
				$sql .= $column.'=\''.$value.'\',';
        					}
			$sql = substr($sql,0,-1) . ' WHERE '.substr($table,10,-1).'_ID=\''.$id.'\'';
			$go = true;
		}
		else
		{
                        ShowErrPhp('Title Cannot be Blank');
                    }
		}
		else
		{
			$sql = 'INSERT INTO '.$table.' ';

			if($table=='gradebook_assignments')
			{
				if($columns['ASSIGNMENT_TYPE_ID'])
				{
					$_REQUEST['assignment_type_id'] = $columns['ASSIGNMENT_TYPE_ID'];
					unset($columns['ASSIGNMENT_TYPE_ID']);
				}
				
                               
                                $id = DBGet(DBQuery('SHOW TABLE STATUS LIKE \'gradebook_assignments\''));
                                $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
                                $id = $id[1]['ID'];

                                
				$_REQUEST['assignment_id'] = $id;
                                
                                $check_cp_type=DBGet(DBQuery('SELECT MARKING_PERIOD_ID FROM course_periods WHERE COURSE_PERIOD_ID='.$course_period_id));
                                if($check_cp_type[1]['MARKING_PERIOD_ID']!='')
                                {
				$fields = 'ASSIGNMENT_TYPE_ID,STAFF_ID,MARKING_PERIOD_ID,';
				$values = "'".$_REQUEST['assignment_type_id']."','".User('STAFF_ID')."','".UserMP()."',";
                                }
                                else
                                {
                                $full_year_mp=DBGet(DBQuery('SELECT MARKING_PERIOD_ID FROM school_years WHERE SCHOOL_ID='.UserSchool().' AND SYEAR='.UserSyear()));
                                $full_year_mp=$full_year_mp[1]['MARKING_PERIOD_ID'];
                                $fields = 'ASSIGNMENT_TYPE_ID,STAFF_ID,MARKING_PERIOD_ID,';
				$values = "'".$_REQUEST['assignment_type_id']."','".User('STAFF_ID')."','".$full_year_mp."',";
                                }
			}
			elseif($table=='gradebook_assignment_types')
			{
				
                            $id = DBGet(DBQuery('SHOW TABLE STATUS LIKE \'gradebook_assignment_types\''));
                                $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
                                $id = $id[1]['ID'];
				$fields = 'STAFF_ID,COURSE_ID,COURSE_PERIOD_ID,';
				$values = '\''.User('STAFF_ID').'\',\''.$course_id.'\',\''.$course_period_id.'\',';
				$_REQUEST['assignment_type_id'] = $id;
			}

			$go = false;

			if(!$columns['COURSE_ID'] && $_REQUEST['table']=='gradebook_assignments')
				$columns['COURSE_ID'] = 'N';

			foreach($columns as $column=>$value)
			{
				
                                if($columns['DUE_DATE'] && $columns['ASSIGNED_DATE'])
                                {   
                                    if(strtotime($columns['DUE_DATE'])<strtotime($columns['ASSIGNED_DATE']))
                                    {
                                        $err=true;
                                        break 2;
                                    }
                                }
                                if($column=='COURSE_ID' && $value=='Y')
					$value = $course_id;
				elseif($column=='COURSE_ID')
				{
					$column = 'COURSE_PERIOD_ID';
					$value = $course_period_id;                                        
				}
                                if($column=='DESCRIPTION' && $value!='')
                                {
                                    $value=htmlspecialchars($_SESSION['ASSIGNMENT_DESCRIPTION']);
                                }

				if($value!='')
				{
                                    if($column!='DESCRIPTION' && $table=='gradebook_assignments')
                                    {
                                        $value= paramlib_validation($column,$value);
                                    }
					$fields .= $column.',';

					$values .= '\''.str_replace("'","''",$value).'\',';
					$go = true;                                        
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
		}
                

                if((isset($columns['DUE_DATE']) || isset($columns['ASSIGNED_DATE'])) && $err==false)
                {
                    
                    $get_dates=DBGet(DBQuery('SELECT START_DATE,END_DATE FROM marking_periods WHERE marking_period_id=\''.UserMP().'\' '));
                    $s_d=strtotime($get_dates[1]['START_DATE']);
                    $e_d=strtotime($get_dates[1]['END_DATE']);
                    if(isset($columns['DUE_DATE']) && isset($columns['ASSIGNED_DATE']))
                    {
                        if(strtotime($columns['DUE_DATE'])>$s_d && strtotime($columns['DUE_DATE'])<=$e_d && strtotime($columns['ASSIGNED_DATE'])>=$s_d && strtotime($columns['ASSIGNED_DATE'])<$e_d)
                            $go=true;
                        else
                        {
                            $msg='<Font color=red>Assigned date and due date must be within the current marking periods start date and end date.</FONT>';
                            $go=false;
                            $_REQUEST['assignment_id'] = 'new'; 
                        }
                    }
                    if(isset($columns['DUE_DATE']) && !isset($columns['ASSIGNED_DATE']))
                    {
                        if(strtotime($columns['DUE_DATE'])>$s_d && strtotime($columns['DUE_DATE'])<=$e_d)
                            $go=true;
                        else
                        {
                            $msg='<Font color=red>Due date must be within the current marking periods start date and end date.</FONT>';
                            $go=false;
                            $_REQUEST['assignment_id'] = 'new'; 
                        }
                    }
                    if(!isset($columns['DUE_DATE']) && isset($columns['ASSIGNED_DATE']))
                    {
                        if(strtotime($columns['ASSIGNED_DATE'])>=$s_d && strtotime($columns['ASSIGNED_DATE'])<$e_d)
                            $go=true;
                        else
                        {
                            $msg='<Font color=red>Assigned date must be within the current marking periods start date and end date.</FONT>';
                            $go=false;
                            $_REQUEST['assignment_id'] = 'new'; 
                        }
                    }
                }
                
		if($go){
                    if($_REQUEST['assignment_id']!='')
                        DBQuery_assignment($sql);
                    else
                        DBQuery ($sql);
                        DBQuery('UPDATE gradebook_assignments SET UNGRADED=2 WHERE ASSIGNMENT_ID IN (SELECT ASSIGNMENT_ID FROM gradebook_grades WHERE POINTS IS NULL OR POINTS=\'\') OR ASSIGNMENT_ID NOT IN (SELECT ASSIGNMENT_ID FROM gradebook_grades WHERE POINTS IS NOT NULL OR POINTS!=\'\')');
                        }
                if($msg)
                {
                    echo '<b>'.$msg.'</b>';
                }
	}
	unset($_REQUEST['tables']);
        unset($_SESSION['ASSIGNMENT_DESCRIPTION']);
//       $_REQUEST['assignment_id'] = 'new'; 
        $_REQUEST['ajax'] = true;
	unset($_SESSION['_REQUEST_vars']['tables']);
        if($redirect_now=='y')
        echo '<script type="text/javascript">check_content("Ajax.php?modname=grades/Assignments.php");</script>';
        
}

if($err)
{
    unset($_REQUEST['tables']);
     $_REQUEST['assignment_id'] = 'new'; 
     $_REQUEST['ajax'] = true;
    echo '<Font color=red>Due date must be greater than assigned date.</FONT>';
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete')
{  

	if($_REQUEST['assignment_type_id'] || $_REQUEST['assignment_id'])
        {
         if($_REQUEST['assignment_type_id'] && !$_REQUEST['assignment_id'])
        {   
         $table ='assignment type';
         $data=DBGet(DBQuery('select assignment_id from gradebook_assignments where assignment_type_id='.$_REQUEST['assignment_type_id'].''));
         if(count($data)>0)
             UnableDeletePromptMod('Gradebook Assignment Type cannot be deleted because assignments are created in this assignment type.','','modfunc=&assignment_type_id='.$_REQUEST['assignment_type_id']);
        else
        {
         if(DeletePromptAssignment($table, $_REQUEST['assignment_type_id']))
            {

            DBQuery('DELETE FROM gradebook_assignment_types  WHERE assignment_type_id=\''.$_REQUEST['assignment_type_id'].'\'');
            DBQuery('DELETE FROM gradebook_assignments WHERE assignment_type_id=\''.$_REQUEST['assignment_type_id'].'\'');

            DBQuery('DELETE FROM gradebook_grades WHERE assignment_id=\''.$data[1]['assignment_id'].'\'');
            unset($_REQUEST['assignment_type_id']);
            unset($_REQUEST['modfunc']);
            }  
        }
        }
        else
        {
        $table = 'assignment';

        $has_assigned=0;
        
	$stmt = DBGet(DBQuery("SELECT COUNT(*) AS TOTAL_ASSIGNED FROM gradebook_grades WHERE assignment_id=".$_REQUEST['assignment_id']));

//        $has_assigned=COUNT($stmt);
        $has_assigned=$stmt[1]['TOTAL_ASSIGNED'];
		if($has_assigned>0){
                    UnableDeletePromptMod('Gradebook Assignment cannot be deleted because grade was given for this assignment.','','modfunc=&assignment_type_id='.$_REQUEST['assignment_type_id'].'&assignment_id='.$_REQUEST['assignment_id']);
		}else{
		if(DeletePromptAssignment($table, $_REQUEST['assignment_type_id']))
			{

                        DBQuery('DELETE FROM gradebook_grades WHERE assignment_id=\''.$_REQUEST['assignment_id'].'\'');
                        DBQuery('DELETE FROM gradebook_assignments WHERE assignment_id=\''.$_REQUEST['assignment_id'].'\'');
                        unset($_REQUEST['assignment_id']);
                        unset($_REQUEST['modfunc']);
			}
                        
        }
	}
        }
        unset($_SESSION['_REQUEST_vars']['modfunc']);
}

if(!$_REQUEST['modfunc'] && $course_id)
{

	// ASSIGNMENT TYPES
	$sql = ' SELECT ASSIGNMENT_TYPE_ID,TITLE 
                 FROM (
                    ( select gat.ASSIGNMENT_TYPE_ID,gat.TITLE  FROM gradebook_assignment_types gat where gat.COURSE_PERIOD_ID=\''.$course_period_id.'\' )
                  UNION  
                   (SELECT gat.ASSIGNMENT_TYPE_ID as ASSIGNMENT_TYPE_ID,concat(gat.TITLE,\' (\',cp.title,\')\') as TITLE FROM gradebook_assignment_types gat , gradebook_assignments ga, course_periods cp
                    where cp.course_period_id =gat.course_period_id and gat.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND ga.COURSE_ID IS NOT NULL 
                    AND ga.COURSE_PERIOD_ID IS NULL AND ga.COURSE_ID=\''.UserCourse().'\' AND ga.STAFF_ID=\''.UserID().'\' ) 
                  )as t
                  GROUP BY ASSIGNMENT_TYPE_ID';
	
        $QI = DBQuery($sql);
	$types_RET = DBGet($QI);

	if($_REQUEST['assignment_id']!='new' && $_REQUEST['assignment_type_id']!='new')
        {
		$delete_button = "<INPUT type=button value=Delete onClick='javascript:window.location=\"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&assignment_type_id=$_REQUEST[assignment_type_id]&assignment_id=$_REQUEST[assignment_id]\"'>";
//                echo "<INPUT type=text name=type_id value='$_REQUEST[assignment_id]' id=type_id>";
        }

	// ADDING & EDITING FORM
	if($_REQUEST['assignment_id'] && $_REQUEST['assignment_id']!='new')
	{
		$sql = 'SELECT ASSIGNMENT_TYPE_ID,TITLE,ASSIGNED_DATE,DUE_DATE,POINTS,COURSE_ID,DESCRIPTION,
				CASE WHEN DUE_DATE<ASSIGNED_DATE THEN \'Y\' ELSE NULL END AS DATE_ERROR
				FROM gradebook_assignments
				WHERE ASSIGNMENT_ID=\''.$_REQUEST['assignment_id'].'\'';
		$QI = DBQuery($sql);
		$RET = DBGet($QI);
		$RET = $RET[1];
		$title = $RET['TITLE'];
	}
	elseif($_REQUEST['assignment_type_id'] && $_REQUEST['assignment_type_id']!='new' && $_REQUEST['assignment_id']!='new')
	{
		$sql = 'SELECT at.TITLE,at.FINAL_GRADE_PERCENT,
				(SELECT sum(FINAL_GRADE_PERCENT) FROM gradebook_assignment_types WHERE COURSE_PERIOD_ID=\''.$course_period_id.'\') AS TOTAL_PERCENT
				FROM gradebook_assignment_types at
				WHERE at.ASSIGNMENT_TYPE_ID=\''.$_REQUEST['assignment_type_id'].'\'';
		$QI = DBQuery($sql);
		$RET = DBGet($QI,array('FINAL_GRADE_PERCENT'=>'_makePercent'));
		$RET = $RET[1];
		$title = $RET['TITLE'];
	}
	elseif($_REQUEST['assignment_id']=='new')
	{
		$title = 'New Assignment';
		$new = true;
	}
	elseif($_REQUEST['assignment_type_id']=='new')
	{
		$sql = 'SELECT sum(FINAL_GRADE_PERCENT) AS TOTAL_PERCENT FROM gradebook_assignment_types WHERE COURSE_PERIOD_ID=\''.$course_period_id.'\'';
		$QI = DBQuery($sql);
		$RET = DBGet($QI,array('FINAL_GRADE_PERCENT'=>'_makePercent'));
		$RET = $RET[1];
		$title = 'New Assignment Type';
	}

	if($_REQUEST['assignment_id'])
	{
           
		echo "<FORM name=F3 action=Modules.php?modname=$_REQUEST[modname]&assignment_type_id=$_REQUEST[assignment_type_id]";
		if($_REQUEST['assignment_id']!='new')
			echo "&assignment_id=$_REQUEST[assignment_id]";
                else 
                    echo "&assignment_id=new";
		echo "&table=gradebook_assignments method=POST>";

		DrawHeader($title,$delete_button.'<INPUT type=submit value=Save onclick="formcheck_assignments();">');
                echo "<INPUT type=hidden name=type_id value='$_REQUEST[assignment_id]' id=type_id>";
		$header .= '<TABLE cellpadding=3 bgcolor=#F0F0F1 width=100%>';
		$header .= '<TR>';

		$header .= '<TD>' . TextInput($RET['TITLE'],'tables['.$_REQUEST['assignment_id'].'][TITLE]',($RET['TITLE']?'':'<FONT color=red>').'Title'.($RET['TITLE']?'':'</FONT>'),'size=36') . '</TD>';
//$header .= '<TD>' . TextInput($RET['TITLE'],'tables['.$_REQUEST['assignment_id'].'][TITLE]',($RET['TITLE']?'':'<FONT color=red>').'Title'.($RET['TITLE']?'':'</FONT>'),'size=36') . '</TD>';
		if($id=="new" || $_REQUEST['tab_id']=="new" || $RET['POINTS']=='')
            $extra = ' size=4 maxlength=5 onkeydown="return numberOnlyMod(event,this);" ';
            else
            $extra = ' size=4 maxlength=5 onkeydown=\"return numberOnlyMod(event,this);\"';
                $header .= '<TD>' . TextInput($RET['POINTS'],'tables['.$_REQUEST['assignment_id'].'][POINTS]',($RET['POINTS']!=''?'':'<FONT color=red>').'Points'.($RET['POINTS']?'':'</FONT>'),$extra) . '</TD>';
		$header .= '<TD>' . CheckboxInput($RET['COURSE_ID'],'tables['.$_REQUEST['assignment_id'].'][COURSE_ID]','Apply to all Periods for this Course') . '</TD>';
		foreach($types_RET as $type)
			$assignment_type_options[$type['ASSIGNMENT_TYPE_ID']] = $type['TITLE'];

		$header .= '<TD>' . SelectInput($RET['ASSIGNMENT_TYPE_ID']?$RET['ASSIGNMENT_TYPE_ID']:$_REQUEST['assignment_type_id'],'tables['.$_REQUEST['assignment_id'].'][ASSIGNMENT_TYPE_ID]','Assignment Type',$assignment_type_options,false) . '</TD>';
		$header .= '</TR><TR>';

                $header .= '<TD valign=top>' . DateInputAY($new && Preferences('DEFAULT_ASSIGNED','Gradebook')=='Y'?DBDate():$RET['ASSIGNED_DATE'],'tables['.$_REQUEST['assignment_id'].'][ASSIGNED_DATE]',1) .($_REQUEST['assignment_id']=='new'?'<br><small><font color="gray">Assigned<font color="gray"></font></font></small>':'<small><font color="gray">Assigned Date<font color="gray"></font></font></small>').'</TD>';
		$header .= '<TD valign=top>' . DateInputAY($new && Preferences('DEFAULT_DUE','Gradebook')=='Y'?DBDate():$RET['DUE_DATE'],'tables['.$_REQUEST['assignment_id'].'][DUE_DATE]',2) . ($_REQUEST['assignment_id']=='new'?'<br><small><font color="gray">Due<font color="gray"></font></font></small>':'<small><font color="gray">Due Date<font color="gray"></font></font></small>').'</TD>';
                $header .= '<TD rowspan=2 colspan=2><TD></TR>';
		
	}
	elseif($_REQUEST['assignment_type_id'])
	{
            
		echo "<FORM name=F3 action=Modules.php?modname=$_REQUEST[modname]&table=gradebook_assignment_types";
		if($_REQUEST['assignment_type_id']!='new')
			echo "&assignment_type_id=$_REQUEST[assignment_type_id]";
		echo " method=POST>";
                         
		DrawHeader($title,$delete_button.'<INPUT type=submit value=Save onclick="formcheck_assignments();">');
		$header .= '<TABLE cellpadding=3 bgcolor=#F0F0F1 width=100%>';
		$header .= '<TR>';

		$header .= '<TD>' . TextInput($RET['TITLE'],'tables['.$_REQUEST['assignment_type_id'].'][TITLE]','Title','size=36') . '</TD>';
		if($programconfig['WEIGHT']=='Y')
		{
			$header .= '<TD>' . TextInput($RET['FINAL_GRADE_PERCENT'],'tables['.$_REQUEST['assignment_type_id'].'][FINAL_GRADE_PERCENT]',($RET['FINAL_GRADE_PERCENT']!=0?'':'<FONT color=red>').'Percent of Final Grade'.($RET['FINAL_GRADE_PERCENT']!=0?'':'</FONT>')) . '</TD>';
			$header .= '<TD>' . NoInput($RET['TOTAL_PERCENT']==1?'100%':'<FONT COLOR=red>'.(100*$RET['TOTAL_PERCENT']).'%</FONT>','Percent Total') . '</TD>';
		}

		$header .= '</TR>';
		$header .= '</TABLE>';
	}
	else
		$header = false;

	if($header)
	{
            if($_REQUEST['assignment_id'])
            {
                echo $header;
                echo '<TR><TD colspan=3 align=center>';
                $oFCKeditor = new FCKeditor('tables['.$_REQUEST['assignment_id'].'][DESCRIPTION]');
                $oFCKeditor->BasePath = 'modules/messaging/fckeditor/';
                $oFCKeditor->Value = html_entity_decode($RET['DESCRIPTION']);
                $oFCKeditor->Height = '200';
                $oFCKeditor->Width = '600';
                $oFCKeditor->ToolbarSet= 'Mytoolbar ';
                echo  $oFCKeditor->Create(). '<br/><small><font color=grey align=left>Description</font></small></TD>';
                echo '</TR></TABLE>';

            }
                else
                    DrawHeader($header);
		echo '</FORM>';
	}

	// DISPLAY THE MENU
	$LO_options = array('save'=>false,'search'=>false,'add'=>true);

	echo '<TABLE><TR>';

	if(count($types_RET))
	{
		if($_REQUEST['assignment_type_id'])
		{
			foreach($types_RET as $key=>$value)
			{
				if($value['ASSIGNMENT_TYPE_ID']==$_REQUEST['assignment_type_id'])
					$types_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
			}
		}
	}

	echo '<TD valign=top>';
	$columns = array('TITLE'=>'Assignment Type');
	$link = array();
	$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
	$link['TITLE']['variables'] = array('assignment_type_id'=>'ASSIGNMENT_TYPE_ID');
	$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&assignment_type_id=new";
	$link['add']['first'] = 50000; // number before add link moves to top

	ListOutput($types_RET,$columns,'Assignment Type','Assignment Types',$link,array(),$LO_options);
	echo '</TD>';


	// ASSIGNMENTS
	if($_REQUEST['assignment_type_id'] && $_REQUEST['assignment_type_id']!='new' && count($types_RET))
	{
		$sql = 'SELECT ASSIGNMENT_ID,TITLE FROM gradebook_assignments WHERE (COURSE_ID=\''.$course_id.'\' OR COURSE_PERIOD_ID=\''.$course_period_id.'\') AND ASSIGNMENT_TYPE_ID=\''.$_REQUEST['assignment_type_id'].'\' AND MARKING_PERIOD_ID=\''.(GetCpDet($course_period_id,'MARKING_PERIOD_ID')!=''?UserMP():GetMPId('FY')).'\' ORDER BY '.Preferences('ASSIGNMENT_SORTING','Gradebook').' DESC';
		$QI = DBQuery($sql);
		$assn_RET = DBGet($QI);

		if(count($assn_RET))
		{
			if($_REQUEST['assignment_id'] && $_REQUEST['assignment_id']!='new')
			{
				foreach($assn_RET as $key=>$value)
				{
					if($value['ASSIGNMENT_ID']==$_REQUEST['assignment_id'])
						$assn_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
				}
			}
		}
                
		echo '<TD valign=top>';
		$columns = array('TITLE'=>'Assignment');
		$link = array();
		$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&assignment_type_id=$_REQUEST[assignment_type_id]";
		$link['TITLE']['variables'] = array('assignment_id'=>'ASSIGNMENT_ID');
		$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&assignment_type_id=$_REQUEST[assignment_type_id]&assignment_id=new";
		$link['add']['first'] = 50000; // number before add link moves to top

		ListOutput($assn_RET,$columns,'Assignment','Assignments',$link,array(),$LO_options);

		echo '</TD>';
	}

	echo '</TR></TABLE>';
}
elseif(!$course_id)
	echo '<BR>'.ErrorMessage(array('You don\'t have a course this period.'),'error');

function _makePercent($value,$column)
{
	return Percent($value,2);
}

?>
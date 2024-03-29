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

$sql_urg_sch_id = 'SELECT school_id FROM student_goal WHERE goal_id = '.clean_param($_REQUEST['goal_id'],PARAM_INT);
$res_urg_sch_id = mysql_query($sql_urg_sch_id);
$row_urg_sch_id = mysql_fetch_array($res_urg_sch_id);
$urg_sch_id = $row_urg_sch_id[0];

if(UserSchool() != '')
	$school_id = UserSchool();
else
	$school_id = $urg_sch_id;
	
$i=0;
$gid = $_REQUEST[goal_id];
$tabl = $_REQUEST[tabl];

if(($_REQUEST['day_tables'] || $_REQUEST['tables']) && ($_POST['day_tables'] || $_REQUEST['ajax']))
{
	foreach($_REQUEST['day_tables'] as $id=>$values)
	{
		if($_REQUEST['day_tables'][$id]['START_DATE'] && $_REQUEST['month_tables'][$id]['START_DATE'] && $_REQUEST['year_tables'][$id]['START_DATE'])
		{
			$_REQUEST['tables'][$id]['START_DATE'] = $_REQUEST['day_tables'][$id]['START_DATE'].'-'.$_REQUEST['month_tables'][$id]['START_DATE'].'-'.$_REQUEST['year_tables'][$id]['START_DATE'];
			$start_date = $_REQUEST['tables'][$id]['START_DATE'];
		}
		elseif(isset($_REQUEST['day_tables'][$id]['START_DATE']) && isset($_REQUEST['month_tables'][$id]['START_DATE']) && isset($_REQUEST['year_tables'][$id]['START_DATE']))
		$_REQUEST['tables'][$id]['START_DATE'] = '';


		if($_REQUEST['day_tables'][$id]['END_DATE'] && $_REQUEST['month_tables'][$id]['END_DATE'] && $_REQUEST['year_tables'][$id]['END_DATE'])
		{
			$_REQUEST['tables'][$id]['END_DATE'] = $_REQUEST['day_tables'][$id]['END_DATE'].'-'.$_REQUEST['month_tables'][$id]['END_DATE'].'-'.$_REQUEST['year_tables'][$id]['END_DATE'];
			$end_date = $_REQUEST['tables'][$id]['END_DATE'];
		}
		elseif(isset($_REQUEST['day_tables'][$id]['END_DATE']) && isset($_REQUEST['month_tables'][$id]['END_DATE']) && isset($_REQUEST['year_tables'][$id]['END_DATE']))
			$_REQUEST['tables'][$id]['END_DATE'] = '';
	}
	if(!$_POST['tables'])
		$_POST['tables'] = $_REQUEST['tables'];
		
}

unset($_SESSION['_REQUEST_vars']['goal_id']);unset($_SESSION['_REQUEST_vars']['course_id']);unset($_SESSION['_REQUEST_vars']['course_period_id']);

// if only one subject, select it automatically -- works for Course Setup and Choose a Course
if($_REQUEST['modfunc']!='delete' && !$_REQUEST['goal_id'])
{	
	 $subjects_RET = DBGet(DBQuery('SELECT GOAL_ID,GOAL_TITLE FROM student_goal WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' AND STUDENT_ID=\''.UserStudentID().'\''));

	if(count($subjects_RET)==1)
		$_REQUEST['goal_id'] = $subjects_RET[1]['GOAL_ID'];
}




if(clean_param($_REQUEST['action'],PARAM_ALPHAMOD) == 'delete')
{
	$_REQUEST['goal_id'] = $_REQUEST['gid'];
	$sql_pro_del = 'DELETE FROM student_goal_progress WHERE progress_id = '.$_REQUEST['pid'];
	$res_pro_del = mysql_query($sql_pro_del);
}

if(clean_param($_REQUEST['action'],PARAM_ALPHAMOD) == 'delete_goal')
{
	$_REQUEST['goal_id'] = $_REQUEST['gid'];
	$sql_pro_del = 'DELETE FROM student_goal_progress WHERE progress_id = '.$_REQUEST['pid'];
	$res_pro_del = mysql_query($sql_pro_del);
}


// UPDATING
if(clean_param($_REQUEST['tables'],PARAM_NOTAGS) && ($_POST['tables'] || $_REQUEST['ajax']) && AllowEdit())
{
	$where = array('student_goal'=>'GOAL_ID',
			'student_goal_progress'=>'PROGRESS_ID',
			'course_periods'=>'COURSE_PERIOD_ID');
	foreach($_REQUEST['tables'] as $table_name=>$tables)
	{ 
		foreach($tables as $id=>$columns)
		{
			if($id!='new' && $i==0)
			{
				if(is_numeric($table_name))
				{
					if($tabl == 'student_goal')
					{
						$id = $table_name;
						$table_name = 'student_goal';
					}
					
					if($tabl == 'student_goal_progress')
					{
						$id = $table_name;
						$table_name = 'student_goal_progress';
					}
				}
                                 if(!isset($start_date) || !isset($end_date))
                                {
                 $sql_s_date = 'SELECT START_DATE,END_DATE FROM '.$table_name.' WHERE GOAL_ID=\''.$id.'\'';
						$res_s_date = mysql_query($sql_s_date);
						$row_s_date = mysql_fetch_array($res_s_date);
                                }
if((strtotime($start_date)>=strtotime($end_date) && $start_date!="" && $end_date!="") || (strtotime($row_s_date['START_DATE'])>=strtotime($end_date) && $row_s_date['START_DATE']!="" && $end_date!="") || (strtotime($start_date)>=strtotime($row_s_date['END_DATE']) && $start_date!="" && $row_s_date['END_DATE']!="") || (strtotime($row_s_date['START_DATE'])>=strtotime($row_s_date['END_DATE']) && $row_s_date['START_DATE']!="" && $row_s_date['END_DATE']!=""))
{
    ShowErr('Data not saved because start and end date is not valid');
}
else
{
    if(!is_numeric($table_name))
    {
            $sql = 'UPDATE '.$table_name.' SET ';
    }
    if($_REQUEST['tables'][$id]['START_DATE']!='')
    {
            $sql.= 'START_DATE=\''.str_replace("'", "\'",$_REQUEST['tables'][$id]['START_DATE']).'\',';
    }
    if(!is_numeric($table_name))
    { 
            foreach($columns as $column=>$value)
            { 
                if(trim($value)!='')
                {
                    $value=paramlib_validation($column,$value);
                    #$sql.= $column."='".str_replace("\'","''",$value)."',";		// Windows

                    $sql.= $column.'=\''.str_replace("'", "''",$value).'\',';		// linux
                    
                }
            }
    }
			
			############################### Date Update Start #################################
			
   if((!isset($start_date) && strtotime($row_s_date['START_DATE'])<strtotime($end_date)) || (!isset($start_date) && strtotime($row_s_date['START_DATE'])<strtotime($row_s_date['END_DATE'])))

                               $sql.='START_DATE=\''.$row_s_date['START_DATE'].'\',';
if((!isset($end_date) && strtotime($start_date)<strtotime($row_s_date['END_DATE'])) || (!isset($end_date) && strtotime($row_s_date['START_DATE'])<strtotime($row_s_date['END_DATE'])))
                $sql.='END_DATE=\''.$row_s_date['END_DATE'].'\',';


if((isset($start_date) && strtotime($start_date)<strtotime($end_date)) || (isset($start_date) && strtotime($start_date)<strtotime($row_s_date['END_DATE'])))
				$sql.='START_DATE=\''.$start_date.'\',';
if((isset($end_date) && strtotime($row_s_date['START_DATE'])<strtotime($end_date)) || (isset($start_date) && strtotime($start_date)<strtotime($end_date)))
				$sql.='END_DATE=\''.$end_date.'\',';
			
			################################ Date Update End ##################################
			
				if(!is_numeric($table_name) && is_numeric($id))
				{
					$sql = substr($sql,0,-1) . ' WHERE '.$where[$table_name].'=\''.$id.'\'';
					DBQuery($sql);
					
					# ----------------------------------------------------------------- #
					
					if($tabl == 'student_goal')
					{
						$_REQUEST['goal_id'] = $id;		
					}
					
					if($tabl == 'student_goal_progress')
					{
						$sql_goal_id = 'SELECT goal_id FROM student_goal_progress WHERE progress_id='.$id;
						$res_goal_id = mysql_query($sql_goal_id);
						$row_goal_id = mysql_fetch_array($res_goal_id);
						$_REQUEST['progress_id'] = $id;
						$_REQUEST['goal_id'] = $row_goal_id[0];
					}
					
					# ----------------------------------------------------------------- #
					
					
				}
			}}
			else
			{
				$sql="INSERT INTO $table_name ";

				if($table_name=='student_goal')
				{
					
                    $id = DBGet(DBQuery('SHOW TABLE STATUS LIKE \'student_goal\''));
                    $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
					$fields = 'STUDENT_ID,SCHOOL_ID,SYEAR,START_DATE,END_DATE,';
					$values = '\''.UserStudentID().'\',\''.UserSchool().'\',\''.UserSyear().'\',\''.$start_date.'\',\''.$end_date.'\',';
					$_REQUEST['goal_id'] = $id[1]['ID'];
				}
				elseif($table_name=='student_goal_progress')
				{
					$_REQUEST[goal_id];
					
                    $id = DBGet(DBQuery('SHOW TABLE STATUS LIKE \'student_goal_progress\''));
                    $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
					$fields = 'GOAL_ID,STUDENT_ID,START_DATE,';
					$values = '\''.$_REQUEST[hgoal].'\',\''.UserStudentID().'\',\''.$start_date.'\',';
					$_REQUEST['progress_id'] = $id[1]['ID'];
				}

				$go = 0;
                               
                                
				foreach($columns as $column=>$value)
				{ 
                                   
                                    $value=paramlib_validation($column,$value);
					if(isset($value))
					{
						$fields .= $column.',';
						
						# $values .= "'".str_replace("\'","''",$value)."',";	// Windows
						
						 $values .= '\''.str_replace("'", "''",$value).'\',';	// linux
                                                
						
						$go = true;
					}
				}
				$sql.='('.substr($fields,0,-1).') values('.substr($values,0,-1).')';
					

				if($go)
                                {
                                   
                                   if(isset($start_date) && isset($end_date) && strtotime($end_date)<strtotime($start_date))
                                         ShowErr('Data not saved because start and end date is not valid');
                                   else
                                        DBQuery($sql);
                                    
                                }
					
					
					# ---------------------------------------------------------------- #

					if($tabl == 'student_goal_progress')
					{
						$sql_p_max = 'select max(progress_id) from student_goal_progress where student_id='.UserStudentID();
						$res_p_max = mysql_query($sql_p_max);
						$row_p_max = mysql_fetch_array($res_p_max);
						
						$sql_goal_id = 'select goal_id from student_goal_progress where progress_id='.$row_p_max[0];
						$res_goal_id = mysql_query($sql_goal_id);
						$row_goal_id = mysql_fetch_array($res_goal_id);
						$_REQUEST['progress_id'] = $row_p_max[0];
						$_REQUEST['goal_id'] = $row_goal_id[0];
					}
					
					# ---------------------------------------------------------------- #
					
					
			$i++;
			}
		}
	}
	unset($_REQUEST['tables']);
}

if((!clean_param($_REQUEST['modfunc'],PARAM_NOTAGS) || clean_param($_REQUEST['modfunc'],PARAM_NOTAGS)=='choose_course') && !clean_param($_REQUEST['course_modfunc'],PARAM_NOTAGS))
{
	if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)!='choose_course')
		DrawBC("Students > ".ProgramTitle());
	
	$sql = 'SELECT GOAL_ID,GOAL_TITLE FROM student_goal WHERE SCHOOL_ID=\''.$school_id.'\' AND SYEAR=\''.UserSyear().'\' AND STUDENT_ID=\''.UserStudentID().'\' ORDER BY START_DATE DESC';
	
	$QI = DBQuery($sql);
	$subjects_RET = DBGet($QI);
	
	# -------------------------------------- CP_ID ------------------------------#
	
		$sql_cp = 'SELECT cp.COURSE_PERIOD_ID AS COURSE_PERIOD, cp.TITLE AS COURSE_PERIOD_NAME FROM course_periods cp, schedule s WHERE s.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND s.STUDENT_ID=\''.UserStudentID().'\'';		
		$QI_cp = DBQuery($sql_cp);
		$cp_RET = DBGet($QI_cp);
	
	# ----------------------------------------------------------------------------------------------------------#

	if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)!='choose_course')
	{
		if(AllowEdit())
			$delete_button = "<INPUT type=button class=btn_medium value=Delete onClick='javascript:window.location=\"Modules.php?modname=students/Student.php&include=GoalInc&modfunc=delete&goal_id=$_REQUEST[goal_id]&progress_id=$_REQUEST[progress_id]&course_period_id=$_REQUEST[course_period_id]\"'> ";
		// ADDING & EDITING FORM

		
		if($_REQUEST['progress_id'])
		{
			if($_REQUEST['progress_id']!='new')
			{
				$sql = 'SELECT START_DATE,PROGRESS_NAME,PROFICIENCY,PROGRESS_DESCRIPTION FROM student_goal_progress
						WHERE PROGRESS_ID=\''.$_REQUEST[progress_id].'\'';
				$QI = DBQuery($sql);
				$RET = DBGet($QI);
				$RET = $RET[1];
				$title = $RET['PROGRESS_NAME'];
				
				# -------------------------- CPID Start ---------------------------------- #
				
				$sql_sel_cp = 'SELECT COURSE_PERIOD_ID
						FROM student_goal_progress
						WHERE PROGRESS_ID=\''.$_REQUEST[progress_id].'\'';
				$QI_sel_cp = DBQuery($sql_sel_cp);
				$RET_sel_cp = DBGet($QI_sel_cp);
				$RET_sel = $RET_sel_cp[1];
				$title_sel_cp = $RET_sel_cp[1]['COURSE_PERIOD_ID'];
				
				# -------------------------- CPID End ---------------------------------- #
				
			}
			else
			{
				$sql = 'SELECT GOAL_TITLE
						FROM student_goal
						WHERE GOAL_ID=\''.$_REQUEST[goal_id].'\' ORDER BY GOAL_TITLE';
				$QI = DBQuery($sql);
				$RET = DBGet($QI);
			
				$title = $RET[1]['GOAL_TITLE'];
				
				unset($RET);
			}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)!='choose_course')
			{
				foreach($subjects_RET as $type)
					$options[$type['GOAL_ID']] = $type['GOAL_TITLE'];
					
				# -------------------------- For CP Option ------------------------------------------------ #
				
				foreach($cp_RET as $type_cp)
					$options_cp[$type_cp['COURSE_PERIOD']] = $type_cp['COURSE_PERIOD_NAME'];
					
				# -------------------------- For CP Option ------------------------------------------------ #
				
				
				
				$sql_gid = 'SELECT GOAL_ID FROM student_goal_progress WHERE PROGRESS_ID=\''.$_REQUEST[progress_id].'\'';
				$res_gid = mysql_query($sql_gid);
				$row_gid = mysql_fetch_array($res_gid);

			
			}

$edit_per_stu = DBGet(DBQuery('SELECT CAN_EDIT FROM profile_exceptions WHERE MODNAME=\'students/Student.php&category_id=5\' AND PROFILE_ID=3'));
if(User('PROFILE_ID')==1)
    $edit_per_adm = DBGet(DBQuery('SELECT CAN_EDIT FROM profile_exceptions WHERE MODNAME=\'students/Student.php&category_id=5\' AND PROFILE_ID=1'));
else if(User('PROFILE_ID')==0)
    $edit_per_adm = DBGet(DBQuery('SELECT CAN_EDIT FROM profile_exceptions WHERE MODNAME=\'students/Student.php&category_id=5\' AND PROFILE_ID=0'));
else 
    $edit_per_adm = DBGet(DBQuery('SELECT CAN_EDIT FROM profile_exceptions WHERE MODNAME=\'students/Student.php&category_id=5\' AND PROFILE_ID=1'));

$edit_per_teach = DBGet(DBQuery('SELECT CAN_EDIT FROM profile_exceptions WHERE MODNAME=\'students/Student.php&category_id=5\' AND PROFILE_ID=2'));

$edit_per_prnt = DBGet(DBQuery('SELECT CAN_EDIT FROM profile_exceptions WHERE MODNAME=\'students/Student.php&category_id=5\' AND PROFILE_ID=4'));



			$header .= '<TABLE cellpadding=4 width=98% border="0" align="center">';
			
			# ---------------------------------------------------------------------------- #
			
			# ----------------------------- Delete ---------------------------------------- #
			
	if(($_REQUEST['progress_id']!='new'))
	{	
		if(((User('PROFILE') == 'admin') && isset($edit_per_adm[1]['CAN_EDIT'])) || ((User('PROFILE') == 'teacher') && isset($edit_per_teach[1]['CAN_EDIT'])) || ((User('PROFILE') == 'student') && isset($edit_per_stu[1]['CAN_EDIT'])) || ((User('PROFILE') == 'parent') && isset($edit_per_prnt[1]['CAN_EDIT'])))
		{
			$header .= '<TR>';
			$header .= "<TD>&nbsp;</TD><TD align=right><a href='Modules.php?modname=students/Student.php&include=GoalInc&category_id=5&action=delete&gid=".$row_gid[0]."&pid=".$_REQUEST[progress_id]."'>Delete This Progress</a></TD>"; // DateInput is copied from schoolsetup/MarkingPeriods.php line 295
			$header .= '</TR>';
		}
	}
			
			# ------------------------------ Delete ---------------------------------------- #
			
			$header .= '<TR>';
			$header .= '<TD>Goal Title</TD><TD>' . SelectInput($RET['GOAL_ID']?$RET['GOAL_ID']:$_REQUEST['goal_id'],'tables[student_goal_progress]['.$_REQUEST['progress_id'].'][GOAL_ID]','',$options,false) . '</TD>'; // DateInput is copied from schoolsetup/MarkingPeriods.php line 295
			$header .= '</TR>';
			
			
			$header .= '<TR>';
			$header .= '<TD>Course Period</TD><TD>' . SelectInput($RET_sel['COURSE_PERIOD_ID'],'tables[student_goal_progress]['.$_REQUEST['progress_id'].'][COURSE_PERIOD_ID]','',$options_cp) . '</TD>'; // DateInput is copied from schoolsetup/MarkingPeriods.php line 295
			$header .= '</TR>';
			
			#----------------------------------------------------------------------------#
			$header .= '<TR>';
			
                        $header .= '<TD><input type="hidden" name="req_progress_id" id="req_progress_id" value="'.$_REQUEST['progress_id'].'" /></TD>';
			$header .= '</TR>';
			
			$header .= '<TR>';
			
                        $header .= '<TD>Date of Entry<input type="hidden" name="hgoal" value="'.$_REQUEST[goal_id].'" /></TD><TD>' . DateInputAY($RET['START_DATE'],'tables['.$_REQUEST['progress_id'].'][START_DATE]',1) . '</TD>'; // DateInput is copied from schoolsetup/MarkingPeriods.php line 295
			$header .= '</TR>';
			$header .= '<TR>';
			$header .= '<TD>Progress Period Name</TD><TD>' . TextInput($RET['PROGRESS_NAME'],'tables[student_goal_progress]['.$_REQUEST['progress_id'].'][PROGRESS_NAME]','','size=60 maxlength=50') . '</TD>';
			$header .= '</TR>';
			$header .= '<TR>';
			
		
			$options = array('0-10%'=>'0-10%','11-20%'=>'11-20%','21-30%'=>'21-30%','31-40%'=>'31-40%','41-50%'=>'41-50%','51-60%'=>'51-60%','61-70%'=>'61-70%','71-80%'=>'71-80%','81-90%'=>'81-90%','91-100%'=>'91-100%');
			
			$header .= '<TD>Proficiency  Scale</TD><TD>' . SelectInput($RET['PROFICIENCY'],'tables[student_goal_progress]['.$_REQUEST['progress_id'].'][PROFICIENCY]','',$options) . '</TD>';
			$header .= '</TR>';
			$header .= '<TR>';
			$header .= '<TD valign=top>Progress Assessment</TD><TD>' . TextAreaInput($RET['PROGRESS_DESCRIPTION'],'tables[student_goal_progress]['.$_REQUEST['progress_id'].'][PROGRESS_DESCRIPTION]','', 'rows=10 cols=57', 'true', '200px') . '<input type="hidden" name="tabl" id="tabl" value="student_goal_progress"></TD>';
			$header .= '</TR>';
			$header .= '</TABLE>';
			DrawHeader($header);
			
		}
		elseif($_REQUEST['goal_id'])
		{
			if($_REQUEST['goal_id']!='new')
			{
				$sql = 'SELECT GOAL_TITLE,START_DATE,END_DATE,GOAL_DESCRIPTION
						FROM student_goal
						WHERE GOAL_ID=\''.$_REQUEST[goal_id].'\' and SYEAR=\''.UserSyear().'\'';
				$QI = DBQuery($sql);
				$RET = DBGet($QI);
				$RET = $RET[1];
				$title = $RET['GOAL_TITLE'];
			}
			else
			{
				$title = 'New Subject';
				unset($delete_button);
			}
			
			
			
			
$edit_per_stu = DBGet(DBQuery('SELECT CAN_EDIT FROM profile_exceptions WHERE MODNAME=\'students/Student.php&category_id=5\' AND PROFILE_ID=0'));

if(User('PROFILE_ID')==1)
    $edit_per_adm = DBGet(DBQuery('SELECT CAN_EDIT FROM profile_exceptions WHERE MODNAME=\'students/Student.php&category_id=5\' AND PROFILE_ID=1'));
else if(User('PROFILE_ID')==0)
    $edit_per_adm = DBGet(DBQuery('SELECT CAN_EDIT FROM profile_exceptions WHERE MODNAME=\'students/Student.php&category_id=5\' AND PROFILE_ID=0'));
else 
    $edit_per_adm = DBGet(DBQuery('SELECT CAN_EDIT FROM profile_exceptions WHERE MODNAME=\'students/Student.php&category_id=5\' AND PROFILE_ID=1'));


$edit_per_teach = DBGet(DBQuery('SELECT CAN_EDIT FROM profile_exceptions WHERE MODNAME=\'students/Student.php&category_id=5\' AND PROFILE_ID=2'));

$edit_per_prnt = DBGet(DBQuery('SELECT CAN_EDIT FROM profile_exceptions WHERE MODNAME=\'students/Student.php&category_id=5\' AND PROFILE_ID=3'));
			
			
			

			$header .= '<TABLE cellpadding=3 width=100% align="center" border="0">';
			
			
			
			# ----------------------------- Delete ---------------------------------------- #
			
	
	if($_REQUEST['goal_id']!='new')
	{	
		if(((User('PROFILE') == 'admin') && isset($edit_per_adm[1]['CAN_EDIT'])) || ((User('PROFILE') == 'teacher') && isset($edit_per_teach[1]['CAN_EDIT'])) || ((User('PROFILE') == 'student') && isset($edit_per_stu[1]['CAN_EDIT'])) || ((User('PROFILE') == 'parent') && isset($edit_per_prnt[1]['CAN_EDIT'])))
		{
			$header .= '<TR>';

			$header .= "<TD>&nbsp;</TD><TD align=right><a href='Modules.php?modname=students/Student.php&include=GoalInc&category_id=5&action=delete_goal&gid=".$_REQUEST['goal_id']."'>Delete This Goal</a></TD>"; // DateInput is copied from schoolsetup/MarkingPeriods.php line 295
			$header .= '</TR>';
		}
	}
	
	
			
			# ------------------------------ Delete ---------------------------------------- #
			
			
			
			
			$header .= '<TR>';
			$header .= '<TD>Goal Title </TD><TD>' . TextInput($RET['GOAL_TITLE'],'tables[student_goal]['.$_REQUEST['goal_id'].'][GOAL_TITLE]','','size=75 maxlength=50') . '</TD>';
			$header .= '</TR>';
			$header .= '<TR>';

                        if($_REQUEST[goal_id].trim()!='')
                            $header .= '<TD>Begin Date</TD><TD><input type="hidden" name="goalId" id="goalId" value="'.$_REQUEST[goal_id].'" />' . DateInputAY($RET['START_DATE'],'tables['.$_REQUEST['goal_id'].'][START_DATE]',2) . '</TD>'; // DateInput is copied from schoolsetup/MarkingPeriods.php line 295
                        else
                            $header .= '<TD>Begin Date</TD><TD><input type="hidden" name="goalId" id="goalId" value="new" />' . DateInputAY($RET['START_DATE'],'tables['.$_REQUEST['goal_id'].'][START_DATE]',2) . '</TD>'; // DateInput is copied from schoolsetup/MarkingPeriods.php line 295
                        $header .= '</TR>';
			$header .= '<TR>';

                        $header .= '<TD>End Date</TD><TD>' . DateInputAY($RET['END_DATE'],'tables['.$_REQUEST['goal_id'].'][END_DATE]',3) . '</TD>'; // DateInput is copied from schoolsetup/MarkingPeriods.php line 296
			$header .= '</TR>';
			$header .= '<TR>';
			$header .= '<TD valign=top>Goal Description</TD><TD>' . TextAreaInput($RET['GOAL_DESCRIPTION'],'tables[student_goal]['.$_REQUEST['goal_id'].'][GOAL_DESCRIPTION]','', 'rows=10 cols=70', 'true', '200px') . '<input type="hidden" name="tabl" id="tabl" value="student_goal"></TD>';
			$header .= '</TR>';
			$header .= '</TABLE>';
			DrawHeader($header);
		}
	}

	// DISPLAY THE MENU
	$LO_options = array('save'=>false,'search'=>false);

	if(!$_REQUEST['goal_id'] || $_REQUEST['modfunc']=='choose_course')
	DrawHeaderHome('Goals',"<A HREF=ForWindow.php?modname=students/Student.php&include=GoalInc&modfunc=$_REQUEST[modfunc]&course_modfunc=search>Search</A>");

	echo '<TABLE><TR>';

	if(count($subjects_RET))
	{
		if($_REQUEST['goal_id'])
		{
			foreach($subjects_RET as $key=>$value)
			{
				if($value['GOAL_ID']==$_REQUEST['goal_id'])
					$subjects_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
			}
		}
	}

	echo '<TD valign=top>';
	$columns = array('GOAL_TITLE'=>'Goals');
	$link = array();
	$link['GOAL_TITLE']['link'] = "Modules.php?modname=students/Student.php&include=GoalInc";
	$link['GOAL_TITLE']['variables'] = array('goal_id'=>'GOAL_ID');
	if($_REQUEST['modfunc']!='choose_course')
		$link['add']['link'] = "Modules.php?modname=students/Student.php&include=GoalInc&goal_id=new";
	else
		$link['TITLE']['link'] .= "&modfunc=$_REQUEST[modfunc]";

	ListOutput($subjects_RET,$columns,'Goal','Goals',$link,array(),$LO_options);
	echo '</TD>';

	if($_REQUEST['goal_id'] && $_REQUEST['goal_id']!='new')
	{       
                $sql_goal =DBQuery( 'SELECT GOAL_ID FROM student_goal WHERE GOAL_ID=\''.$_REQUEST[goal_id].'\' and SYEAR=\''.UserSyear().'\'');
                $sql_goal_fetch=mysql_fetch_array($sql_goal);
		
		$sql = "SELECT PROGRESS_ID,PROGRESS_NAME FROM student_goal_progress WHERE GOAL_ID='".$sql_goal_fetch['GOAL_ID']."' AND STUDENT_ID=".UserStudentID()." ORDER BY START_DATE DESC";
		$QI = DBQuery($sql);
		$courses_RET = DBGet($QI);

		if(count($courses_RET))
		{
			if($_REQUEST['progress_id'])
			{
				foreach($courses_RET as $key=>$value)
				{
					if($value['PROGRESS_ID']==$_REQUEST['progress_id'])
						$courses_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
				}
			}
		}

		echo '<TD valign=top>';
		$columns = array('PROGRESS_NAME'=>'Progresses');
		$link = array();
		$link['PROGRESS_NAME']['link'] = "Modules.php?modname=students/Student.php&include=GoalInc&goal_id=$_REQUEST[goal_id]";
		$link['PROGRESS_NAME']['variables'] = array('progress_id'=>'PROGRESS_ID');
		if($_REQUEST['modfunc']!='choose_course')
			$link['add']['link'] = "Modules.php?modname=students/Student.php&include=GoalInc&goal_id=$_REQUEST[goal_id]&progress_id=new";
		else
			$link['PROGRESS_NAME']['link'] .= "&modfunc=$_REQUEST[modfunc]";
			
			
		

		ListOutput($courses_RET,$columns,'Progress','Progresses',$link,array(),$LO_options);
		echo '</TD>';
	}

	echo '</TR></TABLE>';
}

if($_REQUEST['modname']=='scheduling/Courses.php' && $_REQUEST['modfunc']=='choose_course' && $_REQUEST['course_period_id'])
{
	$course_title = DBGet(DBQuery("SELECT TITLE FROM course_periods WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'"));
	$course_title = $course_title[1]['TITLE'] . '<INPUT type=hidden name=tables[parent_id] value='.$_REQUEST['course_period_id'].'>';

	echo "<script language=javascript>opener.document.getElementById(\"course_div\").innerHTML = \"$course_title</small>\"; window.close();</script>";
}


?>

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

//////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////New Datepicker/////////////////////////////////////////////////////////////
if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
{
	$start_date = $_REQUEST['year_start'].'-'.$_REQUEST['month_start'].'-'.$_REQUEST['day_start'];
	$st_dt = ProperDateMAvr($start_date);
}

if($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end'])
{
	$end_date = $_REQUEST['year_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['day_end'];
	$end_dt = ProperDateMAvr($end_date);
}

if($_REQUEST['chk_pro'])
{
	$progress = $_REQUEST['chk_pro'];
}


if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['st_arr']))
	{
	$st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
	$extra['WHERE'] = ' AND s.STUDENT_ID IN ('.$st_list.')';

	
	if($_REQUEST['mailing_labels']=='Y')
		Widgets('mailing_labels');

	$RET = GetStuList($extra);

	if(count($RET))
	{
		include('modules/students/includes/FunctionsInc.php');
		//------------Comment Heading -----------------------------------------------------
		
		$people_categories_RET = DBGet(DBQuery('SELECT c.ID AS CATEGORY_ID,c.TITLE AS CATEGORY_TITLE,f.ID,f.TITLE,f.TYPE,f.SELECT_OPTIONS,f.DEFAULT_SELECTION,f.REQUIRED FROM people_field_categories c,people_fields f WHERE f.CATEGORY_ID=c.ID ORDER BY c.SORT_ORDER,c.TITLE,f.SORT_ORDER,f.TITLE'),array(),array('CATEGORY_ID'));

		explodeCustom($people_categories_RET, $people_custom, 'p');

		unset($_REQUEST['modfunc']);
		$handle = PDFStart();
				
		foreach($RET as $student)
		{
			$_SESSION['student_id'] = $student['STUDENT_ID'];



$sql_student = DBGet(DBQuery('SELECT gender AS GENDER, ethnicity AS ETHNICITY, common_name AS COM_NAME, social_security AS SOCIAL_SEC, language AS LANG, birthdate AS BDATE  FROM students WHERE STUDENT_ID=\''.$_SESSION['student_id'].'\''),array('BDATE'=>'ProperDate'));

$sql_student = $sql_student[1];

$bir_dt = $sql_student['BDATE'];
unset($_openSIS['DrawHeader']);

if(!isset($st_dt) && !isset($end_dt))
{
	$sql_goal = 'SELECT GOAL_ID,GOAL_TITLE,START_DATE,END_DATE,GOAL_DESCRIPTION FROM student_goal WHERE STUDENT_ID=\''.$_SESSION['student_id'].'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' ORDER BY GOAL_TITLE';
}
if(isset($st_dt) && !isset($end_dt))
{
	$sql_goal = 'SELECT GOAL_ID,GOAL_TITLE,START_DATE,END_DATE,GOAL_DESCRIPTION FROM student_goal WHERE STUDENT_ID=\''.$_SESSION['student_id'].'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' AND START_DATE>=\''.$st_dt.'\' ORDER BY GOAL_TITLE';
}
if(!isset($st_dt) && isset($end_dt))
{
	$sql_goal = 'SELECT GOAL_ID,GOAL_TITLE,START_DATE,END_DATE,GOAL_DESCRIPTION FROM student_goal WHERE STUDENT_ID=\''.$_SESSION['student_id'].'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' AND START_DATE<=\''.$end_dt.'\' ORDER BY GOAL_TITLE';
}
if(isset($st_dt) && isset($end_dt))
{
	$sql_goal = 'SELECT GOAL_ID,GOAL_TITLE,START_DATE,END_DATE,GOAL_DESCRIPTION FROM student_goal WHERE STUDENT_ID=\''.$_SESSION['student_id'].'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' AND START_DATE>=\''.$st_dt.'\' AND START_DATE<=\''.$end_dt.'\' ORDER BY GOAL_TITLE';
}

$res_goal = DBGet(DBQuery($sql_goal),array('START_DATE'=>'ProperDate','END_DATE'=>'ProperDate'));

	
	
			//----------------------------------------------

		if(count($res_goal) != 0)
		{
		
			echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
			echo "<tr><td width=105>".DrawLogo()."</td><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."</font></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=3 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
			echo "<table width=100% cellspacing=0 style=\"border-collapse:collapse\">";
		
		
			echo "<tr><td width=15%>Student Name:</td>";
			echo "<td>" .$student['FULL_NAME']. "</td></tr>";
			
			echo "<tr><td>Grade:</td>";
			echo "<td>". $student['GRADE_ID'] ." </td></tr>";
			echo "<tr><td>Gender:</td>";
			echo "<td>".$sql_student['GENDER'] ."</td></tr>";
			echo "<tr><td>Ethnicity:</td>";
			echo "<td>".$sql_student['ETHNICITY'] ."</td></tr>";
			if($sql_student['COM_NAME'] !='')
			{
			echo "<tr><td>Common Name:</td>";
			echo "<td>".$sql_student['COM_NAME'] ."</td></tr>";
			}
			if($sql_student['SOCIAL_SEC'] !='')
			{
			echo "<tr><td>Social Security:</td>";
			echo "<td>".$sql_student['SOCIAL_SEC'] ."</td></tr>";
			}
			echo "<tr><td>Date of Birth:</td>";
			echo "<td>".$bir_dt."</td></tr>";
			if($sql_student['LANG'] !='')
			{
			echo "<tr><td>Language Spoken:</td>";
			echo "<td>".$sql_student['LANG'] ."</td></tr>";
			echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
			}
			
			echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
			echo '<tr><td><b><u>GoalInc Details</u></b></td><td>&nbsp;</td></tr>';
			echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
			foreach($res_goal as $row_goal)
			{               
				echo '<tr><td><b>GoalInc Title: </b></td><td>'.$row_goal['GOAL_TITLE'].'</td></tr>';
				echo '<tr><td><b>Begin Date: </b></td><td>'.$row_goal['START_DATE'].'</td></tr>';
				echo '<tr><td><b>End Date: </b></td><td>'.$row_goal['END_DATE'].'</td></tr>';
				echo '<tr><td valign=top><b>GoalInc Description: </b></td><td>'.$row_goal['GOAL_DESCRIPTION'].'</td></tr>';
				echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
				
				if($progress == 'Y')
				{               $goal_id=$row_goal['GOAL_ID'];
					$res_pro = DBGet(DBQuery("SELECT START_DATE,PROGRESS_NAME ,PROFICIENCY,PROGRESS_DESCRIPTION,(SELECT TITLE FROM course_periods cp WHERE cp.COURSE_PERIOD_ID=student_goal_progress.COURSE_PERIOD_ID) AS CP_TITLE FROM student_goal_progress WHERE STUDENT_ID='".$_SESSION['student_id']."' AND GOAL_ID='".$goal_id."' ORDER BY PROGRESS_NAME"),array('START_DATE'=>'ProperDate'));
					echo '<tr><td><b><u>Progress Details</u></b></td><td>&nbsp;</td></tr>';
					echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
					foreach($res_pro as $row_pro)
					{
						echo '<tr><td><b>Date of Entry: </b></td><td>'.$row_pro['START_DATE'].'</td></tr>';
					# ----------------------------- CP ------------------------------------------------- #	
						echo '<tr><td><b>Course Period: </b></td><td>'.$row_pro['CP_TITLE'].'</td></tr>';
					# ----------------------------- CP ------------------------------------------------- #		
						echo '<tr><td><b>Progress Period Name: </b></td><td>'.$row_pro['PROGRESS_NAME'].'</td></tr>';
						echo '<tr><td><b>Proficiency: </b></td><td>'.$row_pro['PROFICIENCY'].'</td></tr>';
						echo '<tr><td><b>Progress Assessment: </b></td><td>'.$row_pro['PROGRESS_DESCRIPTION'].'</td></tr>';
						echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
					}
				}
				
				echo "<tr><td colspan=2 style=\"height:18px; border-top:1px solid #333;\"></td></tr>";
			}
			
			
			echo '</td><td></td><td></td></tr></table></TABLE><div style="page-break-before: always;">&nbsp;</div>';
			foreach($categories_RET as $id=>$category)
			{
				if($id!='1' && $id!='3' && $id!='2' && $id!='4' && $_REQUEST['category'][$id])
				{
					$_REQUEST['category_id'] = $id;
					
					$separator = '';
					if(!$category[1]['INCLUDE'])
						include('modules/students/includes/OtherInfoInc.php');
					elseif(!strpos($category[1]['INCLUDE'],'/'))
						include('modules/students/includes/'.$category[1]['INCLUDE'].'.php');
					else
					{
						include('modules/'.$category[1]['INCLUDE'].'.php');
						$separator = '<HR>';
						
					}

				}
			}
			
		}

		}
		PDFStop($handle);
	}
	else
		BackPrompt('No Students were found.');
	}
	else
		BackPrompt('You must choose at least one student.');
	unset($_SESSION['student_id']);
	//echo '<pre>'; var_dump($_REQUEST['modfunc']); echo '</pre>';
	$_REQUEST['modfunc']=true;
}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Students >> ".ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=ForExport.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_search_all_schools=$_REQUEST[_search_all_schools]&_openSIS_PDF=true method=POST target=_blank>";
		
		
	
#	DrawHeaderHome('<table><tr><td>'.PrepareDate($start_date,'_start').'</td><td> - </td><td>'.PrepareDate($end_date,'_end').'</td><td> - </td><td>'.$advanced_link.'</td><td> : <INPUT type=submit value=Go class=btn_medium></td></tr></table>');

	echo '<TABLE border=0 width=98% align=center><tr><td style=padding-top:25px;>Please select the date range :</td><TD valign=middle style=padding-top:25px;>';
	#$date=date("Y-m-d");
	$date=''; // 2009-04-08
	echo 'From : </TD><TD valign=middle>';
	#DrawHeader(PrepareDateGoal_Start($date,'_date',false,array('submit'=>true)));
	#DrawHeader(PrepareDate($date,'_date',false,array('submit'=>true)));
//	DrawHeader(PrepareDate($start_date,'_start'));
        DrawHeader(DateInputAY($start_date,'start',1));
	echo '</TD><TD valign=middle style=padding-top:25px;>To : </TD><TD valign=middle>';
	#DrawHeader(PrepareDateGoal_End($date,'_date',false,array('submit'=>true)));
//	DrawHeader(PrepareDate($end_date,'_end'));
        DrawHeader(DateInputAY($end_date,'end',2));
	echo '</TD><TD valign=middle style=padding-top:22px;><input type="checkbox" name="chk_pro" id="chk_pro" value="Y" /> With Progress';
	echo '</TD></TR></TABLE>';


	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ',s.STUDENT_ID AS CHECKBOX';
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['options']['search'] = false;
	$extra['new'] = true;

	Widgets('mailing_labels');
	Widgets('course');
	Widgets('request');
	Widgets('activity');
	Widgets('absences');
	Widgets('gpa');
	Widgets('class_rank');
	Widgets('letter_grade');
	Widgets('eligibility');

	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Print Info for Selected Students\'></CENTER>';
		echo "</FORM>";
	}
}

// GetStuList by default translates the grade_id to the grade title which we don't want here.
// One way to avoid this is to provide a translation function for the grade_id so here we
// provide a passthru function just to avoid the translation.
function _grade_id($value)
{
	return $value;
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}

function explodeCustom(&$categories_RET, &$custom, $prefix)
{
	foreach($categories_RET as $id=>$category)
		foreach($category as $i=>$field)
		{
			$custom .= ','.$prefix.'.CUSTOM_'.$field['ID'];
			if($field['TYPE']=='select' || $field['TYPE']=='codeds')
			{
				$select_options = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
				$select_options = explode("\r",$select_options);
				$options = array();
				foreach($select_options as $option)
				{
					if($field['TYPE']=='codeds')
					{
						$option = explode('|',$option);
						if($option[0]!='' && $option[1]!='')
							$options[$option[0]] = $option[1];
					}
					else
						$options[$option] = $option;
				}
				$categories_RET[$id][$i]['SELECT_OPTIONS'] = $options;
			}
		}
}

function printCustom(&$categories, &$values)
{
	echo "<table width=100%><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-weight:bold;\">".$categories[1]['CATEGORY_TITLE']."</td></tr>";
	foreach($categories as $field)
	{
		echo '<TR>';
		echo '<TD>'.($field['REQUIRED']&&$values['CUSTOM_'.$field['ID']]==''?'<FONT color=red>':'').$field['TITLE'].($field['REQUIRED']&&$values['CUSTOM_'.$field['ID']]==''?'</FONT>':'').'</TD>';
		if($field['TYPE']=='select')
			echo '<TD>'.($field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]!=''?'':'<FONT color=red>').$values['CUSTOM_'.$field['ID']].($field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]!=''?'':'</FONT>').'</TD>';
		elseif($field['TYPE']=='codeds')

			echo '<TD>'.($field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]!=''?$field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]:'<FONT color=red>'.$values['CUSTOM_'.$field['ID']].'</FONT>').'</TD>';
		else
			echo '<TD>'.$values['CUSTOM_'.$field['ID']].'</TD>';
		echo '</TR>';
	}
	echo '</table>';
}
/*
function con_date($date)
{
	$mother_date = $date;
	$year = substr($mother_date, 2, 2);
	$temp_month = substr($mother_date, 5, 2);
	
	if($temp_month == '01')
		$month = 'JAN';
	elseif($temp_month == '02')
		$month = 'FEB';
	elseif($temp_month == '03')
		$month = 'MAR';
	elseif($temp_month == '04')
		$month = 'APR';
	elseif($temp_month == '05')
		$month = 'MAY';
	elseif($temp_month == '06')
		$month = 'JUN';
	elseif($temp_month == '07')
		$month = 'JUL';
	elseif($temp_month == '08')
		$month = 'AUG';
	elseif($temp_month == '09')
		$month = 'SEP';
	elseif($temp_month == '10')
		$month = 'OCT';
	elseif($temp_month == '11')
		$month = 'NOV';
	elseif($temp_month == '12')
		$month = 'DEC';
		
	$day = substr($mother_date, 8, 2);
	
	$select_date = $day.'-'.$month.'-'.$year;
	return $select_date;
}
*/
function con_date($date)
{
	$mother_date = $date;
	$year = substr($mother_date, 7);
	$temp_month = substr($mother_date, 3, 3);
	
		if($temp_month == 'JAN')
			$month = '01';
		elseif($temp_month == 'FEB')
			$month = '02';
		elseif($temp_month == 'MAR')
			$month = '03';
		elseif($temp_month == 'APR')
			$month = '04';
		elseif($temp_month == 'MAY')
			$month = '05';
		elseif($temp_month == 'JUN')
			$month = '06';
		elseif($temp_month == 'JUL')
			$month = '07';
		elseif($temp_month == 'AUG')
			$month = '08';
		elseif($temp_month == 'SEP')
			$month = '09';
		elseif($temp_month == 'OCT')
			$month = '10';
		elseif($temp_month == 'NOV')
			$month = '11';
		elseif($temp_month == 'DEC')
			$month = '12';
			
	$day = substr($mother_date, 0, 2);
	
	$select_date = $year.'-'.$month.'-'.$day;
	return $select_date;
}

?>

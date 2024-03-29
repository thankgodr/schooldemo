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
if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['cp_arr']))
	{
	$cp_list = '\''.implode('\',\'',$_REQUEST['cp_arr']).'\'';

	
	$extra['DATE'] = GetMP();

	// get the fy marking period id, there should be exactly one fy marking period
	$fy_id = DBGet(DBQuery('SELECT MARKING_PERIOD_ID FROM school_years WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));
	$fy_id = $fy_id[1]['MARKING_PERIOD_ID'];

	$course_periods_RET = DBGet(DBQuery('SELECT cp.TITLE,cp.COURSE_PERIOD_ID,cpv.PERIOD_ID,cp.MARKING_PERIOD_ID,cpv.DAYS,c.TITLE AS COURSE_TITLE,cp.TEACHER_ID,(SELECT CONCAT(Trim(LAST_NAME),\', \',FIRST_NAME) FROM staff WHERE STAFF_ID=cp.TEACHER_ID) AS TEACHER FROM course_periods cp,course_period_var cpv,courses c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID IN ('.$cp_list.') ORDER BY TEACHER'));

	$first_extra = $extra;
	$handle = PDFStart();
	$PCL_UserCoursePeriod = $_SESSION['UserCoursePeriod']; // save/restore for teachers
	foreach($course_periods_RET as $teacher_id=>$course_period)
	{
		unset($_openSIS['DrawHeader']);
		

		$_openSIS['User'] = array(1=>array('STAFF_ID'=>$course_period['TEACHER_ID'],'NAME'=>'name','PROFILE'=>'teacher','SCHOOLS'=>','.UserSchool().',','SYEAR'=>UserSyear()));
		$_SESSION['UserCoursePeriod'] = $course_period['COURSE_PERIOD_ID'];
		echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
		echo "<tr><td width=105>".DrawLogo()."</td><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Teacher Class List</div></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=3 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
		echo "<table >";
		echo '<table border=0>';
		echo '<tr><td>Teacher Name:</td>';
		echo '<td>'.$course_period['TEACHER'].'</td></tr>';

                echo '<tr><td>Course Name:</td>';
		echo '<td>'.$course_period['COURSE_TITLE'].'</td></tr>';
		echo '<tr><td>Course Period Name:</td>';
		echo '<td>'.GetActualCpName($course_period).'</td></tr>';
                echo '<tr><td>Course Period Occurance:</td>';
		echo '<td>'.GetPeriodOcc($course_period['COURSE_PERIOD_ID']).'</td></tr>';
		echo '<tr><td>Marking Period:</td>';
		echo '<td>'.GetMP($course_period['MARKING_PERIOD_ID']).'</td></tr>';
		
		echo '</table>';
		$extra = $first_extra;
		$extra['MP'] = $course_period['MARKING_PERIOD_ID'];
		
		
		include('modules/miscellaneous/Export.php');


		echo "<div style=\"page-break-before: always;\"></div>";
	}
	$_SESSION['UserCoursePeriod'] = $PCL_UserCoursePeriod;
	PDFStop($handle);
	}
	else
        {
		BackPrompt('You must choose at least one course period.');
}
}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Scheduling > ".ProgramTitle());

	if(User('PROFILE')!='admin')
        {
		$_REQUEST['search_modfunc'] = 'list';
        }
	if($_REQUEST['search_modfunc']=='list' || $_REQUEST['search_modfunc']=='select')
	{
		$_REQUEST['search_modfunc'] = 'select';
		
		$extra['extra_header_left'] = '<TABLE>';
		$extra['extra_header_left'] .= '<TR><TD><INPUT type=checkbox name=include_inactive value=Y>Include Inactive Students</TD></TR>';
		$extra['extra_header_left'] .= '</TABLE>';

		$Search = 'mySearch';
		include('modules/miscellaneous/Export.php');
	}
	else
	{
		echo "<FORM action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=".strip_tags(trim($_REQUEST[modfunc]))."&search_modfunc=list&next_modname=".strip_tags(trim($_REQUEST[next_modname]))." method=POST>";
		echo '<BR>';
		PopTable('header','Search');
		echo '<TABLE border=0>';

		$RET = DBGet(DBQuery('SELECT s.STAFF_ID,CONCAT(Trim(s.LAST_NAME),\', \',s.FIRST_NAME) AS FULL_NAME FROM staff s,staff_school_relationship ssr WHERE s.STAFF_ID=ssr.STAFF_ID AND s.PROFILE=\''.'teacher'.'\' AND FIND_IN_SET(\''.UserSchool().'\', ssr.SCHOOL_ID)>0 AND ssr.SYEAR=\''.UserSyear().'\' ORDER BY FULL_NAME'));
		echo '<TR><TD align=right>Teacher</TD><TD>';
		echo "<SELECT name=teacher_id style='max-width:250;'><OPTION value=''>N/A</OPTION>";
		foreach($RET as $teacher)
			echo "<OPTION value=$teacher[STAFF_ID]>$teacher[FULL_NAME]</OPTION>";
		echo '</SELECT>';
		echo '</TD></TR>';

		$RET = DBGet(DBQuery("SELECT SUBJECT_ID,TITLE FROM course_subjects WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY TITLE"));
		echo '<TR><TD align=right>Subject</TD><TD>';
		echo "<SELECT name=subject_id style='max-width:250;'><OPTION value=''>N/A</OPTION>";
		foreach($RET as $subject)
			echo "<OPTION value=$subject[SUBJECT_ID]>$subject[TITLE]</OPTION>";
		echo '</SELECT>';

		$RET = DBGet(DBQuery("SELECT PERIOD_ID,TITLE FROM school_periods WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
		echo '<TR><TD align=right>Period</TD><TD>';
		echo "<SELECT name=period_id style='max-width:250;'><OPTION value=''>N/A</OPTION>";
		foreach($RET as $period)
			echo "<OPTION value=$period[PERIOD_ID]>$period[TITLE]</OPTION>";
		echo '</SELECT>';
		echo '</TD></TR>';

		Widgets('course');

		echo '<TR><TD colspan=2 align=center>';
		echo '<BR>';
		echo Buttons('Submit','Reset');
		echo '</TD></TR>';
		echo '</TABLE>';
		echo '</FORM>';
		PopTable('footer');
	}
}

function mySearch($extra)
{

	echo "<FORM name=exp id=exp action=ForExport.php?modname=".strip_tags(trim($_REQUEST[modname]))."&head_html=Teacher+Class+List&modfunc=save&search_modfunc=list&_openSIS_PDF=true onsubmit=document.forms[0].relation.value=document.getElementById(\"relation\").value; method=POST target=_blank>";
	echo '<DIV id=fields_div></DIV>';
	DrawHeader('',$extra['header_right']);
	DrawHeader($extra['extra_header_left'],$extra['extra_header_right']);

	if(User('PROFILE')=='admin')
	{
		if($_REQUEST['teacher_id'])
			$where .= " AND cp.TEACHER_ID='$_REQUEST[teacher_id]'";
		if($_REQUEST['first'])
			$where .= " AND UPPER(s.FIRST_NAME) LIKE '".strtoupper($_REQUEST['first'])."%'";
		if($_REQUEST['w_course_period_id'] && $_REQUEST['w_course_period_id_which']!='course')
			$where .= " AND cp.COURSE_PERIOD_ID='".$_REQUEST['w_course_period_id']."'";
		if($_REQUEST['subject_id'])
		{
			$from .= ",courses c";
			$where .= " AND c.COURSE_ID=cp.COURSE_ID AND c.SUBJECT_ID='".$_REQUEST['subject_id']."'";
		}
		if($_REQUEST['period_id'])
                {
			$where .= " AND cpv.PERIOD_ID='".$_REQUEST['period_id']."'";
                }
			$sql = "SELECT cp.COURSE_PERIOD_ID,cp.TITLE FROM course_periods cp,course_period_var cpv$from WHERE cp.SCHOOL_ID='".UserSchool()."' AND cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID AND cp.SYEAR='".UserSyear()."'$where";
	}
	else // teacher
	{
		$sql = "SELECT cp.COURSE_PERIOD_ID,cp.TITLE FROM course_periods cp,course_period_var cpv WHERE cp.SCHOOL_ID='".UserSchool()."' AND cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.TEACHER_ID='".User('STAFF_ID')."'";
	}
	$sql .= ' GROUP BY cp.COURSE_PERIOD_ID ORDER BY (SELECT SORT_ORDER FROM school_periods WHERE PERIOD_ID=cpv.PERIOD_ID)';

	$course_periods_RET = DBGet(DBQuery($sql),array('COURSE_PERIOD_ID'=>'_makeChooseCheckbox'));
	$LO_columns = array('COURSE_PERIOD_ID'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'cp_arr\');"><A>','TITLE'=>'Course Period');

	echo '<INPUT type=hidden name=relation>';
	ListOutput($course_periods_RET,$LO_columns,'Course Period','Course Periods');

	if(count($course_periods_RET)!=0)
	echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Create Class Lists for Selected Course Periods\'></CENTER>';
	echo "</FORM>";
}

function _makeChooseCheckbox($value,$title)
{
	return "<INPUT type=checkbox name=cp_arr[] value=$value checked>";
}
function GetActualCpName($cp_array)
{
    $cp_name=$cp_array['TITLE'];
    $teacher_name=$cp_array['TEACHER_F'];
    $cp_name=  explode('-', $cp_name);
    return $cp_name[0].' - '.$cp_name[1];
}
function GetPeriodOcc($cp_id)
{
    $period_name=array();
    $days=array('M'=>'Monday','T'=>'Tuesday','W'=>'Wednesday','H'=>'Thursday','F'=>'Friday','S'=>'Saturday','U'=>'Sunday');
    $get_det=DBGet(DBQuery('SELECT cpv.DAYS,cpv.START_TIME,cpv.END_TIME,sp.TITLE FROM course_period_var cpv,school_periods sp WHERE cpv.PERIOD_ID=sp.PERIOD_ID AND cpv.COURSE_PERIOD_ID='.$cp_id));
    foreach($get_det as $gd)
    {
        $period_name[]=$days[$gd['DAYS']].' - '.$gd['TITLE'].' ('.date("g:i A", strtotime($gd['START_TIME'])).' - '.date("g:i A", strtotime($gd['END_TIME'])).')';
    }
    return implode(',',$period_name);
}
?>
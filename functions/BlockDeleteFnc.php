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
function BlockDelete($item)
{
	switch($item)
	{
		case 'school':
				$find_student = DBGet(DBQuery('SELECT COUNT(STUDENT_ID) AS STUDENT_EXIST FROM student_enrollment WHERE SCHOOL_ID=\''.UserSchool().'\''));
				$find_student = $find_student[1]['STUDENT_EXIST'];
				$find_staff = DBGet(DBQuery('SELECT COUNT(STAFF_ID) AS STAFF_EXIST FROM staff WHERE CURRENT_SCHOOL_ID=\''.UserSchool().'\''));
				$find_staff = $find_staff[1]['STAFF_EXIST'];
				if($find_student>0 && $find_staff>0)	
				{
					PopTable('header','Unable to Delete');
					DrawHeaderHome('<font color=red>This School cannot be deleted. There are Students and Teachers in this School</font>');
					echo '<div align=right><a href=Modules.php?modname=schoolsetup/Schools.php&school_id='.UserSchool().' style="text-decoration:none">back to School Information</a></div>';
					PopTable('footer');
					return false;
				}
				else
					return true;
		break;
		
		case 'subject':
				$find_student = DBGet(DBQuery('SELECT COUNT(sch.STUDENT_ID) AS STUDENT_EXIST FROM schedule sch,course_periods cp, courses c WHERE c.SUBJECT_ID=\''.$_REQUEST['subject_id'].'\''));
				$find_student = $find_student[1]['STUDENT_EXIST'];
				if($find_student>0)	
				{
					PopTable('header','Unable to Delete');
					DrawHeaderHome('<font color=red>Subject cannot be deleted. There are <font color=green>'.$find_student.'</font> Students Enrolled</font>');
					echo '<div align=right><a href=Modules.php?modname=schoolsetup/Courses.php&subject_id='.strip_tags(trim($_REQUEST['subject_id'])).' style="text-decoration:none"><b>back to Subject</b></a></div>';
					PopTable('footer');
					return false;
				}
				else
					return true;
		break;
		
		case 'course':
				$find_student = DBGet(DBQuery('SELECT COUNT(sch.STUDENT_ID) AS STUDENT_EXIST FROM schedule sch,course_periods cp, courses c WHERE sch.COURSE_ID=\''.$_REQUEST['course_id'].'\' AND sch.COURSE_ID=c.COURSE_ID AND c.SUBJECT_ID=\''.$_REQUEST['subject_id'].'\''));
				$find_student = $find_student[1]['STUDENT_EXIST'];
				if($find_student>0)	
				{
					PopTable('header','Unable to Delete');
					DrawHeaderHome('<font color=red>Course cannot be deleted. There are <font color=green>'.$find_student.'</font> Students Enrolled</font>');
					echo '<div align=right><a href=Modules.php?modname=schoolsetup/Courses.php&subject_id='.strip_tags(trim($_REQUEST['subject_id'])).'&course_id='.strip_tags(trim($_REQUEST['course_id'])).' style="text-decoration:none"><b>back to Course</b></a></div>';
					PopTable('footer');
					return false;
				}
				else
					return true;
		break;
		
		case 'course period':
				$find_student = DBGet(DBQuery('SELECT COUNT(sch.STUDENT_ID) AS STUDENT_EXIST FROM schedule sch,course_periods cp, courses c WHERE sch.COURSE_ID=\''.$_REQUEST['course_id'].'\' AND sch.COURSE_ID=c.COURSE_ID AND sch.COURSE_PERIOD_ID=\''.$_REQUEST['course_period_id'].'\' AND sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND c.SUBJECT_ID=\''.$_REQUEST['subject_id'].'\''));
				$find_student = $find_student[1]['STUDENT_EXIST'];
				if($find_student>0)	
				{
					PopTable('header','Unable to Delete');
					DrawHeaderHome('<font color=red>Period cannot be deleted. There are <font color=green>'.$find_student.'</font> Students Enrolled</font>');
					echo '<div align=right><a href=Modules.php?modname=schoolsetup/Courses.php&subject_id='.strip_tags(trim($_REQUEST['subject_id'])).'&course_id='.strip_tags(trim($_REQUEST['course_id'])).'&course_period_id='.strip_tags(trim($_REQUEST['course_period_id'])).' style="text-decoration:none"><b>back to Period</b></a></div>';
					PopTable('footer');
					return false;
				}
				else
					return true;
		break;
		
		case 'calendar':
		case 'marking_period':
		case 'grade_level':
	}
}
?>
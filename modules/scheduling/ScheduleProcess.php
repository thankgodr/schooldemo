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
include('../../RedirectRootInc.php');
include('../../Warehouse.php');
$course_period_id=$_REQUEST['cp_id'];
$insert=$_REQUEST['insert'];
$date=  DBDate();
if($insert=='true')
{
    $course_RET=DBGet(DBQuery("SELECT *,cp.title AS CP_TITLE FROM course_periods cp,course_period_var cpv,school_periods sp WHERE cp.course_period_id=cpv.course_period_id AND cpv.period_id=sp.period_id AND cp.course_period_id=$course_period_id"));
    $course=$course_RET[1];
    
    $varified=VerifyStudentSchedule($course_RET);
    if($varified===true)
    {
        $course[MP]=($course[MARKING_PERIOD_ID]!=''?$course[MP]:'FY');
        $course[MARKING_PERIOD_ID]=($course[MARKING_PERIOD_ID]!=''?$course[MARKING_PERIOD_ID]:GetMPId('FY'));
        DBQuery("INSERT INTO temp_schedule(SYEAR,SCHOOL_ID,STUDENT_ID,START_DATE,MODIFIED_BY,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID) values('".UserSyear()."','".UserSchool()."','".UserStudentID()."','".$date."','".User('STAFF_ID')."','$course[COURSE_ID]','".$course_period_id."','$course[MP]','$course[MARKING_PERIOD_ID]')");
        $html = 'resp';
        $html .= '<tr id="selected_course_tr_'.$course["COURSE_PERIOD_ID"].'"><td align=left><INPUT type="checkbox" id="selected_course_'.$course["COURSE_PERIOD_ID"].'" name="selected_course_periods[]" checked="checked" value="'.$course["COURSE_PERIOD_ID"].'"></td><td><b> '.$course["CP_TITLE"].'</b></td></tr>';
        $_SESSION['course_periods'][$course_period_id]=$course['CP_TITLE'];
    }
    else
    {
        $html='conf<strong>'.$varified.'</strong>';
        $html .='<input type=hidden id=conflicted_cp value='.$course_period_id.'>';
    }
}
elseif($insert=='false')
{
    DBQuery("DELETE FROM temp_schedule WHERE course_period_id=$course_period_id");
    unset($_SESSION['course_periods'][$course_period_id]);
}
echo $html;
?>

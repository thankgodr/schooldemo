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
unset($_SESSION['_REQUEST_vars']['subject_id']);
unset($_SESSION['_REQUEST_vars']['course_id']);
unset($_SESSION['_REQUEST_vars']['course_period_id']);
foreach($_REQUEST['tables']['courses'][$_REQUEST['course_id']] as $in=>$dt)
{
    $_REQUEST['tables']['courses'][$_REQUEST['course_id']][$in]=str_replace("'","\'",$dt);
}
foreach($_REQUEST['tables']['course_periods'][$_REQUEST['cp_id']] as $in=>$dt)
{
    $_REQUEST['tables']['course_periods'][$_REQUEST['cp_id']][$in]=str_replace("'","\'",$dt);
}
if(isset($_SESSION['seat_error']))
{
    echo $_SESSION['seat_error'];
    unset($_SESSION['seat_error']);
}
if($_REQUEST['error']=='Blocked_assoc')
{
    echo "<font color=red><b>Cannot Modify this course period as it has association. </b></font>";  
    unset($_REQUEST['error']);
}
if($_REQUEST['error']=='Blocked_period_room')
{
    echo "<font color=red><b>Cannot Modify period or room as this course period has association. </b></font>";  
    unset($_REQUEST['error']);
}
if($_REQUEST['course_period_id']=='new')
{
    if($_REQUEST['schedule_type']=='fixed' && $_REQUEST['tables']['course_periods']['new']['TOTAL_SEATS']!=0 && $_REQUEST['tables']['course_period_var']['new']['ROOM_ID']!='')
    {
        $get_seat=DBGet(DBQuery('SELECT CAPACITY FROM rooms WHERE ROOM_ID='.$_REQUEST['tables']['course_period_var']['new']['ROOM_ID']));
        if($get_seat[1]['CAPACITY']<$_REQUEST['tables']['course_periods']['new']['TOTAL_SEATS'])
        {
        $_REQUEST['tables']['course_periods']['new']['TOTAL_SEATS']=$get_seat[1]['CAPACITY'];   
        echo '<font color=red>Total seats cannot be more than room capcity.</font>';   
        }
    }
    if($_REQUEST['tables']['course_periods']['new']['SCHEDULE_TYPE']=='VARIABLE' && $_REQUEST['tables']['course_periods']['new']['TOTAL_SEATS']!=0 && $_REQUEST['course_period_variable']['new']['ROOM_ID']!='')
    {
        $get_seat=DBGet(DBQuery('SELECT CAPACITY FROM rooms WHERE ROOM_ID='.$_REQUEST['course_period_variable']['new']['ROOM_ID']));
        if($get_seat[1]['CAPACITY']<$_REQUEST['tables']['course_periods']['new']['TOTAL_SEATS'])
        {
        $_REQUEST['tables']['course_periods']['new']['TOTAL_SEATS']=$get_seat[1]['CAPACITY'];   
        echo '<font color=red>Total seats cannot be more than room capcity.</font>';   
        }
    }
}
if($_REQUEST['course_period_id']!='new')
{
    
    if($_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['SCHEDULE_TYPE']=='FIXED' )
    {
        if($_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS']!='')
        $total_seats=$_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS'];
        else
        {
        $total_seats=DBGet(DBQuery('SELECT TOTAL_SEATS FROM course_periods WHERE COURSE_PERIOD_ID='.$_REQUEST['course_period_id']));    
        $total_seats=$total_seats[1]['TOTAL_SEATS'];
        }
        if($_REQUEST['tables']['course_period_var'][$_REQUEST['course_period_id']]['ROOM_ID']!='')
        $room_id=$_REQUEST['tables']['course_period_var'][$_REQUEST['course_period_id']]['ROOM_ID'];
        else
        {
        $room_id=DBGet(DBQuery('SELECT ROOM_ID FROM course_period_var WHERE COURSE_PERIOD_ID='.$_REQUEST['course_period_id']));    
        $room_id=$room_id[1]['ROOM_ID'];
        }
        $get_seat=DBGet(DBQuery('SELECT CAPACITY FROM rooms WHERE ROOM_ID='.$room_id));
       
        if($get_seat[1]['CAPACITY']<$total_seats)
        {   
            $check_asociation=DBGet(DBQuery('SELECT COUNT(*) as REC_EX FROM schedule WHERE course_period_id='.$_REQUEST['course_period_id'].' AND (END_DATE IS NULL OR END_DATE=\'0000-00-00\' OR END_DATE=\'\' OR END_DATE<\''.date('Y-m-d').'\') '));    
            if($check_asociation[1]['REC_EX']>0)
            {
            $old_room=DBGet(DBQuery('SELECT cpv.ROOM_ID,cp.TOTAL_SEATS FROM course_period_var cpv,course_periods cp WHERE cp.COURSE_PERIOD_ID='.$_REQUEST['course_period_id'].' AND cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID'));      
            $_REQUEST['tables']['course_period_var'][$_REQUEST['course_period_id']]['ROOM_ID']=$old_room[1]['ROOM_ID'];
            $_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS']=$old_room[1]['TOTAL_SEATS'];   
            }
            else
            {
            $_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS']=$get_seat[1]['CAPACITY'];   
            }
            
            echo '<font color=red>Total seats cannot be more than room capcity.</font>';   
        }
    }
    
    
    if($_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['SCHEDULE_TYPE']=='VARIABLE')
    {
        $room_ids_check=array();
        foreach($_REQUEST['course_period_variable'][$_REQUEST['course_period_id']] as $tmp_ai=>$tmp_ad)
        {
            
            if($tmp_ad['ROOM_ID']!='')
                $room_ids_check[$tmp_ai]=$tmp_ad['ROOM_ID'];
            else
            {
                if($tmp_ai!='n')
                {
                $room_ids=DBGet(DBQuery('SELECT ROOM_ID FROM course_period_var WHERE ID='.$tmp_ai));
                $room_ids_check[$tmp_ai]=$room_ids[1]['ROOM_ID'];
                }
            }
        }
        
        $room_ids_check=implode(',',$room_ids_check);
        $get_seat=DBGet(DBQuery('SELECT MIN(CAPACITY) as CAPACITY FROM rooms WHERE ROOM_ID IN ('.$room_ids_check.')'));
        
        if($_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS']!='')
            $total_seats=$_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS'];
        else
        {
            $total_seats=DBGet(DBQuery('SELECT TOTAL_SEATS FROM course_periods WHERE COURSE_PERIOD_ID='.$_REQUEST['course_period_id']));
            $total_seats=$total_seats[1]['TOTAL_SEATS'];
        }
        if($get_seat[1]['CAPACITY']<$total_seats)
        {
            
            $check_asociation=DBGet(DBQuery('SELECT COUNT(*) as REC_EX FROM schedule WHERE course_period_id='.$_REQUEST['course_period_id'].' AND (END_DATE IS NULL OR END_DATE=\'0000-00-00\' OR END_DATE=\'\' OR END_DATE<\''.date('Y-m-d').'\') '));    
            if($check_asociation[1]['REC_EX']>0)
            {
                $old_seat=DBGet(DBQuery('SELECT TOTAL_SEATS FROM course_periods WHERE COURSE_PERIOD_ID='.$_REQUEST['course_period_id']));      
                $_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS']=$old_seat[1]['TOTAL_SEATS'];       
                foreach($_REQUEST['course_period_variable'][$_REQUEST['course_period_id']] as $tmp_ai=>$tmp_ad)
                {
                    if($tmp_ai!='n')
                    {
                    $old_room=DBGet(DBQuery('SELECT cpv.ROOM_ID,cp.TOTAL_SEATS FROM course_period_var cpv,course_periods cp WHERE cpv.ID='.$tmp_ai.' AND cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID'));      
                    $_REQUEST['course_period_variable'][$_REQUEST['course_period_id']][$tmp_ai]['ROOM_ID']=$old_room[1]['ROOM_ID'];
                    }
                }
            }
            else
            {
                $_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS']=$get_seat[1]['CAPACITY'];       
            }
            
            echo '<font color=red>Total seats cannot be more than room capcity.</font>'; 
        }
    }
    
    
    if($_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['SCHEDULE_TYPE']=='BLOCKED' && $_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS']!='')
    {
        
        $total_seats=$_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS'];
        
        $room_ids_check=array();
        $room_ids=DBGet(DBQuery('SELECT ROOM_ID FROM course_period_var WHERE COURSE_PERIOD_ID='.$_REQUEST['course_period_id']));
        
        foreach($room_ids as $room_ids_data)
        {
            $room_ids_check[$room_ids_data['ROOM_ID']]=$room_ids_data['ROOM_ID'];
        }
        if(count($room_ids_check)>0)
        {
        $room_ids_check=implode(",",$room_ids_check);
        $get_seat=DBGet(DBQuery('SELECT MIN(CAPACITY) as CAPACITY FROM rooms WHERE ROOM_ID IN ('.$room_ids_check.')'));
        
        if($get_seat[1]['CAPACITY']<$total_seats)
        {   
            $check_asociation=DBGet(DBQuery('SELECT COUNT(*) as REC_EX FROM schedule WHERE course_period_id='.$_REQUEST['course_period_id'].' AND (END_DATE IS NULL OR END_DATE=\'0000-00-00\' OR END_DATE=\'\' OR END_DATE<\''.date('Y-m-d').'\') '));    
            if($check_asociation[1]['REC_EX']>0)
            {
            $old_room=DBGet(DBQuery('SELECT cpv.ROOM_ID,cp.TOTAL_SEATS FROM course_period_var cpv,course_periods cp WHERE cp.COURSE_PERIOD_ID='.$_REQUEST['course_period_id'].' AND cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID'));      
            $_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS']=$old_room[1]['TOTAL_SEATS'];   
            }
            else
            {
            $_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS']=$get_seat[1]['CAPACITY'];   
            }
            
            echo '<font color=red>Total seats cannot be more than room capcity.</font>';   
        }
        
        
        }
        
    }
    
    if($_REQUEST['modfunc']=='detail'  && $_REQUEST['values']['ROOM_ID']!='' )
    {
        
        $total_seats=DBGet(DBQuery('SELECT TOTAL_SEATS FROM course_periods WHERE COURSE_PERIOD_ID='.$_REQUEST['course_period_id']));    
        $total_seats=$total_seats[1]['TOTAL_SEATS'];
        $room_id=$_REQUEST['values']['ROOM_ID'];
        
        $get_seat=DBGet(DBQuery('SELECT CAPACITY FROM rooms WHERE ROOM_ID='.$room_id));
        if($get_seat[1]['CAPACITY']<$total_seats)
        {   
            $check_asociation=DBGet(DBQuery('SELECT COUNT(*) as REC_EX FROM schedule WHERE course_period_id='.$_REQUEST['course_period_id'].' AND (END_DATE IS NULL OR END_DATE=\'0000-00-00\' OR END_DATE=\'\' OR END_DATE<\''.date('Y-m-d').'\') '));    
            if($check_asociation[1]['REC_EX']>0)
            {
               if($_REQUEST['mode']=='add')
               {
                 //unset($_REQUEST);  
                 $_SESSION['seat_error']='<font color=red>Total seats cannot be more than room capcity.</font>';   
                  echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&subject_id='.$_REQUEST[subject_id].'&course_id='.$_REQUEST[course_id].'&course_period_id='.$_REQUEST[course_period_id].'&month='.date(strtotime($_REQUEST['meet_date'])).'"; window.close();</script>'; 
               }
               if($_REQUEST['mode']=='edit')
               {
                   $old_room=DBGet(DBQuery('SELECT ROOM_ID FROM course_period_var WHERE ID='.$_REQUEST['cpv_id']));
                   $_REQUEST['values']['ROOM_ID']=$old_room[1]['ROOM_ID'];
                   $_SESSION['seat_error']='<font color=red>Total seats cannot be more than room capcity.</font>'; 
                    echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&subject_id='.$_REQUEST[subject_id].'&course_id='.$_REQUEST[course_id].'&course_period_id='.$_REQUEST[course_period_id].'&month='.date(strtotime($_REQUEST['meet_date'])).'"; window.close();</script>'; 
               }
               
            }
            else
            {
            DBQuery('UPDATE course_periods SET TOTAL_SEATS='.$get_seat[1]['CAPACITY'].' WHERE COURSE_PERIOD_ID='.$_REQUEST['course_period_id']);
            $_SESSION['seat_error']='<font color=red>Total seats cannot be more than room capcity.</font>';   
             echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&subject_id='.$_REQUEST[subject_id].'&course_id='.$_REQUEST[course_id].'&course_period_id='.$_REQUEST[course_period_id].'&month='.date(strtotime($_REQUEST['meet_date'])).'"; window.close();</script>'; 
            }
        }
    }
}
$not_pass_update=false;
if ($_REQUEST['course_period_id'] != 'new') {
    if ($_REQUEST['w_course_period_id']) {
        $sql_parent = "UPDATE course_periods SET PARENT_ID=" . $_REQUEST['w_course_period_id'] . " WHERE COURSE_PERIOD_ID=" . $_REQUEST['course_period_id'];
        DBQuery($sql_parent);
    }
}
if($_REQUEST['action']=='delete')
{
    if (scheduleAssociation($_REQUEST['course_period_id'])) {
                $scheduleAssociation = true;
                }
                if (gradeAssociation($_REQUEST['course_period_id'])) {
                    $gradeAssociation = true;
                }
                if(!$scheduleAssociation && !$gradeAssociation)
                {
                    if (DeletePromptCommon('course period')) 
                    {
                        $checking_days1=DBGet(DBQuery('SELECT COUNT(*) AS TOTAL FROM course_period_var WHERE course_period_id='.$_REQUEST[course_period_id]));
                        if($checking_days1[1]['TOTAL']>1)
                        {
                        DBQuery("DELETE FROM course_period_var WHERE id=$_REQUEST[cpv_id]");
                        $data_sql="SELECT period_id,days FROM course_period_var WHERE course_period_id=$_REQUEST[course_period_id]";
                        $data_RET=  DBGet(DBQuery($data_sql));
                        foreach($data_RET as $count=>$data)
                        {
                                if($data['PERIOD_ID']!='')
                                {
                                    $period='';
                                    $qry="SELECT short_name FROM school_periods WHERE period_id=$data[PERIOD_ID]";
                                    $period=  DBGet(DBQuery($qry));
                                    $period=$period[1];
                                    $p.=$period['SHORT_NAME'];
                                }
                                if($data['DAYS']!='')
                                    $d.=$data['DAYS'];
                        }
                        $cp_data_sql="SELECT mp,short_name,marking_period_id,teacher_id FROM course_periods WHERE course_period_id=$_REQUEST[course_period_id]";
                        $cp_data_RET=  DBGet(DBQuery($cp_data_sql));
                        
                        $cp_data_RET=$cp_data_RET[1];
                        if($cp_data_RET['MP']!='FY' && $_REQUEST['date_range']=='mp')
                        {
                            if($cp_data_RET['MP']=='SEM')
                                $table=' school_semesters';
                            if($cp_data_RET['MP']=='QTR')
                                $table='  school_quarters';
                            
                            $mp_sql="SELECT short_name FROM $table WHERE marking_period_id=$cp_data_RET[MARKING_PERIOD_ID]";
                            $mp=  DBGet(DBQuery($mp_sql));
                            $mp=' - '.$mp[1]['SHORT_NAME'];
                        }
                        else
                            $mp='Custom';
                        
                        $teacher_sql="SELECT first_name,last_name,middle_name FROM staff WHERE staff_id=$cp_data_RET[TEACHER_ID]";
                        $teacher_RET=  DBGet(DBQuery($teacher_sql));
                        $teacher_RET=$teacher_RET[1];
                        $teacher.=$teacher_RET['FIRST_NAME'];
                        if($teacher_RET['MIDDLE_NAME']!='')
                           $teacher.=' '.$teacher_RET['MIDDLE_NAME'];
                        $teacher.=' '.$teacher_RET['LAST_NAME'];
                        
                        $title_full=$p.$mp.' - '.$d.' - '.$cp_data_RET['SHORT_NAME'].' - '.$teacher;
                        $update_title_sql="UPDATE course_periods SET title='".str_replace("'","''",trim($title_full))."' WHERE course_period_id=$_REQUEST[course_period_id]";
                       DBQuery($update_title_sql);
                        }
                        else
                        {
                         echo '<font color=red>Unable to delete data. Course period should have atleast one period.</font>';   
                        }
                        unset($_REQUEST['action']);
                        #header("Location: \"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=$_REQUEST[course_period_id]\"");
                    }
                }
                else
                {
                    echo '<font color=red>Unable to delete course period because it has association</font>';
                }
}
if(isset($_SESSION['conflict']))
{
    echo '<font color=red>'.$_SESSION['conflict'].'</font>';
    unset($_SESSION['conflict']);
}
if ($_REQUEST['modfunc'] != 'delete' && !$_REQUEST['subject_id']) {
    $subjects_RET = DBGet(DBQuery("SELECT SUBJECT_ID,TITLE FROM course_subjects WHERE SCHOOL_ID='" . UserSchool() . "' AND SYEAR='" . UserSyear() . "'"));
    if (count($subjects_RET) == 1)
        $_REQUEST['subject_id'] = $subjects_RET[1]['SUBJECT_ID'];
}

if (clean_param($_REQUEST['course_modfunc'], PARAM_ALPHAMOD) == 'search') {
    PopTable('header', 'Search');
    echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&course_modfunc=search method=POST>";
    echo '<TABLE><TR><TD><INPUT type=text class=cell_floating name=search_term value="' . $_REQUEST['search_term'] . '"></TD><TD><INPUT type=submit class=btn_medium value=Search onclick=\'formload_ajax("F1")\';></TD></TR></TABLE>';
    echo '</FORM>';
    PopTable('footer');

    if ($_REQUEST['search_term']) {
        $subjects_RET = DBGet(DBQuery("SELECT SUBJECT_ID,TITLE FROM course_subjects WHERE (UPPER(TITLE) LIKE '%" . strtoupper($_REQUEST['search_term']) . "%' OR UPPER(SHORT_NAME) = '" . strtoupper($_REQUEST['search_term']) . "') AND SYEAR='" . UserSyear() . "' AND SCHOOL_ID='" . UserSchool() . "'"));
        $courses_RET = DBGet(DBQuery("SELECT SUBJECT_ID,COURSE_ID,TITLE FROM courses WHERE (UPPER(TITLE) LIKE '%" . strtoupper($_REQUEST['search_term']) . "%' OR UPPER(SHORT_NAME) = '" . strtoupper($_REQUEST['search_term']) . "') AND SYEAR='" . UserSyear() . "' AND SCHOOL_ID='" . UserSchool() . "'"));
        $periods_RET = DBGet(DBQuery("SELECT c.SUBJECT_ID,cp.COURSE_ID,cp.COURSE_PERIOD_ID,cp.TITLE FROM course_periods cp,courses c WHERE cp.COURSE_ID=c.COURSE_ID AND (UPPER(cp.TITLE) LIKE '%" . strtoupper($_REQUEST['search_term']) . "%' OR UPPER(cp.SHORT_NAME) = '" . strtoupper($_REQUEST['search_term']) . "') AND cp.SYEAR='" . UserSyear() . "' AND cp.SCHOOL_ID='" . UserSchool() . "'"));

        echo '<TABLE><TR><TD valign=top>';
        $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
        $link['TITLE']['variables'] = array('subject_id' => 'SUBJECT_ID');
        ListOutput($subjects_RET, array('TITLE' => 'Subject'), 'Subject', 'Subjects', $link, array(), array('search' => false, 'save' => false));
        echo '</TD><TD valign=top>';
        $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
        $link['TITLE']['variables'] = array('subject_id' => 'SUBJECT_ID', 'course_id' => 'COURSE_ID');
        ListOutput($courses_RET, array('TITLE' => 'Course'), 'Course', 'Courses', $link, array(), array('search' => false, 'save' => false));
        echo '</TD><TD valign=top>';
        $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
		$link['TITLE']['variables'] = array('subject_id'=>'SUBJECT_ID','course_id'=>'COURSE_ID','course_period_id'=>'COURSE_PERIOD_ID');
		ListOutput($periods_RET,array('TITLE'=>'Course Period'),'Course Period','Course Periods',$link,array(),array('search'=>false,'save'=>false));
        echo '</TD></TR></TABLE>';
    }
}

if (clean_param($_REQUEST['course_modfunc'], PARAM_ALPHAMOD) == 'standard_search') {
    PopTable('header', 'Search');
    echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&course_modfunc=search method=POST>";
    echo '<TABLE><TR><TD><INPUT type=text class=cell_floating name=search_term value="' . $_REQUEST['search_term'] . '"></TD><TD><INPUT type=submit class=btn_medium value=Search onclick=\'formload_ajax("F1")\';></TD></TR></TABLE>';
    echo '</FORM>';
    PopTable('footer');

    if ($_REQUEST['search_term']) {
        $subjects_RET = DBGet(DBQuery("SELECT SUBJECT_ID,TITLE FROM course_subjects WHERE (UPPER(TITLE) LIKE '%" . strtoupper($_REQUEST['search_term']) . "%' OR UPPER(SHORT_NAME) = '" . strtoupper($_REQUEST['search_term']) . "') AND SYEAR='" . UserSyear() . "' AND SCHOOL_ID='" . UserSchool() . "'"));
        $courses_RET = DBGet(DBQuery("SELECT SUBJECT_ID,COURSE_ID,TITLE FROM courses WHERE (UPPER(TITLE) LIKE '%" . strtoupper($_REQUEST['search_term']) . "%' OR UPPER(SHORT_NAME) = '" . strtoupper($_REQUEST['search_term']) . "') AND SYEAR='" . UserSyear() . "' AND SCHOOL_ID='" . UserSchool() . "'"));
        $periods_RET = DBGet(DBQuery("SELECT c.SUBJECT_ID,cp.COURSE_ID,cp.COURSE_PERIOD_ID,cp.TITLE FROM course_periods cp,courses c WHERE cp.COURSE_ID=c.COURSE_ID AND (UPPER(cp.TITLE) LIKE '%" . strtoupper($_REQUEST['search_term']) . "%' OR UPPER(cp.SHORT_NAME) = '" . strtoupper($_REQUEST['search_term']) . "') AND cp.SYEAR='" . UserSyear() . "' AND cp.SCHOOL_ID='" . UserSchool() . "'"));

        echo '<TABLE><TR><TD valign=top>';
        $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
        $link['TITLE']['variables'] = array('subject_id' => 'SUBJECT_ID');
        ListOutput($subjects_RET, array('TITLE' => 'Subject'), 'Subject', 'Subjects', $link, array(), array('search' => false, 'save' => false));
        echo '</TD><TD valign=top>';
        $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
        $link['TITLE']['variables'] = array('subject_id' => 'SUBJECT_ID', 'course_id' => 'COURSE_ID');
        ListOutput($courses_RET, array('TITLE' => 'Course'), 'Course', 'Courses', $link, array(), array('search' => false, 'save' => false));
        echo '</TD><TD valign=top>';
        $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
		$link['TITLE']['variables'] = array('subject_id'=>'SUBJECT_ID','course_id'=>'COURSE_ID','course_period_id'=>'COURSE_PERIOD_ID');
		ListOutput($periods_RET,array('TITLE'=>'Course Period'),'Course Period','Course Periods',$link,array(),array('search'=>false,'save'=>false));
        echo '</TD></TR></TABLE>';
    }
}
// UPDATING
if (clean_param($_REQUEST['tables'], PARAM_NOTAGS) && ($_POST['tables'] || $_REQUEST['ajax']) && AllowEdit()) {

    $where = array('course_subjects' => 'SUBJECT_ID',
        'courses' => 'COURSE_ID',
        'course_periods' => 'COURSE_PERIOD_ID','course_period_var'=>'COURSE_PERIOD_ID');

    if ($_REQUEST['tables']['parent_id'])
        $_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['PARENT_ID'] = $_REQUEST['tables']['parent_id'];

    //===================================For custom range==========================

        if($_REQUEST['month_begin'] && $_REQUEST['day_begin'] && $_REQUEST['year_begin'])
        {
	while(!VerifyDate($begin = $_REQUEST['day_begin'].'-'.$_REQUEST['month_begin'].'-'.$_REQUEST['year_begin']))
		$_REQUEST['day_begin']--;
                $_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['BEGIN_DATE']=date('Y-m-d',strtotime($begin));
        }
        if($_REQUEST['month_end'] && $_REQUEST['day_end'] && $_REQUEST['year_end'])
        {
	while(!VerifyDate($end = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['year_end']))
		$_REQUEST['day_end']--;
                $_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['END_DATE']=date('Y-m-d',  strtotime($end));
        }
       
if($_REQUEST['course_period_variable'])
{
   
    if (scheduleAssociation($_REQUEST['course_period_id'])) {
                $scheduleAssociation = true;
                }
                if (gradeAssociation($_REQUEST['course_period_id'])) {
                    $gradeAssociation = true;
                }
               
                    $columns['COURSE_PERIOD_ID']=$_REQUEST['course_period_id'];
                    $values=DBGet(DBQuery("SELECT * FROM course_periods WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'"));
                    if($_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TEACHER_ID'])
                    $columns['TEACHER_ID']=$_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TEACHER_ID'];
                    else
                        $columns['TEACHER_ID']=$values[1]['TEACHER_ID'];
                    
                    if($_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['SECONDARY_TEACHER_ID'])
                            $columns['SECONDARY_TEACHER_ID']=$_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['SECONDARY_TEACHER_ID'];
                        else
                            $columns['SECONDARY_TEACHER_ID']=$values[1]['SECONDARY_TEACHER_ID'];

                    if($_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['BEGIN_DATE'])
                            $columns['BEGIN_DATE']=$_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['BEGIN_DATE'];
                    else
                        $columns['BEGIN_DATE']=$values[1]['BEGIN_DATE'];
                    
                    if($_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['END_DATE'])
                            $columns['END_DATE']=$_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['END_DATE'];
                    else
                        $columns['END_DATE']=$values[1]['END_DATE'];
                    if($_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['MARKING_PERIOD_ID'])
                        $columns['MARKING_PERIOD_ID']=$_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['MARKING_PERIOD_ID'];
                    else
                        $columns['MARKING_PERIOD_ID']=$values[1]['MARKING_PERIOD_ID'];
                   $columns['CP_SECTION']='cpv';
                    foreach($_REQUEST['course_period_variable'] as $cp_id=>$days)
                    {
                        if($cp_id!='new')
                        { 
                            foreach($days as $day=>$period)
                            {
                                if($day!='n')
                                {
                                $day_exits=  DBGet(DBQuery("SELECT DAYS,PERIOD_ID,ROOM_ID FROM course_period_var WHERE id='$day' AND course_period_id=$cp_id"));
                                if(isset($period['DAYS']))
                                    $days_str .="'".$period['DAYS']."',";
                                if($day_exits)
                                {
                                  
                                    if(($period['PERIOD_ID'] && $period['PERIOD_ID']!=$day_exits[1]['PERIOD_ID']) || ($period['ROOM_ID'] && $period['ROOM_ID']!=$day_exits[1]['ROOM_ID']) || ($period['DAYS'] && $period['DAYS']!=$day_exits[1]['DAYS']) || ($columns['SECONDARY_TEACHER_ID']!=$values[1]['SECONDARY_TEACHER_ID']) || ($columns['TEACHER_ID']!=$values[1]['TEACHER_ID']) )
                                    {

                                        $columns['START_TIME']=$period['START_TIME'];
                                        $columns['END_TIME']=$period['END_TIME'];
                                        if($period['PERIOD_ID'])
                                            $columns['PERIOD_ID']=$period['PERIOD_ID'];
                                        else
                                            $columns['PERIOD_ID']=$day_exits[1]['PERIOD_ID'];
                                        if($period['ROOM_ID'])
                                            $columns['ROOM_ID']=$period['ROOM_ID'];
                                        else
                                            $columns['ROOM_ID']=$day_exits[1]['ROOM_ID'];
                                        $columns['DAYS']=$period['DAYS'];
                                        $columns['SELECT_DAYS']=$period['DAYS'];
                                        $columns['CP_VAR_ID']=$day;
                                        
                                        $columns['ID']=$period['ID'];
                                        $conflict=  VerifyVariableSchedule_Update($columns);
                                        if($conflict!==true)
                                        {
                                            echo '<font color=red>'.$conflict.'</font>';
                                          
                                            $not_pass_update=true;
                                            break 2;
                                        }
                                    }
                                    if(($period['PERIOD_ID']=='' || $period['ROOM_ID']=='') && !$period['START_TIME'])
                                    {
                                        $columns['PERIOD_ID']=$period['PERIOD_ID'];
                                        $columns['ROOM_ID']=$period['ROOM_ID'];
                                        $columns['SELECT_DAYS']=$period['DAYS'];
                                        $conflict=  VerifyVariableSchedule_Update($columns);
                                        if($conflict!==true)
                                        {
                                            echo '<font color=red>'.$conflict.'</font>';
                                          
                                            $not_pass_update=true;
                                            break 2;
                                        }
                                    }
                                    $up_day=false;
                                    $check_does_attendance=DBGet(DBQuery("SELECT DOES_ATTENDANCE FROM course_period_var WHERE id=".$period['ID']." AND course_period_id=$cp_id"));
                                    if($check_does_attendance[1]['DOES_ATTENDANCE']=='Y')
                                    {
                                        if(!isset($period['DOES_ATTENDANCE']))
                                            $period['DOES_ATTENDANCE']='N';
                                    }
                                    $days_upsql="UPDATE course_period_var SET ";
                                    foreach($period as $column=>$value)
                                    {
                                        
                                        if(isset($value))
                                        {
                                            if($column=='DOES_ATTENDANCE')
                                            {
                                             if($value=='Y')
                                             {
                                                $days_upsql .=$column." = '".$value."',";
                                                $up_day=true;
                                             }
                                            else
                                            {
                                                if(!scheduleAssociation($_REQUEST['course_period_id']))
                                                {
                                                $days_upsql .=$column." = NULL,"; 
                                                $up_day=true;
                                                }
                                            }
                                            
                                            }
                                            else
                                            {
                                               if(!scheduleAssociation($_REQUEST['course_period_id']))
                                                { 
                                                    if($column=='DAYS')
                                                    {
                                                     if($value!='')
                                                        $days_upsql .=$column." = '".$value."',";
                                                     else
                                                         $day_blank_error='<font color=red>Day cannot be blank.</font>';

                                                     $up_day=true;
                                                    }
                                                    else
                                                    {

                                                    if($value!='')
                                                        $days_upsql .=$column." = '".$value."',";
                                                    else
                                                        $days_upsql .=$column." = NULL,";
                                                    $up_day=true;

                                                    }

                                            }
                                            else
                                            {
                                                $check_cp=  DBGet(DBQuery("SELECT * FROM course_period_var WHERE course_period_id='".$_REQUEST['course_period_id']."'"));
                                                $check_cp=$check_cp[1];
                                                if(($period['DAYS']!='' && $check_cp['DAYS']!=$period['DAYS']) || ($period['ROOM_ID']!='' && $check_cp['ROOM_ID']!=$period['ROOM_ID']) || ($period['PERIOD_ID']!='' && $check_cp['PERIOD_ID']!=$period['PERIOD_ID']))
                                                    $assoc_err="<font color=red><b>Cannot Modify this course period as it has association. </b></font>";
                                            }
                                        }
                                            
                                        }
                                        
                                    }
                                    $days_upsql =  substr($days_upsql,0,-1);
                                     $days_upsql .=" WHERE id=".$period['ID']." AND course_period_id=$cp_id";

                                    if($up_day)
                                    {
                                        DBQuery($days_upsql);
                                    }
                                    
  
                            }
                            }
                            else
                            {
                                if($period['START_TIME']!='' && $period['END_TIME']!='' && $period['PERIOD_ID']!='' && $period['ROOM_ID']!='')
                                {
                                        $columns['START_TIME']=$period['START_TIME'];
                                        $columns['END_TIME']=$period['END_TIME'];
                                        $columns['PERIOD_ID']=$period['PERIOD_ID'];
                                        $columns['ROOM_ID']=$period['ROOM_ID'];
                                        $columns['CP_VAR_ID']='n';
                                        $columns['DAYS']=$period['DAYS'];
                                        $columns['SELECT_DAYS']=$period['DAYS'];
                                        $conflict=  VerifyVariableSchedule_Update($columns);
                                        if($conflict!==true)
                                        {
                                            echo '<font color=red>'.$conflict.'</font>';
                                   
                                            $not_pass_update=true;
                                            break 2;
                                        }
                                       if(!scheduleAssociation($_REQUEST['course_period_id']))
                                       {
                                            $days_sql="INSERT INTO course_period_var(course_period_id,days,period_id,start_time,end_time,room_id,does_attendance)VALUES";

                                             $days_sql .="($_REQUEST[course_period_id],'$period[DAYS]',$period[PERIOD_ID],'$period[START_TIME]','$period[END_TIME]',$period[ROOM_ID],'$period[DOES_ATTENDANCE]'),";
                                             $days_sql =  substr($days_sql,0,-1);
                                             DBQuery($days_sql);
                                       }
                                        
                                }
                                if($period['DAYS']!='' && ($period['START_TIME']=='' || $period['END_TIME']=='' || $period['PERIOD_ID']=='' || $period['ROOM_ID']==''))
                                    {
                                        $msg= '<font color=red>Please enter valid data</font>';
                                        unset($_REQUEST['course_period_variable']);
                                        echo $msg;
                                        unset($msg);
                                    }
                               unset($_REQUEST['course_period_variable']);
                            }

                            }

                    }
//                    
                }
                    if($not_pass_update!=true && $day_blank_error)
                    {
                        echo $day_blank_error;
                        unset($day_blank_error);
                    }
                            
                            
                }
    //=======================================================================
if($_REQUEST['tables']['course_period_var'])
{
    foreach ($_REQUEST['tables']['course_period_var'] as $columns_var)
    {
                if ($columns_var['DAYS']) {
                foreach ($columns_var['DAYS'] as $day => $y) {
                    if ($y == 'Y')
                        $days .= $day;
                }
                $columns_var['DAYS'] = $days;
                unset($days);
            }
    }
}

    if($_REQUEST['course_id']=='new')
    {
        if($_REQUEST['tables']['courses']['new']['SUBJECT_ID']!='')
        {
            $_REQUEST['subject_id']=$_REQUEST['tables']['courses']['new']['SUBJECT_ID'];
            unset($_REQUEST['tables']['courses']['new']['SUBJECT_ID']);
        }
        foreach($_REQUEST['tables']['courses']['new'] as $ci=>$cd)
        {
            $_REQUEST['tables']['courses']['new'][$ci]=str_replace('"','""',$cd);
        }
    }
    if($_REQUEST['course_id']!='new' && $_REQUEST['course_id']!='')
    {
        foreach($_REQUEST['tables']['courses'][$_REQUEST['course_id']] as $ci=>$cd)
        {
            $_REQUEST['tables']['courses'][$_REQUEST['course_id']][$ci]=str_replace('"','""',$cd);
            $_REQUEST['tables']['courses'][$_REQUEST['course_id']][$ci]=str_replace("\'","'",$cd);
        }
    }
    foreach ($_REQUEST['tables'] as $table_name => $tables) {
        $go = false;
        $flag_err='N';
       
        foreach ($tables as $id => $columns) {
            if(strtotime($columns['BEGIN_DATE'])>strtotime($columns['END_DATE']))
            {
                
                echo '<font color=red><b>Begin date cannot be grater than end date.</b></font>';
                $not_pass_update=true;
                break 2;
    
            }
            if ($columns['MARKING_PERIOD_ID']!='') {
                        $mark_id_dates=DBGet(DBQuery('SELECT * FROM marking_periods WHERE SCHOOL_ID='.UserSchool().' AND SYEAR='.UserSyear().' AND MARKING_PERIOD_ID='.$columns['MARKING_PERIOD_ID'].''));
                        $columns['BEGIN_DATE']=$mark_id_dates[1]['START_DATE'];
                        $columns['END_DATE']=$mark_id_dates[1]['END_DATE'];
                        if (GetMP($columns['MARKING_PERIOD_ID'], 'TABLE') == 'school_years')
                            $columns['MP'] = 'FY';
                        elseif (GetMP($columns['MARKING_PERIOD_ID'], 'TABLE') == 'school_semesters')
                            $columns['MP'] = 'SEM';
                        elseif (GetMP($columns['MARKING_PERIOD_ID'], 'TABLE') == 'school_quarters')
                            $columns['MP'] = 'QTR';
                    }
                    else {
                        if($columns['BEGIN_DATE'] && $columns['END_DATE'])
                        {
                        $columns['MARKING_PERIOD_ID']='';
                        $columns['MP'] = '';
                        }
                    }
            
            if ($columns['TOTAL_SEATS'] && !is_numeric($columns['TOTAL_SEATS']))
                $columns['TOTAL_SEATS'] = ereg_replace('[^0-9]+', '', $columns['TOTAL_SEATS']);
            $days_fix='';
            if ($columns['DAYS']) {
                foreach ($columns['DAYS'] as $day => $y) {
                    if ($y == 'Y')
                     $days_fix .= $day;
                }
              $columns['DAYS'] = $days_fix;
            }
            
            if ($id != 'new') {

               unset($scheduleAssociation);
               unset($gradeAssociation);
            $tot_seat=$_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS'];
            $does_attn=$_REQUEST['tables']['course_period_var'][$_REQUEST['course_period_id']]['DOES_ATTENDANCE'];
            if($tot_seat!='')
            {
                $seat_num=DBGet(DBQuery('SELECT FILLED_SEATS FROM course_periods WHERE COURSE_PERIOD_ID=\''.$_REQUEST['course_period_id'].'\' LIMIT 0,1 '));
                if($seat_num[1]['FILLED_SEATS']>$tot_seat)
                {
                    $tot_seat='error';
                }
            }
                $update=true;
                if($tot_seat!='' && $does_attn!='')
                {
                if (scheduleAssociation($id)) {
                        $scheduleAssociation = true;
                    }
                    if (gradeAssociation($id)) {
                        $gradeAssociation = true;
                    }
                    
                }
                if($tot_seat!='error'){
                
                if ($table_name == 'courses' && $columns['SUBJECT_ID'] && $columns['SUBJECT_ID'] != $_REQUEST['subject_id'])
                    $_REQUEST['subject_id'] = $columns['SUBJECT_ID'];

                $sql = "UPDATE $table_name SET ";

                if ($table_name == 'course_periods') {
                    
                    $current = DBGet(DBQuery("SELECT TEACHER_ID,MARKING_PERIOD_ID,SHORT_NAME,TOTAL_SEATS,CALENDAR_ID FROM course_periods WHERE " . $where[$table_name] . "='$id'"));
                    if ($scheduleAssociation)
                        $cur_total_seat = $current[1]['TOTAL_SEATS'];
                    
                    if ($columns['TEACHER_ID'])
                        $staff_id = $columns['TEACHER_ID'];
                    else
                        $staff_id = $current[1]['TEACHER_ID'];
                    if ($columns['CALENDAR_ID'])
                        $calendar_id = $columns['CALENDAR_ID'];
                    else
                        $calendar_id = $current[1]['CALENDAR_ID'];
                    if ($columns['PERIOD_ID'])
                        $period_id = $columns['PERIOD_ID'];
                    else
                        $period_id = $current[1]['PERIOD_ID'];
                    if (isset($columns['MARKING_PERIOD_ID']))
                        $marking_period_id = $columns['MARKING_PERIOD_ID'];
                    else
                        $marking_period_id = $current[1]['MARKING_PERIOD_ID'];
                    if ($columns['DAYS'])
                        $days = $columns['DAYS'];
                    else
                        $days = $current[1]['DAYS'];
                    if ($columns['SHORT_NAME'])
                        $short_name = $columns['SHORT_NAME'];
                    else
                        $short_name = $current[1]['SHORT_NAME'];
                    
                    
                    if (isset($columns['MARKING_PERIOD_ID'])) {
                            if (GetMP($columns['MARKING_PERIOD_ID'], 'TABLE') == 'school_years')
                                $columns['MP'] = 'FY';
                            elseif (GetMP($columns['MARKING_PERIOD_ID'], 'TABLE') == 'school_semesters')
                                $columns['MP'] = 'SEM';
                            else
                                $columns['MP'] = 'QTR';

                            if (GetMP($columns['MARKING_PERIOD_ID'], 'TABLE') != 'school_years')
                               $mp_title = GetMP($columns['MARKING_PERIOD_ID'], 'SHORT_NAME') . ' - ';
                        } else {
                            if (GetMP($current[1]['MARKING_PERIOD_ID'], 'TABLE') == 'school_years')
                                $current[1]['MP'] = 'FY';
                            elseif (GetMP($current[1]['MARKING_PERIOD_ID'], 'TABLE') == 'school_semesters')
                                $current[1]['MP'] = 'SEM';
                            else
                                $current[1]['MP'] = 'QTR';

                            if (GetMP($current[1]['MARKING_PERIOD_ID'], 'TABLE') != 'school_years')
                               $mp_title = GetMP($current[1]['MARKING_PERIOD_ID'], 'SHORT_NAME') . ' - ';
                        }
                        
                     $col['CP_SECTION']='cp';
                    if($columns['SCHEDULE_TYPE']=='VARIABLE' && $not_pass_update==false)
                    { 
                        $col['COURSE_PERIOD_ID']=$_REQUEST['course_period_id'];
                        $values=DBGet(DBQuery("SELECT * FROM course_periods WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'"));
                        if($columns['TEACHER_ID'])
                            $col['TEACHER_ID']=$columns['TEACHER_ID'];
                        else
                            $col['TEACHER_ID']=$values[1]['TEACHER_ID'];
                        
                         if($columns['SECONDARY_TEACHER_ID'])
                            $col['SECONDARY_TEACHER_ID']=$columns['SECONDARY_TEACHER_ID'];
                        else
                            $col['SECONDARY_TEACHER_ID']=$values[1]['SECONDARY_TEACHER_ID'];

                        if($columns['BEGIN_DATE'])
                                $col['BEGIN_DATE']=$columns['BEGIN_DATE'];
                        else
                            $col['BEGIN_DATE']=$values[1]['BEGIN_DATE'];

                        if($columns['END_DATE'])
                                $col['END_DATE']=$columns['END_DATE'];
                        else
                            $col['END_DATE']=$values[1]['END_DATE'];
                        if($columns['MARKING_PERIOD_ID'])
                            $col['MARKING_PERIOD_ID']=$columns['MARKING_PERIOD_ID'];
                        else
                            $col['MARKING_PERIOD_ID']=$values[1]['MARKING_PERIOD_ID'];
                        
                        

                        foreach($_REQUEST['course_period_variable'] as $cp_id=>$days)
                        {
                            foreach($days as $day=>$period)
                            {
                                
                                
                                $day_exits=  DBGet(DBQuery("SELECT DAYS,PERIOD_ID,ROOM_ID FROM course_period_var WHERE days='$day' AND course_period_id=$cp_id"));
                                $col['START_TIME']=$period['START_TIME'];
                                $col['END_TIME']=$period['END_TIME'];
                                if($period['PERIOD_ID'])
                                    $col['PERIOD_ID']=$period['PERIOD_ID'];
                                else
                                    $col['PERIOD_ID']=$day_exits[1]['PERIOD_ID'];
                                if($period['ROOM_ID'])
                                    $col['ROOM_ID']=$period['ROOM_ID'];
                                else
                                    $col['ROOM_ID']=$day_exits[1]['ROOM_ID'];
                                $col['DAYS']=$day;
                                $col['SELECT_DAYS']=$period['DAYS'];
                                

                                if($period['DAYS']!='' && $period['PERIOD_ID']!='' && $period['ROOM_ID']!='')
                                {

                                $conflict=VerifyVariableSchedule_Update($col);
                                if($conflict!==true)
                                {
                                    echo '<font color=red>'.$conflict.'</font>';
                                    $not_pass_update=true;
                                    break 4;
                                }
                                }
                                
                            }
                        }
                    }
                    if($columns['SCHEDULE_TYPE']=='VARIABLE')
                    {
                        $title_val=DBGet(DBQuery("SELECT DAYS,PERIOD_ID FROM course_period_var WHERE course_period_id='".$_REQUEST['course_period_id']."'"));
                        foreach ($title_val as $key => $title_value) {

                            $days_n[]=$title_value['DAYS'];
                            $pd_id=DBGet(DBQuery("SELECT SHORT_NAME FROM school_periods WHERE PERIOD_ID='".$title_value['PERIOD_ID']."'"));

                            $period_value_n[]=$pd_id[1]['SHORT_NAME'];
                        }
                        for($i=count($period_value_n);$i>=0;$i--)
                        {
                            $period_value.=$period_value_n[$i];
                            $days.=$days_n[$i];
                        }
                        
                    }
                    if($columns['SCHEDULE_TYPE']=='FIXED')
                    {
                        $col['COURSE_PERIOD_ID']=$_REQUEST['course_period_id'];
                        $values=DBGet(DBQuery("SELECT * FROM course_periods WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'"));
                        if($columns['TEACHER_ID'])
                            $col['TEACHER_ID']=$columns['TEACHER_ID'];
                        else
                            $col['TEACHER_ID']=$values[1]['TEACHER_ID'];
                        
                        if($columns['SECONDARY_TEACHER_ID'])
                            $col['SECONDARY_TEACHER_ID']=$columns['SECONDARY_TEACHER_ID'];
                        else
                            $col['SECONDARY_TEACHER_ID']=$values[1]['SECONDARY_TEACHER_ID'];

                        if($columns['BEGIN_DATE'])
                                $col['BEGIN_DATE']=$columns['BEGIN_DATE'];
                        else
                            $col['BEGIN_DATE']=$values[1]['BEGIN_DATE'];

                        if($columns['END_DATE'])
                                $col['END_DATE']=$columns['END_DATE'];
                        else
                            $col['END_DATE']=$values[1]['END_DATE'];
                        if($columns['MARKING_PERIOD_ID'])
                            $col['MARKING_PERIOD_ID']=$columns['MARKING_PERIOD_ID'];
                        else
                            $col['MARKING_PERIOD_ID']=$values[1]['MARKING_PERIOD_ID'];
                        $title_val=DBGet(DBQuery("SELECT DAYS,PERIOD_ID,ROOM_ID FROM course_period_var WHERE course_period_id='".$_REQUEST['course_period_id']."'"));
                        if($columns_var['DAYS'])
                            $col_var['DAYS']=$columns_var['DAYS'];
                        else
                            $col_var['DAYS']=$title_val[1]['DAYS'];
                        if($columns_var['PERIOD_ID'])
                            $col_var['PERIOD_ID']=$columns_var['PERIOD_ID'];
                        else
                            $col_var['PERIOD_ID']=$title_val[1]['PERIOD_ID'];
                        if($columns_var['ROOM_ID'])
                            $col_var['ROOM_ID']=$columns_var['ROOM_ID'];
                        else
                            $col_var['ROOM_ID']=$title_val[1]['ROOM_ID'];
                            $days=$title_val[1]['DAYS'];
                            $pd_id=DBGet(DBQuery("SELECT SHORT_NAME FROM school_periods WHERE PERIOD_ID='".$title_val[1]['PERIOD_ID']."'"));
                            $period_value.=$pd_id[1]['SHORT_NAME'];
                            $conflict=VerifyFixedSchedule($col,$col_var,$update=true);
                            if($conflict!==true)
                            {
                                echo '<font color=red>'.$conflict.'</font>';

                                break 2;
                            }
                    }
                    
                    if($columns['SCHEDULE_TYPE']=='BLOCKED')
                    {
                    

                        $values=DBGet(DBQuery("SELECT * FROM course_periods WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'"));
                        if(($columns['TEACHER_ID'] && $columns['TEACHER_ID']!=$values[1]['TEACHER_ID']) || ($columns['BEGIN_DATE'] && $columns['BEGIN_DATE']!=$values[1]['BEGIN_DATE']) || ($columns['END_DATE'] && $columns['END_DATE']!=$values[1]['END_DATE']) || ($columns['MARKING_PERIOD_ID'] && $columns['MARKING_PERIOD_ID']!=$values[1]['MARKING_PERIOD_ID']))
                        {
                            if($columns['TEACHER_ID'])
                                $col_bl['TEACHER_ID']=$columns['TEACHER_ID'];

                            else
                                $col_bl['TEACHER_ID']=$values[1]['TEACHER_ID'];
                            
                            if($columns['SECONDARY_TEACHER_ID'])
                            $col_bl['SECONDARY_TEACHER_ID']=$columns['SECONDARY_TEACHER_ID'];
                        else
                            $col_bl['SECONDARY_TEACHER_ID']=$values[1]['SECONDARY_TEACHER_ID'];

                            if($columns['BEGIN_DATE'])
                                    $col_bl['BEGIN_DATE']=$columns['BEGIN_DATE'];
                            else
                                $col_bl['BEGIN_DATE']=$values[1]['BEGIN_DATE'];

                            if($columns['END_DATE'])
                                    $col_bl['END_DATE']=$columns['END_DATE'];
                            else
                                $col_bl['END_DATE']=$values[1]['END_DATE'];
                            if($columns['MARKING_PERIOD_ID'])
                                $col_bl['MARKING_PERIOD_ID']=$columns['MARKING_PERIOD_ID'];
                            $conflict=VerifyBlockedSchedule($col_bl,$_REQUEST['course_period_id'],'cp',true);
                            if($conflict!==true)
                            {
                                echo '<font color=red>'.$conflict.'</font>';
                      
                                break 2;
                            }
                        }
                    }
                    $teacher = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME FROM staff WHERE STAFF_ID='$staff_id'"));

                    foreach($_REQUEST['tables']['course_period_var'][$_REQUEST['course_period_id']]['DAYS'] as $days=>$day_val)
                    {
                       $day_initial[]=$days; 
                    }

                    unset($day_initial);
                        
                    if ($short_name)
                        $mp_title .= paramlib_validation($column = SHORT_NAME, $short_name) . ' - ';
                    
                    $title = str_replace("'", "''",str_replace("\'", "'",$mp_title) . $teacher[1]['FIRST_NAME'] . ' ' . $teacher[1]['MIDDLE_NAME'] . ' ' . $teacher[1]['LAST_NAME']);
                    $sql .= "TITLE='$title',last_updated=NOW(),MODIFIED_BY=".  User('STAFF_ID').",";
                    $days='';
                    
                    
                }
               
                if (!(isset($columns['TITLE']) && trim($columns['TITLE']) == '')) {
                    foreach ($columns as $column => $value) {
                        if ($column == 'GRADE_SCALE_ID' && str_replace("\'", "''", $value) == '' ) {
                            if(!gradeAssociation($id))
                            {
                            $sql .= $column . " = NULL,";
                            $go = true; 
                            }
                            else
                            {
                                $flag_err='Y';
                                continue;
                            }
                        } 
                        elseif ($column == 'TEACHER_ID' && str_replace("\'", "''", $value) == 0) {
                            if(!scheduleAssociation($id) && !gradeAssociation($id))
                            {
                            $sql .= $column . "='" . $staff_id . "',";
                            $go = true; 
                            }
                            else
                            {
                                $flag_err='Y';
                                continue;
                            }
                    }
                        else {
                            if (stripos($_SERVER['SERVER_SOFTWARE'], 'linux')) {
                                $value = mysql_real_escape_string($value);
                                $value = str_replace('%u201D', "\"", $value);
                            }
                           
                            if (str_replace("\'", "''", $value) == '')
                            { 
                                    if(($table_name=='course_periods' || $table_name=='course_period_var') && ($column=='COURSE_WEIGHT' || $column=='GRADE_SCALE_ID' || $column=='CREDITS' || $column=='DOES_BREAKOFF' || $column=='DOES_HONOR_ROLL' || $column=='HALF_DAY' || $column=='DOES_CLASS_RANK' || $column=='SECONDARY_TEACHER_ID') && !gradeAssociation($id))
                                    {
                                        $sql .= $column . " = NULL,";
                                        $go=true;
                                    }
                                    if(gradeAssociation($id) && ($column == 'TEACHER_ID' || $column=='SECONDARY_TEACHER_ID' || $column == 'GRADE_SCALE_ID' || $column=='CREDITS'|| $column=='DOES_BREAKOFF' || $column=='DOES_HONOR_ROLL' || $column=='HALF_DAY' || $column=='DOES_CLASS_RANK'))
                                    {
                                        $flag_err='Y';
                                    }
                                    if($column=='DOES_ATTENDANCE' && !(scheduleAssociation($id)))
                                    {
                                         $sql .= $column . " = NULL,";
                                        $go=true;
                                    }
                                    if($column=='DOES_ATTENDANCE' && scheduleAssociation($id))
                                    {
                                         $assoc_err="<font color=red><b>Cannot Modify this course period as it has association. </b></font>";
                                    }
                                    if(($column=='GENDER_RESTRICTION' || $column=='PARENT_ID') && !scheduleAssociation($id))
                                    {
                                        if($column=='PARENT_ID')
                                         $sql .= $column . " = ".$id.",";   
                                        else
                                            $sql .= $column . " = NULL,";
                                        $go=true;
                                    }
                                    if($table_name=='courses' || $table_name=='course_subjects')
                                    {
                                        $sql .= $column ."=NULL,";
                                        $go=true;
                                    }
                                    if(!scheduleAssociation($id) && ($column=='MARKING_PERIOD_ID' || $column=='MP'))
                                    {
                                        $sql .= $column . "='".str_replace("'","''",$value) ."',";
                                         $go=true;
                                    }
                            }
                            else
                            {    
                                 
                                if($column=='SHORT_NAME' ||  $column=='TOTAL_SEATS' || $column=='DOES_ATTENDANCE')
                                {
                                    if($column=='DOES_ATTENDANCE')
                                    {
                                        if(scheduleAssociation($id))
                                        {
                                            $check_attn=  DBGet(DBQuery('SELECT * FROM school_periods WHERE PERIOD_ID=(SELECT PERIOD_ID FROM course_period_var WHERE COURSE_PERIOD_ID='.$id.')'));
                                            $check_attn=$check_attn[1];
                                            if($check_attn['ATTENDANCE']=='Y')
                                            {
                                                $sql .= $column . "='".str_replace("'", "''",str_replace("\'", "'",$value)) ."',";
                                                $go=true;
                                            }
                                        }
                                        else
                                        {
                                            if($columns['PERIOD_ID']!='')
                                            {
                                                $check_attn=  DBGet(DBQuery('SELECT * FROM school_periods WHERE PERIOD_ID='.$columns['PERIOD_ID']));
                                                $check_attn=$check_attn[1];

                                            }
                                            else
                                            {
                                                $check_attn=  DBGet(DBQuery('SELECT * FROM school_periods WHERE PERIOD_ID=(SELECT PERIOD_ID FROM course_period_var WHERE COURSE_PERIOD_ID='.$id.')'));
                                                $check_attn=$check_attn[1];
                                            }
                                            if($check_attn['ATTENDANCE']=='Y')
                                            {
                                                $sql .= $column . "='".str_replace("'", "''",str_replace("\'", "'",$value)) ."',";
                                                $go=true;
                                            }
                                        }
                                    }
                                    else
                                    {
                                $sql .= $column."='".str_replace("'", "''",str_replace("\'", "'",$value))."',";
                                $go=true;
                                    }
                                }
                                else
                                {
                                    if(!gradeAssociation($id) && ($column=='COURSE_WEIGHT' || $column=='GRADE_SCALE_ID' || $column=='SECONDARY_TEACHER_ID' || $column == 'TEACHER_ID' || $column=='CREDITS' || $column=='DOES_BREAKOFF' || $column=='DOES_HONOR_ROLL' || $column=='HALF_DAY' || $column=='DOES_CLASS_RANK'))
                                    {
                                       $sql .= $column. "='".str_replace("'","''",$value)."',";
                                       $go=true;
                                    }
                                    else {
                                        if(gradeAssociation($id) && ($column == 'TEACHER_ID' || $column=='SECONDARY_TEACHER_ID' || $column == 'GRADE_SCALE_ID' || $column=='CREDITS'|| $column=='DOES_BREAKOFF' || $column=='DOES_HONOR_ROLL' || $column=='HALF_DAY' || $column=='DOES_CLASS_RANK'))
                                        {
                                            $flag_err='Y';
                                        }
                                        if(scheduleAssociation($id) && ($column=='ROOM_ID' || $column=='DAYS' || $column=='PERIOD_ID' || $column=='MARKING_PERIOD_ID'))
                                        {
                                            $flag_err='Y';
                                        }
                                        if($table_name=='courses' || $table_name=='course_subjects')
                                        {
                                             $sql .= $column . "='".str_replace("'","''",$value)."',";
                                             $go=true;
                                        }
                                        if(!scheduleAssociation($id) && ($column=='MARKING_PERIOD_ID' || $column=='MP'  || $column=='GENDER_RESTRICTION' || $column=='PARENT_ID'))
                                        {
                                            $sql .= $column . "='".str_replace("'","''",$value) ."',";
                                             $go=true;
                                        }
                                        if(!scheduleAssociation($id) && ($column=='BEGIN_DATE' || $column=='END_DATE'))
                                        {
                                            $sql .= $column . "='".str_replace("'","''",date('Y-m-d',strtotime($value))) ."',";
                                             $go=true;
                                        }
                                        if (!scheduleAssociation($id) && $table_name=='course_period_var' && $_REQUEST['tables']['course_periods'][$id]['SCHEDULE_TYPE']=='FIXED') {
                                           
                                            if($column=='PERIOD_ID')
                                            {
                                                $period_time_edt =  DBGet(DBQuery("SELECT START_TIME, END_TIME FROM school_periods WHERE period_id={$value}"));
                                                $period_time_edt=$period_time_edt[1];
                                                $start_time_edt=$period_time_edt['START_TIME'];
                                                $end_time_edt=$period_time_edt['END_TIME'];
                                                $check_attn=  DBGet(DBQuery('SELECT * FROM school_periods WHERE PERIOD_ID='.$value));
                                                $check_attn=$check_attn[1];
                                                if($check_attn['ATTENDANCE']=='Y')
                                                    $sql .=" PERIOD_ID=".$value." , START_TIME='".$start_time_edt."' , END_TIME='".$end_time_edt."',";
                                                else
                                                    $sql .=" PERIOD_ID=".$value." , START_TIME='".$start_time_edt."' , END_TIME='".$end_time_edt."',DOES_ATTENDANCE=NULL,";
                                                $go=true;
                                            }
                                            if($column=='ROOM_ID' || $column=='DAYS' || $column=='DOES_ATTENDANCE')
                                            {
                                                if($column=='DOES_ATTENDANCE')
                                                {
                                                    if($columns['PERIOD_ID']!='')
                                                    {
                                                        $check_attn=  DBGet(DBQuery('SELECT * FROM school_periods WHERE PERIOD_ID='.$columns['PERIOD_ID']));
                                                        $check_attn=$check_attn[1];

                                                    }
                                                    else
                                                    {
                                                        $check_attn=  DBGet(DBQuery('SELECT * FROM school_periods WHERE PERIOD_ID=(SELECT PERIOD_ID FROM course_period_var WHERE COURSE_PERIOD_ID='.$id.')'));
                                                        $check_attn=$check_attn[1];
                                                    }
                                                    if($check_attn['ATTENDANCE']=='Y')
                                                    {
                                                        $sql .= $column . "='".str_replace("'","''",$value) ."',";
                                                        $go=true;
                                                    }
                                                }
                                                else
                                                {
                                                    $sql .= $column . "='".str_replace("'","''",$value) ."',";
                                                    $go=true;
                                                }
                                            }
                                        
                                    }
                                        
                                    }
                                }
                            }
                        }
                    }
                $sql = substr($sql, 0, -1) . " WHERE " . $where[$table_name] . "='$id'";
                    if ($go  && $not_pass_update==false)
                    {
                        DBQuery($sql);

                    }
                    else
                        {
                        $cp_gdr_teach_id=  DBGet(DBQuery('SELECT * FROM course_periods WHERE course_period_id='.$id));

                        if(((isset($columns['GRADE_SCALE_ID'])&&$columns['GRADE_SCALE_ID']!=$cp_gdr_teach_id[1]['GRADE_SCALE_ID']) || (isset($columns['TEACHER_ID'])&&$columns['TEACHER_ID']!=$cp_gdr_teach_id[1]['TEACHER_ID'])) && $not_pass_update==false)
                                $assoc_err="<font color=red><b>Cannot Modify this course period as it has association. </b></font>";
                        }
                        if($flag_err=='Y')
                        {
                            $cp_gdr_teach_id=  DBGet(DBQuery('SELECT * FROM course_periods WHERE course_period_id='.$id));

                        if(((isset($columns['GRADE_SCALE_ID'])&&$columns['GRADE_SCALE_ID']!=$cp_gdr_teach_id[1]['GRADE_SCALE_ID']) || (isset($columns['TEACHER_ID'])&&$columns['TEACHER_ID']!=$cp_gdr_teach_id[1]['TEACHER_ID'])) && $not_pass_update==false)
                                $assoc_err="<font color=red><b>Cannot Modify this course period as it has association. </b></font>";
                            
                        }
                        if($flag_err=='Y')
                        {
                            $cp_gdr_teach_id=  DBGet(DBQuery('SELECT * FROM course_periods cp, course_period_var cpv WHERE cp.course_period_id='.$id.' AND cp.course_period_id=cpv.course_period_id' ));
                            if(((isset($columns['ROOM_ID'])&&$columns['ROOM_ID']!=$cp_gdr_teach_id[1]['ROOM_ID']) || (isset($columns['PERIOD_ID'])&&$columns['PERIOD_ID']!=$cp_gdr_teach_id[1]['PERIOD_ID']) || (isset($columns['DAYS'])&&$columns['DAYS']!=$cp_gdr_teach_id[1]['DAYS']) || (isset($columns['MARKING_PERIOD_ID'])&&$columns['MARKING_PERIOD_ID']!=$cp_gdr_teach_id[1]['MARKING_PERIOD_ID'])))
                                $assoc_err="<font color=red><b>Cannot Modify this course period as it has association. </b></font>";
                        }
                        if($flag_err=='Y')
                        {
                            if((isset($columns['DOES_BREAKOFF'])  || isset($columns['DOES_HONOR_ROLL']) || isset($columns['HALF_DAY']) || isset($columns['DOES_CLASS_RANK'])) && $not_pass_update==false)
                            {
                                $assoc_err="<font color=red><b>Cannot Modify this course period as it has association. </b></font>";
                            }
                        }
                }
                
                        
                if($columns['SCHEDULE_TYPE']=='VARIABLE')
                {

                    foreach($_REQUEST['course_period_variable'] as $cp_id=>$days)
                        {

                        $schedule_check=DBGet(DBQuery("SELECT COUNT(*) AS TOTAL FROM schedule WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'"));
                            foreach($days as $day=>$period)
                            {

                                if($period['ID']!='')
                                {   
                                     $up_cpv_sql="UPDATE course_period_var set";
                                     if(!isset($period['DOES_ATTENDANCE']))
                                     {
                                         $period['DOES_ATTENDANCE']=NULL;
                                     }
                                    foreach($period as $col=>$data)
                                    {
                                        if($schedule_check[1]['TOTAL']==0)
                                        {
                                            if($col!='ID')
                                            {
                                            $up_cpv_sql.=" $col='".$data."',";
                                            }
                                            if($col=='ROOM_ID')
                                            {
                                            if($conflict!==true)
                                            {
//                                      
                                            $not_pass_update=true;
                                            break 2;
                                            }
                                            
                                            }
                                        }
                                        else
                                        {
                                            if($col=='DOES_ATTENDANCE')
                                            {
                                            $up_cpv_sql.=" $col='".$data."',";
                                            $does_att=1;
                                            }
                                            if($col=='PERIOD_ID' && $data!='' && $not_pass_update==false)
                                            {
                                                $error="<font color=red><b>Cannot Modify this course period as it has association. </b></font>";
                                            }
                                            if($col=='DAYS' && $data!='' && $not_pass_update==false)
                                            {
                                                $schedule_days_check=DBGet(DBQuery("SELECT DAYS FROM course_period_var WHERE ID='".$period['ID']."'"));
                                                if($schedule_days_check[1]['DAYS']!=$data)
                                                {
                                                   $error="<font color=red><b>Cannot Modify this course period as it has association. </b></font>";  
                                                }
                                                
                                                
                                            }
                                            if($col=='ROOM_ID' && $data!='' && $not_pass_update==false)
                                            {
                                            
                                                $schedule_days_check=DBGet(DBQuery("SELECT ROOM_ID FROM course_period_var WHERE ID='".$period['ID']."'"));
                                                if($schedule_days_check[1]['ROOM_ID']!=$data)
                                                {
                                                    $error="<font color=red><b>Cannot Modify this course period as it has association. </b></font>";  
                                                }
                                            }
                                        }
                                    }
                                    $up_cpv_sql=substr($up_cpv_sql,0,-1);
                                    $up_cpv_sql.=" WHERE ID='".$period['ID']."'";
                                    if($not_pass_update==false)
                                   DBQuery($up_cpv_sql);
                                   
                                }
                            }
                        }
                        if($error)
                        {
                            echo $error;
                        }
                }
                if (($scheduleAssociation || $gradeAssociation) && is_array($asso_err)) {
                    foreach ($asso_err as $err) {
                        ShowErrPhp($err);
                    }
                }
              }
              else 
                {
                    echo "<font color=red><b>Cannot Modify seats as it has association. </b></font>";
                }
            } else {
                $update=false;
                $sql = "INSERT INTO $table_name ";
                if ($table_name == 'course_subjects') {
                    $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'course_subjects'"));
                    $id[1]['ID'] = $id[1]['AUTO_INCREMENT'];
                    $fields = 'SCHOOL_ID,SYEAR,';
                    $values = "'" . UserSchool() . "','" . UserSyear() . "',";
                    $_REQUEST['subject_id'] = $id[1]['ID'];
                } elseif ($table_name == 'courses') {
                    $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'courses'"));
                    $id[1]['ID'] = $id[1]['AUTO_INCREMENT'];
                    $_REQUEST['course_id'] = $id[1]['ID'];
                    $fields = 'SUBJECT_ID,SCHOOL_ID,SYEAR,';
                    $values = "'$_REQUEST[subject_id]','" . UserSchool() . "','" . UserSyear() . "',";
                } elseif ($table_name == 'course_periods') 
                {
                        if($columns['SCHEDULE_TYPE']=='FIXED')
                        {
                            
                            $pd_id=DBGet(DBQuery("SELECT * FROM school_periods WHERE PERIOD_ID='".$_REQUEST['tables']['course_period_var']['new']['PERIOD_ID']."'"));
                            $period_value.=$pd_id[1]['SHORT_NAME'];
                            $conflict=VerifyFixedSchedule($columns,$columns_var);
                            if($conflict!==true)
                            {
                                echo '<font color=red>'.$conflict.'</font>';
                                $not_pass=true;
                                $_SESSION['conflict']=$conflict;

                                  echo "<script>window.location.href='Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=new'</script>";
                                break 2;
                            }
                        }
                        elseif($columns['SCHEDULE_TYPE']=='VARIABLE')
                        {
                            $pd_id=DBGet(DBQuery("SELECT SHORT_NAME FROM school_periods WHERE PERIOD_ID='".$_REQUEST['course_period_variable']['new']['PERIOD_ID']."'"));
                            $period_value.=$pd_id[1]['SHORT_NAME'];
                            $conflict=VerifyVariableSchedule($columns);
                            if($conflict!==true)
                            {
                                echo '<font color=red>'.$conflict.'</font>';
                                $not_pass=true;
                                $_SESSION['conflict']=$conflict;
                                echo "<script>window.location.href='Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=new'</script>";
                                break 2;
                            }
                        }
                        $id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'course_periods'"));
                        $id[1]['ID'] = $id[1]['AUTO_INCREMENT'];
                        $fields = 'SYEAR,SCHOOL_ID,COURSE_ID,TITLE,MODIFIED_BY,';
                        $teacher = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME FROM staff WHERE STAFF_ID='$columns[TEACHER_ID]'"));

                        if (!isset($columns['PARENT_ID']))
                            $columns['PARENT_ID'] = $id[1]['ID'];

                        if (isset($columns['MARKING_PERIOD_ID'])) {
                            if (GetMP($columns['MARKING_PERIOD_ID'], 'TABLE') == 'school_years')
                                $columns['MP'] = 'FY';
                            elseif (GetMP($columns['MARKING_PERIOD_ID'], 'TABLE') == 'school_semesters')
                                $columns['MP'] = 'SEM';
                            else
                                $columns['MP'] = 'QTR';

                            if (GetMP($columns['MARKING_PERIOD_ID'], 'TABLE') != 'school_years')
                            {
                                
                               $mp_title = GetMP($columns['MARKING_PERIOD_ID'], 'SHORT_NAME') . ' - ';
                            }
                             
                        }
                        if (isset($columns['MARKING_PERIOD_ID'])=='')
                            $mp_title =  'Custom - ';
                        
                        if($columns['SCHEDULE_TYPE']=='FIXED')
                        {
                            foreach($_REQUEST['tables']['course_period_var']['new']['DAYS'] as $key=>$value)
                                $days.=$key;

                        }

                        if ($columns['SHORT_NAME'])
                            
                            $mp_title .= paramlib_validation($column = SHORT_NAME, $columns['SHORT_NAME']) . ' - ';
                        $title = str_replace("'", "''",str_replace("\'", "'",$mp_title) . $teacher[1]['FIRST_NAME'] . ' ' . $teacher[1]['MIDDLE_NAME'] . ' ' . $teacher[1]['LAST_NAME']);
                        $values = "'" . UserSyear() . "','" . UserSchool() . "','$_REQUEST[course_id]','$title',".  User('STAFF_ID').",";
                        $_REQUEST['course_period_id'] = $id[1]['ID'];
                }
                elseif($table_name=='course_period_var')
                {
                    if( $columns['START_TIME']=='' && $columns['END_TIME']=='')
                    {
                        $pd_id=DBGet(DBQuery("SELECT * FROM school_periods WHERE PERIOD_ID='".$_REQUEST['tables']['course_period_var']['new']['PERIOD_ID']."'"));

                        $columns['START_TIME']=$pd_id[1]['START_TIME'];
                        $columns['END_TIME']=$pd_id[1]['END_TIME'];
                    }
                   $fields ='COURSE_PERIOD_ID,';
                   $values = "$_REQUEST[course_period_id],";
                }
                
                $go = 0;
                foreach ($columns as $column => $value) {
                    if ($value != '') {
                        $value = trim(paramlib_validation($column, $value));
                        $fields .= $column . ',';
                        if (stripos($_SERVER['SERVER_SOFTWARE'], 'linux')) {
                            $value = mysql_real_escape_string($value);
                        }
                        $values .= '"' . $value . '",';

                        $go = true;
                    }
                }
              $sql .= '(' . substr($fields, 0, -1) . ') values(' . substr($values, 0, -1) . ')';
                if ($go) {
                    DBQuery($sql);
                }
                
                // ----------------------------------------------- //
                if ($_REQUEST['w_course_period_id']) {
                    $max_id = DBGet(DBQuery("SELECT MAX(COURSE_PERIOD_ID) AS CP_ID FROM course_periods;"));
                    $sql_2 = "UPDATE course_periods SET PARENT_ID=" . $_REQUEST['w_course_period_id'] . " WHERE COURSE_PERIOD_ID = " . $max_id[1]['CP_ID'];
                    DBQuery($sql_2);
                }
                // ----------------------------------------------- //
            }
        }
        }
    if($assoc_err)
    {
        echo $assoc_err;
            unset($assoc_err);
    }
    if($_REQUEST['course_period_variable'] && $not_pass!=true && $update==false)
    {
        $days_sql="INSERT INTO course_period_var(course_period_id,days,period_id,start_time,end_time,room_id,does_attendance)VALUES";
        foreach($_REQUEST['course_period_variable'] as $cp_id=>$days)
        {

            $days_sql .="($_REQUEST[course_period_id],'$days[DAYS]',$days[PERIOD_ID],'".$days['n']['START_TIME']."','".$days['n']['END_TIME']."',$days[ROOM_ID],'$days[DOES_ATTENDANCE]'),";

            }
        $days_sql =  substr($days_sql,0,-1);
        DBQuery($days_sql);
    }
    if($not_pass==false)
        unset($_REQUEST['tables']);
}

if (clean_param($_REQUEST['modfunc'], PARAM_ALPHAMOD) == 'delete' && AllowEdit()) {
    unset($sql);
    $course_period_id = paramlib_validation($colmn = PERIOD_ID, $_REQUEST[course_period_id]);
    $course_id = paramlib_validation($colmn = PERIOD_ID, $_REQUEST[course_id]);
    $subject_id = paramlib_validation($colmn = PERIOD_ID, $_REQUEST[subject_id]);

if (clean_param($_REQUEST['course_period_id'], PARAM_ALPHANUM)) {
        $table = 'course period';
        $sql[] = "UPDATE course_periods SET PARENT_ID=NULL WHERE PARENT_ID='$course_period_id'";
        $sql[] = "DELETE FROM course_periods WHERE COURSE_PERIOD_ID='$course_period_id'";
        $sql[] = "DELETE FROM schedule WHERE COURSE_PERIOD_ID='$course_period_id'";
    } elseif (clean_param($_REQUEST['course_id'], PARAM_ALPHANUM)) {
        $table = 'course';
        $course_period = DBGet(DBQuery("SELECT COURSE_PERIOD_ID FROM course_periods WHERE COURSE_ID='$course_id'"));
        foreach ($course_period as $course1)
            if ($course1['COURSE_PERIOD_ID'] == '') {
                $sql[] = "DELETE FROM courses WHERE COURSE_ID='$course_id'";
                $extra_sql = "SELECT COURSE_PERIOD_ID FROM course_periods WHERE COURSE_ID='$course_id'";
                $result_sql = DBGet(DBQuery($extra_sql));
                $sql[] = "UPDATE course_periods SET PARENT_ID=NULL WHERE PARENT_ID = '" . $result_sql . "'";
                $sql[] = "DELETE FROM course_periods WHERE COURSE_ID='$course_id'";
                $sql[] = "DELETE FROM schedule WHERE COURSE_ID='$course_id'";
                $sql[] = "DELETE FROM schedule_requests WHERE COURSE_ID='$course_id'";

                if (DeletePromptCommon($table)) {
                    if (BlockDelete($table)) {
                        DBQuery($sql);
                        unset($_REQUEST['modfunc']);
                    }
                }
            }



        if ($course1['COURSE_PERIOD_ID'] != '') {
            PopTable('header', 'Unable to Delete');
            DrawHeaderHome('<font color=red>Course cannot be deleted.</font>');
            echo '<div align=right><a href=Modules.php?modname=schoolsetup/Courses.php&subject_id=' . $subject_id . '&course_id=' . $course_id . ' style="text-decoration:none"><b>back to Course</b></a></div>';
            PopTable('footer');
        } else {
            if (DeletePromptCommon($table)) {
                if (BlockDelete($table)) {
                    $sql[] = "DELETE FROM courses WHERE COURSE_ID='$course_id'";
                    $extra_sql = "SELECT COURSE_PERIOD_ID FROM course_periods WHERE COURSE_ID='$course_id'";
                    $result_sql = DBGet(DBQuery($extra_sql));
                    $sql[] = "UPDATE course_periods SET PARENT_ID=NULL WHERE PARENT_ID = '" . $result_sql . "'";
                    $sql[] = "DELETE FROM course_periods WHERE COURSE_ID='$course_id'";
                    $sql[] = "DELETE FROM schedule WHERE COURSE_ID='$course_id'";
                    $sql[] = "DELETE FROM schedule_requests WHERE COURSE_ID='$course_id'";
                    foreach ($sql as $query)
                        DBQuery($query);
                    unset($_REQUEST['modfunc']);
                    unset($_REQUEST[course_id]);
                }
            }
        }
    } elseif (clean_param($_REQUEST['subject_id'], PARAM_ALPHANUM)) {
        $table = 'subject';
        $subject = DBGet(DBQuery("SELECT COURSE_ID FROM courses WHERE SUBJECT_ID='$subject_id'"));
        foreach ($subject as $subject1)
            if ($subject1['COURSE_ID'] == '') {
                $sql[] = "DELETE FROM course_subjects WHERE SUBJECT_ID='$subject_id'";
                $courses = DBGet(DBQuery("SELECT COURSE_ID FROM courses WHERE SUBJECT_ID='$subject_id'"));
                if (count($courses)) {
                    foreach ($courses as $course) {
                        $sql[] = "DELETE FROM courses WHERE COURSE_ID='$course[COURSE_ID]'";


                        $extra_sql2 = "SELECT COURSE_PERIOD_ID FROM course_periods WHERE COURSE_ID='$course[COURSE_ID]'";
                        $result_sql2 = DBGet(DBQuery($extra_sql2));
                        $sql[] = "UPDATE course_periods SET PARENT_ID=NULL WHERE PARENT_ID = '" . $result_sql2 . "'";

                        $sql[] = "DELETE FROM course_periods WHERE COURSE_ID='$course[COURSE_ID]'";
                        $sql[] = "DELETE FROM schedule WHERE COURSE_ID='$course[COURSE_ID]'";
                        $sql[] = "DELETE FROM schedule_requests WHERE COURSE_ID='$course[COURSE_ID]'";
                    }
                }
            }
        if ($subject1['COURSE_ID'] != '') {
            PopTable('header', 'Unable to Delete');
            DrawHeaderHome('<font color=red>Subject cannot be deleted.</font>');
            echo '<div align=right><a href=Modules.php?modname=schoolsetup/Courses.php&subject_id=' . $subject_id . ' style="text-decoration:none"><b>back to Subject</b></a></div>';
            PopTable('footer');
        } else {
            if (DeletePromptCommon($table)) {
                if (BlockDelete($table)) {
                    $sql[] = "DELETE FROM course_subjects WHERE SUBJECT_ID='$subject_id'";
                    $courses = DBGet(DBQuery("SELECT COURSE_ID FROM courses WHERE SUBJECT_ID='$subject_id'"));
                    if (count($courses)) {
                        foreach ($courses as $course) {
                            $sql[] = "DELETE FROM courses WHERE COURSE_ID='$course[COURSE_ID]'";
                            $extra_sql2 = "SELECT COURSE_PERIOD_ID FROM course_periods WHERE COURSE_ID='$course[COURSE_ID]'";
                            $result_sql2 = DBGet(DBQuery($extra_sql2));
                            $sql[] = "UPDATE course_periods SET PARENT_ID=NULL WHERE PARENT_ID = '" . $result_sql2 . "'";
                            $sql[] = "DELETE FROM course_periods WHERE COURSE_ID='$course[COURSE_ID]'";
                            $sql[] = "DELETE FROM schedule WHERE COURSE_ID='$course[COURSE_ID]'";
                            $sql[] = "DELETE FROM schedule_requests WHERE COURSE_ID='$course[COURSE_ID]'";
                        }
                    }
                    foreach ($sql as $query)
                        DBQuery($query);
                    unset($_REQUEST['modfunc']);
                    unset($_REQUEST['subject_id']);
                }
            }
        }
    }

    if ($_REQUEST['course_period_id']) {
        if (DeletePromptCommon($table)) { 
            if (BlockDelete($table)) {
                foreach ($sql as $query)
                    DBQuery($query);
                unset($_REQUEST['modfunc']);
                unset($_REQUEST['course_period_id']);
            }
        }
    }
}



if($_REQUEST['modfunc']=='detail')
{
        if($_POST['button']=='Save' && $_REQUEST['mode']=='add')
            $conflict=VerifyBlockedSchedule($_REQUEST['values'],$_REQUEST['course_period_id'],'cpv');
        if($_POST['button']=='Save' && $_REQUEST['mode']=='edit')
        {
            
            $title_val=DBGet(DBQuery("SELECT PERIOD_ID,ROOM_ID FROM course_period_var WHERE course_period_id='".$_REQUEST['course_period_id']."'"));
            
                        if($_REQUEST['values']['PERIOD_ID'])
                            $col_blk['PERIOD_ID']=$_REQUEST['values']['PERIOD_ID'];
                        else
                            $col_blk['PERIOD_ID']=$title_val[1]['PERIOD_ID'];
                        if($_REQUEST['values']['ROOM_ID'])
                            $col_blk['ROOM_ID']=$_REQUEST['values']['ROOM_ID'];
                        else
                            $col_blk['ROOM_ID']=$title_val[1]['ROOM_ID'];
            $conflict=VerifyBlockedSchedule($col_blk,$_REQUEST['course_period_id'],'cpv',true);
        }

        if($_POST['button']=='Save' && $conflict===true)
        {
            if($_REQUEST['mode']=='add')
            {
                $chek_assoc=  DBGet(DBQuery('SELECT COUNT(*) as REC_EX FROM schedule WHERE COURSE_PERIOD_ID='.$_REQUEST['course_period_id'].' AND (START_DATE<=\''.date('Y-m-d').'\' AND (END_DATE IS NULL OR END_DATE=\'0000-00-00\' OR END_DATE>=\''.date('Y-m-d').'\' ))'));
                    if($chek_assoc[1]['REC_EX']==0)
                    {
                        $sql = "INSERT INTO course_period_var ";
                        $fields = 'COURSE_PERIOD_ID,DAYS,COURSE_PERIOD_DATE,';
                        $values = "'".$_REQUEST['course_period_id']."','".  conv_day(date('D',strtotime($_REQUEST['meet_date'])),'key')."','".$_REQUEST['meet_date']."',";
                        $go=false;
                        foreach($_REQUEST['values'] as $column=>$value)
                        {
                                if(trim($value))
                                {
                                    $value=paramlib_validation($column,$value);
                                    $fields .= $column.',';
                                    $values .= '"'.str_replace("","",trim($value)).'",';
                                    $go = true;
                                }
                        }
                        $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';  
                    }
                    else
                    {
                        $error='Blocked_assoc';
                    }
            }
            elseif($_REQUEST['mode']=='edit')
            {
                $chek_assoc=  DBGet(DBQuery('SELECT COUNT(*) as REC_EX FROM schedule WHERE COURSE_PERIOD_ID='.$_REQUEST['course_period_id'].' AND (START_DATE<=\''.date('Y-m-d').'\' AND (END_DATE IS NULL OR END_DATE=\'0000-00-00\' OR END_DATE>=\''.date('Y-m-d').'\' ))'));
                    if($chek_assoc[1]['REC_EX']==0)
                    {
                        if($_REQUEST['values']['PERIOD_ID']!='' && $_REQUEST['values']['DOES_ATTENDANCE']=='')
                             {
                                 $check_sp=  DBGet(DBQuery('SELECT * FROM school_periods WHERE PERIOD_ID='.$_REQUEST['values']['PERIOD_ID']));
                                 $check_sp=$check_sp[1];
                                 $cp_per_id=DBGet(DBQuery('SELECT * FROM course_period_var WHERE ID='.$_REQUEST['cpv_id']));
                                 $cp_per_id=$cp_per_id[1];
                                 if($check_sp['PERIOD_ID']!=$cp_per_id['PERIOD_ID'])
                                 {
                                    if($check_sp['ATTENDANCE']=='' && $cp_per_id['DOES_ATTENDANCE']=='Y')
                                    {
                                        $_REQUEST['values']['DOES_ATTENDANCE']='';
                                    }
                                 }
                             }
                             elseif($_REQUEST['values']['PERIOD_ID']=='' && $_REQUEST['values']['DOES_ATTENDANCE']!='')
                             {
                                
                                 $cp_per_id=DBGet(DBQuery('SELECT * FROM course_period_var WHERE ID='.$_REQUEST['cpv_id']));
                                 $cp_per_id=$cp_per_id[1];
                                  $check_sp=  DBGet(DBQuery('SELECT * FROM school_periods WHERE PERIOD_ID='.$cp_per_id['PERIOD_ID']));
                                 $check_sp=$check_sp[1];
                                    if($check_sp['ATTENDANCE']=='')
                                    {
                                        $_REQUEST['values']['DOES_ATTENDANCE']='';
                                    }
                             }
                             if($_REQUEST['values']['PERIOD_ID']!='' && $_REQUEST['values']['DOES_ATTENDANCE']!='')
                             {
                                 $check_sp=  DBGet(DBQuery('SELECT * FROM school_periods WHERE PERIOD_ID='.$_REQUEST['values']['PERIOD_ID']));
                                 $check_sp=$check_sp[1];
                                    if($check_sp['ATTENDANCE']=='')
                                    {
                                        $_REQUEST['values']['DOES_ATTENDANCE']='';
                                    }
                             }
                        $sql = "UPDATE course_period_var SET ";
                        $go=false;
                        foreach($_REQUEST['values'] as $column=>$value){
                            $value=paramlib_validation($column,$value);
                            if($column=='DOES_ATTENDANCE' && $value=='')
                                $sql .= $column.'=null,';
                            else
                                $sql .= $column.'="'.str_replace("","",trim($value)).'",';
                            $go=true;
                        }
                        $sql = substr($sql,0,-1);
                        $sql.=" WHERE COURSE_PERIOD_ID={$_REQUEST['course_period_id']} AND course_period_date='".$_REQUEST['meet_date']."' AND ID='".$_REQUEST['cpv_id']."'";
                        $sql;
                    }
                    else
                    {
                        $check_take_attn=  DBGet(DBQuery('SELECT COUNT(*) AS TOTAL FROM attendance_period WHERE COURSE_PERIOD_ID='.$_REQUEST['course_period_id'].' AND school_date=\''.$_REQUEST['meet_date'].'\''));
                        $check_miss_attn=  DBGet(DBQuery('SELECT COUNT(*) AS TOTAL FROM missing_attendance WHERE COURSE_PERIOD_ID='.$_REQUEST['course_period_id'].' AND school_date=\''.$_REQUEST['meet_date'].'\''));
                        if($check_take_attn[1]['TOTAL']>0 || $check_miss_attn[1]['TOTAL']>0)
                            $error='Blocked_assoc';
                         else
                         {
                             if($_REQUEST['values']['PERIOD_ID']!='' && $_REQUEST['values']['DOES_ATTENDANCE']=='')
                             {
                                 $check_sp=  DBGet(DBQuery('SELECT * FROM school_periods WHERE PERIOD_ID='.$_REQUEST['values']['PERIOD_ID']));
                                 $check_sp=$check_sp[1];
                                 $cp_per_id=DBGet(DBQuery('SELECT * FROM course_period_var WHERE ID='.$_REQUEST['cpv_id']));
                                 $cp_per_id=$cp_per_id[1];
                                 if($check_sp['PERIOD_ID']!=$cp_per_id['PERIOD_ID'])
                                 {
                                    if($check_sp['ATTENDANCE']=='' && $cp_per_id['DOES_ATTENDANCE']=='Y' && isset($_REQUEST['values']['DOES_ATTENDANCE']))
                                    {
                                        $_REQUEST['values']['DOES_ATTENDANCE']='';
                                    }
                                 }
                             }
                             elseif($_REQUEST['values']['PERIOD_ID']=='' && $_REQUEST['values']['DOES_ATTENDANCE']!='')
                             {
                                
                                 $cp_per_id=DBGet(DBQuery('SELECT * FROM course_period_var WHERE ID='.$_REQUEST['cpv_id']));
                                 $cp_per_id=$cp_per_id[1];
                                  $check_sp=  DBGet(DBQuery('SELECT * FROM school_periods WHERE PERIOD_ID='.$cp_per_id['PERIOD_ID']));
                                 $check_sp=$check_sp[1];
                                    if($check_sp['ATTENDANCE']=='')
                                    {
                                        $_REQUEST['values']['DOES_ATTENDANCE']='';
                                    }
                             }
                             if($_REQUEST['values']['PERIOD_ID']!='' && $_REQUEST['values']['DOES_ATTENDANCE']!='')
                             {
                                 $cp_per_id=DBGet(DBQuery('SELECT * FROM course_period_var WHERE ID='.$_REQUEST['cpv_id']));
                                 $cp_per_id=$cp_per_id[1];
                                 $check_sp=  DBGet(DBQuery('SELECT * FROM school_periods WHERE PERIOD_ID='.$cp_per_id['PERIOD_ID']));
                                 $check_sp=$check_sp[1];
                                    if($check_sp['ATTENDANCE']=='')
                                    {
                                        $_REQUEST['values']['DOES_ATTENDANCE']='';
                                    }
                             }
                             $sql = "UPDATE course_period_var SET ";
                             $go=false;
                    foreach($_REQUEST['values'] as $column=>$value){
                        $value=paramlib_validation($column,$value);
                        if($column=='DOES_ATTENDANCE' && $value=='')
                        {
                            $sql .= $column.'=NULL,';
                            $go=true;
                        }
                        elseif($column=='DOES_ATTENDANCE' && $value!='') 
                        {
                            $sql .= $column.'="'.str_replace("","",trim($value)).'",';
                            $go=true;
                        }
                        elseif(($column=='PERIOD_ID' && $value!='')|| ($column=='ROOM_ID' && $value!=''))
                            $error='Blocked_period_room';
                        
                    }
                    $sql = substr($sql,0,-1);
                    $sql.=" WHERE COURSE_PERIOD_ID={$_REQUEST['course_period_id']} AND course_period_date='".$_REQUEST['meet_date']."' AND ID='".$_REQUEST['cpv_id']."'";
                    $sql;
                         }
                    }
            }
            if($go)
                    DBQuery ($sql);
            DBQuery("UPDATE course_period_var cpv,school_periods sp SET cpv.start_time=sp.start_time,cpv.end_time=sp.end_time WHERE sp.period_id=cpv.period_id AND course_period_id=$_REQUEST[course_period_id]");
            unset($_REQUEST['values']);
            unset($_SESSION['_REQUEST_vars']['values']);
            if($error=='Blocked_assoc')
            echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&error=Blocked_assoc&subject_id='.$_REQUEST[subject_id].'&course_id='.$_REQUEST[course_id].'&course_period_id='.$_REQUEST[course_period_id].'&month='.date(strtotime($_REQUEST['meet_date'])).'"; window.close();</script>'; 
            
            elseif ($error=='Blocked_period_room') {
             echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&error=Blocked_period_room&subject_id='.$_REQUEST[subject_id].'&course_id='.$_REQUEST[course_id].'&course_period_id='.$_REQUEST[course_period_id].'&month='.date(strtotime($_REQUEST['meet_date'])).'"; window.close();</script>'; 
        }else
            echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&subject_id='.$_REQUEST[subject_id].'&course_id='.$_REQUEST[course_id].'&course_period_id='.$_REQUEST[course_period_id].'&month='.date(strtotime($_REQUEST['meet_date'])).'"; window.close();</script>'; 
        }
        elseif($_POST['button']=='Clear&Exit')
        {
           $chek_assoc=  DBGet(DBQuery('SELECT COUNT(*) as REC_EX FROM schedule WHERE COURSE_PERIOD_ID='.$_REQUEST['course_period_id'].' AND (START_DATE<=\''.date('Y-m-d').'\' AND (END_DATE IS NULL OR END_DATE=\'0000-00-00\' OR END_DATE>=\''.date('Y-m-d').'\' ))'));
            if($chek_assoc[1]['REC_EX']==0)
            {
            DBQuery ("DELETE FROM course_period_var WHERE course_period_id=$_REQUEST[course_period_id] AND  course_period_date='".$_REQUEST[meet_date]."' and id='".$_REQUEST[cpv_id]."'");
            unset($_REQUEST['values']);
            unset($_SESSION['_REQUEST_vars']['values']);
            echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&subject_id='.$_REQUEST[subject_id].'&course_id='.$_REQUEST[course_id].'&course_period_id='.$_REQUEST[course_period_id].'&month='.date(strtotime($_REQUEST['meet_date'])).'"; window.close();</script>'; 
            }
            else
            {
                echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname='.$_REQUEST['modname'].'&error=Blocked_assoc&subject_id='.$_REQUEST[subject_id].'&course_id='.$_REQUEST[course_id].'&course_period_id='.$_REQUEST[course_period_id].'&month='.date(strtotime($_REQUEST['meet_date'])).'"; window.close();</script>'; 
            }
        }
        else
        {
                $cpblocked_RET=DBGet(DBQuery("SELECT COURSE_PERIOD_DATE,PERIOD_ID,ROOM_ID,DOES_ATTENDANCE FROM course_period_var where course_period_id=$_REQUEST[course_period_id] AND course_period_date='".$_REQUEST['meet_date']."' AND id='".$_REQUEST['id']."'"));
                $cpblocked_RET=$cpblocked_RET[1];
                $periods_RET = DBGet(DBQuery("SELECT PERIOD_ID,TITLE FROM school_periods WHERE SCHOOL_ID='" . UserSchool() . "' AND SYEAR='" . UserSyear() . "' ORDER BY SORT_ORDER"));
                if (count($periods_RET)) {
                    foreach ($periods_RET as $period)
                        $periods[$period['PERIOD_ID']] = $period['TITLE'];
                }

                $room_RET = DBGet(DBQuery("SELECT ROOM_ID,TITLE FROM rooms WHERE SCHOOL_ID='" . UserSchool() . "' ORDER BY SORT_ORDER"));
                if (count($room_RET)) {
                    foreach ($room_RET as $room)
                        $rooms[$room['ROOM_ID']] = $room['TITLE'];
                }
                if(isset($_REQUEST['values'])){
                    echo '<font color=red><b>'.$conflict.' on selected date</b></font>';
                    unset($_REQUEST['values']);
                    unset($_SESSION['_REQUEST_vars']['values']);
                    $_REQUEST['id']=$_REQUEST['cpv_id'];
                }
                echo "<FORM name=popform id=popform action=ForWindow.php?modname=$_REQUEST[modname]&meet_date=$_REQUEST[meet_date]&modfunc=detail&mode=$_REQUEST[mode]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=$_REQUEST[course_period_id]&calendar_id=$_REQUEST[calendar_id] METHOD=POST>";
                echo '<BR>';
                PopTableforWindow('header',$title);
                echo '<div id="block_error"></div>';
                echo '<TABLE cellpadding="0" width="100%">';
                echo '<input type="hidden" name="get_status" id="get_status" value="" />';
                 echo '<input type="hidden" name="'.$date.'_id" id="'.$date.'_id" value="'.$_REQUEST['course_period_id'].'"/>';
                 echo '<input type="hidden" id="run_block_valid" value="block"/>';
                echo '<TR><TD><B style="color:#436477">Add Class</B></TD></TR>';
                echo '<TR><TD class="break"></TD></TR></TABLE>';
                echo '<TABLE cellpadding="10">';
                if($_REQUEST['add']=='new')
                    unset($cpblocked_RET);
                if($_REQUEST['id']!='')
                    echo "<input type=hidden name=cpv_id value='$_REQUEST[id]' />";
                echo '<TR><TD align="right"><B>Date</B></TD><TD>'.  ProperDate($_REQUEST[meet_date]).'</TD></TR>';
                echo '<TR><TD align="right"><B>Period</B></TD><TD>'.SelectInput($cpblocked_RET['PERIOD_ID'], 'values[PERIOD_ID]', '', $periods, 'N/A','id='.$date.'_period class=cell_floating onchange="formcheck_periods_F2(\''.$date.'\');"').'</TD>';
                echo '<TD><input type="hidden" id="hidden_period_block" value="'.$cpblocked_RET['PERIOD_ID'].'" /></TD></TR>';
                echo '<TR><TD align="right"><B>Room</B></TD><TD>'.SelectInput($cpblocked_RET['ROOM_ID'], 'values[ROOM_ID]', '', $rooms,'N/A','id='.$date.'_room ').'</TD></TR>';
                echo '<TR><TD align="right"><B>Takes attendance</B></TD><TD>'.CheckboxInput($cpblocked_RET['DOES_ATTENDANCE'], 'values[DOES_ATTENDANCE]','','',false,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>', '<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>',true,' id='.$date.'_does_attendance onclick="formcheck_periods_attendance_F2('.(($date!='')?$date:1).',this);"'). '<br><div id="ajax_output"></div></TD></TR>';
                echo '<TR><TD colspan=2 align=center><INPUT type=submit class=btn_medium name=button value=Save onClick="return validate_block_schedule('.$date.');">';
                echo '&nbsp;';
                if($_REQUEST['mode']=='edit')
                    echo '<INPUT type=submit name=button class=btn_medium value=Clear&Exit onclick="formload_ajax(\'popform\');"> &nbsp ';
                else
                    echo ' &nbsp <INPUT type=submit name=button class=btn_medium value=Close onclick="window.close();">';
                echo '</TD></TR>';			
                echo '</TABLE>';
                PopTableWindow('footer');
                echo '</FORM>';
        }
}

if (!$_REQUEST['modfunc'] && !$_REQUEST['course_modfunc'] && !$_REQUEST['action']) {
    DrawBC("School Setup > " . ProgramTitle());
    $sql = "SELECT SUBJECT_ID,TITLE FROM course_subjects WHERE SCHOOL_ID='" . UserSchool() . "' AND SYEAR='" . UserSyear() . "' ORDER BY TITLE";
    $QI = DBQuery($sql);
    $subjects_RET = DBGet($QI);

    if (AllowEdit())
        $delete_button = "<INPUT type=button class=btn_medium value=Delete onClick='javascript:window.location=\"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=$_REQUEST[course_period_id]\"'> ";
    // ADDING & EDITING FORM
    if (clean_param($_REQUEST['course_period_id'], PARAM_ALPHANUM)) {
        if ($_REQUEST['course_period_id'] != 'new') {
            $sql = "SELECT PARENT_ID,TITLE,SHORT_NAME,
								MP,MARKING_PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID,CALENDAR_ID,IF(MARKING_PERIOD_ID IS NULL,BEGIN_DATE,NULL) AS BEGIN_DATE,IF(MARKING_PERIOD_ID IS NULL,END_DATE,NULL) AS END_DATE,
								TOTAL_SEATS,(TOTAL_SEATS - FILLED_SEATS) AS AVAILABLE_SEATS,
								GRADE_SCALE_ID,DOES_HONOR_ROLL,DOES_CLASS_RANK,
								GENDER_RESTRICTION,HOUSE_RESTRICTION,CREDITS,
								HALF_DAY,DOES_BREAKOFF,COURSE_WEIGHT,DAYS,PERIOD_ID,ROOM_ID,DOES_ATTENDANCE,SCHEDULE_TYPE
						FROM course_periods cp LEFT JOIN course_period_var cpv ON (cp.course_period_id=cpv.course_period_id)
						WHERE cp.COURSE_PERIOD_ID='$_REQUEST[course_period_id]'";
            $QI = DBQuery($sql);
            $RET = DBGet($QI);

            $RET = $RET[1];
            $title = $RET['TITLE'];
            $new = false;
//          
            $cpdays_RET=  DBGet(DBQuery("SELECT ID,DAYS,PERIOD_ID,ROOM_ID,DOES_ATTENDANCE,START_TIME,END_TIME FROM course_period_var where days IS NOT NULL AND course_period_id=$_REQUEST[course_period_id] ORDER BY ID"));
            
            $cpblocked_RET=  DBGet(DBQuery("SELECT COURSE_PERIOD_DATE,PERIOD_ID,ROOM_ID,DOES_ATTENDANCE FROM course_period_var where course_period_date IS NOT NULL AND course_period_id=$_REQUEST[course_period_id]"),array(),array('COURSE_PERIOD_DATE'));
            $div=true;
        } else {
            $sql = "SELECT TITLE
						FROM courses
						WHERE COURSE_ID='$_REQUEST[course_id]'";
            $QI = DBQuery($sql);
            $RET = DBGet($QI);
            $RET=$RET[1];
            $title = $RET['TITLE'] . ' - New Course Period';
            unset($RET);
            if($not_pass==true)
            {
                foreach($_REQUEST['tables']['course_periods'] as $id=>$data)
                {
                    foreach ($data as $key=>$val)
                    {
                        $RET[$key]=$val;
                    }
                }
                if($RET['SCHEDULE_TYPE']=='FIXED')
                    $RET=$RET+$columns_var;
                elseif($RET['SCHEDULE_TYPE']=='VARIABLE')
                    $cpdays_RET=$_REQUEST['course_period_variable']['new'];
                $div=false;
            }
            unset($delete_button);
            $checked = 'CHECKED';
            $new = true;
        }

        echo "<FORM name=F2 id=F2 action=Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=$_REQUEST[course_period_id] method=POST>";
        echo '<input type="hidden" name="get_status" id="get_status" value="" />';
        echo '<INPUT TYPE="hidden" NAME="course_period_day_checked" id="course_period_day_checked" VALUE="" />';      
        echo '<input type="hidden" name="cp_id" id="cp_id" value="' . $_REQUEST['course_period_id'] . '"/>';
        
        
        DrawHeaderHome($title, $delete_button . SubmitButton('Save', '', 'id=save_cp class=btn_medium onclick="return validate_course_period();"'));

        $header .= '<TABLE cellpadding=4 width=100%>';
        $header .= '<TR>';
        $header .= '<TD>' . TextInput($RET['SHORT_NAME'], 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][SHORT_NAME]', 'Short Name', 'class=cell_floating',$div) . '</TD>';
        echo '<input type="hidden" id="hidden_cp_id" value="'.$_REQUEST['course_period_id'].'">';
        $cal_RET = DBGet(DBQuery("SELECT TITLE,CALENDAR_ID FROM school_calendars WHERE SYEAR='" . UserSyear() . "' AND SCHOOL_ID='" . UserSchool() . "' ORDER BY DEFAULT_CALENDAR DESC"));
        $options = array();
        foreach ($cal_RET as $option)
            $options[$option['CALENDAR_ID']] = $option['TITLE'];
        if($_REQUEST[course_period_id]=='new')
            $header .= '<TD>' . SelectInput($RET['CALENDAR_ID'], 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][CALENDAR_ID]', 'Calendar', $options, 'N/A',' id=calendar_id onchange=reset_schedule();',$div) . '</TD>';
        else
        {
            $cal_sql="SELECT TITLE FROM school_calendars WHERE SYEAR='" . UserSyear() . "' AND SCHOOL_ID='" . UserSchool() . "' AND CALENDAR_ID='".$RET['CALENDAR_ID']."'";
            $cal_RET=DBGET(DBQuery($cal_sql));
            $cal_RET=$cal_RET[1];
            $header .= '<TD>' .NoInput($cal_RET['TITLE'],'Calendar'). '</TD>';
        }
        
            $teachers_RET = DBGet(DBQuery("SELECT STAFF_ID,LAST_NAME,FIRST_NAME,MIDDLE_NAME FROM staff INNER JOIN staff_school_relationship USING (staff_id) WHERE school_id='" . UserSchool() . "' AND SYEAR='" . UserSyear() . "' AND PROFILE='teacher' AND (ISNULL(IS_DISABLE) OR IS_DISABLE='N') AND (END_DATE>=CURDATE() OR END_DATE IS NULL OR END_DATE='0000-00-00') AND START_DATE<=CURDATE() ORDER BY LAST_NAME,FIRST_NAME "));
//       ECHO "SELECT STAFF_ID,LAST_NAME,FIRST_NAME,MIDDLE_NAME FROM staff INNER JOIN staff_school_relationship USING (staff_id) WHERE school_id='" . UserSchool() . "' AND SYEAR='" . UserSyear() . "' AND PROFILE='teacher' AND ISNULL(IS_DISABLE) AND (END_DATE>=CURDATE() OR END_DATE IS NULL OR END_DATE='0000-00-00') AND START_DATE<=CURDATE() ORDER BY LAST_NAME,FIRST_NAME ";
            if (count($teachers_RET)) {
            foreach ($teachers_RET as $teacher)
            {
                
                $teachers[$teacher['STAFF_ID']] = trim($teacher['LAST_NAME']) . ', ' . $teacher['FIRST_NAME'] . ' ' . $teacher['MIDDLE_NAME'];
            }
                }
//                ECHO 'SELECT TEACHER FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='.$_REQUEST['course_period_id'].'';
               if($_REQUEST['course_period_id']!='new')
               {
                $qr=  DBGET(DBQuery('SELECT TEACHER_ID FROM course_periods WHERE COURSE_PERIOD_ID='.$_REQUEST['course_period_id'].''));
               
                $teacher_id=$qr[1]['TEACHER_ID'];
                $qr1=DBGet(DBQuery('SELECT STAFF_ID,LAST_NAME,FIRST_NAME,MIDDLE_NAME FROM staff WHERE STAFF_ID='.$qr[1]['TEACHER_ID'].''));
              $k=$qr1[1]['STAFF_ID'];
               $teachers[$k]  = trim($qr1[1]['LAST_NAME']) .', ' . $qr1[1]['FIRST_NAME'] . ' ' . $qr1[1]['MIDDLE_NAME'];
   
    }
//       
        if($RET['AVAILABLE_SEATS']!=$RET['TOTAL_SEATS'])
        $header .= '<TD>' . SelectInputDisabledMsg($RET['TEACHER_ID'], 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][TEACHER_ID]', 'Primary Teacher', $teachers,'N/A','',$div,"To Change teacher go to School Setup->Courses->Teacher Re-Assignment") . '</TD>';
        else
        $header .= '<TD>' . SelectInput($RET['TEACHER_ID'], 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][TEACHER_ID]', 'Primary Teacher', $teachers,'N/A','',$div) . '</TD>';    
        
        $header .= '<TD>' . SelectInput($RET['SECONDARY_TEACHER_ID'], 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][SECONDARY_TEACHER_ID]', 'Secondary Teacher', $teachers,'N/A','',$div) . '</TD>';
        $header .= '<TD>' . TextInput($RET['TOTAL_SEATS'], 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][TOTAL_SEATS]', 'Seats', 'size=4 class=cell_floating',$div) . '</TD>';
        if ($_REQUEST['course_period_id'] != 'new')
            $header .= '<TD><FONT color=green>' . $RET['AVAILABLE_SEATS'] . '</FONT><BR><FONT color=gray><SMALL>Available Seats</SMALL></FONT></TD>';
        else
            $header .= '<TD>&nbsp;</TD>';
        
        $header .= '</TR>';
        $header .= '<TR>';
        $options_RET = DBGet(DBQuery("SELECT TITLE,ID FROM report_card_grade_scales WHERE SYEAR='" . UserSyear() . "' AND SCHOOL_ID='" . UserSchool() . "'"));
        $options = array();
        foreach ($options_RET as $option)
            $options[$option['ID']] = $option['TITLE'];
        $header .= '<TD>' . SelectInput($RET['GRADE_SCALE_ID'], 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][GRADE_SCALE_ID]', 'Grading Scale', $options, 'Not Graded','',$div) . '</TD>';
        $header .= '<TD valign=top>' . TextInput(sprintf('%0.3f', $RET['CREDITS']), 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][CREDITS]', 'Credit Hours', 'size=4 class=cell_floating') . '</TD>';
        $header .= '<TD>' . SelectInput($RET['GENDER_RESTRICTION'], 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][GENDER_RESTRICTION]', 'Gender Restriction', array('N' => 'None', 'M' => 'Male', 'F' => 'Female'), false,'',$div) . '</TD>';
         if ($_REQUEST['course_period_id'] != 'new' && $RET['PARENT_ID'] != $_REQUEST['course_period_id']) {
            $parent = DBGet(DBQuery("SELECT cp.TITLE as CP_TITLE,c.TITLE AS C_TITLE FROM course_periods cp,courses c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.COURSE_PERIOD_ID='" . $RET['PARENT_ID'] . "'"));
            $parent = $parent[1]['C_TITLE'] . ' : ' . $parent[1]['CP_TITLE'];
        } elseif ($_REQUEST['course_period_id'] != 'new') {
            $children = DBGet(DBQuery("SELECT COURSE_PERIOD_ID FROM course_periods WHERE PARENT_ID='" . $_REQUEST['course_period_id'] . "' AND COURSE_PERIOD_ID!='" . $_REQUEST['course_period_id'] . "'"));
            if (count($children))
                $parent = 'N/A';
            else
                $parent = 'None';
        }

        //		--------------------------------------------- Temp Coment ------------------------------------------------- 	//
        if ($_REQUEST['course_period_id'] != 'new' && $RET['PARENT_ID'] != $_REQUEST['course_period_id']) {
            $header .= "<TD colspan=2><DIV id=course_div>" . $parent . "</DIV> " . ($parent != 'N/A' && AllowEdit() ? "<A HREF=# onclick='window.open(\"ForWindow.php?modname=miscellaneous/ChooseParentCourse.php\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Choose</A>" . "&nbsp;&nbsp;" . "<INPUT type=checkbox name='tables[course_periods][" . $_REQUEST['course_period_id'] . "][PARENT_ID]' value='" . $_REQUEST['course_period_id'] . "' >&nbsp;Remove" . "<BR>" : '') . "<small><FONT color=" . Preferences('TITLES') . ">Parent Period</FONT></small></TD>";
        } else {
            $header .= "<TD colspan=2><DIV id=course_div>" . $parent . "</DIV> " . ($parent != 'N/A' && AllowEdit() ? "<A HREF=# onclick='window.open(\"ForWindow.php?modname=miscellaneous/ChooseParentCourse.php\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Choose</A><BR>" : '') . "<small><FONT color=" . Preferences('TITLES') . ">Parent Period</FONT></small></TD>";
        }
		
        
        $header .= '<TD>' . CheckboxInput($RET['DOES_BREAKOFF'], 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][DOES_BREAKOFF]', 'Allow Teacher Gradescale', $checked, $new, '<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>', '<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>',$div) . '</TD>';
        $header .= '</TR><TR>'; 
        $header .= '<TD>' . CheckboxInput($RET['COURSE_WEIGHT'], 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][COURSE_WEIGHT]', 'Course is Weighted', $checked, $new, '<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>', '<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>',$div) . '</TD>';
        
                     
//        
        $header .= '<TD valign=top>' . CheckboxInput($RET['DOES_HONOR_ROLL'], 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][DOES_HONOR_ROLL]', 'Affects Honor Roll', $checked, $new, '<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>', '<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>',$div) . '</TD>';
        $header .= '<TD valign=top>' . CheckboxInput($RET['HALF_DAY'], 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][HALF_DAY]', 'Half Day', $checked, $new, '<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>', '<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>',$div) . '</TD>';
        $header .= '<TD>' . CheckboxInput($RET['DOES_CLASS_RANK'], 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][DOES_CLASS_RANK]', 'Affects Class Rank', $checked, $new, '<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>', '<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>',$div) . '</TD>';
               
        $header .= '</TR>';
        unset($options);
        $mp_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,SHORT_NAME,'2' AS t,SORT_ORDER FROM school_quarters WHERE SCHOOL_ID='" . UserSchool() . "' AND SYEAR='" . UserSyear() . "' UNION SELECT MARKING_PERIOD_ID,SHORT_NAME,'1' AS t,SORT_ORDER FROM school_semesters WHERE SCHOOL_ID='" . UserSchool() . "' AND SYEAR='" . UserSyear() . "' UNION SELECT MARKING_PERIOD_ID,SHORT_NAME,'0' AS t,SORT_ORDER FROM school_years WHERE SCHOOL_ID='" . UserSchool() . "' AND SYEAR='" . UserSyear() . "' ORDER BY 3,4"));

        if (count($mp_RET)) {
            foreach ($mp_RET as $mp)
                $options[$mp['MARKING_PERIOD_ID']] = $mp['SHORT_NAME'];
        } 
        $header .= '<TR><TD colspan=6><BR /><B>Choose the Duration</B></TD></TR><TR><TD colspan=6><TABLE cellpadding=4 width="100%" style="border:1px dotted #999"><TR><TD colspan=2><TABLE height="60px"><TR><TD><input type=radio name=date_range value=mp id=preset onchange=mp_range_toggle(this);  '.($RET['MARKING_PERIOD_ID']?' checked':'').'></TD><TD><label for=preset id=select_mp style="display:'.($RET['MARKING_PERIOD_ID']?'none':'block').'">Marking Period</label><DIV id=mp_range style=float:left;display:'.($RET['MARKING_PERIOD_ID']?'block':'none').'>' . SelectInput($RET['MARKING_PERIOD_ID'], 'tables[course_periods][' . $_REQUEST['course_period_id'] . '][MARKING_PERIOD_ID]', 'Marking Period', $options,'N/A','id=marking_period',$div) . '</DIV></TD></TR></TABLE></TD>';

          $header .= '<TD colspan=4 width="550px"><TABLE><TR><TD><input type=radio name=date_range value=dr id=custom onchange=mp_range_toggle(this); '.($RET['BEGIN_DATE']?' checked':'').'></TD><TD><label for=custom id=select_range style="display:'.($RET['BEGIN_DATE']?'none':'block').'">Custom Date Range</label><DIV id=date_range style=display:'.($RET['BEGIN_DATE']?'block':'none').'><TABLE><TR><TD>' . DateInputAY($RET['BEGIN_DATE'], 'begin', 1) . '</TD><TD> &nbsp; &nbsp; &nbsp; </TD><TD>  ' . DateInputAY($RET['END_DATE'], 'end', 2) . '</TD></TR></TABLE> </DIV></TD></TR></TABLE></TD></TR></TABLE></TD></TR>';
		

          $header .= '<TR><TD colspan=6><BR /><B>Choose Schedule Type</B></TD></TR><TR><TD colspan=6><TABLE cellpadding=4 width="100%" style="border:1px dotted #999"><TR><TD><input type=radio name=schedule_type id=fixed_schedule value=fixed onclick=show_cp_meeting_days(this.value,"'.$_REQUEST[course_period_id].'"); '.($RET['SCHEDULE_TYPE']=='FIXED'?' checked':'').' '.disabled().'><label for=fixed_schedule>Fixed Schedule</label></TD><TD><input type=radio name=schedule_type id=variable_schedule value=variable onclick=show_cp_meeting_days(this.value,"'.$_REQUEST[course_period_id].'"); '.($RET['SCHEDULE_TYPE']=='VARIABLE'?' checked':'').' '.disabled().'><label for=variable_schedule> Variable Schedule</label></TD><TD colspan=3><input type=radio name=schedule_type id=blocked_schedule value=blocked onclick=show_cp_meeting_days(this.value,"'.$_REQUEST[course_period_id].'");  '.($RET['SCHEDULE_TYPE']=='BLOCKED'?' checked':'').' '.disabled().'><label for=blocked_schedule>Enter by Calendar Days</label></TD>';
        $header .= '</TR>';
        $header .='<TR>';
         if($_REQUEST['course_period_id']!='new' || $not_pass==true)
        {
            $periods_RET = DBGet(DBQuery("SELECT PERIOD_ID,TITLE FROM school_periods WHERE SCHOOL_ID='" . UserSchool() . "' AND SYEAR='" . UserSyear() . "' ORDER BY SORT_ORDER"));
            if (count($periods_RET)) {
            foreach ($periods_RET as $period)
            $periods[$period['PERIOD_ID']] = $period['TITLE'];
            }

            $room_RET = DBGet(DBQuery("SELECT ROOM_ID,TITLE FROM rooms WHERE SCHOOL_ID='" . UserSchool() . "' ORDER BY SORT_ORDER"));
            if (count($room_RET)) {
            foreach ($room_RET as $room)
            $rooms[$room['ROOM_ID']] = $room['TITLE'];
            }
            $days_RET=  DBGet(DBQuery("SELECT DAYS FROM school_calendars WHERE calendar_id=$RET[CALENDAR_ID]"));
            $cal_days=str_split($days_RET[1]['DAYS']);
            foreach($cal_days as $day)
            {
                $caldays[$day]=  conv_day($day);
            }
        }
        if($_REQUEST['course_period_id']=='new'&& $not_pass==false)
            $header .= '<TD colspan=6><DIV id=meeting_days></DIV></TD>';
        elseif($RET['SCHEDULE_TYPE']=='VARIABLE')
        {
            $header .='<input type=hidden name=tables[course_periods]['.$_REQUEST['course_period_id'].'][SCHEDULE_TYPE] value=VARIABLE id="variable"/>';
            echo '<input type="hidden" name="get_status" id="get_status" value="" />';
           
            $header .= '<TD colspan=6><DIV id=meeting_days>';
            
            $header .= '<TABLE  width=100% class="grid"><TR><TD></TD><TD width="100px" class="subtabs"><strong>Days</strong></TD><TD width="200px" class="subtabs"><strong>Period</strong></TD><TD class="subtabs"><strong>Time</strong></TD><TD width="150px" class="subtabs"><strong>Room</strong></TD><TD width="130px" align="center" class="subtabs"><strong>Takes attendance</strong></TD></TR>';
			$rowcolor='even';
                        if($not_pass==true)
                        {
                        $cp_var_val=$cpdays_RET;
                        }
            if($_REQUEST['course_period_id'] != 'new')
            {
            if(count($cpdays_RET)>0)
                {
              for($i=1;$i<=count($cpdays_RET);$i++)
                {

                   $cp_var_val=$cpdays_RET[$i];
   
                $header .='<TR class="'.$rowcolor.'"><TD align="center">'.'<a href=\'Modules.php?modname='.$_REQUEST['modname'].'&action=delete&subject_id='.$_REQUEST['subject_id'].'&course_id='.$_REQUEST['course_id'].'&course_period_id='.$_REQUEST['course_period_id'].'&cpv_id='.$cp_var_val['ID'].'\' >'.button('remove').'</a>'.'';
                $header .='<input type="hidden" name="course_period_variable['.$_REQUEST['course_period_id'].']['.$cp_var_val['ID'].'][DAYS]" value='.$cp_var_val['DAYS'].'</TD>';
                $header .='<TD>' .SelectInput($cp_var_val['DAYS'], 'course_period_variable['.$_REQUEST['course_period_id'].']['.$cp_var_val['ID'].'][DAYS]'.$i,'',$caldays,'N/A','id=days'.$i) .'</TD>';
                $header .='<TD>'. SelectInput($cp_var_val['PERIOD_ID'], 'course_period_variable[' . $_REQUEST['course_period_id'] . ']['.$cp_var_val['ID'].'][PERIOD_ID]'.$i, '', $periods, 'N/A','id='.$cp_var_val['DAYS'].$i.'_period class=cell_floating  onchange=show_period_time(this.value,"'.$cp_var_val['DAYS'].$i.'","'.$_REQUEST['course_period_id'].'","'.$cp_var_val['ID'].'");',$div) . '<input type=hidden name=course_period_variable['.$_REQUEST['course_period_id'].']['.$cp_var_val['ID'].'][ID] value="'.$cp_var_val['ID'].'"></TD>';
                $header .='<TD><div id='.$cp_var_val['DAYS'].$i.'_period_time>'.($cp_var_val['PERIOD_ID']? ProperTime($cp_var_val[START_TIME]).' To '.ProperTime($cp_var_val[END_TIME]).'<input type=hidden name=course_period_variable[' . $_REQUEST['course_period_id'] . '][' . $cp_var_val['ID'] . '][START_TIME] value="'.$cp_var_val[START_TIME].'"><input type=hidden name=course_period_variable['.$_REQUEST['course_period_id'].']['.$cp_var_val['ID'].'][END_TIME] value="'.$cp_var_val[END_TIME].'">' : '').'</div></TD>'; 
                $header .='<TD>' . SelectInput($cp_var_val['ROOM_ID'], 'course_period_variable[' .$_REQUEST['course_period_id'] .']['.$cp_var_val['ID'].'][ROOM_ID]', '', $rooms,'N/A','id='.$cp_var_val['DAYS'].'_room ',$div) . '<input type=hidden id=course_period_variable['.$_REQUEST['course_period_id'].']['.$cp_var_val['ID'].'][ROOM_ID]'.$i.' value="'.$cp_var_val['ROOM_ID'].'"></TD>';
                $header .='<TD align="center">' . CheckboxInput($cp_var_val['DOES_ATTENDANCE'], 'course_period_variable[' . $_REQUEST['course_period_id'] . ']['.$cp_var_val['ID'].'][DOES_ATTENDANCE]'.$i, '', '',false,'Yes','No',($value=='Y'?$div:false),' id='.$cp_var_val['DAYS'].$i.'_does_attendance onclick="formcheck_periods_attendance_F2(3,this,'.$i.');"') . '<br></TD></TR>';
                echo '<input type="hidden" name="cp_id" id="'.$cp_var_val['DAYS'].$i.'_id" value="'.$_REQUEST['course_period_id'].'"/>';
                echo '<input type="hidden" name="fixed_day" id="fixed_day3_'.$i.'" value="'.$cp_var_val['DAYS'].$i.'" />';
                echo '<input type="hidden"  id="disabled_option_'.$i.'" value="'.$cp_var_val['PERIOD_ID'].'" />';
                echo '<input type="hidden" id="for_editing_room" value="'.$cp_var_val['ID'].'"/>';
                
                }
                }
                }
                $header .='<TR class="'.$rowcolor.'"><TD align="center">'.button('add').'</TD>';
                $header .='<TD>' .SelectInput('', 'course_period_variable[' . $_REQUEST['course_period_id'] . '][n][DAYS]','',$caldays,'N/A','id=n') .'</TD>';
                $header .='<TD>' . SelectInput('', 'course_period_variable[' . $_REQUEST['course_period_id'] . '][n][PERIOD_ID]', '', $periods, 'N/A','id=n_period class=cell_floating '.$disable.' onchange=show_period_time(this.value,"n","'.$_REQUEST['course_period_id'].'","n");',$div) . '</TD>';
                $header .='<TD><div id=n_period_time></div></TD>'; 
                $header .='<TD>' . SelectInput('', 'course_period_variable[' . $_REQUEST['course_period_id'] . '][n][ROOM_ID]', '', $rooms,'N/A','id=n_room '.$disable,$div) . '</TD>';
                $header .='<TD align="center">' . CheckboxInput('', 'course_period_variable[' . $_REQUEST['course_period_id'] . '][n][DOES_ATTENDANCE]', '', '',false,'Yes','No',($value=='Y'?$div:false),' id=n_does_attendance onclick="formcheck_periods_attendance_F2(4,this);"'.$disable) . '<br></TD></TR>';
            $header .= '</TR></TABLE>';
            $header .= '<center><table><tr><td><div id="ajax_output"></div></td></tr></table></center>';
            $header .='</DIV></TD>';
            echo '<input type="hidden" name="cp_id" id="n_id" value="'.$_REQUEST['course_period_id'].'"/>';
            echo '<input type="hidden" name="fixed_day4" id="fixed_day4" value="n" />';
        }
        elseif ($RET['SCHEDULE_TYPE']=='FIXED')
        {
                $header .='<input type=hidden name=tables[course_periods][' . $_REQUEST['course_period_id'] . '][SCHEDULE_TYPE] value=FIXED />';
                echo '<input type="hidden" name="get_status" id="get_status" value="" />';
                echo '<input type="hidden" name="cp_id" id="'.$day.'_id" value="'.$course_period_id.'"/>';
                $header .='<TD colspan=4><DIV id=meeting_days>';
                $header .= '<TABLE  width=100%><TR>';
                $header .='<TD>' . SelectInput($RET['ROOM_ID'], 'tables[course_period_var][' . $_REQUEST['course_period_id'] . '][ROOM_ID]', 'Class Room', $rooms,'N/A','id='.$day.'_room '.$disable,$div) . '</TD>';
                $header .='<TD>' . SelectInput($RET['PERIOD_ID'], 'tables[course_period_var][' . $_REQUEST['course_period_id'] . '][PERIOD_ID]', 'Period', $periods, 'N/A','id='.$day.'_period class=cell_floating onClick="disable_hidden_field('.(($day!='')?2:1).');"'.$disable,$div) . '</TD>';
                $header.='<input type=hidden id="'.$day.'_period" value="'.$RET['PERIOD_ID'].'" name=fixed_hidden>';
                $header.='<input type=hidden id="fixed_tag_name" value="'.'tables[course_period_var][' . $_REQUEST['course_period_id'] . '][PERIOD_ID]'.'">';
                $header .= '<TD>';
                if($not_pass!=true)
                    $header .= '<DIV id=days><div onclick=\'addHTML("';
                $header .= '<TABLE><TR>';
                foreach ($caldays as $day=>$short_day) {
                if (strpos($RET['DAYS'], $day) !== false)
                $value = 'Y';
                else
                $value = '';

                $header .= '<TD>' .CheckboxInput($value, 'tables[course_period_var]['.$_REQUEST['course_period_id'].'][DAYS][' . $day . ']', ($day == 'U' ? 'S' : $day), $checked, false, '', '', false) . '</TD>';
                }
                $header .= '</TR></TABLE>';
                if($not_pass!=true)
                    $header .= '","days",true);\'>' . $RET['DAYS'] . '</div></DIV><small><FONT color=' . Preferences('TITLES') . '>Meeting Days</FONT></small>';
                $header .= '</TD>';
                $header .= '<TD valign=top align="center">' . CheckboxInput($RET['DOES_ATTENDANCE'], 'tables[course_period_var][' . $_REQUEST['course_period_id'] . '][DOES_ATTENDANCE]', 'Takes attendance', $checked, $new, '<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>', '<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>', $div,' id='.$day.'_does_attendance onclick="formcheck_periods_attendance_F2('.(($day!='')?2:1).',this);"') . '<br><div id="ajax_output"></div></TD>';
                $header .= '</TR></TABLE></TD>';
                echo '<input type="hidden" name="fixed_day" id="fixed_day" value="'.$day.'" />';
                
        }
        elseif($RET['SCHEDULE_TYPE']=='BLOCKED')
        {
                if($RET['MARKING_PERIOD_ID']!='')
                {
                    $mp_RET=  DBGet(DBQuery("SELECT START_DATE,END_DATE FROM marking_periods WHERE marking_period_id=$RET[MARKING_PERIOD_ID]"));
                    $mp_RET=$mp_RET[1];
                    $begin=  $mp_RET['START_DATE'];
                    $end= $mp_RET['END_DATE'];
                }
                elseif($RET['BEGIN_DATE']!='' && $RET['END_DATE']!='')
                {
                    $begin=  $RET['BEGIN_DATE'];
                    $end= $RET['END_DATE'];
                }
                $header .='<input type=hidden name=tables[course_periods][' . $_REQUEST['course_period_id'] . '][SCHEDULE_TYPE] value=BLOCKED />';
                $header .='<TD colspan=6>';
                $header .= "<DIV id=meeting_days><CENTER>";
                $header .=_makeMonths('Modules.php?modname='.$_REQUEST['modname'].'&subject_id='.$_REQUEST['subject_id'].'&course_id='.$_REQUEST['course_id'].'&course_period_id='.$_REQUEST['course_period_id'].'&month=',$begin,$end);
                $header .="<TABLE border=0 cellpadding=0 cellspacing=0 class=pixel_border><TR><TD>";
                $header .= "<TABLE cellpadding=3 cellspacing=1><TR class=calendar_header align=center>";
                $header .= "<TD class=white>Sunday</TD><TD class=white>Monday</TD><TD class=white>Tuesday</TD><TD class=white>Wednesday</TD><TD class=white>Thursday</TD><TD class=white>Friday</TD><TD width=99 class=white>Saturday</TD>";
                $header .= "</TR><TR>";
                
                $month=date('m',$_REQUEST['month']);
                $year=date('Y',$_REQUEST['month']);
                $time = mktime(0,0,0,$month,1,$year);
                $last = 31;
                while(!checkdate(9, $last,2012))
                    $last--;
                $skip = date("w",$time);
                
                if($skip)
                {
                $header .= "<td colspan=" . $skip . "></td>";
                $return_counter = $skip;
                }
                for($i=1;$i<=$last;$i++)
                {
                        $day_time = mktime(0,0,0,$month,$i,$year);
                        $date = date('Y-m-d',$day_time);
                        $header .= "<TD  title='".  ProperDate($date)."' width=100 class=".($periods[$cpblocked_RET[$date][1]['PERIOD_ID']]?'calendar_active':'calendar_holiday')." valign=top>
                                <table width=100><tr><td width=5 valign=top>$i</td><td width=95 align=right></TD></TR>";
                        $header .= "</td></tr><tr><TD colspan=2 height=40 valign=top>";
                        if(in_array(date('D',$day_time), $caldays) && $date>=$begin && $date<=$end)
                        {
                                
                                $block_periods=DBGet(DBQuery("SELECT * FROM course_period_var WHERE course_period_id='".$_REQUEST['course_period_id']."'                                    
                                                              AND course_period_date='".$date."'"));                            
                                $header .=$periods[$cpblocked_RET[$date][1]['PERIOD_ID']].'<br>';
                                $header .=$rooms[$cpblocked_RET[$date][1]['ROOM_ID']].'<br>';
                                if($cpblocked_RET[$date][1]['PERIOD_ID']=='' && AllowEdit())                                
                                    $header .=  '<tr><td valign=bottom align=left>'.button('add','',"# onclick='javascript:window.open(\"ForWindow.php?modname=$_REQUEST[modname]&modfunc=detail&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=$_REQUEST[course_period_id]&mode=add&calendar_id=$_REQUEST[calendar_id]&meet_date=$date\",\"blank\",\"width=600,height=400\"); return false;'")."</td></tr>";                            
                                else
                                {
                                    foreach($block_periods as $ind=>$data)
                                    {
                                        $header .='<table><tr><td>attendance : '.($data['DOES_ATTENDANCE']=='Y'?'Yes':'No').'</td>';
                                        if(AllowEdit())
                                        $header .=  '<td valign=bottom align=left>'.button('edit','',"# onclick='javascript:window.open(\"ForWindow.php?modname=$_REQUEST[modname]&modfunc=detail&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=$_REQUEST[course_period_id]&mode=edit&calendar_id=$_REQUEST[calendar_id]&id=$data[ID]&meet_date=$date\",\"blank\",\"width=600,height=400\"); return false;'")."</td></tr></table>";
                                        else
                                            $header .=  '<td></td></tr></table>';
                                    }
                                    if(AllowEdit())
                                    $header .=  '<tr><td valign=bottom align=left>'.button('add','',"# onclick='javascript:window.open(\"ForWindow.php?modname=$_REQUEST[modname]&modfunc=detail&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=$_REQUEST[course_period_id]&mode=add&calendar_id=$_REQUEST[calendar_id]&meet_date=$date&add=new\",\"blank\",\"width=600,height=400\"); return false;'")."</td></tr>";
                                }                                
                                
                        }
                        $header .= "</td></tr>";
                        $header .= "</table></TD>";
                        $return_counter++;

                        if($return_counter%7==0)
                        $header .= "</TR><TR>";
                }
                $header .= "</TR></TABLE>";

                $header .= "</TD></TR></TABLE>";
                $header .= "</DIV></CENTER>";
                $header .='</TD>';
        }
        $header .= '</TR></TABLE></TD></TR>';
        $header .= '</TABLE>';
        DrawHeaderHome($header);
        echo '</FORM>';
    } elseif (clean_param($_REQUEST['course_id'], PARAM_ALPHANUM)) {
        $grade_level_RET = DBGet(DBQuery("SELECT ID,TITLE FROM school_gradelevels WHERE school_id='" . UserSchool() . "'"));
        if ($_REQUEST['course_id'] != 'new') {
            $sql = "SELECT c.TITLE,sg.TITLE AS GRADE_LEVEL_TITLE,c.SHORT_NAME,GRADE_LEVEL
						FROM courses c LEFT JOIN school_gradelevels sg ON c.grade_level=sg.id
						WHERE COURSE_ID='$_REQUEST[course_id]'";
            $QI = DBQuery($sql);
            $RET = DBGet($QI);
            $RET = $RET[1];
            $title = trim($RET['TITLE'] . ' - ' . $RET['GRADE_LEVEL_TITLE'], '- ');
        } else {
            $sql = "SELECT TITLE
						FROM course_subjects
						WHERE SUBJECT_ID='$_REQUEST[subject_id]' ORDER BY TITLE";
            $QI = DBQuery($sql);
            $RET = DBGet($QI);
            $title = $RET[1]['TITLE'] . ' - New Course';
            unset($delete_button);
            unset($RET);
        }

        echo "<FORM name=F3 id=F3 action=Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id] method=POST>";
        DrawHeaderHome($title, $delete_button . SubmitButton('Save', '', 'class=btn_medium onclick="return formcheck_Timetable_course_F3();"'));
        $header .= '<TABLE cellpadding=3 width=100%>';
        $header .= '<TR>';
        foreach ($grade_level_RET as $grade_level)
            $grade_levels[$grade_level['ID']] = $grade_level['TITLE'];
        $header .= '<TD>' . TextInput($RET['TITLE'], 'tables[courses][' . $_REQUEST['course_id'] . '][TITLE]', 'Title', 'id=course_title class=cell_mod_wide') . '</TD>';
        $header .= '<TD>' . TextInput($RET['SHORT_NAME'], 'tables[courses][' . $_REQUEST['course_id'] . '][SHORT_NAME]', 'Short Name', 'id=short_name class=cell_floating') . '</TD>';
//        if($_REQUEST['course_id'] != 'new')
//        $header .= "<TD><a href=# onclick='window.open(\"ForWindow.php?modname=$_REQUEST[modname]&modfunc=enter_standards&course_id=$_REQUEST[course_id]\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'><b>Standards</b></a></TD>";
        $header .="<TD><input type=hidden value=".$_REQUEST['course_id']." id=course_id_div /></TD>";
        $header .= '<TD>' . SelectInput($RET['GRADE_LEVEL'], 'tables[courses][' . $_REQUEST['course_id'] . '][GRADE_LEVEL]', 'Grade Level', $grade_levels) . '</TD>';

        foreach ($subjects_RET as $type)
            $options[$type['SUBJECT_ID']] = $type['TITLE'];

        $header .= '<TD>' . SelectInput($RET['SUBJECT_ID'] ? $RET['SUBJECT_ID'] : $_REQUEST['subject_id'], 'tables[courses][' . $_REQUEST['course_id'] . '][SUBJECT_ID]', 'Subject', $options, false) . '</TD>';
        $header .= '</TR>';
        $header .= '</TABLE>';
        DrawHeaderHome($header);
        echo '</FORM>';
    } elseif (clean_param($_REQUEST['subject_id'], PARAM_ALPHANUM)) {
        if ($_REQUEST['subject_id'] != 'new') {
            $sql = "SELECT TITLE
						FROM course_subjects
						WHERE SUBJECT_ID='$_REQUEST[subject_id]'";
            $QI = DBQuery($sql);
            $RET = DBGet($QI);
            $RET = $RET[1];
            $title = $RET['TITLE'];
        } else {
            $title = 'New Subject';
            unset($delete_button);
        }

        echo "<FORM name=F4 id=F4 action=Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id] method=POST>";
        DrawHeaderHome($title, $delete_button . SubmitButton('Save', '', 'class=btn_medium onclick="formcheck_Timetable_course_F4();"'));
        $header .= '<TABLE cellpadding=3 width=100%>';
        $header .= '<TR>';

        $header .= '<TD>' . TextInput($RET['TITLE'], 'tables[course_subjects][' . $_REQUEST['subject_id'] . '][TITLE]', 'Title', 'class=cell_wide') . '</TD>';

        $header .= '</TR>';
        $header .= '</TABLE>';
        DrawHeader($header);
        echo '</FORM>';
    }


    // DISPLAY THE MENU
    $LO_options = array('save' => false, 'search' => false);

    if (!$_REQUEST['subject_id'])

        DrawHeaderHome('Course', "<A HREF=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&course_modfunc=search>Search</A>");

    echo '<TABLE><TR>';

    if (count($subjects_RET)) {
        if (clean_param($_REQUEST['subject_id'], PARAM_ALPHANUM)) {
            foreach ($subjects_RET as $key => $value) {
                if ($value['SUBJECT_ID'] == $_REQUEST['subject_id'])
                    $subjects_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
            }
        }
    }

    echo '<TD valign=top>';
    $columns = array('TITLE' => 'Subject');
    $link = array();
    $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]";
    $link['TITLE']['variables'] = array('subject_id' => 'SUBJECT_ID');
    $link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&subject_id=new";
    ListOutput($subjects_RET, $columns, 'Subject', 'Subject', $link, array(), $LO_options);
    echo '</TD>';

    if (clean_param($_REQUEST['subject_id'], PARAM_ALPHANUM) && $_REQUEST['subject_id'] != 'new') {
        $sql = "SELECT COURSE_ID,c.TITLE, CONCAT_WS(' - ',c.title,sg.title) AS GRADE_COURSE FROM courses c LEFT JOIN school_gradelevels sg ON c.grade_level=sg.id WHERE SUBJECT_ID='$_REQUEST[subject_id]' ORDER BY c.TITLE";
        $QI = DBQuery($sql);
        $courses_RET = DBGet($QI);

        if (count($courses_RET)) {
            if (clean_param($_REQUEST['course_id'], PARAM_ALPHANUM)) {
                foreach ($courses_RET as $key => $value) {
                    if ($value['COURSE_ID'] == $_REQUEST['course_id'])
                        $courses_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
                }
            }
        }

        echo '<TD valign=top>';
        $columns = array('GRADE_COURSE' => 'Course');
        $link = array();
        $link['GRADE_COURSE']['link'] = "Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]";
       
        $link['GRADE_COURSE']['variables'] = array('course_id' => 'COURSE_ID');
        $link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=new";

        ListOutput($courses_RET, $columns, 'Course', 'Courses', $link, array(), $LO_options);
        echo '</TD>';

        if (clean_param($_REQUEST['course_id'], PARAM_ALPHANUM) && $_REQUEST['course_id'] != 'new') {
            $sql = "SELECT COURSE_PERIOD_ID,TITLE,COALESCE(TOTAL_SEATS-FILLED_SEATS,0) AS AVAILABLE_SEATS FROM course_periods WHERE COURSE_ID='$_REQUEST[course_id]' AND (marking_period_id IN(" . GetAllMP(GetMPTable(GetMP(UserMP(), 'TABLE')), UserMP()) . ") OR (MARKING_PERIOD_ID IS NULL)) ORDER BY TITLE";

            $QI = DBQuery($sql);
            $periods_RET = DBGet($QI);

            if (count($periods_RET)) {
                if (clean_param($_REQUEST['course_period_id'], PARAM_ALPHANUM)) {
                    foreach ($periods_RET as $key => $value) {
                        if ($value['COURSE_PERIOD_ID'] == $_REQUEST['course_period_id'])
                            $periods_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
                    }
                }
            }

            echo '<TD valign=top>';
            $columns = array('TITLE' => 'Period');
            if ($_REQUEST['modname'] == 'Schdeuling/Schedule.php')
                $columns += array('AVAILABLE_SEATS' => 'Available Seats');
            $link = array();
            $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]";
            $link['TITLE']['variables'] = array('course_period_id' => 'COURSE_PERIOD_ID');
            $link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=new";
            ListOutput($periods_RET, $columns, 'Period', 'Periods', $link, array(), $LO_options);
            echo '</TD>';
        }
    }

    echo '</TR></TABLE>';
}

if (clean_param($_REQUEST['modname'], PARAM_ALPHAEXT) == 'Schedule/Courses.php' && clean_param($_REQUEST['course_period_id'], PARAM_ALPHANUM)) {
    $course_title = DBGet(DBQuery("SELECT TITLE FROM course_periods WHERE COURSE_PERIOD_ID='" . $_REQUEST['course_period_id'] . "'"));
    $course_title = $course_title[1]['TITLE'] . '<INPUT type=hidden name=tables[parent_id] value=' . $_REQUEST['course_period_id'] . '>';
    echo "<script language=javascript>opener.document.getElementById(\"course_div\").innerHTML = \"$course_title</small>\"; window.close();</script>";
}

function conv_day($short_date,$type='')
{
    $days = array('U'=>'Sun', 'M'=>'Mon', 'T'=>'Tue', 'W'=>'Wed', 'H'=>'Thu', 'F'=>'Fri', 'S'=>'Sat');
    if($type=='key')
        return array_search($short_date,$days);
    else
        return $days[$short_date];
}

function disabled()
{
    if($_REQUEST['course_period_id']!='new')
        return 'disabled';
}

function _makeMonths($link,$begin_date,$end_date)
{
    $begin_date=  strtotime($begin_date);
    $end_date=  strtotime($end_date);
    $one_day=86400;
    if(!$_REQUEST['month'])
    {
        $_REQUEST['month']=date($begin_date);
    }
    $days=date('t',$_REQUEST['month']);
    
    $last_day_end=date('t',$end_date);
   $begin=strtotime(date('Y-m-1',$begin_date))."<br>";
    $end=strtotime(date('Y-m-'.$last_day_end,$end_date));   
    $prev=$_REQUEST['month']-$one_day*30;
    $next=$_REQUEST['month']+$one_day*$days;
    $prev_month_f=strtotime(date('Y-m-d',$next));
    $prev_month_f=strtotime('Previous Month',$prev_month_f);
    if($link!=''){
        if($prev >= $begin)
        {
            $prev_month_f=strtotime('Previous Month',$prev_month_f);
            $prev=$prev_month_f;
            $html .= "<a href='javascript:void(0);' title='Previous' onclick=\"window.location='".$link.$prev."';\" style=\"font-size:12px;\">&lt;&lt; Prev</a> &nbsp; &nbsp; ";
        }
        $html .="<strong><span style=\"font-size:12px;\">".date('F', $_REQUEST['month'])."&nbsp;".date('Y', $_REQUEST['month'])."</span></strong> &nbsp; &nbsp; ";
        if($next<=$end)      
            $html .="<a href='javascript:void(0);' title='Next' onclick=\"window.location='".$link.$next."';\" style=\"font-size:12px;\">Next &gt;&gt;</a>";
    }
    
    return $html;
}

function makeTextInput($value,$name)
{       global $THIS_RET;
        if($THIS_RET['ID'])
                $id = $THIS_RET['ID'];
        else
                $id = 'new';

        if($name!='TITLE')
                $extra = 'size=5 maxlength=5 class=cell_small';
                else 
        $extra = 'class=cell_wide ';


        return $comment.TextInput($value,'values['.$id.']['.$name.']','',$extra);
}


function _makeChooseCheckbox($value,$title)
{
	return "<INPUT type=checkbox name=stand_arr[] value=$value checked>";
}


?>

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

foreach($_REQUEST as $i=>$r)
{

    if($i=='month_schedule')
    {
        foreach($r as $dt=>$req)
        {
            foreach($req as $r_d)
            $end_d[]=$r_d['END_DATE'];
        }
    }
    if($i=='day_schedule')
    {
        foreach($r as $dt=>$req)
        {
            foreach($req as $r_d)
            $end_d[]=$r_d['END_DATE'];
        }
    }
    if($i=='year_schedule')
    {
        foreach($r as $dt=>$req)
        {
            foreach($req as $r_d)
            $end_d[]=$r_d['END_DATE'];
        }
    }
}
$end_d=implode('-',$end_d);
$end_d=date('m-d-Y',strtotime($end_d));
include('../../RedirectModulesInc.php');
ini_set('memory_limit', '12000000M');
ini_set('max_execution_time','50000');
DrawBC("Scheduling >> ".ProgramTitle());
$tot_cp='';
Widgets('activity');
Widgets('course');
Widgets('request');

if(!$_SESSION['student_id']){
Search('student_id',$extra);
}
####################

/////For deleting schedule
if($_REQUEST['del']=='true')
{   
    $association_query_reportcard=DBQuery('Select * from  student_report_card_grades where student_id=\''.UserStudentId() .'\' and course_period_id=\''.$_REQUEST['cp_id'].'\'');
    $association_query_grade=DBQuery('Select * from gradebook_grades where student_id=\''.UserStudentId() .'\' and course_period_id=\''.$_REQUEST['cp_id'].'\' '); 
    $association_query_attendance=DBQuery('Select * from attendance_period where student_id=\''.UserStudentId() .'\' and course_period_id=\''.$_REQUEST['cp_id'].'\' '); 
    $schedule_data=  DBGet(DBQuery('Select * from schedule where student_id=\''.UserStudentId() .'\' and course_period_id=\''.$_REQUEST['cp_id'].'\' and syear ='.  UserSyear().' ')); 
    
    if(mysql_num_rows($association_query_grade)>0 || mysql_num_rows($association_query_attendance)>0 || mysql_num_rows($association_query_reportcard)>0)
    {
    UnableDeletePrompt('Cannot delete because students attendance are already taken.');  
   
    unset($_REQUEST['del']);
    unset($_REQUEST['c_id']);
  
    }
    elseif (mysql_num_rows($association_query_grade)>0) 
    {
        UnableDeletePrompt('Cannot delete because assignments grading are already given.');  

    unset($_REQUEST['del']);
    unset($_REQUEST['c_id']);
    }
    elseif (mysql_num_rows($association_query_reportcard)>0) 
    {
        UnableDeletePrompt('Cannot delete because final grade is already given .');  
   
    unset($_REQUEST['del']);
    unset($_REQUEST['c_id']);
    }
    else 
    {   
        
        if(DeletePromptMod('schedule'))
        {
        $schedule_fetch= DBGet(DBQuery('SELECT DROPPED FROM schedule WHERE ID=\''.$_REQUEST['schedule_id'].'\''));
        $schedule_status=$schedule_fetch[1]['DROPPED'];
             $seat_query=DBQuery('SELECT FILLED_SEATS FROM course_periods WHERE COURSE_ID=\''.$_REQUEST['c_id'].'\' AND COURSE_PERIOD_ID=\''.$_REQUEST['cp_id'].'\' ');
             $seat_fetch=DBGet($seat_query);
        if($schedule_status=='Y')
        {
           $seat_fill=$seat_fetch[1]['FILLED_SEATS']; 
        }
        if($schedule_status=='N') 
        {
             $seat_fill=$seat_fetch[1]['FILLED_SEATS']-1;
        }
        DBQuery('Delete from schedule where student_id=\''.UserStudentId() .'\' and course_period_id=\''.$_REQUEST['cp_id'].'\' and course_id=\''.$_REQUEST['c_id'].'\' and id=\''.$_REQUEST['schedule_id'].'\'');
             DBQuery('Update course_periods set filled_seats=\''.$seat_fill.'\' where course_id=\''.$_REQUEST['c_id'].'\' and course_period_id=\''.$_REQUEST['cp_id'].'\' ');
        unset($_REQUEST['del']);
        unset($_REQUEST['c_id']);
        unset($_REQUEST['cp_id']);

        echo "<script>window.location.href='Modules.php?modname=scheduling/Schedule.php'</script>";
        }
        unset($_REQUEST['del']);
        unset($_REQUEST['c_id']);

         
    }
 

}

else
{
  
if(isset($_REQUEST['student_id']) )
{
	$RET = DBGet(DBQuery('SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX,SCHOOL_ID FROM students,student_enrollment WHERE students.STUDENT_ID=\''.$_REQUEST['student_id'].'\' AND student_enrollment.STUDENT_ID = students.STUDENT_ID '));
	
        $count_student_RET[1]['NUM']=$_SESSION['count_stu'];
        if($count_student_RET[1]['NUM']>1){
	DrawHeaderHome( 'Selected Student:'.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Deselect</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname=students/Student.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
	




        }else if($count_student_RET[1]['NUM']==1){
        DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Deselect</font></A>) ');
        }

	
}
####################


if($_REQUEST['month_date'] && $_REQUEST['day_date'] && $_REQUEST['year_date'])
	while(!VerifyDate($date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date']))
		$_REQUEST['day_date']--;
else
{
	$min_date = DBGet(DBQuery('SELECT min(SCHOOL_DATE) AS MIN_DATE FROM attendance_calendar WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));
	if($min_date[1]['MIN_DATE'] && DBDate('postgres')<$min_date[1]['MIN_DATE'])
	{
		$date = $min_date[1]['MIN_DATE'];
		$_REQUEST['day_date'] = date('d',strtotime($date));
		$_REQUEST['month_date'] = strtoupper(date('m',strtotime($date)));
		$_REQUEST['year_date'] = date('Y',strtotime($date));
                 $first_visit='yes';
	}
	else
	{
		$_REQUEST['day_date'] = date('d');
//		
                $_REQUEST['month_date'] = date('m');
		$_REQUEST['year_date'] = date('Y');
//		
                $date = $_REQUEST['year_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['day_date'];
                $first_visit='yes';
	}
}

if($_REQUEST['month_schedule'] && ($_POST['month_schedule']||$_REQUEST['ajax']))
{
	foreach($_REQUEST['month_schedule'] as $id=>$start_dates)
	foreach($start_dates as $start_date=>$columns)
	{
		foreach($columns as $column=>$value)
		{
			$_REQUEST['schedule'][$id][$start_date][$column] = $_REQUEST['day_schedule'][$id][$start_date][$column].'-'.$value.'-'.$_REQUEST['year_schedule'][$id][$start_date][$column];
			if($_REQUEST['schedule'][$id][$start_date][$column]=='--')
				$_REQUEST['schedule'][$id][$start_date][$column] = '';
		}
	}
	unset($_REQUEST['month_schedule']);
	unset($_REQUEST['day_schedule']);
	unset($_REQUEST['year_schedule']);
	unset($_SESSION['_REQUEST_vars']['month_schedule']);
	unset($_SESSION['_REQUEST_vars']['day_schedule']);
	unset($_SESSION['_REQUEST_vars']['year_schedule']);
	$_POST['schedule'] = $_REQUEST['schedule'];
}

if($_REQUEST['schedule'] && ($_POST['schedule'] || $_REQUEST['ajax']))
{       

     $error_flag=0;
     $count_start_date=0;
     $count_update_data=0;
	foreach($_REQUEST['schedule'] as $course_period_id=>$start_dates)
       {
           
	foreach($start_dates as $start_date=>$columns)
	{ 
                    $count_start_date++;
                    $flag=0;
                    
                        $schdl_is_exist_qry=DBGet(DBQuery('SELECT COUNT(*) AS ROWS FROM schedule WHERE STUDENT_ID=\''.UserStudentID().'\' AND COURSE_PERIOD_ID=\''.$course_period_id.'\''));
       
                        if($schdl_is_exist_qry[1]['ROWS']>1)
                        {
                            $schdl_drop_status=DBGet(DBQuery('SELECT DROPPED FROM schedule WHERE ID=\''.$columns['SCHEDULE_ID'].'\''));
                            if($schdl_drop_status[1]['DROPPED']=='N')
                            {
                                $drooped_end_date=DBGet(DBQuery('SELECT END_DATE FROM schedule WHERE STUDENT_ID=\''.UserStudentID().'\' AND COURSE_PERIOD_ID=\''.$course_period_id.'\' AND DROPPED=\'Y\' ORDER BY END_DATE'));
                                if((date('Y-m-d',strtotime($columns['START_DATE']))>$drooped_end_date[1]['END_DATE']) || $columns['START_DATE']=='')
                                {
                                    $flag=1;
                                }                   
                                
                            }
                            elseif($schdl_drop_status[1]['DROPPED']=='Y')
                            {
                                $schdl_start_date=DBGet(DBQuery('SELECT MAX(START_DATE) AS GREATER FROM schedule WHERE STUDENT_ID=\''.UserStudentID().'\' AND COURSE_PERIOD_ID=\''.$course_period_id.'\''));
                                
                                  if($start_date==$schdl_start_date[1]['GREATER'])
                                {
                                    
                                    $flag=1;
                                }
                                else 
                                {
                                    $_SESSION['schedule_error']=2;
                                    
                                }
                            }
                                
                        }
                        else
                        {
                            $flag=1;
                        }
//                        
//                    
                        if($flag==0 && ($columns['START_DATE']!='' || $columns['END_DATE']!=''))
                        {
                            $start_date_msg="dropped_schdl_error";
                        }
                        if($flag==1)
                        {
                            $count_update_data++;
                            $error_flag=1;
       	$sql = 'UPDATE schedule SET ';
                foreach($columns as $column=>$value)
		{   
                            if($column=='SCHEDULE_ID')
                                continue;
                            $edt_qry=DBGet(DBQuery('SELECT START_DATE,END_DATE FROM schedule WHERE STUDENT_ID=\''.UserStudentID().'\' AND COURSE_PERIOD_ID=\''.$course_period_id.'\' AND START_DATE=\''.date('Y-m-d',strtotime($start_date)).'\''));
                
                    $edt_fetch_start_t=strtotime($edt_qry[1]['START_DATE']);
                 $edt_fetch_end_t=strtotime($edt_qry[1]['END_DATE']);
                  
                    
               $value= paramlib_validation($column,$value);
              
               $end_date_time=strtotime($value);
              
                     
                  $new_st_date=date('Y-m-d',strtotime($start_date));
           $new_st_date_time=strtotime($new_st_date);
                            if($columns['END_DATE']!='' && $columns['START_DATE']!='')
                {

                                if($columns['END_DATE']<=$columns['START_DATE'])
                                {

                                    $end_date_msg="end";
                                }
                                elseif($column=='START_DATE')
                                {  
                                    $value= paramlib_validation($column,$value);
                                    $start_date_time=strtotime($value);
                                    $enroll_date_sql=  DBGet(DBQuery('SELECT START_DATE FROM student_enrollment WHERE SYEAR = \''.UserSyear().'\' AND STUDENT_ID = \''.UserStudentID().'\''));

                                    if(strtotime($enroll_date_sql[1]['START_DATE']) <= strtotime($value))
                                    {  
                                        if($start_date_time < $edt_fetch_end_t || $edt_fetch_end_t=='')
                                        {  
                                        $sql .= $column.'=\''.str_replace("\'","''",$value).'\',';
                                        $tot_cp.=$course_period_id.',';
                                        }
                                        else
                                        {
                                            
                                            $start_date_msg="start";
                                   
                                        }
                                    }
                                    else
                                    {
                                    $start_date_msg="enroll";

                                    }

                                }
                                else 
                                {
                                    if($start_date_msg!="enroll" && $start_date_msg!="strat")
                                    $sql .= $column.'=\''.str_replace("\'","''",$value).'\',';
                                }
                            }
                        else
                         {
                            
                        if($column=='END_DATE')
                        {
$prev_scheduler_lock = DBGet(DBQuery('SELECT SCHEDULER_LOCK FROM schedule WHERE STUDENT_ID=\''.UserStudentID().'\' AND COURSE_PERIOD_ID=\''.$course_period_id.'\' AND START_DATE=\''.date('Y-m-d',strtotime($start_date)).'\''))   ;

		 if($column==END_DATE && str_replace("\'","''",$value)=='')
                      $sql .= $column."=NULL,";
                else if($column==END_DATE && str_replace("\'","''",$value)!='' && $prev_scheduler_lock[1]['SCHEDULER_LOCK']!='Y' )
                    {
                    $mother_date = $value;
                    $year = substr($mother_date, 7, 4);
                    $day = substr($mother_date, 0, 2);
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

                         $select_date = $year.'-'.$month.'-'.$day;
                         
                         
                       $end_date_sql=  DBGet(DBQuery('SELECT MAX(SCHOOL_DATE) AS SCHOOL_DATE FROM attendance_period WHERE COURSE_PERIOD_ID = \''.$course_period_id.'\' AND STUDENT_ID = \''.UserStudentID().'\''));
//                   
                        
                            if(strtotime($select_date)>=$edt_fetch_start_t || $new_st_date_time<=strtotime($select_date))
                            {
                                
                                if(strtotime($end_date_sql[1]['SCHOOL_DATE'])<= strtotime($select_date))
                                {
//                                    
                                        $sql .= $column.'=\''.str_replace("\'","''",$value).'\',';
                                        if($columns['END_DATE'])
                                            DBQuery('DELETE FROM attendance_period WHERE STUDENT_ID=\''.UserStudentID().'\' AND COURSE_PERIOD_ID=\''.$course_period_id.'\' AND SCHOOL_DATE > \''.$columns['END_DATE'].'\'');
//                                   
           
                    }
                     else 
                    {    
                                    $end_date_msg="attn";
                                    $mother_date = $end_date_sql[1]['SCHOOL_DATE'];
                                    $year = substr($mother_date, 0, 4);
                                    $day = substr($mother_date, 8, 2);
                                    $month = substr($mother_date, 5, 2);
                         
                                    $select_date1 = $month.'-'.$day.'-'.$year;
                                    $_SESSION['last_attendance']=$select_date1;
                    }
                            }
                            else
                            {    
                                $end_date_msg="end";
                 
                }
                }
                 else
                     $sch_lock_msg="end";
                }

                else
                {  
                    if($column=='START_DATE')
                    {  
                        $value= paramlib_validation($column,$value);
                        $start_date_time=strtotime($value);
                        $enroll_date_sql=  DBGet(DBQuery('SELECT START_DATE FROM student_enrollment WHERE SYEAR = \''.UserSyear().'\' AND STUDENT_ID = \''.UserStudentID().'\''));
                        
                        if(strtotime($enroll_date_sql[1]['START_DATE']) <= strtotime($value))
                        {  
                            if($start_date_time < $edt_fetch_end_t || $edt_fetch_end_t=='')
                            {  
                            $sql .= $column.'=\''.str_replace("\'","''",$value).'\',';
                            $tot_cp.=$course_period_id.',';
                        }
                        else
                        {
                        $start_date_msg="start";
                      
                        }
                        }
                        else
                        {
                        $start_date_msg="enroll";
                        }
                        
                    }
                     else
                    {
                        $sql .= $column.'=\''.str_replace("\'","''",$value).'\',';
                    }
             }   
             
                }
		
                        }

                if($columns['START_DATE'] || $columns['END_DATE'] || $columns['MARKING_PERIOD_ID'])
                {
                 $sql.= MODIFIED_DATE."='".DBDate()."',";
                $sql.= MODIFIED_BY."='".User('STAFF_ID')."',";
                }
       
         $sql = substr($sql,0,-1) . ' WHERE STUDENT_ID=\''.UserStudentID().'\' AND COURSE_PERIOD_ID=\''.$course_period_id.'\' AND START_DATE=\''.date('Y-m-d',strtotime($start_date)).'\'';
          DBQuery($sql);
          ########################### For Missing Attendance ###########################
         
                
	################################# Start of Filled seats update code ###############################

			$start_end_RET = DBGet(DBQuery('SELECT START_DATE,END_DATE FROM schedule WHERE STUDENT_ID=\''.UserStudentID().'\' AND END_DATE<=CURRENT_DATE AND COURSE_PERIOD_ID=\''.$course_period_id.'\''));

			if(count($start_end_RET))
			{
				$end_null_RET = DBGet(DBQuery('SELECT START_DATE,END_DATE FROM schedule WHERE STUDENT_ID=\''.UserStudentID().'\' AND COURSE_PERIOD_ID=\''.$course_period_id.'\' AND END_DATE IS NULL'));
					if(!count($end_null_RET)){
					
					DBQuery('CALL SEAT_COUNT()');
					}
			}

	################################# End of Filled seats update code ###############################

			
	}

        }
            
        }
        
         if($tot_cp!='')
          {
             $tot_cp = substr($tot_cp,0,-1);
             DBQuery('DELETE FROM missing_attendance WHERE COURSE_PERIOD_ID IN ('.$tot_cp.')');
             $all_cps=explode(',',$tot_cp);
             foreach($all_cps as $cps)
             {
                 $schedule_type_check1=DBGet(DBQuery("SELECT SCHEDULE_TYPE FROM course_periods WHERE COURSE_PERIOD_ID='".$cps."'"));
              
                 if($schedule_type_check1[1]['SCHEDULE_TYPE']=='FIXED')
                 {
                  DBQuery('INSERT INTO missing_attendance(SCHOOL_ID,SYEAR,SCHOOL_DATE,COURSE_PERIOD_ID,PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID) SELECT s.ID AS SCHOOL_ID,acc.SYEAR,acc.SCHOOL_DATE,cp.COURSE_PERIOD_ID,cpv.PERIOD_ID, IF(tra.course_period_id=cp.course_period_id AND acc.school_date<tra.assign_date =true,tra.pre_teacher_id,cp.teacher_id) AS TEACHER_ID,cp.SECONDARY_TEACHER_ID FROM attendance_calendar acc INNER JOIN marking_periods mp ON mp.SYEAR=acc.SYEAR AND mp.SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN mp.START_DATE AND mp.END_DATE INNER JOIN course_periods cp ON cp.MARKING_PERIOD_ID=mp.MARKING_PERIOD_ID AND cp.CALENDAR_ID=acc.CALENDAR_ID INNER JOIN course_period_var cpv ON cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID  AND cpv.DOES_ATTENDANCE=\'Y\'  LEFT JOIN teacher_reassignment tra ON (cp.course_period_id=tra.course_period_id) INNER JOIN school_periods sp ON sp.SYEAR=acc.SYEAR AND sp.SCHOOL_ID=acc.SCHOOL_ID AND sp.PERIOD_ID=cpv.PERIOD_ID AND (sp.BLOCK IS NULL AND position(substring(\'UMTWHFS\' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cpv.DAYS)>0 OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK) INNER JOIN schools s ON s.ID=acc.SCHOOL_ID INNER JOIN schedule sch ON sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID  AND sch.START_DATE<=acc.SCHOOL_DATE AND (sch.END_DATE IS NULL OR sch.END_DATE>=acc.SCHOOL_DATE ) AND cp.COURSE_PERIOD_ID IN ('.$tot_cp.') LEFT JOIN attendance_completed ac ON ac.SCHOOL_DATE=acc.SCHOOL_DATE AND IF(tra.course_period_id=cp.course_period_id AND acc.school_date<=tra.assign_date =true,ac.staff_id=tra.pre_teacher_id,ac.staff_id=cp.teacher_id) AND ac.PERIOD_ID=sp.PERIOD_ID WHERE acc.SYEAR=\''.UserSyear().'\'  AND acc.SCHOOL_ID=\''.UserSchool().'\' AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) AND acc.SCHOOL_DATE<=\''.date('Y-m-d').'\'  AND ac.STAFF_ID IS NULL GROUP BY s.TITLE,acc.SCHOOL_DATE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.TEACHER_ID');   
          }
                if($schedule_type_check1[1]['SCHEDULE_TYPE']=='VARIABLE')
                {
                    $day1=DBGet(DBQuery("SELECT DAYS,PERIOD_ID FROM course_period_var WHERE COURSE_PERIOD_ID='".$cps."' AND DOES_ATTENDANCE='Y'"));
                   foreach($day1 as $index=>$day)
                    {
                       if($day['DAYS']=='M')
                           $day2[$day['PERIOD_ID']]='Monday';
                       if($day['DAYS']=='T')
                           $day2[$day['PERIOD_ID']]='Tuesday';
                       if($day['DAYS']=='W')
                           $day2[$day['PERIOD_ID']]='Wednesday';
                       if($day['DAYS']=='H')
                           $day2[$day['PERIOD_ID']]='Thursday';
                       if($day['DAYS']=='F')
                           $day2[$day['PERIOD_ID']]='Friday';
                       if($day['DAYS']=='S')
                           $day2[$day['PERIOD_ID']]='Saturday';
                       if($day['DAYS']=='U')
                           $day2[$day['PERIOD_ID']]='Sunday';

                    $days_check=DBGet(DBQuery("SELECT sch.START_DATE FROM schedule sch,course_periods cp WHERE cp.COURSE_PERIOD_ID='".$cps."' AND cp.COURSE_PERIOD_ID=sch.COURSE_PERIOD_ID AND cp.COURSE_ID=sch.COURSE_ID AND sch.SCHOOL_ID='".UserSchool()."' AND sch.SYEAR='".UserSyear()."' AND sch.STUDENT_ID='".UserStudentID()."'"));
                   foreach($days_check as $index=>$dates)
                   {    
                       $day_found_count=0;
                       $sec=0;
                       $total_diff_days=(strtotime(date('Y-m-d'))-strtotime($dates['START_DATE']))/86400;
                       for($i=0;$i<$total_diff_days;$i++)
                       {
                           
                           
                       $day_found=date('l',strtotime($dates['START_DATE'])+$sec);

                        
                        if($day_found==$day2[$day['PERIOD_ID']])
                        {
                            
                           $dates_all=date('Y-m-d',strtotime($dates['START_DATE'])+$sec);
                           $calendar_id=DBGet(DBQuery("SELECT CALENDAR_ID FROM course_periods WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".  UserSchool()."' AND COURSE_PERIOD_ID='".$cps."'"));
                           $calendar_id=$calendar_id[1]['CALENDAR_ID']; 
                           $attendance_day_date=DBGet(DBQuery("SELECT COUNT(*) as PRESENT FROM attendance_calendar WHERE SYEAR='".UserSyear()."' AND SCHOOL_DATE='". $dates_all."' AND SCHOOL_ID='".  UserSchool()."' AND CALENDAR_ID='".$calendar_id."'"));
                           if($attendance_day_date[1]['PRESENT']!=0)
                            {
                           $day_found_count++;
                            $teach_id=DBGet(DBquery("SELECT TEACHER_ID FROM teacher_reassignment WHERE course_period_id='".$cps."' AND ASSIGN_DATE<='".$dates_all."'"));
                           if($teach_id[1]['TEACHER_ID']!='')
                           {
                               $teachers_id=$teach_id[1]['TEACHER_ID'];
                           }
                            else
                             {
                                $teachers_id=DBGet(DBQuery("SELECT TEACHER_ID,SECONDARY_TEACHER_ID FROM course_periods WHERE COURSE_PERIOD_ID='".$cps."'"));
                                if($teachers_id[1]['SECONDARY_TEACHER_ID']!='')
                                 $secondary_teachers_id=$teachers_id[1]['SECONDARY_TEACHER_ID'];
                                else
                                 $secondary_teachers_id='';
                                $teachers_id=$teachers_id[1]['TEACHER_ID'];
                             }

                            $attendance_completed_check=DBGet(DBQuery("SELECT COUNT(*) as COMPLETED FROM attendance_completed WHERE PERIOD_ID='".$day['PERIOD_ID']."' AND COURSE_PERIOD_ID='".$cps."'
                                                                    AND SCHOOL_DATE='".$dates_all."'"));
                         if($attendance_completed_check[1]['COMPLETED']==0)
                            {
                            if($secondary_teachers_id!='')
                              DBquery("INSERT INTO missing_attendance (SCHOOL_ID,SYEAR,SCHOOL_DATE,COURSE_PERIOD_ID,PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID) 
                                       VALUES ('".UserSchool()."','".UserSyear()."','".$dates_all."','".$cps."','".$day['PERIOD_ID']."','".$teachers_id."','".$secondary_teachers_id."')");
                             else
                              DBquery("INSERT INTO missing_attendance (SCHOOL_ID,SYEAR,SCHOOL_DATE,COURSE_PERIOD_ID,PERIOD_ID,TEACHER_ID) 
                                       VALUES ('".UserSchool()."','".UserSyear()."','".$dates_all."','".$cps."','".$day['PERIOD_ID']."','".$teachers_id."')");
                            }
                        }
                        }
                           $sec=$sec+86400;
                       }

                   }
                  }
                }
                
                if($schedule_type_check1[1]['SCHEDULE_TYPE']=='BLOCKED')
                {
                
                     $block_schedule_vals=DBGet(DBQuery("SELECT COURSE_PERIOD_DATE,PERIOD_ID FROM course_period_var WHERE COURSE_PERIOD_ID='".$cps."' AND DOES_ATTENDANCE='Y'"));
//                    
                     foreach($block_schedule_vals as $index=>$vals)
                     {
                       $calendar_id=DBGet(DBQuery("SELECT CALENDAR_ID FROM course_periods WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".  UserSchool()."' AND COURSE_PERIOD_ID='".$cps."'"));
                           $calendar_id=$calendar_id[1]['CALENDAR_ID']; 
                       $attendance_day_date=DBGet(DBQuery("SELECT COUNT(*) as PRESENT FROM attendance_calendar WHERE SYEAR='".UserSyear()."' AND SCHOOL_DATE='".$vals['COURSE_PERIOD_DATE']."' AND SCHOOL_ID='".  UserSchool()."' AND CALENDAR_ID='".$calendar_id."'"));
                        
                       if($attendance_day_date[1]['PRESENT']!=0)  
                        {
                        $days_check=DBGet(DBQuery("SELECT sch.START_DATE FROM schedule sch,course_periods cp WHERE cp.COURSE_PERIOD_ID='".$cps."' AND cp.COURSE_PERIOD_ID=sch.COURSE_PERIOD_ID AND cp.COURSE_ID=sch.COURSE_ID AND sch.SCHOOL_ID='".UserSchool()."' AND sch.SYEAR='".UserSyear()."' AND sch.STUDENT_ID='".UserStudentID()."' AND sch.START_DATE<='".$vals['COURSE_PERIOD_DATE']."'"));
                        if($days_check[1]['START_DATE']!='')
                        {
                            $teach_id=DBGet(DBquery("SELECT TEACHER_ID FROM teacher_reassignment WHERE course_period_id='".$cps."' AND ASSIGN_DATE<='".$vals['COURSE_PERIOD_DATE']."'"));
                            if($teach_id[1]['TEACHER_ID']!='')
                            {
                                $teachers_id=$teach_id[1]['TEACHER_ID'];
                            }
                            else
                            {
                            $teachers_id=DBGet(DBQuery("SELECT TEACHER_ID,SECONDARY_TEACHER_ID FROM course_periods WHERE COURSE_PERIOD_ID='".$cps."'"));
                            if($teachers_id[1]['SECONDARY_TEACHER_ID']!='')
                             $secondary_teachers_id=$teachers_id[1]['SECONDARY_TEACHER_ID'];
                            else
                             $secondary_teachers_id='';
                            $teachers_id=$teachers_id[1]['TEACHER_ID'];
                             }  
                             $attendance_completed_check=DBGet(DBQuery("SELECT COUNT(*) as COMPLETED FROM attendance_completed WHERE PERIOD_ID='".$vals['PERIOD_ID']."' AND COURSE_PERIOD_ID='".$cps."'
                                                                    AND SCHOOL_DATE='".$vals['COURSE_PERIOD_DATE']."'"));
                         if($attendance_completed_check[1]['COMPLETED']==0)
                            {
                          
                        if($secondary_teachers_id!='')
                        DBquery("INSERT INTO missing_attendance (SCHOOL_ID,SYEAR,SCHOOL_DATE,COURSE_PERIOD_ID,PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID) 
                               VALUES ('".UserSchool()."','".UserSyear()."','".$vals['COURSE_PERIOD_DATE']."','".$cps."','".$vals['PERIOD_ID']."','".$teachers_id."','".$secondary_teachers_id."')");
                        else
                        DBquery("INSERT INTO missing_attendance (SCHOOL_ID,SYEAR,SCHOOL_DATE,COURSE_PERIOD_ID,PERIOD_ID,TEACHER_ID) 
                               VALUES ('".UserSchool()."','".UserSyear()."','".$vals['COURSE_PERIOD_DATE']."','".$cps."','".$vals['PERIOD_ID']."','".$teachers_id."')");
                            }
                        }
                     }
                     }
                }
             }
             
          }
	DBQuery("CALL SEAT_FILL()");
	unset($_SESSION['_REQUEST_vars']['schedule']);
	unset($_REQUEST['schedule']);

}
if(UserStudentID() && $_REQUEST['modfunc']!='choose_course' && $_REQUEST['modfunc']!='more_info')
{
    
	echo "<FORM name=modify id=modify action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=modify METHOD=POST>";

	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['include_inactive']);

    ##################################################################

    $years_RET = DBGet(DBQuery('SELECT MARKING_PERIOD_ID,TITLE,NULL AS SEMESTER_ID FROM school_years WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool()."'"));

  $semesters_RET = DBGet(DBQuery('SELECT MARKING_PERIOD_ID,TITLE,NULL AS SEMESTER_ID FROM school_semesters WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER'));

  $uarters_RET = DBGet(DBQuery('SELECT MARKING_PERIOD_ID,TITLE,SEMESTER_ID FROM school_quarters WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER'));

  
$mp_RET = DBGet(DBQuery('SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,1 AS TBL FROM school_years WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' UNION SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,2 AS TBL FROM school_semesters WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' UNION SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,3 AS TBL FROM school_quarters WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' ORDER BY TBL,SORT_ORDER'));


  
  $mp = CreateSelect($mp_RET, 'marking_period_id', 'Modules.php?modname='.$_REQUEST['modname'].'&marking_period_id=', $_REQUEST['marking_period_id']);



    ###################################################################3



	DrawHeaderHome(PrepareDateSchedule($date,'_date',false,array('submit'=>true)).' <INPUT type=checkbox name=include_inactive value=Y'.($_REQUEST['include_inactive']=='Y'?" CHECKED onclick='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST)."&include_inactive=\";'":" onclick='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST)."&include_inactive=Y\";'").'>Include Inactive Courses : &nbsp;  Marking Period :  '.$mp.' &nbsp;',SubmitButton('Save','','class=btn_medium onclick=\'formload_ajax("modify");\''));

	$fy_id = DBGet(DBQuery('SELECT MARKING_PERIOD_ID FROM school_years WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));
	$fy_id = $fy_id[1]['MARKING_PERIOD_ID'];
        
	$sql = 'SELECT          
                              s.COURSE_ID as ACTION,
				s.COURSE_ID,s.COURSE_PERIOD_ID,s.ID AS SCHEDULE_ID,
				s.MARKING_PERIOD_ID,s.START_DATE,s.END_DATE,s.MODIFIED_DATE,s.MODIFIED_BY,
				UNIX_TIMESTAMP(s.START_DATE) AS START_EPOCH,UNIX_TIMESTAMP(s.END_DATE) AS END_EPOCH,sp.PERIOD_ID,
				cpv.PERIOD_ID,cp.MARKING_PERIOD_ID as COURSE_MARKING_PERIOD_ID,cp.MP,sp.SORT_ORDER,
				c.TITLE,cp.COURSE_PERIOD_ID AS PERIOD_PULLDOWN,
				s.STUDENT_ID,r.TITLE AS ROOM,(SELECT GROUP_CONCAT(cpv.DAYS) FROM course_period_var cpv WHERE cpv.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID) as DAYS,SCHEDULER_LOCK,CONCAT(st.LAST_NAME, \''.' '.'\' ,st.FIRST_NAME) AS MODIFIED_NAME
			FROM courses c,course_periods cp,course_period_var cpv,school_periods sp,rooms r,schedule s
                        LEFT JOIN staff st ON s.MODIFIED_BY = st.STAFF_ID
			WHERE
			 s.COURSE_ID = c.COURSE_ID AND s.COURSE_ID = cp.COURSE_ID
				AND s.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID
                                AND cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID
                                 AND r.ROOM_ID=cpv.ROOM_ID
				AND s.SCHOOL_ID = sp.SCHOOL_ID AND s.SYEAR = c.SYEAR AND sp.PERIOD_ID = cpv.PERIOD_ID
				AND s.STUDENT_ID=\''.UserStudentID().'\'
				AND s.SYEAR=\''.UserSyear().'\'';
                            $sql.=' AND s.SCHOOL_ID = \''.UserSchool().'\'';
                             
                            if($_REQUEST['include_inactive']!='Y'){
                                $sql .= ' AND (\''.date('Y-m-d',strtotime($date)).'\' BETWEEN s.START_DATE AND s.END_DATE OR (s.END_DATE IS NULL AND s.START_DATE<=\''.date('Y-m-d',strtotime($date)).'\')) ';
                            }

                     if(clean_param($_REQUEST['marking_period_id'],PARAM_INT)){
                            $mp_id=$_REQUEST['marking_period_id'];
                     }

                     if(!isset($_REQUEST['marking_period_id'])){
                         $mp_id=UserMP();
                     }
                    $sql .=' AND (s.MARKING_PERIOD_ID IN ('.GetAllMP(GetMPTable(GetMP($mp_id,'TABLE')),$mp_id).')  OR s.MARKING_PERIOD_ID IS NULL)'; 
                    $sql .= ' GROUP BY cp.COURSE_PERIOD_ID ORDER BY sp.SORT_ORDER,s.MARKING_PERIOD_ID';


	$QI = DBQuery($sql);
	$schedule_RET = DBGet($QI,array('ACTION'=>'_makeAction','TITLE'=>'_makeTitle','PERIOD_PULLDOWN'=>'_makePeriodSelect','DAYS'=>'_makeDays','COURSE_MARKING_PERIOD_ID'=>'_makeMPSelect','SCHEDULER_LOCK'=>'_makeLock','START_DATE'=>'_makeDate','END_DATE'=>'_makeDate','SCHEDULE_ID'=>'_makeInfo'));
        
    $link['add']['link'] = "# onclick='window.open(\"ForWindow.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=choose_course&ses=1\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");' ";
	$link['add']['title'] = "Add a Course";
        
        $columns = array('ACTION'=>'Action','TITLE'=>'Course ','PERIOD_PULLDOWN'=>'Period - Teacher','ROOM'=>'Room','DAYS'=>'Days of Week','COURSE_MARKING_PERIOD_ID'=>'Term','SCHEDULER_LOCK'=>'<IMG SRC=assets/locked.gif border=0>','START_DATE'=>'Enrolled','END_DATE'=>'Dropped','SCHEDULE_ID'=>'More info');

        $days_RET = DBGet(DBQuery('SELECT DISTINCT DAYS FROM course_period_var'));
	if(count($days_RET)==1)
		unset($columns['DAYS']);
	if($_REQUEST['_openSIS_PDF'])
		unset($columns['SCHEDULER_LOCK']);
        if($start_date_msg=="start")
        {
             echo "<b style='color:red'>Enrolled Date Cannot Be After Dropped Date</b>";
        
             unset($start_date_msg);
        }
        if($start_date_msg=="enroll")
        {
             echo "<b style='color:red'>Course Enrolled Date Cannot Be Before Student's School Start Date</b>";

             unset($start_date_msg);
        }
        if($start_date_msg=="prev_schdl_error")
        {
             echo "<b style='color:red'>Course Enrolled Date Cannot Be Before Dropdate Of Previous Schedule</b>";

             unset($start_date_msg);
        }
        if($start_date_msg=="dropped_schdl_error")
        {

             echo "<b style='color:red'>You cannot modify the schedule enrolled date, as it's clashing with other dropped course</b>";

             unset($start_date_msg);
        }
        if($start_date_msg=="error")
        {
             echo "<b style='color:red'>Course Enrolled Date Cannot Be Before Dropdate Of Previous Schedule</b>";

             unset($start_date_msg);
        }
        if($end_date_msg=="end")
        {
            echo "<b style='color:red'>Please enter proper dropped date. Dropped date must be greater than start date.</b>";
        
            unset($end_date_msg);
        }

        if($sch_lock_msg=="end")
        {
            echo "<b style='color:red'>This Schedule is locked,dropped date can not be changed.</b>";
        
            unset($sch_lock_msg);
        }
        if($end_date_msg=="attn" && strtotime($end_d)<strtotime($_SESSION['last_attendance']))
        {
          
            echo "<b style='color:red'>Course cannot be dropped because student has got attendance till ".$_SESSION['last_attendance'].".</b>";
        
            unset($end_date_msg);
        }
	VerifySchedule($schedule_RET);
	echo '<div style="width:820px; overflow:auto; overflow-x:scroll; padding-bottom:8px;">';
	ListOutputSchedule($schedule_RET,$columns,'Course','Courses',$link);
	echo '</div>';

	if(!$schedule_RET)
	echo '';
	else
	{
	    DrawHeader( "<table><tr><td>&nbsp;&nbsp;</td><td>". (ProgramLinkforExport('Scheduling/PrintSchedules.php','<img src=assets/print.png>','&modfunc=save&st_arr[]='.UserStudentID().'&mp_id='.$mp_id.'&include_inactive='.$_REQUEST['include_inactive'].'&_openSIS_PDF=true target=_blank'))."</td><td>". (ProgramLinkforExport('scheduling/PrintSchedules.php','Print Schedule','&modfunc=save&st_arr[]='.UserStudentID().'&mp_id='.$mp_id.'&include_inactive='.$_REQUEST['include_inactive'].'&_openSIS_PDF=true target=_blank'))."</td></tr></table>");
	    echo '<BR><CENTER>'.SubmitButton('Save','','class=btn_medium onclick=\'formload_ajax("modify");\'').'</CENTER>';
	}

	echo '</FORM>';
	echo "<div class=break></div>";

	if(AllowEdit())
	{
		unset($_REQUEST);
		$_REQUEST['modname'] = 'scheduling/Schedule.php';
		$_REQUEST['search_modfunc'] = 'list';
		$extra['link']['FULL_NAME']['link'] = 'Modules.php?modname=scheduling/Requests.php';
		$extra['link']['FULL_NAME']['variables'] = array('subject_id'=>'SUBJECT_ID','course_id'=>'COURSE_ID');
		include('modules/scheduling/UnfilledRequests.php');
	}
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='choose_course')
{
    
	if(!isset($_REQUEST['confirm_cid']) || !$_REQUEST['sel_course_period'])
		include "modules/scheduling/MultiCoursesforWindow.php";
	else
	{
            foreach($_REQUEST['sel_course_period'] as $ses_cpid => $select_cpid)
            {

            DBQuery("INSERT INTO schedule (SYEAR,SCHOOL_ID,STUDENT_ID,START_DATE,MODIFIED_DATE,MODIFIED_BY,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID) values('".UserSyear()."','".UserSchool()."','".UserStudentID()."','".$date."','".$date."','".User('STAFF_ID')."','".clean_param($_SESSION['crs_id'][$ses_cpid],PARAM_INT)."','".clean_param($select_cpid,PARAM_INT)."','".clean_param($_SESSION['mp'][$ses_cpid],PARAM_ALPHA)."','".clean_param((GetCpDet($ses_cpid,'MARKING_PERIOD_ID')!=''?$_SESSION['marking_period_id'][$ses_cpid]:GetMPId('FY')),PARAM_INT)."')");
            DBQuery('UPDATE course_periods SET FILLED_SEATS=FILLED_SEATS+1 WHERE COURSE_PERIOD_ID=\''.clean_param($select_cpid,PARAM_INT).'\'');
                                          
            }
                unset($_SESSION['course_period']);
                unset($_SESSION['crs_id']);
                unset($_SESSION['marking_period_id']);
                unset($_SESSION['mp']);
                echo "<script language=javascript>opener.document.location = 'Modules.php?modname=".clean_param($_REQUEST['modname'],PARAM_NOTAGS)."&time=".time()."';window.close();</script>";

	}
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='more_info')
{
    $sql = 'SELECT
                                s.COURSE_ID,s.COURSE_PERIOD_ID,
                                s.MARKING_PERIOD_ID,s.START_DATE,s.END_DATE,s.MODIFIED_DATE,s.MODIFIED_BY,
                                UNIX_TIMESTAMP(s.START_DATE) AS START_EPOCH,UNIX_TIMESTAMP(s.END_DATE) AS END_EPOCH,sp.PERIOD_ID,
                                cpv.PERIOD_ID,cp.MARKING_PERIOD_ID as COURSE_MARKING_PERIOD_ID,cp.MP,sp.SORT_ORDER,
                                c.TITLE,cp.COURSE_PERIOD_ID AS PERIOD_PULLDOWN,
                                s.STUDENT_ID,r.TITLE AS ROOM,(SELECT GROUP_CONCAT(cpv.DAYS) FROM course_period_var cpv WHERE cpv.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID) as DAYS,SCHEDULER_LOCK,CONCAT(st.LAST_NAME, \''.' '.'\' ,st.FIRST_NAME) AS MODIFIED_NAME
                                FROM courses c,course_periods cp,course_period_var cpv,rooms r,school_periods sp,schedule s
                                LEFT JOIN staff st ON s.MODIFIED_BY = st.STAFF_ID
                                WHERE
                                s.COURSE_ID = c.COURSE_ID AND s.COURSE_ID = cp.COURSE_ID
                                AND cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID
                                 AND r.ROOM_ID=cpv.ROOM_ID
                                AND s.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID
                                AND s.SCHOOL_ID = sp.SCHOOL_ID AND s.SYEAR = c.SYEAR AND sp.PERIOD_ID = cpv.PERIOD_ID
                                AND s.ID='.$_REQUEST[id].'  GROUP BY cp.COURSE_PERIOD_ID';

                                $QI = DBQuery($sql);
                                $schedule_RET = DBGet($QI,array('TITLE'=>'_makeTitle','PERIOD_PULLDOWN'=>'_makePeriodSelect','COURSE_MARKING_PERIOD_ID'=>'_makeMP','DAYS'=>'_makeDays','SCHEDULER_LOCK'=>'_makeViewLock','START_DATE'=>'_makeViewDate','END_DATE'=>'_makeViewDate','MODIFIED_DATE'=>'_makeViewDate'));
                                $columns = array('TITLE'=>'Course ','PERIOD_PULLDOWN'=>'Period - Teacher','ROOM'=>'Room','DAYS'=>'Days of Week','COURSE_MARKING_PERIOD_ID'=>'Term','SCHEDULER_LOCK'=>'<IMG SRC=assets/locked.gif border=0>','START_DATE'=>'Enrolled','END_DATE'=>'Dropped','MODIFIED_NAME'=>'Modified By','MODIFIED_DATE'=>'Modified Date');
                                $options=array('search'=>false,'count'=>false,'save'=>false,'sort'=>false);

                                ListOutput($schedule_RET,$columns,'Course','Courses',$link,'',$options);

                                echo '<br /><div align="center"><input type="button" class=btn_medium value="Close" onclick="window.close();"></div>';
                                }
}
function _makeTitle($value,$column='')
{	global $_openSIS,$THIS_RET;
	return $value;
}
///For deleting schedules
function _makeAction($value)
{	
       global $THIS_RET;
        $i=UserStudentId();
        $rem="<a href=Modules.php?modname=scheduling/Schedule.php&student_id=$i&del=true&c_id=$value&cp_id=$THIS_RET[COURSE_PERIOD_ID]&schedule_id=$THIS_RET[SCHEDULE_ID]><img src='assets/remove_button.gif'/></a>";
	return $rem;//
}

function _makeViewLock($value,$column)
{	global $THIS_RET;

	if($value=='Y')
		$img = 'locked';
	else
		$img = 'unlocked';

	return '<IMG SRC=assets/'.$img.'.gif >';
}

function _makePeriodSelect($course_period_id,$column='')
{	global $_openSIS,$THIS_RET,$fy_id;

	$sql = 'SELECT cp.COURSE_PERIOD_ID,cp.PARENT_ID,cp.TITLE,cp.MARKING_PERIOD_ID,COALESCE(cp.TOTAL_SEATS-cp.FILLED_SEATS,0) AS AVAILABLE_SEATS FROM course_periods cp,course_period_var cpv,school_periods sp WHERE sp.PERIOD_ID=cpv.PERIOD_ID AND cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID AND cp.COURSE_ID=\''.$THIS_RET[COURSE_ID].'\' ORDER BY sp.SORT_ORDER';
	$QI = DBQuery($sql);
	$orders_RET = DBGet($QI);

	foreach($orders_RET as $value)
	{
		if($value['COURSE_PERIOD_ID']!=$value['PARENT_ID'])
		{
			$parent = DBGet(DBQuery('SELECT SHORT_NAME FROM course_periods WHERE COURSE_PERIOD_ID=\''.$value['PARENT_ID'].'\''));
			$parent = $parent[1]['SHORT_NAME'];
		}
		$periods[$value['COURSE_PERIOD_ID']] = $value['TITLE'] . (($value['MARKING_PERIOD_ID']!=$fy_id && $value['COURSE_PERIOD_ID']!=$course_period_id)?' ('.GetMP($value['MARKING_PERIOD_ID']).')':'').($value['COURSE_PERIOD_ID']!=$course_period_id?' ('.$value['AVAILABLE_SEATS'].' seats)':'').(($value['COURSE_PERIOD_ID']!=$course_period_id && $parent)?' -> '.$parent:'');
	}

	
	return SelectInput_Disonclick($course_period_id,"schedule[$THIS_RET[COURSE_PERIOD_ID]][$THIS_RET[START_DATE]][COURSE_PERIOD_ID]",'',$periods,false);
}

function _makeMPSelect($mp_id,$name='')
{	global $_openSIS,$THIS_RET,$fy_id;
    if($mp_id!='')
    {
	if(!$_openSIS['_makeMPSelect'])
	{
		$semesters_RET = DBGet(DBQuery('SELECT MARKING_PERIOD_ID,TITLE,NULL AS SEMESTER_ID FROM school_semesters WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER'));
		$quarters_RET = DBGet(DBQuery('SELECT MARKING_PERIOD_ID,TITLE,SEMESTER_ID FROM school_quarters WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER'));

		$_openSIS['_makeMPSelect'][$fy_id][1] = array('MARKING_PERIOD_ID'=>"$fy_id",'TITLE'=>'Full Year','SEMESTER_ID'=>'');
		foreach($semesters_RET as $sem)
			$_openSIS['_makeMPSelect'][$fy_id][] = $sem;
		foreach($quarters_RET as $qtr)
			$_openSIS['_makeMPSelect'][$fy_id][] = $qtr;

		$quarters_QI = DBQuery('SELECT MARKING_PERIOD_ID,TITLE,SEMESTER_ID FROM school_quarters WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER');
		$quarters_indexed_RET = DBGet($quarters_QI,array(),array('SEMESTER_ID'));

		foreach($semesters_RET as $sem)
		{
			$_openSIS['_makeMPSelect'][$sem['MARKING_PERIOD_ID']][1] = $sem;
			foreach($quarters_indexed_RET[$sem['MARKING_PERIOD_ID']] as $qtr)
				$_openSIS['_makeMPSelect'][$sem['MARKING_PERIOD_ID']][] = $qtr;
		}

		foreach($quarters_RET as $qtr)
			$_openSIS['_makeMPSelect'][$qtr['MARKING_PERIOD_ID']][] = $qtr;
	}

	foreach($_openSIS['_makeMPSelect'][$mp_id] as $value)
		$mps[$value['MARKING_PERIOD_ID']] = $value['TITLE'];

	if($THIS_RET['MARKING_PERIOD_ID']!=$mp_id)
		$mps[$THIS_RET['MARKING_PERIOD_ID']] = '* '.$mps[$THIS_RET['MARKING_PERIOD_ID']];

	return SelectInput($THIS_RET['MARKING_PERIOD_ID'],"schedule[$THIS_RET[COURSE_PERIOD_ID]][$THIS_RET[START_DATE]][MARKING_PERIOD_ID]",'',$mps,false);
    }
    else
    {
       $check_custom=DBGet(DBQuery('SELECT BEGIN_DATE,END_DATE FROM course_periods WHERE COURSE_PERIOD_ID='.$THIS_RET['COURSE_PERIOD_ID'].' AND BEGIN_DATE IS NOT NULL AND END_DATE IS NOT NULL AND BEGIN_DATE!=\'0000-00-00\' AND END_DATE!=\'0000-00-00\' '));
       if(count($check_custom)>0)
       {
           return '<div style="white-space: nowrap;">'.ProperDateAY($check_custom[1]['BEGIN_DATE']).' to '.ProperDateAY($check_custom[1]['END_DATE']).'</div>';
       }
    }
}

function _makeDate($value,$column)
{	global $THIS_RET;
       static $counter=0;
//  
	if($column=='START_DATE')
		$allow_na = false;
	else
		$allow_na = true;
     
        if($column=='END_DATE' && $THIS_RET[END_DATE]!='')
        {
            return date('M/d/Y',strtotime($value));
        }
        else
        {
         
             $counter++;
            return '<div style="white-space: nowrap;">'.DateInputAY($value,"schedule[$THIS_RET[COURSE_PERIOD_ID]][$THIS_RET[START_DATE]][$column]",$counter.$THIS_RET[COURSE_PERIOD_ID]).'</div>';
        }
}

function _makeInfo($value,$column)
{
    global $THIS_RET;
    return "<center><a href='#' onclick='window.open(\"ForWindow.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=more_info&id=$value\",\"\",\"scrollbars=yes,resizable=yes,width=900,height=200\");'><IMG SRC='assets/icon_info.png' width='20px' /></a></center>";
}

function _makeMP($value,$column)
{
    global $THIS_RET;
    if($value!='')
    return GetMP($value);
    else
    {
       $check_custom=DBGet(DBQuery('SELECT BEGIN_DATE,END_DATE FROM course_periods WHERE COURSE_PERIOD_ID='.$THIS_RET['COURSE_PERIOD_ID'].' AND BEGIN_DATE IS NOT NULL AND END_DATE IS NOT NULL AND BEGIN_DATE!=\'0000-00-00\' AND END_DATE!=\'0000-00-00\' '));
       if(count($check_custom)>0)
       {
           return '<div style="white-space: nowrap;">'.ProperDateAY($check_custom[1]['BEGIN_DATE']).' to '.ProperDateAY($check_custom[1]['END_DATE']).'</div>';
       }
    }
}

function  _makeViewDate($value,$column)
{
    if($value)
        return ProperDate($value);
    else
        return '<center>n/a</center>';
}

function _makeLock($value,$column)
{	global $THIS_RET;
       $hidd="<input type='hidden' name='schedule[$THIS_RET[COURSE_PERIOD_ID]][$THIS_RET[START_DATE]][SCHEDULE_ID]' value='".$THIS_RET[SCHEDULE_ID]."'>";

	if($value=='Y')
		$img = 'locked';
	else
		$img = 'unlocked';

	return '<IMG SRC=assets/'.$img.'.gif '.(AllowEdit()?'onclick="if(this.src.indexOf(\'assets/locked.gif\')!=-1) {this.src=\'assets/unlocked.gif\'; document.getElementById(\'lock'.$THIS_RET['COURSE_PERIOD_ID'].'-'.$THIS_RET['START_DATE'].'\').value=\'\';} else {this.src=\'assets/locked.gif\'; document.getElementById(\'lock'.$THIS_RET['COURSE_PERIOD_ID'].'-'.$THIS_RET['START_DATE'].'\').value=\'Y\';}"':'').'><INPUT type=hidden name=schedule['.$THIS_RET['COURSE_PERIOD_ID'].']['.$THIS_RET['START_DATE'].'][SCHEDULER_LOCK] id=lock'.$THIS_RET['COURSE_PERIOD_ID'].'-'.$THIS_RET['START_DATE'].' value='.$value.'>'.$hidd;
}
function _makeDays($value)
{
    $value=str_replace(',','',$value);
    for($i=0;$i<strlen($value);$i++)
    {
        $arr[]=substr($value,$i,1);
    }
    $arr=array_unique($arr);
    $arr=implode('',$arr);
    return $arr;
}
function VerifySchedule(&$schedule)
{
	$conflicts = array();

	$ij = count($schedule);
	for($i=1; $i<$ij; $i++)
		for($j=$i+1; $j<=$ij; $j++)
			if(!$conflicts[$i] || !$conflicts[$j])
				if(strpos(GetAllMP(GetMPTable(GetMP($schedule[$i]['MARKING_PERIOD_ID'],'TABLE')),$schedule[$i]['MARKING_PERIOD_ID']),"'".$schedule[$j]['MARKING_PERIOD_ID']."'")!==false
				&& (!$schedule[$i]['END_EPOCH'] || $schedule[$j]['START_EPOCH']<=$schedule[$i]['END_EPOCH']) && (!$schedule[$j]['END_EPOCH'] || $schedule[$i]['START_EPOCH']<=$schedule[$j]['END_EPOCH']))
					if($schedule[$i]['COURSE_ID']==$schedule[$j]['COURSE_ID']) //&& $schedule[$i]['COURSE_WEIGHT']==$schedule[$j]['COURSE_WEIGHT'])
						$conflicts[$i] = $conflicts[$j] = true;
					else
						if($schedule[$i]['PERIOD_ID']==$schedule[$j]['PERIOD_ID'])
							if(strlen($schedule[$i]['DAYS'])+strlen($schedule[$j]['DAYS'])>7)
								$conflicts[$i] = $conflicts[$j] = true;
							else
								foreach(veriry_str_split($schedule[$i]['DAYS']) as $k)
									if(strpos($schedule[$j]['DAYS'],$k)!==false)
									{
										$conflicts[$i] = $conflicts[$j] = true;
										break;
									}

	foreach($conflicts as $i=>$true)
		$schedule[$i]['TITLE'] = '<FONT color=red>'.$schedule[$i]['TITLE'].'</FONT>';
}

function veriry_str_split($str)
{
	$ret = array();
	$len = strlen($str);
	for($i=0;$i<$len;$i++)
		$ret [] = substr($str,$i,1);
	return $ret;
}



function CreateSelect($val, $name, $link='', $mpid)
	{
	 	
		
		if($link!='')
		$html .= "<select name=".$name." id=".$name." onChange=\"window.location='".$link."' + this.options[this.selectedIndex].value;\">";
		else
		$html .= "<select name=".$name." id=".$name." >";
		
				foreach($val as $key=>$value)
				{
					
					
					if(!isset($mpid) && (UserMP() == $value[strtoupper($name)]))
						$html .= "<option selected value=".UserMP().">".$value['TITLE']."</option>";
					else
					{
						if($value[strtoupper($name)]==$_REQUEST[$name])
							$html .= "<option selected value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";
						else
							$html .= "<option value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";
					}
					
				}



		$html .= "</select>";
		return $html;
	}


?>

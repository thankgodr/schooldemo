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
unset($_SESSION['student_id']);
$stu_sd_err_count=0;
if(isset($_SESSION['MANUAL_ERROR']))
{
    echo "<b style='color:red'>".$_SESSION['MANUAL_ERROR']."</b>";
     unset($_SESSION['MANUAL_ERROR']);
}

if($_REQUEST['modfunc']=='save')
{
    $mon_arr=array("JAN"=>"01","FEB"=>"02","MAR"=>"03","APR"=>"04","MAY"=>"05","JUN"=>"06","JUL"=>"07","AUG"=>"08","SEP"=>"09","OCT"=>"10","NOV"=>"11","DEC"=>"12");
    $_REQUEST['year']=$_REQUEST['year_start'];
    $_REQUEST['month']=$_REQUEST['month_start'];
    $_REQUEST['day']=$_REQUEST['day_start'];
    $st_dt=$_REQUEST['year'].'-'.$mon_arr[$_REQUEST['month']].'-'.$_REQUEST['day'];
    if($_REQUEST['marking_period_id']!='')
    {
        $chk_st_dt=DBGet(DBQuery('SELECT START_DATE,END_DATE FROM marking_periods WHERE MARKING_PERIOD_ID='.$_REQUEST['marking_period_id'].' AND SYEAR='.UserSyear().' AND SCHOOL_ID='.UserSchool()));
        $chk_st_dt=$chk_st_dt[1];
        if(strtotime($st_dt)<strtotime($chk_st_dt['START_DATE']) || strtotime($st_dt)>strtotime($chk_st_dt['END_DATE']))
        {
            $modname=$_REQUEST['modname'];
            unset($_REQUEST);
            $_REQUEST['modname']=$modname;
            unset($modname);
            DrawHeaderHome('<IMG SRC=assets/warning_button.gif>&nbsp;<font style="color:red"><b>Schedule start date cannot be before marking periods end date.</b></font>');
        }
    }
}

if(!$_REQUEST['modfunc'] && $_REQUEST['search_modfunc']!='list')
	unset($_SESSION['MassSchedule.php']);

if(isset($_REQUEST['per']))
	$per_status = $_REQUEST['per'];

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHA)=='seatso')
{
    $c_id=$_REQUEST['course_period_id'];
 if(isset($_REQUEST['tables']['course_periods'][$c_id]['TOTAL_SEATS']))
 {
   
    $stu_alrd_id=array();
    $n_schedule_stu=$_SESSION['NOT_SCHEDULE']; 
    $room_name=$room_av_id=array();
    $course_id=$_REQUEST['course_period_id'];
    
//    echo 'SELECT ROOM_ID FROM course_period_var WHERE COURSE_PERIOD_ID=\''.$_REQUEST['course_period_id'].'\'';
    $rooms_id=DBGet(DBQuery('SELECT ROOM_ID FROM course_period_var WHERE COURSE_PERIOD_ID=\''.$_REQUEST['course_period_id'].'\''));

for($i=1;$i<=count($rooms_id);$i++)
{
    array_push($room_av_id,$rooms_id[$i]['ROOM_ID']);
}

    $room_id=$rooms_id[1]['ROOM_ID'];
 $c_id = $_REQUEST['course_period_id'];
 
 $seat=$_REQUEST['tables']['course_periods'][$c_id]['TOTAL_SEATS'];
for($i=0;$i<count($room_av_id);$i++)
{
 $roomsv_id=DBGet(DBQuery('SELECT CAPACITY,TITLE FROM rooms WHERE ROOM_ID=\''.$room_av_id[$i].'\''));   
 if($roomsv_id[1]['CAPACITY']<$seat)
 {
     
     array_push($room_name,$roomsv_id[1]['TITLE']);
 }
}

if(count($room_name)>0)
{
$_SESSION['MANUAL_ERROR']="Unable to update because   ".implode(',',$room_name)."   capacity is lower than your requested seats.";    

//echo "<script>window.location.href='modname=scheduling/MassSchedule.php'</script>";

 echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname=scheduling/MassSchedule.php"; window.close();</script>';
}
 else {
  
     DBQuery('UPDATE course_periods SET total_seats='.$seat.' WHERE COURSE_PERIOD_ID=\''.$course_id.'\'');

    $in_data_stu_id=DBGet(DBQuery('SELECT * FROM schedule WHERE COURSE_PERIOD_ID=\''.$course_id.'\'')); 
  
for($i=1;$i<=count($in_data_stu_id);$i++)
{
    array_push($stu_alrd_id,$in_data_stu_id[$i]['STUDENT_ID']);
} 
$n_schedule_stu=$_SESSION['NOT_SCHEDULE']; 
foreach($n_schedule_stu as $k => $v)
{
    if(!in_array($v,$stu_alrd_id))
    {
      
          $sql = 'INSERT INTO schedule (SYEAR,SCHOOL_ID,STUDENT_ID,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID,START_DATE,MODIFIED_DATE,MODIFIED_BY)
	values(\''.UserSyear().'\',\''.UserSchool().'\',\''.$v.'\',\''.$in_data_stu_id[1]['COURSE_ID'].'\',\''.$in_data_stu_id[1]['COURSE_PERIOD_ID'].'\',\''.$in_data_stu_id[1]['MP'].'\',\''.clean_param($in_data_stu_id[1]['MARKING_PERIOD_ID'],PARAM_INT).'\',\''.$_SESSION['SCH']['START_DATE'].'\',\''.date('Y-m-d').'\',\''.User('STAFF_ID').'\')';
 DBQuery($sql);

}

$qr=DBGet(DBQuery('SELECT COUNT(*) AS TOTAL FROM schedule WHERE (END_DATE>\''.date('Y-m-d').'\' or END_DATE IS NULL OR END_DATE=\'0000-00-00\' ) AND COURSE_PERIOD_ID=\''.$course_id.'\'')); 


DBQuery('UPDATE course_periods SET filled_seats='.$qr[1]['TOTAL'].' WHERE COURSE_PERIOD_ID=\''.$course_id.'\'');
echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname=scheduling/MassSchedule.php"; window.close();</script>';
unset($_SESSION['NOT_SCHEDULE']);
}

}
}
else
{
 echo '<SCRIPT language=javascript>opener.document.location = "Modules.php?modname=scheduling/MassSchedule.php"; window.close();</script>';
      
}
}
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHA)=='save')
{  
	if($_SESSION['MassSchedule.php'])
	{
            
		$start_date = $_REQUEST['day'].'-'.$_REQUEST['month'].'-'.$_REQUEST['year'];
                $_SESSION['SCH']['START_DATE']=$start_date;
		if(!VerifyDate($start_date))
			BackPrompt('The date you entered is not valid');
		$course_mp = DBGet(DBQuery('SELECT MARKING_PERIOD_ID FROM course_periods WHERE COURSE_PERIOD_ID=\''.$_SESSION['MassSchedule.php']['course_period_id'].'\''));
		$course_mp = $course_mp[1]['MARKING_PERIOD_ID'];
		$course_mp_table = GetMPTable(GetMP($course_mp,'TABLE'));

		if($course_mp_table!='FY' && $course_mp!=$_REQUEST['marking_period_id'] && strpos(GetChildrenMP($course_mp_table,$course_mp),"'".$_REQUEST['marking_period_id']."'")===false)
		{
		ShowErr("You cannot schedule a student into that course during the marking period that you chose.  This course meets on ".GetMP($course_mp).'.');
	
		for_error_sch();
		}
		$mp_table = GetMPTable(GetMP($_REQUEST['marking_period_id'],'TABLE'));

		$current_RET = DBGet(DBQuery('SELECT STUDENT_ID FROM schedule WHERE COURSE_PERIOD_ID=\''.$_SESSION['MassSchedule.php']['course_period_id'].'\' AND SYEAR=\''.UserSyear().'\' AND ((\''.$start_date.'\' BETWEEN START_DATE AND END_DATE OR END_DATE IS NULL) AND \''.$start_date.'\'>=START_DATE)'),array(),array('STUDENT_ID'));
		$request_RET = DBGet(DBQuery('SELECT STUDENT_ID FROM schedule_requests WHERE WITH_PERIOD_ID IN(SELECT cpv.PERIOD_ID FROM course_period_var cpv WHERE cpv.COURSE_PERIOD_ID=\''.$_SESSION['MassSchedule.php']['course_period_id'].'\')  AND SYEAR=\''.UserSyear().'\' AND COURSE_ID=\''.$_SESSION['MassSchedule.php']['course_id'].'\''),array(),array('STUDENT_ID'));
		
		
// ----------------------------------------- Time Clash Logic Start ---------------------------------------------------------- //
		
		function get_min($time)
		{
			$org_tm = $time;
			$stage = substr($org_tm,-2);
			$main_tm = substr($org_tm,0,5);
			$main_tm = trim($main_tm);
			$sp_time = split(':',$main_tm);
			$hr = $sp_time[0];
			$min = $sp_time[1];
			if($hr == 12)
			{
				$hr = $hr;
			}
			else
			{
				if($stage == 'AM')
					$hr = $hr;
				if($stage == 'PM')
					$hr = $hr + 12;
			}
			
			$time_min = (($hr * 60) + $min);
			return $time_min;
		}
		
		function con_date($date)
		{
			$mother_date = $date;
			$year = substr($mother_date, 7, 4);
			$temp_month = substr($mother_date, 3, 3);
			
				if($temp_month == 'JAN')
					$month = '-01-';
				elseif($temp_month == 'FEB')
					$month = '-02-';
				elseif($temp_month == 'MAR')
					$month = '-03-';
				elseif($temp_month == 'APR')
					$month = '-04-';
				elseif($temp_month == 'MAY')
					$month = '-05-';
				elseif($temp_month == 'JUN')
					$month = '-06-';
				elseif($temp_month == 'JUL')
					$month = '-07-';
				elseif($temp_month == 'AUG')
					$month = '-08-';
				elseif($temp_month == 'SEP')
					$month = '-09-';
				elseif($temp_month == 'OCT')
					$month = '-10-';
				elseif($temp_month == 'NOV')
					$month = '-11-';
				elseif($temp_month == 'DEC')
					$month = '-12-';
					
				$day = substr($mother_date, 0, 2);
				
				$select_date = $year.$month.$day;
				return $select_date;
			
		}
                $convdate = con_date($start_date);
                $course_per_id = $_SESSION['MassSchedule.php']['course_period_id'];
                ////Start Date Check///////////////////
                $start_date_q=DBGet(DBQuery('SELECT START_DATE FROM school_years WHERE school_id='.UserSchool().' AND syear='.UserSyear().''));
                if(strtotime($start_date_q[1]['START_DATE'])>strtotime($convdate))
                {
                 $start_date_q_clash='Cannot schedule students before school start date';   
                }
                else
                {
                unset($start_date_q_clash);

                foreach($_REQUEST['student'] as $index=>$value)
                {
                    $_SESSION['NOT_SCHEDULE'][$index]=$index;
                     $_SESSION['NOT_SCHEDULE1'][$index]=$index;
                $stu_start_date_q=DBGet(DBQuery('SELECT START_DATE FROM student_enrollment WHERE STUDENT_ID='.$index.' AND school_id='.UserSchool().' AND syear='.UserSyear().''));
                    if(strtotime($stu_start_date_q[1]['START_DATE'])>strtotime($convdate))
                    {
                        unset($_REQUEST['student'][$index]);
                        $stu_s_date_err_q=DBGet(DBQuery('SELECT FIRST_NAME,LAST_NAME FROM students WHERE STUDENT_ID='.$index.''));
                        $stu_s_date_err_n=$stu_s_date_err_q[1]['FIRST_NAME']."&nbsp;".$stu_s_date_err_q[1]['LAST_NAME'];
                        $stu_sd_err.=$stu_s_date_err_n.'<br>';
                        $stu_sd_err_count++;
                    }
                    if($_REQUEST['student'][$index])
                    {
                        $cp_mp=  DBGet(DBQuery('SELECT MARKING_PERIOD_ID,BEGIN_DATE,END_DATE FROM course_periods WHERE SCHOOL_ID='.UserSchool().' AND SYEAR='.UserSyear().' AND COURSE_PERIOD_ID = \''.$course_per_id.'\' '));
                        $cp_mp[1]['MARKING_PERIOD_ID']=($cp_mp[1]['MARKING_PERIOD_ID']!=''?$cp_mp[1]['MARKING_PERIOD_ID']:GetMPId('FY'));
                        
                        $cp_mp_st_dt=DBGet(DBQuery('SELECT * FROM marking_periods WHERE MARKING_PERIOD_ID='.$cp_mp[1]['MARKING_PERIOD_ID']));
                        if(strtotime($cp_mp_st_dt[1]['START_DATE'])>strtotime($convdate))
                        {
                            unset($_REQUEST['student'][$index]);
                            $stu_s_mp_date_err_n=DBGet(DBQuery('SELECT FIRST_NAME,LAST_NAME FROM students WHERE STUDENT_ID='.$index.''));
                            $stu_s_mp_date_err_n=$stu_s_mp_date_err_n[1]['FIRST_NAME']."&nbsp;".$stu_s_mp_date_err_n[1]['LAST_NAME'];
                            $stu_mp_sd_err.=$stu_s_mp_date_err_n.'<br>';
  
                        }
                    }
                }
		$course_per_id = $_SESSION['MassSchedule.php']['course_period_id'];
		$per_id = DBGet(DBQuery('SELECT PERIOD_ID, DAYS FROM course_period_var WHERE COURSE_PERIOD_ID = \''.$course_per_id.'\''));
		$period_id = $per_id[1]['PERIOD_ID'];
		$days = $per_id[1]['DAYS'];
		$day_st_count = strlen($days);
		
                        $st_time = DBGet(DBQuery('SELECT START_TIME, END_TIME FROM school_periods WHERE PERIOD_ID = \''.$period_id.'\' AND (IGNORE_SCHEDULING IS NULL OR IGNORE_SCHEDULING!=\''.Y.'\')'));    /********* for homeroom scheduling*/
                        if($st_time)
                        {
                            $start_time = $st_time[1]['START_TIME'];
                            $min_start_time = get_min($start_time);
                            $end_time = $st_time[1]['END_TIME'];
                            $min_end_time = get_min($end_time);
                        }
// ----------------------------------------- Time Clash Logic End ---------------------------------------------------------- //		
		
		foreach($_REQUEST['student'] as $student_id=>$yes)
		{
                    
                     # ------------------------------------ PARENT RESTRICTION STARTS----------------------------------------- #
                                    $pa_RET=DBGet(DBQuery('SELECT PARENT_ID FROM course_periods WHERE COURSE_PERIOD_ID=\''.$_SESSION['MassSchedule.php']['course_period_id'].'\''));
                                    if($pa_RET[1]['PARENT_ID']!=$_SESSION['MassSchedule.php']['course_period_id'])
                                    {
                                        $stu_pa=DBGet(DBQuery('SELECT START_DATE,END_DATE FROM schedule WHERE STUDENT_ID=\''.$student_id.'\' AND COURSE_PERIOD_ID=\''.$pa_RET[1]['PARENT_ID'].'\' AND DROPPED=\''.N.'\' AND START_DATE<=\''.date('Y-m-d',strtotime($start_date)).'\''));
                                        $par_sch=count($stu_pa);
                                        if($par_sch<1 || (strtotime(DBDate())<strtotime($stu_pa[$par_sch]['START_DATE']) && $stu_pa[$par_sch]['START_DATE']!="") || (strtotime(DBDate())>strtotime($stu_pa[$par_sch]['END_DATE']) && $stu_pa[$par_sch]['END_DATE']!=""))
                                        {
                                             $select_stu_RET = DBGet(DBQuery('SELECT FIRST_NAME,LAST_NAME FROM students WHERE STUDENT_ID=\''.$student_id.'\''));
                                                                $select_stu = $select_stu_RET[1]['FIRST_NAME']."&nbsp;".$select_stu_RET[1]['LAST_NAME'];
                                                                $parent_res .= $select_stu."<br>";
                                                                continue;
                                        }
                                         
                                    }
                                    
            
                                    # ------------------------------------ PARENT RESTRICTION ENDS----------------------------------------- #
			if($_SESSION['MassSchedule.php']['gender']!='N')
                                                     {
                                                            $select_stu_RET = DBGet(DBQuery('SELECT FIRST_NAME,LAST_NAME,LEFT(GENDER,1) AS GENDER FROM students WHERE STUDENT_ID=\''.$student_id.'\''));
                                                            if($_SESSION['MassSchedule.php']['gender']!=$select_stu_RET[1]['GENDER']){
                                                                $select_stu = $select_stu_RET[1]['FIRST_NAME']."&nbsp;".$select_stu_RET[1]['LAST_NAME'];
                                                                $gender_conflict .= $select_stu."<br>";
                                                                continue;
                                                            }
                                                            #$clash = true;
                                                     }
                         # ------------------------------------ Same Days Conflict Start ------------------------------------------ #
			$mp_RET = DBGet(DBQuery('SELECT cp.MP,cp.MARKING_PERIOD_ID,cpv.DAYS,cpv.PERIOD_ID,cp.MARKING_PERIOD_ID,cp.TOTAL_SEATS,COALESCE(cp.FILLED_SEATS,0) AS FILLED_SEATS FROM course_periods cp,course_period_var cpv WHERE cp.COURSE_PERIOD_ID=\''.$_SESSION['MassSchedule.php']['course_period_id'].'\' AND cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID'));
                        $mps = GetAllMP(GetMPTable(GetMP($mp_RET[1]['MARKING_PERIOD_ID'],'TABLE')),$mp_RET[1]['MARKING_PERIOD_ID']);
                        $period_RET = DBGet(DBQuery('SELECT cpv.DAYS FROM schedule s,course_periods cp,course_period_var cpv WHERE cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.STUDENT_ID=\''.$student_id.'\' AND cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID AND cpv.PERIOD_ID=\''.$mp_RET[1]['PERIOD_ID'].'\' AND s.MARKING_PERIOD_ID IN ('.$mps.') AND (s.END_DATE IS NULL OR \''.$convdate.'\'<=s.END_DATE)'));
                        $ig_scheld=DBGet(DBQuery('SELECT IGNORE_SCHEDULING FROM school_periods WHERE PERIOD_ID=\''.$mp_RET[1]['PERIOD_ID'].'\' AND SCHOOL_ID=\''.UserSchool().'\''));
                        $sql_dupl = 'SELECT COURSE_PERIOD_ID FROM schedule WHERE STUDENT_ID = \''.$student_id.'\' AND COURSE_PERIOD_ID = \''.$_SESSION['MassSchedule.php']['course_period_id'].'\' AND (END_DATE IS NULL OR (\''.$convdate.'\' BETWEEN START_DATE AND END_DATE)) AND SCHOOL_ID=\''.UserSchool().'\'';
                        $rit_dupl = DBQuery($sql_dupl);
                        $count_entry = mysql_num_rows($rit_dupl);
                        $days_conflict = false;
                        if($count_entry<1 && $ig_scheld[1]['IGNORE_SCHEDULING']!='Y')
                        foreach($period_RET as $existing)
                        {
                                if(strlen($mp_RET[1]['DAYS'])+strlen($existing['DAYS'])>7)
                                {
                                        $days_conflict = true;
                                        break;
                                }
                                else
				foreach(_str_split($mp_RET[1]['DAYS']) as  $i)
                                if(strpos($existing['DAYS'],$i)!==false)
                                {
                                        $days_conflict = true;
                                        break 2;
                                }
                        }
                        if($count_entry>=1)
                            $days_conflict = true;
                        if($days_conflict)
                        {
                            $select_stu_RET = DBGet(DBQuery('SELECT FIRST_NAME,LAST_NAME FROM students WHERE STUDENT_ID=\''.$student_id.'\''));
                            $select_stu = $select_stu_RET[1]['FIRST_NAME']."&nbsp;".$select_stu_RET[1]['LAST_NAME'];
                            $period_res .= $select_stu."<br>";
                            continue;
			
                        }
                        # ------------------------------------ Same Days Conflict End ------------------------------------------ #
			$sql = 'SELECT COURSE_PERIOD_ID,START_DATE, MARKING_PERIOD_ID FROM schedule WHERE STUDENT_ID = \''.$student_id.'\' AND SCHOOL_ID=\''.UserSchool().'\' AND MARKING_PERIOD_ID IN ('.$mps.') AND (END_DATE IS NULL OR END_DATE >\''.$convdate.'\')';
			
			$coue_p_id = DBGet(DBQuery($sql));

			if(count($coue_p_id)>=1)
			{
                            foreach($coue_p_id as $ci =>$cv)
                            {
                                $min_sel_start_time="";
                                $min_sel_end_time="";
                                $cp_id = $cv['COURSE_PERIOD_ID'];
                                $st_dt = $cv['START_DATE'];
                                $mp_id_stu = $cv['MARKING_PERIOD_ID'];
                                
			# --------------------------------- For Duplicate Entry Start -------------------------------------- #

			
			
			# --------------------------------- For Duplicate Entry Start -------------------------------------- #
			

					# -------------------------------------- #
					$sel_per_id = DBGet(DBQuery('SELECT PERIOD_ID, DAYS FROM course_period_var WHERE COURSE_PERIOD_ID = \''.$cp_id.'\''));
					$sel_period_id = $sel_per_id[1]['PERIOD_ID'];
					$sel_days = $sel_per_id[1]['DAYS'];
					
					$ignore_existing_period_ret=DBGet(DBQuery('SELECT IGNORE_SCHEDULING FROM school_periods WHERE PERIOD_ID=\''.$sel_period_id.'\''));
                                        $ignore_existing_period=$ignore_existing_period_ret[1]['IGNORE_SCHEDULING'];
                                        if($ignore_existing_period =='Y')
                                            $sel_period_id='';
                                         if($sel_period_id)
                                         {
                                            $sel_st_time = DBGet(DBQuery('SELECT START_TIME, END_TIME FROM school_periods WHERE PERIOD_ID = \''.$sel_period_id.'\''));
                                            $sel_start_time = $sel_st_time[1]['START_TIME'];
                                            $min_sel_start_time = get_min($sel_start_time);
                                            $sel_end_time = $sel_st_time[1]['END_TIME'];
                                            $min_sel_end_time = get_min($sel_end_time);
                                         }
					# ---------------------------- Days conflict ------------------------------------ #
//					
						$j = 0;
						for($i=0; $i<$day_st_count; $i++)
						{
							$clip = substr($days, $i, 1);
							$pos = strpos($sel_days, $clip);
							if($pos !== false)
								$j++;
						}
//					
					
					# ---------------------------- Days conflict ------------------------------------ #
					if($j != 0)
					{				
                                            $time_clash_found = 0;
                                            if((($min_sel_start_time <= $min_start_time) && ($min_sel_end_time >= $min_start_time)) && $min_sel_start_time!='' || (($min_sel_start_time <= $min_end_time) && ($min_sel_end_time >= $min_end_time) && $min_sel_start_time!='') || (($min_sel_start_time >= $min_start_time) && ($min_sel_end_time <= $min_end_time) && $min_start_time!=''))
						{
							$select_stu = DBGet(DBQuery('SELECT FIRST_NAME,LAST_NAME FROM students WHERE STUDENT_ID=\''.$student_id.'\''));
							$select_stu = $select_stu[1]['FIRST_NAME']."&nbsp;".$select_stu[1]['LAST_NAME'];
							$time_clash .= $select_stu."<br>";
							$time_clash_found = 1;
							break;
						}


//			# -------------------- Manual OverRide -------------------------- #
//
//							
						
					}
                                      
                                        }
					if($j==0 || $time_clash_found==0)
					{
					
							// ------------------------------------------------------- //
							
							# -------------------- Manual OverRide -------------------------- #
							
							$check_seats = DBGet(DBQuery('SELECT  (TOTAL_SEATS - FILLED_SEATS) AS AVAILABLE_SEATS FROM course_periods WHERE COURSE_PERIOD_ID=\''.$_SESSION['MassSchedule.php']['course_period_id'].'\''));
							$check_seats = $check_seats[1]['AVAILABLE_SEATS'];
							
							# -------------------- Manual OverRide -------------------------- #
							
							
							if($check_seats > 0)
							{
								$sql = 'INSERT INTO schedule (SYEAR,SCHOOL_ID,STUDENT_ID,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID,START_DATE,MODIFIED_DATE,MODIFIED_BY)
													values(\''.UserSyear().'\',\''.UserSchool().'\',\''.clean_param($student_id,PARAM_INT).'\',\''.$_SESSION['MassSchedule.php']['course_id'].'\',\''.$_SESSION['MassSchedule.php']['course_period_id'].'\',\''.$mp_table.'\',\''.clean_param($_REQUEST['marking_period_id'],PARAM_INT).'\',\''.$start_date.'\',\''.date('Y-m-d').'\',\''.User('STAFF_ID').'\')';
								DBQuery($sql);
//								
								$request_exists = false;
								$note = "That course has been added to the selected students' schedules.";	
                                                               
                                                                
                                                        }
							else
							{
							
								$no_seat = 'There is no available seats in this period.<br>';
								
								$no_seat .= '</DIV>'."<A HREF=# onclick='window.open(\"ForWindow.php?modname=$_REQUEST[modname]&modfunc=seats&course_period_id=".$_SESSION['MassSchedule.php']['course_period_id']."\",\"\",\"scrollbars=no,status=no,screenX=500,screenY=500,resizable=no,width=500,height=200\");'style=\"text-decoration:none;\"><strong><input type=button class=btn_large value='Manual Override'></strong></A></TD></TR>";
							
							}						
							
							// ------------------------------------------------------- //
					
					}
					
				//}
				
                        
			}
			else
			{
				
						# -------------------- Manual OverRide -------------------------- #
						
					$check_seats = DBGet(DBQuery('SELECT  (TOTAL_SEATS - FILLED_SEATS) AS AVAILABLE_SEATS FROM course_periods WHERE COURSE_PERIOD_ID=\''.$_SESSION['MassSchedule.php']['course_period_id'].'\''));
					$check_seats = $check_seats[1]['AVAILABLE_SEATS'];
						
						# -------------------- Manual OverRide -------------------------- #
				if($check_seats > 0)
				{
					$sql = 'INSERT INTO schedule (SYEAR,SCHOOL_ID,STUDENT_ID,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID,START_DATE,MODIFIED_DATE,MODIFIED_BY)
											values(\''.UserSyear().'\',\''.UserSchool().'\',\''.clean_param($student_id,PARAM_INT).'\',\''.$_SESSION['MassSchedule.php']['course_id'].'\',\''.$_SESSION['MassSchedule.php']['course_period_id'].'\',\''.$mp_table.'\',\''.clean_param($_REQUEST['marking_period_id'],PARAM_INT).'\',\''.$start_date.'\',\''.date('Y-m-d').'\',\''.User('STAFF_ID').'\')';
					DBQuery($sql);
//					DBQuery("UPDATE course_periods SET FILLED_SEATS=FILLED_SEATS+1 WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'");
					$request_exists = false;
					$note = "That course has been added to the selected students' schedules.";
				
//                                                $name=array();
//                                                $stu_alrd_id1=array();   
//                                                 $in_data_stu_id1=DBGet(DBQuery('SELECT * FROM SCHEDULE WHERE COURSE_PERIOD_ID=\''.$course_id.'\'')); 
//
//                                                for($i=1;$i<=count($in_data_stu_id1);$i++)
//                                                {
//                                                    array_push($stu_alrd_id1,$in_data_stu_id1[$i]['STUDENT_ID']);
//                                                } 
//                                                print_r($stu_alrd_id1);
//                                                $n_schedule_stu1=$_SESSION['NOT_SCHEDULE1']; 
//                                               print_r($n_schedule_stu1);
//                                                foreach($n_schedule_stu1 as $k => $v)
//                                                {
//                                                    if(!in_array($v,$stu_alrd_id1))
//                                                    {
//
//                                                          $er = DBGET(DBQuery('SELECT * FROM students WHERE STUDENT_ID='.$v.''));
//                                                
////                                                          $er=DBQuery($sql);
//                                                $nam=$er[1]['FIRST_NAME']." ".$er[1]['Last_NAME'];
//                                                array_push($name,$nam);
//                                                }
//                                                }
//                                                print_r($name);
//                                                if(count($name)>0)
//                                                $note.="But".implode(",",$name)."can not be scheduled due to unavailable seat.please mannual override course period seat.";
//                                
//                                                unset($_SESSION['NOT_SCHEDULE1']);
                                }
				else
				{
					
					$no_seat = 'There is no available seats in this period.<br>';
							
					$no_seat .= '</DIV>'."<A HREF=# onclick='window.open(\"ForWindow.php?modname=$_REQUEST[modname]&modfunc=seats&course_period_id=".$_SESSION['MassSchedule.php']['course_period_id']."\",\"\",\"scrollbars=no,status=no,screenX=500,screenY=500,resizable=no,width=500,height=200\");'style=\"text-decoration:none;\"><strong><input type=button class=btn_large value='Manual Override'></strong></A></TD></TR>";
						
				}
                        
			}
			
		
		
		
		
		}
                    }
                    
                    DBQuery('DELETE FROM missing_attendance WHERE COURSE_PERIOD_ID ='.$_SESSION['MassSchedule.php']['course_period_id'].'');
                     $cps=$_SESSION['MassSchedule.php']['course_period_id'];
//                    DBQuery('INSERT INTO missing_attendance(SCHOOL_ID,SYEAR,SCHOOL_DATE,COURSE_PERIOD_ID,PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID) SELECT s.ID AS SCHOOL_ID,acc.SYEAR,acc.SCHOOL_DATE,cp.COURSE_PERIOD_ID,cpv.PERIOD_ID, IF(tra.course_period_id=cp.course_period_id AND acc.school_date<tra.assign_date =true,tra.pre_teacher_id,cp.teacher_id) AS TEACHER_ID,cp.SECONDARY_TEACHER_ID FROM attendance_calendar acc INNER JOIN marking_periods mp ON mp.SYEAR=acc.SYEAR AND mp.SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN mp.START_DATE AND mp.END_DATE INNER JOIN course_periods cp ON cp.MARKING_PERIOD_ID=mp.MARKING_PERIOD_ID AND cp.CALENDAR_ID=acc.CALENDAR_ID INNER JOIN course_period_var cpv ON cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID AND cpv.DOES_ATTENDANCE=\'Y\' LEFT JOIN teacher_reassignment tra ON (cp.course_period_id=tra.course_period_id) INNER JOIN school_periods sp ON sp.SYEAR=acc.SYEAR AND sp.SCHOOL_ID=acc.SCHOOL_ID AND sp.PERIOD_ID=cpv.PERIOD_ID AND (sp.BLOCK IS NULL AND position(substring(\'UMTWHFS\' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cpv.DAYS)>0 OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK) INNER JOIN schools s ON s.ID=acc.SCHOOL_ID INNER JOIN schedule sch ON sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID  AND sch.START_DATE<=acc.SCHOOL_DATE AND (sch.END_DATE IS NULL OR sch.END_DATE>=acc.SCHOOL_DATE ) AND cp.COURSE_PERIOD_ID ='.$_SESSION['MassSchedule.php']['course_period_id'].' LEFT JOIN attendance_completed ac ON ac.SCHOOL_DATE=acc.SCHOOL_DATE AND IF(tra.course_period_id=cp.course_period_id AND acc.school_date<=tra.assign_date =true,ac.staff_id=tra.pre_teacher_id,ac.staff_id=cp.teacher_id) AND ac.PERIOD_ID=sp.PERIOD_ID WHERE acc.SYEAR=\''.UserSyear().'\'  AND acc.SCHOOL_ID=\''.UserSchool().'\' AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) AND acc.SCHOOL_DATE<=\''.date('Y-m-d').'\'  AND ac.STAFF_ID IS NULL GROUP BY s.TITLE,acc.SCHOOL_DATE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.TEACHER_ID');        
                $schedule_type_check1=DBGet(DBQuery("SELECT SCHEDULE_TYPE FROM course_periods WHERE COURSE_PERIOD_ID='".$cps."'"));                
                 if($schedule_type_check1[1]['SCHEDULE_TYPE']=='FIXED')
                 {
                   DBQuery('INSERT INTO missing_attendance(SCHOOL_ID,SYEAR,SCHOOL_DATE,COURSE_PERIOD_ID,PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID) SELECT s.ID AS SCHOOL_ID,acc.SYEAR,acc.SCHOOL_DATE,cp.COURSE_PERIOD_ID,cpv.PERIOD_ID, IF(tra.course_period_id=cp.course_period_id AND acc.school_date<tra.assign_date =true,tra.pre_teacher_id,cp.teacher_id) AS TEACHER_ID,cp.SECONDARY_TEACHER_ID FROM attendance_calendar acc INNER JOIN marking_periods mp ON mp.SYEAR=acc.SYEAR AND mp.SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN mp.START_DATE AND mp.END_DATE INNER JOIN course_periods cp ON cp.MARKING_PERIOD_ID=mp.MARKING_PERIOD_ID AND cp.CALENDAR_ID=acc.CALENDAR_ID INNER JOIN course_period_var cpv ON cp.COURSE_PERIOD_ID=cpv.COURSE_PERIOD_ID AND cpv.DOES_ATTENDANCE=\'Y\' LEFT JOIN teacher_reassignment tra ON (cp.course_period_id=tra.course_period_id) INNER JOIN school_periods sp ON sp.SYEAR=acc.SYEAR AND sp.SCHOOL_ID=acc.SCHOOL_ID AND sp.PERIOD_ID=cpv.PERIOD_ID AND (sp.BLOCK IS NULL AND position(substring(\'UMTWHFS\' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cpv.DAYS)>0 OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK) INNER JOIN schools s ON s.ID=acc.SCHOOL_ID INNER JOIN schedule sch ON sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID  AND sch.START_DATE<=acc.SCHOOL_DATE AND (sch.END_DATE IS NULL OR sch.END_DATE>=acc.SCHOOL_DATE ) AND cp.COURSE_PERIOD_ID ='.$_SESSION['MassSchedule.php']['course_period_id'].' LEFT JOIN attendance_completed ac ON ac.SCHOOL_DATE=acc.SCHOOL_DATE AND IF(tra.course_period_id=cp.course_period_id AND acc.school_date<=tra.assign_date =true,ac.staff_id=tra.pre_teacher_id,ac.staff_id=cp.teacher_id) AND ac.PERIOD_ID=sp.PERIOD_ID WHERE acc.SYEAR=\''.UserSyear().'\'  AND acc.SCHOOL_ID=\''.UserSchool().'\' AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) AND acc.SCHOOL_DATE<=\''.date('Y-m-d').'\'  AND ac.STAFF_ID IS NULL GROUP BY s.TITLE,acc.SCHOOL_DATE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.TEACHER_ID');                          
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
//                    }
//                    print_r($day2);
                    $days_check=DBGet(DBQuery("SELECT sch.START_DATE FROM schedule sch,course_periods cp WHERE cp.COURSE_PERIOD_ID='".$cps."' AND cp.COURSE_PERIOD_ID=sch.COURSE_PERIOD_ID AND cp.COURSE_ID=sch.COURSE_ID AND sch.SCHOOL_ID='".UserSchool()."' AND sch.SYEAR='".UserSyear()."'"));
                   foreach($days_check as $index=>$dates)
                   {    
                       $day_found_count=0;
                       $sec=0;
                       $total_diff_days=(strtotime(date('Y-m-d'))-strtotime($dates['START_DATE']))/86400;
                       for($i=0;$i<$total_diff_days;$i++)
                       {
                           
                           
                       $day_found=date('l',strtotime($dates['START_DATE'])+$sec);
//                        echo '<br>';
                        
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
//                           $period_id_for_mi=DBGet(DBQuery("SELECT PERIOD_ID FROM course_period_var WHERE COURSE_PERIOD_ID='".$cps."' AND DAYS='".$day1[$i+1]['DAYS']."'"));
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
//                     print_r($block_schedule_vals);echo '<br>';
                     foreach($block_schedule_vals as $index=>$vals)
                     {
                      $calendar_id=DBGet(DBQuery("SELECT CALENDAR_ID FROM course_periods WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".  UserSchool()."' AND COURSE_PERIOD_ID='".$cps."'"));
                        $calendar_id=$calendar_id[1]['CALENDAR_ID']; 
                        $attendance_day_date=DBGet(DBQuery("SELECT COUNT(*) as PRESENT FROM attendance_calendar WHERE SYEAR='".UserSyear()."' AND SCHOOL_DATE='".$vals['COURSE_PERIOD_DATE']."' AND SCHOOL_ID='".  UserSchool()."' AND CALENDAR_ID='".$calendar_id."'"));
                        if($attendance_day_date[1]['PRESENT']!=0)  
                        {
                        $days_check1=DBGet(DBQuery("SELECT sch.START_DATE FROM schedule sch,course_periods cp WHERE cp.COURSE_PERIOD_ID='".$cps."' AND cp.COURSE_PERIOD_ID=sch.COURSE_PERIOD_ID AND cp.COURSE_ID=sch.COURSE_ID AND sch.SCHOOL_ID='".UserSchool()."' AND sch.SYEAR='".UserSyear()."' AND sch.START_DATE<='".$vals['COURSE_PERIOD_DATE']."'"));
                        foreach($days_check1 as $days_check)
                        {
                        if($days_check['START_DATE']!='')
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
//                UpdateMissingAttendance($_SESSION['MassSchedule.php']['course_period_id']);
		unset($_REQUEST['modfunc']);
		unset($_SESSION['MassSchedule.php']);
	}
	else
	{
		//BackPrompt('You must choose a Course');
		ShowErr('You must choose a Course');
		#for_error();
		for_error_sch();
	}
		
}
if($_REQUEST['modfunc']!='choose_course')
{ #echo "<script>alert('a')</script>";
	DrawBC("Scheduling > ".ProgramTitle());
	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM name=sav action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=save method=POST>";
		#DrawHeader('',SubmitButton('Add Course to Selected Students'));
		PopTable_wo_header ('header');
		echo '<TABLE><TR><TD>Course to Add</TD><TD><DIV id=course_div>';
		if($_SESSION['MassSchedule.php'])
		{
			$course_title = DBGet(DBQuery('SELECT TITLE FROM courses WHERE COURSE_ID=\''.$_SESSION['MassSchedule.php']['course_id'].'\''));
			$course_title = $course_title[1]['TITLE'];
			$period_title = DBGet(DBQuery('SELECT TITLE FROM course_periods WHERE COURSE_PERIOD_ID=\''.$_SESSION['MassSchedule.php']['course_period_id'].'\''));
			$period_title = $period_title[1]['TITLE'];

			echo "$course_title - ".strip_tags(trim($_REQUEST[course_weight]))."<BR>$period_title";
		}
		echo '</DIV>'."<A HREF=# onclick='window.open(\"ForWindow.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Choose a Course</A></TD></TR>";
//		echo '<TR><TD>Start Date</TD><TD>'.PrepareDate(DBDate(),'').'</TD></TR>';
                echo '<TR><TD>Start Date</TD><TD>'.  DateInputAY(date('Y-m-d'),'start',1).'</TD></TR>';
		echo '<TR><TD>Marking Period</TD><TD>';
		//echo '<SELECT name=marking_period_id><OPTION value=0>Full Year</OPTION>';
		$years_RET = DBGet(DBQuery('SELECT MARKING_PERIOD_ID,TITLE,NULL AS SEMESTER_ID FROM school_years WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));
		$semesters_RET = DBGet(DBQuery('SELECT MARKING_PERIOD_ID,TITLE,NULL AS SEMESTER_ID FROM school_semesters WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER'));
		$quarters_RET = DBGet(DBQuery('SELECT MARKING_PERIOD_ID,TITLE,SEMESTER_ID FROM school_quarters WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER'));
//		if($course_title['MARKING_PERIOD_ID']==NULL || $course_title['MARKING_PERIOD_ID']=='')
//                    $extra1="disabled=true";
//                else {
//                $extra1='';    
//                }
                echo '<SELECT name=marking_period_id id=marking_period><OPTION value='.$years_RET[1]['MARKING_PERIOD_ID'].'>'.$years_RET[1]['TITLE'].'</OPTION>';
		foreach($semesters_RET as $mp)
			echo "<OPTION value=$mp[MARKING_PERIOD_ID]>".$mp['TITLE'].'</OPTION>';
		foreach($quarters_RET as $mp)
			echo "<OPTION value=$mp[MARKING_PERIOD_ID]>".$mp['TITLE'].'</OPTION>';
		echo '</SELECT>';
		echo '</TD></TR>';
		echo '</TABLE>';
		PopTable ('footer');
	}

	if($note)
		DrawHeader('<IMG SRC=assets/check.gif>'.$note);
        if($gender_conflict)
                DrawHeaderHome('<IMG SRC=assets/warning_button.gif><br>'.$gender_conflict.' have Gender Clash');
        if($parent_res)
                DrawHeaderHome('<IMG SRC=assets/warning_button.gif><br>'.$parent_res.' have Parent course restriction');
        if($start_date_q_clash)
                DrawHeaderHome('<IMG SRC=assets/warning_button.gif><br>'.$start_date_q_clash);
        if($stu_sd_err)
                {
                if($stu_sd_err_count>1)
                {
                $put='their';    
                }
                else
                {$put='his/her';}
                 DrawHeaderHome('<IMG SRC=assets/warning_button.gif><br>'.$stu_sd_err.' cannot be scheduled before '.$put.' enrolled date');
                 unset($stu_sd_err);
                 unset($stu_s_date_err_n);
                }
	if($period_res)
                DrawHeaderHome('<IMG SRC=assets/warning_button.gif><br>'.$period_res.' have already scheduled in that period');
        if($time_clash)
		DrawHeaderHome('<IMG SRC=assets/warning_button.gif><br>'.$time_clash.' have a period time clash');
        if($check_seats<=0 && $no_seat)
		DrawHeaderHome('<IMG SRC=assets/warning_button.gif>'.$no_seat);
//	elseif($clash)
//		DrawHeaderHome('<IMG SRC=assets/warning_button.gif>'.$clash." is already in the schedule");
        elseif($request_exists)
		DrawHeaderHome('<IMG SRC=assets/warning_button.gif>'.$request_clash.' already have unscheduled requests');
	
                  
#for_error_sch();
}
if(!$_REQUEST['modfunc'])
{
	if($_REQUEST['search_modfunc']!='list')
		unset($_SESSION['MassSchedule.php']);
	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",Concat(NULL) AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name="controller" onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
	$extra['new'] = true;

	Widgets('course');
	Widgets('request');
	Widgets('activity');

	Search_GroupSchedule('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
            if($_SESSION['count_stu']!=0)
                   echo '<BR><CENTER>'.SubmitButton('','','class=btn_group_schedule onclick=\'return validate_group_schedule();\'').'</CENTER>'; 
//		echo '<BR><CENTER>'.SubmitButton('','','class=btn_group_schedule onclick=\'formload_ajax("sav");\'').'</CENTER>';
		echo "</FORM>";
	}

}

if($_REQUEST['modfunc']=='choose_course')
{

	if(!$_REQUEST['course_period_id'])
		include 'modules/scheduling/CoursesforWindow.php';
	else
	{
		$_SESSION['MassSchedule.php']['subject_id'] = $_REQUEST['subject_id'];
		$_SESSION['MassSchedule.php']['course_id'] = $_REQUEST['course_id'];
		//$_SESSION['MassSchedule.php']['course_weight'] = $_REQUEST['course_weight'];
		$_SESSION['MassSchedule.php']['course_period_id'] = $_REQUEST['course_period_id'];

		$course_title = DBGet(DBQuery('SELECT TITLE FROM courses WHERE COURSE_ID=\''.$_SESSION['MassSchedule.php']['course_id'].'\''));
		$course_title = $course_title[1]['TITLE'];
		$period_title_RET = DBGet(DBQuery('SELECT TITLE,MARKING_PERIOD_ID,GENDER_RESTRICTION,BEGIN_DATE,END_DATE FROM course_periods WHERE COURSE_PERIOD_ID=\''.$_SESSION['MassSchedule.php']['course_period_id'].'\''));
		$period_title = $period_title_RET[1]['TITLE'];
		$mperiod = ($period_title_RET[1]['MARKING_PERIOD_ID']!=''?$period_title_RET[1]['MARKING_PERIOD_ID']:GetMPId('FY'));
//                if($mperiod=='')
//                {
//                    $get_mp_id=DBGet(DBQuery('SELECT MARKING_PERIOD_ID FROM marking_periods WHERE START_DATE<=\''.$period_title_RET[1]['BEGIN_DATE'].'\' AND END_DATE>=\''.$period_title_RET[1]['BEGIN_DATE'].'\' AND SCHOOL_ID='.  UserSchool().' AND SYEAR='.  UserSyear()));
//                    $mperiod=$get_mp_id[1]['MARKING_PERIOD_ID'];
//                }
                
                if($period_title_RET[1]['MARKING_PERIOD_ID']==NULL)
                $true='true';
            else {
            $true='';    
            }
           
                                    $gender_res=$period_title_RET[1]['GENDER_RESTRICTION'];
                                    $_SESSION['MassSchedule.php']['gender']=$gender_res;
                                    if($gender_res=='N')
		echo "<script language=javascript>opener.document.getElementById(\"course_div\").innerHTML = \"$course_title<BR>$period_title\";opener.document.getElementById(\"marking_period\").value=\"$mperiod\";opener.document.getElementById(\"marking_period\").disabled=\"$true\"; window.close();</script>";
                                    else
                                        echo "<script language=javascript>opener.document.getElementById(\"course_div\").innerHTML = \"$course_title <BR>$period_title <br>Gender : ".($gender_res=='M'?'Male':'Female')." \";opener.document.getElementById(\"marking_period\").value=\"$mperiod\"; window.close();</script>";
               
	}
}

if($_REQUEST['modfunc']=='seats')
	{
	
	if($_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS']!='' && $_REQUEST['update'])
		{
//			DBQuery('UPDATE course_periods SET TOTAL_SEATS=\''.$_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['TOTAL_SEATS'].'\' WHERE COURSE_PERIOD_ID=\''.$_REQUEST['course_period_id'].'\'');
		}
	if($_REQUEST['course_period_id'])
		{			
				$sql = DBGet(DBQuery('SELECT TOTAL_SEATS FROM course_periods WHERE COURSE_PERIOD_ID=\''.$_REQUEST['course_period_id'].'\''));
				$RET = $sql[1]; 
		}
	echo '<div align=center>'; 
	echo '<form name=update_seats id=update_seats method=POST action=ForWindow.php?modname='.strip_tags(trim($_REQUEST['modname'])).'&modfunc=seatso&course_period_id='.$_REQUEST['course_period_id'].'&update=true>';
 	echo '<TABLE><TR>';
	echo '<td colspan=2 align=center><b>Click on the number of seats to edit and click update</b></td></tr><tr><td colspan=2 align=center><table><tr><td>Total Seats</td><td>:</td><td>' . TextInput($RET['TOTAL_SEATS'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][TOTAL_SEATS]','','size=10 class=cell_floating maxlength=5').'</td></tr></table>';
    echo '</TR><tr><td colspan=2 align=center><input type=submit value=Update class=btn_medium onclick="return check_update_seat('.$_REQUEST['course_period_id'].'); "></td></tr></TABLE>';
	echo '</form>';
	echo '</div>';
	}



function _makeChooseCheckbox($value,$title)
{	global $THIS_RET;

		return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}
function _str_split($str)
{
	$ret = array();
	$len = strlen($str);
	for($i=0;$i<$len;$i++)
		$ret [] = substr($str,$i,1);
	return $ret;
}



?>

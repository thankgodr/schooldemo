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
error_reporting(0);
session_start();
ini_set('max_execution_time','50000');
ini_set('max_input_time','50000');
include("CustomClassInc.php");
$mysql_database=$_SESSION['db'];
$dbUser=$_SESSION['username'];
$dbPass=$_SESSION['password'];
$dbconn = mysql_connect($_SESSION['server'],$_SESSION['username'],$_SESSION['password']) or die() ;
mysql_select_db($mysql_database);
$get_routines=mysql_query('SELECT routine_name,routine_type FROM information_schema.routines WHERE routine_schema=\''.$mysql_database.'\' ');
while($get_routines_arr=mysql_fetch_assoc($get_routines))
{
  
    mysql_query('DROP '.$get_routines_arr['routine_type'].' IF EXISTS '.$get_routines_arr['routine_name']);
}
$get_trigger=mysql_query('SELECT trigger_name FROM information_schema.triggers WHERE trigger_schema=\''.$mysql_database.'\' ');
while($get_trigger_arr=mysql_fetch_assoc($get_trigger))
{
  
    mysql_query('DROP TRIGGER IF EXISTS '.$get_trigger_arr['trigger_name']);
}
$proceed=mysql_query("SELECT name,value
FROM app
WHERE value='4.6' OR value='4.7' OR value LIKE '4.8%' OR value='4.9' OR value='5.0' OR value='5.1' OR value='5.2' OR value='5.3'");
$proceed=mysql_fetch_assoc($proceed);
if(!$proceed)
{
    $proceed=mysql_query("SELECT name,value
    FROM APP
    WHERE value='4.6' OR value='4.7' OR value LIKE '4.8%' OR value='4.9' OR value='5.0'");
    $proceed=mysql_fetch_assoc($proceed);
}
$version=$proceed['value'];
mysql_query('UPDATE '.table_to_upper('students',$version).' SET failed_login=0 WHERE failed_login is null');
if($version!='5.2' && $version!='5.3')
{
mysql_query('Create table staff_new as SELECT * FROM '.table_to_upper('staff',$version).'');
mysql_query('TRUNCATE TABLE staff_new');
mysql_query('ALTER TABLE `staff_new` DROP `syear`, DROP `schools`, DROP `rollover_id`');

mysql_query('DROP TABLE '.table_to_upper('staff_school_relationship',$version).'');
   mysql_query('CREATE TABLE '.table_to_upper('staff_school_relationship',$version).' (
 `staff_id` int(11) NOT NULL,
 `school_id` int(11) NOT NULL,
 `syear` int(4) NOT NULL,
 `start_date` date NOT NULL,
 `end_date` date NOT NULL,
 PRIMARY KEY (`staff_id`,`school_id`,`syear`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8');

$sql=  mysql_query('SELECT * FROM '.table_to_upper('staff',$version).' order by staff_id asc');
while($row= mysql_fetch_array($sql))
{
    if($row['username']!='')
        $staff_sql=mysql_query("SELECT staff_id FROM staff_new WHERE username='".$row['username']."' AND username IS NOT NULL");
    else 
        $staff_sql=mysql_query("SELECT staff_id FROM staff_new WHERE first_name='".$row['first_name']."' AND last_name='".$row['last_name']."' AND profile='".$row['profile']."'");
    if(mysql_num_rows($staff_sql)==0)
    {
        $staff_id=$row['staff_id'];
        mysql_query("insert into staff_new (staff_id,current_school_id,title,first_name,last_name,middle_name,username,password,phone,email,profile,homeroom,last_login,failed_login,profile_id,is_disable) values('".$row['staff_id']."','".$row['current_school_id']."'
            ,'".$row['title']."','".$row['first_name']."','".$row['last_name']."','".$row['middle_name']."','".$row['username']."','".$row['password']."'
                ,'".$row['phone']."','".$row['email']."','".$row['profile']."','".$row['homeroom']."','".$row['last_login']."','".$row['failed_login']."','".$row['profile_id']."','".$row['is_disable']."')");
    if($row['username']!='')
        $st_info_sql=mysql_query("SELECT syear,staff_id,schools FROM ".table_to_upper('staff',$version)." WHERE username='".$row['username']."' AND username IS NOT NULL");
    else
        $st_info_sql=mysql_query("SELECT syear,staff_id,schools FROM ".table_to_upper('staff',$version)." WHERE first_name='".$row['first_name']."' AND last_name='".$row['last_name']."' AND profile='".$row['profile']."' AND username IS NULL");

    while ($row1 = mysql_fetch_array($st_info_sql))
        {


            $school=  substr(substr($row1['schools'],0, -1),1);
            $all_school=  explode(',',$school);
            foreach($all_school as $key=>$value)
            {

                mysql_query('insert into '.table_to_upper('staff_school_relationship',$version).' values(\''.$staff_id.'\',\''.$value.'\',\''.$row1['syear'].'\',\'0000-00-00\',\'0000-00-00\')')or die(mysql_error());
            }
            
            
              
            mysql_query("update attendance_completed set staff_id='".$row['staff_id']."' WHERE staff_id='".$row1['staff_id']."'");
            mysql_query("update  course_periods set teacher_id='".$row['staff_id']."' WHERE teacher_id='".$row1['staff_id']."'");
            mysql_query("update  course_periods set secondary_teacher_id='".$row['staff_id']."' WHERE secondary_teacher_id='".$row1['staff_id']."'");
            mysql_query("update  login_records set staff_id='".$row['staff_id']."' WHERE staff_id='".$row1['staff_id']."'");
            mysql_query("update missing_attendance set teacher_id='".$row['staff_id']."' WHERE teacher_id='".$row1['staff_id']."'");
            mysql_query("update portal_notes set published_user='".$row['staff_id']."'WHERE published_user='".$row1['staff_id']."'");
            mysql_query("update program_user_config set user_id='".$row['staff_id']."'WHERE user_id='".$row1['staff_id']."'");
            mysql_query("update schedule_requests set with_teacher_id='".$row['staff_id']."'WHERE with_teacher_id='".$row1['staff_id']."'");
            
            mysql_query("update teacher_reassignment set teacher_id='".$row['staff_id']."'WHERE teacher_id='".$row1['staff_id']."'");
            mysql_query("update teacher_reassignment set pre_teacher_id='".$row['staff_id']."'WHERE pre_teacher_id='".$row1['staff_id']."'");
            mysql_query("update teacher_reassignment set modified_by='".$row['staff_id']."'WHERE modified_by='".$row1['staff_id']."'");
            mysql_query("update gradebook_assignments set staff_id='".$row['staff_id']."' WHERE staff_id='".$row1['staff_id']."'");
            mysql_query("update gradebook_assignment_types set staff_id='".$row['staff_id']."' WHERE staff_id='".$row1['staff_id']."'");
            mysql_query("update grades_completed set staff_id='".$row['staff_id']."' WHERE staff_id='".$row1['staff_id']."'");
            mysql_query("update student_mp_comments set staff_id='".$row['staff_id']."' WHERE staff_id='".$row1['staff_id']."'");
            mysql_query("update schedule set modified_by='".$row['staff_id']."' WHERE modified_by='".$row1['staff_id']."'");
        
    }
    }
        
    
}
mysql_query('DROP TABLE '.table_to_upper('staff',$version).'');
mysql_query('RENAME TABLE `staff_new` TO '.table_to_upper('staff',$version).'');
}
/////Create Ethinicity and Language Table and insert data////
mysql_query("CREATE TABLE IF NOT EXISTS `ethnicity` (
  `ethnicity_id` int(8) NOT NULL AUTO_INCREMENT,
  `ethnicity_name` varchar(255) NOT NULL,
  `sort_order` int(8) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date time ethnicity record modified',
  PRIMARY KEY (`ethnicity_id`)
) ") or die(mysql_error().' At Line 1');
mysql_query("CREATE TABLE IF NOT EXISTS `language` (
  `language_id` int(8) NOT NULL AUTO_INCREMENT,
  `language_name` varchar(127) NOT NULL,
  `sort_order` int(8) DEFAULT NULL,
  PRIMARY KEY (`language_id`)
)") or die(mysql_error().'  At Line 2');

mysql_query("INSERT INTO `ethnicity` (`ethnicity_id`, `ethnicity_name`, `sort_order`, `last_updated`) VALUES
(1, 'White, Non-Hispanic', 1, '0000-00-00 00:00:00'),
(2, 'Black, Non-Hispanic', 2, '0000-00-00 00:00:00'),
(3, 'Hispanic', 3, '0000-00-00 00:00:00'),
(4, 'American Indian or Native Alaskan', 4, '0000-00-00 00:00:00'),
(5, 'Pacific Islander', 5, '0000-00-00 00:00:00'),
(6, 'Asian', 6, '0000-00-00 00:00:00'),
(7, 'Indian', 7, '0000-00-00 00:00:00'),
(8, 'Middle Eastern', 8, '0000-00-00 00:00:00'),
(9, 'African', 9, '0000-00-00 00:00:00'),
(10, 'Mixed Race', 10, '0000-00-00 00:00:00'),
(11, 'Other', 11, '0000-00-00 00:00:00'),
(12, 'Black', 12, '0000-00-00 00:00:00'),
(13, 'White', 13, '0000-00-00 00:00:00'),
(14, 'African', 14, '0000-00-00 00:00:00'),
(15, 'Indigenous', 15, '2013-05-31 08:50:54')") or die(mysql_error().'  At Line 3');

mysql_query("INSERT INTO `language` (`language_id`, `language_name`, `sort_order`) VALUES
(1, 'English', 1),
(2, 'Arabic', 2),
(3, 'Bengali', 3),
(4, 'Chinese', 4),
(5, 'French', 5),
(6, 'German', 6),
(7, 'Haitian', 7),
(8, 'Creole', 8),
(9, 'Hindi', 9),
(10, 'Italian', 10),
(11, 'Japanese', 11),
(12, 'Korean', 12),
(13, 'Malay', 13),
(14, 'Polish', 14),
(15, 'Portuguese', 15),
(16, 'Russian', 16),
(17, 'Spanish', 17),
(18, 'Thai', 18),
(19, 'Turkish', 19),
(20, 'Urdu', 20),
(21, 'Vietnamese', 21)") or die(mysql_error().'  At Line 4');


mysql_query("CREATE TABLE IF NOT EXISTS `student_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `syear` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `address` varchar(5000) DEFAULT NULL,
  `street` varchar(5000) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zipcode` varchar(255) DEFAULT NULL,
  `bus_pickup` varchar(1) DEFAULT NULL,
  `bus_dropoff` varchar(1) DEFAULT NULL,
  `bus_no` varchar(255) DEFAULT NULL,
  `type` varchar(500) NOT NULL,
  `people_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
)") or die(mysql_error().'  At Line 5');

/////Create login_authentication,students_join_people_new,people_new,student_address,students_new table and insert corressponding_data////////
mysql_query("CREATE TABLE `login_authentication` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `profile_id` int(11) NOT NULL,
 `username` varchar(255) NOT NULL,
 `password` varchar(255) NOT NULL,
 `last_login` datetime DEFAULT NULL,
 `failed_login` int(3) NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`),
 UNIQUE KEY `COMPOSITE` (`user_id`,`profile_id`)
)") or die(mysql_error().'  At Line 6');

mysql_query("CREATE TABLE students_join_people_new (
   id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
   student_id numeric NOT NULL,
   person_id numeric(10,0) NOT NULL,
   is_emergency varchar(10) DEFAULT NULL,
   emergency_type varchar(100) DEFAULT NULL,
   relationship varchar(100) NOT NULL
)") or die(mysql_error().'  At Line 7');

mysql_query("CREATE TABLE `people_new` (
 `staff_id` int(11) NOT NULL AUTO_INCREMENT,
 `current_school_id` decimal(10,0) DEFAULT NULL,
 `title` varchar(5) DEFAULT NULL,
 `first_name` varchar(100) DEFAULT NULL,
 `last_name` varchar(100) DEFAULT NULL,
 `middle_name` varchar(100) DEFAULT NULL,
 `home_phone` varchar(255) DEFAULT NULL,
 `work_phone` varchar(255) DEFAULT NULL,
 `cell_phone` varchar(255) DEFAULT NULL,
 `email` varchar(100) DEFAULT NULL,
 `custody` varchar(1) DEFAULT NULL,
 `profile` varchar(30) DEFAULT NULL,
 `profile_id` decimal(10,0) DEFAULT NULL,
 `is_disable` varchar(10) DEFAULT NULL,
 PRIMARY KEY (`staff_id`)
)") or die(mysql_error().'  At Line 8');

mysql_query("CREATE TABLE IF NOT EXISTS `user_profiles_new` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `profile` varchar(30) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
)") or die(mysql_error().'  At Line 9');

mysql_query("INSERT INTO `user_profiles_new` (`profile`, `title`) VALUES
('admin', 'Super Administrator')") or die(mysql_error().'  At Line 10');
mysql_query("UPDATE  `user_profiles_new` SET  `id` =  '0'") or die(mysql_error().'  At Line 11');
mysql_query("ALTER TABLE  `user_profiles_new` AUTO_INCREMENT=1") or die(mysql_error().'  At Line 12');
mysql_query("INSERT INTO `user_profiles_new` (`profile`, `title`) VALUES
('admin', 'Administrator'),
('teacher', 'Teacher'),
('student', 'Student'),
('parent', 'Parent'),
('admin', 'Admin Asst')") or die(mysql_error().'  At Line 13');

$get_profiles=mysql_query('SELECT * FROM user_profiles WHERE (title<>\'Super Administrator\' AND title<>\'Administrator\' AND title<>\'Teacher\' AND title<>\'Student\' AND title<>\'Parent\' AND title<>\'Admin Asst\') ') or die(mysql_error().'  At Line 14');
if(mysql_num_rows($get_profiles)>0)
{
 while($get_profiles_a=mysql_fetch_assoc($get_profiles))
 {
     mysql_query('INSERT INTO user_profiles_new (profile,title) VALUES (\''.$get_profiles_a['profile'].'\',\''.$get_profiles_a['title'].'\')') or die(mysql_error().'  At Line 15');
 }
}
unset($get_profiles);
unset($get_profiles_a);
$max_p_id=mysql_query('SELECT max(id) as m_id FROM user_profiles_new') or die(mysql_error().'  At Line 85');
$max_p_id=mysql_fetch_assoc($max_p_id);
$max_p_id=$max_p_id['m_id'];

$c_ex=mysql_query('SHOW tables like \'calendar_events_visiblity\'') or die(mysql_error().'  At Line 76');
if(mysql_num_rows($c_ex)>0)
{
mysql_query('UPDATE calendar_events_visiblity SET profile_id=\''.($max_p_id+3).'\' WHERE profile_id=\'0\' ') or die(mysql_error().'  At Line 77');
mysql_query('UPDATE calendar_events_visiblity SET profile_id=\''.($max_p_id+4).'\' WHERE profile_id=\'3\' ') or die(mysql_error().'  At Line 78');
mysql_query('UPDATE calendar_events_visiblity SET profile_id=\''.($max_p_id+5).'\' WHERE profile_id=\'4\' ') or die(mysql_error().'  At Line 79');
}
mysql_query('UPDATE profile_exceptions SET profile_id=\''.($max_p_id+3).'\' WHERE profile_id=\'0\' ') or die(mysql_error().'  At Line 80');
mysql_query('UPDATE profile_exceptions SET profile_id=\''.($max_p_id+4).'\' WHERE profile_id=\'3\' ') or die(mysql_error().'  At Line 81');
mysql_query('UPDATE profile_exceptions SET profile_id=\''.($max_p_id+5).'\' WHERE profile_id=\'4\' ') or die(mysql_error().'  At Line 82');

mysql_query('UPDATE staff SET profile_id=\''.($max_p_id+4).'\' WHERE profile_id=\'3\' ') or die(mysql_error().'  At Line 83');
mysql_query('UPDATE staff SET profile_id=\''.($max_p_id+5).'\' WHERE profile_id=\'4\' ') or die(mysql_error().'  At Line 84');

$get_profiles=mysql_query('SELECT * FROM user_profiles WHERE (title<>\'Super Administrator\' AND title<>\'Administrator\' AND title<>\'Teacher\' AND title<>\'Student\' AND title<>\'Parent\' AND title<>\'Admin Asst\')') or die(mysql_error().'  At Line 16');
if(mysql_num_rows($get_profiles)>0)
{
 while($get_profiles_a=mysql_fetch_assoc($get_profiles))
 {
  $get_new_profiles=mysql_query('SELECT * FROM user_profiles_new WHERE title=\''.$get_profiles_a['title'].'\' AND profile=\''.$get_profiles_a['profile'].'\' ') or die(mysql_error().'  At Line 17');  
  if(mysql_num_rows($get_new_profiles)>0)
  {
      while($get_new_profiles_a=mysql_fetch_assoc($get_new_profiles))
      {
          $c_ex=mysql_query('SHOW tables like \'calendar_events_visiblity\'') or die(mysql_error().'  At Line 18');
          if(mysql_num_rows($c_ex)>0)
          mysql_query('UPDATE calendar_events_visiblity SET profile_id=\''.$get_new_profiles_a['id'].'\' WHERE profile_id=\''.$get_profiles_a['id'].'\' ') or die(mysql_error().'  At Line 19');
          mysql_query('UPDATE profile_exceptions SET profile_id=\''.$get_new_profiles_a['id'].'\' WHERE profile_id=\''.$get_profiles_a['id'].'\' ') or die(mysql_error().'  At Line 20');
          mysql_query('UPDATE staff SET profile_id=\''.$get_new_profiles_a['id'].'\' WHERE profile_id=\''.$get_profiles_a['id'].'\' ') or die(mysql_error().'  At Line 21');
          
      }
  }
 }
}

$c_ex=mysql_query('SHOW tables like \'calendar_events_visiblity\'') or die(mysql_error().'  At Line 85');
if(mysql_num_rows($c_ex)>0)
{
mysql_query('UPDATE calendar_events_visiblity SET profile_id=\'3\' WHERE profile_id=\''.($max_p_id+3).'\' ') or die(mysql_error().'  At Line 86');
mysql_query('UPDATE calendar_events_visiblity SET profile_id=\'4\' WHERE profile_id=\''.($max_p_id+4).'\' ') or die(mysql_error().'  At Line 87');
mysql_query('UPDATE calendar_events_visiblity SET profile_id=\'5\' WHERE profile_id=\''.($max_p_id+5).'\' ') or die(mysql_error().'  At Line 88');
}
mysql_query('UPDATE profile_exceptions SET profile_id=\'3\' WHERE profile_id=\''.($max_p_id+3).'\' ') or die(mysql_error().'  At Line 89');
mysql_query('UPDATE profile_exceptions SET profile_id=\'4\' WHERE profile_id=\''.($max_p_id+4).'\' ') or die(mysql_error().'  At Line 90');
mysql_query('UPDATE profile_exceptions SET profile_id=\'5\' WHERE profile_id=\''.($max_p_id+5).'\' ') or die(mysql_error().'  At Line 91');

mysql_query('UPDATE staff SET profile_id=\'4\' WHERE profile_id=\''.($max_p_id+4).'\' ') or die(mysql_error().'  At Line 92');
mysql_query('UPDATE staff SET profile_id=\'5\' WHERE profile_id=\''.($max_p_id+5).'\' ') or die(mysql_error().'  At Line 93');


$get_staff=mysql_query('SELECT * FROM staff WHERE profile_id!=4') or die(mysql_error().'  At Line 22');
if(mysql_num_rows($get_staff)>0)
{
    while($get_staff_a=mysql_fetch_assoc($get_staff))
    {
    $last_login_au=date("Y-m-d H:m:s");    
    mysql_query('INSERT INTO login_authentication (user_id,profile_id,username,password,last_login,failed_login) VALUES 
    (\''.$get_staff_a['staff_id'].'\',\''.$get_staff_a['profile_id'].'\',\''.$get_staff_a['username'].'\',\''.$get_staff_a['password'].'\',\''.$last_login_au.'\',\''.$get_staff_a['failed_login'].'\')') or die(mysql_error().'  At Line 23');
    }
}

mysql_query("CREATE TABLE IF NOT EXISTS `staff_new` (
  `staff_id` int(8) NOT NULL AUTO_INCREMENT,
  `current_school_id` decimal(10,0) DEFAULT NULL,
  `title` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `first_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `middle_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `phone` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `profile` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `homeroom` varchar(5) CHARACTER SET utf8 DEFAULT NULL,
  `profile_id` decimal(10,0) DEFAULT NULL,
  `primary_language_id` int(8) DEFAULT NULL,
  `gender` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `ethnicity_id` int(8) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `alternate_id` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `name_suffix` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `second_language_id` int(8) DEFAULT NULL,
  `third_language_id` int(8) DEFAULT NULL,
  `is_disable` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `physical_disability` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  `disability_desc` VARCHAR( 225 ) DEFAULT NULL, 
  PRIMARY KEY (`staff_id`)
)") or die(mysql_query().'  At Line 24');
$get_s_d=mysql_query('SELECT staff_id,current_school_id,title,first_name,last_name,middle_name,phone,email,profile,homeroom,profile_id,is_disable FROM staff WHERE profile_id!=4') or die(mysql_query().'  At Line 25');
if(mysql_num_rows($get_s_d)>0)
{
    while($get_s_da=mysql_fetch_assoc($get_s_d))
    {
        if($get_s_da['is_disable']!='')
        mysql_query('INSERT INTO staff_new (staff_id,current_school_id,title,first_name,last_name,middle_name,phone,email,profile,homeroom,profile_id,is_disable) 
        VALUES (\''.$get_s_da['staff_id'].'\',\''.$get_s_da['current_school_id'].'\',\''.$get_s_da['title'].'\',\''.$get_s_da['first_name'].'\',\''.$get_s_da['last_name'].'\',\''.$get_s_da['middle_name'].'\',\''.$get_s_da['phone'].'\',\''.$get_s_da['email'].'\',\''.$get_s_da['profile'].'\',\''.$get_s_da['homeroom'].'\',\''.$get_s_da['profile_id'].'\',\''.$get_s_da['is_disable'].'\') ') or die(mysql_error().'  At Line 26');
        else
        mysql_query('INSERT INTO staff_new (staff_id,current_school_id,title,first_name,last_name,middle_name,phone,email,profile,homeroom,profile_id) 
        VALUES (\''.$get_s_da['staff_id'].'\',\''.$get_s_da['current_school_id'].'\',\''.$get_s_da['title'].'\',\''.$get_s_da['first_name'].'\',\''.$get_s_da['last_name'].'\',\''.$get_s_da['middle_name'].'\',\''.$get_s_da['phone'].'\',\''.$get_s_da['email'].'\',\''.$get_s_da['profile'].'\',\''.$get_s_da['homeroom'].'\',\''.$get_s_da['profile_id'].'\') ') or die(mysql_error().'  At Line 26 a');
        unset($get_s_da);
    }
}
mysql_query("CREATE TABLE students_new (
    student_id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    last_name character varying(50) NOT NULL,
    first_name character varying(50) NOT NULL,
    middle_name character varying(50),
    name_suffix character varying(3),
    gender character varying(255),
    ethnicity character varying(255),
    common_name character varying(255),
    social_security character varying(255),
    birthdate character varying(255),
    language character varying(255),
    estimated_grad_date character varying(255),
    alt_id character varying(50),
    email character varying(50),
    phone character varying(30),
    is_disable varchar(10) default NULL
)") or die(mysql_error().'  At Line 27');
mysql_query("INSERT INTO students_new SELECT student_id,last_name,first_name,middle_name,name_suffix,gender,
ethnicity,common_name,social_security,birthdate,language,estimated_grad_date,alt_id,email,phone,is_disable FROM students") or die(mysql_error().'  At Line 28');

$get_stu=mysql_query('SELECT student_id FROM students') or die(mysql_error().'  At Line 29');
while($get_stu_a=mysql_fetch_assoc($get_stu))
{
$school_id=mysql_query('SELECT school_id,syear FROM student_enrollment WHERE student_id='.$get_stu_a['student_id'].' ORDER BY ID DESC LIMIT 0,1') or die(mysql_error().'  At Line 30');
if(mysql_num_rows($school_id)>0)
$school_id=  mysql_fetch_assoc($school_id);
$syear=$school_id['syear'];
$school_id=$school_id['school_id'];

$get_add=mysql_query('SELECT a.* FROM address a,students_join_address sja WHERE sja.address_id=a.address_id AND sja.student_id='.$get_stu_a['student_id']) or die(mysql_error().'  At Line 31');
if(mysql_num_rows($get_add)>0)
{
    while($get_add_a=mysql_fetch_assoc($get_add))
    {
        if($get_add_a['prim_student_relation']!='')
        {
            mysql_query('INSERT INTO people_new (current_school_id,first_name,last_name,home_phone,work_phone,cell_phone,email,custody,profile,profile_id)
                      VALUES (\''.$school_id.'\',\''.$get_add_a['pri_first_name'].'\',\''.$get_add_a['pri_last_name'].'\',\''.$get_add_a['home_phone'].'\',\''.$get_add_a['work_phone'].'\',\''.$get_add_a['mobile_phone'].'\',\''.$get_add_a['email'].'\',\''.$get_add_a['prim_custody'].'\',\'parent\',4)') or die(mysql_error().'  At Line 32');
            $r_sid=mysql_query('SELECT MAX(staff_id) as STAFF_ID FROM people_new') or die(mysql_error().'  At Line 33');
            $r_sid=mysql_fetch_assoc($r_sid);
            $r_sid=$r_sid['STAFF_ID'];
            mysql_query('INSERT INTO students_join_people_new (student_id,person_id,emergency_type,relationship) VALUES 
                    (\''.$get_stu_a['student_id'].'\',\''.$r_sid.'\',\'Primary\',\''.$get_add_a['prim_student_relation'].'\') ') or die(mysql_error().'  At Line 34');
            mysql_query('INSERT INTO student_address (student_id,syear,school_id,address,street,city,state,zipcode,type,people_id) VALUES
                  (\''.$get_stu_a['student_id'].'\',\''.$syear.'\',\''.$school_id.'\',\''.$get_add_a['prim_address'].'\',\''.$get_add_a['prim_street'].'\',\''.$get_add_a['prim_city'].'\',\''.$get_add_a['prim_state'].'\',\''.$get_add_a['prim_zipcode'].'\',\'Primary\',\''.$r_sid.'\') ') or die(mysql_error().'  At Line 35');
        }
        if($get_add_a['sec_student_relation']!='')
        {
            mysql_query('INSERT INTO people_new (current_school_id,first_name,last_name,home_phone,work_phone,cell_phone,email,custody,profile,profile_id)
                      VALUES (\''.$school_id.'\',\''.$get_add_a['sec_first_name'].'\',\''.$get_add_a['sec_last_name'].'\',\''.$get_add_a['sec_home_phone'].'\',\''.$get_add_a['sec_work_phone'].'\',\''.$get_add_a['sec_mobile_phone'].'\',\''.$get_add_a['sec_email'].'\',\''.$get_add_a['sec_custody'].'\',\'parent\',4)') or die(mysql_error().'  At Line 36');
            $r_sid=mysql_query('SELECT MAX(staff_id) as STAFF_ID FROM people_new') or die(mysql_error().'  At Line 37');
            $r_sid=mysql_fetch_assoc($r_sid);
            $r_sid=$r_sid['STAFF_ID'];
            mysql_query('INSERT INTO students_join_people_new (student_id,person_id,emergency_type,relationship) VALUES 
                    (\''.$get_stu_a['student_id'].'\',\''.$r_sid.'\',\'Secondary\',\''.$get_add_a['sec_student_relation'].'\') ') or die(mysql_error().'  At Line 38');
            mysql_query('INSERT INTO student_address (student_id,syear,school_id,address,street,city,state,zipcode,type,people_id) VALUES
                  (\''.$get_stu_a['student_id'].'\',\''.$syear.'\',\''.$school_id.'\',\''.$get_add_a['sec_address'].'\',\''.$get_add_a['sec_street'].'\',\''.$get_add_a['sec_city'].'\',\''.$get_add_a['sec_state'].'\',\''.$get_add_a['sec_zipcode'].'\',\'Secondary\',\''.$r_sid.'\') ') or die(mysql_error().'  At Line 39');
        }
        mysql_query('INSERT INTO student_address (student_id,syear,school_id,address,street,city,state,zipcode,bus_pickup,bus_dropoff,bus_no,type) VALUES
                  (\''.$get_stu_a['student_id'].'\',\''.$syear.'\',\''.$school_id.'\',\''.$get_add_a['address'].'\',\''.$get_add_a['street'].'\',\''.$get_add_a['city'].'\',\''.$get_add_a['state'].'\',\''.$get_add_a['zipcode'].'\',\''.$get_add_a['bus_pickup'].'\',\''.$get_add_a['bus_dropoff'].'\',\''.$get_add_a['bus_no'].'\',\'Home Address\') ') or die(mysql_error().'  At Line 40');
    
        mysql_query('INSERT INTO student_address (student_id,syear,school_id,address,street,city,state,zipcode,type) VALUES
                  (\''.$get_stu_a['student_id'].'\',\''.$syear.'\',\''.$school_id.'\',\''.$get_add_a['mail_address'].'\',\''.$get_add_a['mail_street'].'\',\''.$get_add_a['mail_city'].'\',\''.$get_add_a['mail_state'].'\',\''.$get_add_a['mail_zipcode'].'\',\'Mail\') ') or die(mysql_error().'  At Line 41');
    
    }
}
$stu_j_p=mysql_query('SELECT sjp.*,p.* FROM students_join_people sjp,people p WHERE sjp.person_id=p.person_id AND sjp.student_id='.$get_stu_a['student_id']) or die(mysql_error().'  At Line 42');
if(mysql_num_rows($stu_j_p)>0)
{
    while($stu_jp_a=mysql_fetch_assoc($stu_j_p))
    {
    mysql_query('INSERT INTO people_new (current_school_id,first_name,last_name,home_phone,work_phone,cell_phone,email,custody,profile,profile_id)
    VALUES (\''.$school_id.'\',\''.$stu_jp_a['first_name'].'\',\''.$stu_jp_a['last_name'].'\',\''.$stu_jp_a['addn_home_phone'].'\',\''.$stu_jp_a['addn_work_phone'].'\',\''.$stu_jp_a['addn_mobile_phone'].'\',\''.$stu_jp_a['addn_email'].'\',\''.$stu_jp_a['custody'].'\',\'parent\',4)') or die(mysql_error().'  At Line 43');
    $r_sid=mysql_query('SELECT MAX(staff_id) as STAFF_ID FROM people_new') or die(mysql_error().'  At Line 44');
    $r_sid=mysql_fetch_assoc($r_sid);
    $r_sid=$r_sid['STAFF_ID'];
    if($stu_jp_a['emergency']!='')
    mysql_query('INSERT INTO students_join_people_new (student_id,person_id,is_emergency,emergency_type,relationship) VALUES 
    (\''.$get_stu_a['student_id'].'\',\''.$r_sid.'\',\''.$stu_jp_a['emergency'].'\',\'Other\',\''.$stu_jp_a['student_relation'].'\') ') or die(mysql_error().'  At Line 45');    
    else
    mysql_query('INSERT INTO students_join_people_new (student_id,person_id,emergency_type,relationship) VALUES 
    (\''.$get_stu_a['student_id'].'\',\''.$r_sid.'\',\'Other\',\''.$stu_jp_a['student_relation'].'\') ') or die(mysql_error().'  At Line 46');
    mysql_query('INSERT INTO student_address (student_id,syear,school_id,address,street,city,state,zipcode,type,people_id) VALUES
    (\''.$get_stu_a['student_id'].'\',\''.$syear.'\',\''.$school_id.'\',\''.$stu_jp_a['addn_address'].'\',\''.$stu_jp_a['addn_street'].'\',\''.$stu_jp_a['addn_city'].'\',\''.$stu_jp_a['addn_state'].'\',\''.$stu_jp_a['addn_zipcode'].'\',\'Other\',\''.$r_sid.'\') ') or die(mysql_error().'  At Line 47');
    }
}

$stu_j_u=mysql_query('SELECT sju.*,s.* FROM students_join_users sju,staff s WHERE sju.staff_id=s.staff_id AND sju.student_id='.$get_stu_a['student_id']) or die(mysql_error().'  At Line 48');
if(mysql_num_rows($stu_j_u)>0)
{
    while($stu_ju_a=mysql_fetch_assoc($stu_j_u))
    {
    $check_user_ex=mysql_query('SELECT * from login_authentication WHERE username=\''.$stu_ju_a['username'].'\' ');    
    if(mysql_num_rows($check_user_ex)==0)
    {
    mysql_query('INSERT INTO people_new (current_school_id,first_name,last_name,cell_phone,email,profile,profile_id)
    VALUES (\''.$school_id.'\',\''.$stu_ju_a['first_name'].'\',\''.$stu_ju_a['last_name'].'\',\''.$stu_ju_a['phone'].'\',\''.$stu_ju_a['email'].'\',\'parent\',4)') or die(mysql_error().'  At Line 49');
    $r_sid=mysql_query('SELECT MAX(staff_id) as STAFF_ID FROM people_new') or die(mysql_error().'  At Line 50');
    $r_sid=mysql_fetch_assoc($r_sid);
    $r_sid=$r_sid['STAFF_ID'];
    mysql_query('INSERT INTO students_join_people_new (student_id,person_id,emergency_type,relationship) VALUES 
    (\''.$get_stu_a['student_id'].'\',\''.$r_sid.'\',\'Other\',\'Other Family Member\') ') or die(mysql_error().'  At Line 51');    
    mysql_query('INSERT INTO student_address (student_id,syear,school_id,type,people_id) VALUES
    (\''.$get_stu_a['student_id'].'\',\''.$syear.'\',\''.$school_id.'\',\'Other\',\''.$r_sid.'\') ') or die(mysql_error().'  At Line 52');
    $last_login_au=date("Y-m-d H:m:s");
    mysql_query('INSERT INTO login_authentication (user_id,profile_id,username,password,last_login,failed_login) VALUES 
    (\''.$r_sid.'\',4,\''.$stu_ju_a['username'].'\',\''.$stu_ju_a['password'].'\',\''.$last_login_au.'\',\''.$stu_ju_a['failed_login'].'\')') or die(mysql_error().'  At Line 53');
    }
    else
    {
        $check_user_exa=mysql_fetch_assoc($check_user_ex);
        mysql_query('INSERT INTO students_join_people_new (student_id,person_id,emergency_type,relationship) VALUES 
        (\''.$get_stu_a['student_id'].'\',\''.$check_user_exa['user_id'].'\',\'Other\',\'Other Family Member\') ') or die(mysql_error().'  At Line 54');    
        mysql_query('INSERT INTO student_address (student_id,syear,school_id,type,people_id) VALUES
        (\''.$get_stu_a['student_id'].'\',\''.$syear.'\',\''.$school_id.'\',\'Other\',\''.$check_user_exa['user_id'].'\') ') or die(mysql_error().'  At Line 55');
    }
    
    }
}
}
mysql_query('RENAME TABLE `student_medical` TO `student_immunization`') or die(mysql_error().'  At Line 56');
mysql_query('RENAME TABLE `attendance_calendars` TO `school_calendars`') or die(mysql_error().'  At Line 57');
mysql_query("CREATE TABLE IF NOT EXISTS `staff_field_categories` (
  `id` int(8) NOT NULL DEFAULT '0',
  `title` varchar(100) DEFAULT NULL,
  `sort_order` decimal(10,0) DEFAULT NULL,
  `include` varchar(100) DEFAULT NULL,
  `admin` char(1) DEFAULT NULL,
  `teacher` char(1) DEFAULT NULL,
  `parent` char(1) DEFAULT NULL,
  `none` char(1) DEFAULT NULL
)") or die(mysql_error().'  At Line 58');
mysql_query("INSERT INTO `staff_field_categories` (`id`, `title`, `sort_order`, `include`, `admin`, `teacher`, `parent`, `none`) VALUES
(1, 'Demographic Info', 1, NULL, 'Y', 'Y', 'Y', 'Y'),
(2, 'Addresses & Contacts', 2, NULL, 'Y', 'Y', 'Y', 'Y'),
(3, 'School Information', 3, NULL, 'Y', 'Y', 'Y', 'Y'),
(4, 'Certification Information', 4, NULL, 'Y', 'Y', 'Y', 'Y')") or die(mysql_error().'  At Line 59');
//////////////////////////////////////////////////////////////////////////////////
mysql_query("CREATE TABLE IF NOT EXISTS `student_gpa_calculated_new` (
  `student_id` decimal(10,0) DEFAULT NULL,
  `marking_period_id` int(11) DEFAULT NULL,
  `mp` varchar(4) DEFAULT NULL,
  `gpa` decimal(10,2) DEFAULT NULL,
  `weighted_gpa` decimal(10,2) DEFAULT NULL,
  `unweighted_gpa` decimal(10,2) DEFAULT NULL,
  `class_rank` decimal(10,0) DEFAULT NULL,
  `grade_level_short` varchar(100) DEFAULT NULL,
  `cgpa` decimal(10,2) DEFAULT NULL,
  `cum_unweighted_factor` decimal(10,6) DEFAULT NULL,
  KEY `student_gpa_calculated_ind1` (`marking_period_id`,`student_id`) USING BTREE
)") or die(mysql_error().' At Line 94');
$gpa_calc=mysql_query('SELECT * FROM student_gpa_calculated') or die(mysql_error().' At Line 95');
if(mysql_num_rows($gpa_calc)>0)
{
    while($gpa_calc_a=mysql_fetch_assoc($gpa_calc))
    {
        $cgpa=mysql_query('SELECT cgpa FROM student_gpa_running WHERE student_id='.$gpa_calc_a['student_id'].' AND marking_period_id='.$gpa_calc_a['marking_period_id']) or die(mysql_error().' At Line 96');
        if(mysql_num_rows($cgpa)>0)
        {   
            $cgpa_a=mysql_fetch_assoc($cgpa);
            $cgpa_a=$cgpa_a['cgpa'];
        }
        $c_uf=mysql_query('SELECT cum_unweighted_factor FROM student_mp_stats WHERE student_id='.$gpa_calc_a['student_id'].' AND marking_period_id='.$gpa_calc_a['marking_period_id']) or die(mysql_error().' At Line 97');
        if(mysql_num_rows($c_uf)>0)
        {
            $c_uf_a=mysql_fetch_assoc($c_uf);
            $c_uf_a=$c_uf_a['cum_unweighted_factor'];
        }
//        $q_string='INSERT INTO student_gpa_calculated_new (student_id,marking_period_id,mp,gpa,weighted_gpa,unweighted_gpa,class_rank,grade_level_short';
//        $q_string_values='\''.$gpa_calc_a['student_id'].'\',\''.$gpa_calc_a['marking_period_id'].'\',\''.$gpa_calc_a['mp'].'\',\''.$gpa_calc_a['gpa'].'\',\''.$gpa_calc_a['weighted_gpa'].'\',\''.$gpa_calc_a['unweighted_gpa'].'\',\''.$gpa_calc_a['class_rank'].'\',\''.$gpa_calc_a['grade_level_short'].'\' ';
        foreach($gpa_calc_a as $gpa_i=>$gpa_d)
        {
            if($gpa_d!='')
            {
            $q_string_columns.=$gpa_i.',';
            $q_string_values.='\''.$gpa_d.'\',';
            }
        }
        if($cgpa_a!='')
        {
         $q_string_columns.='cgpa,';
         $q_string_values.='\''.$cgpa_a.'\',';
        }
        if($c_uf_a!='')
        {
         $q_string_columns.='cum_unweighted_factor,';
         $q_string_values.='\''.$c_uf_a.'\',';
        }
        $q_string='INSERT INTO student_gpa_calculated_new ('.substr($q_string_columns,0,-1).') VALUES ('.substr($q_string_values,0,-1).')';
        mysql_query($q_string) or die(mysql_error().' At Line 98');
        unset($cgpa);
        unset($c_uf);
        unset($q_string);
        unset($q_string_columns);
        unset($q_string_values);
    }
}
/////////////////////Creating student_field_categories////////////////////////////
mysql_query('CREATE TABLE student_field_categories_new (
    id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title character varying(100),
    sort_order numeric,
    include character varying(100)
)') or die (mysql_error().' At Line 99');
$get_m_stfid=mysql_query('SELECT MAX(ID) as M_ID from student_field_categories');
$get_m_stfid=mysql_fetch_assoc($get_m_stfid);
$get_m_stfid=$get_m_stfid['M_ID'];
mysql_query('INSERT INTO student_field_categories_new SELECT * FROM student_field_categories WHERE ID<6') or die(mysql_error().' At Line 100');
mysql_query('INSERT INTO student_field_categories_new (title,sort_order) VALUES (\'Enrollment Info\',6)') or die(mysql_error().' At Line 101');
if($get_m_stfid>=6)
{
    $sfc=mysql_query('SELECT * FROM student_field_categories WHERE id>=6');
    while($sfc_a=mysql_fetch_assoc($sfc))
    {
        mysql_query('INSERT INTO student_field_categories_new (title,sort_order) VALUES (\''.$sfc_a['title'].'\',\''.$sfc_a['sort_order'].'\')') or die(mysql_error().' At Line 102');
        
    }
    mysql_query('UPDATE custom_fields SET category_id=category_id+1 WHERE category_id>=6') or die(mysql_error().' At Line 103');
}
$catg=mysql_query('SELECT * FROM student_field_categories_new WHERE id>=6') ;
while($catg_a=mysql_fetch_assoc($catg))
{
    
$mname='students/Student.php&category_id='.$catg_a['id'];
$catg_ex=mysql_query('SELECT * FROM profile_exceptions WHERE modname=\''.$mname.'\' ') or die(mysql_error().' At Line 115');
if(mysql_num_rows($catg_ex)==0)
{
mysql_query('INSERT INTO profile_exceptions (profile_id,modname,can_use,can_edit) VALUES (0,\''.$mname.'\',\'Y\',\'Y\')')or die (mysql_error().' At Line 113');
mysql_query('INSERT INTO profile_exceptions (profile_id,modname,can_use,can_edit) VALUES (1,\''.$mname.'\',\'Y\',\'Y\')')or die (mysql_error().' At Line 114');
}
}
unset($catg_a);
unset($catg);
unset($catg_ex);
unset($mname);
unset($get_m_stfid);
unset($sfc);
unset($sfc_a);

///////////////////////////////////////////////////////////////////////////////////
///////////////////////////Adding Custom Coulmns Into students/////////////////////

$chk_st_cl=mysql_query("SHOW COLUMNS FROM  `students` LIKE  'CUSTOM_%' ") or die(mysql_error().'  At Line 109');
if(mysql_num_rows($chk_st_cl)>0)
{
    while($chk_st_cl_a=mysql_fetch_assoc($chk_st_cl))
    {
        if($chk_st_cl_a['Default']!='')
        mysql_query("ALTER TABLE  `students_new` ADD  ".$chk_st_cl_a['Field']." ".$chk_st_cl_a['Type']." DEFAULT '".$chk_st_cl_a['Default']."' ") or die(mysql_error().' At Line 110');
        else
        mysql_query("ALTER TABLE  `students_new` ADD  ".$chk_st_cl_a['Field']." ".$chk_st_cl_a['Type']."") or die(mysql_error().' At Line 111');
        
        $f_vals=mysql_query('SELECT * from students');
        if(mysql_num_rows($f_vals)>0)
        {
            while($f_vals_a=mysql_fetch_assoc($f_vals))
            {
            mysql_query('UPDATE students_new SET '.$chk_st_cl_a['Field'].'=\''.$f_vals_a[$chk_st_cl_a['Field']].'\' WHERE student_id='.$f_vals_a['student_id']) or die (mysql_error().' At Line 112');
            }
        }
        unset($f_vals);
        unset($f_vals_a);
    }
}
//////////////////////////////////////////////////////////////////////////////////
/////Deleting unwanted tables/////////////////////////////////////////////////////
mysql_query('DROP table config') or die(mysql_error().'  At Line 60');
mysql_query('DROP table old_course_weights') or die(mysql_error().'  At Line 61');
mysql_query('DROP table student_gpa_running') or die(mysql_error().'  At Line 62');
mysql_query('DROP table student_gpa_calculated') or die(mysql_error().'  At Line 105');
mysql_query('DROP table students_join_users') or die(mysql_error().'  At Line 63');
mysql_query('DROP table students_join_people') or die(mysql_error().'  At Line 64');
mysql_query('DROP table people') or die(mysql_error().'  At Line 65');
mysql_query('DROP table user_profiles') or die(mysql_error().'  At Line 66');
mysql_query('DROP table staff') or die(mysql_error().'  At Line 67');
mysql_query('DROP table students') or die(mysql_error().'  At Line 68');   
mysql_query('DROP table student_mp_stats') or die(mysql_error().'  At Line 69');   
mysql_query('DROP view student_contacts') or die(mysql_error().'  At Line 70');   
mysql_query('DROP table student_field_categories') or die(mysql_error().'  At Line 106');   
//////////////////////////////////////////////////////////////////////////////////
/////Renaming tables with suffix '_new' to original format/////////////////////////////////////////////
mysql_query('RENAME TABLE `students_join_people_new` TO `students_join_people`') or die(mysql_error().'  At Line 71');
mysql_query('RENAME TABLE `people_new` TO `people`') or die(mysql_error().'  At Line 72');
mysql_query('RENAME TABLE `user_profiles_new` TO `user_profiles`') or die(mysql_error().'  At Line 73');
mysql_query('RENAME TABLE `staff_new` TO `staff`') or die(mysql_error().'  At Line 74');
mysql_query('RENAME TABLE `students_new` TO `students`') or die(mysql_error().'  At Line 75');
mysql_query('RENAME TABLE `student_gpa_calculated_new` TO `student_gpa_calculated`') or die(mysql_error().'  At Line 107');
mysql_query('RENAME TABLE `student_field_categories_new` TO `student_field_categories`') or die(mysql_error().'  At Line 108');
/////////////////////////////////////////////////////////////////////////////////////////////////////////
mysql_query("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('1','schoolsetup/SchoolCustomFields.php','Y','Y')");
mysql_query("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('1','users/Staff.php','Y','Y')");
mysql_query("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('1','users/Staff.php&staff_id=new','Y','Y')");
mysql_query("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('1','users/Exceptions_staff.php','Y','Y')");
mysql_query("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('1','users/StaffFields.php','Y','Y')");
mysql_query("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('1','users/Staff.php&category_id=1','Y','Y')");
mysql_query("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('1','users/Staff.php&category_id=2','Y','Y')");
mysql_query("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('1','users/Staff.php&category_id=3','Y','Y')");
mysql_query("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('1','users/Staff.php&category_id=4','Y','Y')");
mysql_query("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('1','messaging/Inbox.php','Y','Y')");
mysql_query("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('1','messaging/Compose.php','Y','Y')");
mysql_query("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('1','messaging/SentMail.php','Y','Y')");
mysql_query("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('1','messaging/Trash.php','Y','Y')");
mysql_query("INSERT INTO profile_exceptions (PROFILE_ID,MODNAME,CAN_USE,CAN_EDIT) values('1','messaging/Group.php','Y','Y')");

mysql_query("INSERT INTO `profile_exceptions` (`profile_id`, `modname`, `can_use`, `can_edit`) VALUES
(0, 'students/Student.php&category_id=6', 'Y', 'Y'),
(0, 'users/User.php&category_id=5', 'Y', 'Y'),
(0, 'schoolsetup/PortalNotes.php', 'Y', 'Y'),
(0, 'schoolsetup/Schools.php', 'Y', 'Y'),
(0, 'schoolsetup/Schools.php?new_school=true', 'Y', 'Y'),
(0, 'schoolsetup/CopySchool.php', 'Y', 'Y'),
(0, 'schoolsetup/MarkingPeriods.php', 'Y', 'Y'),
(0, 'schoolsetup/Calendar.php', 'Y', 'Y'),
(0, 'schoolsetup/Periods.php', 'Y', 'Y'),
(0, 'schoolsetup/GradeLevels.php', 'Y', 'Y'),
(0, 'schoolsetup/Rollover.php', 'Y', 'Y'),
(0, 'schoolsetup/Courses.php', 'Y', 'Y'),
(0, 'schoolsetup/CourseCatalog.php', 'Y', 'Y'),
(0, 'schoolsetup/PrintCatalog.php', 'Y', 'Y'),
(0, 'schoolsetup/PrintCatalogGradeLevel.php', 'Y', 'Y'),
(0, 'schoolsetup/PrintAllCourses.php', 'Y', 'Y'),
(0, 'schoolsetup/UploadLogo.php', 'Y', 'Y'),
(0, 'schoolsetup/TeacherReassignment.php', 'Y', 'Y'),
(0, 'students/Student.php', 'Y', 'Y'),
(0, 'students/Student.php&include=GeneralInfoInc&student_id=new', 'Y', 'Y'),
(0, 'students/AssignOtherInfo.php', 'Y', 'Y'),
(0, 'students/AddUsers.php', 'Y', 'Y'),
(0, 'students/AdvancedReport.php', 'Y', 'Y'),
(0, 'students/AddDrop.php', 'Y', 'Y'),
(0, 'students/Letters.php', 'Y', 'Y'),
(0, 'students/MailingLabels.php', 'Y', 'Y'),
(0, 'students/StudentLabels.php', 'Y', 'Y'),
(0, 'students/PrintStudentInfo.php', 'Y', 'Y'),
(0, 'students/PrintStudentContactInfo.php', 'Y', 'Y'),
(0, 'students/GoalReport.php', 'Y', 'Y'),
(0, 'students/StudentFields.php', 'Y', 'Y'),
(0, 'students/AddressFields.php', 'Y', 'Y'),
(0, 'students/PeopleFields.php', 'Y', 'Y'),
(0, 'students/EnrollmentCodes.php', 'Y', 'Y'),
(0, 'students/Upload.php?modfunc=edit', 'Y', 'Y'),
(0, 'students/Upload.php', 'Y', 'Y'),
(0, 'students/Student.php&category_id=1', 'Y', 'Y'),
(0, 'students/Student.php&category_id=3', 'Y', 'Y'),
(0, 'students/Student.php&category_id=2', 'Y', 'Y'),
(0, 'students/Student.php&category_id=4', 'Y', 'Y'),
(0, 'students/StudentReenroll.php', 'Y', 'Y'),
(0, 'students/EnrollmentReport.php', 'Y', 'Y'),
(0, 'users/User.php', 'Y', 'Y'),
(0, 'users/User.php&category_id=1', 'Y', 'Y'),
(0, 'users/User.php&category_id=2', 'Y', 'Y'),
(0, 'users/User.php&staff_id=new', 'Y', 'Y'),
(0, 'users/AddStudents.php', 'Y', 'Y'),
(0, 'users/Preferences.php', 'Y', 'Y'),
(0, 'users/Profiles.php', 'Y', 'Y'),
(0, 'users/Exceptions.php', 'Y', 'Y'),
(0, 'users/UserFields.php', 'Y', 'Y'),
(0, 'users/TeacherPrograms.php?include=grades/InputFinalGrades.php', 'Y', 'Y'),
(0, 'users/TeacherPrograms.php?include=grades/grades.php', 'Y', 'Y'),
(0, 'users/TeacherPrograms.php?include=attendance/TakeAttendance.php', 'Y', 'Y'),
(0, 'users/TeacherPrograms.php?include=attendance/Missing_Attendance.php', 'Y', 'Y'),
(0, 'users/TeacherPrograms.php?include=eligibility/EnterEligibility.php', 'Y', 'Y'),
(0, 'users/UploadUserPhoto.php', 'Y', 'Y'),
(0, 'users/UploadUserPhoto.php?modfunc=edit', 'Y', 'Y'),
(0, 'users/UserAdvancedReport.php', 'Y', 'Y'),
(0, 'scheduling/Schedule.php', 'Y', 'Y'),
(0, 'scheduling/Requests.php', 'Y', 'Y'),
(0, 'scheduling/MassSchedule.php', 'Y', 'Y'),
(0, 'scheduling/MassRequests.php', 'Y', 'Y'),
(0, 'scheduling/MassDrops.php', 'Y', 'Y'),
(0, 'scheduling/ScheduleReport.php', 'Y', 'Y'),
(0, 'scheduling/RequestsReport.php', 'Y', 'Y'),
(0, 'scheduling/UnfilledRequests.php', 'Y', 'Y'),
(0, 'scheduling/IncompleteSchedules.php', 'Y', 'Y'),
(0, 'scheduling/AddDrop.php', 'Y', 'Y'),
(0, 'scheduling/PrintSchedules.php', 'Y', 'Y'),
(0, 'scheduling/PrintRequests.php', 'Y', 'Y'),
(0, 'scheduling/PrintClassLists.php', 'Y', 'Y'),
(0, 'scheduling/PrintClassPictures.php', 'Y', 'Y'),
(0, 'scheduling/Courses.php', 'Y', 'Y'),
(0, 'scheduling/Scheduler.php', 'Y', 'Y'),
(0, 'scheduling/ViewSchedule.php', 'Y', 'Y'),
(0, 'grades/ReportCards.php', 'Y', 'Y'),
(0, 'grades/CalcGPA.php', 'Y', 'Y'),
(0, 'grades/Transcripts.php', 'Y', 'Y'),
(0, 'grades/TeacherCompletion.php', 'Y', 'Y'),
(0, 'grades/GradeBreakdown.php', 'Y', 'Y'),
(0, 'grades/FinalGrades.php', 'Y', 'Y'),
(0, 'grades/GPARankList.php', 'Y', 'Y'),
(0, 'grades/ReportCardGrades.php', 'Y', 'Y'),
(0, 'grades/ReportCardComments.php', 'Y', 'Y'),
(0, 'grades/FixGPA.php', 'Y', 'Y'),
(0, 'grades/EditReportCardGrades.php', 'Y', 'Y'),
(0, 'grades/EditHistoryMarkingPeriods.php', 'Y', 'Y'),
(0, 'grades/HistoricalReportCardGrades.php', 'Y', 'Y'),
(0, 'attendance/Administration.php', 'Y', 'Y'),
(0, 'attendance/AddAbsences.php', 'Y', 'Y'),
(0, 'attendance/AttendanceData.php?list_by_day=true', 'Y', 'Y'),
(0, 'attendance/Percent.php', 'Y', 'Y'),
(0, 'attendance/Percent.php?list_by_day=true', 'Y', 'Y'),
(0, 'attendance/DailySummary.php', 'Y', 'Y'),
(0, 'attendance/StudentSummary.php', 'Y', 'Y'),
(0, 'attendance/TeacherCompletion.php', 'Y', 'Y'),
(0, 'attendance/DuplicateAttendance.php', 'Y', 'Y'),
(0, 'attendance/AttendanceCodes.php', 'Y', 'Y'),
(0, 'attendance/FixDailyAttendance.php', 'Y', 'Y'),
(0, 'eligibility/Student.php', 'Y', 'Y'),
(0, 'eligibility/AddActivity.php', 'Y', 'Y'),
(0, 'eligibility/StudentList.php', 'Y', 'Y'),
(0, 'eligibility/TeacherCompletion.php', 'Y', 'Y'),
(0, 'eligibility/Activities.php', 'Y', 'Y'),
(0, 'eligibility/EntryTimes.php', 'Y', 'Y'),
(0, 'tools/LogDetails.php', 'Y', 'Y'),
(0, 'tools/DeleteLog.php', 'Y', 'Y'),
(0, 'tools/Backup.php', 'Y', 'Y'),
(0, 'tools/Rollover.php', 'Y', 'Y'),
(0, 'students/Upload.php', 'Y', 'Y'),
(0, 'students/Upload.php?modfunc=edit', 'Y', 'Y'),
(0, 'schoolsetup/SystemPreference.php', 'Y', 'Y'),
(0, 'students/Student.php&category_id=5', 'Y', 'Y'),
(0, 'grades/HonorRoll.php', 'Y', 'Y'),
(0, 'users/TeacherPrograms.php?include=grades/ProgressReports.php', 'Y', 'Y'),
(0, 'users/User.php&category_id=2', 'Y', 'Y'),
(0, 'grades/HonorRollSetup.php', 'Y', 'Y'),
(0, 'grades/AdminProgressReports.php', 'Y', 'Y'),
(0, 'Billing/LedgerCard.php', 'Y', 'Y'),
(0, 'Billing/Balance_Report.php', 'Y', 'Y'),
(0, 'Billing/DailyTransactions.php', 'Y', 'Y'),
(0, 'Billing/PaymentHistory.php', 'Y', 'Y'),
(0, 'Billing/Fee.php', 'Y', 'Y'),
(0, 'Billing/StudentPayments.php', 'Y', 'Y'),
(0, 'Billing/MassAssignFees.php', 'Y', 'Y'),
(0, 'Billing/MassAssignPayments.php', 'Y', 'Y'),
(0, 'Billing/SetUp.php', 'Y', 'Y'),
(0, 'Billing/SetUp_FeeType.php', 'Y', 'Y'),
(0, 'Billing/SetUp_PayPal.php', 'Y', 'Y'),
(0, 'users/Staff.php', 'Y', 'Y'),
(0, 'users/Staff.php&staff_id=new', 'Y', 'Y'),
(0, 'users/Exceptions_staff.php', 'Y', 'Y'),
(0, 'users/StaffFields.php', 'Y', 'Y'),
(0, 'users/Staff.php&category_id=1', 'Y', 'Y'),
(0, 'users/Staff.php&category_id=2', 'Y', 'Y'),
(0, 'users/Staff.php&category_id=3', 'Y', 'Y'),
(0, 'users/Staff.php&category_id=4', 'Y', 'Y'),
(0, 'schoolsetup/SchoolCustomFields.php', 'Y', 'Y'),
(0, 'messaging/Inbox.php', 'Y', NULL),
(0, 'messaging/Compose.php', 'Y', NULL),
(0, 'messaging/SentMail.php', 'Y', NULL),
(0, 'messaging/Trash.php', 'Y', NULL),
(0, 'messaging/Group.php', 'Y', NULL)");

$check_sa=mysql_query('SELECT s.staff_id from staff s,login_authentication la WHERE la.user_id=s.staff_id and la.username=\'os4ed\' ');
if(mysql_num_rows($check_sa)>0)
{
    $check_sa=mysql_fetch_assoc($check_sa);
    mysql_query('UPDATE staff SET profile_id=0 WHERE staff_id='.$check_sa['staff_id']);
    mysql_query('UPDATE login_authentication SET profile_id=0 WHERE user_id='.$check_sa['staff_id']);
}
if($proceed['name']){
        
                    $dummyFile = "dummy.txt";
                    $fpt = fopen($dummyFile, 'w');

                    if ($fpt == FALSE)
                    {
                        die(show_error1().' Show Error 1');
                    }
                    else
                    {
                        unlink($dummyFile);
                    }
                    fclose($fpt);
    
	$date_time=date("m-d-Y");
    $Export_FileName=$mysql_database.'_'.$date_time.'.sql';
	$myFile = "UpgradeInc.sql";
    executeSQL($myFile);
	
	        exec("mysqldump -n -t -c --skip-add-locks --skip-disable-keys --skip-triggers --user $dbUser --password='$dbPass' $mysql_database > $Export_FileName");

                    $res_student_field='SHOW COLUMNS FROM '.table_to_upper('students',$version).' WHERE FIELD LIKE "CUSTOM_%"';

	$objCustomStudents=new custom($mysql_database);
	$objCustomStudents->set($res_student_field,'students');
	
	$res_staff_field='SHOW COLUMNS FROM '.table_to_upper('staff',$version).' WHERE FIELD LIKE "CUSTOM_%"';
	$objCustomStaff=new custom($mysql_database);
	$objCustomStaff->set($res_staff_field,'staff');
	
	mysql_query("drop database $mysql_database");
	mysql_query("CREATE DATABASE $mysql_database CHARACTER SET utf8 COLLATE utf8_general_ci");
	mysql_select_db($mysql_database);
	
        $myFile = "OpensisSchemaMysqlInc.sql";
    executeSQL($myFile);
	
	//execute custome field for student
	foreach($objCustomStudents->customQueryString as $query){
	mysql_query($query);
	}
	//execute custome field for satff
	foreach($objCustomStaff->customQueryString as $query){
	mysql_query($query);
	}
	

                    $myFile = "OpensisProcsMysqlInc.sql";
                    executeSQL($myFile);

                    //=====================For version prior than 4.9 only====================================
                    if($version!='5.0' || $version!='5.1' || $version!='5.2' || $version!='5.3')
                    {
                        $Export_FileName=to_upper_tables_to_import($Export_FileName);
                    }
                    //=========================================================

                    exec("mysql --user $dbUser --password='$dbPass' $mysql_database < $Export_FileName",$result,$status);
                    if($status!=0)
                    {
                        die(show_error1('db').' Show Error 2');
                    }
                    if($version!='5.0')
                    {
                        unlink($Export_FileName);
                    }
                    $myFile = "OpensisTriggerMysqlInc.sql";
                    executeSQL($myFile);
                    
	mysql_query("delete from app");
	$appTable="INSERT INTO `app` (`name`, `value`) VALUES
('version', '5.4'),
('date', 'December 01, 2013'),
('build', '01122013001'),
('update', '0'),
('last_updated', 'December 01, 2013')";
	mysql_query($appTable);
	$custom_insert=mysql_query("select count(*) from custom_fields where title in('Ethnicity','Common Name','Physician','Physician Phone','Preferred Hospital','Gender','Email','Phone','Language')");
	$custom_insert=mysql_fetch_array($custom_insert);
	$custom_insert=$custom_insert[0];
	if($custom_insert<9){
	$custom_insert="INSERT INTO `custom_fields` (`type`, `search`, `title`, `sort_order`, `select_options`, `category_id`, `system_field`, `required`, `default_selection`, `hide`) VALUES
('text', NULL, 'Ethnicity', 3, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Common Name', 2, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Physician', 6, NULL, 2, 'Y', NULL, NULL, NULL),
('text', NULL, 'Physician Phone', 7, NULL, 2, 'Y', NULL, NULL, NULL),
('text', NULL, 'Preferred Hospital', 8, NULL, 2, 'Y', NULL, NULL, NULL),
('text', NULL, 'Gender', 5, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Email', 6, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Phone', 9, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Language', 8, NULL, 1, 'Y', NULL, NULL, NULL);";
mysql_query($custom_insert);
	}
	$login_msg=mysql_query("SELECT COUNT(*) FROM login_message WHERE 1");
	$login_msg=mysql_fetch_array($login_msg);
	$login_msg=$login_msg[0];
	if($login_msg<1){
	$login_msg="INSERT INTO `login_message` (`id`, `message`, `display`) VALUES
(1, 'This is a restricted network. Use of this network, its equipment, and resources is monitored at all times and requires explicit permission from the network administrator. If you do not have this permission in writing, you are violating the regulations of this network and can and will be prosecuted to the fullest extent of law. By continuing into this system, you are acknowledging that you are aware of and agree to these terms.', 'Y')";
mysql_query($login_msg);
	}
	
	$syear=mysql_fetch_assoc(mysql_query("select MAX(syear) as year, MIN(start_date) as start from school_years"));
	$_SESSION['syear']=$syear['year'];
                  $max_syear=$syear['year'];
                  $start_date=$syear['start'];
//=============================4.8.1 To 4.9===================================
if($version!='5.0' && $version!='4.9' && $version!='5.1' && $version!='5.2' && $version!='5.3')
{
        $up_sql="INSERT INTO student_enrollment_codes(syear,title,short_name,type)VALUES
        (".$max_syear.",'Transferred out','TRAN','TrnD'),
        (".$max_syear.",'Transferred in','TRAN','TrnE'),
        (".$max_syear.",'Rolled over','ROLL','Roll'); ";
        mysql_query($up_sql) or die(show_error1().' Show Error 3');

        $up_sql ="INSERT INTO profile_exceptions (profile_id, modname, can_use, can_edit) VALUES
            (3, 'scheduling/PrintSchedules.php','Y',NULL),
            (1, 'scheduling/ViewSchedule.php', 'Y', NULL),
            (2, 'scheduling/ViewSchedule.php', 'Y', NULL),
            (1, 'schoolsetup/UploadLogo.php', 'Y', 'Y'); ";
        mysql_query($up_sql) or die(show_error1().' Show Error 4');

        $up_sql ="INSERT INTO program_config (program, title, value) VALUES
            ('MissingAttendance', 'LAST_UPDATE','".$start_date."'); ";
        mysql_query($up_sql) or die(show_error1().' Show Error 5');

        $up_sql ="UPDATE profile_exceptions SET modname='scheduling/ViewSchedule.php' WHERE modname='scheduling/Schedule.php' AND (profile_id=0 OR profile_id=3);";
        mysql_query($up_sql) or die(show_error1().' Show Error 6');
}
//====================================================================
	mysql_query("UPDATE schedule SET dropped='Y' WHERE end_date IS NOT NULL AND end_date < CURDATE() AND dropped='N'");
	header('Location: Step5.php');
	unset($objCustomStudents);
	unset($objCustomStaff);
}else{
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../styles/Installer.css" />
</head>
<body>
<div class="heading2">Warning
<div style="height:270px;">
<br /><br />
 <table border="0" cellspacing="6" cellpadding="3" align="center">
            <tr>
			 <td colspan="2" align="center">
		<p>	The database you have chosen is not compliant with openSIS-CE ver 4.6 or 4.7 or 4.8X or 4.9 or 5.0 or 5.1 or 5.2 We are unable to proceed.</p>

<p>Click Retry to select another database, or Exit to quit the installation.
</p>
		</td>
			</tr>
            <tr>
            	<td colspan="2" style="height:100px;">&nbsp;</td>
            </tr>
			<tr>


  <td align="left"><a href="Selectdb.php"><img src="images/retry.png"  alt="Retry"  border="0"/></a></td>
    <td align="right"><a href="Step0.php" ><img src="images/exit.png" alt="Exit" border="0" /></a></td>


	          </tr>
	        </table>
</div>
</div>
</body>
</html>
<?php }

function executeSQL($myFile)
{	
	$sql = file_get_contents($myFile);
	$sqllines = explode("\n",$sql);
	$cmd = '';
	$delim = false;
	foreach($sqllines as $l)
	{
		if(preg_match('/^\s*--/',$l) == 0)
		{
			if(preg_match('/DELIMITER \$\$/',$l) != 0)
			{	
				$delim = true;
			}
			else
			{
				if(preg_match('/DELIMITER ;/',$l) != 0)
				{
					$delim = false;
				}
				else
				{
					if(preg_match('/END\$\$/',$l) != 0)
					{
						$cmd .= ' END';
					}
					else
					{
						$cmd .= ' ' . $l . "\n";
					}
				}
				if(preg_match('/.+;/',$l) != 0 && !$delim)
				{
					$result = mysql_query($cmd) or die(show_error1().' Show Error 7');
					$cmd = '';
				}
			}
		}
	}
}

function show_error1($msg='')
{
    if($msg=='')
        $msg='Application does not have permission to write into install directory.';
    elseif($msg=='db')
        $msg='Your database is not compatible with openSIS-CE<br />Please take this screen shot and send it to your openSIS representative for resolution.';
    $err .= "
<html>
<head>
<link rel='stylesheet' type='text/css' href='../styles/Installer.css' />
</head>
<body>

<div style='height:280px;'>

<br /><br /><span class='header_txt'></span>

<div align='center'>
$msg
</div>
<div style='height:50px;'>&nbsp;</div>";
$err.="<div align='center'><a href='Selectdb.php?mod=upgrade'><img src='images/retry.png' border='0' /></a> &nbsp; &nbsp; <a href='Step0.php'><img src='images/exit.png' border='0' /></a></div>";
$err.="</div></body></html>";
echo $err;
}

function table_to_upper($table,$ver)
{
    if($ver=='4.6' || $ver=='4.7' || $ver=='4.8' || $ver=='4.8.1' || $ver=='4.9')
        $return=  strtoupper ($table);
    else
        $return=$table;
    return $return;
}

function to_upper_tables_to_import($input_file)
{
    $output_file='temp_opensis5.0.sql';
    $handle = @fopen($input_file, "r"); // Open file form read.
    $str='';
    if ($handle) {
        while (!feof($handle)) // Loop til end of file.
        {
            $buffer = fgets($handle, 4096); // Read a line.
            if(substr($buffer, 0,11)=='INSERT INTO')
            {
                $arr_line=explode(' ', $buffer);
                $arr_line[2]= strtolower($arr_line[2]);
                $str_line=implode(' ', $arr_line);
                $str .= $str_line;
            }
            else
            {
                $str .=$buffer;
            }
        }
        fclose($handle); // Close the file.
       
        $f = fopen($output_file, "w"); 
    fwrite($f, $str); 
    }
    return $output_file;
}
?>

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
function GetStaffList(& $extra)
{	global $profiles_RET;
	$functions = array('PROFILE'=>'makeProfile');
	switch(User('PROFILE'))
	{
		case 'admin':
		$profiles_RET = DBGet(DBQuery('SELECT * FROM user_profiles'),array(),array('ID'));
                     $sql = 'SELECT DISTINCT CONCAT(s.LAST_NAME,  \' \' ,s.FIRST_NAME) AS FULL_NAME,
                        la.USERNAME,s.PROFILE,s.PROFILE_ID,s.IS_DISABLE,s.STAFF_ID '.$extra['SELECT'].'
                        FROM
                        people s '.$extra['FROM'].',login_authentication la,students st,student_enrollment ssm
                        WHERE
                        st.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='.UserSyear().'  AND s.PROFILE IS NOT NULL AND s.PROFILE_ID=(SELECT ID FROM user_profiles WHERE TITLE=\'parent\') AND s.STAFF_ID=la.USER_ID AND la.PROFILE_ID=(SELECT ID FROM user_profiles WHERE TITLE=\'parent\')';
		if($_REQUEST['_search_all_schools']!='Y')
			$sql .= ' AND ssm.SCHOOL_ID='.UserSchool().' AND s.STAFF_ID IN (SELECT PERSON_ID FROM students_join_people sjp WHERE ssm.STUDENT_ID = sjp.STUDENT_ID
AND ssm.SCHOOL_ID='.UserSchool().' AND ssm.SYEAR='.UserSyear().') ';
                   else
                       $sql .= ' AND ssm.SCHOOL_ID IN('.  GetUserSchools(UserID(),true).') ';
		if($_REQUEST['_dis_user']!='Y')
			$sql .= ' AND (s.IS_DISABLE<>\'Y\' OR  s.IS_DISABLE IS NULL)';
		if($_REQUEST['username'])
			$sql .= 'AND UPPER(la.USERNAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['username'])).'%\' ';
		if($_REQUEST['last'])
			$sql .= 'AND UPPER(s.LAST_NAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['last'])).'%\' ';
		if($_REQUEST['first'])
			$sql .= 'AND UPPER(s.FIRST_NAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['first'])).'%\' ';
		if($_REQUEST['profile'])
                {
                    if(is_number($_REQUEST['profile'])==FALSE)
                        $sql .= ' AND s.PROFILE=\''.$_REQUEST['profile'].'\' AND s.PROFILE_ID IS NULL ';
                     else 
                        $sql .= ' AND s.PROFILE_ID=\''.$_REQUEST['profile'].'\' ';
                }

		$sql .= $extra['WHERE'].' ';
		$sql .= 'ORDER BY FULL_NAME';

/**************************************for Back to User*************************************************************/
            if($_SESSION['staf_search']['sql'] && $_REQUEST['return_session']) {
               $sql= $_SESSION['staf_search']['sql'];
            }
            else
            {
                if ($_REQUEST['sql_save_session_staf'])
                    $_SESSION['staf_search']['sql'] = $sql;
            }
/***************************************************************************************************/
		if ($extra['functions'])
			$functions += $extra['functions'];
		return DBGet(DBQuery($sql),$functions);
		break;
	}
}
function GetUserStaffList(& $extra)
{	global $profiles_RET;
	$functions = array('PROFILE'=>'makeProfile');
        switch(User('PROFILE'))
	{
		case 'admin':
		$profiles_RET = DBGet(DBQuery('SELECT * FROM user_profiles'),array(),array('ID'));
                  $sql = 'SELECT DISTINCT CONCAT(s.LAST_NAME,  \' \' ,s.FIRST_NAME) AS FULL_NAME,
					s.PROFILE,s.PROFILE_ID,ssr.END_DATE,s.STAFF_ID '.$extra['SELECT'].'
                FROM
					staff s INNER JOIN staff_school_relationship ssr USING(staff_id) '.$extra['FROM'].',login_authentication la
				WHERE
					(s.PROFILE_ID!=4 OR s.PROFILE_ID IS NULL) AND ssr.SYEAR=\''.UserSyear().'\' AND s.STAFF_ID=la.USER_ID AND la.PROFILE_ID NOT IN (3,4)';
                    if(User('PROFILE_ID')=='1')
                    $sql.=' AND s.PROFILE_ID!=0 ';
                    
		if($_REQUEST['_search_all_schools']!='Y')
			$sql .= ' AND school_id='.  UserSchool().' ';
                   else
                       $sql .= ' AND school_id IN('.  GetUserSchools(UserID(),true).') ';
		if($_REQUEST['_dis_user']!='Y')
			$sql .= ' AND (s.IS_DISABLE<>\'Y\' OR  s.IS_DISABLE IS NULL) AND (ssr.END_DATE>=\''.date('Y-m-d').'\' OR ssr.END_DATE=\'0000-00-00\' )';
		if($_REQUEST['username'])
			$sql .= 'AND UPPER(la.USERNAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['username'])).'%\' ';
		if($_REQUEST['last'])
			$sql .= 'AND UPPER(s.LAST_NAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['last'])).'%\' ';
		if($_REQUEST['first'])
			$sql .= 'AND UPPER(s.FIRST_NAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['first'])).'%\' ';
		if($_REQUEST['profile']=="")
                {
                     $sql .= ' ';
                }
                else
                {
                if($_REQUEST['profile']==0 || $_REQUEST['profile'])
                {
                    if(is_number($_REQUEST['profile'])==FALSE)
                        $sql .= ' AND s.PROFILE=\''.$_REQUEST['profile'].'\' AND s.PROFILE_ID IS NULL ';
                     else 
                        $sql .= ' AND s.PROFILE_ID=\''.$_REQUEST['profile'].'\' ';
                }
                }

		$sql .= $extra['WHERE'].' ';
                
		$sql .= 'ORDER BY FULL_NAME ';
/**************************************for Back to User*************************************************************/
            if($_SESSION['staf_search']['sql'] && $_REQUEST['return_session']) {
               $sql= $_SESSION['staf_search']['sql'];
            }
            else
            {
                if ($_REQUEST['sql_save_session_staf'])
                    $_SESSION['staf_search']['sql'] = $sql;
            }
/***************************************************************************************************/
		if ($extra['functions'])
			$functions += $extra['functions'];

		return DBGet(DBQuery($sql),$functions);
		break;
	}
}



function makeProfile($value)
{	global $THIS_RET,$profiles_RET;

	if($THIS_RET['PROFILE_ID']!='')
		$return = $profiles_RET[$THIS_RET['PROFILE_ID']][1]['TITLE'];
	elseif($value=='admin')
		$return = 'Administrator w/Custom';
	elseif($value=='teacher')
		$return = 'Teacher w/Custom';
	elseif($value=='parent')
		$return = 'Parent w/Custom';
	elseif($value=='none')
		$return = 'No Access';
	else $return = $value;

	return $return;
}



# ---------------------------------------- For Missing attn ------------------------------------------------- #

function GetStaffList_Miss_Atn(& $extra)
{

		global $profiles_RET;
		$functions = array('PROFILE'=>'makeProfile');
		switch(User('PROFILE'))
		{
			case 'admin':
			$profiles_RET = DBQuery('SELECT * FROM user_profiles');
			$sql = 'SELECT CONCAT(s.LAST_NAME, \' \', s.FIRST_NAME) AS FULL_NAME,
						s.PROFILE,s.PROFILE_ID,s.STAFF_ID '.$extra['SELECT'].'
					FROM
						staff s INNER JOIN staff_school_relationship ssr USING(staff_id) '.$extra['FROM'].'
					WHERE
						ssr.SYEAR=\''.UserSyear().'\'';
			if($_REQUEST['_search_all_schools']!='Y')
				$sql .= ' AND ssr.school_id='.UserSchool().' ';
			if($_REQUEST['username'])
				$sql .= 'AND UPPER(s.USERNAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['username'])).'%\' ';
			if($_REQUEST['last'])
				$sql .= 'AND UPPER(s.LAST_NAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['last'])).'%\' ';
			if($_REQUEST['first'])
				$sql .= 'AND UPPER(s.FIRST_NAME) LIKE \''.str_replace("'","\'",strtoupper($_REQUEST['first'])).'%\' ';
			if($_REQUEST['profile'])
				$sql .= 'AND s.PROFILE=\''.$_REQUEST['profile'].'\' ';


$sql_st = 'SELECT teacher_id FROM missing_attendance mi where school_id=\''.  UserSchool().'\' AND syear=\''.  UserSyear().'\''.$extra['WHERE2'].' UNION SELECT secondary_teacher_id FROM missing_attendance mi where school_id=\''.  UserSchool().'\' AND syear=\''.  UserSyear().'\''.$extra['WHERE2'];
$res_st = DBQuery($sql_st) or die(mysql_error());
//echo count($res_st);
$a = 0;

while($row_st = mysql_fetch_array($res_st))
 {



   


		$teacher_str .= "'".$row_st['teacher_id']."',";
		$a++;

	 }

 		if($a != 0){
                                        $teacher_str = substr($teacher_str, 0, -1);
                                        $sql .= 'AND s.STAFF_ID IN ('.$teacher_str.')';
                                        
 }



			$sql .= $extra['WHERE'].' ';
			$sql .= 'ORDER BY FULL_NAME';


			if ($extra['functions'])
				$functions += $extra['functions'];


		if($a != 0)
			return DBGet(DBQuery($sql),$functions);

			break;
	}

}








?>

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
include("functions/ParamLibFnc.php");
include("Data.php");
include("functions/DbGetFnc.php");


function db_start()
{	global $DatabaseServer,$DatabaseUsername,$DatabasePassword,$DatabaseName,$DatabasePort,$DatabaseType;

	switch($DatabaseType)
	{
		case 'mysql':
			$connection = mysql_connect($DatabaseServer,$DatabaseUsername,$DatabasePassword);
			mysql_select_db($DatabaseName);
		break;
	}

	// Error code for both.
	if($connection === false)
	{
		switch($DatabaseType)
		{
			case 'mysql':
				$errormessage = mysql_error($connection);
			break;
		}
		db_show_error("","Could not Connect to Database: $DatabaseServer",$errstring);
	}
	return $connection;
}

// This function connects, and does the passed query, then returns a connection identifier.
// Not receiving the return == unusable search.
//		ie, $processable_results = DBQuery("select * from students");
function DBQuery($sql)
{	global $DatabaseType,$_openSIS;

	$connection = db_start();

	switch($DatabaseType)
	{
		case 'mysql':
			$sql = str_replace('&amp;', "", $sql);
			$sql = str_replace('&quot', "", $sql);
			$sql = str_replace('&#039;', "", $sql);
			$sql = str_replace('&lt;', "", $sql);
			$sql = str_replace('&gt;', "", $sql);
		  	$sql = ereg_replace("([,\(=])[\r\n\t ]*''",'\\1NULL',$sql);
			if(preg_match_all("/'(\d\d-[A-Za-z]{3}-\d{2,4})'/",$sql,$matches))
				{
					foreach($matches[1] as $match)
					{
						$dt = date('Y-m-d',strtotime($match));
						$sql = preg_replace("/'$match'/","'$dt'",$sql);
					}
				}
			if(substr($sql,0,6)=="BEGIN;")
			{
				$array = explode( ";", $sql );
				foreach( $array as $value )
				{
					if($value!="")
					{
						$result = mysql_query($value);
						if(!$result)
						{
							mysql_query("ROLLBACK");
							die(db_show_error($sql,"DB Execute Failed.",mysql_error()));
						}
					}
				}
			}
			else
			{
				$result = mysql_query($sql) or die(db_show_error($sql,"DB Execute Failed.",mysql_error()));
			}
		break;
	}
	return $result;
}

// return next row.
function db_fetch_row($result)
{	global $DatabaseType;

	switch($DatabaseType)
	{
		case 'mysql':
			$return = mysql_fetch_array($result);
			if(is_array($return))
			{
				foreach($return as $key => $value)
				{
					if(is_int($key))
						unset($return[$key]);
				}
			}
		break;
	}
	return @array_change_key_case($return,CASE_UPPER);
}

// returns code to go into SQL statement for accessing the next value of a sequence function db_seq_nextval($seqname)
function db_seq_nextval($seqname)
{	global $DatabaseType;

	if($DatabaseType=='mysql')
		$seq="fn_".strtolower($seqname)."()";
		
	return $seq;
}

function db_case($array)
{	global $DatabaseType;

	$counter=0;
	if($DatabaseType=='mysql')
	{
		$array_count=count($array);
		$string = " CASE WHEN $array[0] =";
		$counter++;
		$arr_count = count($array);
		for($i=1;$i<$arr_count;$i++)
		{
			$value = $array[$i];

			if($value=="''" && substr($string,-1)=='=')
			{
				$value = ' IS NULL';
				$string = substr($string,0,-1);
			}

			$string.="$value";
			if($counter==($array_count-2) && $array_count%2==0)
				$string.=" ELSE ";
			elseif($counter==($array_count-1))
				$string.=" END ";
			elseif($counter%2==0)
				$string.=" WHEN $array[0]=";
			elseif($counter%2==1)
				$string.=" THEN ";

			$counter++;
		}
	}

	return $string;
}

function db_properties($table)
{	global $DatabaseType,$DatabaseUsername;

	switch($DatabaseType)
	{
		case 'mysql':
			$result = DBQuery("SHOW COLUMNS FROM $table");
			while($row = db_fetch_row($result))
			{
				$properties[strtoupper($row['FIELD'])]['TYPE'] = strtoupper($row['TYPE'],strpos($row['TYPE'],'('));
				if(!$pos = strpos($row['TYPE'],','))
					$pos = strpos($row['TYPE'],')');
				else
					$properties[strtoupper($row['FIELD'])]['SCALE'] = substr($row['TYPE'],$pos+1);

				$properties[strtoupper($row['FIELD'])]['SIZE'] = substr($row['TYPE'],strpos($row['TYPE'],'(')+1,$pos);

				if($row['NULL']!='')
					$properties[strtoupper($row['FIELD'])]['NULL'] = "Y";
				else
					$properties[strtoupper($row['FIELD'])]['NULL'] = "N";
			}
		break;
	}
	return $properties;
}

function db_show_error($sql,$failnote,$additional='')
{	global $openSISTitle,$openSISVersion,$openSISNotifyAddress,$openSISMode;

	//PopTable('header','Error');
	$tb = debug_backtrace();
	$error = $tb[1]['file'] . " at " . $tb[1]['line'];
        
            echo "
                    <TABLE CELLSPACING=10 BORDER=0>
                            <TD align=right><b>Date:</TD>
                            <TD><pre>".date("m/d/Y h:i:s")."</pre></TD>
                    </TR><TR>
                            <TD align=right><b>Failure Notice:</b></TD>
                            <TD><pre> $failnote </pre></TD>
                    </TR><TR>
                            <TD align=right><b>SQL:</b></TD>
                            <TD>$sql</TD>
                    </TR>
                    </TR><TR>
                            <TD align=right><b>Traceback:</b></TD>
                            <TD>$error</TD>
                    </TR>
                    </TR><TR>
                            <TD align=right><b>Additional Information:</b></TD>
                            <TD>$additional</TD>
                    </TR>
                    </TABLE>";
       
		echo "
		<TABLE CELLSPACING=10 BORDER=0>
			<TR><TD align=right><b>Date:</TD>
			<TD><pre>".date("m/d/Y h:i:s")."</pre></TD>
		</TR><TR>
			<TD align=right></TD>
			<TD>openSIS has encountered an error that could have resulted from any of the following:
			<br/>
			<ul>
			<li>Invalid data input</li>
			<li>Database SQL error</li>
			<li>Program error</li>
			</ul>
			
			Please take this screen shot and send it to your openSIS representative for debugging and resolution.
			</TD>
		</TR>
		
		</TABLE>";
        
	echo "<!-- SQL STATEMENT: \n\n $sql \n\n -->";

	if($openSISNotifyAddress)
	{
		$message = "System: $openSISTitle \n";
		$message .= "Date: ".date("m/d/Y h:i:s")."\n";
		$message .= "Page: ".$_SERVER['PHP_SELF'].' '.ProgramTitle()." \n\n";
		$message .= "Failure Notice:  $failnote \n";
		$message .= "Additional Info: $additional \n";
		$message .= "\n $sql \n";
		$message .= "Request Array: \n".ShowVar($_REQUEST,'Y', 'N');
		$message .= "\n\nSession Array: \n".ShowVar($_SESSION,'Y', 'N');
		mail($openSISNotifyAddress,'openSIS Database Error',$message);

	}

	die();
}
$log_msg=  DBGet(DBQuery("SELECT MESSAGE FROM login_message WHERE DISPLAY='Y'"));
//print_r($_REQUEST);exit;
if($_REQUEST['pass_type_form']=='password')
{
    if($_REQUEST['pass_user_type']=='pass_student')
    {
        if($_REQUEST['password_stn_id']=='')
        {
            $_SESSION['err_msg'] = '<font color="red"><b>Please Enter Student Id.</b></font>';
            echo'<script>window.location.href="ForgotPass.php"</script>';
        }
        if($_REQUEST['uname']=='')
        {
            $_SESSION['err_msg'] = '<font color="red"><b>Please Enter Username.</b></font>';
            echo'<script>window.location.href="ForgotPass.php"</script>';
        }
        if($_REQUEST['month_password_dob']=='' || $_REQUEST['day_password_dob']=='' || $_REQUEST['year_password_dob']=='')
        {
            $_SESSION['err_msg'] = '<font color="red"><b>Please Enter Birthday Properly.</b></font>';
            echo'<script>window.location.href="ForgotPass.php"</script>';
        }
        
        if($_REQUEST['password_stn_id']!='' && $_REQUEST['uname']!='' && $_REQUEST['month_password_dob']!='' && $_REQUEST['day_password_dob']!='' && $_REQUEST['year_password_dob']!='')
        {
            $stu_dob=$_REQUEST['year_password_dob'].'-'.$_REQUEST['month_password_dob'].'-'.$_REQUEST['day_password_dob'];
            $stu_info=  DBGet(DBQuery('SELECT s.* FROM students s,login_authentication la  WHERE la.USER_ID=s.STUDENT_ID AND la.USERNAME=\''.$_REQUEST['uname'].'\' AND s.BIRTHDATE=\''.date('Y-m-d',strtotime($stu_dob)).'\' AND s.STUDENT_ID='.$_REQUEST['password_stn_id'].' AND la.PROFILE_ID=3'));

            if($stu_info[1]['STUDENT_ID']=='')
            {
                 $_SESSION['err_msg'] = '<font color="red" ><b>Incorrect login credential.</b></font>';
                
                echo'<script>window.location.href="ForgotPass.php"</script>';
            }
            else
            {
                $flag='stu_pass';
            }
        }
//         if($_REQUEST['password_stn_id']!='')
//        {echo "hi";
//           $res=  DBGet(DBQuery('select USERNAME from login_authentication where user_id='.$_REQUEST['password_stn_id'].' and profile_id=3 '));
//             if(strtolower($res[1]['USERNAME'])!=strtolower($_REQUEST['uname']))
//        echo    $_SESSION['err_msg'] = '<font color="red"><b>Please Enter correct Username111.</b></font>';
//          exit; 
////          echo'<script>window.location.href="ForgotPass.php"</script>';
//        }
    }
    if($_REQUEST['pass_user_type']=='pass_staff')
    {
        
        if($_REQUEST['uname']=='')
        {
            $_SESSION['err_msg'] = '<font color="red"><b>Please Enter Username.</b></font>';
            echo'<script>window.location.href="ForgotPass.php"</script>';
        }
        if($_REQUEST['password_stf_email']=='')
        {
            $_SESSION['err_msg'] = '<font color="red"><b>Please Enter Email Address.</b></font>';
            echo'<script>window.location.href="ForgotPass.php"</script>';
        }
        
        if($_REQUEST['password_stf_email']!='' && $_REQUEST['uname']!='')
        {
            
            $stf_info=  DBGet(DBQuery('SELECT s.* FROM staff s,login_authentication la  WHERE la.USER_ID=s.STAFF_ID AND la.USERNAME=\''.$_REQUEST['uname'].'\' AND s.EMAIL=\''.$_REQUEST['password_stf_email'].'\' AND la.PROFILE_ID IN (SELECT ID FROM user_profiles WHERE ID NOT IN (0,3,4))'));
        
            if($stf_info[1]['STAFF_ID']=='')
            {
                $_SESSION['err_msg'] = '<font color="red" ><b>Incorrect login credential.</b></font>';
                echo'<script>window.location.href="ForgotPass.php"</script>';
            }
            else
            {
                $flag='stf_pass';
            }
        }
    }
    if($_REQUEST['pass_user_type']=='pass_parent')
    {
        if($_REQUEST['uname']=='')
        {
            $_SESSION['err_msg'] = '<font color="red"><b>Please Enter Username.</b></font>';
            echo'<script>window.location.href="ForgotPass.php"</script>';
        }
        if($_REQUEST['password_stf_email']=='')
        {
            $_SESSION['err_msg'] = '<font color="red"><b>Please Enter Email Address.</b></font>';
            echo'<script>window.location.href="ForgotPass.php"</script>';
        }
        
        if($_REQUEST['password_stf_email']!='' && $_REQUEST['uname']!='')
        {
            
            $par_info=  DBGet(DBQuery('SELECT p.* FROM people p,login_authentication la  WHERE la.USER_ID=p.STAFF_ID AND la.USERNAME=\''.$_REQUEST['uname'].'\' AND p.EMAIL=\''.$_REQUEST['password_stf_email'].'\' AND la.PROFILE_ID = 4'));
        
            if($par_info[1]['STAFF_ID']=='')
            {
                $_SESSION['err_msg'] = '<font color="red" ><b>Incorrect login credential.</b></font>';
                echo'<script>window.location.href="ForgotPass.php"</script>';
            }
            else
            {
                $flag='par_pass';
            }
        }
    }
}
if($_REQUEST['user_type_form']=='username')
{
    if($_REQUEST['uname_user_type']=='uname_student')
    {
        if($_REQUEST['username_stn_id']=='')
        {
            $_SESSION['err_msg'] = '<font color="red"><b>Please Enter Student Id.</b></font>';
            echo'<script>window.location.href="ForgotPass.php"</script>';
        }
        if($_REQUEST['pass']=='')
        {
            $_SESSION['err_msg'] = '<font color="red"><b>Please Enter Password.</b></font>';
            echo'<script>window.location.href="ForgotPass.php"</script>';
        }
        if($_REQUEST['month_username_dob']=='' || $_REQUEST['day_username_dob']=='' || $_REQUEST['year_username_dob']=='')
        {
            $_SESSION['err_msg'] = '<font color="red"><b>Please Enter Birthday Properly.</b></font>';
            echo'<script>window.location.href="ForgotPass.php"</script>';
        }
        
        if($_REQUEST['username_stn_id']!='' && $_REQUEST['pass']!='' && $_REQUEST['month_username_dob']!='' && $_REQUEST['day_username_dob']!='' && $_REQUEST['year_username_dob']!='')
        {
            $stu_dob=$_REQUEST['year_username_dob'].'-'.$_REQUEST['month_username_dob'].'-'.$_REQUEST['day_username_dob'];
            $stu_info=  DBGet(DBQuery('SELECT s.* FROM students s,login_authentication la  WHERE la.USER_ID=s.STUDENT_ID AND la.PASSWORD=\''.md5($_REQUEST['pass']).'\' AND s.BIRTHDATE=\''.date('Y-m-d',strtotime($stu_dob)).'\' AND s.STUDENT_ID='.$_REQUEST['username_stn_id'].''));
        
            if($stu_info[1]['STUDENT_ID']=='')
            {
                $_SESSION['err_msg'] = '<font color="red" ><b>Incorrect login credential.</b></font>';
                echo'<script>window.location.href="ForgotPass.php"</script>';
            }
            else
            {
                $get_uname=DBGet(DBQuery('SELECT USERNAME FROM login_authentication WHERE USER_ID='.$_REQUEST['username_stn_id'].' AND PROFILE_ID=3'));
                $_SESSION['fill_username']=$get_uname[1]['USERNAME'];
                echo'<script>window.location.href="index.php"</script>';
            }
        }
    }
    if($_REQUEST['uname_user_type']=='uname_staff')
    {
        
        if($_REQUEST['pass']=='')
        {
            $_SESSION['err_msg'] = '<font color="red"><b>Please Enter Password.</b></font>';
            echo'<script>window.location.href="ForgotPass.php"</script>';
        }
        if($_REQUEST['username_stf_email']=='')
        {
            $_SESSION['err_msg'] = '<font color="red"><b>Please Enter Email Address.</b></font>';
            echo'<script>window.location.href="ForgotPass.php"</script>';
        }
        
        if($_REQUEST['username_stf_email']!='' && $_REQUEST['pass']!='')
        {
            $stf_info=  DBGet(DBQuery('SELECT s.* FROM staff s,login_authentication la WHERE la.USER_ID=s.STAFF_ID AND la.PASSWORD=\''.md5($_REQUEST['pass']).'\' AND s.EMAIL=\''.$_REQUEST['username_stf_email'].'\''));
        
            if($stf_info[1]['STAFF_ID']=='')
            {
                $_SESSION['err_msg'] = '<font color="red" ><b>Incorrect login credential.</b></font>';
                echo'<script>window.location.href="ForgotPass.php"</script>';
            }
            else
            {
                $get_uname=DBGet(DBQuery('SELECT USERNAME FROM login_authentication WHERE USER_ID='.$stf_info[1]['STAFF_ID'].' AND PROFILE_ID='.$stf_info[1]['PROFILE_ID']));
                $_SESSION['fill_username']=$get_uname[1]['USERNAME'];
                echo'<script>window.location.href="index.php"</script>';
            }
        }
    }
    if($_REQUEST['uname_user_type']=='uname_parent')
    {
        if($_REQUEST['pass']=='')
        {
            $_SESSION['err_msg'] = '<font color="red"><b>Please Enter Password.</b></font>';
            echo'<script>window.location.href="ForgotPass.php"</script>';
        }
        if($_REQUEST['username_stf_email']=='')
        {
            $_SESSION['err_msg'] = '<font color="red"><b>Please Enter Email Address.</b></font>';
            echo'<script>window.location.href="ForgotPass.php"</script>';
        }
        
        if($_REQUEST['username_stf_email']!='' && $_REQUEST['pass']!='')
        {
            $par_info=  DBGet(DBQuery('SELECT p.* FROM people p,login_authentication la WHERE la.USER_ID=p.STAFF_ID AND la.PASSWORD=\''.md5($_REQUEST['pass']).'\' AND p.EMAIL=\''.$_REQUEST['username_stf_email'].'\' '));
        
            if($par_info[1]['STAFF_ID']=='')
            {
                $_SESSION['err_msg'] = '<font color="red" ><b>Incorrect login credential.</b></font>';
                echo'<script>window.location.href="ForgotPass.php"</script>';
            }
            else
            {
                $get_uname=DBGet(DBQuery('SELECT USERNAME FROM login_authentication WHERE USER_ID='.$par_info[1]['STAFF_ID'].' AND PROFILE_ID=4'));
                $_SESSION['fill_username']=$get_uname[1]['USERNAME'];
                echo'<script>window.location.href="index.php"</script>';
            }
        }
    }
}
if($_REQUEST['new_pass']!='' && $_REQUEST['ver_pass']!='')
{
//   print_r($_REQUEST);echo '<br><br>';
   $get_vals=explode(",",$_REQUEST['user_info']);
//   print_r($get_vals);
   $flag='submited_value';
   
   $get_info=DBGet(DBQuery('SELECT COUNT(*) AS EX_REC FROM login_authentication WHERE user_id!='.$get_vals[0].' AND profile_id!='.$get_vals[1].' AND password=\''.md5($_REQUEST['ver_pass']).'\' '));
   if($get_info[1]['EX_REC']>0)
   {
      $_SESSION['err_msg_mod'] = '<font color="red" ><b>Incorrect login credential.</b></font>';
   }
   else
   {
       DBQuery('UPDATE login_authentication SET password=\''.md5($_REQUEST['ver_pass']).'\' WHERE user_id='.$get_vals[0].' AND profile_id='.$get_vals[1].' ');
       $_SESSION['conf_msg'] = '<font color="red" ><b>Password updated successfully.</b></font>';
       echo'<script>window.location.href="index.php"</script>';
   }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>openSIS Student Information System</title>
<link rel="shortcut icon" href="favicon.ico">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="styles/Help.css" />
<link rel="stylesheet" type="text/css" href="styles/Calendar.css" />
<link rel="stylesheet" type="text/css" href="styles/Login.css"/>
<script src='js/Ajaxload.js'></script>
<script src='js/Validation.js'></script>
<script src='js/Validator.js'></script>
</head>
<body>
<form name="f1" method="post" action="">
<table width='100%' height='550px' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td valign='middle' height='100%'>
    <table class='wrapper' border='0' cellspacing='0' cellpadding='0' align='center'>
        
        <tr>
          <td class='header'>
          <table width='100%' border='0' cellspacing='0' cellpadding='0' class='logo_padding'>
              
                <tr>
                <td><img src='assets/osis_logo.png' border='0' /></td>
                <td align='right'><a href='http://www.os4ed.com' target=_blank ><img src='assets/os4ed_logo.png' height='62' width='66' border='0'/></a></td>
              
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td class='content'>
          <table width='100%' border='0' cellspacing='0' cellpadding='0'>
              <tr>
                <td>
                <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                      <td class='header_padding'>
                      <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                          <tr>
                            <td class='header_txt'>Forgot Password</td>
                          </tr>
                        </table>
                        </td>
                    </tr><tr>
                      <td class='padding'>
                      <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                          <tr>
                            <tr>
                            <td>
                                <a href="ForgotPass.php" style="text-decoration:none;color:black;font-weight:bold">&lsaquo;&lsaquo; Back</a>
                                <?php if($flag=='stu_pass') { ?>
                                <input type="hidden" name="user_info" value="<?php echo $stu_info[1]['STUDENT_ID'].',3,'.$_REQUEST['uname'];?>"/>
                                <?php 
                                } 
                                if($flag=='stf_pass') { ?>
                                  <input type="hidden" name="user_info" value="<?php echo $stf_info[1]['STAFF_ID'].','.$stf_info[1]['PROFILE_ID'].','.$_REQUEST['uname'];?>"/>
                                  <?php 
                                  } 
                                if($flag=='par_pass') { ?>
                                   <input type="hidden" name="user_info" value="<?php echo $par_info[1]['STAFF_ID'].','.$par_info[1]['PROFILE_ID'].','.$_REQUEST['uname'];?>"/>
                                   <?php 
                                   } 
                                if($flag=='submited_value') {   ?>
                                   <input type="hidden" name="user_info" value="<?php echo $_REQUEST['user_info'];?>"/>
                                <?php
                                }   
                                ?>
                            </td>
                            </tr>
                               <tr><td align="center"><div id="divErr"><?php if($_SESSION['err_msg_mod']!='') echo $_SESSION['err_msg_mod']; unset($_SESSION['err_msg_mod']);?></div></td></tr>
                            <td>
                                        <table border='0' width='100%' cellspacing='2' cellpadding='2' align=center>
                                            <tr>
                                                <td colspan="2"><p>Password must be minimum 8 characters long with at least one capital, one numeric and one special character. Example: S@mple123
</p></td>
                                            </tr>
                                    <tr><td colspan=2 align="center">
                                   
                                                     </td></tr>
                                     
                                     <tr>
                                       <td width="30%" align="right">Enter new password:</td>
                                       <td width="70%"><input type="password" name="new_pass" id="new_pass" class="login_txt" AUTOCOMPLETE="off" onkeyup="forgotpasswordStrength(this.value);passwordMatch();forgotpassvalidate_password(this.value,'<?php echo $_REQUEST['uname'];?>',<?php if($flag=='stu_pass') echo 3; else if($flag=='stf_pass') echo $stf_info[1]['PROFILE_ID']; else echo $par_info[1]['PROFILE_ID'];?>);" /><span id=passwordStrength></span></td>
                                     </tr>
                                     <tr>
                                       <td width="30%" align="right">Re-enter new password:</td>
                                       <td width="70%"><input type="password" name="ver_pass" id="ver_pass" class="login_txt" AUTOCOMPLETE = "off" onkeyup="passwordMatch();"/><span id=passwordMatch></span></td>
                                     </tr>
                                    <tr>
                                       <td></td>
                                       <td><input type="submit" name="save" class="btn" value="Update" onClick="return pass_check();"/>
                                       </td>
                                     </tr>
                                     <tr>
                                     <td align='center'></td>
                                     <td></td>
                                       </tr>
				  </table>    
                                      
				  </td>
                          </tr>
                          
                        </table></td>
                    </tr>
                  </table>
              </tr>
            </table>
            </td>
        <tr>
          <td class='footer' valign='top'>
          <table width='100%' border='0' cellspacing='0' cellpadding='0'>
              <tr>
                <td class='margin'></td>
              </tr>
              <tr>
                <td align='center' class='copyright'>
                openSIS is a product of Open Solutions for Education, Inc. (<a href='http://www.os4ed.com' target='_blank'>OS4Ed</a>).
                and is licensed under the <a href='http://www.gnu.org/licenses/gpl.html' target='_blank'>GPL License</a>.
                </td>
              </tr>
            </table></td>
        </tr>
      </table>
      </td>
  		</tr>
	</table>
</form>
</body>
</html>


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


$st_flag=false;
$error=false;
$error_school='';
if($_REQUEST['staff_id']!='new')
{
$profile=DBGet(DBQuery("SELECT id FROM user_profiles WHERE profile='parent'"));
if(UserID() && !$_REQUEST['staff_id'])
$user_profile=DBGet(DBQuery("SELECT profile_id FROM people WHERE staff_id='".UserID()."'"));
else
$user_profile=DBGet(DBQuery("SELECT profile_id FROM people WHERE staff_id='".$_REQUEST['staff_id']."'"));
    if($profile[1]['ID']==$user_profile[1]['PROFILE_ID'])
    {
      $_SESSION['fn']='user';  
    }
    else
    {
      $_SESSION['fn']='staff';  
    }
}
else
{
    $_SESSION['fn']=''; 
}
###########################################
#print_r($_REQUEST);
if(isset($_REQUEST['staff_id']) && $_REQUEST['staff_id']!='new')
{
	if(User('PROFILE')=='admin')
        {
            $RET = DBGet(DBQuery('SELECT FIRST_NAME,LAST_NAME FROM people WHERE STAFF_ID=\''.$_REQUEST['staff_id'].'\''));
            $count_staff_RET=DBGet(DBQuery('SELECT COUNT(*) AS NUM FROM people'));
            if($count_staff_RET[1]['NUM']>1){
                DrawHeaderHome( 'Selected User: '.$RET[1]['FIRST_NAME'].'&nbsp;'.$RET[1]['LAST_NAME'].' (<A HREF=Side.php?staff_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Deselect</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname=users/User.php&ajax=true&bottom_back=true&return_session=true target=body>Back to User List</A>');
            }else{
                DrawHeaderHome( 'Selected User: '.$RET[1]['FIRST_NAME'].'&nbsp;'.$RET[1]['LAST_NAME'].' (<A HREF=Side.php?staff_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Deselect</font></A>)');
            }
        }
}
#############################################
if(User('PROFILE')!='admin' && User('PROFILE')!='teacher' && $_REQUEST['staff_id'] && $_REQUEST['staff_id']!='new')
{
        if(!AllowUse())
        {
	if(User('USERNAME'))
	{
		HackingLog();

        }
	exit;
        }
}

if($_REQUEST['modfunc']=='remove_stu')
{
    $delete=DeletePromptMod('student',"include=GeneralInfoInc&category_id=1&staff_id=$_REQUEST[staff_id]");
    if($delete==1)
    {
        DBGet(DBQuery('DELETE FROM students_join_people WHERE STUDENT_ID='.$_REQUEST['id'].' AND PERSON_ID='.$_REQUEST['staff_id']));
        echo "<script>window.location.href='Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&include=GeneralInfoInc&category_id=1&staff_id=$_REQUEST[staff_id]'</script>";
    }
    
}
else
{

   
if(!$_REQUEST['include'])
{
	$_REQUEST['include'] = 'GeneralInfoInc';
	$_REQUEST['category_id'] = '1';
}
elseif(!$_REQUEST['category_id'])
	if($_REQUEST['include']=='GeneralInfoInc')
		$_REQUEST['category_id'] = '1';

        elseif($_REQUEST['include']=='AddressInfoInc')
		$_REQUEST['category_id'] = '2';
        
	elseif($_REQUEST['include']!='OtherInfoUserInc')
	{
		$include = DBGet(DBQuery('SELECT ID FROM people_field_categories WHERE INCLUDE=\''.$_REQUEST['include'].'\''));
		$_REQUEST['category_id'] = $include[1]['ID'];
	}
    
      
        
        
if(User('PROFILE')!='admin')
{
	if(User('PROFILE_ID'))
		$can_edit_RET = DBGet(DBQuery('SELECT MODNAME FROM profile_exceptions WHERE PROFILE_ID=\''.User('PROFILE_ID').'\' AND MODNAME=\''.'users/User.php&category_id='.$_REQUEST[category_id].'\' AND CAN_EDIT=\''.'Y'.'\''));
	else
        {
                $profile_id_mod=DBGet(DBQuery("SELECT PROFILE_ID FROM staff WHERE USER_ID='".User('STAFF_ID')));
                $profile_id_mod=$profile_id_mod[1]['PROFILE_ID'];   
                if($profile_id_mod!='') 
		$can_edit_RET = DBGet(DBQuery('SELECT MODNAME FROM profile_exceptions WHERE PROFILE_ID=\''.$profile_id_mod.'\' AND MODNAME=\''.'users/User.php&category_id='.$_REQUEST[category_id].'\' AND CAN_EDIT=\''.'Y'.'\''),array(),array('MODNAME'));
        }
	if($can_edit_RET)
		$_openSIS['allow_edit'] = true;
}

unset($schools);
if($_REQUEST['modfunc']=='update')
{
  
    $up_go='n';

    if($_REQUEST['category_id']==1)
    {
    if(count($_REQUEST['people'])>0)
    {
        $up_sql='UPDATE people SET ';
        foreach($_REQUEST['people'] as $pi=>$pd)
        {


            $up_sql.=$pi."='".str_replace("'","'/",$pd)."',";
            $up_go='y';

        }
        if($up_go=='y')
        {
        $up_sql=substr($up_sql,0,-1);
        $up_sql.=" WHERE STAFF_ID=".$_REQUEST['staff_id'];
        
        DBQuery($up_sql);
        }
        unset($up_sql);
        unset($pi);
        unset($pd);
        unset($up_go);
    }
    $up_go='n';
    if($_REQUEST['login_authentication']['PASSWORD']!='')
    {
        $up_sql='UPDATE login_authentication SET PASSWORD=\''.md5($_REQUEST['login_authentication']['PASSWORD']).'\' WHERE PROFILE_ID=4 AND USER_ID='.$_REQUEST['staff_id'];
        DBQuery($up_sql);
        unset($up_sql);
    }
    }
    else if($_REQUEST['category_id']==2)
    {
        if(count($_REQUEST['student_addres'])>0)
        {
        $up_sql='UPDATE student_address SET ';
        foreach($_REQUEST['student_addres'] as $pi=>$pd)
        {

            $up_sql.=$pi."='".str_replace("'","''",$pd)."',";
            $up_go='y';

        }
        if($up_go=='y')
        {
        $up_sql=substr($up_sql,0,-1);
        $up_sql.=" WHERE PEOPLE_ID=".$_REQUEST['staff_id'];

        DBQuery($up_sql);
        }
        unset($up_sql);
        unset($pi);
        unset($pd);
        unset($up_go);
        }
    }
    else
    {

        $disp_error='';
        if($_REQUEST['modfunc']=='update')
        {
            $flag=0;
            $qry='UPDATE people SET ';
            foreach($_REQUEST['staff'] as $in=>$d)
            {
                $field_id=explode('_',$in);
                $field_id=$field_id[1];
                $check_stat=DBGet(DBQuery('SELECT TITLE,REQUIRED FROM people_fields WHERE ID=\''.$field_id.'\' '));
                if($check_stat[1]['REQUIRED']=='Y')
                {
                    if($d!='')
                    {
                        $qry.=' '.$in.'=\''.str_replace("'","''",$d).'\',';
                        $flag++;

                    }
                    else
                    {
                    $disp_error='<font style="color:red"><b>'.$check_stat[1]['TITLE'].' is required.</b></font>';    
                    }
                }
                else
                {
                    if($d!='')
                    $qry.=' '.$in.'=\''.str_replace("'","''",$d).'\',';
                    else
                    $qry.=' '.$in.'=NULL,';
                    
                    $flag++;
                }
                    
                    
            }
            if($flag>0)
            {
                
                $qry=substr($qry,0,-1).' WHERE STAFF_ID='.$_REQUEST['staff_id'];
                DBQuery($qry);
            }
        }
    }
}
if($disp_error!='')
{
    echo $disp_error;
    unset($disp_error);
}



 

$extra['SELECT'] = ',LAST_LOGIN';

$extra['functions'] = array('LAST_LOGIN'=>'makeLogin');

if(basename($_SERVER['PHP_SELF'])!='index.php')
{
	if($_REQUEST['staff_id']=='new')
		DrawBC("Users > Add a User");
	else
		DrawBC("Users > ".ProgramTitle());
	unset($_SESSION['staff_id']);
        
        Search('staff_id',$extra);
}
else
	DrawHeader('Create Account');

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete' && basename($_SERVER['PHP_SELF'])!='index.php' && AllowEdit())
{
	if(DeletePrompt('user'))
	{
		DBQuery('DELETE FROM program_user_config WHERE USER_ID=\''.UserStaffID().'\'');
		
		DBQuery('DELETE FROM students_join_people WHERE PERSON_ID=\''.UserStaffID().'\'');
		DBQuery('DELETE FROM staff WHERE STAFF_ID=\''.UserStaffID().'\'');
		unset($_SESSION['staff_id']);
		unset($_REQUEST['staff_id']);
		unset($_REQUEST['modfunc']);
		echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
		Search('staff_id',$extra);
	}
}
if((UserStaffID() || $_REQUEST['staff_id']=='new') && ((basename($_SERVER['PHP_SELF'])!='index.php') || !$_REQUEST['staff']['USERNAME']) && $_REQUEST['modfunc']!='delete' && $_SESSION['fn']!='staff')
{
    
	if($_REQUEST['staff_id']!='new')
	{
		
				$sql = 'SELECT s.TITLE,s.STAFF_ID,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,
                                USERNAME,PASSWORD,up.TITLE AS PROFILE,s.PROFILE_ID,s.HOME_PHONE,s.EMAIL,LAST_LOGIN,IS_DISABLE
				FROM people s,user_profiles up,login_authentication la WHERE s.STAFF_ID=la.USER_ID AND la.PROFILE_ID =4 AND s.STAFF_ID=\''.UserStaffID().'\' AND s.PROFILE_ID=up.ID';
		$QI = DBQuery($sql);
		$staff = DBGet($QI);

		$staff = $staff[1];
		echo "<FORM name=staff id=staff action=Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&category_id=$_REQUEST[category_id]&staff_id=".UserStaffID()."&modfunc=update method=POST >";
	}
	elseif(basename($_SERVER['PHP_SELF'])!='index.php')
        {
            $staff=array();
            echo "<FORM name=staff id=staff action=Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&category_id=$_REQUEST[category_id]&modfunc=update method=POST>";
        }
        else
		echo "<FORM name=F2 id=F2 action=index.php?modfunc=create_account METHOD=POST>";

	if(basename($_SERVER['PHP_SELF'])!='index.php')
	{
		if(UserStaffID() && UserStaffID()!=User('STAFF_ID') && UserStaffID()!=$_SESSION['STAFF_ID'] && User('PROFILE')=='admin')
			$delete_button = '<INPUT type=button class=btn_medium value=Delete onclick="window.location=\'Modules.php?modname='.$_REQUEST['modname'].'&modfunc=delete\'">';
	}
	
	if(User('PROFILE_ID')!='')
		$can_use_RET = DBGet(DBQuery('SELECT MODNAME FROM profile_exceptions WHERE PROFILE_ID=\''.User('PROFILE_ID').'\' AND CAN_USE=\''.'Y'.'\''),array(),array('MODNAME'));
	else
        {
                $profile_id_mod=DBGet(DBQuery("SELECT PROFILE_ID FROM staff WHERE USER_ID='".User('STAFF_ID')));
                $profile_id_mod=$profile_id_mod[1]['PROFILE_ID'];   
                if($profile_id_mod!='') 
		$can_use_RET = DBGet(DBQuery('SELECT MODNAME FROM profile_exceptions WHERE PROFILE_ID=\''.$profile_id_mod.'\' AND CAN_USE=\''.'Y'.'\''),array(),array('MODNAME'));
        }
	$profile = DBGet(DBQuery("SELECT PROFILE FROM people WHERE STAFF_ID='".UserStaffID()."'"));
	
	$profile = $profile[1]['PROFILE'];

	$categories_RET = DBGet(DBQuery('SELECT ID,TITLE,INCLUDE FROM people_field_categories WHERE '.($profile?strtoupper($profile).'=\'Y\'':'ID=\'1\'').' ORDER BY SORT_ORDER,TITLE'));

	foreach($categories_RET as $category)
	{
		if($can_use_RET['users/User.php&category_id='.$category['ID']])
		{
				if($category['ID']=='1')
					$include = 'GeneralInfoInc';
				elseif($category['ID']=='2')
					$include = 'AddressInfoInc';
				elseif($category['INCLUDE'])
					$include = $category['INCLUDE'];
				else
					$include = 'OtherInfoUserInc';
                                
                        if(User('PROFILE_ID')==4)
                        $tabs[] = array('title'=>$category['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&include=$include&category_id=".$category['ID']);    
                        else
			$tabs[] = array('title'=>$category['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&include=$include&category_id=".$category['ID']."&staff_id=".UserStaffID());
                     

		}
	}
        
        
	$_openSIS['selected_tab'] = "Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]";
	if($_REQUEST['category_id'])
		$_openSIS['selected_tab'] .= '&category_id='.$_REQUEST['category_id'];
        if(User('PROFILE_ID')!=4)
        $_openSIS['selected_tab'] .='&staff_id='.$_REQUEST['staff_id']; 
        

	echo '<BR>';
	PopTable('header',$tabs,'width=96%');
        
        
	if(!strpos($_REQUEST['include'],'/'))
        {
		include('modules/users/includes/'.$_REQUEST['include'].'.php');
        }
	else
	{
		include('modules/'.$_REQUEST['include'].'.php');
		$separator = '<HR>';
		include('modules/users/includes/OtherInfoUserInc.php');
	}
        
	PopTable('footer');
        $sql='SELECT count(s.ID) as schools FROM schools s,staff st INNER JOIN staff_school_relationship ssr USING(staff_id) WHERE s.id=ssr.school_id AND ssr.syear='.UserSyear().' AND st.staff_id='.User('STAFF_ID');
        $school_admin=DBGet(DBQuery($sql));
	echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onclick="return formcheck_user_user_mod('.$_SESSION[staff_school_chkbox_id].');"').'</CENTER>';
//        
        echo '</FORM>';
}
unset($_SESSION['fn']);
}
?>

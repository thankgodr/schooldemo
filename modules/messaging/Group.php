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
$curProfile= User('PROFILE');
$userName=  User('USERNAME');

if(!isset($_REQUEST['modfunc']))
{
echo "<FORM name=Group id=Group action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=group method=POST >";
PopTable('header','Group');
echo "<table align='right'><tr><td><a href='#' onclick='load_link(\"Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=add_group\");'>".button('add')."</a></td><td>Add Group</td></tr></table>";
echo '<TABLE style="overflow:auto; width:820px;" >';
     $select="SELECT *  from mail_group  WHERE USER_NAME ='$userName'";
    $link['GROUP_NAME']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=groupmember";
    $link['GROUP_NAME']['variables'] = array('group_id'=>'GROUP_ID');       
    $columns=array('GROUP_NAME'=>'Group Name','DESCRIPTION'=>'Description','CREATION_DATE'=>'Create Date','MEMBERS'=>'Members','action'=>'Action');
    $list = DBGet(DBQuery($select));
    
    
    foreach($list as $id=>$value)
    {
      
        $qr=  DBGet(DBQuery('SELECT COUNT(*) AS MEMBERS FROM mail_groupmembers where group_id='.$list[$id]['GROUP_ID'].''));
      
        $list[$id]['MEMBERS']=$qr[1]['MEMBERS'];
        if($list[$id]['DESCRIPTION']=="N")
        $list[$id]['DESCRIPTION']='';
        if($list[$id]['action']=="")
        {
            $list[$id]['action']="<a href='Modules.php?modname=$_REQUEST[modname]&modfunc=groupmember&group_id=$value[GROUP_ID]'>".button('edit')."</a>&nbsp;&nbsp;<a href='Modules.php?modname=$_REQUEST[modname]&modfunc=delete&group_id=$value[GROUP_ID]'>".button('remove')."</a>";
        }
    }
    ListOutput( $list,$columns,'Group','Groups',$link,array(),array('search'=>false),'');
 PopTable('footer');
}

if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='delete')
{
$group_id= $_REQUEST['group_id'];
$members=  DBGet (DBQuery ("select count(*) as countmember from mail_groupmembers where group_id=".$group_id.""));
$count_members=$members[1]['COUNTMEMBER'];
if($count_members>0)
{
    if(DeleteMail("group with ".$count_members." groupmembers",'delete',$_REQUEST['modname'],true))
    {
        $member_del="delete from mail_groupmembers where group_id=".$group_id."";
        $member_del_execute=  DBQuery($member_del);
        $mail_delete="delete from mail_group where group_id =".$group_id."";
        $mail_delete_ex=DBQuery($mail_delete);
        unset($_REQUEST['modfunc']);
        echo "<script>window.location='Modules.php?modname=messaging/Group.php'</script>";
    }
}
 else 
{
    if(DeleteMail('group','delete',$_REQUEST['modname'],true))
    {       
        $mail_delete="delete from mail_group where group_id =".$group_id."";
        $mail_delete_ex=DBQuery($mail_delete);
        unset($_REQUEST['modfunc']);
        echo "<script>window.location='Modules.php?modname=messaging/Group.php'</script>";
    }
}
    unset($_REQUEST['modfunc']);
}

 if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='groupmember')
{
 PopTable('header','Group Members');
 echo "<FORM name=sav id=sav action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=members&groupid=".strip_tags(trim($_REQUEST[group_id]))." method=POST>";

 echo '<div style="overflow:auto; width:820px;">';
 echo "<div id='members'>";

$member="select * from mail_groupmembers where GROUP_ID='".$_REQUEST['group_id']."'";
$member_list=DBGet(DBQuery($member));
foreach($member_list as $key=>$value)
{
    $member_list[$key]['PROFILE'];
    $select="SELECT * FROM user_profiles WHERE ID='".$member_list[$key]['PROFILE']."'";
    $profile=DBGet(DBQuery($select));
    $member_list[$key]['PROFILE']=$profile[1]['PROFILE'];
}
$columns=array('USER_NAME'=>'User Name','PROFILE'=>'Profile');
$extra['SELECT'] = ",Concat(NULL) AS CHECKBOX";
$extra['LO_group'] = array('GROUP_ID');
$extra['columns_before']= array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'group\');" checked><A>');
$extra['new'] = true;
    if(is_array($extra['columns_before']))
	{
		$LO_columns = $extra['columns_before'] + $columns;
		$columns = $LO_columns;
                
        }
foreach($member_list as $id=>$value)
{
    $extra['columns_before']['CHECKBOX'] = "<INPUT type=checkbox name=group[".$value['ID']."] value=Y CHECKED>";
    $member_list[$id]=$extra['columns_before']+$value;
}
$group="select GROUP_NAME,DESCRIPTION from mail_group where GROUP_ID=$_REQUEST[group_id]";
$groupDetails=DBGet(DBQuery($group));
$groupname=$groupDetails[1]['GROUP_NAME'];
$groupdesc=($groupDetails[1]['DESCRIPTION']=='N'?'':$groupDetails[1]['DESCRIPTION']);

echo '<table><tr><td>Group Name:'.'</td>';
echo '<td>'.TextInput($groupname,'groupname','','maxlength=50 style="font-size:12px;"',false).'</td>';

echo '<tr><td>Description:'.'</td>';
echo '<td>'.TextInput($groupdesc,'groupdesc','','maxlength=50 style="font-size:12px;"',false).'</td>';
echo'<tr><td><input type=hidden name =gid value='.strip_tags(trim($_REQUEST['group_id'])) .'></td></tr>';

for($i=0;$i<strlen($groupname);$i++)
{
    if($groupname[$i]==" ")
    $groupname[$i]=str_replace(" ", "_",$groupname[$i]);
    else if($groupname[$i]=="'")
    $groupname[$i]=str_replace("'", "\\",$groupname[$i]);

}
$grp=$groupname;

if($groupdesc=='N')
    $groupdesc='N';
else
{
    for($i=0;$i<strlen($groupdesc);$i++)
    {
        if($groupdesc[$i]==" ")
        $groupdesc[$i]=str_replace(" ", "_",$groupdesc[$i]);
        else if($groupdesc[$i]=="'")
        $groupdesc[$i]=str_replace("'", "\\",$groupdesc[$i]);

    }
}
$gid=$_REQUEST[group_id];
echo '<td align="right"></td>';
echo '</tr></table><table align="right"><tr><td><a href=Modules.php?modname='.$_REQUEST[modname].'&modfunc=exist_group&group_name='.$grp.'&desc='.$groupdesc.'&grp_id='.$gid.'>'.button('add').'</a></td><td>Add Member</td></tr></table>';
ListOutput( $member_list,$columns,'Member','Members','',array(),array('search'=>false,'save'=>false),'');
echo "</div>";
        echo "</div>";

        {
            if(isset($userName))
                echo '<table align="center" width="94%"><tr><td align="center"><INPUT type=submit class=btn_medium value=Save></td></tr></table>';
           
        }
        echo '</FORM>';
         PopTable('footer');

}

if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='exist_group')
{
  
  
   PopTable('header','Group Members');
    
    $grp_name=$_REQUEST['group_name'];

    echo "<FORM name=search action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=add_group_member&search=true&group_id=$grp_name&desc=".strip_tags(trim($_REQUEST[desc]))."&grp_id=".strip_tags(trim($_REQUEST[grp_id]))." method=POST>";
		echo '<TABLE>';
		echo '<TR><TD align=right>Last Name</TD><TD><INPUT type=text class=cell_floating name=last></TD></TR>';
		echo '<TR><TD align=right>First Name</TD><TD><INPUT type=text class=cell_floating name=first></TD></TR>';
		echo '<TR><TD align=right>Username</TD><TD><INPUT type=text class=cell_floating name=username></TD></TR>';
//                echo '<TR><TD><INPUT type=text class=cell_floating name=grp_id value='.$_REQUEST['grp_id'].'></TD></TR>';
                if(User('PROFILE')=='teacher')
                {
                    $profiles=  DBGet(DBQuery('SELECT * FROM user_profiles where id!=2'));
                }
                else if(User('PROFILE')=='parent')
                {
                    $profiles=  DBGet(DBQuery('SELECT * FROM user_profiles where id!=4'));
                } 
                else if(User('PROFILE')=='student')
                {
                    $profiles=  DBGet(DBQuery('SELECT * FROM user_profiles where id!=3'));
                } 
                else
                    $profiles=  DBGet(DBQuery('SELECT * FROM user_profiles'));
                $options[-1]='N/A';
                
                
                foreach($profiles as $key=>$value)
                {
                    $options[$value['ID']]=$value['TITLE'];
                }
		echo '<TR><TD align=right>Profile</TD><TD><SELECT name=profile>';
		foreach($options as $key=>$val)
			echo '<OPTION value="'.$key.'">'.$val;
		echo '</SELECT></TD></TR>';
		if($extra['search'])
			echo $extra['search'];
		echo '<TR><TD colspan=2 align=center>';
		
		if(User('PROFILE')=='admin' || User('PROFILE')=='teacher'|| User('PROFILE')=='parent')
			echo '<INPUT type=checkbox name=_search_all_schools value=Y'.(Preferences('DEFAULT_ALL_SCHOOLS')=='Y'?' CHECKED':'').'>Search All Schools<BR>';
			echo '<INPUT type=checkbox name=_dis_user value=Y>Include Disabled User<BR><br>';
		
		echo "<INPUT type=SUBMIT class=btn_medium value='Submit'>&nbsp<INPUT type=RESET class=btn_medium value='Reset'>";
		echo '</TD></TR>';
		echo '</TABLE>';
		/********************for Back to user***************************/
                    echo '<input type=hidden name=sql_save_session_staf value=true />';
                /************************************************/
                echo '</FORM>';
                PopTable('footer');
}

if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='add_group_member')
{
    $groupname=$_REQUEST['group_id'];
    $desc=$_REQUEST['desc'];
    for($i=0;$i<strlen($groupname);$i++)
    {
//    if($groupname[$i]=="_")
//    $groupname[$i]=str_replace("_", " ",$groupname[$i]);
     if($groupname[$i]=="\\")
    $groupname[$i]=str_replace("\\", "'",$groupname[$i]);
    }
    $_REQUEST['group_id']=$groupname;
    
    if($desc=='No')
        $desc="";
    else
    {
        for($i=0;$i<strlen($desc);$i++)
        {
        if($desc[$i]=="_")
            $desc[$i]=str_replace("_", " ",$desc[$i]);
        else if($desc[$i]=="\\")
            $desc[$i]=str_replace("\\", "'",$desc[$i]);
        }
    }
    
    echo "<FORM name=Group id=Compose action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=member_insert&grp_id=".strip_tags(trim($_REQUEST[grp_id]))." method=POST >";
PopTable('header','Group');
echo '<table>
      <tr>
      <td>Group Name: </td>
      <td>'.TextInput_mail($_REQUEST['group_id'],'txtExistGrpName','','class=cell_medium readonly').'
      </td>
      </tr>
      <tr>
      <td>Description: </td>
      <td>'.TextInput_mail($desc,'txtExistGrpDesc','','class=cell_medium readonly').'
      </td>
      </tr>
      <tr>
      <td colspan=2>';
     echo  DrawHeader('','',"<INPUT TYPE=SUBMIT name=button id=button class=btn_big VALUE='Add Members' onclick='return mail_group_chk();'/>")
       . '</td>
        </tr>
        </table>';
     $lastName=$_REQUEST['last'];
     $firstName=$_REQUEST['first'];
     $userName=$_REQUEST['username'];
     $profile=$_REQUEST['profile'];
     $disable=$_REQUEST['_dis_user'];
     $allschools=$_REQUEST['_search_all_schools'];
    
     echo '<input type=hidden value='.$profile.' name=profile>';
      if(isset($_REQUEST['group_id']))  
      {
          $select1="select * from mail_group where GROUP_ID='".$_REQUEST['grp_id']."'";
          $groupselect=DBGet(DBQuery($select1));
             
          $member="select * from mail_groupmembers where GROUP_ID=".$groupselect[1]['GROUP_ID']."";
          $existuser=DBGet(DBQuery($member));
          $existuser=DBGet(DBQuery($member));
          foreach($existuser as $id=>$value)
          {
              $usernames[]=array('PROFILE_ID'=>$existuser[$id]['PROFILE'],'USERNAME'=>$existuser[$id]['USER_NAME']);                           
          }
         
          foreach($usernames as $id=>$value)
          {
               if($value['PROFILE_ID']!=3 ||$value['PROFILE_ID']!=4)
               {
                    $staff="select * from login_authentication,staff where login_authentication.user_id=staff.staff_id and USERNAME='$value[USERNAME]' and login_authentication.profile_id not in(3)";
                    $stafflist=DBGet(DBQuery($staff));
                    $staff_id[]=$stafflist[1]['STAFF_ID'];
               }
               if($value['PROFILE_ID']==3)
               {                               
                    $stu="select * from login_authentication,students where login_authentication.user_id=students.student_id and profile_id=3 and USERNAME='$value[USERNAME]'";                              
                    $stulist=DBGet(DBQuery($stu));
                    $stu_id[]=$stulist[1]['STUDENT_ID'];    
               }
                            
          }  
          $staff_id=  array_filter($staff_id);
          $stu_id= array_filter($stu_id);

          if($profile!=-1)//search by profile
          {
              if($profile==3)//students
              {
                if(User('PROFILE')=='teacher')
                   $user="SELECT * FROM students,login_authentication WHERE profile_id=3 and login_authentication.user_id=students.student_id and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''  AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING ( course_period_id ) WHERE course_periods.teacher_id = ".UserID().")";
                elseif(User('PROFILE')=='parent')
                  {
                    $parent_id=UserID();
                      $qr= DBGet(DBQuery('Select STUDENT_ID from students_join_people where person_id=\''.$parent_id.'\''));
                     $student_id=$qr[1]['STUDENT_ID'];
                   $user="SELECT * FROM students,login_authentication WHERE profile_id=3 and login_authentication.user_id=students.student_id  and students.student_id in (Select STUDENT_ID from students_join_people where person_id=".$parent_id.") and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''  AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].")";
                    
                 
                }
                  
                 elseif(UserProfileID()==1 || UserProfileID()==5)
                       
                  {
                     $user= "select * from students,login_authentication,student_enrollment WHERE profile_id=3 and login_authentication.user_id=students.student_id and students.student_id=student_enrollment.student_id and student_enrollment.school_id in(select school_id from staff_school_relationship where staff_id=".  UserID().") and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].")";   
                  }
                      else  
                      {
                   
                          $user="select * from students,login_authentication WHERE profile_id=3 and login_authentication.user_id=students.student_id and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].")";                
                
                      }
                }
               
              
              if($profile==2)//teachers
              {
                  if(User('PROFILE')=='parent')
                  {
                      $parent_id=UserID();
                      $qr= DBGet(DBQuery('Select STUDENT_ID from students_join_people where person_id=\''.$parent_id.'\''));
                     $student_id=$qr[1]['STUDENT_ID'];
                      
//                     echo  $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND staff_id  IN (Select distinct person_id from students_join_people where person_id<>".$parent_id.")";
                       $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND staff_id  IN (SELECT distinct(course_periods.teacher_id) FROM course_periods,schedule where schedule.course_period_id=course_periods.course_period_id and schedule.student_id in (Select STUDENT_ID from students_join_people where person_id=".$parent_id."))";
                  }
                  else if(User('PROFILE')=='student')
                  {
                       $studentId=UserStudentID();
                       $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND staff_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where schedule.student_id=".$studentId.")";
                  }
                  else if(UserProfileID()==1 || UserProfileID()==5)
                       
                  {
                    $user="SELECT * FROM login_authentication,staff,staff_school_relationship WHERE login_authentication.user_id=staff.staff_id  and staff_school_relationship.staff_id=staff.staff_id  and staff_school_relationship.school_id in (select school_id from staff_school_relationship where staff_id=".  UserID().")  and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID']." )";
                  }
                  else
                  {
                       $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID']." )";
                  }
              }
              if($profile==4)//parents
              {
                  if (User('PROFILE')=='teacher')
                  {
                    $teacher_id= UserID();
                    $user='SELECT * FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4 and people.profile_id='.$profile.' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id='.$groupselect[1]['GROUP_ID'].' ) and  TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' AND user_id IN (SELECT DISTINCT person_id FROM students_join_people WHERE student_id IN (SELECT student_id FROM students WHERE student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.$teacher_id.'\')))';
                  }
                  else if(User('PROFILE')=='admin')
                  {
                      $user='SELECT * FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4 and people.profile_id='.$profile.' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id='.$groupselect[1]['GROUP_ID'].' ) and  TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' ';        
                  }
                 else if(User('PROFILE')=='student')
                  {$student_id=  UserStudentID();
                      $user='SELECT * FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4 and people.profile_id='.$profile.' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id='.$groupselect[1]['GROUP_ID'].' ) and  TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' AND user_id IN (SELECT DISTINCT person_id FROM students_join_people WHERE student_id='.$student_id.' )';        
                  }
              }
              if($profile==0 ||$profile==1 ||$profile==5)//all types of admin
              {
                  $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=$profile and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].")";
              }
              if($lastName!="")
              {
                  $user=$user." AND LAST_NAME LIKE '$lastName%' ";
              }
              if($firstName!="")
              {
                  $user=$user." AND FIRST_NAME LIKE '$firstName%' ";
              }
              if($userName!="")
              {
                  $user=$user." AND USERNAME LIKE '$userName%' ";
              }
              if($disable=='' && ($profile==3 || $profile==4))//only enabled students 
              {
                  $user=$user." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
              }
              if($disable=='' && $profile!=3 && $profile!=4)//only enabled users
              {
                  $user=$user." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
              }
              if($disable=='Y')//with disabled users
              {
                  $user=$user." ";
              }
           
          }
          
          else 
          {    
               if(User('PROFILE')=='admin'  && UserProfileID()==0)//all types of admin
               {
                    $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id  AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id not in(3,4)";
                    $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,students WHERE login_authentication.user_id=students.student_id AND login_authentication.profile_id=3 AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id=3";
                    $user3="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id=4";
               }
                if(UserProfileID()==1 || UserProfileID()==5)//all types of admin
               {
                      $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff,staff_school_relationship WHERE login_authentication.user_id=staff.staff_id and staff_school_relationship.staff_id=staff.staff_id  and staff_school_relationship.school_id in (select school_id from staff_school_relationship where staff_id=".  UserID().") AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id not in(3,4)";
                    $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,students,student_enrollment WHERE login_authentication.user_id=students.student_id and students.student_id=student_enrollment.student_id and student_enrollment.school_id in(select school_id from staff_school_relationship where staff_id=".  UserID().")AND login_authentication.profile_id=3 AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id=3";
                      $user3="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,students_join_people,people  WHERE login_authentication.user_id=people.staff_id AND students_join_people.person_id in(select school_id from students,student_enrollment,students_join_people  where students.student_id=student_enrollment.student_id and students_join_people.student_id=students.student_id and student_enrollment.school_id in(select school_id from staff_school_relationship where  staff_id=".  UserID().")) and TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id=4";
//              $user3="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id AND people.staff_id in (select staff_id from staff_school_relationship where staff_id=".  UserID().") and TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id=4";
                    }
               if(User('PROFILE')=='teacher')//teachers
               {
                   $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff,staff_school_relationship WHERE login_authentication.user_id=staff.staff_id and staff_school_relationship.staff_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id in(0,1,5) and school_id in(select school_id from staff_school_relationship where staff_id=".UserID().")";//all types of admin
                   $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM students,login_authentication WHERE profile_id=3 and login_authentication.user_id=students.student_id and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''  AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING ( course_period_id ) WHERE course_periods.teacher_id = ".UserID().")";//scheduled students
                   $user3='SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4  AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id='.$groupselect[1]['GROUP_ID'].' ) and  TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' AND user_id IN (SELECT DISTINCT person_id FROM students_join_people WHERE student_id IN (SELECT student_id FROM students WHERE student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.UserID().'\')))';//parents                  
               }
               if(User('PROFILE')=='parent')//parents
               {
                   $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id in(0,1,5)";//all types of admin
                  $parent_id=UserID();
//                     
                       $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2  AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND staff_id  IN (SELECT distinct(course_periods.teacher_id) FROM course_periods,schedule where schedule.course_period_id=course_periods.course_period_id and schedule.student_id in (Select STUDENT_ID from students_join_people where person_id=".$parent_id."))";
//              $user3="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id"
                $user3="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM students,login_authentication WHERE profile_id=3 and login_authentication.user_id=students.student_id  and students.student_id in (Select STUDENT_ID from students_join_people where person_id=".$parent_id.") and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''  AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].")";   
               }
               if(User('PROFILE')=='student')//students
               {
                   $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") and login_authentication.profile_id in(0,1,5)";//all types of admin
                   
                 $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2  AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id=".$groupselect[1]['GROUP_ID'].") AND staff_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where schedule.student_id=".UserStudentID().")";//teachers                 
                
                   $student_id=  UserStudentID();
                       $user3='SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4  AND USERNAME NOT IN(SELECT USER_NAME from mail_groupmembers where group_id='.$groupselect[1]['GROUP_ID'].' ) and  TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' AND user_id IN (SELECT DISTINCT person_id FROM students_join_people WHERE student_id='.$student_id.' )'; 
               }
              if($lastName!="")
              {
                  $user1=$user1." AND LAST_NAME LIKE '$lastName%' ";
                  $user2=$user2." AND LAST_NAME LIKE '$lastName%' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                    $user3=$user3." AND LAST_NAME LIKE '$lastName%' ";
              }
              if($firstName!="")
              {
                  $user1=$user1." AND FIRST_NAME LIKE '$firstName%' ";
                  $user2=$user2." AND FIRST_NAME LIKE '$firstName%' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                    $user3=$user3." AND FIRST_NAME LIKE '$firstName%' ";
              }
              if($userName!="")
              {
                  $user1=$user1." AND USERNAME LIKE '$userName%' ";
                  $user2=$user2." AND USERNAME LIKE '$userName%' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                    $user3=$user3." AND USERNAME LIKE '$userName%' ";
              }

              if($disable=='' && ($profile==3 || $profile==4))//only enabled students 
              {
                  $user1=$user1." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
                  $user2=$user2." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                      $user3=$user3." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
              }
              if($disable=='' && $profile!=3 && $profile!=4)//only enabled users
              {
                  $user1=$user1." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
                  $user2=$user2." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                      $user3=$user3." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
              }
              if($disable=='Y')//with disabled users
              {
                  $user1=$user1." ";
                  $user2=$user2." ";
                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
                      $user2=$user2." ";
              }
              if(User('PROFILE')=='admin'|| User('PROFILE')=='teacher' || User('PROFILE')=='parent' || User('PROFILE')=='student')
                 $user=$user1." UNION ALL ".$user2." UNION ALL ".$user3;
             else 
                 $user=$user1." UNION ALL ".$user2;
          }
 
              $userlist=DBGet(DBQueryMod($user)); 
               
              if($_REQUEST['_search_all_schools']!='Y')
              {
                  $final_arr = array();
                    foreach($userlist as $key=>$value)
                {
                    if($userlist[$key]['PROFILE_ID']==3){
                     $select="SELECT se.*,up.* FROM student_enrollment se,user_profiles up WHERE up.ID=".$userlist[$key]['PROFILE_ID']." and se.school_id=".UserSchool()." AND se.student_id='".$userlist[$key]['USER_ID']."'";
                    $profile=DBGet(DBQuery($select));
                    foreach($profile as $k=>$v){
                    $_arr['USERNAME'] = $userlist[$key]['USERNAME'];
                    $_arr['LAST_NAME'] = $userlist[$key]['LAST_NAME'];
                    $_arr['USER_ID'] = $profile[$k]['STUDENT_ID'];
                    $_arr['FIRST_NAME']= $userlist[$key]['LAST_NAME'].' '.$userlist[$key]['FIRST_NAME'];
                    $_arr['PROFILE_ID']= $profile[$k]['PROFILE'];
                    $_arr['IS_DISABLE']= $userlist[$key]['IS_DISABLE'];
                    array_push($final_arr,$_arr);
                    
                    }
                    }
                    
                    else if($userlist[$key]['PROFILE_ID']==4){
                     
//                        $sql = "select student_id from  students_join_people where person_id=".$userlist[$key]['USER_ID'];
//                        $fetch = DBGet(DBQuery($sql));
//                        foreach($fetch as $k1=>$v1){
                       if(User('PROFILE')=='student')
                        $select="SELECT se.*,up.* FROM student_enrollment se,user_profiles up WHERE up.ID=".$userlist[$key]['PROFILE_ID']." and se.school_id=".UserSchool()." AND se.student_id=".  UserStudentID()."";
                       if(User('PROFILE')=='teacher')
                            $select="SELECT se.*,up.* FROM student_enrollment se,user_profiles up WHERE up.ID=".$userlist[$key]['PROFILE_ID']." and se.school_id=".UserSchool()." AND se.student_id in (select schedule.student_id from  schedule,course_periods,students_join_people where course_periods.course_period_id=schedule.course_period_id  and  schedule.student_id=students_join_people.student_id and students_join_people.person_id=".$userlist[$key]['USER_ID']." and teacher_id=".UserID().")";
                       else
                         $select="SELECT se.*,up.* FROM student_enrollment se,user_profiles up WHERE up.ID=".$userlist[$key]['PROFILE_ID']." and se.school_id=".UserSchool()." AND se.student_id in (select student_id from  students_join_people where person_id=".$userlist[$key]['USER_ID'].")";
                    $profile=DBGet(DBQuery($select));
                    
                    
                    foreach($profile as $k=>$v){
                    $_arr['USERNAME'] = $userlist[$key]['USERNAME'];
                    $_arr['LAST_NAME'] = $userlist[$key]['LAST_NAME'];
                    $_arr['USER_ID'] = $userlist[$key]['USER_ID'];
                    $_arr['FIRST_NAME']= $userlist[$key]['LAST_NAME'].' '.$userlist[$key]['FIRST_NAME'];
                    $_arr['PROFILE_ID']= $profile[$k]['PROFILE'];
                    $_arr['IS_DISABLE']= $userlist[$key]['IS_DISABLE'];
                    array_push($final_arr,$_arr);
                  
                    }
//                    }
                    }
                    else{ 
                        $select="SELECT se.*,up.* FROM staff_school_relationship se,user_profiles up WHERE up.ID=".$userlist[$key]['PROFILE_ID']." and se.school_id=".UserSchool()." AND se.staff_id='".$userlist[$key]['USER_ID']."'";
                    $profile=DBGet(DBQuery($select));
                    foreach($profile as $k=>$v){
                    $_arr['USERNAME'] = $userlist[$key]['USERNAME'];
                    $_arr['LAST_NAME'] = $userlist[$key]['LAST_NAME'];
                    $_arr['USER_ID'] = $profile[$k]['STAFF_ID'];
                    $_arr['FIRST_NAME']= $userlist[$key]['LAST_NAME'].' '.$userlist[$key]['FIRST_NAME'];
                    $_arr['PROFILE_ID']= $profile[$k]['PROFILE'];
                    $_arr['IS_DISABLE']= $userlist[$key]['IS_DISABLE'];
                    array_push($final_arr,$_arr);
                    }
                    }
                }
                   
                array_unshift($final_arr,"");
                    unset($final_arr[0]);
                    $userlist = $final_arr;
                
              }
              else{
            
              foreach($userlist as $key=>$value)
              {
                    $select="SELECT * FROM user_profiles WHERE ID='".$userlist[$key]['PROFILE_ID']."'";
                    $profile=DBGet(DBQuery($select));
                    $userlist[$key]['FIRST_NAME']=$userlist[$key]['LAST_NAME'].' '.$userlist[$key]['FIRST_NAME'];
                    $userlist[$key]['PROFILE_ID']=$profile[1]['PROFILE'];
              }
              }
              
             
//              exit;
              
              if($_REQUEST['_dis_user']=='Y')
              $columns=array('FIRST_NAME'=>'Member','USERNAME'=>'User Name','PROFILE_ID'=>'Profile','STATUS'=>'Status');
              else
              $columns=array('FIRST_NAME'=>'Member','USERNAME'=>'User Name','PROFILE_ID'=>'Profile');
              $extra['SELECT'] = ",Concat(NULL) AS CHECKBOX";
              $extra['LO_group'] = array('STAFF_ID');
              $extra['columns_before']= array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'groups\');"><A>');
              $extra['new'] = true;
              if(is_array($extra['columns_before']))
              {
                    $LO_columns = $extra['columns_before'] + $columns;
                    $columns = $LO_columns;
              }
              foreach($userlist as $id=>$value)
              {
                    $extra['columns_before']['CHECKBOX'] = "<INPUT type=checkbox name=groups[".$value['USER_ID'].",".$value['PROFILE_ID']."] value=Y>";
                    $userlist[$id]=$extra['columns_before']+$value;
              }
              if($_REQUEST['_dis_user']=='Y')
              {
                  foreach($userlist as $ui=>$ud)
                  {
                      
                      if($ud['PROFILE_ID']=='student')      
//                          echo 'student'.$ud['USER_ID'].'<br>';
                      $chck_status=DBGet(DBQuery('SELECT COUNT(1) as DISABLED FROM students s,student_enrollment se WHERE se.STUDENT_ID=s.STUDENT_ID AND s.STUDENT_ID='.$ud['USER_ID'].' AND se.SYEAR='.UserSyear().' AND (s.IS_DISABLE=\'Y\' OR (se.END_DATE<\''.date('Y-m-d').'\'  AND se.END_DATE IS NOT NULL AND se.END_DATE<>\'0000-00-00\' ))'));
                      elseif($ud['PROFILE_ID']=='parent')
//                          echo 'parent'.$ud['USER_ID'].'<br>';
                      $chck_status=DBGet(DBQuery('SELECT COUNT(1) as DISABLED FROM people WHERE STAFF_ID='.$ud['USER_ID'].' AND IS_DISABLE=\'Y\' '));   
                      else
//                          echo 'else'.$ud['USER_ID'].'<br>';
                      $chck_status=DBGet(DBQuery('SELECT COUNT(1) as DISABLED FROM staff s,staff_school_relationship se WHERE se.STAFF_ID=s.STAFF_ID AND s.STAFF_ID='.$ud['USER_ID'].' AND se.SYEAR='.UserSyear().' AND (s.IS_DISABLE=\'Y\' OR (se.END_DATE<\''.date('Y-m-d').'\'  AND se.END_DATE IS NOT NULL AND se.END_DATE<>\'0000-00-00\' ))'));
                      
                      
                        if($chck_status[1]['DISABLED']!=0)
                        $userlist[$ui]['STATUS']="<font style='color:red'>Inactive</font>";   
                        else
                        $userlist[$ui]['STATUS']="<font style='color:green'>Active</font>";
                  }
              }
              
              ListOutputExcel($userlist,$columns,'Member','Members','',array(),array('search'=>false),'');                          
          
      }
      
     echo "</FORM>";
     PopTable('footer');
}

if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='add_group')
{
    if(!isset($_REQUEST['search']))
    {
        echo "<FORM name=Group id=Compose action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=group_insert method=POST >";
PopTable('header','Group');
echo '<table>
      <tr>
      <td>Group Name: </td>
      <td>'.TextInput_mail('','txtGrpName','','onkeyup=groups(this.value) class=cell_medium').'
      </td>
      </tr>
      <tr>
      <td>Description: </td>
      <td>'.TextInput_mail('','txtGrpDesc','','onkeyup=desc(this.value) class=cell_medium').'
      </td>
      </tr>
      <tr>
      <td colspan=2>';
     echo  DrawHeader('','',"<INPUT TYPE=SUBMIT name=button id=button class=btn_wide VALUE='Add Group' onclick='return mail_group_chk();'/>")
       . '</td>
        </tr>
        </table>';
     
echo "</FORM>";

		if($_SESSION['staff_id'])
		{
			unset($_SESSION['staff_id']);
			echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
		}

		echo '<BR>';
		
    }
    if(isset($_REQUEST['search']) && $_REQUEST['search']=='true' && $_REQUEST['modfunc']=='add_group')
    {        
        
    echo "<FORM name=Group id=Compose action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=group_insert method=POST >";
    PopTable('header','Group');
   
    echo '<table>
      <tr>
      <td>Group Name: </td>
      <td>'.TextInput_mail($_REQUEST['groupname'],'txtGrpName','','class=cell_medium').'
      </td>
      </tr>
      <tr>
      <td>Description: </td>
      <td>'.TextInput_mail($_REQUEST['groupdescription'],'txtGrpDesc','','class=cell_medium').'
      </td>
      </tr>
      <tr>
      <td colspan=2>';
     echo  DrawHeader('','',"<INPUT TYPE=SUBMIT name=button id=button class=btn_medium VALUE='Add Group' onclick='return mail_group_chk();'/>")
       . '</td>
        </tr>
        </table>';

     $lastName=$_REQUEST['last'];
     $firstName=$_REQUEST['first'];
     $userName=$_REQUEST['username'];
     $profile=$_REQUEST['profile'];
     $disable=$_REQUEST['_dis_user'];
     $allschools=$_REQUEST['_search_all_schools'];
//          if($profile!=-1)//search by profile
//          {
//              if($profile==3)//students
//              {
//                if(User('PROFILE')=='teacher')
//                   $user="SELECT * FROM students,login_authentication WHERE profile_id=3 and login_authentication.user_id=students.student_id and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''  AND student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING ( course_period_id ) WHERE course_periods.teacher_id = ".UserID().")";
//                else   
//                   $user="select * from students,login_authentication WHERE profile_id=3 and login_authentication.user_id=students.student_id and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' ";                
//              }
//              if($profile==2)//teachers
//              {
//                  if(User('PROFILE')=='parent')
//                  {
//                       $parent_id=UserID();
//                       $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''  AND staff_id IN(Select distinct person_id from students_join_people where person_id<>".$parent_id.")";
//
//                  }
//                  if(User('PROFILE')=='student')
//                  {
//                       $studentId=UserStudentID();
//                       $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND staff_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where schedule.student_id=".$studentId.")";
//                  }
//                  if(User('PROFILE')=='admin')
//                  {
//                       $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''";
//                  }
//              }
//              if($profile==4)//parents
//              {
//                  if (User('PROFILE')=='teacher')
//                  {
//                    $teacher_id= UserID();
//                    $user='SELECT * FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4 and people.profile_id='.$profile.' and  TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' AND user_id IN (SELECT DISTINCT person_id FROM students_join_people WHERE student_id IN (SELECT student_id FROM students WHERE student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.$teacher_id.'\')))';
//                  }
//                  if(User('PROFILE')=='admin')
//                  {
//                      $user='SELECT * FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4 and people.profile_id='.$profile.' and  TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' ';        
//                  }
//                   if(User('PROFILE')=='student')
//                  {
//                       $studentId=UserStudentID();
//                       $user='SELECT * FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4 and people.profile_id='.$profile.' and  TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' ';   
//                  }
//              }
//              if($profile==0 ||$profile==1 ||$profile==5)//all types of admin
//              {
//                  $user="SELECT * FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=$profile and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' ";
//              }
//              if($lastName!="")
//              {
//                  $user=$user." AND LAST_NAME LIKE '$lastName%' ";
//              }
//              if($firstName!="")
//              {
//                  $user=$user." AND FIRST_NAME LIKE '$firstName%' ";
//              }
//              if($userName!="")
//              {
//                  $user=$user." AND USERNAME LIKE '$userName%' ";
//              }
//
//              if($disable=='' && ($profile==3 || $profile==4))//only enabled students 
//              {
//                  $user=$user." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
//              }
//              if($disable=='' && $profile!=3 && $profile!=4)//only enabled users
//              {
//                  $user=$user." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
//              }
//              if($disable=='Y')//with disabled users
//              {
//                  $user=$user." ";
//              }
//          }
//         else 
//          {          
//               if(User('PROFILE')=='admin')//all types of admin
//               {
//                    $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' and login_authentication.profile_id not in(3,4)";
//                    $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,students WHERE login_authentication.user_id=students.student_id AND login_authentication.profile_id=3 AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' and login_authentication.profile_id=3";
//                    $user3="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND login_authentication.profile_id=4";
//               }
//               if(User('PROFILE')=='teacher')//teachers
//               {
//                   $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' and login_authentication.profile_id in(0,1,5)";//all types of admin
//                   $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM students,login_authentication WHERE profile_id=3 and login_authentication.user_id=students.student_id and  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> ''  AND student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING ( course_period_id ) WHERE course_periods.teacher_id = ".UserID().")";//scheduled students
//                   $user3='SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,people WHERE login_authentication.user_id=people.staff_id and login_authentication.profile_id=4 AND TRIM( IFNULL( USERNAME, \'\' ) ) <> \'\' AND user_id IN (SELECT DISTINCT person_id FROM students_join_people WHERE student_id IN (SELECT student_id FROM students WHERE student_id IN (SELECT DISTINCT student_id FROM course_periods INNER JOIN schedule USING (course_period_id ) WHERE course_periods.teacher_id = \''.UserID().'\')))';//parents                  
//               }
//               if(User('PROFILE')=='parent')//parents
//               {
//                   $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' and login_authentication.profile_id in(0,1,5)";//all types of admin
//                   $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND staff_id NOT IN (Select distinct person_id from students_join_people where person_id<>".UserID().")";//parents                
//
//               }
//               if(User('PROFILE')=='student')//students
//               {
//                   $user1="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id AND TRIM( IFNULL( USERNAME, '' ) ) <> '' AND TRIM( IFNULL( PASSWORD, '' ) ) <> '' and login_authentication.profile_id in(0,1,5)";//all types of admin
//                   $user2="SELECT username,LAST_NAME,first_name,login_authentication.profile_id,is_disable,login_authentication.user_id FROM login_authentication,staff WHERE login_authentication.user_id=staff.staff_id and login_authentication.profile_id=2 and staff.PROFILE_ID=$profile AND  TRIM( IFNULL( USERNAME, '' ) ) <> '' AND  TRIM( IFNULL( PASSWORD, '' ) ) <> '' AND staff_id IN(Select distinct teacher_id from course_periods INNER JOIN schedule using(course_period_id) where schedule.student_id=".UserStudentID().")";//teachers                 
//               }
//              if($lastName!="")
//              {
//                  $user1=$user1." AND LAST_NAME LIKE '$lastName%' ";
//                  $user2=$user2." AND LAST_NAME LIKE '$lastName%' ";
//                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
//                    $user3=$user3." AND LAST_NAME LIKE '$lastName%' ";
//              }
//              if($firstName!="")
//              {
//                  $user1=$user1." AND FIRST_NAME LIKE '$firstName%' ";
//                  $user2=$user2." AND FIRST_NAME LIKE '$firstName%' ";
//                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
//                    $user3=$user3." AND LAST_NAME LIKE '$firstName%' ";
//              }
//              if($userName!="")
//              {
//                  $user1=$user1." AND USERNAME LIKE '$userName%' ";
//                  $user2=$user2." AND USERNAME LIKE '$userName%' ";
//                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
//                    $user3=$user3." AND LAST_NAME LIKE '$firstName%' ";
//              }
//
//              if($disable=='' && ($profile==3 || $profile==4))//only enabled students 
//              {
//                  $user1=$user1." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
//                  $user2=$user2." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
//                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
//                      $user3=$user3." AND TRIM( IFNULL( is_disable, 'NULL' ) ) = 'NULL' ";
//              }
//              if($disable=='' && $profile!=3 && $profile!=4)//only enabled users
//              {
//                  $user1=$user1." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
//                  $user2=$user2." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
//                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
//                      $user3=$user3." AND TRIM( IFNULL( is_disable, '' ) ) <> 'Y' ";
//              }
//              if($disable=='Y')//with disabled users
//              {
//                  $user1=$user1." ";
//                  $user2=$user2." ";
//                  if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
//                      $user2=$user2." ";
//              }
//              if(User('PROFILE')=='admin'||User('PROFILE')=='teacher')
//                 $user=$user1." UNION ALL ".$user2." UNION ALL ".$user3;
//             else 
//                 $user=$user1." UNION ALL ".$user2;
//          }
         
              $userlist=DBGet(DBQueryMd($user)); 
              
              foreach($userlist as $key=>$value)
              {
                    $select="SELECT * FROM user_profiles WHERE ID='".$userlist[$key]['PROFILE_ID']."'";
                    $profile=DBGet(DBQuery($select));
                    $userlist[$key]['FIRST_NAME']=$userlist[$key]['LAST_NAME'].' '.$userlist[$key]['FIRST_NAME'];
                    $userlist[$key]['PROFILE_ID']=$profile[1]['PROFILE'];
              }
              $columns=array('FIRST_NAME'=>'Member','USERNAME'=>'User Name','PROFILE_ID'=>'Profile');
              $extra['SELECT'] = ",Concat(NULL) AS CHECKBOX";
              $extra['LO_group'] = array('STAFF_ID');
              $extra['columns_before']= array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'groups\');"><A>');
              $extra['new'] = true;
              if(is_array($extra['columns_before']))
              {
                    $LO_columns = $extra['columns_before'] + $columns;
                    $columns = $LO_columns;
              }
              foreach($userlist as $id=>$value)
              {

                  $extra['columns_before']['CHECKBOX'] = "<INPUT type=checkbox name=groups[".$value['USER_ID'].",".$value['PROFILE_ID']."] value=Y>";
                  $userlist[$id]=$extra['columns_before']+$value;
              }
              ListOutput( $userlist,$columns,'Member','Members','',array(),array('search'=>false),'');                          
          
               echo '</FORM>';  
                }
          
PopTable('footer');
   
}

if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='member_insert')
 {
    
       if($_REQUEST['groups'])
     {
           
        $grp=array_keys($_REQUEST['groups']);
         $select="select * from mail_group where group_id='".$_REQUEST['grp_id']."'";
        
        $grp_select=DBGet(DBQuery($select));
        $grp_select[1]['GROUP_ID'];
        $grp_select['group_name']=$grp_select[1]['GROUP_NAME'];
        $grp_select['description']=$grp_select[1]['DESCRIPTION'];
        foreach($grp as $i=>$j)
        {
            $idProfile=  explode(",", $j);
            $member_select=  DBGet(DBQuery("Select * from login_authentication,user_profiles where login_authentication.profile_id=user_profiles.id and user_profiles.profile='".$idProfile[1]."' and login_authentication.user_id='$idProfile[0]'  "));
           
            $grp_members='INSERT INTO mail_groupmembers(GROUP_ID,USER_NAME,profile) VALUES(\''.$grp_select[1]['GROUP_ID'].'\',\''.$member_select[1]['USERNAME'].'\',\''.$member_select[1]['PROFILE_ID'].'\')';
            $members=DBGet(DBQuery($grp_members));
        }
        
    } 
    else
    {
        PopTable('header','Alert Message');
        echo "<CENTER><h4>Please select atleast one member to add</h4><br><FORM action=$PHP_tmp_SELF METHOD=POST><INPUT type=button class=btn_medium name=delete_cancel value=OK onclick='window.location=\"Modules.php?modname=messaging/Group.php\"'></FORM></CENTER>";
        PopTable('footer');
	return false;
    }
        
    unset($_REQUEST['modfunc']);
    echo "<script>window.location='Modules.php?modname=messaging/Group.php'</script>";
 }
 
if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='group_insert')
{
    
     $exist_group=DBGet(DBQuery("SELECT * FROM mail_group WHERE USER_NAME='$userName'"));
    foreach($exist_group as $id=>$value)
    {
        if(strtolower($exist_group[$id]['GROUP_NAME'])==strtolower($_REQUEST['txtGrpName']))
            
        {
            PopTable('header','Alert Message');
            echo "<CENTER><h4>groupname already exist for $userName</h4><br><FORM action=$PHP_tmp_SELF METHOD=POST><INPUT type=button class=btn_medium name=delete_cancel value=OK onclick='window.location=\"Modules.php?modname=messaging/Group.php\"'></FORM></CENTER>";
            PopTable('footer');
            return false;
        }
    }
    $description=$_REQUEST['txtGrpDesc'];
    if($description=="")
        $description='N';
        if($_REQUEST['txtGrpName'])
        {
            $group='INSERT INTO mail_group(GROUP_NAME,DESCRIPTION,USER_NAME,CREATION_DATE) VALUES(\''. str_replace("'", "\\'",$_REQUEST['txtGrpName']).'\',\''.str_replace("'", "\\'",$description).'\',\''.$userName.'\',now())';  
            $group_info=DBQuery($group);

            if($_REQUEST['groups'])
            {            
                $grp=array_keys($_REQUEST['groups']);
               // print_r($grp);
                $select="select group_id from mail_group where group_name='".str_replace("'","\'",$_REQUEST['txtGrpName'])."'";
                $grp_select=DBGet(DBQuery($select));
                $grp_select[1]['GROUP_ID'];
                foreach($grp as $i=>$j)
                {
                     $idProfile=  explode(",", $j);
      
                    $member_select=  DBGet(DBQuery("Select * from login_authentication,user_profiles where login_authentication.profile_id=user_profiles.id and user_profiles.profile='".$idProfile[1]."' and login_authentication.user_id='$idProfile[0]'  "));
                   
                    $grp_members='INSERT INTO mail_groupmembers(GROUP_ID,USER_NAME,profile) VALUES(\''.$grp_select[1]['GROUP_ID'].'\',\''.$member_select[1]['USERNAME'].'\',\''.$member_select[1]['PROFILE_ID'].'\')';
                    $members=DBGet(DBQuery($grp_members));
                }
            } 

            unset($_REQUEST['modfunc']);
            echo "<script>window.location='Modules.php?modname=messaging/Group.php'</script>";
        }
        else 
        {
            echo "<script>window.location='Modules.php?modname=messaging/Group.php&modfunc=add_group'</script>";
        }
    }
    
if(isset($_REQUEST['modfunc']) && $_REQUEST['modfunc']=='members' && $_REQUEST['groupid'])
{ 
    if(isset($_REQUEST['groupname']))
    {
        $gid=$_REQUEST['groupid'];
    $exist_group=DBGet(DBQuery("SELECT * FROM mail_group WHERE USER_NAME='$userName' and group_id!='$gid'"));
    foreach($exist_group as $id=>$value)
    {
        if($exist_group[$id]['GROUP_NAME']==$_REQUEST[groupname])
        {
            PopTable('header','Alert Message');
            echo "<CENTER><h4>groupname already exist for $userName</h4><br><FORM action=$PHP_tmp_SELF METHOD=POST><INPUT type=button class=btn_medium name=delete_cancel value=OK onclick='window.location=\"Modules.php?modname=messaging/Group.php\"'></FORM></CENTER>";
            PopTable('footer');
            exit;
        }
    }
        
       $update="UPDATE mail_group SET GROUP_NAME='".str_replace("'", "\\'",$_REQUEST[groupname])."' WHERE GROUP_ID=$_REQUEST[groupid]";
       
       $update_group=DBGet(DBQuery($update));
    }
     if(isset($_REQUEST['groupdesc']))
        {
         if(trim($_REQUEST['groupdesc'])!="")
            $update="UPDATE mail_group SET DESCRIPTION='".str_replace("'", "\\'",$_REQUEST[groupdesc])."' WHERE GROUP_ID=$_REQUEST[groupid]";
         else
             $update="UPDATE mail_group SET DESCRIPTION='N' WHERE GROUP_ID=$_REQUEST[groupid]";
            $update_group=DBGet(DBQuery($update));
        }
        if(isset($_REQUEST['group']))
        {
   if(implode(',',$_REQUEST['group'])=='')
   {
       $select="select * from mail_groupmembers where group_id=$_REQUEST[groupid]";
        $list=DBGet(DBQuery($select));
         foreach($list as $m=>$n)
        {
             if($list[$m]['ID'])
                $del_id[]=$list[$m]['ID'];
        }
   
       $id=implode(',',$del_id);
       $select="DELETE FROM mail_groupmembers WHERE GROUP_ID=$_REQUEST[groupid] AND ID IN($id)";
       $not_in_group=DBGet(DBQuery($select));
       
       echo "<script>window.location='Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."'</script>";
       
   }     
   else
   {
       $not_select="select * from mail_groupmembers where GROUP_ID=$_REQUEST[groupid]";
        $list1=DBGet(DBQuery($not_select));
        foreach($list1 as $i=>$j)
        {
            $id_list[]=$j['ID'];
        }
        $id3=implode(',',$id_list);
       $id1=array_keys($_REQUEST['group']);
       $id2= implode(',',$id1);
       if($id2==$id3)
           echo "<script>window.location='Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."'</script>";
        else   
        {
       $select="SELECT * FROM mail_groupmembers WHERE GROUP_ID=$_REQUEST[groupid] AND ID NOT IN($id2)";
            $list=DBGet(DBQuery($select));
       foreach($list as $i=>$j)
       {
           $del_id1[]=$list[$i]['ID'];
       } 
        $id=implode(',',$del_id1);
        $select="DELETE FROM mail_groupmembers WHERE GROUP_ID=$_REQUEST[groupid] AND ID IN($id)";
        $not_in_group=DBGet(DBQuery($select));
        echo "<script>window.location='Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."'</script>";
        
        }
   }
 
        }
         echo "<script>window.location='Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."'</script>";
}
?>
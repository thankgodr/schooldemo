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


DrawBC("users >> ".ProgramTitle());
PopTable ('header','Upload Staff\'s Photo');
$UserPicturesPath='assets/userphotos/';
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='edit')
{
	if($UserPicturesPath && (($file = @fopen($picture_path=$UserPicturesPath.'/'.UserStaffID().'.JPG','r')) || ($file = @fopen($picture_path=$UserPicturesPath.'/'.UserStaffID().'.JPG','r'))))
	{
	echo '<div align=center><IMG SRC="'.$picture_path.'?id='.rand(6,100000).'" width=150 class=pic></div><div class=break></div>';
	}
	unset($_REQUEST['modfunc']);
}

if(UserStaffID())
{
$profile=DBGet(DBQuery('SELECT PROFILE FROM staff WHERE STAFF_ID=\''.UserStaffID().'\' '));
if($profile[1]['PROFILE']!='parent')
{
if(clean_param($_REQUEST['action'],PARAM_ALPHAMOD)=='upload' && $_FILES['file']['name'])
{
	$target_path=$UserPicturesPath.'/'.UserStaffID().'.JPG';
	$destination_path = $UserPicturesPath;	    #$target_path=$UserPicturesPath.UserSyear().'/'.User('STAFF_ID').'.JPG';
        $upload= new upload();
	$upload->target_path=$target_path;
	$upload->deleteOldImage();
	$upload->destination_path=$destination_path;
	$upload->name=$_FILES["file"]["name"];
	$upload->setFileExtension();
	$upload->fileExtension;
	$upload->validateImage();
	if($upload->wrongFormat==1){
	$_FILES["file"]["error"]=1;
	}
	
	if ($_FILES["file"]["error"] > 0)
        {
        $msg = "<font color=red><b>Cannot upload file. Only jpeg, jpg, png, gif files are allowed.</b></font>";
        echo '
            '.$msg.'
            <form enctype="multipart/form-data" action="Modules.php?modname=users/UploadUserPhoto.php&action=upload" method="POST">';
    echo '<div align=center>Select image file: <input name="file" type="file" /><br /><br>
    <input type="submit" value="Upload" class=btn_medium />&nbsp;<input type=button class=btn_medium value=Cancel onclick=\'load_link("Modules.php?modname=users/User.php");\'></div>
    </form>';
    PopTable ('footer');
        }
  	else
    {
	  move_uploaded_file($_FILES["file"]["tmp_name"], $upload->target_path);
	  @fopen($upload->target_path,'r');
	  echo '<div align=center><IMG SRC="'.$upload->target_path.'?id='.rand(6,100000).'" width=150 class=pic></div><div class=break></div>';
	  fclose($upload->target_path);
      echo "<b>Copied file to " .$upload->destination_path."</b><p>";
      $filename =  $upload->target_path;
	  PopTable ('footer');
    }    
}
else
{
echo '
'.$msg.'
<form enctype="multipart/form-data" action="Modules.php?modname=users/UploadUserPhoto.php&action=upload" method="POST">';
echo '<div align=center>Select image file: <input name="file" type="file" /><br /><br>
<input type="submit" value="Upload" class=btn_medium />&nbsp;<input type=button class=btn_medium value=Cancel onclick=\'load_link("Modules.php?modname=users/User.php");\'></div>
</form>';
PopTable ('footer');
}
}
else
{
echo 'Cannot upload parent\'s picture.';
PopTable ('footer');  
}
}
else
{
	echo 'Please select a staff first! from the <b>"Staff"</b> Tab';
	PopTable ('footer');
}

class upload{

var $target_path;
var $destination_path;
var $name;
var $fileExtension;
var $allowExtension=array("jpg","jpeg","png","gif","bmp");
var $wrongFormat=0;
function deleteOldImage(){
if(file_exists($this->target_path))
	unlink($this->target_path);
}

function setFileExtension(){
$this->fileExtension=strtolower(substr($this->name,strrpos($this->name,".")+1));
}

function validateImage(){
if(!in_array($this->fileExtension, $this->allowExtension)){
$this->wrongFormat=1;
}
}
function get_file_extension($file_name) {
return end(explode('.',$file_name));
}
}
?>
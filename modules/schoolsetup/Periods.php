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

    if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && ($_POST['values'] || $_REQUEST['ajax']) && AllowEdit())
    {
        foreach($_REQUEST['values'] as $id=>$columns)
        {
            if(!(isset($columns['TITLE']) && trim($columns['TITLE'])==''))
            {
                if($columns['START_HOUR'])
                {
                    if (strlen($columns['START_MINUTE'])<2)
                    {
                        $sm= '0'.$columns['START_MINUTE'];
                        $columns['START_TIME'] = $columns['START_HOUR'].':'.$sm.' '.$columns['START_M'];
                    }
                    else 
                        $columns['START_TIME'] = $columns['START_HOUR'].':'.$columns['START_MINUTE'].' '.$columns['START_M'];
                    $columns['START_TIME'] =date("H:i", strtotime($columns['START_TIME']));
                    unset($columns['START_HOUR']);unset($columns['START_MINUTE']);unset($columns['START_M']);
                }
                if($columns['END_HOUR'])
                {
                    if (strlen($columns['END_MINUTE'])<2)
                    {
                        $em= '0'.$columns['END_MINUTE'];
                        $columns['END_TIME'] = $columns['END_HOUR'].':'.$em.' '.$columns['END_M'];
                    }
                    else
                        $columns['END_TIME'] = $columns['END_HOUR'].':'.$columns['END_MINUTE'].' '.$columns['END_M'];
                        $columns['END_TIME'] =date("H:i", strtotime($columns['END_TIME']));
                    unset($columns['END_HOUR']);unset($columns['END_MINUTE']);unset($columns['END_M']);
                }
                ##############################################################################################################
                    if($id!='new')
                    {
                        $sql = 'UPDATE school_periods SET ';
                        $title_change='';
                        foreach($columns as $column=>$value)
                        {
                            $value=trim(paramlib_validation($column,$value));
                            if($column=='ignore_scheduling' && $value==''){
                                $sql .= $column.'=NULL';
                                $go=true;
                            }
                            elseif($column=='ATTENDANCE')
                            {
                                if($value=='')
                                {
//                               
                                    $per_attn_check=  DBGet(DBQuery('SELECT COUNT(*) AS TOTAL FROM course_period_var WHERE PERIOD_ID='.$id.' AND DOES_ATTENDANCE=\'Y\''));
                                 
                                   
                                    if($per_attn_check[1]['TOTAL']>0)
                                    {
                                        $err='Cannot modify Used for Attendance as period is associated';
                                        $go=false;
                                    }
                                    else
                                    {
                                        $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","'",$value)).'\',';
                                        $go=true;
                                    }
                                }
                                else
                                {
                                    $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","'",$value)).'\',';
                                        $go=true;
                                }
                                
                            }
                            elseif(strtolower($column)=='start_time' || strtolower($column)=='end_time'){
                            $checker=DBGet(DBQuery('SELECT COUNT(1) as TOTAL FROM schedule s,course_period_var cp WHERE cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND (s.END_DATE>\''.date('Y-m-d').'\' OR s.END_DATE IS NULL) AND cp.PERIOD_ID=\''.$id.'\' '));
                            if($checker[1]['TOTAL']==0)
                            {
                            $sql .= $column.'=\''.str_replace("'","'",str_replace("\'","'",$value)).'\',';
                            $go=true;
                            }
                            else
                            {
                            $err='Cannot modify start time or end time as period is associated';
                            $go=false;
                            }
                        }
                            else{
                            if($column=='TITLE')
                            {
                            $title_change=str_replace("'","''",str_replace("\'","'",$value));
                            }
                            $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","'",$value)).'\',';
                            $go=true;
                        }
                        }                        
                        $sql = substr($sql,0,-1) . ' WHERE PERIOD_ID=\''.$id.'\'';
                       
                        $sql = str_replace('&amp;', "", $sql);
                        $sql = str_replace('&quot', "", $sql);
                        $sql = str_replace('&#039;', "", $sql);
                        $sql = str_replace('&lt;', "", $sql);
                        $sql = str_replace('&gt;', "", $sql);
                        if($go)
                        {
                        DBQuery($sql);
                        if($title_change!='')
                        {
                        $check_for_cps=DBGet(DBQuery('SELECT COURSE_PERIOD_ID,TITLE FROM course_periods WHERE COURSE_PERIOD_ID='.$id));
                        foreach($check_for_cps as $cpi=>$cpd)
                        {
                            $old_title=explode('-',$cpd['TITLE']);
                            $old_title[0]=$title_change;
                            $old_title=implode(' - ',$old_title);
                            $old_title = str_replace("'","''",str_replace("\'","''",$old_title));
                            DBQuery('UPDATE course_periods SET TITLE=\''.$old_title.'\' WHERE COURSE_PERIOD_ID='.$cpd['COURSE_PERIOD_ID']);
                        }
                        
                        }
                        }

                        # -------------------------- Length Update Start -------------------------- #

                        $sql_get_length = 'SELECT start_time, end_time from school_periods WHERE period_id=\''.$id.'\'';

//                        $res_get_length = mysql_query($sql_get_length);
                        $row_get_length = DBGet(DBQuery($sql_get_length));				
                       $start_time = strtotime(date('m/d/Y') .' '.$row_get_length[1]['START_TIME']);
                        $end_time = strtotime(date('m/d/Y') .' '.$row_get_length[1]['END_TIME']);
                        if($start_time>$end_time)
                            $end_time = strtotime(date('m/d/Y') .' '.$row_get_length[1]['END_TIME'])+86400;

                        $length = ($end_time-$start_time)/60;

                        $sql_length_update = 'UPDATE school_periods set length = '.$length.' where period_id=\''.$id.'\'';
                        $res_length_update = DBQuery($sql_length_update);

                        # --------------------------- Length Update End --------------------------- #
                        
                    }
                    else
                    {
                        
                        $sql='SELECT TITLE,SHORT_NAME,SORT_ORDER,START_TIME,END_TIME FROM  school_periods WHERE SYEAR= \''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'';
                        $periods=DBGET(DBQuery($sql));
                        for($i=1;$i<=count($periods);$i++)
                        {
                            $shortname[$i]=$periods[$i]['SHORT_NAME'];
                            $p_title[$i]=$periods[$i]['TITLE'];
                            $sort_order[$i]=$periods[$i]['SORT_ORDER'];
                            $st_time[$i]=strtotime($periods[$i]['START_TIME']);
                            $end_time[$i]=strtotime($periods[$i]['END_TIME']);
                        }
                        
                        if(in_array($columns['TITLE'], $p_title))
                        {
                            $err_msg="Title already exists";
                            break;
                        }
                        else 
                        {
                            if(in_array($columns['SHORT_NAME'], $shortname) && ($columns['SHORT_NAME']!='' || $columns['SHORT_NAME']!=NULL))
                            {
                                $err_msg="Short name already exists";
                                break;
                            }
                            else
                            {
                                if(in_array($columns['SORT_ORDER'], $sort_order))
                                {
                                    $err_msg="Sort order already exists";
                                    break;
                                }
                                else 
                                {

                                            $sql = 'INSERT INTO school_periods ';
                                            $fields = 'SCHOOL_ID,SYEAR,';
                                            $values = '\''.UserSchool().'\',\''.UserSyear().'\',';
                                            $go = 0;
                                            foreach($columns as $column=>$value)
                                            {
                                                if(trim($value))
                                                {
                                                $value=trim(paramlib_validation($column,$value));
                                                $fields .= $column.',';
                                                $values .= '\''.str_replace("'","''",str_replace("\'","'",$value)).'\',';
                                                $go = true;
                                                }
                                            }
                                            $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

                                            if($go)
                                                DBQuery($sql);

                                            # ----------------------------- Length Calculate start --------------------- #

                                            $p_id = DBGet(DBQuery('SELECT max(PERIOD_ID) AS period_id FROM school_periods WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));
                                            $period_id = $p_id[1]['PERIOD_ID'];

                                            $time_chk = DBGet(DBQuery('SELECT START_TIME,END_TIME FROM school_periods WHERE PERIOD_ID=\''.$period_id.'\' AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));
                                            $start_tm_chk = $time_chk[1]['START_TIME'];
                                            $end_tm_chk = $time_chk[1]['END_TIME'];

                                            $start_time = strtotime(date('m/d/Y') .' '.$start_tm_chk);
                                            $end_time = strtotime(date('m/d/Y') .' '.$end_tm_chk);
                                            if($start_time>$end_time)
                                                $end_time = strtotime(date('m/d/Y') .' '.$end_tm_chk)+86400;

                                            $length = ($end_time-$start_time)/60;

                                            $sql_up = 'update school_periods set length = '.$length.' where period_id=\''.$period_id.'\' and syear=\''.UserSyear().'\' and school_id=\''.UserSchool().'\'';
                                            $res_up = DBQuery($sql_up);

                                            # -------------------------------------------------------------------------- #

                                }
                            }
                        }
                    }
            }
            
              
                       
        }
        if($err)
            echo '<font style="color:red"><b>'.$err.'</b></font>';
    }

DrawBC("School Setup > ".ProgramTitle());

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='remove' && AllowEdit())
{
                  $prd_id=paramlib_validation($colmn=PERIOD_ID,$_REQUEST[id]);
	$has_assigned_RET=DBGet(DBQuery('SELECT COUNT(*) AS TOTAL_ASSIGNED FROM course_period_var WHERE PERIOD_ID=\''.$prd_id.'\''));
	$has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
	if($has_assigned>0){
	UnableDeletePrompt('Cannot delete because course periods are created on this period.');
	}else{
	if(DeletePrompt_Period('period'))
	{
		DBQuery('DELETE FROM school_periods WHERE PERIOD_ID=\''.$prd_id.'\'');
		unset($_REQUEST['modfunc']);
	}
	}
}

if($_REQUEST['modfunc']!='remove')
{
	

$sql = 'SELECT PERIOD_ID,TITLE,SHORT_NAME,SORT_ORDER,LENGTH,START_TIME,END_TIME,ATTENDANCE,IGNORE_SCHEDULING FROM school_periods WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER';
	$QI = DBQuery($sql);
	
	

$periods_RET = DBGet($QI,array('TITLE'=>'_makeTextInput','SHORT_NAME'=>'_makeTextInput','SORT_ORDER'=>'_makeTextInputMod','LENGTH'=>'LENGTH','START_TIME'=>'_makeTimeInput','END_TIME'=>'_makeTimeInputEnd','ATTENDANCE'=>'_makeCheckboxInput','IGNORE_SCHEDULING'=>'_makeCheckboxInput'));



$columns = array('TITLE'=>'Title','SHORT_NAME'=>'Short Name','SORT_ORDER'=>'Sort Order','START_TIME'=>'Start Time','END_TIME'=>'End Time','LENGTH'=>'Length <div></div>(minutes)','ATTENDANCE'=>'Used for <div></div>Attendance','IGNORE_SCHEDULING'=>'Ignore for <div></div>Scheduling');
	

$link['add']['html'] = array('TITLE'=>_makeTextInput('','TITLE'),'SHORT_NAME'=>_makeTextInput('','SHORT_NAME'),'SORT_ORDER'=>_makeTextInputMod2('','SORT_ORDER'),'START_TIME'=>_makeTimeInput('','START_TIME'),'END_TIME'=>_makeTimeInputEnd('','END_TIME'),'ATTENDANCE'=>_makeCheckboxInput('','ATTENDANCE'),'IGNORE_SCHEDULING'=>_makeCheckboxInput('','IGNORE_SCHEDULING'));
	
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
	$link['remove']['variables'] = array('id'=>'PERIOD_ID');
	if($err_msg)
        {
            echo "<b style='color:red'>".$err_msg."</b>";
        
            unset($err_msg);
        }
         $LO = DBGet(DBQuery($sql));
$period_id_arr=array();
foreach($LO as $ti => $td)
{
    array_push($period_id_arr,$td[PERIOD_ID]);
}

 $period_id=implode(',',$period_id_arr);
	echo "<FORM name=F1 id=F1 action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=update method=POST>";

            echo '<input type="hidden" name="h1" id="h1" value="'.$period_id.'">';

        echo '<div style="overflow:auto; width:820px;">';
        echo '<div id="students" >';
	ListOutput($periods_RET,$columns,'Period','Periods',$link);
        echo '</div></div>';
       
        $count=count($periods_RET);
        if($count!=0)
        {
            $maxPeriodId=  DBGet(DBQuery("select max(PERIOD_ID) as maxPeriodId from school_periods WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));                 

            $maxPeriodId=$maxPeriodId[1][MAXPERIODID];
            echo "<input type=hidden id=count name=count value=$maxPeriodId />";
        }
        else
            echo "<input type=hidden id=count name=count value=$count />";
	echo '<br><CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_school_setup_periods();"').'</CENTER>';
	echo '</FORM>';
}

function _makeTextInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['PERIOD_ID'])
		$id = $THIS_RET['PERIOD_ID'];
	else
		$id = 'new';
	
	if($name!='TITLE')
		$extra = 'size=5 maxlength=10 class=cell_floating ';
		else 
		$extra = 'class=cell_floating';
	
	return TextInput_mod_a($value,'values['.$id.']['.$name.']','',$extra);
}

function _makeTextInputMod($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['PERIOD_ID'])
		$id = $THIS_RET['PERIOD_ID'];
	else
		$id = 'new';
	
	if($THIS_RET['SORT_ORDER']!='')
                        $extra = 'size=5 maxlength=10 class=cell_floating onkeydown=\"return numberOnly(event);\"';
	else
                         $extra = 'size=5 maxlength=10 class=cell_floating onkeydown="return numberOnly(event);"';
        
	return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}

function _makeTextInputMod2($value,$name)
{ global $THIS_RET;

if($THIS_RET['PERIOD_ID'])
$id = $THIS_RET['PERIOD_ID'];
else
$id = 'new';

if($name!='TITLE')
$extra = 'size=5 maxlength=10 class=cell_floating onkeydown="return numberOnly(event);"';

return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}

function _makeCheckboxInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['PERIOD_ID'])
		$id = $THIS_RET['PERIOD_ID'];
	else
		$id = 'new';
	
	return CheckboxInput($value,'values['.$id.']['.$name.']','','',($id=='new'?true:false),'<IMG SRC=assets/check.gif height=15>','<IMG SRC=assets/x.gif height=15>');
}

function _makeTimeInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['PERIOD_ID'])
		$id = $THIS_RET['PERIOD_ID'];
	else
		$id = 'new';
        if($id!='new')
            $value=date("g:i A", strtotime($value));
	$hour = substr($value,0,strpos($value,':'));
	$m = substr($value,0,strpos($value,''));
	
	for($i=1;$i<=12;$i++)
		$hour_options[$i] = $i;

         for($i=0;$i<=9;$i++)
		$minute_options[$i] = '0'.$i;
	for($i=10;$i<=59;$i++)
		$minute_options[$i] = $i;
	
        if($id!='new')
        {
 $sql_ampm_s = 'SELECT START_TIME FROM school_periods WHERE period_id='.$id;

 $row_ampm_s = DBGet(DBQuery($sql_ampm_s));
 $ampm_s =date("g:i A", strtotime($row_ampm_s[1]['START_TIME']));
 $f_ampm_s = substr($ampm_s, -2);

 $min_s =date("g:i A", strtotime($row_ampm_s[1]['START_TIME']));
 $f_min_s = explode(":", $min_s);
 $fn_min_s =substr($f_min_s[1],0,2);
 if(!is_numeric($fn_min_s))
 	$fn_min_s =substr($f_min_s[1],0,1);
        } 
	
	if($id!='new' && $value)
	{
		
		
		return '<DIV id=time'.$id.'><div onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',SelectInput($hour,'values['.$id.'][START_HOUR]','',$hour_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($fn_min_s,'values['.$id.'][START_MINUTE]','',$minute_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($f_ampm_s,'values['.$id.'][START_M]','',array('AM'=>'AM','PM'=>'PM'),false,'',false)).'</TD></TR></TABLE>","time'.$id.'",true);\'>'.$value.'</div></DIV>';
		
		
	}
	else
		return '<TABLE cellspacing=0 cellpadding=0><TR><TD>'.SelectInput($hour,'values['.$id.'][START_HOUR]','',$hour_options,'N/A','',false).'</TD><TD>'.SelectInput($fn_min_s,'values['.$id.'][START_MINUTE]','',$minute_options,'N/A','',false).'</TD><TD>'.SelectInput($f_ampm_s,'values['.$id.'][START_M]','',array('AM'=>'AM','PM'=>'PM'),'N/A','',false).'</TD></TR></TABLE>';
}



function _makeTimeInputEnd($value,$name)
{	global $THIS_RET;

	if($THIS_RET['PERIOD_ID'])
		$id = $THIS_RET['PERIOD_ID'];
	else
		$id = 'new';
        if($id!='new')
            $value=date("g:i A", strtotime($value));
	$hour = substr($value,0,strpos($value,':'));
	$m = substr($value,0,strpos($value,''));
	
	for($i=1;$i<=12;$i++)
		$hour_options[$i] = $i;
	
       for($i=0;$i<=9;$i++)
		$minute_options[$i] = '0'.$i;
	for($i=10;$i<=59;$i++)
		$minute_options[$i] = $i;
	
        if($id!='new')
        {
 $sql_ampm = 'select end_time from school_periods where period_id='.$id;
 $res_ampm = mysql_query($sql_ampm);
 $row_ampm = mysql_fetch_array($res_ampm);
 $ampm =date("g:i A", strtotime($row_ampm['end_time']));
 $f_ampm = substr($ampm, -2);
 
 $min =date("g:i A", strtotime($row_ampm['end_time']));
 $f_min = explode(":", $min);
 $fn_min =substr($f_min[1],0,2);
 if(!is_numeric($fn_min))
 	$fn_min =substr($f_min[1],0,1);
        } 
	
		
	if($id!='new' && $value)
		return '<DIV id=etime'.$id.'><div onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',SelectInput($hour,'values['.$id.'][END_HOUR]','',$hour_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($fn_min,'values['.$id.'][END_MINUTE]','',$minute_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($f_ampm,'values['.$id.'][END_M]','',array('AM'=>'AM','PM'=>'PM'),false,'',false)).'</TD></TR></TABLE>","etime'.$id.'",true);\'>'.$value.'</div></DIV>';
	else
		return '<TABLE cellspacing=0 cellpadding=0><TR><TD>'.SelectInput($hour,'values['.$id.'][END_HOUR]','',$hour_options,'N/A','',false).'</TD><TD>'.SelectInput($fn_min,'values['.$id.'][END_MINUTE]','',$minute_options,'N/A','',false).'</TD><TD>'.SelectInput($f_ampm,'values['.$id.'][END_M]','',array('AM'=>'AM','PM'=>'PM'),'N/A','',false).'</TD></TR></TABLE>';
}
?>

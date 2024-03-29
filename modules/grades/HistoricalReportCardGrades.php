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
include 'modules/grades/DeletePromptX.fnc.php';
DrawBC("Gradebook > ".ProgramTitle());

Search('student_id');
echo '<style type="text/css">#div_margin { margin-top:-20px; _margin-top:-1px; }</style>';
$_REQUEST['SCHOOL_NAME']=str_replace("'","\'",$_REQUEST['SCHOOL_NAME']);

if(isset($_REQUEST['student_id']) )
{
	$RET = DBGet(DBQuery('SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX,SCHOOL_ID FROM students,student_enrollment WHERE students.STUDENT_ID=\''.$_REQUEST['student_id'].'\' AND student_enrollment.STUDENT_ID = students.STUDENT_ID '));

        $count_student_RET=DBGet(DBQuery('SELECT COUNT(*) AS NUM FROM students'));
        if($count_student_RET[1]['NUM']>1){
	DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Deselect</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname=students/Student.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
	
        }else if($count_student_RET[1]['NUM']==1){
        DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Deselect</font></A>) ');
        }

	
}
####################

if(UserStudentID())
{
    $student_id = UserStudentID();
    $_REQUEST['mp_id'];
    if(isset($_REQUEST['mp_id'] ) )
    $mp_id = $_REQUEST['mp_id'];
    else
    {
        $current_markingperiod=DBGet(DBQuery('SELECT MARKING_PERIOD_ID from student_gpa_calculated where marking_period_id=(SELECT marking_period_id FROM history_school WHERE STUDENT_ID='.$student_id.' ORDER BY ID LIMIT 0,1) AND STUDENT_ID='.$student_id.''));
//    
           $mp_id = $current_markingperiod[1]['MARKING_PERIOD_ID'];
    }      
    $tab_id = ($_REQUEST['tab_id']?$_REQUEST['tab_id']:'grades');
    if ($_REQUEST['modfunc']=='update' && $_REQUEST['removemp'] && $mp_id && DeletePromptX('Marking Period')){
            DBQuery('UPDATE student_gpa_calculated SET  cum_unweighted_factor=NULL WHERE student_id = '.$student_id.' and marking_period_id = '.$mp_id.'');
//        
            unset($mp_id);
    }
    
    if ($_REQUEST['modfunc']=='update' && !$_REQUEST['removemp']){
	
        if ($_REQUEST['new_sms']) {
		
			// ------------------------ Start -------------------------- //
			$res=DBQuery('SELECT * FROM student_gpa_calculated WHERE student_id='.$student_id.' AND marking_period_id='.$_REQUEST['new_sms']);
			$rows = mysql_num_rows($res);
			
			if($rows==0)
			{
				DBQuery('INSERT INTO student_gpa_calculated (student_id, marking_period_id) VALUES ('.$student_id.', '.$_REQUEST['new_sms'].')');
			}
			elseif($rows!=0)
			{
				echo "<b>This Marking Periods has been updated.</b>";
			}
			// ------------------------- End --------------------------- //
            $mp_id = $_REQUEST['new_sms'];
            
        }

        if (($_REQUEST['SMS_GRADE_LEVEL']|| $_REQUEST['SCHOOL_NAME'] )&& $mp_id) {
            $updategl = DBQuery('UPDATE student_gpa_calculated SET grade_level_short = \''.trim($_REQUEST['SMS_GRADE_LEVEL']).'\'
                            WHERE marking_period_id = '.$mp_id.' AND student_id = '.  UserStudentID().'');
             $res      = DBQuery('SELECT * FROM history_school WHERE student_id='.UserStudentID().' AND marking_period_id='.$mp_id.' ');
	     $rows    = mysql_num_rows($res);
		if($rows!=0)
	        {
                     $updatestats = 'UPDATE history_school SET school_name=\''.trim($_REQUEST['SCHOOL_NAME']).'\'
                                     WHERE marking_period_id = '.$mp_id.' AND student_id = '.UserStudentID().'';
                }
                elseif($rows==0)
                {	
                    $updatestats = 'INSERT INTO history_school  (student_id, marking_period_id,school_name) VALUES
                        ('.UserStudentID().','.$mp_id.',\''.trim($_REQUEST['SCHOOL_NAME']).'\')';
                }       
            DBQuery($updatestats);
        } 
        foreach($_REQUEST['values'] as $id=>$columns)
        {
                if($id!='new')
                {
                        $sql = "UPDATE student_report_card_grades SET ";
                        if($columns['GPA_CAL']=='Y')
                            $GPA_CAL_selected=true;
                       if(!isset($GPA_CAL_selected))  
                                        $sql .= "GPA_CAL=NULL,"  ;
                        if($columns['UNWEIGHTED_GP']){
                            $gp=$columns['UNWEIGHTED_GP'];
                        }
                        else {
                            $gp_RET=DBGet(DBQuery('SELECT IF(ISNULL(UNWEIGHTED_GP),  WEIGHTED_GP,UNWEIGHTED_GP ) AS GP FROM student_report_card_grades WHERE id=\''.$id.'\''));
                            $gp=$gp_RET[1];
                            $gp=$gp['GP'];
                        }
                        
                        $go = false;
                        if( $columns['WEIGHTED_GP']=='Y' && $tab_id=='grades'){
                                $sql .= 'WEIGHTED_GP'.'=\''.$gp.'\',UNWEIGHTED_GP=NULL,';
                                $go=true;
                        }
                        elseif($tab_id=='grades'){
                            $sql .= 'UNWEIGHTED_GP'.'=\''.$gp.'\',WEIGHTED_GP=NULL,';
                            $go=true;
                        }
                        foreach($columns as $column=>$value)
                        {
                            if($column=='UNWEIGHTED_GP' || $column=='WEIGHTED_GP')
                                continue;
                            $go=true;
                            $sql .= $column.'=\''.str_replace("\'","''",$value).'\',';
                        }
//                  
                            $sql = substr($sql,0,-1) . " WHERE ID='$id'";
//                      
                            if($go && $_REQUEST['S1']=='Save')
                                DBQuery($sql);
                            unset($GPA_CAL_selected);
                }
            else
            {
                $sql = 'INSERT INTO student_report_card_grades ';
                $fields = 'SCHOOL_ID, SYEAR, STUDENT_ID, MARKING_PERIOD_ID, ';
                $values = UserSchool().', '.UserSyear().', '.$student_id.', '.$mp_id.', ';

                $go = false;
               
                 if( $columns['WEIGHTED_GP']=='Y' && $tab_id=='grades'){
                     $fields .= 'WEIGHTED_GP,';
                     if($columns['UNWEIGHTED_GP']=="")
                     {
                         $values .='NULL'.',';
                     }
                     else
                     {
                        $values .=$columns['UNWEIGHTED_GP'].',';
                     }
                 }
                 elseif($tab_id=='grades'){
                     $fields .= 'UNWEIGHTED_GP,';
                     if($columns['UNWEIGHTED_GP']=="")
                     {
                         $values .='NULL'.',';
                     }
                     else
                     {
                        $values .=$columns['UNWEIGHTED_GP'].',';
                     }
                 }
                 
                foreach($columns as $column=>$value)
                {
                    if($column=='UNWEIGHTED_GP' || $column=='WEIGHTED_GP')
                                continue;
                        
                    if(trim($value))
                    {
                        
                        $fields .= $column.',';
                        $values .= '\''.str_replace("\'","''",$value).'\',';
                        $go = true;
                    }
                }
                $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

                if($go && $mp_id && $student_id)
                    DBQuery($sql);
            }
        }
        unset($_REQUEST['modfunc']); 

        $stu_val['STUDENT_ID']=$student_id;
        $stu_val['MARKING_PERIOD_ID']=$mp_id;
    $res=  DBGet(DBQuery( 'SELECT
    SUM(srcg.weighted_gp/s.reporting_gp_scale) AS sum_weighted_factors,  COUNT(*) AS count_weighted_factors,                        
    SUM(srcg.unweighted_gp/srcg.gp_scale) AS sum_unweighted_factors, 
    COUNT(*) AS count_unweighted_factors,
   IF(ISNULL( sum(srcg.unweighted_gp) ),  (select SUM(sg.weighted_gp*sg.credit_earned) from student_report_card_grades sg where sg.marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND sg.student_id=\''.$stu_val['STUDENT_ID'].'\' AND sg.gpa_cal=\'Y\')/ (select sum(sg.credit_attempted) from student_report_card_grades sg where sg.marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND sg.student_id=\''.$stu_val['STUDENT_ID'].'\' AND sg.gpa_cal=\'Y\'),
                      IF(ISNULL( sum(srcg.weighted_gp) ), (select SUM(sg.unweighted_gp*sg.credit_earned) from student_report_card_grades sg where sg.marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND sg.student_id=\''.$stu_val['STUDENT_ID'].'\' AND sg.gpa_cal=\'Y\')/(select sum(sg.credit_attempted) from student_report_card_grades sg where sg.marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND sg.student_id=\''.$stu_val['STUDENT_ID'].'\' AND sg.gpa_cal=\'Y\'),
                         ( (select SUM(sg.unweighted_gp*sg.credit_attempted) from student_report_card_grades sg where sg.marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND sg.student_id=\''.$stu_val['STUDENT_ID'].'\' AND sg.gpa_cal=\'Y\')+ (select SUM(sg.weighted_gp*sg.credit_earned) from student_report_card_grades sg where sg.marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND sg.student_id=\''.$stu_val['STUDENT_ID'].'\' AND sg.gpa_cal=\'Y\'))/(select sum(sg.credit_attempted) from student_report_card_grades sg where sg.marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND sg.student_id=\''.$stu_val['STUDENT_ID'].'\' AND sg.gpa_cal=\'Y\')
                        )
      ) AS gpa,

   (select SUM(sg.weighted_gp*sg.credit_earned) from student_report_card_grades sg where sg.marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND sg.student_id=\''.$stu_val['STUDENT_ID'].'\' AND sg.gpa_cal=\'Y\')/(select sum(sg.credit_attempted) from student_report_card_grades sg where sg.marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND sg.student_id=\''.$stu_val['STUDENT_ID'].'\' 
                                                   AND sg.gpa_cal=\'Y\' AND sg.weighted_gp  IS NOT NULL  AND sg.unweighted_gp IS NULL GROUP BY sg.student_id, sg.marking_period_id) AS weighted_gpa,
    (select SUM(sg.unweighted_gp*sg.credit_earned) from student_report_card_grades sg where sg.marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND sg.student_id=\''.$stu_val['STUDENT_ID'].'\' AND sg.gpa_cal=\'Y\')/ (select sum(sg.credit_attempted) from student_report_card_grades sg where sg.marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND sg.student_id=\''.$stu_val['STUDENT_ID'].'\'
                                                     AND sg.gpa_cal=\'Y\' AND sg.unweighted_gp  IS NOT NULL  AND sg.weighted_gp IS NULL GROUP BY sg.student_id, sg.marking_period_id) unweighted_gpa,
    eg.short_name AS grade_level_short FROM student_report_card_grades srcg
  INNER JOIN schools s ON s.id=srcg.school_id

  LEFT JOIN enroll_grade eg on eg.student_id=srcg.student_id AND eg.syear=srcg.syear AND eg.school_id=srcg.school_id
  WHERE  srcg.student_id=\''.$stu_val['STUDENT_ID'].'\' AND srcg.gp_scale<>0 AND srcg.gpa_cal=\'Y\' AND srcg.course_period_id IS NULL AND srcg.marking_period_id NOT LIKE \'E%\'
  GROUP BY srcg.marking_period_id,eg.short_name'));

    
    $stu_stat=DBGet(DBQuery('SELECT COUNT(*) AS COUNT FROM student_gpa_calculated WHERE marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND student_id=\''.$stu_val['STUDENT_ID'].'\''));

    if($stu_stat[1]['COUNT']==0)
      DBQuery('INSERT INTO student_gpa_calculated (student_id,marking_period_id)
      VALUES(\''.$stu_val['STUDENT_ID'].'\',\''.$stu_val['MARKING_PERIOD_ID'].'\')');
    

  
  DBQuery('UPDATE student_gpa_calculated g
    INNER JOIN (
	SELECT s.student_id,
		SUM(s.weighted_gp/sc.reporting_gp_scale)/COUNT(*) AS cum_weighted_factor,
		SUM(s.unweighted_gp/s.gp_scale)/COUNT(*) AS cum_unweighted_factor
	FROM student_report_card_grades s
	INNER JOIN schools sc ON sc.id=s.school_id
	
	WHERE s.marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND s.course_period_id IS NULL AND s.gpa_cal=\'Y\' AND 
	s.student_id=\''.$stu_val['STUDENT_ID'].'\') gg ON gg.student_id=g.student_id
    SET g.cum_unweighted_factor=gg.cum_unweighted_factor
    WHERE g.student_id=\''.$stu_val['STUDENT_ID'].'\'');
  
  $stu_gpa_cal=DBGet(DBQuery('SELECT COUNT(*) AS COUNT FROM student_gpa_calculated WHERE marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND student_id=\''.$stu_val['STUDENT_ID'].'\''));
        if($stu_gpa_cal[1]['COUNT']!=0)
    {
            
        DBQuery('UPDATE student_gpa_calculated SET gpa=\''.$res[1]['GPA'].'\', weighted_gpa=\''.$res[1]['WEIGHTED_GPA'].'\',unweighted_gpa=\''.$res[1]['UNWEIGHTED_GPA'].'\' WHERE marking_period_id=\''.$stu_val['MARKING_PERIOD_ID'].'\' AND student_id=\''.$stu_val['STUDENT_ID'].'\'');
    }
    else
      DBQuery('INSERT INTO student_gpa_calculated(student_id,marking_period_id,mp,gpa,weighted_gpa,unweighted_gpa,grade_level_short)
      VALUES(\''.$stu_val['STUDENT_ID'].'\',\''.$stu_val['MARKING_PERIOD_ID'].'\',\''.$stu_val['MARKING_PERIOD_ID'].'\',\''.$res[1]['GPA'].'\',\''.$res[1]['WEIGHTED_GPA'].'\',
        \''.$res[1]['unweighted_gpa'].'\',\''.$res[1]['GRADE_LEVEL_SHORT'].'\')');
    

    unset($stu_val);
    }
    if($_REQUEST['modfunc']=='remove')
    {
        if(DeletePromptX('Student Grade'))
        {
            DBQuery('DELETE FROM student_report_card_grades WHERE ID=\''.$_REQUEST['id'].'\'');
        }
    }    
    if(!$_REQUEST['modfunc']){    
        $stuRET = DBGet(DBQuery('SELECT LAST_NAME, FIRST_NAME, MIDDLE_NAME, NAME_SUFFIX from students where STUDENT_ID = '.$student_id.''));
        $stuRET = $stuRET[1];
        $displayname = $stuRET['LAST_NAME'].(($stuRET['NAME_SUFFIX'])?$stuRET['suffix'].' ':'').', '.$stuRET['FIRST_NAME'].' '.$stuRET['MIDDLE_NAME'];
       

       
       $gquery = 'SELECT mp.syear, mp.marking_period_id as mp_id, mp.title as mp_name, mp.post_end_date as posted, sgc.grade_level_short as GRADE_LEVEL, 
       sgc.weighted_gpa, sgc.unweighted_gpa
       FROM marking_periods mp, student_gpa_calculated sgc, schools s
       WHERE sgc.marking_period_id = mp.marking_period_id and
             s.id = mp.school_id and sgc.student_id = '.$student_id.' AND mp.marking_period_id IN (SELECT marking_period_id FROM  history_marking_periods)
       AND mp.school_id = \''.UserSchool().'\' order by mp.post_end_date';
           
        $GRET = DBGet(DBQuery($gquery));
       
       
        $last_posted = null;
        $gmp = array(); //grade marking_periodso
        $grecs = array();  //grade records
        if($GRET){
            foreach($GRET as $rec){
                if ($mp_id == null || $mp_id == $rec['MP_ID']){
                    $mp_id = $rec['MP_ID'];
                    $gmp[$rec['MP_ID']] = array('schoolyear'=>formatSyear($rec['SYEAR']),
                                                'mp_name'=>$rec['MP_NAME'],
                                                'grade_level'=>$rec['GRADE_LEVEL'],
                                                'weighted_cum'=>$rec['WEIGHTED_CUM'],
                                                'unweighted_cum'=>$rec['UNWEIGHTED_CUM'],
                                                'weighted_gpa'=>$rec['WEIGHTED_GPA'],
                                                'unweighted_gpa'=>$rec['UNWEIGHTED_GPA'],
                                                'gpa'=>$rec['GPA']);
                }
                if ($mp_id != $rec['MP_ID']){
                    $gmp[$rec['MP_ID']] = array('schoolyear'=>formatSyear($rec['SYEAR']),
                                                'mp_name'=>$rec['MP_NAME'],
                                                'grade_level'=>$rec['GRADE_LEVEL'],
                                                'weighted_cum'=>$rec['WEIGHTED_CUM'],
                                                'unweighted_cum'=>$rec['UNWEIGHTED_CUM'],
                                                'weighted_gpa'=>$rec['WEIGHTED_GPA'],
                                                'unweighted_gpa'=>$rec['UNWEIGHTED_GPA'],
                                                'gpa'=>$rec['GPA']);
                }    
            }
        } else {
            

             $mp_id =0;
        }
        
       if($mp_id)
        $historyschool = DBGet(DBQuery('SELECT SCHOOL_NAME  from history_school where STUDENT_ID = '.$student_id.' and marking_period_id='.$mp_id.' '));
        
        $mpselect = "<FORM action=Modules.php?modname=$_REQUEST[modname]&tab_id=".$_REQUEST['tab_id']." method=POST>";
        $mpselect .= "<SELECT name=mp_id onchange='this.form.submit();'>";
        foreach ($gmp as $id=>$mparray){
            $mpselect .= "<OPTION value=".$id.(($id==$mp_id)?' SELECTED':'').">".$mparray['schoolyear'].' '.$mparray['mp_name'].', Grade '.$mparray['grade_level']."</OPTION>";
        }
        $mpselect .= "<OPTION value=0 ".(($mp_id=='0')?' SELECTED':'').">Add another marking period</OPTION>";   
        $mpselect .= '</SELECT>';
        
        echo '</FORM>';

            echo "<FORM action=Modules.php?modname=".strip_tags(trim($_REQUEST[modname]))."&modfunc=update&tab_id=".strip_tags(trim($_REQUEST[tab_id]))."&mp_id=$mp_id method=POST>";
         
              $sms_grade_level = TextInput($gmp[$mp_id]['grade_level'],"SMS_GRADE_LEVEL","",'size=25  class=cell_floating');
           echo '<BR><table  width=90%><tr><td >Student:</td><td>'.$displayname.'</td></tr>
                 <tr><td >Weighted GPA:</td><td>'.sprintf('%0.2f',$gmp[$mp_id]['weighted_gpa']).'</td></tr>
                 <tr> <td >Unweighted GPA:</td><td>'.sprintf('%0.2f',$gmp[$mp_id]['unweighted_gpa']).'</td></tr>
                 ';
                 
            
            
          
            
            if ($mp_id=="0"){
                $syear = UserSyear();
                $sql = 'SELECT MARKING_PERIOD_ID, SYEAR, TITLE, POST_END_DATE FROM marking_periods WHERE SCHOOL_ID = \''.UserSchool().
                        '\' ORDER BY POST_END_DATE';
                $MPRET = DBGet(DBQuery($sql));
                if ($MPRET){
                    $mpoptions = array();
                    foreach ($MPRET as $id=>$mp){
                        $mpoptions[$mp['MARKING_PERIOD_ID']] = formatSyear($mp['SYEAR']).' '.$mp['TITLE'];
                    } 

                    echo '<tr> <td >New Marking Period:</td><td>'.SelectInput(null,'new_sms','',$mpoptions,false,$extra).'</td></tr>';
                    echo '<tr> <td >School Name:</td><td>'.TextInput($historyschool[1]['school_name'],"SCHOOL_NAME","",'size=35  class=cell_floating ').'</td></tr>
                           <tr> <td >Grade Level:</td><td>'.$sms_grade_level.'</td>
                          </tr></table>';
                    
//				
                } 
                
            } 
            
            else {

             
                $selectedmp = $mp_id;
               
                if($historyschool[1]['SCHOOL_NAME'])
                    $school_name= $historyschool[1]['SCHOOL_NAME'];
                else
                {
                    $get_schoolid = DBGet(DBQuery("SELECT school_id FROM  marking_periods  WHERE marking_period_id = $selectedmp"));
                    if($get_schoolid[1]['school_id'])
                    {
                        $get_schoolid = DBGet(DBQuery("SELECT title FROM  schools  WHERE id = $get_schoolid[1][school_id]")); 
                        $school_name=  $get_schoolid[1]['title'];
                    }
                }
                echo '<tr> <td >Grade Level:</td><td>'.$sms_grade_level.'</td>
                      <tr> <td >Select Marking Period:</td><td>'.$mpselect.'</td></tr>
                      <tr> <td >School Name:</td><td>'.TextInput($school_name,"SCHOOL_NAME","",'size=35  class=cell_floating').'</td>
                      </tr></table>'; 
               $sql = 'SELECT ID,COURSE_CODE,COURSE_TITLE,GRADE_PERCENT,GRADE_LETTER,
                    IF(ISNULL(UNWEIGHTED_GP),  WEIGHTED_GP,UNWEIGHTED_GP ) AS GP,WEIGHTED_GP as WEIGHTED_GP,
                    GP_SCALE,GPA_CAL,CREDIT_ATTEMPTED,CREDIT_EARNED,CREDIT_CATEGORY
                       FROM student_report_card_grades WHERE STUDENT_ID = '.$student_id.' AND COURSE_PERIOD_ID IS NULL AND MARKING_PERIOD_ID = '.$mp_id.' ORDER BY ID';
            
                //build forms based on tab selected
              
                   $functions = array( 
                                        'COURSE_CODE'=>'makeTextInput',
                                        'COURSE_TITLE'=>'makeTextInput',
                                        'GRADE_PERCENT'=>'makeTextInput',
                                        'GRADE_LETTER'=>'makeTextInput',
                                        'GP'=>'makeTextInput',                  
                                        'WEIGHTED_GP'=>'makeCheckboxInput',
                                        'GP_SCALE'=>'makeTextInput',
                                        'GPA_CAL'=>'makeCheckboxInput',
                                        'CREDIT_ATTEMPTED'=>'makeTextInput',
                                        'CREDIT_EARNED'=>'makeTextInput'
                                    
                                        );
                    $LO_columns = array(
                                        'COURSE_CODE'=>'Code',
                                        'COURSE_TITLE'=>'Course Name',
                                        'GRADE_PERCENT'=>'Percentage',
                                        'GRADE_LETTER'=>'Letter Grade',
                                        'GP'=>'GP Value',
                                        'GPA_CAL'=>'Calculate GPA',
                                        'WEIGHTED_GP'=>'Weighted GP',
                                        'GP_SCALE'=>'Grade Scale',
                                        'CREDIT_ATTEMPTED'=>'Credit Attempted',
                                        'CREDIT_EARNED'=>'Credit Earned'
                                      
                                        );
                    $link['add']['html'] = array('COURSE_CODE'=>makeTextInput('','COURSE_CODE'),
                                        'COURSE_TITLE'=>makeTextInput('','COURSE_TITLE'),
                                        'GRADE_PERCENT'=>makeTextInput('','GRADE_PERCENT'),
                                        'GRADE_LETTER'=>makeTextInput('','GRADE_LETTER'),
                                        'GP'=>makeTextInput('','GP'),
                                        'WEIGHTED_GP'=>makeCheckboxInput('','WEIGHTED_GP'),
                                        'GP_SCALE'=>makeTextInput('','GP_SCALE'),
                                        'GPA_CAL'=>makeCheckboxInput('','GPA_CAL'),
                                         'CREDIT_ATTEMPTED'=>makeTextInput('','CREDIT_ATTEMPTED'),
                                        'CREDIT_EARNED'=>makeTextInput('','CREDIT_EARNED')
                                      
                                        );
               
                $link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&mp_id=$mp_id";
                $link['remove']['variables'] = array('id'=>'ID');
                $link['add']['html']['remove'] = button('add');
               if($mp_id)
               {
                   $LO_ret = DBGet(DBQuery($sql),$functions);
			
            
                    echo '<div style="width:820px; overflow:auto; overflow-x:scroll; padding-bottom:8px;">';
            	ListOutput($LO_ret,$LO_columns,'','',$link,array(),array('count'=>true,'download'=>true,'search'=>true));
                echo '</div>';
               }
				
            }
            echo '<CENTER>';


            echo SubmitButton('Save','S1','class=btn_medium').'</CENTER>';
            echo '</FORM>';
    }
}
function makeTextInput($value,$name)
{    global $THIS_RET;

    if($THIS_RET['ID'])
        $id = $THIS_RET['ID'];
    else
        $id = 'new';
    if($name=='COURSE_TITLE')
        $extra = 'size=25 maxlength=25 class=cell_floating';
    elseif($name=='COURSE_CODE')
        $extra = 'size=10 maxlength=20 class=cell_floating';
    elseif($name=='GRADE_PERCENT')
        $extra = 'size=6 maxlength=6 class=cell_floating';
    elseif($name=='GRADE_LETTER' )
        $extra = 'size=5 maxlength=5 class=cell_floating';
     elseif( $name=='GP' )
     {  $name='UNWEIGHTED_GP'; 
        $extra = 'size=5 maxlength=5 class=cell_floating';
     }
    else
    $extra = 'size=10 maxlength=10 class=cell_floating';

    return TextInput($value,"values[$id][$name]",'',$extra);
}
function formatSyear($value){
    return substr($value,2).'-'.substr($value+1,2);
}

function makeCheckboxInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
        
        if($THIS_RET['WEIGHTED_GP']!=NULL)
		$yes='Yes';
        elseif($THIS_RET['GPA_CAL']!=NULL)
		$yes='Yes';
        
        else
            $no='No';
	if($name=='GPA_CAL')
        return CheckboxInput($value,'values['.$id.']['.$name.']','','',($id=='new'?true:false),$yes,$no,false);
        else
	return '<input type=hidden name=values['.$id.']['.$name.'] value="'.$value.'" />'.CheckboxInput($value,'values['.$id.']['.$name.']','','',($id=='new'?true:false),$yes,$no,false);
                    
}

?>

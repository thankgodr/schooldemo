function loadajax(frmname)
{
  this.formobj=document.forms[frmname];
	if(!this.formobj)
	{
	  alert("BUG: couldnot get Form object "+frmname);
		return;
	}
	if(this.formobj.onsubmit)
	{
	 this.formobj.old_onsubmit = this.formobj.onsubmit;
	 this.formobj.onsubmit=null;
	}
	else
	{
	 this.formobj.old_onsubmit = null;
	}
	this.formobj.onsubmit=ajax_handler;
	
}

function ajax_handler()
{
	if(ajaxform(this, this.action) =='failed')
	return true;
	
	return false;
}

function formload_ajax(frm){
    
		var frmloadajax  = new loadajax(frm);
}



var hand = function(str){
	window.document.getElementById('response_span').innerHTML=str;
}



function ajax_call (url, callback_function, error_function) {
	
        var xmlHttp = null;
	try {
		// for standard browsers
		xmlHttp = new XMLHttpRequest ();
	} catch (e) {
		// for internet explorer
		try {
			xmlHttp = new ActiveXObject ("Msxml2.XMLHTTP");
	    } catch (e) {
			xmlHttp = new ActiveXObject ("Microsoft.XMLHTTP");
	    }
	}
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState == 4)
			try {
				if (xmlHttp.status == 200) {
					
					callback_function (xmlHttp.responseText);
				}
			} catch (e) {
				
				error_function (e.description);
			}
	 }
	
	 xmlHttp.open ("GET", url);
	 xmlHttp.send (null);
 }
 // --------------------------------------------------- USER ----------------------------------------------------------------------------------- //
 
 function usercheck_init(i) {
	var obj = document.getElementById('ajax_output');
	obj.innerHTML = ''; 
	
	if (i.value.length < 1) return;
	
 	var err = new Array ();
	if (i.value.match (/[^A-Za-z0-9_]/)) err[err.length] = 'Username can only contain letters, numbers and underscores';
 	if (i.value.length < 3) err[err.length] = 'Username too short';
 	if (err != '') {
	 	obj.style.color = '#ff0000';
	 	obj.innerHTML = err.join ('<br />');
	 	return;
 	}
 	
	var pqr = i.value;
	
	
	ajax_call('Validator.php?u='+i.value+'user', usercheck_callback, usercheck_error); 
 }
 
   function usercheck_callback (data) {
 	var response = (data == '1');

 	var obj = document.getElementById('ajax_output');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == '1') ? 'Username OK' : 'Username already taken';
 }

  function usercheck_init_mod(i,opt) {
	var obj = document.getElementById('ajax_output_'+opt);
	obj.innerHTML = ''; 
	
	if (i.value.length < 1) return;
	
 	var err = new Array ();
	if (i.value.match (/[^A-Za-z0-9_]/)) err[err.length] = 'Username can only contain letters, numbers and underscores';
 	if (i.value.length < 3) err[err.length] = 'Username too short';
 	if (err != '') {
	 	obj.style.color = '#ff0000';
	 	obj.innerHTML = err.join ('<br />');
	 	return;
 	}
 	
	var pqr = i.value;
 
	if(opt=='1')
	ajax_call('Validator.php?u='+i.value+'user', usercheck_callback_p, usercheck_error); 
        
        if(opt=='2')
	ajax_call('Validator.php?u='+i.value+'user', usercheck_callback_s, usercheck_error); 
 }

function usercheck_callback_p (data) {
 	var response = (data == 1);

 	var obj = document.getElementById('ajax_output_1');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == 1) ? 'Username OK' : 'Username already taken';
 }
 
function usercheck_callback_s (data) {
 	var response = (data == 1);

 	var obj = document.getElementById('ajax_output_2');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == 1) ? 'Username OK' : 'Username already taken';
 }

 
  function usercheck_error (err) {
 	alert ("Error: " + err);
 }
 function grab_GradeLevel(school_id)
 {
     
//     var xmlhttp;
//if (window.XMLHttpRequest)
//  {// code for IE7+, Firefox, Chrome, Opera, Safari
//  xmlhttp=new XMLHttpRequest();
//  }
//else
//  {// code for IE6, IE5
//  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
//  }
//xmlhttp.onreadystatechange=function()
//  {
//  if (xmlhttp.readyState==4 && xmlhttp.status==200)
//    {
//    document.getElementById("grab_grade").innerHTML=xmlhttp.responseText;
//    }
//  }
//xmlhttp.open("GET","GrabGradeLevel.php?id="+school_id,true);
//xmlhttp.send();
     
     
         ajax_call('GrabGradeLevel.php?id='+school_id, grab_GradeLevel_callback, grab_GradeLevel_error); 
 }
function grab_GradeLevel_callback(data)
{
    
    var obj = document.getElementById('grab_grade');
    obj.innerHTML =data;
}
function grab_GradeLevel_error()
{
    
}
// ------------------------------------------------------ USER ---------------------------------------------------------------------------------- //
function usercheck_init_staff(i) {
	
        var obj = document.getElementById('ajax_output_st');
	obj.innerHTML = ''; 
	document.getElementById('usr_err_check').value='0';
	if (i.value.length < 1) return;
	
 	var err = new Array ();
	if (i.value.match (/[^A-Za-z0-9_]/)) err[err.length] = 'Username can only contain letters, numbers and underscores';
 	if (i.value.length < 3) err[err.length] = 'Username too short';
 	if (err != '') {
	 	obj.style.color = '#ff0000';
	 	obj.innerHTML = err.join ('<br />');
	 	return;
 	}
       
	ajax_call('Validator.php?u='+i.value+'stud', usercheck_callback_staff); 
 }

 function usercheck_callback_staff (data) {
     
 	var response =data;
        if(response==1)
            document.getElementById('usr_err_check').value='1';
 	var obj = document.getElementById('ajax_output_st');
 	obj.style.color = (response==1) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == 1) ? 'Username OK' : 'Username already taken';
        if(response!=1)
            document.getElementById("USERNAME").value='';
 }
// ------------------------------------------------------ Student ------------------------------------------------------------------------------ //

 function usercheck_init_student(i) {
	var obj = document.getElementById('ajax_output_st');
	obj.innerHTML = ''; 
	
	if (i.value.length < 1) return;
	
 	var err = new Array ();
	if (i.value.match (/[^A-Za-z0-9_]/)) err[err.length] = 'Username can only contain letters, numbers and underscores';
 	if (i.value.length < 3) err[err.length] = 'Username too short';
 	if (err != '') {
	 	obj.style.color = '#ff0000';
	 	obj.innerHTML = err.join ('<br />');
	 	return;
 	}
	ajax_call('Validator.php?u='+i.value+'stud', usercheck_callback_student, usercheck_error_student); 
 }

 function usercheck_callback_student (data) {
 	var response =data ;

 	var obj = document.getElementById('ajax_output_st');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == 1) ? 'Username OK' : 'Username already taken';
 }

 function usercheck_error_student (err) {
 	alert ("Error: " + err);
 }

// ------------------------------------------------------ Student ------------------------------------------------------------------------------ //

// ------------------------------------------------------ Student ID------------------------------------------------------------------------------ //

 function usercheck_student_id(i) {
	var obj = document.getElementById('ajax_output_stid');
	obj.innerHTML = ''; 
	
	if (i.value.length < 1) return;
	
 	var err = new Array ();
	if (i.value.match (/[^0-9_]/)) err[err.length] = 'Student ID can only contain numbers';
 	
 	if (err != '') {
	 	obj.style.color = '#ff0000';
	 	obj.innerHTML = err.join ('<br />');
	 	return;
 	}
 	ajax_call ('ValidatorInt.php?u='+i.value+'stid', usercheck_callback_student_id, usercheck_error_student_id); 
 }

 function usercheck_callback_student_id (data) {
 	var response = (data == '1');

 	var obj = document.getElementById('ajax_output_stid');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == '1') ? 'Student ID OK' : 'Student ID already taken';
 }

 function usercheck_error_student_id (err) {
 	alert ("Error: " + err);
 }

// ------------------------------------------------------ Student ID------------------------------------------------------------------------------ //


//-----------------Take attn depends on period------------------------------------------------------
function disable_hidden_field(option,value)
{
    if(option=='')
    var ids='cp';
    else if(option==1)
    var ids='';
    else if(option==2)
    var ids=document.getElementById('fixed_day').value; 

    document.F2.fixed_hidden.disabled=true;

}
function formcheck_periods_attendance_F2(option,attendance,i_value)
           {
    if(option=='')
    var ids='cp';
    else if(option==1)
    var ids='';
    else if(option==2)
    var ids=document.getElementById('fixed_day').value;
    else if(option==3)
    {
    if(i_value=='')
    var ids=document.getElementById('fixed_day3').value;
    else
    var ids=document.getElementById('fixed_day3_'+i_value).value;
           }
    else if(option==4)
    var ids=document.getElementById('fixed_day4').value;
           else
    var ids=option;

           if(document.getElementById(ids+'_period'))
           {
                period_id = document.getElementById(ids+'_period').value;
           }
           else
           {
                if(option==3)
                {
                if(document.getElementById('disabled_option_'+i_value).value)
                period_id =document.getElementById('disabled_option_'+i_value).value;
                else
                period_id =0;
                }
                else
                period_id =0;    
           }
           
    var err = new Array ();
    if(attendance.checked)
        {
           var obj = document.getElementById('ajax_output');
           var period_id;
           var cp_id=document.getElementById(ids+'_id').value;
           obj.innerHTML = '';
           if (attendance.value.length < 1) return;

                if (period_id.length ==0)
                    {
                    err[err.length] = 'Select Period';
                    document.getElementById('get_status').value = 'false';
                    }
                    else
                        err[err.length] ='';
                if (err != '') {
                        obj.style.color = '#ff0000';
                        obj.innerHTML = err.join ('<br />');
                        return;
                }
                var pqr = attendance.value;
                var att_id=attendance.id;
                ajax_call('ValidatorAttendance.php?u='+attendance.value+'&p_id='+period_id+'&cp_id='+cp_id+'&ids='+att_id, attendance_callback, attendance_error);              
            var xmlhttp;
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
            {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
            if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
            var tag_id=xmlhttp.responseText;
                if(tag_id!='0' && tag_id!='1')
                    {
                        document.getElementById(tag_id).checked=false;
                    }
            }
            }
            xmlhttp.open("GET",'ValidatorAttendance.php?u='+attendance.value+'&p_id='+period_id+'&cp_id='+cp_id+'&ids='+att_id,true);
            xmlhttp.send();
    }       
        else
            {
                if (period_id.length ==0)
                    {
                        err[err.length] = 'Select Period';
                        document.getElementById('get_status').value = 'false';
                    }
                    else
                        err[err.length] ='';
                if (err != '') {
                        obj.style.color = '#ff0000';
                        obj.innerHTML = err.join ('<br />');
                        return;
                }
                if(err =='')
                {
           document.getElementById('ajax_output').innerHTML = '';
           document.getElementById('get_status').value ='';
            }
}
}

  function attendance_callback (data)
  {
      
       var response =data.split('/');
 	var obj = document.getElementById('ajax_output');
 	obj.style.color = (response[0]==1) ? '#008800' : '#ff0000';
        obj.innerHTML = (response[0] == 1 ? '' : 'Turn on attendance for the<br>period in School Setup &gt;&gt; Periods');
       if(response[0]==0)
           {
         document.getElementById('get_status').value = response;
         document.getElementById(response[1]).checked=false;
           }
       else
           {
           
        document.getElementById('get_status').value ='';
           }
   }

  function attendance_error (err) {
// 	alert ("Error: " + err);
 }

function formcheck_periods_F2(option)
{
    if(document.getElementById('hidden_period_block') )
    document.getElementById('hidden_period_block').value=document.getElementById('_period').value;

    var ids=option.toString();
    if(!document.getElementById(ids+'_does_attendance') || (!document.getElementById(ids+'_does_attendance').checked))
    {
       var obj = document.getElementById('ajax_output');
       var period_id=document.getElementById(ids+'_period').value;
       var cp_id=document.getElementById(ids+'_id').value;
       var err = new Array ();
       if (period_id.length ==0)
       {
            err[err.length] = 'Select Period';
            document.getElementById('get_status').value = 'false';
       }
       else
           err[err.length]='';
       if(err =='')
           {
           document.getElementById('ajax_output').innerHTML = '';
           document.getElementById('get_status').value ='';
           }
       if (err != '')
       {
            obj.style.color = '#ff0000';
            obj.innerHTML = err.join ('<br />');
            return;
       }
       if(!document.getElementById(ids+'_does_attendance'))
       ajax_call('ValidatorAttendance.php?u=N&p_id='+period_id+'&cp_id='+cp_id, attendance_callback, attendance_error);
    }
    else
    {
      if(document.getElementById(ids+'_does_attendance').checked)
      {
        formcheck_periods_attendance_F2(document.getElementById(ids+'_does_attendance'));
      }
      else
        document.getElementById('get_status').value ='';
}
}

//----------------------------------------------------------------------


function ajax_call_modified(url,callback_function,error_function)
{
    var xmlHttp = null;
    try {
        xmlHttp = new XMLHttpRequest ();
    } catch (e) {
    try {
        xmlHttp = new ActiveXObject ("Msxml2.XMLHTTP");
    } catch (e) {
        xmlHttp = new ActiveXObject ("Microsoft.XMLHTTP");
    }
    }
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 1){
            try {
                
                if(url=='BackupForRollover.php?action=Backup')
                document.getElementById('back_db').style.display="block";
                else
                document.getElementById('calculating').style.display="block";
            } catch (e) {
                error_function (e.description);
            }
        }
        if (xmlHttp.readyState == 4){
            try {
                if (xmlHttp.status == 200) {
                    callback_function(xmlHttp.responseText);
                }
            } catch (e) {
                error_function (e.description);
            }
        }
    }
   
    
    xmlHttp.open ("GET", url);
    xmlHttp.send (null);
}

function ajax_call_modified_forgotpass(url,callback_function,error_function,div_id)
{
    var xmlHttp = null;
    try {
        xmlHttp = new XMLHttpRequest ();
    } catch (e) {
    try {
        xmlHttp = new ActiveXObject ("Msxml2.XMLHTTP");
    } catch (e) {
        xmlHttp = new ActiveXObject ("Microsoft.XMLHTTP");
    }
    }
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 1){
            try {
                document.getElementById(div_id).style.display="block";
            } catch (e) {
                error_function (e.description);
            }
        }
        if (xmlHttp.readyState == 4){
            try {
                if (xmlHttp.status == 200) {
                    callback_function(xmlHttp.responseText);
                }
            } catch (e) {
                error_function (e.description);
            }
        }
    }
    xmlHttp.open ("GET", url);
    xmlHttp.send (null);
}

//=========================================Missing Attendance===========================
function mi_callback(mi_data)
{
                    document.getElementById("resp").innerHTML=mi_data;
                    document.getElementById("calculating").style.display="none";
                    if(mi_data.search('NEW_MI_YES')!=-1)
                    {
                        document.getElementById("attn_alert").style.display="block"
                    }
}
function calculate_missing_atten()
{
     var url = "CalculateMissingAttendance.php";
     ajax_call_modified(url,mi_callback,missing_attn_error);
}

function missing_attn_error(err)
{
    alert ("Error: " + err);
}
//-------------------------------------Missing Attendance end ------------------------------------------------

function recalculate_gpa(stu_all,mp)
{
     var url = 'RecalCulateProcess.php?students='+stu_all+'&mp='+mp;
     ajax_call_modified(url,re_gpa_callback,recal_gpa_error);
}
function re_gpa_callback(re_gpa_data)
{
                    document.getElementById("resp").innerHTML=re_gpa_data;
                    document.getElementById("calculating").style.display="none";
                    
}
function recal_gpa_error(err)
{
    alert ("Error: " + err);
}


function calculate_gpa(mp)
{
    var url = 'CalculateGpaProcess.php?&mp='+mp;
     ajax_call_modified(url,gpa_callback,gpa_error);
}
function gpa_callback(re_gpa_data)
{
    
                    document.getElementById("resp").innerHTML=re_gpa_data;
                    document.getElementById("calculating").style.display="none";
                    
}
function gpa_error(err)
{
    alert ("Error: " + err);
}



function rollover_callback(roll_data)
{
    
    roll_data=roll_data.trim();
    var total_data;
    total_data=roll_data.split('|');
	var value=total_data[2];
	if(value==0)
	{
		var rollover_class='rollover_no';
	}
	else
	{
		var rollover_class='rollover_yes';		
	}
	
	if(total_data[0]=='users'){
            document.getElementById("staff").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("staff").setAttribute("class", rollover_class);
            document.getElementById("staff").setAttribute("className", rollover_class);
            if(document.getElementById("chk_school_periods").value=='Y')
            {
            ajax_rollover('school_periods');
        }
            else
            {
                ajax_rollover('school_years');
            }
        }
        else if(total_data[0]=='School Periods')
        {
            document.getElementById("school_periods").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("school_periods").setAttribute("class", rollover_class);
            document.getElementById("school_periods").setAttribute("className", rollover_class);
            ajax_rollover('school_years');
        }

       else if(total_data[0]=='Marking Periods')
       {
            document.getElementById("school_years").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("school_years").setAttribute("class", rollover_class);
            document.getElementById("school_years").setAttribute("className", rollover_class);

            if(document.getElementById("chk_school_calendars").value=='Y')
            {
            ajax_rollover('school_calendars');
            }
            else if(document.getElementById("chk_report_card_grade_scales").value=='Y')
            {
                ajax_rollover('report_card_grade_scales');
            }
            else if(document.getElementById("chk_course_subjects").value=='Y')
            {
                ajax_rollover('course_subjects');
            }
             else if(document.getElementById("chk_courses").value=='Y')
            {
                ajax_rollover('courses');
            }
            else if(document.getElementById("chk_course_periods").value=='Y')
            {
                ajax_rollover('course_periods');
            }
            else
            {
                ajax_rollover('student_enrollment_codes');
            }
            
       }
       else if(total_data[0]=='Calendars')
       {
            document.getElementById("attendance_calendars").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("attendance_calendars").setAttribute("class", rollover_class);
            document.getElementById("attendance_calendars").setAttribute("className", rollover_class);
            ajax_rollover('report_card_grade_scales');
       }
       else if(total_data[0]=='Report Card Grade Codes')
       {
            document.getElementById("report_card_grade_scales").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("report_card_grade_scales").setAttribute("class", rollover_class);
            document.getElementById("report_card_grade_scales").setAttribute("className", rollover_class);
            if(document.getElementById('chk_course_subjects').value=='Y')
                ajax_rollover('course_subjects');
            else if(document.getElementById('chk_courses').value=='Y')
            ajax_rollover('courses');
            else if(document.getElementById('chk_course_periods').value=='Y')
                ajax_rollover('course_periods');
            else
                ajax_rollover('student_enrollment_codes');
       }
       else if(total_data[0]=='Subjects')
       {
            document.getElementById("course_subjects").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("course_subjects").setAttribute("class", rollover_class);
            document.getElementById("course_subjects").setAttribute("className", rollover_class);
            if(document.getElementById('chk_courses').value=='Y')
                ajax_rollover('courses');
            else if(document.getElementById('chk_course_periods').value=='Y')
                ajax_rollover('course_periods');
            else
                ajax_rollover('student_enrollment_codes');
       }
       else if(total_data[0]=='Courses')
       {
            document.getElementById("courses").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("courses").setAttribute("class", rollover_class);
            document.getElementById("courses").setAttribute("className", rollover_class);
            if(document.getElementById('chk_course_periods').value=='Y')
                ajax_rollover('course_periods');
            else
            ajax_rollover('student_enrollment_codes');
       }
        else if(total_data[0]=='Course Periods')
       {
            document.getElementById("course_periods").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("course_periods").setAttribute("class", rollover_class);
            document.getElementById("course_periods").setAttribute("className", rollover_class);
            ajax_rollover('student_enrollment_codes');
       }
        else if(total_data[0]=='Student Enrollment Codes')
       {
            document.getElementById("student_enrollment_codes").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("student_enrollment_codes").setAttribute("class", rollover_class);
            document.getElementById("student_enrollment_codes").setAttribute("className", rollover_class);
            ajax_rollover('student_enrollment');
       }
       else if(total_data[0]=='Students')
       {
            document.getElementById("student_enrollment").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("student_enrollment").setAttribute("class", rollover_class);
            document.getElementById("student_enrollment").setAttribute("className", rollover_class);
            if(document.getElementById("chk_honor_roll").value=='Y')
            {
            ajax_rollover('honor_roll');
       }
            else if(document.getElementById("chk_attendance_codes").value=='Y')
            {
                ajax_rollover('attendance_codes');
            }
            else if(document.getElementById("chk_report_card_comments").value=='Y')
            {
                ajax_rollover('report_card_comments');
            }
            else
            {
                ajax_rollover('NONE');
            }
       }
       else if(total_data[0]=='Honor Roll Setup')
       {
            document.getElementById("honor_roll").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("honor_roll").setAttribute("class", rollover_class);
            document.getElementById("honor_roll").setAttribute("className", rollover_class);
            if(document.getElementById("chk_attendance_codes").value=='Y')
            {
            ajax_rollover('attendance_codes');
       }
            else if(document.getElementById("chk_report_card_comments").value=='Y')
            {
                ajax_rollover('report_card_comments');
            }
            else
            {
                ajax_rollover('NONE');
            }
            
       }
       else if(total_data[0]=='Attendance Codes')
       {
            document.getElementById("attendance_codes").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("attendance_codes").setAttribute("class", rollover_class);
            document.getElementById("attendance_codes").setAttribute("className", rollover_class);
            
            if(document.getElementById("chk_report_card_comments").value=='Y')
            {
            ajax_rollover('report_card_comments');
       }
            else
            {
                ajax_rollover('NONE');
            }
       }

       else if(total_data[0]=='Report Card Comment Codes')
       {
            document.getElementById("report_card_comments").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("report_card_comments").setAttribute("class", rollover_class);
            document.getElementById("report_card_comments").setAttribute("className", rollover_class);
            ajax_rollover('NONE');
       }
        else 
        {
            document.getElementById("response").innerHTML=roll_data;
            document.getElementById("calculating").style.display="none";
        }
}

function rollover_error(err)
{
    alert ("Error: " + err);
}
function back_before_roll()
{   
     
     var url = 'BackupForRollover.php?action=Backup';
     ajax_call_modified(url,back_before_roll_callback,back_before_roll_error);
     
}
function back_before_roll_callback(data)
{
    if(data.trim()=='File Saved')
    {   
         document.getElementById('back_db').style.display="none";
         ajax_rollover('staff');
    }
    else
    {
        alert('Error: '+ data);
    }
}
function back_before_roll_error(err)
{
    alert ("Error: " + err);
}
function ajax_rollover(roll_table)
{   
     var url = 'RolloverShadow.php?table_name='+roll_table;
     ajax_call_modified(url,rollover_callback,rollover_error);
}
function formcheck_rollover()
{
    var start_month_len=document.getElementById("monthSelect1").value;
    var start_day_len=document.getElementById("daySelect1").value;
    var start_year_len=document.getElementById("yearSelect1").value;
    if(start_month_len=="" || start_day_len=="" || start_year_len=="")
    {     
        document.getElementById("start_date").innerHTML="Please Enter Start Date ";
        return false;
    }

    var custom_dt=document.getElementById("custom_date").value;
    if(custom_dt=="Y")
    {
    var prev_end_date_s=document.getElementById("prev_start_date").value;
    var prev_end_date=Date.parse(prev_end_date_s);
    var s_month_len=document.getElementById("monthSelect2").value;
    var s_day_len=document.getElementById("daySelect2").value;
    var s_year_len=document.getElementById("yearSelect2").value;

    var e_month_len=document.getElementById("monthSelect3").value;
    var e_day_len=document.getElementById("daySelect3").value;
    var e_year_len=document.getElementById("yearSelect3").value;

    if(s_month_len=="" || s_day_len=="" || s_year_len=="")
    {
    document.getElementById("start_date").innerHTML="Please Enter a Valid New Year's Begin Date";
    return false;   
    }
    if(e_month_len=="" || e_day_len=="" || e_year_len=="")
    {
    document.getElementById("start_date").innerHTML="Please Enter Valid New Year's End Date";
    return false;   
    }
    if(s_month_len!="" && s_day_len!="" && s_year_len!="" && e_month_len!="" && e_day_len!="" && e_year_len!="")
    {
        var s_start_s=s_year_len+'-'+s_month_len+'-'+s_day_len;
        var e_start_s=e_year_len+'-'+e_month_len+'-'+e_day_len;
        var s_start_dt=Date.parse(s_start_s);
        var s_end_dt=Date.parse(e_start_s);
        if(s_start_dt<=prev_end_date)
        {
        document.getElementById("start_date").innerHTML="New Year's Begin Date Has To Be After Previous Year's End Date";
        return false;   
        }
        else if(s_start_dt>=s_end_dt)
        {
        document.getElementById("start_date").innerHTML="New Year's End Date Has To Be After New Year's Start Date";
        return false;   
        }
        else
        {
            var tot_round=document.getElementById("tot_round");
            
            if(tot_round!=null)
            {
                tot_round=tot_round.value;
                tot_round=parseInt(tot_round);
                var prev_l_st=0;
                for(var i=1;i<=tot_round;i++)
                {
                    var l_st=document.getElementById("round_"+i).value;
                    l_st=parseInt(l_st);
                    var l_st_m=l_st-1;
                    var l_en=document.getElementById("roll_"+i).value;
                    l_en=parseInt(l_en);
                    ///////Checking semesters////////////////////////
                    for(var j=l_st_m;j<=l_st;j++)
                    {
                    var s_month=document.getElementById("monthSelect"+j).value;
                    var s_day=document.getElementById("daySelect"+j).value;
                    var s_year=document.getElementById("yearSelect"+j).value;
                    var sem_dt=s_year+'-'+s_month+'-'+s_day;
                    var sem_name=document.getElementById("name_"+j).value;
                        if(s_month=="" || s_day=="" || s_year=="")
                        {
                         document.getElementById("start_date").innerHTML="Please Enter Valid "+sem_name;
                         return false;   
                        }
                        else
                        {
                         sem_dt=Date.parse(sem_dt); 
                         if(sem_dt<s_start_dt)
                         {
                          document.getElementById("start_date").innerHTML=sem_name+" Cannot Be Before School's Begin Date";
                          return false;      
                         }
                         if(sem_dt>s_end_dt)
                         {
                          document.getElementById("start_date").innerHTML=sem_name+" Cannot Be Be After School's End Date";
                          return false;   
                         }
                         else
                         {
                            
                            if(j!=l_st_m)
                            {
                            var j_p=j-1;
                            var s_p_month=document.getElementById("monthSelect"+j_p).value;
                            var s_p_day=document.getElementById("daySelect"+j_p).value;
                            var s_p_year=document.getElementById("yearSelect"+j_p).value;
                            var sem_p_dt=s_p_year+'-'+s_p_month+'-'+s_p_day;
                             sem_p_dt=Date.parse(sem_p_dt); 
                            var sem_p_name=document.getElementById("name_"+j_p).value;
                                if(sem_dt<sem_p_dt)
                                {
                                document.getElementById("start_date").innerHTML=sem_name+" Cannot Be Before "+sem_p_name;
                                return false;   
                                }
                            }
                            else
                            {
                            if(prev_l_st!=0)
                            {
                                var p_e_month=document.getElementById("monthSelect"+prev_l_st).value;
                                var p_e_day=document.getElementById("daySelect"+prev_l_st).value;
                                var p_e_year=document.getElementById("yearSelect"+prev_l_st).value;
                                var e_p_dt=p_e_year+'-'+p_e_month+'-'+p_e_day;
                                e_p_dt=Date.parse(e_p_dt);
                                var e_p_name=document.getElementById("name_"+prev_l_st).value;
                                if(sem_dt<e_p_dt)
                                {
                                document.getElementById("start_date").innerHTML=sem_name+" Cannot Be Before "+e_p_name;
                                return false;       
                                }
                            }
                            }
                         }
                        }
                    }
                    
                    var check_q=document.getElementById("quarter_"+i);
                    if(check_q!=null)
                    {
                        check_q=check_q.value;
                        if(check_q!='')
                        {
                        var q_da=check_q.split("-");
                        for(var d_q=0;d_q<q_da.length;d_q++)
                        {
                            var q_t=q_da[d_q];
                            var t_q=q_t.split("`");
                            var qs_month=document.getElementById("monthSelect"+t_q[0]).value;
                            var qs_day=document.getElementById("daySelect"+t_q[0]).value;
                            var qs_year=document.getElementById("yearSelect"+t_q[0]).value;
                            var qs_dt=qs_year+'-'+qs_month+'-'+qs_day;

                            qs_dt=Date.parse(qs_dt);
                            var qs_name=document.getElementById("name_"+t_q[0]).value;

                            var qe_month=document.getElementById("monthSelect"+t_q[1]).value;
                            var qe_day=document.getElementById("daySelect"+t_q[1]).value;
                            var qe_year=document.getElementById("yearSelect"+t_q[1]).value;
                            var qe_dt=qe_year+'-'+qe_month+'-'+qe_day;
                            qe_dt=Date.parse(qe_dt);
                            var qe_name=document.getElementById("name_"+t_q[1]).value;
                            
                            var ss_month=document.getElementById("monthSelect"+l_st_m).value;
                            var ss_day=document.getElementById("daySelect"+l_st_m).value;
                            var ss_year=document.getElementById("yearSelect"+l_st_m).value;
                            var ss_dt=ss_year+'-'+ss_month+'-'+ss_day;
                            ss_dt=Date.parse(ss_dt);
                            var ss_name=document.getElementById("name_"+l_st_m).value;

                            var se_month=document.getElementById("monthSelect"+l_st).value;
                            var se_day=document.getElementById("daySelect"+l_st).value;
                            var se_year=document.getElementById("yearSelect"+l_st).value;
                            var se_dt=se_year+'-'+se_month+'-'+se_day;
                            se_dt=Date.parse(se_dt);
                            var se_name=document.getElementById("name_"+l_st).value;
                            
                            if(qs_month=="" || qs_day=="" || qs_year=="")
                            {
                            document.getElementById("start_date").innerHTML="Please Enter Valid "+qs_name;
                            return false;      
                            }
                            if(qe_month=="" || qe_day=="" || qe_year=="")
                            {
                            document.getElementById("start_date").innerHTML="Please Enter Valid "+qe_name;
                            return false;      
                            }
                           
                            if(qs_month!="" && qs_day!="" && qs_year!="")
                            {
                                if(qs_dt<ss_dt)
                                {
                                document.getElementById("start_date").innerHTML=qs_name+" Cannot Be Before "+ss_name;
                                return false;  
                                }
                                if(qs_dt>se_dt)
                                {
                                document.getElementById("start_date").innerHTML=qs_name+" Cannot Be After "+se_name;
                                return false;  
                                }
                            }
                            if(qe_month!="" && qe_day!="" && qe_year!="")
                            {
                                if(qe_dt<qs_dt)
                                {
                                document.getElementById("start_date").innerHTML=qe_name+" Cannot Be Before "+qs_name;
                                return false;  
                                }
                                if(qe_dt>se_dt)
                                {
                                document.getElementById("start_date").innerHTML=qe_name+" Cannot Be After "+se_name;
                                return false;  
                                }
                            }
                           
                            if(d_q!=0)
                            {
                            var pd_q=d_q-1;
                            var old_elem=q_da[pd_q];
                            var s_old_elem=old_elem.split('`');
                            var qp_month=document.getElementById("monthSelect"+s_old_elem[1]).value;
                            var qp_day=document.getElementById("daySelect"+s_old_elem[1]).value;
                            var qp_year=document.getElementById("yearSelect"+s_old_elem[1]).value;
                            var qp_dt=qp_year+'-'+qp_month+'-'+qp_day;

                            qp_dt=Date.parse(qp_dt);
                            var qp_name=document.getElementById("name_"+s_old_elem[1]).value;
                            if(qp_dt>qs_dt)
                            {
                            document.getElementById("start_date").innerHTML=qs_name+" Cannot Be Before "+qp_name;
                            return false;    
                            }
                            }
                            var check_p=document.getElementById("progress_"+i);
                            if(check_p!=null)
                            {
                                check_p=check_p.value;
                                if(check_p!='')
                                {
                                var p_da=check_p.split("-");
  
                                var check_c_p=p_da[d_q].split('^');
                                for(var ip=0;ip<check_c_p.length;ip++)
                                {
                                    var m_p=check_c_p[ip].split('`');
                                    var ps_month=document.getElementById("monthSelect"+m_p[0]).value;
                                    var ps_day=document.getElementById("daySelect"+m_p[0]).value;
                                    var ps_year=document.getElementById("yearSelect"+m_p[0]).value;
                                    var ps_dt=ps_year+'-'+ps_month+'-'+ps_day;

                                    ps_dt=Date.parse(ps_dt);
                                    var ps_name=document.getElementById("name_"+m_p[0]).value;
                                    
                                    var pe_month=document.getElementById("monthSelect"+m_p[1]).value;
                                    var pe_day=document.getElementById("daySelect"+m_p[1]).value;
                                    var pe_year=document.getElementById("yearSelect"+m_p[1]).value;
                                    var pe_dt=pe_year+'-'+pe_month+'-'+pe_day;
                                    pe_dt=Date.parse(pe_dt);
                                    var pe_name=document.getElementById("name_"+m_p[1]).value;
                                    
                                    if(ps_month=='' || ps_day=='' || ps_year=='')
                                    {
                                    document.getElementById("start_date").innerHTML="Please Enter Valid "+ps_name;
                                    return false;    
                                    }
                                    if(pe_month=='' || pe_day=='' || pe_year=='')
                                    {
                                    document.getElementById("start_date").innerHTML="Please Enter Valid "+pe_name;
                                    return false;    
                                    }
                                    if(ps_month!="" && ps_day!="" && ps_year!="")
                                    {
                                        if(ps_dt<qs_dt)
                                        {
                                        document.getElementById("start_date").innerHTML=ps_name+" Cannot Be Before "+qs_name;
                                        return false;  
                                        }
                                        if(ps_dt>qe_dt)
                                        {
                                        document.getElementById("start_date").innerHTML=ps_name+" Cannot Be After "+qe_name;
                                        return false;  
                                        }
                                    }
                                    if(pe_month!="" && pe_day!="" && pe_year!="")
                                    {
                                        if(pe_dt<ps_dt)
                                        {
                                        document.getElementById("start_date").innerHTML=pe_name+" Cannot Be Before "+ps_name;
                                        return false;  
                                        }
                                        if(pe_dt>qe_dt)
                                        {
                                        document.getElementById("start_date").innerHTML=pe_name+" Cannot Be After "+qe_name;
                                        return false;  
                                        }
                                    }
                                    if(ip!=0)
                                    {
                                    var pd_p=ip-1;
                                    var old_elem_p=check_c_p[pd_p];
                                    var p_old_elem=old_elem_p.split('`');
                                    var pp_month=document.getElementById("monthSelect"+p_old_elem[1]).value;
                                    var pp_day=document.getElementById("daySelect"+p_old_elem[1]).value;
                                    var pp_year=document.getElementById("yearSelect"+p_old_elem[1]).value;
                                    var pp_dt=pp_year+'-'+pp_month+'-'+pp_day;
      
                                    pp_dt=Date.parse(pp_dt);
                                    var pp_name=document.getElementById("name_"+p_old_elem[1]).value;
                                    if(pp_dt>ps_dt)
                                    {
                                    document.getElementById("start_date").innerHTML=ps_name+" Cannot Be Before "+pp_name;
                                    return false;    
                                    }
                                    }
                                    
                                }
                                } 
                            }
                        }
                        }
                    }
                    prev_l_st=l_st;
                }
                
            }
        }

    }
        
      
    }
}

function validate_rollover(thisFrm,thisElement)
{
    if(thisElement.name=='courses')
    {
        if(thisElement.checked==true)
        {
            thisFrm.course_subjects.checked=true;
        }
    }
    
    if(thisElement.name=='course_periods')
    {
        if(thisElement.checked==true)
        {
            thisFrm.school_periods.checked=true;
            thisFrm.attendance_calendars.checked=true;
            thisFrm.course_subjects.checked=true;
            thisFrm.courses.checked=true;
            
        }
        if(thisFrm.report_card_comments.checked==true && thisElement.checked==false)
        {
            thisElement.checked=true;
        }
    }
    if(thisElement.name=='report_card_comments' && thisElement.checked==true)
    {
        thisFrm.school_periods.checked=true;
        thisFrm.attendance_calendars.checked=true;
        thisFrm.course_subjects.checked=true;
        thisFrm.courses.checked=true;
        thisFrm.course_periods.checked=true;
    }
    if(thisFrm.course_periods.checked==true && thisElement.checked==false && (thisElement.name=='school_periods' || thisElement.name=='attendance_calendars' || thisElement.name=='course_subjects'|| thisElement.name=='courses'))
    {
        thisElement.checked=true;
    }
    if(thisFrm.courses.checked==true && thisElement.checked==false && thisElement.name=='course_subjects')
    {
        thisElement.checked=true;
    }
}

function validate_password(password,stid)
{   
 
     var url = "Validator.php?validate=pass&password=" + password +"&stfid="+ stid;
     ajax_call(url,pass_val_callback,pass_val_error);
}
function validate_password_staff(password,stid)
{   
 
     var url = "Validator.php?validate=pass&password=" + password +"&stfid="+ stid;
     ajax_call(url,pass_val_callback_staff,pass_val_error);
}

function pass_val_callback(data)
{
 	var obj = document.getElementById('passwordStrength');
        
            if(data!=1)
            {
 	obj.style.color ='#ff0000';
                  obj.style.backgroundColor =  "#cccccc" ;
 	obj.innerHTML = 'Invalid password';
        
            }
         
}
function pass_val_callback_staff(data)
{
 	var obj = document.getElementById('passwordStrength');
        
            if(data!=1)
            {
 	obj.style.color ='#ff0000';
                  obj.style.backgroundColor =  "#cccccc" ;
 	obj.innerHTML = 'Invalid password';
        document.getElementById('PASSWORD').value='';
            }
         
}
function pass_val_error(err)
{
    alert ("Error: " + err);
}

function validate_password_mod(password,opt)
{   
     document.getElementById('val_pass').value='Y';
     var url = "Validator.php?validate=pass_o&password=" + password+"&opt="+opt;
     ajax_call(url,pass_val_callback_mod,pass_val_error);

}

function pass_val_callback_mod(data)
{
    
        var data_m=data.split("_");
        data=data_m[0];
 	var obj = document.getElementById('passwordStrength'+data_m[1]);
        
            if(data!='1')
            {
 	obj.style.color ='#ff0000';
                  obj.style.backgroundColor =  "#cccccc" ;
 	obj.innerHTML = 'Invalid password';
        document.getElementById('val_pass').value='';
            }
         
}

function pass_val_error(err)
{
    alert ("Error: " + err);
}



//-------------------------------------------------- historical grade school name pickup --------------------------------------//
function pick_schoolname (data) {
 	
        document.getElementById('SCHOOL_NAME').value = data;
 }

// ------------------------------------------------------ Student ------------------------------------------------------------------------------ //

// ------------------------------------------------------ Student ID------------------------------------------------------------------------------ //

 function GetSchool(i) {
	var obj = document.getElementById('SCHOOL_NAME');
	obj.innerHTML = ''; 
	
 	ajax_call ('GetSchool.php?u='+i, pick_schoolname); 
 }
function show_cp_meeting_days(sch_type,cp_id)
{
    var cal_id=document.getElementById('calendar_id').value;
    document.getElementById("save_cp").style.display="block";
    if(cal_id!='' || sch_type=='blocked')
    {
        if(sch_type=='blocked')
            document.getElementById("save_cp").style.display="none";
        ajax_call('modules/schoolsetup/CourseProcess.php?task=md&cal_id='+cal_id+'&cp_id='+cp_id+'&sch_type='+sch_type, meeting_days_callback, meeting_days_error); 
    }
    else
    {
        document.getElementById('meeting_days').innerHTML='<font color=red>Please select calendar</font>';
        document.getElementById('calendar_id').focus();

    }
}
function meeting_days_callback(data)
{
    document.getElementById('meeting_days').innerHTML=data;
    
}
function meeting_days_error(err)
{
    alert('Error '+err)
}

function show_period_time(period_id,day,cp_id,cp_var_id)
{
   document.getElementById(day+'_does_attendance').checked=false;
    if(cp_var_id=='n')
        cp_var_id='';
    else if(cp_var_id=='new')
        cp_var_id='new';
    if(period_id!='')
        ajax_call('modules/schoolsetup/CourseProcess.php?task=per_time&period_id='+period_id+'&day='+day+'&cp_id='+cp_id+'&cp_var_id='+cp_var_id, period_time_callback, period_time_error); 
    else
        document.getElementById(day+'_period_time').innerHTML='';
}

function period_time_callback(data)
{
    var n=data.indexOf("/");
    var id=data.substr(0,n).trim()+'_period_time';
        document.getElementById(id).innerHTML=data.substr(n+1);
        
}
function period_time_error()
{
    
}

function verify_schedule(thisform)
{
    if(thisform.checked==false)
    {
        if(document.getElementById('selected_course_'+thisform.value))
        {
            document.getElementById('selected_course_'+thisform.value).checked=false;
            var row=document.getElementById('selected_course_tr_'+thisform.value);
            row.parentNode.removeChild(row);

        }
    }
    ajax_call_modified('modules/scheduling/ScheduleProcess.php?cp_id='+thisform.value+'&insert='+thisform.checked,verify_schedule_callback, verify_schedule_error);
}

function verify_schedule_callback(data)
{
    data=data.trim();
    var stat=data.substr(0,4);
    data=data.substr(4);
    document.getElementById("calculating").style.display='none';
    if(stat=='resp')
    {
        document.getElementById('conf_div').innerHTML='';
        document.getElementById('resp_table').innerHTML+=data;
    }
    else if(stat=='conf')
    {
        document.getElementById('conf_div').innerHTML=data;
        document.getElementById('conf_div').style.color="red";
        var cp_id=document.getElementById('conflicted_cp').value;
        document.getElementById('course_'+cp_id).checked=false;
    }
    else
    {
        document.getElementById('conf_div').innerHTML=data;
    }
}
function verify_schedule_error()
{
    alert('Error '+err);
}
function fill_hidden_field(id,value)
{
    var final_value=new Array();
    var temp_text;

    for(var i=0;i<value.length;i++)
    {
        temp_text=value.substr(i,1);
        if(temp_text!=' ')
            final_value[i]=temp_text;
            else
            final_value[i]='+';
    }
    document.getElementById(id).value=final_value.join('');

}

 function peoplecheck_email(i,opt,p_id)
 {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
     if(i.value!='' && re.test(i.value))
     {
        if(opt==2 && p_id==0)
        {
            if(document.getElementById('values[people][PRIMARY][EMAIL]'))
                var pri_email=document.getElementById('values[people][PRIMARY][EMAIL]').value;
            else
                pri_email='';
            if(pri_email==i.value)
            peoplecheck_email_callback('0_2');
            else
            ajax_call('EmailCheck.php?email='+i.value+'&p_id='+p_id+'&opt='+opt, peoplecheck_email_callback, peoplecheck_email_error); 
        }
        if(opt==2 && p_id!=0)
        {
            if(document.getElementById('inputvalues[people][PRIMARY][EMAIL]'))
                pri_email=document.getElementById('inputvalues[people][PRIMARY][EMAIL]').value;
            else
                pri_email='';
            if(pri_email==i.value)
            {
                peoplecheck_email_callback('0_2');
            }
            else
            ajax_call('EmailCheck.php?email='+i.value+'&p_id='+p_id+'&opt='+opt, peoplecheck_email_callback, peoplecheck_email_error);     
        }
        if(opt==1 && p_id!=0)
        {
             if(document.getElementById('inputvalues[people][SECONDARY][EMAIL]'))
                var sec_email=document.getElementById('inputvalues[people][SECONDARY][EMAIL]').value;
             else
                 sec_email='';
            if(sec_email==i.value)
            {
     
            peoplecheck_email_callback('0_1');
            }
            else
            ajax_call('EmailCheck.php?email='+i.value+'&p_id='+p_id+'&opt='+opt, peoplecheck_email_callback, peoplecheck_email_error);     
        }
        if(opt==1 && p_id==0)
        {
            if(document.getElementById('values[people][SECONDARY][EMAIL]'))
                sec_email=document.getElementById('values[people][SECONDARY][EMAIL]').value;
            else
                sec_email='';
        if(sec_email==i.value)
        peoplecheck_email_callback('0_1');
        else
        ajax_call('EmailCheck.php?email='+i.value+'&p_id='+p_id+'&opt='+opt, peoplecheck_email_callback, peoplecheck_email_error); 
        }
     }
     else if(i.value!='' && !re.test(i.value))
     {
         document.getElementById('val_email_'+opt).value='';
         document.getElementById('email_'+opt).innerHTML ='';
     }
     else if(i.value=='')
    {
        document.getElementById('val_email_'+opt).value='';
        document.getElementById('email_'+opt).innerHTML ='';
    }
 }
 function peoplecheck_email_callback (data) {
    var response=data.split('_');
    var obj = document.getElementById('email_'+response[1]);
    if(response[0].trim()=='0')
    {
        obj.style.color = '#ff0000';
	obj.innerHTML = 'Email already taken';
        document.getElementById('val_email_'+response[1]).value='';
    }
    else
        {
            obj.style.color = '#008800';
            obj.innerHTML = 'Email available';   
            document.getElementById('val_email_'+response[1]).value='Y';
        }
}

function peoplecheck_email_error(err)
{
    alert ("Error: " + err);
}

 function check_email(i,id,p_id)
 {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
     if(i.value!='' && re.test(i.value))
        ajax_call('EmailCheckOthers.php?email='+i.value+'&id='+id+'&type='+p_id, check_email_callback, check_email_error); 
 }
 function check_email_callback (data) {

    var response=data.split('_');
    if(response[1]==2)
        var email_id = document.getElementsByName("staff[EMAIL]")[0].id;
    else if(response[1]==3)
        email_id = document.getElementsByName("students[EMAIL]")[0].id;
    else
        email_id = document.getElementsByName("people[EMAIL]")[0].id;
    var obj = document.getElementById('email_error');
    if(response[0].trim()==1)
    {
        obj.style.color = '#ff0000';
	obj.innerHTML = 'Email already taken';
        document.getElementById(email_id).value='';
    }
        
    if(response[0].trim()==0)
    {
        obj.style.color = '#008800';
        obj.innerHTML = 'Email available';      
    }
}

function check_email_error(err)
{
    alert ("Error: " + err);
}
function check_username_install(username)
{alert(username);
  if(username!='' || username.toLowerCase()!='os4ed')  
      ajax_call('UsernameCheckOthers.php?email='+username, check_username_install_callback, check_username_install_error);
}
 function check_username_install_callback (data) {
     

    var obj = document.getElementById('ucheck');
    if(data=='1')
    {
        obj.style.color = '#ff0000';
	obj.innerHTML = 'Username already taken';
        document.getElementById(auname).value='';
    }
        
    if(data==0)
    {
        obj.style.color = '#008800';
        obj.innerHTML = 'Username available';      
    }
}
function check_username_install_error(err)
{
    alert ("Error: " + err);
}
function forgotpassemail_init(usr_type)
{
    var i;
    
    if(usr_type=='pass_email')
	 i = document.getElementById('password_stf_email');
    if(usr_type=='uname_email')
         i = document.getElementById('username_stf_email');
	var pqr = i.value.trim();
        if(usr_type=='pass_email' && document.getElementById('uname').value=='' && pqr!='')
            {
                document.getElementById('pass_err_msg_email').style.color = '#ff0000';
                document.getElementById('pass_err_msg_email').innerHTML = 'Please enter username.';
                document.getElementById('pass_email').value='';
                return false;
            }
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	if (i.value.length > 0) 
            {

            if(!re.test(pqr))
            {
                if(usr_type=='pass_email')
                    {
                document.getElementById('pass_err_msg_email').style.color = '#ff0000';
                document.getElementById('pass_err_msg_email').innerHTML = 'Please enter proper email address.';
                document.getElementById('pass_email').value='';
                return false;
                    }
                    else
                        {
                            document.getElementById('uname_err_msg_email').style.color = '#ff0000';
                document.getElementById('uname_err_msg_email').innerHTML = 'Please enter proper email address.';
                document.getElementById('un_email').value='';
                return false;
                        }
            }

        if(usr_type=='pass_email')
            {
            document.getElementById('pass_err_msg_email').innerHTML='';
        var username=document.getElementById('uname').value;
        if(document.getElementById('pass_staff').checked==true)
            ajax_call_modified_forgotpass('ForgotPassUserName.php?username='+username+'&u='+pqr+'&user_type=staff&used_for=email&form='+usr_type, forgotpassemail_callback, forgotpassemail_error,'pass_calculating_email'); 
        else
            ajax_call_modified_forgotpass('ForgotPassUserName.php?username='+username+'&u='+pqr+'&user_type=parent&used_for=email&form='+usr_type, forgotpassemail_callback, forgotpassemail_error,'pass_calculating_email'); 
     return false; 
            }
            if(usr_type=='uname_email')
                {
            document.getElementById('uname_err_msg_email').innerHTML='';
             if(document.getElementById('uname_staff').checked==true)
            ajax_call_modified_forgotpass('ForgotPassUserName.php?username=&u='+pqr+'&user_type=staff&used_for=email&form='+usr_type, forgotpassemail_callback, forgotpassemail_error,'uname_calculating_email'); 
        else
            ajax_call_modified_forgotpass('ForgotPassUserName.php?username=&u='+pqr+'&user_type=parent&used_for=email&form='+usr_type, forgotpassemail_callback, forgotpassemail_error,'uname_calculating_email'); 
     return false; 
                }
   
        
        }  
        else
            {
                document.getElementById('pass_err_msg_email').innerHTML='';
                document.getElementById('uname_err_msg_email').innerHTML='';
                document.getElementById('pass_email').value='';
                document.getElementById('un_email').value='';
                return true;
            }
        
}

  function forgotpassemail_callback (data) {
      
      
 	var response =data.split('~');
        var obj;
        if(response[1]=='pass_email')
            {
            document.getElementById('pass_calculating_email').style.display="none";
            obj= document.getElementById('pass_err_msg_email');
            }
        else
            {
            document.getElementById('uname_calculating_email').style.display="none";
            obj= document.getElementById('uname_err_msg_email');
            }
 	 
        if(response[0]=='1')
        {
 	obj.style.color =  '#008800';
        
 	obj.innerHTML = 'Email found.';
         if(response[1]=='pass_email')
             {
                 document.getElementById('divErr').innerHTML='';
                document.getElementById('pass_email').value=response[0];
                if(document.getElementById("valid_func").value=='Y')
                {
                    document.getElementById("valid_func").value='N';
                    if(document.getElementById("pass_student").checked==true)
    {
        if(document.getElementById("password_stn_id").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter student id.</b></font>';
            document.getElementById("password_stn_id").focus();
            return false;
        }
        else if(document.getElementById("uname").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter your username.</b></font>';
            document.getElementById("uname").focus();
            return false;
        }
        
        else if(document.getElementById("monthSelect1").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter date of birth.</b></font>';
            return false;
        }
        else
            document.getElementById('f1').submit();

    }
    else
    {
        if(document.getElementById("uname").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter your username.</b></font>';
            document.getElementById('pass_err_msg_email').innerHTML='';
            document.getElementById("uname").focus();
            return false;
        }
        
        else if(document.getElementById("pass_email").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter your email.</b></font>';
            document.getElementById('pass_err_msg_email').innerHTML='';
            document.getElementById("pass_stf_email").focus();
            return false;
        }
        else if(document.getElementById("pass_email").value=='0')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Incorrect login credential.</b></font>';
            document.getElementById('pass_err_msg_email').innerHTML='';
            document.getElementById("pass_stf_email").focus();
            return false;
        }     
        else
            document.getElementById('f1').submit();
    }
                    
                }
                
             }
        else
            {
                document.getElementById('divErr').innerHTML='';
                document.getElementById('un_email').value=response[0];
                if(document.getElementById("valid_func").value=='Y')
                {
                    document.getElementById("valid_func").value='N';
                
                if(document.getElementById("uname_student").checked==true)
    {
        if(document.getElementById("username_stn_id").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter student id.</b></font>';
            document.getElementById("username_stn_id").focus();
            return false;
        }
        else if(document.getElementById("pass").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter your password.</b></font>';
            document.getElementById("pass").focus();
            return false;
        }

        else if(document.getElementById("monthSelect2").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter your date of birth.</b></font>';
            return false;
        }
        else
            document.getElementById('f1').submit();

    }
    else
    {
        if(document.getElementById("pass").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter your password.</b></font>';
            document.getElementById('uname_err_msg_email').innerHTML='';
            document.getElementById("pass").focus();
            return false;
        }
        else if(document.getElementById("un_email").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter your email.</b></font>';
            document.getElementById('uname_err_msg_email').innerHTML='';
            document.getElementById("username_stf_email").focus();
            return false;
        }
        else if(document.getElementById("un_email").value=='0')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Incorrect login credential.</b></font>';
            document.getElementById('uname_err_msg_email').innerHTML='';
            document.getElementById("username_stf_email").focus();
            return false;
        }
        else
            document.getElementById('f1').submit();
    }
    }
            }
        
        }
        else
        {
            obj.style.color =   '#ff0000';
        
 	obj.innerHTML = 'Email not found.';
        if(response[1]=='pass_email')
            {
                
             document.getElementById('pass_email').value=response[0];
             document.getElementById('divErr').innerHTML='';
                if(document.getElementById("valid_func").value=='Y')
                {
                    document.getElementById("valid_func").value='N';
                    if(document.getElementById("pass_student").checked==true)
    {
        if(document.getElementById("password_stn_id").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter student id.</b></font>';
            document.getElementById("password_stn_id").focus();
            return false;
        }
        else if(document.getElementById("uname").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter your username.</b></font>';
            document.getElementById("uname").focus();
            return false;
        }
        
        else if(document.getElementById("monthSelect1").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter date of birth.</b></font>';
            return false;
        }
        else
            document.getElementById('f1').submit();

    }
    else
    {
        if(document.getElementById("uname").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter your username.</b></font>';
            document.getElementById('pass_err_msg_email').innerHTML='';
            document.getElementById("uname").focus();
            return false;
        }
        
        else if(document.getElementById("pass_email").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter your email.</b></font>';
            document.getElementById('pass_err_msg_email').innerHTML='';
            document.getElementById("pass_stf_email").focus();
            return false;
        }
        else if(document.getElementById("pass_email").value=='0')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Incorrect login credential.</b></font>';
            document.getElementById('pass_err_msg_email').innerHTML='';
            document.getElementById("pass_stf_email").focus();
            return false;
        }     
        else
            document.getElementById('f1').submit();
    }
                    
                }
            }
         else
             {
             document.getElementById('divErr').innerHTML='';
                document.getElementById('un_email').value=response[0];
                if(document.getElementById("valid_func").value=='Y')
                {
                    document.getElementById("valid_func").value='N';
                
                if(document.getElementById("uname_student").checked==true)
    {
        if(document.getElementById("username_stn_id").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter student id.</b></font>';
            document.getElementById("username_stn_id").focus();
            return false;
        }
        else if(document.getElementById("pass").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter your password.</b></font>';
            document.getElementById("pass").focus();
            return false;
        }

        else if(document.getElementById("monthSelect2").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter your date of birth.</b></font>';
            return false;
        }
        else
            document.getElementById('f1').submit();

    }
    else
    {
        if(document.getElementById("pass").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter your password.</b></font>';
            document.getElementById('uname_err_msg_email').innerHTML='';
            document.getElementById("pass").focus();
            return false;
        }
        else if(document.getElementById("un_email").value=='')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Please enter your email.</b></font>';
            document.getElementById('uname_err_msg_email').innerHTML='';
            document.getElementById("username_stf_email").focus();
            return false;
        }
        else if(document.getElementById("un_email").value=='0')
        {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Incorrect login credential.</b></font>';
            document.getElementById('uname_err_msg_email').innerHTML='';
            document.getElementById("username_stf_email").focus();
            return false;
        }
        else
            document.getElementById('f1').submit();
    }
    }
             }
        }
            
 }
 
  function forgotpassemail_error (err) {
 	alert ("Error: " + err);
 }
 
 function forgotpassvalidate_password(password,usrid,prof_id)
{   
 
     var url = "PasswordCheck.php?password=" + password +"&usrid="+ usrid+"&prof_id="+prof_id;
     ajax_call(url,forgotpassvalidate_callback,forgotpassvalidate_error);
}

function forgotpassvalidate_callback(data)
{
 	var obj = document.getElementById('passwordStrength');
        
            if(data=='1')
            {
 	obj.style.color ='#ff0000';
                  obj.style.backgroundColor =  "#cccccc" ;
 	obj.innerHTML = 'Invalid password';
            }

         
}
function forgotpassvalidate_error(err)
{
    alert ("Error: " + err);
}

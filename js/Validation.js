
		function formcheck_school_setup_school()
{
    var sel = document.getElementsByTagName('input');
    for (var i = 1; i < sel.length; i++)
    {
        var inp_value = sel[i].value;
        var inp_id = sel[i].id;
        if (inp_value == "")
        {
            var inp_name = sel[i].name;
            if (inp_name == 'values[TITLE]')
            {
                document.getElementById('divErr').innerHTML = "<b><font color=red>" + unescape("Please enter school name") + "</font></b>";
                document.getElementById(inp_id).focus();
                return false;
            }
            else if (inp_name == 'values[ADDRESS]')
            {
                document.getElementById('divErr').innerHTML = "<b><font color=red>" + unescape("Please enter address") + "</font></b>";
                document.getElementById(inp_id).focus();
                return false;
            }
            else if (inp_name == 'values[CITY]')
            {
                document.getElementById('divErr').innerHTML = "<b><font color=red>" + unescape("Please enter city") + "</font></b>";
                document.getElementById(inp_id).focus();
                return false;
            }
            else if (inp_name == 'values[STATE]')
            {
                document.getElementById('divErr').innerHTML = "<b><font color=red>" + unescape("Please enter state") + "</font></b>";
                document.getElementById(inp_id).focus();
                return false;
            }
            else if (inp_name == 'values[ZIPCODE]')
            {
                document.getElementById('divErr').innerHTML = "<b><font color=red>" + unescape("Please enter zip/postal code") + "</font></b>";
                document.getElementById(inp_id).focus();
                return false;
            }
            else if (inp_name == 'values[PHONE]')
            {
                document.getElementById('divErr').innerHTML = "<b><font color=red>" + unescape("Please enter phone number") + "</font></b>";
                document.getElementById(inp_id).focus();
                return false;
            }
            else if (inp_name == 'values[PRINCIPAL]')
            {
                document.getElementById('divErr').innerHTML = "<b><font color=red>" + unescape("Please enter principal") + "</font></b>";
                document.getElementById(inp_id).focus();
                return false;
            }
            else if (inp_name == 'values[REPORTING_GP_SCALE]')
            {
                document.getElementById('divErr').innerHTML = "<b><font color=red>" + unescape("Please enter base grading scale") + "</font></b>";
                document.getElementById(inp_id).focus();
                return false;
            }

        }
        else if (inp_value != "")
        {
            var val = inp_value;
            var inp_name1 = sel[i].name;
            if (inp_name1 == 'values[TITLE]')
            {
                if (val.length > 50)
                {
                    document.getElementById('divErr').innerHTML = "<b><font color=red>" + unescape("Max length for school name is 50") + "</font></b>";
                    document.getElementById(inp_id).focus();
                    return false;
                }
            }
            if (inp_name1 == 'values[ZIPCODE]')
            {

                var charpos = val.search("[^a-zA-Z0-9-\(\)\, ]");
                if (charpos >= 0)
                {
                    document.getElementById('divErr').innerHTML = "<b><font color=red>" + unescape("Please enter a valid zip/postal code.") + "</font></b>";
                    document.getElementById(inp_id).focus();
                    return false;
                }
            }
            if (inp_name1 == 'values[PHONE]')
            {

                var charpos = val.search("[^0-9-\(\)\, ]");
                if (charpos >= 0)
                {
                    document.getElementById('divErr').innerHTML = "<b><font color=red>" + unescape("Please enter a valid phone number.") + "</font></b>";
                    document.getElementById(inp_id).focus();
                    return false;
                }
            }
            else if (inp_name1 == 'values[REPORTING_GP_SCALE]')
            {

                var charpos = val.search("[^0-9.]");
                if (charpos >= 0)
                {
                    document.getElementById('divErr').innerHTML = "<b><font color=red>" + unescape("Please enter decimal value only.") + "</font></b>";
                    document.getElementById(inp_id).focus();
                    return false;
                }
            }
            else if (inp_name1 == 'values[E_MAIL]')
            {
                var emailRegxp = /^(.+)@(.+)$/;
                if (emailRegxp.test(val) != true)
                {
                    document.getElementById('divErr').innerHTML = "<b><font color=red>" + unescape("Please enter a valid email address.") + "</font></b>";
                    document.getElementById(inp_id).focus();
                    return false;
                }
            }
           
        }
    }
    return true;

}

	function formcheck_school_setup_portalnotes()
	{
	
           
		var frmvalidator  = new Validator("F2");
	
        

      
        
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50 characters");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort order allows only numeric value");
		frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
		
		frmvalidator.setAddnlValidationFunction("ValidateDate_Portal_Notes");
        
            
   
                
 var portal_id=document.getElementById("h1").value;
if(portal_id!='')
        {
            var id=portal_id;
            var ar=id.split(',');
            
            for(i=0;i<=ar.length-1;i++)
                {
               
            if(document.getElementById("inputvalues["+ar[i]+"][TITLE]"))
            {
                if(document.getElementById("inputvalues["+ar[i]+"][TITLE]").value==''){
               
               frmvalidator.addValidation("values["+ar[i]+"][TITLE]","req", "Title cannot be blank");               

                }
            }  

          

		frmvalidator.addValidation("values["+ar[i]+"][TITLE]","maxlen=50", "Max length for title is 50 characters");
		
		frmvalidator.addValidation("values["+ar[i]+"][SORT_ORDER]","num", "Sort order allows only numeric value");
		frmvalidator.addValidation("values["+ar[i]+"][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
            }
       
   }
           
       

	
       
	}
	
	
	
	
	
	function formcheck_student_advnc_srch()
	{
	
	var day_to=  $('day_to_birthdate');
    var month_to=  $('month_to_birthdate');
	var day_from=  $('day_from_birthdate');
    var month_from=  $('month_from_birthdate');
	if(!day_to.value && !month_to.value && !day_from.value && !month_from.value ){
		return true;
		}
    if(!day_to.value || !month_to.value || !day_from.value || !month_from.value )
		{ 
		strError="Please provide birthday to day, to month, from day, from month.";
	document.getElementById('divErr').innerHTML="<b><font color=red>"+strError+"</font></b>";return false;
		}	
				 				strError="To date must be equal to or greater than from date.";	

								if(month_from.value > month_to.value ){
document.getElementById('divErr').innerHTML="<b><font color=red>"+strError+"</font></b>";                   
                                return false;
    							}else if(month_from.value == month_to.value && day_from.value > day_to.value ){
document.getElementById('divErr').innerHTML="<b><font color=red>"+strError+"</font></b>";
                                return false;
    							}return true;
                                    
	
	}
	
		
	function ValidateDate_Portal_Notes()
	{
		var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F2"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="month_values[new][START_DATE]")
			{
				sm=elem[i];
			}
			
			if(elem[i].name=="day_values[new][START_DATE]")
			{
				sd=elem[i];
			}
			
			if(elem[i].name=="year_values[new][START_DATE]")
			{
				sy=elem[i];
			}
			
			if(elem[i].name=="month_values[new][END_DATE]")
			{
				em=elem[i];
			}
			
			if(elem[i].name=="day_values[new][END_DATE]")
			{
				ed=elem[i];
			}
			
			if(elem[i].name=="year_values[new][END_DATE]")
			{
				ey=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckDate(sm, sd, sy, em, ed, ey))

		   {
			   em.focus();
			   return false;
		   }
		}
		catch(err)
		{
		
		}

		try
		{  
		   if (false==isDate(psm, psd, psy))
		   {
			   alert("Please enter the grade posting start date");
			   psm.focus();
			   return false;
		   }
		}   
		catch(err)
		{
		
		}
		
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		
		}
		   
		   return true;
		
	}



	function formcheck_school_setup_marking(){

  	var frmvalidator  = new Validator("marking_period");
  	frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
  	frmvalidator.addValidation("tables[new][TITLE]","maxlen=50", "Max length for title is 50 characters");
	
	frmvalidator.addValidation("tables[new][SHORT_NAME]","req","Please enter the short name");
  	frmvalidator.addValidation("tables[new][SHORT_NAME]","maxlen=10", "Max length for short name is 10 characters");
	
	frmvalidator.addValidation("tables[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
  	frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "Enter only numeric value");
	
	frmvalidator.setAddnlValidationFunction("ValidateDate_Marking_Periods");
}

function ValidateDate_Marking_Periods()
{
var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey, grd ;
var frm = document.forms["marking_period"];
var elem = frm.elements;
for(var i = 0; i < elem.length; i++)
{

if(elem[i].name=="month_tables[new][START_DATE]")
{
sm=elem[i];
}
if(elem[i].name=="day_tables[new][START_DATE]")
{
sd=elem[i];
}
if(elem[i].name=="year_tables[new][START_DATE]")
{
sy=elem[i];
}


if(elem[i].name=="month_tables[new][END_DATE]")
{
em=elem[i];
}
if(elem[i].name=="day_tables[new][END_DATE]")
{
ed=elem[i];
}
if(elem[i].name=="year_tables[new][END_DATE]")
{
ey=elem[i];
}


if(elem[i].name=="month_tables[new][POST_START_DATE]")
{
psm=elem[i];
}
if(elem[i].name=="day_tables[new][POST_START_DATE]")
{
psd=elem[i];
}
if(elem[i].name=="year_tables[new][POST_START_DATE]")
{
psy=elem[i];
}


if(elem[i].name=="month_tables[new][POST_END_DATE]")
{
pem=elem[i];
}
if(elem[i].name=="day_tables[new][POST_END_DATE]")
{
ped=elem[i];
}
if(elem[i].name=="year_tables[new][POST_END_DATE]")
{
pey=elem[i];
}

if(elem[i].name=="tables[new][DOES_GRADES]")
{
grd=elem[i];
}

}


try
{
if (false==isDate(sm, sd, sy))
   {
   document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter the start date."+"</font></b>";
   sm.focus();
   return false;
   }
}
catch(err)
{

}
try
{  
   if (false==isDate(em, ed, ey))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter the end date."+"</font></b>";
   em.focus();
   return false;
   }
}   
catch(err)
{

}
try
{
   if (false==CheckDate(sm, sd, sy, em, ed, ey))
   {
   em.focus();
   return false;
   }
}
catch(err)
{

}

if (true==validate_chk(grd))
{

try
{  
   if (false==isDate(psm, psd, psy))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter the grade posting start date."+"</font></b>";
   psm.focus();
   return false;
   }
}   
catch(err)
{

}

try
{  
   if (true==isDate(pem, ped, pey))
   {
   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
   {
   pem.focus();
   return false;
   }
   }

}   
catch(err)
{

}






try
{
   if (false==CheckDateMar(sm, sd, sy, psm, psd, psy))
   {
	   psm.focus();
	   return false;
   }
}
catch(err)
{

}



}




   return true;
}



function formcheck_school_setup_copyschool()
{
	var frmvalidator  = new Validator("prompt_form");
	frmvalidator.addValidation("title","req_copy_school","Please enter the new school's title");
	frmvalidator.addValidation("title","maxlen=100", "Max length for title is 100 characters");
}

function formcheck_school_specific_standards()
{
	var frmvalidator  = new Validator("sss");   
        var count=document.getElementById("count_standard").value.trim();       
            for(var i=1;i<=count;i++)
            {
                frmvalidator.addValidation("values["+i+"][STANDARD_REF_NO]","req", "Please enter Ref Number");
                frmvalidator.addValidation("values["+i+"][STANDARD_REF_NO]","maxlen=100", "Max length for Ref Number is 100 characters");
                frmvalidator.addValidation("values["+i+"][DOMAIN]","req", "Please enter domain");
                frmvalidator.addValidation("values["+i+"][GRADE]","req","Please select the grade");  
                frmvalidator.addValidation("values["+i+"][DOMAIN]","maxlen=100", "Max length for Domain is 100 characters");
                frmvalidator.addValidation("values["+i+"][TOPIC]","maxlen=100", "Max length for Topic is 100 characters");      
            }  
            var topic=document.getElementById("values[new][TOPIC]").value.trim();
           
            var details=document.getElementById("values[new][STANDARD_DETAILS]").value.trim();
            if(topic!='' ||  details!='')
            {
                frmvalidator.addValidation("values[new][STANDARD_REF_NO]","req", "Please enter Ref Number");
                frmvalidator.addValidation("values[new][STANDARD_REF_NO]","maxlen=100", "Max length for Ref Number is 100 characters");
                frmvalidator.addValidation("values[new][GRADE]","req","Please select the grade");       
                frmvalidator.addValidation("values[new][DOMAIN]","req", "Please enter domain");
                frmvalidator.addValidation("values[new][DOMAIN]","maxlen=100", "Max length for Domain is 100 characters");
                frmvalidator.addValidation("values[new][TOPIC]","maxlen=100", "Max length for Topic is 100 characters");
            }
}

function formcheck_school_setup_calender()
{
	var frmvalidator  = new Validator("prompt_form");
	frmvalidator.addValidation("title","req","Please enter the title");
	frmvalidator.addValidation("title","maxlen=100", "Max length for title is 100");
        frmvalidator.setAddnlValidationFunction("ValidateDate_SchoolSetup_calender");
}


function ValidateDate_SchoolSetup_calender()
{    
//var sd,sm,sy,ed,em,ey ;
var frm = document.forms["prompt_form"];
var elem = frm.elements;
for(var i = 0; i < elem.length; i++)
{
if(elem[i].name=="month__min")
{
sm=elem[i];
}
if(elem[i].name=="day__min")
{
sd=elem[i];
}
if(elem[i].name=="year__min")
{
sy=elem[i];
}

if(elem[i].name=="month__max")
{
em=elem[i];
}
if(elem[i].name=="day__max")
{
ed=elem[i];
}
if(elem[i].name=="year__max")
{
ey=elem[i];
}
}
if(sm.value)
{
switch (sm.value) {
    case 'JAN':
        s_m = "1";
        break;
    case 'FEB':
        s_m = "2";
        break;
    case 'MAR':
        s_m = "3";
        break;
    case 'APR':
        s_m = "4";
        break;
   case 'MAY':
        s_m = "5";
        break;
    case 'JUN':
        s_m = "6";
        break;
    case 'JUL':
        s_m = "7";
        break;
    case 'AUG':
        s_m = "8";
        break;  
    case 'SEP':
        s_m = "9";
        break;
    case 'OCT':
        s_m = "10";
        break;
    case 'NOV':
        s_m = "11";
        break;
    case 'DEC':
        s_m = "12";
        break;
} 

try
{
    var s=s_m+"/"+sd.value+"/"+sy.value;
    
if (false==validatedate(s))
   {
   document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter correct start date."+"</font></b>";
   sm.focus();
   return false;
   }
}
catch(err)
{

}
}
else
s='no';
if(em.value)
{
switch (em.value) {
    case 'JAN':
        e_m = "1";
        break;
    case 'FEB':
        e_m = "2";
        break;
    case 'MAR':
        e_m = "3";
        break;
    case 'APR':
        e_m = "4";
        break;
   case 'MAY':
        e_m = "5";
        break;
    case 'JUN':
        e_m = "6";
        break;
    case 'JUL':
        e_m = "7";
        break;
    case 'AUG':
        e_m = "8";
        break;  
    case 'SEP':
        e_m = "9";
        break;
    case 'OCT':
        e_m = "10";
        break;
    case 'NOV':
        e_m = "11";
        break;
    case 'DEC':
        e_m = "12";
        break;
} 
try
{  
    var e=e_m+"/"+ed.value+"/"+ey.value;
   if (false==validatedate(e))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter correct end date."+"</font></b>";
   em.focus();
   return false;
   }
}   
catch(err)
{

}
}
else
e='no';

if(s!='no' && e!='no')
{
 var starDate = new Date(s);
 var endDate = new Date(e);
if (starDate > endDate && endDate!='')
{
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Start date cannot be greater than end date."+"</font></b>";
  return false;
}
else
    return true;
}
else
{
    if(s=='no' && e=='no')
    {
    document.getElementById("divErr").innerHTML="<b><font color=red>"+"Start date and end date cannot be blank."+"</font></b>";    
    }
    else
    {
    if(s=='no')
    document.getElementById("divErr").innerHTML="<b><font color=red>"+"Start date cannot be blank."+"</font></b>";
    if(e=='no')
    document.getElementById("divErr").innerHTML="<b><font color=red>"+"End date cannot be blank."+"</font></b>";
    }    
    return false;
        
}


}
function validatedate(inputText)  
  { 
  var dateformat = /^(0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])[\/\-]\d{4}$/;  
  // Match the date format through regular expression  
  if(inputText.match(dateformat))  
  {  
  
  //Test which seperator is used '/' or '-'  
  var opera1 = inputText.split('/');  
  var opera2 = inputText.split('-');  
  lopera1 = opera1.length;  
  lopera2 = opera2.length;  
  // Extract the string into month, date and year  
  if (lopera1>1)  
  {  
  var pdate = inputText.split('/');  
  }  
  else if (lopera2>1)  
  {  
  var pdate = inputText.split('-');  
  }  
  var mm  = parseInt(pdate[0]);  
  var dd = parseInt(pdate[1]);  
  var yy = parseInt(pdate[2]);  
  // Create list of days of a month [assume there is no leap year by default]  
  var ListofDays = [31,28,31,30,31,30,31,31,30,31,30,31];  
  if (mm==1 || mm>2)  
  {  
  if (dd>ListofDays[mm-1])  
  {  
  //alert('Invalid date format!');  
  return false;  
  }  
  }  
  if (mm==2)  
  {  
  var lyear = false;  
  if ( (!(yy % 4) && yy % 100) || !(yy % 400))   
  {  
  lyear = true;  
  }  
  if ((lyear==false) && (dd>=29))  
  {  
  //alert('Invalid date format!');  
  return false;  
  }  
  if ((lyear==true) && (dd>29))  
  {  
  //alert('Invalid date format!');  
  return false;  
  }  
  }  
  }  
  else  
  {  
  //alert("Invalid date format!");  
  //document.form1.text1.focus();  
  return false;  
  }  
  }  


function formcheck_staff_staff(staff_school_chkbox_id)
{
  	var frmvalidator  = new Validator("staff");
        frmvalidator.addValidation("staff[TITLE]","req","Please enter the Salutation");
  	frmvalidator.addValidation("staff[FIRST_NAME]","req","Please enter the First Name");
	frmvalidator.addValidation("staff[LAST_NAME]","req","Please enter the Last Name");
	frmvalidator.addValidation("staff[GENDER]","req","Please select Gender");
        frmvalidator.setAddnlValidationFunction("ValidateDate_Staff");
        frmvalidator.addValidation("staff[ETHNICITY_ID]","req","Please select Ethnicity");
        frmvalidator.addValidation("staff[PRIMARY_LANGUAGE_ID]","req","Please select Primary language");
        frmvalidator.addValidation("staff[SECOND_LANGUAGE_ID]","req","Please select Secondary language");

        frmvalidator.addValidation("values[ADDRESS][STAFF_ADDRESS1_PRIMARY]","req","Please enter Street address 1");
        frmvalidator.addValidation("values[ADDRESS][STAFF_CITY_PRIMARY]","req","Please enter City");
        frmvalidator.addValidation("values[ADDRESS][STAFF_STATE_PRIMARY]","req","Please enter State");
        frmvalidator.addValidation("values[ADDRESS][STAFF_ZIP_PRIMARY]","req","Please enter Street Zip");
		
		frmvalidator.addValidation("values[ADDRESS][STAFF_ZIP_PRIMARY]","numeric", "Zip allows only numeric value");
		
        frmvalidator.addValidation("values[CONTACT][STAFF_HOME_PHONE]","req","Please enter Home Phone");
        frmvalidator.addValidation("values[CONTACT][STAFF_WORK_PHONE]","req","Please enter Office Phone");
        frmvalidator.addValidation("values[CONTACT][STAFF_WORK_EMAIL]","req","Please enter Work email");
        frmvalidator.addValidation("values[CONTACT][STAFF_WORK_EMAIL]","email","Please enter Work email in proper format");
        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_FIRST_NAME]","req","Please enter Emergency First Name");
        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_LAST_NAME]","req","Please enter Emergency Last Name");
        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_RELATIONSHIP]","req","Please select Relationship to Staff");
        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_HOME_PHONE]","req","Please enter Emergency Home Phone");
        frmvalidator.addValidation("values[EMERGENCY_CONTACT][STAFF_EMERGENCY_WORK_PHONE]","req","Please enter Emergency Work Phone");

        frmvalidator.addValidation("month_values[JOINING_DATE]","req","Please select Joining Date");
        frmvalidator.addValidation("day_values[JOINING_DATE]","req","Please select Joining Date");
        frmvalidator.addValidation("year_values[JOINING_DATE]","req","Please select Joining Date");
        

               return school_check(staff_school_chkbox_id);


}

function formcheck_school_setup_periods()
{
    
  	var frmvalidator  = new Validator("F1");
    if( (document.getElementById('values[new][SHORT_NAME]') && document.getElementById('values[new][SHORT_NAME]').value!='') ||
    (document.getElementById('values[new][SORT_ORDER]') && document.getElementById('values[new][SORT_ORDER]').value!='') )
    {
        frmvalidator.addValidation("values[new][TITLE]","req", "Title cannot be blank");
    }
    
    if( (document.getElementsByName('values[new][START_HOUR]') && document.getElementsByName('values[new][START_HOUR]')[0].value!='') ||
      (document.getElementsByName('values[new][START_MINUTE]') && document.getElementsByName('values[new][START_MINUTE]')[0].value!='') ||
  (document.getElementsByName('values[new][START_M]') && document.getElementsByName('values[new][START_M]')[0].value!='') )
    {
        frmvalidator.addValidation("values[new][TITLE]","req", "Title cannot be blank");
    }
    
    if( (document.getElementsByName('values[new][END_HOUR]') && document.getElementsByName('values[new][END_HOUR]')[0].value!='') ||
      (document.getElementsByName('values[new][END_MINUTE]') && document.getElementsByName('values[new][END_MINUTE]')[0].value!='') ||
  (document.getElementsByName('values[new][END_M]') && document.getElementsByName('values[new][END_M]')[0].value!='') )
    {
        frmvalidator.addValidation("values[new][TITLE]","req", "Title cannot be blank");
    }
	
	if(document.getElementById('values[new][TITLE]') && document.getElementById('values[new][TITLE]').value!='')
	{
		
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50 characters");
                frmvalidator.addValidation("values[new][SHORT_NAME]","req", "Short name cannot be blank");
		frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for short name is 50 characters");
		
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort order allows only numeric value");
		frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
		
		frmvalidator.addValidation("values[new][START_HOUR]","req","Please select start time");
		frmvalidator.addValidation("values[new][START_MINUTE]","req","Please select start time");
		frmvalidator.addValidation("values[new][START_M]","req","Please select start time");
		
		frmvalidator.addValidation("values[new][END_HOUR]","req","Please select end time");
		frmvalidator.addValidation("values[new][END_MINUTE]","req","Please select end time");
		frmvalidator.addValidation("values[new][END_M]","req","Please select end time");
	}
        
        var periods_id=document.getElementById("h1").value;
        
	if(periods_id!='')
        {
            var id=periods_id;
            var ar=id.split(',');
            
            for(i=0;i<=ar.length-1;i++)
                {
               
                

           if(document.getElementById('inputvalues['+ar[i]+'][TITLE]'))
           {
            frmvalidator.addValidation("values["+ar[i]+"][TITLE]","req", "Title cannot be blank");    
            frmvalidator.addValidation("values["+ar[i]+"][TITLE]","maxlen=50", "Max length for title is 50 characters");
       }    frmvalidator.addValidation("values["+ar[i]+"][SHORT_NAME]","req", "Short name cannot be blank"); 

   }
           
       
        }
}


function formcheck_school_setup_grade_levels()
{
		var frmvalidator  = new Validator("F1");
                
    if( (document.getElementById('values[new][SHORT_NAME]') && document.getElementById('values[new][SHORT_NAME]').value!='') ||
    (document.getElementById('values[new][SORT_ORDER]') && document.getElementById('values[new][SORT_ORDER]').value!='') )
    {
        frmvalidator.addValidation("values[new][TITLE]","req", "Title cannot be blank");
    }
		
    if(document.getElementById('values[new][TITLE]') && document.getElementById('values[new][TITLE]').value!='')
    {
        frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50 characters");

        frmvalidator.addValidation("values[new][SHORT_NAME]","req", "Short name cannot be blank");
        frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for short name is 50 characters");

        frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort order allows only numeric value");
        frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
    }
                var grade_id=document.getElementById("h1").value;
    if(grade_id!='')
        {
            var id=grade_id;
            var ar=id.split(',');
            
            for(i=0;i<=ar.length-1;i++)
                {
                   

           if(document.getElementById('inputvalues['+ar[i]+'][TITLE]'))
           {
            frmvalidator.addValidation("values["+ar[i]+"][TITLE]","req", "Title cannot be blank");    
          frmvalidator.addValidation("values["+ar[i]+"][TITLE]","maxlen=50", "Max length for title is 50 characters");
      }
        if(document.getElementById('inputvalues['+ar[i]+'][SHORT_NAME]'))
           {
            frmvalidator.addValidation("values["+ar[i]+"][SHORT_NAME]","req", "Short name cannot be blank");
          frmvalidator.addValidation("values["+ar[i]+"][SHORT_NAME]","maxlen=50", "Max length for title is 50 characters");
      }
        if(document.getElementById('inputvalues['+ar[i]+'][SORT_ORDER]'))
           {
            frmvalidator.addValidation("values["+ar[i]+"][SORT_ORDER]","num", "Sort order allows only numeric value");    
          frmvalidator.addValidation("values["+ar[i]+"][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
      }
     
   }
           
       
        }
		
}


function formcheck_student_student()
{
        if(document.getElementById('email_1') && document.getElementById('email_1').innerHTML=='Email already taken')
        {
         document.getElementsByName('values[people][PRIMARY][EMAIL]')[0].value='';
        }
        if(document.getElementById('email_2') && document.getElementById('email_2').innerHTML=='Email already taken')
        {
         if(document.getElementsByName('values[people][SECONDARY][EMAIL]')[0])   
         document.getElementsByName('values[people][SECONDARY][EMAIL]')[0].value='';
         if(document.getElementsByName('values[people][OTHER][EMAIL]')[0])
         document.getElementsByName('values[people][OTHER][EMAIL]')[0].value='';
        }
  	var frmvalidator  = new Validator("student");
        
        
        frmvalidator.addValidation("values[student_address][HOME][STREET_ADDRESS_1]","req","Please enter address");
        frmvalidator.addValidation("values[student_address][HOME][CITY]","req","Please enter city");
         frmvalidator.addValidation("values[student_address][HOME][STATE]","req","Please enter state");
          frmvalidator.addValidation("values[student_address][HOME][ZIPCODE]","req","Please enter zipcode");
         frmvalidator.addValidation("values[people][PRIMARY][RELATIONSHIP]","req","Please select a primary relationship to student ");
         frmvalidator.addValidation("values[people][PRIMARY][FIRST_NAME]","req","Please enter primary emergency contact frist name ");	
        frmvalidator.addValidation("values[people][PRIMARY][LAST_NAME]","req","Please enter primary emergency contact last name");
        frmvalidator.addValidation("values[people][PRIMARY][EMAIL]","req","Please enter a primary emergency email");
        frmvalidator.addValidation("values[people][PRIMARY][EMAIL]","email","Please enter a valid primary emergency email");
       
        frmvalidator.addValidation("values[people][OTHER][RELATIONSHIP]","req","Please select a additional relationship to student ");
        frmvalidator.addValidation("values[people][OTHER][FIRST_NAME]","req","Please select a additional emergency contact to first name ");
       frmvalidator.addValidation("values[people][OTHER][LAST_NAME]","req","Please enter additional emergency contact last name");
        frmvalidator.addValidation("values[people][OTHER][EMAIL]","req","Please enter a additional emergency email");
        frmvalidator.addValidation("values[people][OTHER][EMAIL]","email","Please enter a valid additional emergency email");	
//}
       frmvalidator.addValidation("students[FIRST_NAME]","req","Please enter the first name");
	frmvalidator.addValidation("students[FIRST_NAME]","maxlen=100", "Max length for school name is 100 characters");
//	frmvalidator.addValidation("students[FIRST_NAME]","alpha","First name must be alphabetic");
        frmvalidator.addValidation("students[LAST_NAME]","req","Please enter the last name");
	frmvalidator.addValidation("students[LAST_NAME]","maxlen=100", "Max length for address is 100 characters");
//        frmvalidator.addValidation("students[LAST_NAME]","alpha","Last name must be alphabetic");
        
        frmvalidator.addValidation("month_students[BIRTHDATE]","req", "Please enter a valid birthdate");
        
        if(document.getElementById('current_date') && document.getElementById('date_2') && document.getElementById('date_2').value!='' && document.getElementById('date_2').style.display!='none')
        {
            var inp_date=document.getElementById('date_2').value;
            var curr_date=document.getElementById('current_date').value;
            
            var inp_date=new Date(inp_date);
            var curr_date=new Date(curr_date);
           
            if(curr_date<=inp_date)
            { 
                document.getElementById('monthSelect2').value ='';
                document.getElementById('daySelect2').value = '';
                document.getElementById('yearSelect2').value = '';
            }   

          
        }
	frmvalidator.addValidation("assign_student_id","num", "Student ID allows only numeric value");
  	frmvalidator.addValidation("values[student_enrollment][new][GRADE_ID]","req","Please select a grade");
	frmvalidator.addValidation("students[USERNAME]","maxlen=50", "Max length for Username is 50");
        frmvalidator.addValidation("students[PASSWORD]","password=8", "Password should be minimum 8 characters with atleast one special character and one number");
	frmvalidator.addValidation("students[PASSWORD]","maxlen=20", "Max length for password is 20 characters");
	frmvalidator.addValidation("students[EMAIL]","email","Please enter a valid email");
	frmvalidator.addValidation("students[PHONE]","phone","Invalid phone number");
        if(document.getElementById("values[people][SECONDARY][CUSTODY]"))
        {
            if(document.getElementsByName("values[people][SECONDARY][CUSTODY]")[0].checked==true)
            var custody='y';
            else
            var custody='n';
        }
        else
            var custody='n';
        if(document.getElementsByName("values[people][SECONDARY][RELATIONSHIP]")[0]){
        if(document.getElementsByName("values[people][SECONDARY][RELATIONSHIP]")[0].value!='' || document.getElementsByName("values[people][SECONDARY][FIRST_NAME]")[0].value!='' || document.getElementsByName("values[people][SECONDARY][LAST_NAME]")[0].value!='' || document.getElementsByName("values[people][SECONDARY][HOME_PHONE]")[0].value!='' || document.getElementsByName("values[people][SECONDARY][WORK_PHONE]")[0].value!=''  || document.getElementsByName("values[people][SECONDARY][CELL_PHONE]")[0].value!=''  || document.getElementsByName("values[people][SECONDARY][EMAIL]")[0].value!=''  || custody=='y'  || document.getElementsByName("secondary_portal")[0].checked==true)
        {
        frmvalidator.addValidation("values[people][SECONDARY][RELATIONSHIP]","req","Please select a secondary relationship to student ");
        frmvalidator.addValidation("values[people][SECONDARY][FIRST_NAME]","req","Please enter secondary emergency contact frist name ");	
        frmvalidator.addValidation("values[people][SECONDARY][LAST_NAME]","req","Please enter secondary emergency contact last name");
        frmvalidator.addValidation("values[people][SECONDARY][EMAIL]","req","Please enter a secondary email");
        frmvalidator.addValidation("values[people][SECONDARY][EMAIL]","email","Please enter a valid secondary email");
        }
        }

    if(document.getElementsByName("values[people][PRIMARY][RELATIONSHIP]")[0])
    {
       if(document.getElementsByName("values[people][PRIMARY][RELATIONSHIP]")[0].value!='')
       var c=1;
    }
    else
    {
       var c=1; 
    }
     if(document.getElementsByName("values[student_address][HOME][STREET_ADDRESS_1]")[0])
    {
       if(document.getElementsByName("values[student_address][HOME][STREET_ADDRESS_1]")[0].value!='')
       var v=1;
    }
    else
    {
       var v=1; 
    }
     if(document.getElementsByName("values[student_address][HOME][CITY]")[0])
    {
       if(document.getElementsByName("values[student_address][HOME][CITY]")[0].value!='')
       var v1=1;
    }
    else
    {
       var v1=1; 
    }
     if(document.getElementsByName("values[student_address][HOME][STATE]")[0])
    {
       if(document.getElementsByName("values[student_address][HOME][STATE]")[0].value!='')
       var v2=1;
    }
    else
    {
       var v2=1; 
    }
     if(document.getElementsByName("values[student_address][HOME][ZIPCODE]")[0])
    {
       if(document.getElementsByName("values[student_address][HOME][ZIPCODE]")[0].value!='')
       var v3=1;
    }
    else
    {
       var v3=1; 
    }
    if(document.getElementsByName("values[people][PRIMARY][FIRST_NAME]")[0])
    {
       if(document.getElementsByName("values[people][PRIMARY][FIRST_NAME]")[0].value!='')
       var k=1;
    }
    else
    {
       var k=1; 
    }
    
    if(document.getElementsByName("values[people][PRIMARY][LAST_NAME]")[0])
    {
       if(document.getElementsByName("values[people][PRIMARY][LAST_NAME]")[0].value!='')
       var l=1;
    }
    else
    {
       var l=1; 
    }
    if(document.getElementsByName("values[people][PRIMARY][EMAIL]")[0])
    {
       if(document.getElementsByName("values[people][PRIMARY][EMAIL]")[0].value!='')
       var e=1;
    }
    else
    {
       var e=1; 
    }
  

        if(document.getElementsByName("values[people][SECONDARY][RELATIONSHIP]")[0] && document.getElementById('val_email_1').value=='Y' && document.getElementById('val_email_2').value!='Y'){
        if(document.getElementsByName("values[people][SECONDARY][RELATIONSHIP]")[0].value=='' && document.getElementsByName("values[people][SECONDARY][FIRST_NAME]")[0].value=='' && document.getElementsByName("values[people][SECONDARY][LAST_NAME]")[0].value=='' && document.getElementsByName("values[people][SECONDARY][HOME_PHONE]")[0].value=='' && document.getElementsByName("values[people][SECONDARY][WORK_PHONE]")[0].value==''  && document.getElementsByName("values[people][SECONDARY][CELL_PHONE]")[0].value==''  && document.getElementsByName("values[people][SECONDARY][EMAIL]")[0].value==''  && custody=='n'  && document.getElementsByName("secondary_portal")[0].checked==false && (typeof k === 'object' || k==1) && (typeof l === 'object' || l==1) && (typeof c === 'object' || c==1) && (typeof e === 'object' || e==1)&& (typeof v === 'object' || v==1) && (typeof v1 === 'object' || v1==1) && (typeof v2 === 'object' || v2==1) && (typeof v3 === 'object' || v3==1))
        {
           
           frmvalidator.clearAllValidations();
        }
        }

  	if(document.getElementById('cal_stu_id'))
        {
            var cal_stu_id=document.getElementById('cal_stu_id').value;
            frmvalidator.addValidation("values[student_enrollment]["+cal_stu_id+"][CALENDAR_ID]","req","Please select calendar");
        }
        
        if(document.getElementById("goalId")){
            
            var goalId=document.getElementById("goalId").value;
            frmvalidator.addValidation("tables[student_goal]["+goalId+"][GOAL_TITLE]","req","Please enter goal title");
//            frmvalidator.setAddnlValidationFunction("ValidateDate_Student");
             frmvalidator.addValidation("month_tables["+goalId+"][START_DATE]","req","Please enter begin date");
             frmvalidator.addValidation("month_tables["+goalId+"][END_DATE]","req","Please enter end date");
//            frmvalidator.addValidation("tables[student_goal]["+goalId+"][START_DATE]","req","Please enter begin date");
//            frmvalidator.addValidation("tables[student_goal]["+goalId+"][END_DATE]","req","Please enter end date");
            frmvalidator.addValidation("tables[student_goal]["+goalId+"][GOAL_DESCRIPTION]","req_withspace","Please enter goal description");
        
                
        }
        
        if(document.getElementById("req_progress_id")){
            
        var req_progress_id=document.getElementById("req_progress_id").value;
        frmvalidator.addValidation("tables[student_goal_progress]["+req_progress_id+"][COURSE_PERIOD_ID]","req","Please enter course period");
//        frmvalidator.setAddnlValidationFunction("ValidateDate_Student");
//        frmvalidator.addValidation("tables["+req_progress_id+"][START_DATE]","req","Please enter begin date");
        frmvalidator.addValidation("month_tables["+req_progress_id+"][START_DATE]","req","Please enter begin date");
        frmvalidator.addValidation("tables[student_goal_progress]["+req_progress_id+"][PROGRESS_NAME]","req","Please enter progress period name");
	frmvalidator.addValidation("tables[student_goal_progress]["+req_progress_id+"][PROFICIENCY]","req","Please select proficiency scale");
	frmvalidator.addValidation("tables[student_goal_progress]["+req_progress_id+"][PROGRESS_DESCRIPTION]","req","Please enter progress assessment");
	

        }
        

          frmvalidator.addValidation("values[student_enrollment][new][NEXT_SCHOOL]","req","Please select rolling / retention options");
          frmvalidator.addValidation("students[PHYSICIAN]","req","Please enter the physician name");
          frmvalidator.addValidation("students[PHYSICIAN_PHONE]","ph","Phone number cannot not be alphabetic.");
	
// 	frmvalidator.setAddnlValidationFunction("ValidateDate_Student");
//        frmvalidator.clearAllValidations();
}

function change_pass()
 {	
 	
	var frmvalidator  = new Validator("change_password");
	frmvalidator.addValidation("old","req","Please enter old password");
	frmvalidator.addValidation("new","req","Please enter new password");
	frmvalidator.addValidation("retype","req","Please retype password");
        frmvalidator.addValidation("new","password=8","Password should be minimum 8 characters with atleast one special character and one number");
	
		
 }

function ValidateDate_Student()
{
    

var bm, bd, by ;
var frm = document.forms["student"];
var elem = frm.elements;
for(var i = 0; i < elem.length; i++)
{

if(elem[i].name=="month_students[BIRTHDATE]")
{
bm=elem[i];
}
if(elem[i].name=="day_students[BIRTHDATE]")
{
bd=elem[i];
}
if(elem[i].name=="year_students[BIRTHDATE]")
{
by=elem[i];
}


}

for(var i = 0; i < elem.length; i++)
{

if(elem[i].name=="month_tables[new][START_DATE]")
{
sm=elem[i];
}
if(elem[i].name=="day_tables[new][START_DATE]")
{
sd=elem[i];
}
if(elem[i].name=="year_tables[new][START_DATE]")
{
sy=elem[i];
}


if(elem[i].name=="month_tables[new][END_DATE]")
{
em=elem[i];
}
if(elem[i].name=="day_tables[new][END_DATE]")
{
ed=elem[i];
}
if(elem[i].name=="year_tables[new][END_DATE]")
{
ey=elem[i];
}



}


try
{
if (false==isDate(sm, sd, sy))
   {
   document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter start date."+"</font></b>";
   sm.focus();
   return false;
   }
}
catch(err)
{

}
try
{  
   if (false==isDate(em, ed, ey))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter end date."+"</font></b>";
   em.focus();
   return false;
   }
}   
catch(err)
{

}
try
{
   if (false==CheckDateGoal(sm, sd, sy, em, ed, ey))
   {
   em.focus();
   return false;
   }
}
catch(err)
{

}
//-----
try
{
   if (false==CheckValidDateGoal(sm, sd, sy, em, ed, ey))
   {
   sm.focus();
   return false;
   }
}
catch(err)
{

}


try
{
if (false==CheckBirthDate(bm, bd, by))
   {
   bm.focus();
   return false;
   }
}
catch(err)
{

}

for(var z = 0; z < elem.length; z++)
{
if(elem[z].name=="students[FIRST_NAME]")
{
var firstnameobj = elem[z];
var firstname =elem[z].value;
}
if(elem[z].name=="students[MIDDLE_NAME]")
{
var middlenameobj  = elem[z];   
var middlename =elem[z].value;
}
if(elem[z].name=="students[LAST_NAME]")
{
 var lastnameobj =    elem[z];
var lastname =elem[z].value;
}
if(elem[z].name=="values[student_enrollment][new][GRADE_ID]")
{
  var gradeobj=  elem[z];
var grade =elem[z].value;
}
var studentbirthday_year = by.value;
var studentbirthday_month = bm.value;
var studentbirthday_day = bd.value;
}
if(firstnameobj && middlenameobj && lastnameobj && gradeobj && by && bm && bd)
{    
ajax_call('CheckDuplicateStudent.php?fn='+firstname+'&mn='+middlename+'&ln='+lastname+'&gd='+grade+'&byear='+studentbirthday_year+'&bmonth='+studentbirthday_month+'&bday='+studentbirthday_day, studentcheck_match, studentcheck_unmatch); 
   return false;
}
else
   return true;  
}

function studentcheck_match(data) {
 	var response = data;
if(response!=0)
{    
 var result = confirm("Duplicate student found. There is already a student with the same information. Do you want to proceed?");
if(result==true)
  {
  document.getElementById("student_isertion").submit();
  return true;
  }
else
  {
  return false;
  }
 }
 else
 {    
   document.getElementById("student_isertion").submit();
   return true;
 }
 }
 
 function studentcheck_unmatch (err) {
 	alert ("Error: " + err);
 }
   




	function formcheck_student_studentField_F2()
	{
		var frmvalidator  = new Validator("F2");
                var t_id=document.getElementById('t_id').value;
		frmvalidator.addValidation("tables["+t_id+"][TITLE]","req","Please enter the title");
                frmvalidator.addValidation("tables["+t_id+"][TITLE]","maxlen=50","Max length for title is 50");

	       frmvalidator.addValidation("tables["+t_id+"][SORT_ORDER]","req","Please enter the sort order");
		frmvalidator.addValidation("tables["+t_id+"][SORT_ORDER]","num", "Sort Order allows only numeric value");
                frmvalidator.addValidation("tables["+t_id+"][SORT_ORDER]","maxsort", "Sort Order  must be greater than 6");
	
	}



	function formcheck_student_studentField_F1()
	{
		var frmvalidator  = new Validator("F1");
                 var f_id=document.getElementById('f_id').value;
		frmvalidator.addValidation("tables["+f_id+"][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables["+f_id+"][TYPE]","req","Please select the data type");
		
		frmvalidator.addValidation("tables["+f_id+"][SORT_ORDER]","req","Please enter the sort order");
		frmvalidator.addValidation("tables["+f_id+"][SORT_ORDER]","num", "Sort Order allows only numeric value");
	}
	
	
	
                    function formcheck_student_studentField_F1_defalut()
                    {
                           var type=document.getElementById('type');
                           if(type.value=='textarea')
                               document.getElementById('tables[new][DEFAULT_SELECTION]').disabled=true;
                           else
                               document.getElementById('tables[new][DEFAULT_SELECTION]').disabled=false;
                    }

///////////////////////////////////////// Student Field End ////////////////////////////////////////////////////////////

///////////////////////////////////////// Address Field Start //////////////////////////////////////////////////////////



	function formcheck_student_addressField_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for school name is 100 characters");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order code allows only numeric value");
	}
	
	


	function formcheck_student_addressField_F1()
	{
		var frmvalidator  = new Validator("F1");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables[new][TYPE]","req","Please select the Data type");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}
	
	



///////////////////////////////////////// Address Field End ////////////////////////////////////////////////////////////

///////////////////////////////////////// Contact Field Start //////////////////////////////////////////////////////////


	
	function formcheck_student_contactField_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for school name is 100 characters");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order code allows only numeric value");
	}
	
	


	function formcheck_student_contactField_F1()
	{
		var frmvalidator  = new Validator("F1");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables[new][TYPE]","req","Please select the data type");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}
	
	



	function formcheck_user_user(staff_school_chkbox_id){
            
        
  	var frmvalidator  = new Validator("staff");
        
  	frmvalidator.addValidation("people[FIRST_NAME]","req","Please enter the first name");

  	frmvalidator.addValidation("people[FIRST_NAME]","maxlen=100", "Max length for first name is 100 characters");
	
		
	frmvalidator.addValidation("people[LAST_NAME]","req","Please enter the Last Name");

  	frmvalidator.addValidation("people[LAST_NAME]","maxlen=100", "Max length for Address is 100");
        frmvalidator.addValidation("people[PASSWORD]","password=8", "Password should be minimum 8 characters with one special character and one number");

        frmvalidator.addValidation("people[EMAIL]","email", "Please enter a valid email");
        
}
function school_check(staff_school_chkbox_id)
		{
			var chk='n';
                        var err='T';
			if(staff_school_chkbox_id)
			{
                                    for(i=1;i<=staff_school_chkbox_id;i++)
                                    {
                                        
                                            if(document.getElementById('staff_SCHOOLS'+i).checked==true)
                                            {
                                                    chk='y';
                                                    
                                                   sd=document.getElementById('daySelect1'+i).value;
                                                   sm=document.getElementById('monthSelect1'+i).value;
                                                   sy=document.getElementById('yearSelect1'+i).value;

                                                   ed=document.getElementById('daySelect2'+i).value;
                                                   em=document.getElementById('monthSelect2'+i).value;
                                                   ey=document.getElementById('yearSelect2'+i).value;
                                                    
                                                            var starDate = new Date(sd+"/"+sm+"/"+sy);
                                                            var endDate = new Date(ed+"/"+em+"/"+ey);
                                                             if (starDate > endDate && endDate!='')
                                                            {
                                                                err='S';

                                                            }
//                                                        
                                            } 
                                         
                                    }
                              
                                
                        }
				if(chk!='y')
				{
					var d = $('divErr');
					err = "Please assign at least one school to this staff.";
					d.innerHTML="<b><font color=red>"+err+"</font></b>";
					return false;
				}
                                else if(chk=='y')
                                {

                                    if(err=='S')
                                    {
                                      var d = $('divErr');
                                       var err_stardate = "Start date cannot be greater than end date.";
                                        d.innerHTML="<b><font color=red>"+err_stardate+"</font></b>";
                                        return false;
                                    }
                                    else
                                    {
                                        return true;
                                    }
                                            
                                }
				else
				{
					return true;
				}
			

	    }
/////////////////////////////////////////  Add User End  ////////////////////////////////////////////////////////////

/////////////////////////////////////////  User Fields Start  //////////////////////////////////////////////////////////

	function formcheck_user_userfields_F2()
	{
		var frmvalidator  = new Validator("F2");
                var t_id=document.getElementById('t_id').value;
		frmvalidator.addValidation("tables["+t_id+"][TITLE]","req","Please enter the title");
//		frmvalidator.addValidation("tables["+t_id+"][TITLE]","alphabetic", "Title allows only alphabetic value");
		frmvalidator.addValidation("tables["+t_id+"][TITLE]","maxlen=50", "Max length for title is 100");
                frmvalidator.addValidation("tables["+t_id+"][SORT_ORDER]","req","Please enter the sort order");
		frmvalidator.addValidation("tables["+t_id+"][SORT_ORDER]","num", "Sort Order allows only numeric value");
                frmvalidator.addValidation("tables["+t_id+"][SORT_ORDER]","ma", "Sort Order  must be greater than 2");
	}
	function formcheck_user_stafffields_F2()
	{
		var frmvalidator  = new Validator("F2");
                var t_id=document.getElementById('t_id').value;
		frmvalidator.addValidation("tables["+t_id+"][TITLE]","req","Please enter the title");
//		frmvalidator.addValidation("tables["+t_id+"][TITLE]","alphabetic", "Title allows only alphabetic value");
		frmvalidator.addValidation("tables["+t_id+"][TITLE]","maxlen=50", "Max length for title is 100");
                frmvalidator.addValidation("tables["+t_id+"][SORT_ORDER]","req","Please enter the sort order");
		frmvalidator.addValidation("tables["+t_id+"][SORT_ORDER]","num", "Sort Order allows only numeric value");
                frmvalidator.addValidation("tables["+t_id+"][SORT_ORDER]","ma1", "Sort Order  must be greater than 5");
	}
	function formcheck_user_userfields_F1()
	{
		var frmvalidator1  = new Validator("F1");
                var f_id=document.getElementById('f_id').value;
                
		frmvalidator1.addValidation("tables["+f_id+"][TITLE]","req","Please enter the field Name");
		frmvalidator1.addValidation("tables["+f_id+"][TITLE]","req", "Field name allows only alphanumeric value");
		frmvalidator1.addValidation("tables["+f_id+"][TITLE]","maxlen=50", "Max length for Field Name is 100");
                //frmvalidator1.addValidation("tables[new][SORT_ORDER]","req","Please enter the sort order");
                frmvalidator1.addValidation("tables["+f_id+"][SORT_ORDER]","num", "sort order allows only numeric value");
                
	}
        
        function formcheck_schoolfields()
        {
            var frmvalidator1  = new Validator("SF1");
            

            if(document.getElementById('custom'))
            {
                var custom_id=document.getElementById('custom').value;
                frmvalidator1.addValidation("tables["+custom_id+"][TITLE]","req","Please enter the field name");
		frmvalidator1.addValidation("tables["+custom_id+"][TITLE]","alnum", "Field name allows only alphanumeric value");
		frmvalidator1.addValidation("tables["+custom_id+"][TITLE]","maxlen=50", "Max length for Field Name is 100");
                frmvalidator1.addValidation("tables["+custom_id+"][SORT_ORDER]","num", "sort order allows only numeric value");
		
            }
            else
            {
                frmvalidator1.addValidation("tables[new][TITLE]","req","Please enter the field name");
		frmvalidator1.addValidation("tables[new][TITLE]","alnum", "Field name allows only alphanumeric value");
		frmvalidator1.addValidation("tables[new][TITLE]","maxlen=50", "Max length for Field Name is 100");
                frmvalidator1.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
          
            }
        }

/////////////////////////////////////////  User Fields End  ////////////////////////////////////////////////////////////

/////////////////////////////////////////  User End  ////////////////////////////////////////////////////////////

//////////////////////////////////////// scheduling start ///////////////////////////////////////////////////////

//////////////////////////////////////// Course start ///////////////////////////////////////////////////////

function formcheck_scheduling_course_F4()
{
	var frmvalidator  = new Validator("F4");
  	frmvalidator.addValidation("tables[course_subjects][new][TITLE]","req","Please enter the title");
  	frmvalidator.addValidation("tables[course_subjects][new][TITLE]","maxlen=100", "Max length for title is 100");
}

function formcheck_scheduling_course_F3()
{

var frmvalidator  = new Validator("F3");
      
       var course_id=document.getElementById('course_id_div').value;
       if(course_id=='new')
    {
  	frmvalidator.addValidation("tables[courses][new][TITLE]","req","Please enter the course name ");
  	frmvalidator.addValidation("tables[courses][new][TITLE]","maxlen=100", "Max length for course is 100 characters ");
        frmvalidator.addValidation("tables[courses][new][SHORT_NAME]","maxlen=50", "Max length for course is 50 characters ");
        
    }
    else
    {
        frmvalidator.addValidation("inputtables[courses]["+course_id+"][TITLE]","req","Please enter the course name ");
  	frmvalidator.addValidation("inputtables[courses]["+course_id+"][TITLE]","maxlen=100", "Max length for course is 100 characters ");
        frmvalidator.addValidation("inputtables[courses]["+course_id+"][SHORT_NAME]","maxlen=100", "Max length for course is 100 characters ");
 
    }
}

function formcheck_scheduling_course_F2()
{
    var count;
    var check=0;
    if(document.getElementById("get_status").value=='false' && document.getElementById('cp_id').value!='new' && document.getElementById('cp_period'))
    {
        document.getElementById("divErr").innerHTML='<font color="red"><b>Cannot take attendance in this period</b></font>';

        return false;
    }
    for(count=1;count<=7;count++)
    {
       if(document.getElementById("DAYS"+count).checked==true)
         check++;  
    }
    if(check==0)
    {    
     document.getElementById("display_meeting_days_chk").innerHTML='<font color="red">Please select atleast one day</font>';
     document.getElementById("DAYS1").focus();
     return false;
    }
    else if((document.getElementById("cp_use_standards").checked==true) && (document.getElementById("cp_standard_scale").value==""))
    {
     document.getElementById("display_meeting_days_chk").innerHTML='<font color="red">Please select standard grade scale</font>';
     document.getElementById("cp_standard_scale").focus();
     return false;
    }    
    else
    {    
	var frmvalidator  = new Validator("F2");
  	frmvalidator.addValidation("tables[course_periods][new][SHORT_NAME]","req","Please enter the short name");
  	frmvalidator.addValidation("tables[course_periods][new][SHORT_NAME]","maxlen=20", "Max length for short name is 20");

  	frmvalidator.addValidation("tables[course_periods][new][TEACHER_ID]","req","Please select the teacher");

  	frmvalidator.addValidation("tables[course_periods][new][ROOM]","req","Please enter the Room");
  	frmvalidator.addValidation("tables[course_periods][new][ROOM]","maxlen=10", "Max length for room is 10");
	
  	frmvalidator.addValidation("tables[course_periods][new][PERIOD_ID]","req","Please select the period");
	frmvalidator.addValidation("tables[course_periods][new][MARKING_PERIOD_ID]","req","Please select marking period");
	frmvalidator.addValidation("tables[course_periods][new][TOTAL_SEATS]","req","Please input total seats");
	frmvalidator.addValidation("tables[course_periods][new][TOTAL_SEATS]","maxlen=10","Max length for seats is 10");
       
       
    }  
}

function validate_course_period()
{
        var frmvalidator  = new Validator("F2");     
        var hidden_cp_id=document.getElementById("hidden_cp_id").value;
        if(hidden_cp_id!='new')
        frmvalidator.addValidation("tables[course_periods]["+hidden_cp_id+"][SHORT_NAME]","req","Please enter the short name");
        else
        frmvalidator.addValidation("tables[course_periods][new][SHORT_NAME]","req","Please enter the short name");
  	if(hidden_cp_id!='new')
        {    
        frmvalidator.addValidation("tables[course_periods]["+hidden_cp_id+"][TOTAL_SEATS]","num","Total Seats allows only numeric value");
        if(document.getElementById("inputtables[course_periods]["+hidden_cp_id+"][TOTAL_SEATS]").value==0)
       {
          document.getElementById("divErr").innerHTML="<b><font color=red>"+"Total Seat cannot be blank."+"</font></b>";
          return false;
       }
       if(document.getElementById("inputtables[course_periods]["+hidden_cp_id+"][TOTAL_SEATS]").value=='')
           frmvalidator.addValidation("tables[course_periods]["+hidden_cp_id+"][TOTAL_SEATS]","req","Total Seat cannot be blank.");
    }
        else
        frmvalidator.addValidation("tables[course_periods][new][TOTAL_SEATS]","num","Total Seats allows only numeric value");
  	
    
        frmvalidator.addValidation("tables[course_periods][new][SHORT_NAME]","maxlen=20", "Max length for short name is 20");

  	frmvalidator.addValidation("tables[course_periods][new][TEACHER_ID]","req","Please select the teacher");
                  frmvalidator.setAddnlValidationFunction("validate_cp_other_fields");
  	frmvalidator.addValidation("tables[course_periods][new][ROOM_ID]","req","Please enter the Room");
  	frmvalidator.addValidation("tables[course_periods][new][ROOM_ID]","maxlen=10", "Max length for room is 10");
        if(hidden_cp_id!='new')
        frmvalidator.addValidation("tables[course_period_var]["+hidden_cp_id+"][ROOM_ID]","req","Please enter the Room");
        else
        frmvalidator.addValidation("tables[course_period_var][new][ROOM_ID]","req","Please enter the Room");       
	frmvalidator.addValidation("tables[course_periods][new][CALENDAR_ID]","req","Please select the calendar");       
        if(hidden_cp_id!='new')
        {
            frmvalidator.addValidation("tables[course_period_var]["+hidden_cp_id+"][PERIOD_ID]","req","Please enter the Period");

        }
        else
        {
            frmvalidator.addValidation("tables[course_period_var][new][PERIOD_ID]","req","Please enter the Period");   

        }       	        
   

	frmvalidator.addValidation("tables[course_periods][new][TOTAL_SEATS]","req","Please input total seats");
	frmvalidator.addValidation("tables[course_periods][new][TOTAL_SEATS]","maxlen=10","Max length for seats is 10");
       if(document.getElementById("variable").value=='VARIABLE')
           {
               frmvalidator.addValidation("course_period_variable[new][DAYS]","req","Please select a day");
               frmvalidator.addValidation("course_period_variable[new][PERIOD_ID]","req","Please select a period");
               if(hidden_cp_id!='new')
               {
               var id_for_room=document.getElementById('for_editing_room').value;
               frmvalidator.addValidation("course_period_variable["+hidden_cp_id+"]["+id_for_room+"][ROOM_ID]","req","Please select a room");
               }
               else
               frmvalidator.addValidation("course_period_variable[new][ROOM_ID]","req","Please select a room");       
           }
          
       
             return true;   

    }

function validate_block_schedule(option)
{

    if(document.getElementById('hidden_period_block').value=='')
    {
        document.getElementById("block_error").innerHTML="<b><font color=red>"+"Please select a period"+"</font></b>";
        document.getElementById("_period").focus();
        return false;
    }
    if(document.getElementById('_room').value=='')
    {
        document.getElementById("block_error").innerHTML="<b><font color=red>"+"Please select a room"+"</font></b>";
        document.getElementById("_room").focus();
        return false;
    }
}
function validate_cp_other_fields()
{
    if(document.getElementById("fixed_schedule").checked==false && document.getElementById("variable_schedule").checked==false && document.getElementById("blocked_schedule").checked==false)
    {
        document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please select schedule type"+"</font></b>";
        document.getElementById("fixed_schedule").focus();
        return false;
    }
     if(document.getElementById("preset").checked==false && document.getElementById("custom").checked==false)
    {
        document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please select marking period or custom date range"+"</font></b>";
        document.getElementById("preset").focus();
        return false;
    }
    if(document.getElementById("custom").checked==true)
    {
        if(document.getElementById("monthSelect1").value=='' || document.getElementById("daySelect1").value=='' || document.getElementById("yearSelect1").value=='')
        {
           document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please input a valid starting date"+"</font></b>";
           document.getElementById("custom").focus();
           return false;
        }
        if(document.getElementById("monthSelect2").value=='' || document.getElementById("daySelect2").value=='' || document.getElementById("yearSelect2").value=='')
        {
           document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please input a valid ending date"+"</font></b>";
           document.getElementById("custom").focus();
           return false;
        }
    }
     if(document.getElementById("preset").checked==true && document.getElementById("marking_period").value=='')
    {
        document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please select marking period"+"</font></b>";
        document.getElementById("marking_period").focus();
        return false;
    }
   if(document.getElementById("fixed_schedule").checked==true)
   {
       var a=document.getElementById("course_period_day_checked");
        a.value="";
        var inputs = document.getElementsByTagName("input");
        var cp_id=document.getElementById("cp_id").value;
        var no_checkbox=0;
        for(var i = 0; i < inputs.length; i++) 
        {
            if(inputs[i].type == "checkbox") 
            {
//                alert(inputs[i].name+"==tables[course_period_var]["+cp_id+"][DAYS][M]");
                if(inputs[i].name=="tables[course_period_var]["+cp_id+"][DAYS][M]" || inputs[i].name=="tables[course_period_var]["+cp_id+"][DAYS][T]" ||inputs[i].name=="tables[course_period_var]["+cp_id+"][DAYS][W]" ||inputs[i].name=="tables[course_period_var]["+cp_id+"][DAYS][H]"|| inputs[i].name=="tables[course_period_var]["+cp_id+"][DAYS][F]")
                {   
                    no_checkbox=no_checkbox+1;
                    if(inputs[i].checked)
                    {
                        a.value="1";
                        break;
                    }
                    
                }
            }
            else
            no_checkbox=no_checkbox+0;
            
        }
        if(no_checkbox==0)
        a.value="1";   
        if(a.value.trim()=="")
        {
        document.getElementById("divErr").innerHTML='<font color="red"><b>You must select at least 1 day</b></font>';       
        return false;
        }
   }
    else
    return true;
}


///////////////////////////////////////// Course End ////////////////////////////////////////////////////////

//////////////////////////////////////// scheduling End ///////////////////////////////////////////////////////

//////////////////////////////////////// Grade Start ///////////////////////////////////////////////////////


function formcheck_grade_grade()
{var grade_id=document.getElementById("h1").value;
   
    var frmvalidator  = new Validator("F1");
    
    if(document.getElementById('values[new][GP_SCALE]')){
     if( (document.getElementById('values[new][GP_SCALE]') && document.getElementById('values[new][GP_SCALE]').value!='') ||
    (document.getElementById('values[new][COMMENT]') && document.getElementById('values[new][COMMENT]').value!='') ||
    (document.getElementById('values[new][SORT_ORDER]') && document.getElementById('values[new][SORT_ORDER]').value!='') )
    {
        frmvalidator.addValidation("values[new][TITLE]","req", "Gradescale cannot be blank");
    }
    }
    
    if(document.getElementById('values[new][BREAK_OFF]')){
    if( (document.getElementById('values[new][BREAK_OFF]') && document.getElementById('values[new][BREAK_OFF]').value!='') ||
    (document.getElementById('values[new][GPA_VALUE]') && document.getElementById('values[new][GPA_VALUE]').value!='') ||
    (document.getElementById('values[new][UNWEIGHTED_GP]') && document.getElementById('values[new][UNWEIGHTED_GP]').value!='') ||
    (document.getElementById('values[new][SORT_ORDER]') && document.getElementById('values[new][SORT_ORDER]').value!='') ||
    (document.getElementById('values[new][COMMENT]') && document.getElementById('values[new][COMMENT]').value!='') )
    {
        frmvalidator.addValidation("values[new][TITLE]","req", "Title cannot be blank");
    }
    }
    
    frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for short name is 50");
    frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort order allows only numeric value");
    frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5");
    
    if(document.getElementById('title') && document.getElementById('title').value!='')
    {
    frmvalidator.addValidation("values[new][GP_SCALE]","req", "Scale value cannot be blank");        
    
    frmvalidator.addValidation("values[new][GP_SCALE]","num", "Please enter numeric value");    
    }
    
    if(document.getElementById('values[new][TITLE]') && document.getElementById('values[new][TITLE]').value!='')
    {
    frmvalidator.addValidation("values[new][BREAK_OFF]","req", "Break off cannot be blank"); 
frmvalidator.addValidation("values[new][BREAK_OFF]","num", "Break off allows only numeric value");
     
   
    frmvalidator.addValidation("values[new][GPA_VALUE]","dec", "Please enter decimal value"); 
    
    frmvalidator.addValidation("values[new][UNWEIGHTED_GP]","dec", "Please enter decimal value");  
    if(document.getElementById('values[new][GP_SCALE]'))
    
      {
            frmvalidator.addValidation("values[new][GP_SCALE]","req", "Scale value cannot be blank");        
    
    frmvalidator.addValidation("values[new][GP_SCALE]","num", "Please enter numeric value");  
      }  
    }
    
//    if(document.getElementById('values[new][TITLE]') && document.getElementById('values[new][TITLE]').value=='')
//    {
//        frmvalidator.clearAllValidations();
//    }
    var grade_id=document.getElementById("h1").value;
    if(grade_id!='')
        {
            var id=grade_id;
            var ar=id.split(',');
            
            for(i=0;i<=ar.length-1;i++)
                {
                   
                

           if(document.getElementById('inputvalues['+ar[i]+'][TITLE]'))
           {
            frmvalidator.addValidation("values["+ar[i]+"][TITLE]","req", "Title cannot be blank");    
           }
           if(document.getElementById('inputvalues['+ar[i]+'][BREAK_OFF]'))
           {
            frmvalidator.addValidation("values["+ar[i]+"][BREAK_OFF]","num", "Break off allows only numeric value");    
            frmvalidator.addValidation("values["+ar[i]+"][BREAK_OFF]","req", "Break off cannot be blank");  
           }
           if(document.getElementById('inputvalues['+ar[i]+'][GPA_VALUE]'))
           {
          
           frmvalidator.addValidation("values["+ar[i]+"][GPA_VALUE]","dec", "Please enter decimal value");
      }
           if(document.getElementById('inputvalues['+ar[i]+'][UNWEIGHTED_GP]'))
           {
           
           frmvalidator.addValidation("values["+ar[i]+"][UNWEIGHTED_GP]","dec", "Please enter decimal value");  
           }
           
           if(document.getElementById('inputvalues['+ar[i]+'][GP_SCALE]'))
           {
           frmvalidator.addValidation("values["+ar[i]+"][GP_SCALE]","req", "Scale Value Cannot be blank"); 
           frmvalidator.addValidation("values["+ar[i]+"][GP_SCALE]","dec", "Please enter decimal value");  
           }
           
       
   }
           
       
        }

    
   
        
}
function formcheck_honor_roll()
{
    var frmvalidator  = new Validator("F1");

    var honor_id=document.getElementById("h1").value;
    
    if(honor_id!='')
    {
            var id=honor_id;
            var ar=id.split(',');
            
            for(i=0;i<=ar.length-1;i++)
                {
                frmvalidator.addValidation("values["+ar[i]+"][TITLE]","req", "Please enter Title");
                frmvalidator.addValidation("values["+ar[i]+"][TITLE]","maxlen=50", "Max length for title is 50");
                frmvalidator.addValidation("values["+ar[i]+"][VALUE]","req", "Breakoff cannot be blank");
                frmvalidator.addValidation("values["+ar[i]+"][VALUE]","num", "Breakoff allows only numeric value");
                frmvalidator.addValidation("values["+ar[i]+"][VALUE]","maxlen=10", "Max length for breakoff is 10");
                }
    }
    if(document.getElementById('values[new][TITLE]').value!='' || document.getElementById('values[new][VALUE]').value!='')
    {
    frmvalidator.addValidation("values[new][TITLE]","req", "Please enter Title");
    frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50");
    frmvalidator.addValidation("values[new][VALUE]","req", "Breakoff cannot be blank");
    frmvalidator.addValidation("values[new][VALUE]","num", "Breakoff allows only numeric value");
    frmvalidator.addValidation("values[new][VALUE]","maxlen=10", "Max length for breakoff is 10");
    }
}

//////////////////////////////////////// Report Card Comment Start ///////////////////////////////////////////////////////

function formcheck_grade_comment()
{

		var frmvalidator  = new Validator("F1");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "ID allows only numeric value");
		
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Comment is 50");
	
}

////////////////////////////////////////  Report Card Comment End  ///////////////////////////////////////////////////////


//////////////////////////////////////// Grade End ///////////////////////////////////////////////////////

///////////////////////////////////////// Eligibility Start ////////////////////////////////////////////////////

///////// Activities Start/////////////////////////////

function formcheck_eligibility_activies()
{
	
//	var frmvalidator  = new Validator("F1");
//        var ar_id=document.getElementById('id_arr').value;
//        ar_id=ar_id.trim();
//       
//        if(ar_id!=0)
//        {
//            var ar_id=ar_id.split(',');
//            for(var i=1;i<=ar_id.length;i++)
//            {
//            frmvalidator.addValidation("values["+ar_id[i]+"][TITLE]","req", "Title cannot be blank");
//            frmvalidator.addValidation("values["+ar_id[i]+"][TITLE]","maxlen=20", "Max length for Title is 20");    
//            }
//        }

        var month=document.getElementById('monthSelect0').value;
        var day=document.getElementById('daySelect0').value;
        var year=document.getElementById('yearSelect0').value;
        var year_end=document.getElementById('yearSelect500000').value;

        
        if(month.trim()!='' || day.trim()!='' || year.trim()!='' || year_end.trim()!='')
        {
        frmvalidator.addValidation("values[new][TITLE]","req", "Title cannot be blank");
	frmvalidator.addValidation("values[new][TITLE]","maxlen=20", "Max length for Title is 20");    
        }
	frmvalidator.setAddnlValidationFunction("ValidateDate_eligibility_activies");

}


	
	function ValidateDate_eligibility_activies()
	{
		var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F1"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="month_values[new][START_DATE]")
			{
				sm=elem[i];
			}
			
			if(elem[i].name=="day_values[new][START_DATE]")
			{
				sd=elem[i];
			}
			
			if(elem[i].name=="year_values[new][START_DATE]")
			{
				sy=elem[i];
			}
			
			if(elem[i].name=="month_values[new][END_DATE]")
			{
				em=elem[i];
			}
			
			if(elem[i].name=="day_values[new][END_DATE]")
			{
				ed=elem[i];
			}
			
			if(elem[i].name=="year_values[new][END_DATE]")
			{
				ey=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckDate(sm, sd, sy, em, ed, ey))
		   {
			   em.focus();
			   return false;
		   }
		}
		catch(err)
		{
		
		}

		try
		{  
		   if (false==isDate(psm, psd, psy))
		   {
			   alert("Please enter the grade posting start date");
			   psm.focus();
			   return false;
		   }
		}   
		catch(err)
		{
		
		}
		
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		
		}
		   
		   return true;
		
	}




///////////////////////////////////////// Activies End ////////////////////////////////////////////////////



///////////////////////////////////////// Entry Times Start ////////////////////////////////////////////////

function formcheck_eligibility_entrytimes()
{
  	var frmvalidator  = new Validator("F1");
	frmvalidator.setAddnlValidationFunction("ValidateTime_eligibility_entrytimes");
}

	function ValidateTime_eligibility_entrytimes()
	{
		var sd, sh, sm, sp, ed, eh, em, ep, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F1"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="values[START_DAY]")
			{
				sd=elem[i];
			}
			if(elem[i].name=="values[START_HOUR]")
			{
				sh=elem[i];
			}
			if(elem[i].name=="values[START_MINUTE]")
			{
				sm=elem[i];
			}
			if(elem[i].name=="values[START_M]")
			{
				sp=elem[i];
			}
			if(elem[i].name=="values[END_DAY]")
			{
				ed=elem[i];
			}
			if(elem[i].name=="values[END_HOUR]")
			{
				eh=elem[i];
			}
			if(elem[i].name=="values[END_MINUTE]")
			{
				em=elem[i];
			}
			if(elem[i].name=="values[END_M]")
			{
				ep=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckTime(sd, sh, sm, sp, ed, eh, em, ep))
		   {
			   sh.focus();
			   return false;
		   }
		}
		catch(err)
		{
		}
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		}
		
		   return true;
	}




///////////////////////////////////////// Entry Times End //////////////////////////////////////////////////
       
function formcheck_mass_drop()
{
    if(document.getElementById("course_div").innerHTML=='')
    {    
        alert("Please choose a course period to drop");
        return false;
    }
    else
        return true;
}



function formcheck_attendance_category()
{
        var frmvalidator  = new Validator("F1");
        frmvalidator.addValidation("new_category_title","req","Please enter attendance category Name");
        frmvalidator.addValidation("new_category_title","maxlen=50", "Max length for category name is 50");
        frmvalidator.addValidation("new_category_title","alphanumeric", "Attendance category Name allows only alphanumeric value");	
}


function formcheck_attendance_codes()
{    
        var frmvalidator  = new Validator("F1");
     
 var attandance_id=document.getElementById("h1").value;
if(attandance_id!='')
        {
            var id=attandance_id;
            var ar=id.split(',');
            
            for(i=0;i<=ar.length-1;i++)
                {
               
                

           
            frmvalidator.addValidation("values["+ar[i]+"][TITLE]","req", "Title cannot be blank");    
            frmvalidator.addValidation("values["+ar[i]+"][TITLE]","maxlen=50", "Max length for title is 50 characters");
            frmvalidator.addValidation("values["+ar[i]+"][SHORT_NAME]","req", "Short Name cannot be blank"); 
     
      frmvalidator.addValidation("values["+ar[i]+"][SORT_ORDER]","num", "Short Order allows only numeric value"); 
      frmvalidator.setAddnlValidationFunction(formcheck_attendance_codes_extra);
   

   }
           
       
        }
        
        if( (document.getElementById('values[new][SHORT_NAME]') && document.getElementById('values[new][SHORT_NAME]').value!='') ||
    (document.getElementById('values[new][SORT_ORDER]') && document.getElementById('values[new][SORT_ORDER]').value!='') )
    {
        frmvalidator.addValidation("values[new][TITLE]","req", "Title cannot be blank");
    }
    if( (document.getElementsByName('values[new][TYPE]') && document.getElementsByName('values[new][TYPE]')[0].value!='') ||
      (document.getElementsByName('values[new][DEFAULT_CODE]') && document.getElementsByName('values[new][DEFAULT_CODE]')[0].checked==true) ||
  (document.getElementsByName('values[new][STATE_CODE]') && document.getElementsByName('values[new][STATE_CODE]')[0].value!='') )
    {
        frmvalidator.addValidation("values[new][TITLE]","req", "Title cannot be blank");
    }
        
        
        if(document.getElementById("values[new][TITLE]").value.trim()!='')
        {    frmvalidator.addValidation("values[new][TITLE]","req","Title cannot be blank");       
            frmvalidator.addValidation("values[new][TITLE]","maxlen=50","Max length for title is 50");
            frmvalidator.addValidation("values[new][SHORT_NAME]","req","Short Name cannot be blank"); 
      
      frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Short Order allows only numeric value");
            frmvalidator.setAddnlValidationFunction(formcheck_attendance_codes_extra);
        }
}
function formcheck_attendance_codes_extra()
{
    if(document.getElementById("values[new][TITLE]").value.trim()!='')
    {
                        var sel = document.getElementsByTagName("select");
			for(var i=1; i<sel.length; i++)
			{
                            var inp_name = sel[i].name;
                            var inp_value = sel[i].value;
                            if(inp_name == 'values[new][TYPE]')
			    {
                                  
                                  if(inp_value == "")
                                  {
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter type")+"</font></b>";
						return false;
                                  }
			    }
			    else if(inp_name == 'values[new][STATE_CODE]')
			    {
                                if(inp_value == "")
                                  {
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter state code")+"</font></b>";
						return false;
                                  }
			    }
                        }
    }
    var count=document.getElementById("count").value.trim();       
        for(var j=1;j<=count;j++)
        {             
             var sel = document.getElementsByTagName("select");
			for(var i=1; i<sel.length; i++)
			{
                            var inp_name = sel[i].name;
                            var inp_value = sel[i].value;
                            if(inp_name == 'values['+j+'][TYPE]')
			    {
                                  
                                  if(inp_value == "")
                                  {
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter type")+"</font></b>";
						return false;
                                  }
			    }
			    else if(inp_name == 'values['+j+'][STATE_CODE]')
			    {
                                if(inp_value == "")
                                  {
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter state code")+"</font></b>";
						return false;
                                  }
			    }
                        }
        }
                        
                        return true;
}
function formcheck_failure_count()
{
       var frmvalidator  = new Validator("failure");
       frmvalidator.addValidation("failure[FAIL_COUNT]","req","Please enter count");
       frmvalidator.addValidation("failure[FAIL_COUNT]","num", "Count allows only numeric value");
       frmvalidator.addValidation("failure[FAIL_COUNT]","maxlen=5", "Max length for count order is 5 digits");
		
}
//-------------------------------------------------assignments Title Validation Starts---------------------------------------------
function formcheck_assignments()
{

           var frmvalidator  = new Validator("F3");
           var type_id=document.getElementById("type_id").value;

         if(type_id.trim()=='')
         {

           frmvalidator.addValidation("tables[new][TITLE]","req","Title cannot be blank");
           frmvalidator.addValidation("tables[new][TITLE]","maxlen=50","Max length for title is 50");
           frmvalidator.addValidation("tables[new][POINTS]","req","Total points cannot be blank");
           frmvalidator.addValidation("month_tables[new][ASSIGNED_DATE]","req","Assigned date cannot be blank");
          frmvalidator.addValidation("month_tables[new][DUE_DATE]","req","Due date cannot be blank");
          
        }
        else
        {

            frmvalidator.addValidation("tables["+type_id+"][TITLE]","req","Title cannot be blank");
           frmvalidator.addValidation("tables["+type_id+"][TITLE]","maxlen=50","Max length for title is 50");
           frmvalidator.addValidation("tables["+type_id+"][POINTS]","req","Total points cannot be blank");
           frmvalidator.addValidation("month_tables["+type_id+"][ASSIGNED_DATE]","req","Assigned date cannot be blank");
          frmvalidator.addValidation("month_tables["+type_id+"][DUE_DATE]","req","Due date cannot be blank");  
        }
           
}
//-------------------------------------------------assignments Title Validation Ends---------------------------------------------


function passwordStrength(password)

{
    document.getElementById("passwordStrength").style.display = "none";

        var desc = new Array();

        desc[0] = "Very Weak";

        desc[1] = "Weak";

        desc[2] = "Good";

        desc[3] = "Strong";

        desc[4] = "Strongest";


        //if password bigger than 7 give 1 point

        if (password.length > 0) 
        {   
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#cccccc" ;
            document.getElementById("passwordStrength").innerHTML = desc[0] ;
            
            
        }


        //if password has at least one number give 1 point

        if (password.match(/\d+/) && password.length > 5) 
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#ff0000" ;
            document.getElementById("passwordStrength").innerHTML = desc[1] ;
        }



        //if password has at least one special caracther give 1 point

        if (password.match(/\d+/) && password.length > 7 && password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) )
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#ff5f5f" ;
            document.getElementById("passwordStrength").innerHTML = desc[2] ;
        }

        
        //if password has both lower and uppercase characters give 1 point      

        if (password.match(/\d+/) && password.length > 10 && password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) && ( password.match(/[A-Z]/) ) ) 
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#56e500" ;
            document.getElementById("passwordStrength").innerHTML = desc[3] ;
        }


        //if password bigger than 12 give another 1 point

        if (password.match(/\d+/) &&  password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) && ( password.match(/[a-z]/) ) && ( password.match(/[A-Z]/) ) && password.length > 12)
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#4dcd00" ;
            document.getElementById("passwordStrength").innerHTML = desc[4] ;
        }

}


function forgotpasswordStrength(password)

{
    document.getElementById("passwordStrength").style.display = "none";

        var desc = new Array();

        desc[0] = "Very Weak";

        desc[1] = "Weak";

        desc[2] = "Good";

        desc[3] = "Strong";

        desc[4] = "Strongest";


        //if password bigger than 7 give 1 point

        if (password.length > 0) 
        {   
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#cccccc" ;
            document.getElementById("passwordStrength").innerHTML = desc[0] ;
            
            
        }


        //if password has at least one number give 1 point

        if (password.match(/\d+/) && password.length > 5) 
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#ff0000" ;
            document.getElementById("passwordStrength").innerHTML = desc[1] ;
        }



        //if password has at least one special caracther give 1 point

        if (password.match(/\d+/) && password.length > 7 && password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) && ( password.match(/[A-Z]/) ))
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#ff5f5f" ;
            document.getElementById("passwordStrength").innerHTML = desc[2] ;
        }

        
        //if password has both lower and uppercase characters give 1 point      

        if (password.match(/\d+/) && password.length > 10 && password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) && ( password.match(/[A-Z]/) ) ) 
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#56e500" ;
            document.getElementById("passwordStrength").innerHTML = desc[3] ;
        }


        //if password bigger than 12 give another 1 point

        if (password.match(/\d+/) &&  password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) && ( password.match(/[a-z]/) ) && ( password.match(/[A-Z]/) ) && password.length > 12)
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#4dcd00" ;
            document.getElementById("passwordStrength").innerHTML = desc[4] ;
        }

}

function passwordStrengthMod(password,opt)

{
    document.getElementById("passwordStrength"+opt).style.display = "none";

        var desc = new Array();

        desc[0] = "Very Weak";

        desc[1] = "Weak";

        desc[2] = "Good";

        desc[3] = "Strong";

        desc[4] = "Strongest";


        //if password bigger than 7 give 1 point

        if (password.length > 0) 
        {   
            document.getElementById("passwordStrength"+opt).style.display = "block" ;
            document.getElementById("passwordStrength"+opt).style.backgroundColor = "#cccccc" ;
            document.getElementById("passwordStrength"+opt).innerHTML = desc[0] ;
            
            
        }


        //if password has at least one number give 1 point

        if (password.match(/\d+/) && password.length > 5) 
        {
            document.getElementById("passwordStrength"+opt).style.display = "block" ;
            document.getElementById("passwordStrength"+opt).style.backgroundColor = "#ff0000" ;
            document.getElementById("passwordStrength"+opt).innerHTML = desc[1] ;
        }



        //if password has at least one special caracther give 1 point

        if (password.match(/\d+/) && password.length > 7 && password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) )
        {
            document.getElementById("passwordStrength"+opt).style.display = "block" ;
            document.getElementById("passwordStrength"+opt).style.backgroundColor = "#ff5f5f" ;
            document.getElementById("passwordStrength"+opt).innerHTML = desc[2] ;
        }

        
        //if password has both lower and uppercase characters give 1 point      

        if (password.match(/\d+/) && password.length > 10 && password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) && ( password.match(/[A-Z]/) ) ) 
        {
            document.getElementById("passwordStrength"+opt).style.display = "block" ;
            document.getElementById("passwordStrength"+opt).style.backgroundColor = "#56e500" ;
            document.getElementById("passwordStrength"+opt).innerHTML = desc[3] ;
        }


        //if password bigger than 12 give another 1 point

        if (password.match(/\d+/) &&  password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) && ( password.match(/[a-z]/) ) && ( password.match(/[A-Z]/) ) && password.length > 12)
        {
            document.getElementById("passwordStrength"+opt).style.display = "block" ;
            document.getElementById("passwordStrength"+opt).style.backgroundColor = "#4dcd00" ;
            document.getElementById("passwordStrength"+opt).innerHTML = desc[4] ;
        }

}
function passwordMatch()
{
    document.getElementById("passwordMatch").style.display = "none" ;
    var new_pass = document.getElementById("new_pass").value;
    var vpass = document.getElementById("ver_pass").value;
    if(new_pass || vpass)
    {
        if(new_pass==vpass)
        {
            document.getElementById("passwordMatch").style.display = "block" ;
            document.getElementById("passwordMatch").style.backgroundColor = "#4dcd00" ;
            document.getElementById("passwordMatch").innerHTML = "Password Match" ;
        }
        if(new_pass!=vpass && vpass!='')
        {
            document.getElementById("passwordMatch").style.display = "block" ;
            document.getElementById("passwordMatch").style.backgroundColor = "#ff0000" ;
            document.getElementById("passwordMatch").innerHTML = "Password MisMatch" ;    
        }
    }
    
}
function pass_check()
{
    if(document.getElementById("new_pass").value==document.getElementById("ver_pass").value)
    {
        var new_pass = document.getElementById("new_pass").value;

        if(new_pass.length < 7 || (new_pass.length > 7 && !new_pass.match((/\d+/))) || (new_pass.length > 7 && !new_pass.match((/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/))))
        {
            document.getElementById('divErr').innerHTML="<b><font color=red>Password must be minimum 8 characters long with at least one capital, one numeric and one special character</font></b>";
            return false;
        }
        
        return true;
    }
    else
    {
        document.getElementById('divErr').innerHTML="<b><font color=red>New Password MisMatch</font></b>";
        return false;
    }
}

function reenroll()
{
    if(document.getElementById("monthSelect1").value=='' || document.getElementById("daySelect1").value=='' || document.getElementById("yearSelect1").value=='')
    {    
        document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter a Proper Date</font></b>";
        return false;
    }
    if(document.getElementById("grade_id").value=='')
    {    
        document.getElementById('divErr').innerHTML="<b><font color=red>Please Select a Grade Level</font></b>";
        return false;
    }
    if(document.getElementById("en_code").value=='')
    {    
        document.getElementById('divErr').innerHTML="<b><font color=red>Please Select an Enrollment Code</font></b>";
        return false;
    }
    
    else
    {
        var x = document.getElementById("sav").elements.length;
        var counter=0;
        for(var i=0;i<=x;i++)
        {
           if(document.getElementById("sav").elements[i])
           {
           var type=document.getElementById("sav").elements[i].type;
            if(type=="checkbox")
            {
                if(document.getElementById("sav").elements[i])
                {
                if(document.getElementById("sav").elements[i].name && document.getElementById("sav").elements[i].name!='')    
                {
                if(document.getElementById("sav").elements[i].checked==true)
                counter++;
                }

                }
            }
           }
        }
        if(counter==0)
        {
        document.getElementById('divErr').innerHTML='<b><font style="color:red">Please select a student</font></b>';
        return false;
        }
        else
        {
         return true;
        }
    }
}

function sel_staff_val()
{
    var sel_stf_info = document.getElementsByName('staff');
    var ischecked_method = false;
    for ( var i = 0; i < sel_stf_info.length; i++) 
    {
    
        if(sel_stf_info[i].checked) 
        {
            ischecked_method = true;
            break;
        }
    }
    if(!ischecked_method)   
    { 
        document.getElementById('sel_err').innerHTML="<b><font color=red>Please select any one.</font></b>";
        return false;
    }
    else
    {
        return true;
    }
}
function formcheck_add_staff(staff_school_chkbox_id)
{

      var frmvalidator  = new Validator("staff");
        
  	frmvalidator.addValidation("staff[TITLE]","req","Please select the salutation");
        frmvalidator.addValidation("staff[FIRST_NAME]","req","Please enter the first name");

  	frmvalidator.addValidation("staff[FIRST_NAME]","maxlen=100", "Max length for first name is 100 characters");
	frmvalidator.addValidation("staff[LAST_NAME]","req","Please enter the Last Name");
  	frmvalidator.addValidation("staff[LAST_NAME]","maxlen=100", "Max length for Address is 100");
        frmvalidator.addValidation("staff[EMAIL]","req","Please select email");
        frmvalidator.addValidation("staff[EMAIL]","email","Invalid email");
        frmvalidator.addValidation("values[SCHOOL][CATEGORY]","req","Please select the category");
        frmvalidator.addValidation("month_values[JOINING_DATE]","req", "Please select the joining date's month");
        frmvalidator.addValidation("day_values[JOINING_DATE]","req", "Please select the joining date's date");
        frmvalidator.addValidation("year_values[JOINING_DATE]","req", "Please select the joining date's year");
        
        var end_date = document.getElementById('end_date_school').value;
        end_date = end_date.split('-');
        var end = new Date(end_date[0], end_date[1], end_date[2]);
        
        var cur = document.getElementById('date_1').value;
        cur = cur.split('-');
        var current = new Date(cur[0], cur[1], cur[2]);
        
        if(current>=end)
        {
            document.getElementById('divErr').innerHTML='<b><font style="color:red">Joining date can not be after school\'s end date</font></b>';
            return false;
        }

        
        if(document.getElementById('r4'))
        {
        if(document.getElementById('r4').checked==true)
        {
//            if(document.getElementById('usr_err_check').value==0)
//            {
//                return false;
//            }
        frmvalidator.addValidation("USERNAME","req", "Please provide username");
        frmvalidator.addValidation("PASSWORD","req", "Please provide password");
        }
        }
        if(document.getElementById('no_date_fields'))
        {
        var no_date_fields=document.getElementById('no_date_fields').value;
        no_date_fields=parseInt(no_date_fields);
        var counter=0;
        var error_handler=0;
            for(var j=0;j<=no_date_fields;j++)
            {
                counter=counter+1;
                var in_date=document.getElementById('date_'+counter).value;
                counter=counter+1;
                var en_date=document.getElementById('date_'+counter).value;
                if(in_date!='' && en_date=='')
                {
                    error_handler=error_handler+1;
                    frmvalidator.clearAllValidations();
                    frmvalidator.addValidation("error_handler","req", "Please provide certification expiry date");
                }
                if(in_date=='' && en_date!='')
                {
                    error_handler=error_handler+1;
                    frmvalidator.clearAllValidations();
                    frmvalidator.addValidation("error_handler","req", "Please provide certification date");
                }
                if(in_date!='' && en_date!='')
                {
                    in_date=new Date(in_date);
                    en_date=new Date(en_date);
                    if(in_date>en_date)
                    {
                    error_handler=error_handler+1;    
                    frmvalidator.clearAllValidations();
                    frmvalidator.addValidation("error_handler","req", "Certification date cannot be after certification expiry date");
                    }
                    else
                    {
                    error_handler=error_handler+0;    
                    }
                }
            }
            if(error_handler==0)
            frmvalidator.clearAllValidations();
        }
        if(staff_school_chkbox_id!=0 && staff_school_chkbox_id!='')
        return school_check(staff_school_chkbox_id);
}
function formcheck_user_user_mod()
{
            
        
  	var frmvalidator  = new Validator("staff");
        
  	frmvalidator.addValidation("people[FIRST_NAME]","req","Please enter the first name");
  	frmvalidator.addValidation("people[FIRST_NAME]","maxlen=100", "Max length for first name is 100 characters");
	
		
	frmvalidator.addValidation("people[LAST_NAME]","req","Please enter the Last Name");

  	frmvalidator.addValidation("people[LAST_NAME]","maxlen=100", "Max length for Address is 100");
    
        frmvalidator.addValidation("people[EMAIL]","email", "Please enter a valid email");
        frmvalidator.addValidation("people[EMAIL]","req", "Please enter the email");

        
        frmvalidator.addValidation("student_addres[ADDRESS]","req","Please enter the address");
        frmvalidator.addValidation("student_addres[CITY]","req","Please enter the city");
        frmvalidator.addValidation("student_addres[STATE]","req","Please enter the state");
        frmvalidator.addValidation("student_addres[ZIPCODE]","req","Please enter the zipcode");
        
}
function validate_email()
{
    var frmvalidator  = new Validator("ComposeMail");
    frmvalidator.setAddnlValidationFunction("mail_body_chk");
    if(!(document.getElementById('cp_id')))
    {
    frmvalidator.addValidation("txtToUser","req","Enter message recipient");
    }
    
}
function mail_body_chk()
{
 var oEditor = FCKeditorAPI.GetInstance('txtBody');
     var body1 = oEditor.GetHTML(true);
     if(body1 == '') 
     {
                document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please write body of message")+"</font></b>";		
		this.txtBody.focus();
                return false;
     }
     else
        return true;    
}
function validate_group_schedule()
{
        var x = document.getElementById("sav").elements.length;
        var counter=0;
        for(var i=0;i<=x;i++)
        {
           if(document.getElementById("sav").elements[i])
           {
           var type=document.getElementById("sav").elements[i].type;
            if(type=="checkbox")
            {
                if(document.getElementById("sav").elements[i])
                {
                if(document.getElementById("sav").elements[i].name && document.getElementById("sav").elements[i].name!='')    
                {
                if(document.getElementById("sav").elements[i].checked==true)
                counter++;
                }

                }
            }
           }
        }
        if(counter==0)
        {
        document.getElementById('divErr').innerHTML='<b><font style="color:red">Please select a student</font></b>';
        return false;
        }
        else
        {
         formload_ajax("sav");   
        }
}
function formcheck_rooms()
{
    var frmvalidator  = new Validator("F1");
   var count_room=document.getElementById("count_room").value.trim();

   if(document.getElementById('values[new][TITLE]').value!='' || document.getElementById('values[new][CAPACITY]').value!='' || document.getElementById('values[new][DESCRIPTION]').value!='' || document.getElementById('values[new][SORT_ORDER]').value!='')
    {
        frmvalidator.addValidation("values[new][TITLE]","req", "Please enter the title");
        frmvalidator.addValidation("values[new][CAPACITY]","req", "Please enter the capacity");
  	frmvalidator.addValidation("values[new][DESCRIPTION]","maxlen=100", "Max length for DESCRIPTION is 100 characters");
        frmvalidator.addValidation("values[new][CAPACITY]","num", "Capacity allows only numeric value");
        frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort Order allows only numeric value");
  
    }
    else
    {
        frmvalidator.clearAllValidations();
    }
    var honor_id=document.getElementById("h1").value;
    
    if(honor_id!='')
        {
            var id=honor_id;
            var ar=id.split(',');
            
            for(i=0;i<=ar.length-1;i++)
                {
                    frmvalidator.addValidation("inputvalues["+ar[i]+"][TITLE]","req","Please enter the title");
          frmvalidator.addValidation("inputvalues["+ar[i]+"][CAPACITY]","req", "Please enter the capacity");
            frmvalidator.addValidation("inputvalues["+ar[i]+"][DESCRIPTION]","maxlen=100", "Max length for DESCRIPTION is 100 characters");
            frmvalidator.addValidation("inputvalues["+ar[i]+"][CAPACITY]","num", "Capacity allows only numeric value");
            frmvalidator.addValidation("inputvalues["+ar[i]+"][SORT_ORDER]","num", "Sort Order allows only numeric value");
            frmvalidator.addValidation("values["+ar[i]+"][DESCRIPTION]","maxlen=100", "Max length for DESCRIPTION is 100 characters");
         
            frmvalidator.addValidation("values["+ar[i]+"][SORT_ORDER]","num", "Sort Order allows only numeric value");
            
                }
}

}
function fill_rooms(option,id)
{
    var room_iv=document.getElementById("room_iv").value;
    if(room_iv!='')
    room_iv=room_iv.split(",");
    if(room_iv.length>0)
    {
        for(var i=0;i<room_iv.length;i++)
        {
            var rd=room_iv[i].split("_");
            if(rd[0]==id)
            {
                var old_string=room_iv[i];
                var new_string=id+'_'+option.value;
            }
        }
    }
    var new_res=document.getElementById("room_iv").value.replace(old_string,new_string);
    document.getElementById("room_iv").value=new_res;
    
}
function formcheck_Timetable_course_F4()
{
    var frmvalidator  = new Validator("F4");
        
  	frmvalidator.addValidation("tables[course_subjects][new][TITLE]","req","Please enter the subject name");
  	frmvalidator.addValidation("tables[course_subjects][new][TITLE]","maxlen=100", "Max length for subject is 100 characters");
	
}
function formcheck_halfday_fullday()
{
    var frmvalidator  = new Validator("sys_pref");
    frmvalidator.addValidation("inputvalues[FULL_DAY_MINUTE]","maxlen=10", "Max length for full day minute is 10 digits");
    frmvalidator.addValidation("inputvalues[HALF_DAY_MINUTE]","maxlen=10", "Max length for half day minute is 10 digits");
    frmvalidator.addValidation("inputvalues[FULL_DAY_MINUTE]","num", "Full day minute allows only numeric value");
    frmvalidator.addValidation("inputvalues[HALF_DAY_MINUTE]","num", "Half day minute allows only numeric value");
}
function formcheck_Timetable_course_F3()
{
    var frmvalidator  = new Validator("F3");
    var course_id=document.getElementById('course_id_div').value;      
    if(course_id=='new')
    {
  	frmvalidator.addValidation("tables[courses][new][TITLE]","req","Please enter the course title ");
  	frmvalidator.addValidation("tables[courses][new][TITLE]","maxlen=50", "Max length for course title is 50 characters ");

        frmvalidator.addValidation("tables[courses][new][SHORT_NAME]","maxlen=25", "Max length for short name is 25 characters ");        
    }
    else
    {
        frmvalidator.addValidation("inputtables[courses]["+course_id+"][TITLE]","req","Please enter the course title ");
  	frmvalidator.addValidation("inputtables[courses]["+course_id+"][TITLE]","maxlen=50", "Max length for course title is 50 characters");

        frmvalidator.addValidation("inputtables[courses]["+course_id+"][SHORT_NAME]","maxlen=25", "Max length for short name is 25 characters"); 
    }
}

function mail_group_chk()
{
     var frmvalidator  = new Validator("Group");
     frmvalidator.addValidation("txtGrpName","req", "Please enter the group name");	

    frmvalidator.addValidation("txtGrpName","maxlen=100", "Max length for group name is 100 characters");	
}

function formcheck_enrollment_code()
{

        var frmvalidator  = new Validator("F1");
        var sn=document.getElementById("values[new][SHORT_NAME]").value;
        var t=document.getElementsByName("values[new][TYPE]")[0].value ;
       

        if(sn.trim()!='' || t!='')
{
        frmvalidator.addValidation("values[new][TITLE]","req", "Title cannot be blank");
        frmvalidator.addValidation("values[new][TITLE]","alphanumeric", "Title allows only alphanumeric value");
        frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50 characters");
}
else
    {
        frmvalidator.clearAllValidations();
    }
    
        var title=document.getElementsByName("values[new][TITLE]")[0].value ;
        if(title!='' && sn=='')
        {
            frmvalidator.addValidation("values[new][SHORT_NAME]","req", "Short name cannot be blank");
        }
        if(title!='' && t=='')
        {
            frmvalidator.addValidation("values[new][TYPE]","req", "Type cannot be blank");
        }
        
    
        var ar_id=document.getElementById('id_arr').value;
        ar_id=ar_id.trim();
        if(ar_id!=0)
        {
            var ar_id=ar_id.split(',');
            for(var i=0;i<ar_id.length;i++)
            {
            frmvalidator.addValidation("values["+ar_id[i]+"][TITLE]","req", "Title cannot be blank");
            frmvalidator.addValidation("values["+ar_id[i]+"][TITLE]","alphanumeric", "Title allows only alphanumeric value");
            frmvalidator.addValidation("values["+ar_id[i]+"][TITLE]","maxlen=50", "Max length for title is 50 characters"); 
            frmvalidator.addValidation("values["+ar_id[i]+"][SHORT_NAME]","req", "Short name cannot be blank");
             frmvalidator.addValidation("values["+ar_id[i]+"][TYPE]","req", "Type cannot be blank");
        }
        }
  
        
}
function formcheck_calendar_event()
{
        var title=document.getElementById('title');        
        if(title!=null)
        {          
        if(title.value.trim()=='')
        {
            document.getElementById('err_message').innerHTML='<font style="color:red"><b>Title cannot be blank.</b></font>';
            return false;
        }
        else if(title.value.length>50)
        {
            document.getElementById('err_message').innerHTML='<font style="color:red"><b>Max length for title is 50 characters.</b></font>';
            return false;
        }
        else
            formload_ajax('popform');   
        }
        else
        {           
            var title=document.getElementById('values[TITLE]');
            if(title.value.trim()=='')
            {
                document.getElementById('err_message').innerHTML='<font style="color:red"><b>Title cannot be blank.</b></font>';
                return false;
            }
            else if(title.value.length>50)
            {
                document.getElementById('err_message').innerHTML='<font style="color:red"><b>Max length for title is 50 characters.</b></font>';
                return false;
            }
            else
                formload_ajax('popform');   
        }
        
        
}
function formcheck_common_standards()
{
    var frmvalidator  = new Validator("standard");
        if(document.getElementById("values[new][SUBJECT]").value!='')
        {           
            frmvalidator.addValidation("values[new][STANDARD_REF_NO]","req","Please enter standard ref number");
            frmvalidator.addValidation("values[new][SUBJECT]","maxlen=50","Max length for subject is 50");
            frmvalidator.addValidation("values[new][GRADE]","maxlen=50","Max length for grade is 50");
            frmvalidator.addValidation("values[new][COURSE]","maxlen=50","Max length for course is 50");
             frmvalidator.addValidation("values[new][DOMAIN]","maxlen=50","Max length for domain is 50");
             frmvalidator.addValidation("values[new][TOPIC]","maxlen=50","Max length for topic is 50");
             frmvalidator.addValidation("values[new][STANDARD_REF_NO]","maxlen=50","Max length for ref number is 50");
             frmvalidator.addValidation("values[new][STANDARD_DETAILS]","maxlen=50","Max length for ref details is 50");
             
        }
        var count=document.getElementById("count").value.trim();

        for(var i=1;i<=count;i++)
        {
             frmvalidator.addValidation("inputvalues["+i+"][STANDARD_REF_NO]","req","Please enter standard ref number");
            frmvalidator.addValidation("inputvalues["+i+"][SUBJECT]","maxlen=50","Max length for subject is 50");
            frmvalidator.addValidation("inputvalues["+i+"][GRADE]","maxlen=50","Max length for grade is 50");
            frmvalidator.addValidation("inputvalues["+i+"][COURSE]","maxlen=50","Max length for course is 50");
             frmvalidator.addValidation("inputvalues["+i+"][DOMAIN]","maxlen=50","Max length for domain is 50");
             frmvalidator.addValidation("inputvalues["+i+"][TOPIC]","maxlen=50","Max length for topic is 50");
             frmvalidator.addValidation("inputvalues["+i+"][STANDARD_REF_NO]","maxlen=50","Max length for ref number is 50");
             frmvalidator.addValidation("inputvalues["+i+"][STANDARD_DETAILS]","maxlen=50","Max length for ref details is 50");
        }
}

function check_effort_cat()
{
            var frmvalidator  = new Validator("cat");
            frmvalidator.addValidation("TITLE","req","Please enter title");
            frmvalidator.addValidation("TITLE","maxlen=50","Max length for title is 50");
            frmvalidator.addValidation("SORT_ORDER","num", "Sort order allows only numeric value");
}
function check_effort_item()
{
            var frmvalidator  = new Validator("F1");
            var count=document.getElementById("count_item").value.trim();           
            if(count!='0')
            {
                for(var i=count;i>0;i--)
                {
                    frmvalidator.addValidation("inputvalues["+i+"][TITLE]","maxlen=50","Max length for title is 50");
                    frmvalidator.addValidation("inputvalues["+i+"][SORT_ORDER]","num", "Sort order allows only numeric value");
                }
            }
            if(document.getElementById("values[new][TITLE]").value!="")
            {

                frmvalidator.addValidation("values[new][TITLE]","maxlen=50","Max length for title is 50");
                frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort order allows only numeric value");
            }
}

function forgotpass()
{
    document.getElementById("valid_func").value='Y';
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
            return true;

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
            return true;
    }
}

function forgotusername()
{
    document.getElementById("valid_func").value='Y';
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
            return true;

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
            return true;
    }
}


function check_update_seat(course_period_id)
{
     var frmvalidator  = new Validator("update_seats");
    alert(2);
    frmvalidator.addValidation("tables[course_periods]["+course_period_id+"][TOTAL_SEATS]","req","Please enter a number");
    alert(3);
}
   	
function formcheck_ada_dates()
{
//  	var frmvalidator  = new Validator("ada_from");
  	var date_1=document.getElementById('date_1').value;
        var date_2=document.getElementById('date_2').value;
        
        if(date_1!='' && date_2!='')
        {
         var date_1_obj=new Date(date_1);
         var date_2_obj=new Date(date_2);
         
            if(date_1_obj>date_2_obj)
            {
            document.getElementById('divErr').innerHTML='<font style="color:red"><b>Start date cannot be after end date.</b></font>';
            return false;
            }
            else
           var frmvalidator  = new Validator("ada_from");
        }
        if(date_1=='')
        {
           document.getElementById('divErr').innerHTML='<font style="color:red"><b>Start cannot be blank.</b></font>';
           return false;
        }
        if(date_2=='')
        {
           document.getElementById('divErr').innerHTML='<font style="color:red"><b>End cannot be blank.</b></font>';
           return false;
        }
}
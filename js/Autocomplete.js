function nameslist(textvalue,id)
{

    if(textvalue!="")
    {
    if(id==1)
    ajax_call('NamesList.php?str='+textvalue+'&block_id='+id, namecheck_match, namecheck_unmatch); 
    if(id==2)
    ajax_call('NamesList.php?str='+textvalue+'&block_id='+id, namecheck_matchCC); 
    if(id==3)
    ajax_call('NamesList.php?str='+textvalue+'&block_id='+id, namecheck_matchBCC); 
    }
}
 function namecheck_match(data) {
 	var response = data;
if(response!=0)
{    
 document.getElementById("ajax_response").innerHTML=response;
 return true;
  }
else
  {
  return false;
  }
 }
 
 function namecheck_unmatch (err) {
 	alert ("Error: " + err);
 }

function namecheck_matchCC(data) 
{
    var response = data;
    if(response!=0)
    {    
     document.getElementById("ajax_response_cc").innerHTML=response;
     return true;
    }
    else
    return false;

}
function namecheck_matchBCC(data) 
{
    var response = data;
    if(response!=0)
    {    
     document.getElementById("ajax_response_bcc").innerHTML=response;
     return true;
    }
    else
    return false;

}
 function a(id,block_id)
 { 
     
    if(block_id==1)
    {
    document.getElementById("txtToUser").value=id;

    document.getElementById("ajax_response").innerHTML="";
    }
    if(block_id==2)
    {
    document.getElementById("txtToCCUser").value=id;

    document.getElementById("ajax_response_cc").innerHTML="";
    }
    if(block_id==3)
    {
    document.getElementById("txtToBCCUser").value=id;

    document.getElementById("ajax_response_bcc").innerHTML="";
    }
   
}
function b(index,val,block_id)
{
   

 

 
    if(block_id==1)
    {
    var a=document.getElementById("txtToUser").value;
    var l=a.slice(0,index);
    document.getElementById("txtToUser").value=l+val;
    document.getElementById("ajax_response").innerHTML="";
    }
    if(block_id==2)
    {
    var a=document.getElementById("txtToCCUser").value;
    var l=a.slice(0,index);
    document.getElementById("txtToCCUser").value=l+val;
    document.getElementById("ajax_response_cc").innerHTML="";
    }
    if(block_id==3)
    {
    var a=document.getElementById("txtToBCCUser").value;
    var l=a.slice(0,index);
    document.getElementById("txtToBCCUser").value=l+val;
    document.getElementById("ajax_response_bcc").innerHTML="";
    }
 
}

function list_of_groups(groupid)
{
    document.getElementById("txtToUser").value=groupid;
  
}


function groupcheck_match(data) {
 	var response = data;
if(response!=0)
{    
 document.getElementById("txtToUser").value=response;
 return true;
  }
else
  {
  document.getElementById("txtToUser").value="";
  return false;
  }
 }
 
function groupcheck_unmatch (err) {
 	alert ("Error: " + err);
 }


function groups(value)
{
    document.getElementById("groupname").value=value;
}
function desc(value)
{
    document.getElementById("groupdescription").value=value;
}
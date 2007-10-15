function contactCopy(contactid){
	if ( xajax.$('iptcallerid') == null)
	{
		callerid= '';
	}else{
		callerid= xajax.$('iptcallerid').value;
	}

	if ( xajax.$('customerid') == null)
	{
		customerid= 0;
	}else{
		customerid= xajax.$('customerid').value;
	}
	xajax_add(callerid,customerid,contactid);
	return false;
}

function btnConfirmCustomerOnClick(){
//	alert (xajax.$('contactid').value);

	if (xajax.$('customer').readOnly  == true)
	{
//		alert('true');
		xajax_add(xajax.$('iptcallerid').value,0,xajax.$('contactid').value);
	}else{
//		alert('false');
		if (xajax.$('customer').value == '')
		{
			return false;
		}
		xajax_confirmCustomer(xajax.$('customer').value,xajax.$('iptcallerid').value,xajax.$('contactid').value);
	}
}

function openWindow(url){
	window.open(url);
}

function btnConfirmContactOnClick(){
	if (xajax.$('customerid').value == '')
		return false;

	if (xajax.$('contact').value == '')
	{
		return false;
	}


	if (xajax.$('contact').readOnly == true)
	{
		xajax_add(xajax.$('iptcallerid').value,xajax.$('customerid').value);
	}else{
		xajax_confirmContact(xajax.$('contact').value,xajax.$('customerid').value,xajax.$('iptcallerid').value);
	}
}

function getRadioValue(radio)
{
   var RadioValue='';
   for(i=0,len=radio.length;i<len;i++)
   {
       if(radio[i].checked)
       {
       RadioValue = radio[i].value
       }
   }
   return RadioValue;
}

function maximizeWin() {
	if (window.screen) { 
		var aw = screen.availWidth; 

		var ah = screen.availHeight; 

		window.moveTo(0, 0); 

		window.resizeTo(aw, ah); 
	} 
	window.focus();
} 

function titlebar(val)
{
	var msg  = "asterCRM";
	var speed = 500;
	var pos = val;
	if (document.getElementById('callerid').value == "")
	{
		document.title = msg;
		blinkingtitle = window.setTimeout("titlebar("+pos+")",speed);
		return;
	}

	var msg1  = "****** "+document.getElementById('callerid').value+" ******";
	var msg2  = "------- "+document.getElementById('callerid').value+" -------";

	if(pos == 0){
		masg = msg1;
		pos = 1;
	}
	else if(pos == 1){
		masg = msg2;
		pos = 0;
	}

	document.title = masg;
	blinkingtitle = window.setTimeout("titlebar("+pos+")",speed);
}

titlebar(0);

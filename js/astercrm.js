function contactCopy(contactid){
	if ( xajax.$('iptcallerid') == null)
	{
		callerid= '';
	}else{
		callerid= xajax.$('iptcallerid').value;
	}

	if ( xajax.$('customerid') == null)
	{
		customerid= '';
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

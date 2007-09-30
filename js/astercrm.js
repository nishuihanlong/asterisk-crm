function btnConfirmCustomerOnClick(){
//	alert();
//	alert (xajax.$('customer').readOnly);

	if (xajax.$('customer').readOnly  == true)
	{
//		alert('true');
		xajax_add(xajax.$('callerid').value);
	}else{
//		alert('false');
		xajax_confirmCustomer(xajax.$('customer').value,xajax.$('callerid').value);
	}
}

function openWindow(url){
	window.open(url);
}

function btnConfirmContactOnClick(){
	if (xajax.$('customerid').value == '')
		return false;

	if (xajax.$('contact').readOnly == true)
	{
		xajax_add(xajax.$('callerid').value,xajax.$('customerid').value);
	}else{
		xajax_confirmContact(xajax.$('contact').value,xajax.$('customerid').value,xajax.$('callerid').value);
	}
}

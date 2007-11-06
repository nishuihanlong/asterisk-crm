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

	if (typeof document.getElementById('contactid') == '' || typeof document.getElementById('contactid') == 'undefined'){
		contactid = document.getElementById('contactid').value;
	}else{
		contactid = '';
	}

	if (document.f.customer.readOnly  == true)
	{
		xajax_add(xajax.$('iptcallerid').value,0,contactid);
	}else{
		if (document.f.customer.value == '')
		{
			return false;
		}

		xajax_confirmCustomer(document.f.customer.value,xajax.$('iptcallerid').value,contactid);
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
	
	if   (document.getElementById('callerid')   ==   null)
		return;

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

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

titlebar(0);
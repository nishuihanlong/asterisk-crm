var timerShowStatus,timerShowChannelsInfo;
function showStatus(){
	xajax_showStatus(xajax.$('curid').value);
}

function clearHistory(objId){
	document.getElementById(objId).innerHTML = '';
}

function checkOut(channelid){
	xajax_checkOut(channelid);
}

function putCurrentTime(objId,initSec){
	var now=new Date();
	now = new Date(now.getTime() - initSec * 1000);
	if (document.getElementById(objId).value == '')
		document.getElementById(objId).value = now ;//- initSec * 1000;
}

function trim(stringToTrim) {
return stringToTrim.replace(/^\s+|\s+$/g,"");
}

function openWindow(url){
	window.open(url);
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

function hideObj(objname) {
	var obj = document.getElementsByName(objname);

	for(i=0;i<obj.length;i++) {
		obj[i].style.display="none";
	}
}

function showObj(objname) {
	var obj = document.getElementsByName(objname);
	for(i=0;i<obj.length;i++) {
		obj[i].style.display="block";
	}
}

function ckbCreditOnClick(objCkb){
	if (document.getElementById(objCkb.value+'-iptCredit').value == "")
	{
		objCkb.checked = false;
		return false;
	}

	if (objCkb.checked){
		if (confirm("select OK to enable credit limit")){
			document.getElementById(objCkb.value+'-iptCredit').readOnly = true;
			objCkb.checked = true;
			document.getElementById(objCkb.value + "-limitstatus").value = "";
			// reset balance
			calculateBalance(objCkb.value);
		}else{
			objCkb.checked = false;
		}
	}else{
		if (confirm("select OK to disable credit limit")){
			document.getElementById(objCkb.value+'-iptCredit').readOnly = false;
			objCkb.checked = false;
			channel = document.getElementById(objCkb.value+'-channel').value;
			if (channel != ''){
				xajax_setCreditLimit(objCkb.value,channel,0);
			}
		}else{
			objCkb.checked = true;
		}
	}
}


function setStatus(trId,status){
	if (status)
	{
		if (confirm("are you sure to lock booth " + trId + "?"))
		{
			xajax_setStatus(trId,-1);
		}
	}else{
		if (confirm("are you sure to unlock booth " + trId + "?"))
		{
			xajax_setStatus(trId,1);
		}
	}
}

function hangupOnClick(trId){
	if (confirm('are you sure to hang up this call?')){
		//alert(document.getElementById( trId + '-channel').value);
		//return false;
//		"Local/84350822-legb-channel"
		hangup(document.getElementById( trId + '-channel').value);
		hangup(document.getElementById( trId + '-legb-channel').value);
	}
	return false;
}

function removeLocalDiv(divId){
	if (confirm("are you sure to remove this box?"))
	{
		oDiv = document.getElementById(divId + '-divContainer');
		oContainer =  document.getElementById('divMainContainer');
		oContainer.removeChild(oDiv);//
		xajax_removeLocalChannel(divId);
	}
}

function deleteRow(i){
    document.getElementById('tblCallbackTable').deleteRow(i);
}

function addDiv(containerId,divId,creditLimit,num,status,displayname){
	var container = document.getElementById(containerId);

	if (displayname == '')
	{
		displayname = divId;
	}
	//检查是否已经存在该id

	if (document.getElementById(divId + '-divContainer') != null){
		return ;
	}


	var divContainer = document.createElement("div");
	divContainer.className="float";
	divContainer.id = divId + '-divContainer';

	// add title div
	var div = document.createElement("div");
	div.className = "lable";
	if (num != '')
	{
		div.innerHTML += "&nbsp;No." + num + ":" + displayname;
	}else{
		div.innerHTML += '<input type="button" value="D" onclick="removeLocalDiv(\'' + divId + '\');return false;">' + divId;
	}
	div.innerHTML += " <span id=\"" + divId + "-status\"></span>";
	divContainer.appendChild(div);

	// add cdr div
	var div = document.createElement("div");
	div.className = "calllog";
	div.innerHTML += "<table width=\"500\" class=\"calllog\">" +
																"<tbody id=\"" + divId + "-tbody\">" +
																"<tr>" +
																"<th style=\"width:70px;\">Phone</th>" +
																"<th style=\"width:50px;\">Sec</th>" +
																"<th style=\"width:50px;\">Price</th>" +
																"<th style=\"width:100px;\"  nowrap>Start At</th>" +
																"<th style=\"width:100px;\">Rate</th>" +
																"</tr>" +
																"<tr id=\"trTitle\" class=\"curchannel\">" +
																"<td id=\"" + divId + "-phone\">&nbsp;</td>" +
																"<td id=\"" + divId + "-duration\"> </td>" +
																"<td id=\"" + divId + "-price\"> </td>" +
																"<td id=\"" + divId + "-startat\" nowrap> </td>" +
																"<td id=\"" + divId + "-rate\">" +
"<div style=\"display: none;\">" +
"<span id=\"" + divId + "-connectcharge\">-</span> for first <span id=\"" + divId + "-initblock\">-</span> seconds " + 
"<span id=\"" + divId + "-rateinitial\">-</span> per <span id=\"" + divId + "-billingblock\">-</span> seconds " +
"</div>" +
"total: <span id=\"" + divId + "-totalsec\">-</span> seconds" +
																"</td>" +
																"</tr>" +
																"<tr id=\"trTitle-legb\" class=\"curchannel\">" +
																"<td id=\"" + divId + "-legb-phone\">&nbsp;</td>" +
																"<td id=\"" + divId + "-legb-duration\"> </td>" +
																"<td id=\"" + divId + "-legb-price\"> </td>" +
																"<td id=\"" + divId + "-legb-startat\" nowrap> </td>" +
																"<td id=\"" + divId + "-legb-rate\">" +
"<div style=\"display: none;\">" +
"<span id=\"" + divId + "-legb-connectcharge\">-</span> for first <span id=\"" + divId + "-legb-initblock\">-</span> seconds " + 
"<span id=\"" + divId + "-legb-rateinitial\">-</span> per <span id=\"" + divId + "-legb-billingblock\">-</span> seconds " +
"</div>" +
																"</td>" +
																"</tr>" +
																"</tbody>" +
															"</table>" +
															"<form action=\"\" name=\"" + divId + "-form\" id=\"" + divId + "-form\">" +
															"<table width=\"500\" class=\"calllog\">" +
																"<tbody id=\"" + divId + "-calllog-tbody\">" +
																"</tbody>" +
															"</table>" +
															"</form>";

	divContainer.appendChild(div);

	// add unbilled div
	var div = document.createElement("div");
	div.className = "lable";
	div.innerHTML += "&nbsp;&nbsp;U:&nbsp;<span id=\""+divId+"-unbilled\">0</span>";
	div.innerHTML += "&nbsp;&nbsp;B:&nbsp;<span name=\"" + divId + "-balance\" id = \"" + divId + "-balance\" style=\"CURSOR: pointer;\" onclick=\"calculateBalance('" + divId + "')\"></span>";
	divContainer.appendChild(div);

	// add creditlimit div
	var div = document.createElement("div");
	div.className = "lable";
	if (creditLimit == ""){
		div.innerHTML += "<input type=\"checkbox\" id=\"" + divId + "-ckbCredit\" name=\"" + divId + "-ckbCredit\" value=\"" + divId + "\" onclick=\"ckbCreditOnClick(this);\">";
		div.innerHTML += "<span id=\"spanLimit\">Limit</span>: <input id=\"" + divId + "-iptCredit\" name=\"" + divId + "-iptCredit\" type=\"text\" value=\"\" size=\"9\" maxlength=\"7\" >";
	}else{
		div.innerHTML += "<input type=\"checkbox\" id=\"" + divId + "-ckbCredit\" name=\"" + divId + "-ckbCredit\" value=\"" + divId + "\" checked onclick=\"ckbCreditOnClick(this);\">";
		div.innerHTML += "<span id=\"spanLimit\">Limit</span>: <input id=\"" + divId + "-iptCredit\" name=\"" + divId + "-iptCredit\" type=\"text\" value=\"" + creditLimit + "\" size=\"9\" maxlength=\"7\" readonly>";
	}
	divContainer.appendChild(div);

	//add lock div
	var div = document.createElement("div");
	div.className = "lable";
	div.innerHTML += "<input type=\"hidden\" id=\"divList[]\" name=\"divList[]\" value=\"" + divId + "\">";
	if (status == -1){
		div.innerHTML += "<input checked type=\"checkbox\" id=\"" + divId+ "-ckbLock\" name=\"" + divId+ "-ckbLock\"  onclick=\"setStatus('" + divId + "',this.checked);\"><span id=\"" + divId + "-lock\" style=\"background-color: red;\">Lock</span> ";
	}else{
		div.innerHTML += "<input type=\"checkbox\" id=\"" + divId+ "-ckbLock\" name=\"" + divId+ "-ckbLock\" value=\"" + divId + "\" onclick=\"setStatus('" + divId + "',this.checked);\"><span id=\"" + divId + "-lock\">Lock</span> ";
	}

	div.innerHTML += "<input type=\"hidden\" id=\"" + divId + "-channel\" name=\"" + divId + "-channel\" value=''>";
	div.innerHTML += "<input type=\"hidden\" id=\"" + divId + "-legb-channel\" name=\"" + divId + "-legb-channel\" value=''>";
	div.innerHTML += '<input type="hidden" id="' + divId + '-localanswertime" name="' + divId + '-localanswertime" value="">';
	div.innerHTML += '<input type="hidden" id="' + divId + '-legb-localanswertime" name="' + divId + '-localanswertime" value="">';
	div.innerHTML += '<input type="hidden" size="4" id="' + divId + '-billsec" name="' + divId + '-billsec" value="0">';
	div.innerHTML += '<input type="hidden" size="4" id="' + divId + '-legb-billsec" name="' + divId + '-billsec" value="0">';
	div.innerHTML += '<input type="hidden" size="4" id="' + divId + '-limitstatus" name="' + divId + '-limitstatus" value="">';
	div.innerHTML += "&nbsp;&nbsp;<a href=\"?\" onclick=\"hangupOnClick('" + divId + "');return false;\">Hangup</a>";
	div.innerHTML += "&nbsp;&nbsp;<a href=\"?\" onclick=\"btnClearOnClick('" + divId + "');return false;\">Clear</a>";
	div.innerHTML += "&nbsp;&nbsp;<a href=\"?\" onclick=\"btnCDROnClick('" + divId + "');return false;\">Cdr</a>";
	divContainer.appendChild(div);

	container.appendChild(divContainer);
}

function addSimpleDiv(containerId,divId,creditLimit,num,status,displayname){
	var container = document.getElementById(containerId);

	if (displayname == '')
	{
		displayname = divId;
	}
	//检查是否已经存在该id

	if (document.getElementById(divId + '-divContainer') != null){
		return ;
	}


	var divContainer = document.createElement("div");
	//divContainer.className="simpleFloat";
	divContainer.id = divId + '-divContainer';

	// add title div
	var div = document.createElement("div");
	div.className = "lable";
	if (num != '')
	{
		div.innerHTML += "&nbsp;No." + num + ":" + displayname;
	}else{
		div.innerHTML += '<input type="button" value="D" onclick="removeLocalDiv(\'' + divId + '\');return false;">' + divId;
	}
	div.innerHTML += " <span id=\"" + divId + "-status\"></span>";
	divContainer.appendChild(div);

	// add cdr div
	var div = document.createElement("div");
	div.className = "peerstatus";
	div.innerHTML += "<table class=\"peerstatus\" width=\"400\" >" +
																"<tbody id=\"" + divId + "-tbody\">" +
																"<tr>" +
																"<th style=\"width:70px;\">Phone</th>" +
																"<th style=\"width:50px;\">Sec</th>" +
																"<th style=\"width:100px;\"  nowrap>Start At</th>" +
																"</tr>" +
																"<tr id=\"trTitle\" class=\"curchannel\">" +
																"<td id=\"" + divId + "-phone\">&nbsp;</td>" +
																"<td id=\"" + divId + "-duration\"> </td>" +
																"<td id=\"" + divId + "-startat\" nowrap> </td>" +
																"</tr>" +
																"</tbody>" +
															"</table>";

	divContainer.appendChild(div);



	//add lock div
	var div = document.createElement("div");
	div.className = "lable";
	div.innerHTML += "<input type=\"hidden\" id=\"divList[]\" name=\"divList[]\" value=\"" + divId + "\">";
	if (status == -1){
		div.innerHTML += "<input checked type=\"checkbox\" id=\"" + divId+ "-ckbLock\" name=\"" + divId+ "-ckbLock\"  onclick=\"setStatus('" + divId + "',this.checked);\"><span id=\"" + divId + "-lock\" style=\"background-color: red;\">Lock</span> ";
	}else{
		div.innerHTML += "<input type=\"checkbox\" id=\"" + divId+ "-ckbLock\" name=\"" + divId+ "-ckbLock\" value=\"" + divId + "\" onclick=\"setStatus('" + divId + "',this.checked);\"><span id=\"" + divId + "-lock\">Lock</span> ";
	}

	div.innerHTML += "<input type=\"hidden\" id=\"" + divId + "-channel\" name=\"" + divId + "-channel\" value=''>";
	div.innerHTML += "<input type=\"hidden\" id=\"" + divId + "-legb-channel\" name=\"" + divId + "-legb-channel\" value=''>";
	div.innerHTML += '<input type="hidden" id="' + divId + '-localanswertime" name="' + divId + '-localanswertime" value="">';
	div.innerHTML += '<input type="hidden" id="' + divId + '-legb-localanswertime" name="' + divId + '-localanswertime" value="">';
	div.innerHTML += '<input type="hidden" size="4" id="' + divId + '-billsec" name="' + divId + '-billsec" value="0">';
	div.innerHTML += '<input type="hidden" size="4" id="' + divId + '-legb-billsec" name="' + divId + '-billsec" value="0">';
	div.innerHTML += '<input type="hidden" size="4" id="' + divId + '-limitstatus" name="' + divId + '-limitstatus" value="">';
	div.innerHTML += "&nbsp;&nbsp;<a href=\"?\" onclick=\"hangupOnClick('" + divId + "');return false;\">Hangup</a>";
	//div.innerHTML += "&nbsp;&nbsp;<a href=\"?\" onclick=\"btnClearOnClick('" + divId + "');return false;\">Clear</a>";
	//div.innerHTML += "&nbsp;&nbsp;<a href=\"?\" onclick=\"btnCDROnClick('" + divId + "');return false;\">Cdr</a>";
	divContainer.appendChild(div);

	container.appendChild(divContainer);
}

function setCurrency(s){
	isNegative = false;
	s = String(s);
	if (s.indexOf('-') == 0){
		isNegative = true;
		s = s.substring(1);
	}
	if(/[^0-9\.]/.test(s)) return "invalid value";
	s=s.replace(/^(\d*)$/,"$1.");
	s=(s+"00").replace(/(\d*\.\d\d)\d*/,"$1");
	s=s.replace(".",",");
	var re=/(\d)(\d{3},)/;
	while(re.test(s))
		s=s.replace(re,"$1,$2");
	s=s.replace(/,(\d\d)$/,".$1");
	s = s.replace(/^\./,"0.");
	if (isNegative){
		s = "-"+s;
	}
	return s;
}

function calculateBalance(divId){
	credit = document.getElementById(divId + '-iptCredit').value;
	unbilled = Number(document.getElementById(divId + '-unbilled').innerHTML);
	if (document.getElementById(divId+'-ckbCredit').checked && document.getElementById('creditlimittype').value == 'balance' && (unbilled - credit)  >= -0.001 )
	{
		alert('the credit should be greater than unbilled');
		document.getElementById(divId+'-ckbCredit').checked = false;
		document.getElementById(divId+'-iptCredit').readOnly = false;
	}

	if (credit == ''){
		credit = 0;
	}else{
		credit = Number(credit);
	}

	document.getElementById(divId + '-balance').innerHTML = setCurrency(parseInt((credit - unbilled)*100)/100);
}

function removeTr(divId){
	tbody = document.getElementById(divId + '-calllog-tbody');
	for (i = tbody.rows.length; i>0 ; i-- )
	{
		tbody.deleteRow(0); 
	}
}

function btnClearOnClick(divId){
	if (!confirm('are you sure to clear this booth?'))
	{
		return false;
	}
	form = document.getElementById(divId + "-form");
	xajax_checkOut(xajax.getFormValues(divId + "-form"),divId);
}

function btnCDROnClick(divId){
	window.open("checkout.php?peer=" + divId ,"CheckOutPage");
}

function hangup(channel){
	if (channel != ''){
		xajax_hangup(channel);
	}
}

function setBillsec(trId){
	var answertime = trim(document.getElementById(trId + '-localanswertime').value);
	var now = new Date();
	var billsec = 0;
	if (answertime != ''){
		answertime = new Date(answertime);
		billsec = parseInt((now.getTime() - answertime.getTime()) / 1000 + 0.9999);
		document.getElementById(trId + '-billsec').value = billsec;
		hours = parseInt(billsec/3600);
		minutes =   parseInt( (billsec - hours*3600)/60);
		seconds = billsec - hours * 3600 - minutes * 60
		document.getElementById(trId + '-duration').innerHTML = hours + ':' + minutes + ':' + seconds;
	}
	return billsec;
}

function checkHangup(){
//	setTimeout("checkHangup()", 900);
//	return;
	oDivList = document.getElementsByName("divList[]");
	for(i=0;i<oDivList.length;i++) {
		trId = oDivList[i].value;
		channel = document.getElementById(trId + '-channel').value;

		//if (odivList[i].checked){	// locked
		//	hangup(channel);
		//	document.getElementById(trId + '-lock').style.backgroundColor="red";
		//}else{
		//	document.getElementById(trId + '-lock').style.backgroundColor="";
		//}
	
		if (channel != ''){
			// check if set credit limit
			if (document.getElementById(trId + "-ckbCredit").checked){
				if (document.getElementById(trId + "-limitstatus").value == ""){
						document.getElementById(trId + "-limitstatus").value = "setting";
						if (document.getElementById("creditlimittype").value == 'balance')
						{
							xajax_setCreditLimit(trId,channel,document.getElementById(trId + "-balance").innerHTML);
						}else{
							xajax_setCreditLimit(trId,channel,document.getElementById(trId + "-iptCredit").value);
						}
						//alert("setting");
				}
			}else{

			}
			// set credit limit
		}

//		oCkbCredit = document.getElementById(trId + "-ckbCredit");
		var billsec = setBillsec(trId);
		var legbBillsec = setBillsec(trId + '-legb');
//
//		//setPrice(trId,billsec);
//		//setPrice(trId + '-legb',legbBillsec);
//
//		var rateinitial = Number(document.getElementById(trId + '-rateinitial').innerHTML);
//		var initblock = Number(document.getElementById(trId + '-initblock').innerHTML);
//		var billingblock = Number(document.getElementById(trId + '-billingblock').innerHTML);
//		var connectcharge = Number(document.getElementById(trId + '-connectcharge').innerHTML);
//
//		if (rateinitial){
//
//			if (oCkbCredit.checked){
//				calculateBalance(trId);
//				var balance = document.getElementById(trId + '-balance').innerHTML;
//
//				balance = balance - connectcharge + 0.0001;
//				if (balance < 0){
//					hangup(channel);
//				}
//
//				var limitsec = 0;
//
//				limitsec = initblock + parseInt(balance / (billingblock * rateinitial/60))*billingblock;
//
//				if ( billsec > limitsec - 1 ){
//					hangup(channel);
//				}
//				document.getElementById(trId + '-totalsec').innerHTML = limitsec;
//			}
//		}
	}
	setTimeout("checkHangup()", 900);
}

function clearCurchannel(divId){
	//Local/84754138-legb-localanswertime
	document.getElementById(divId + '-phone').innerHTML = '&nbsp;';
	document.getElementById(divId + '-startat').innerHTML = '';
	document.getElementById(divId + '-duration').innerHTML = '';
	document.getElementById(divId + '-rateinitial').innerHTML = '-';
	document.getElementById(divId + '-initblock').innerHTML = '-';
	document.getElementById(divId + '-billingblock').innerHTML = '-';
	document.getElementById(divId + '-connectcharge').innerHTML = '-';
	document.getElementById(divId + '-channel').value = '';
	document.getElementById(divId + '-price').innerHTML = '';
	document.getElementById(divId + '-billsec').value = 0;
	document.getElementById(divId + '-localanswertime').value = '';
	document.getElementById(divId + '-totalsec').innerHTML = '-';
	document.getElementById(divId + '-limitstatus').value = '';
}

function appendTr(tbodyId,aryValues){
	var tbody = document.getElementById(tbodyId);
    var tr = document.createElement("tr");

	// caller id
    var td = document.createElement("td");
	td.innerHTML = trim(aryValues["dst"]);
	td.style.width = "80px";
	tr.appendChild(td);
	
 	// duration
   var td = document.createElement("td");
	var hours = parseInt(aryValues["billsec"]/3600);
	var minutes = parseInt( (aryValues["billsec"] - hours*3600)/60);
	var seconds = aryValues["billsec"] - hours * 3600 - minutes * 60
	td.innerHTML = hours + ':' + minutes + ':' + seconds;

	td.style.width = "20px";
	tr.appendChild(td);

 	// price
   var td = document.createElement("td");
	td.innerHTML = trim(aryValues["price"]);
	td.style.width = "20px";
	tr.appendChild(td);


 	// start at
   var td = document.createElement("td");
	td.innerHTML = trim(aryValues["startat"]);
	td.style.width = "90px";
	tr.appendChild(td);

 	// rate
   var td = document.createElement("td");
	td.innerHTML = trim(aryValues["rate"]) + "<input type=\"hidden\" id=\"cdrid[]\" name=\"cdrid[]\" value=\"" + aryValues["id"] + "\">";
	td.style.width = "150px";
	tr.appendChild(td);

	tbody.appendChild(tr);
}


function invite(){
	src = trim(xajax.$('iptLegB').value);
	dest = trim(xajax.$('iptLegA').value);
	creditLimit = trim(xajax.$('creditLimit').value);

	if (src == '' || dest == '')
		return false;

	trId = "Local/" + src;
	//check if legB div exsit
	if (document.getElementById(trId + '-divContainer') == null){
		addDiv("divMainContainer",trId,creditLimit,0);
		xajax_addUnbilled(src,dest);
	} else {
		if (creditLimit != ''){
			document.getElementById(trId + '-ckbCredit').checked = true;
			document.getElementById(trId + '-iptCredit').value = creditLimit;
			document.getElementById(trId + '-balance').innerHTML = creditLimit;
			document.getElementById(trId + '-iptCredit').readOnly = true;
		} else {
			document.getElementById(trId + '-ckbCredit').checked = false;
			document.getElementById(trId + '-iptCredit').value = '';
			document.getElementById(trId + '-balance').innerHTML = '';
			document.getElementById(trId + '-iptCredit').readOnly = false;
		}
	}
	//			alert(document.getElementById(trId));
//			alert(trId);
	//legB

	xajax_invite(src,dest,creditLimit);
}
<?php
/*******************************************************************************
* checkout.php
* 结帐

* Function Desc

* javascript function:		

* Revision asterCC 0.01  2007/11/21 17:55:00  last modified by solo
* Desc: page created
********************************************************************************/

require_once('checkout.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<meta http-equiv="Content-Language" content="utf-8" />
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			
function init(){
	curpeer = document.getElementById("hidCurpeer").value;
	xajax_init(curpeer);
	listCDR();
}

function  addOption(objId,optionVal,optionText)  {
	objSelect = document.getElementById(objId);
	var _o = document.createElement("OPTION");
	_o.text = optionText;
	_o.value = optionVal;
//	alert(objSelect.length);
	objSelect.options.add(_o);
} 

function listCDR(){
	xajax_listCDR(xajax.getFormValues("frmFilter"));
}

function ckbOnClick(objCkb){
	trId = "tr-" + objCkb.value;
	oTotal = document.getElementById('spanTotal');
	oProfit = document.getElementById('spanCallshopCost');
	oPrice = document.getElementById("price-" + objCkb.value) ;
	oCallshop = document.getElementById("callshop-" + objCkb.value) ;

	total = Float02(oTotal.innerHTML);
	profit = Float02(oProfit.innerHTML);

	price = Float02(oPrice.value);
	callshop = Float02(oCallshop.value);

	if (objCkb.checked){
		document.getElementById(trId).style.backgroundColor="#eeeeee";
		total = total + price ;
		profit = profit + callshop;
	}else{
		document.getElementById(trId).style.backgroundColor="#ffffff";
		total = total - price ;
		profit = profit - callshop;
	}
	oTotal.innerHTML = Float02(total);
	oProfit.innerHTML = Float02(profit);

	currency = setCurrency(String(Float02(total)));
	document.getElementById('spanCurrencyTotal').innerHTML = currency;

	currency = setCurrency(String(Float02(profit)));
	document.getElementById('spanCurrencyCallshopCost').innerHTML = currency;
}

function Float02(val)
{
    return parseInt(val * 100 + 0.1)/100;
}

function ckbAllOnClick(objCkb){
	var ockb = document.getElementsByName('ckb[]');
	for(i=0;i<ockb.length;i++) {
		if (ockb[i].checked != objCkb.checked){
			ockb[i].checked = objCkb.checked;
			ckbOnClick(ockb[i]);
		}
	}

	var ockb = document.getElementsByName('ckbAll[]');
	for(i=0;i<ockb.length;i++) {
		ockb[i].checked = objCkb.checked;
	}
}

	//-->
		</SCRIPT>

		<script language="JavaScript" src="js/astercrm.js"></script>

		<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>

	</head>

	<body onload="init();">
		<div id="divNav"></div>
		<br>
		<form name="frmFilter" id="frmFilter" method="post">
		<div>
			<select id="sltBooth" name="sltBooth" onchange="listCDR();">
				<option value="">All</option>
			</select>
			
			From: <input type="text" name="sdate" size="20" value="<?echo date("Y-m-d H:i:s",time()-86400);?>">
			To:<input type="text" name="edate" size="20" value="<?echo date("Y-m-d H:i:s",time());?>">
			<select id="sltDate" name="sltDate" onchange="">
				<option value="month">this month</option>
				<option value="week">this week</option>
				<option value="day">today</option>
			</select>
					<input type="checkbox" value="detail" id="ckbDetail" name="ckbDetail">List Detail
			<input type="button" onclick="listCDR();return false;" value="List">
		</div>
		</form>
		<div id="divUnbilledList" name="divUnbilledList">
		</div>
		
		<center>
		<div>
			<div style="display:none;">Total: <span id="spanTotal" name="spanTotal">0</span> Callshop Cost: <span id="spanCallshopCost" name="spanCallshopCost">0</span></div>
			Total: <span id="spanCurrencyTotal" name="spanCurrencyTotal">0</span><br />
			Callshop Cost: <span id="spanCurrencyCallshopCost" name="spanCurrencyCallshopCost">0</span><br />
			<input type="button" value="Check Out" name="btnCheckOut" id="btnCheckOut" onclick="xajax_checkOut(xajax.getFormValues('f'));">
		</div>
		</center>

		<input type="hidden" id="hidCurpeer" name="hidCurpeer" value="<?echo $_REQUEST['peer']?>">
		<div id="divCopyright"></div>
	</body>
</html>
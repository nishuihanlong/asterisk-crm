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
		var trId = "tr-" + objCkb.value;

		var oTotal = document.getElementById('spanTotal');
		var oCallshopCost = document.getElementById('spanCallshopCost');
		var oResellerCost = document.getElementById('spanResellerCost');

		var oPrice = document.getElementById("price-" + objCkb.value) ;
		var oCallshop = document.getElementById("callshop-" + objCkb.value) ;
		var oReseller = document.getElementById("reseller-" + objCkb.value) ;


		var total = Float02(oTotal.innerHTML);
		var callshopcost = Float02(oCallshopCost.innerHTML);
		var resellercost = Float02(oResellerCost.innerHTML);

		var price  = Float02(oPrice.value);
		var callshop = Float02(oCallshop.value);
		var reseller = Float02(oReseller.value);

		if (objCkb.checked){
			document.getElementById(trId).style.backgroundColor="#eeeeee";
			total = total + price ;
			callshopcost = callshopcost + callshop;
			resellercost = resellercost + reseller;
		}else{
			document.getElementById(trId).style.backgroundColor="#ffffff";
			total = total - price ;
			callshopcost = callshopcost - callshop;
			resellercost = resellercost - reseller;
		}
		oTotal.innerHTML = Float02(total);
		oCallshopCost.innerHTML = Float02(callshopcost);
		oResellerCost.innerHTML = Float02(resellercost);

		var currency;

		currency = setCurrency(String(Float02(total)));
		document.getElementById('spanCurrencyTotal').innerHTML = currency;

		currency = setCurrency(String(Float02(callshopcost)));
		document.getElementById('spanCurrencyCallshopCost').innerHTML = currency;

		currency = setCurrency(String(Float02(resellercost)));
		document.getElementById('spanCurrencyResellerCost').innerHTML = currency;
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

	function setGroup(){
		var resellerid = xajax.$('resellerid').value;
		if (resellerid == '')
			return;
		//清空 groupid
		document.getElementById("groupid").options.length = 0;
		document.getElementById("sltBooth").options.length = 0;

		if (resellerid != 0)
			xajax_setGroup(resellerid);
	}

	function setClid(){
		var groupid = xajax.$('groupid').value;
		if (groupid == '')
			return;
		//清空 clid
		document.getElementById("sltBooth").options.length = 0;
		if (groupid != 0)
			xajax_setClid(groupid);
	}

	//-->
		</SCRIPT>

		<script language="JavaScript" src="js/astercrm.js"></script>
		<script language="JavaScript" src="js/dhtmlgoodies_calendar.js"></script>

		<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>
		<LINK href="js/dhtmlgoodies_calendar.css" type=text/css rel=stylesheet>

	</head>

	<body onload="init();">
		<div id="divNav"></div>
		<br>
		<div id="divLimitStatus" name="divLimitStatus">
		</div>

		<form name="frmFilter" id="frmFilter" method="post">
		<div>
			<SELECT id="resellerid" name="resellerid" onchange="setGroup();">
			</SELECT>

			<SELECT id="groupid" name="groupid" onchange="setClid();">
			</SELECT>

			<select id="sltBooth" name="sltBooth" onchange="listCDR();">
			</select>
			<br>
			
			From: <input type="text" name="sdate" size="20" value="<?echo date("Y-m-d H:i",time()-86400);?>">
			<INPUT onclick="displayCalendar(document.forms[0].sdate,'yyyy-mm-dd hh:ii',this,true)" type="button" value="Cal">
			To:<input type="text" name="edate" size="20" value="<?echo date("Y-m-d H:i",time());?>">
			<INPUT onclick="displayCalendar(document.forms[0].edate,'yyyy-mm-dd hh:ii',this,true)" type="button" value="Cal">
			<input type="checkbox" value="detail" id="ckbDetail" name="ckbDetail">List Detail
			<br>
			<input type="button" onclick="listCDR();return false;" value="List">
		</div>
		</form>

		<div id="divUnbilledList" name="divUnbilledList">
		</div>
		
		<center>
		<div>
			<div style="display:none;">
				Total: <span id="spanTotal" name="spanTotal">0</span> 
				Callshop Cost: <span id="spanCallshopCost" name="spanCallshopCost">0</span>
				Reseller Cost: <span id="spanResellerCost" name="spanResellerCost">0</span>
			</div>
			Total: <span id="spanCurrencyTotal" name="spanCurrencyTotal">0</span><br />
			Callshop Cost: <span id="spanCurrencyCallshopCost" name="spanCurrencyCallshopCost">0</span><br />
			Reseller Cost: <span id="spanCurrencyResellerCost" name="spanCurrencyResellerCost">0</span><br />
			<input type="button" value="Check Out" name="btnCheckOut" id="btnCheckOut" onclick="xajax_checkOut(xajax.getFormValues('f'));">
		</div>
		</center>

		<input type="hidden" id="hidCurpeer" name="hidCurpeer" value="<?echo $_REQUEST['peer']?>">
		<div id="divCopyright"></div>
	</body>
</html>
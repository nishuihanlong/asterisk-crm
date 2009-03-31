<?php
/*******************************************************************************
* report.php
* 座席通话统计

* Function Desc

* javascript function:		

* Revision asterCC 0.01  2007/11/21 17:55:00  last modified by solo
* Desc: page created
********************************************************************************/

require_once('report.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<meta http-equiv="Content-Language" content="utf-8" />
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			
	function init(){
		xajax_init();
		listReport();
	}

	function  addOption(objId,optionVal,optionText,optionSelected)  {
		objSelect = document.getElementById(objId);
		var _o = document.createElement("OPTION");
		_o.text = optionText;
		_o.value = optionVal;
		_o.selected = optionSelected;
	//	alert(objSelect.length);
		objSelect.options.add(_o);
	} 

	function listReport(){
		//document.getElementById("divMsg").style.visibility="visible";
		xajax_listReport(xajax.getFormValues("frmFilter"));
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

	function setAccount(){
		var groupid = xajax.$('groupid').value;
		if (groupid == '')
			return;
		//清空 groupid
		document.getElementById("accountid").options.length = 0;

		if (groupid != 0)
			xajax_setAccount(groupid);
	}

	//-->
		</SCRIPT>

		<script language="JavaScript" src="js/astercrm.js"></script>
		<script language="JavaScript" src="js/dhtmlgoodies_calendar.js"></script>
        <script type="text/javascript">
</script>

       
		<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>
		<LINK href="js/dhtmlgoodies_calendar.css" type=text/css rel=stylesheet>

	</head>

	<body onload="init();" id="report">
		<div id="divNav"></div>
		<br>
		<form name="frmFilter" id="frmFilter" method="post">
		<br>
		<div style="left: 20px;" >&nbsp;
			<SELECT id="groupid" name="groupid" onchange="setAccount();listReport();">
			</SELECT>

			<SELECT id="accountid" name="accountid" onchange="listReport();">
			</SELECT>

			<a href="javascript:void(null);" onclick="xajax_speedDate('td')"><?echo $locate->Translate("Today")?></a>&nbsp;|
			<a href="javascript:void(null);" onclick="xajax_speedDate('tw')"><?echo $locate->Translate("This week")?></a>&nbsp;|
			<a href="javascript:void(null);" onclick="xajax_speedDate('tm')"><?echo $locate->Translate("This month")?></a>&nbsp;|
			<a href="javascript:void(null);" onclick="xajax_speedDate('l3m')"><?echo $locate->Translate("Last 3 months")?></a>&nbsp;|
			<a href="javascript:void(null);" onclick="xajax_speedDate('ty')"><?echo $locate->Translate("This year")?></a>&nbsp;|
			<a href="javascript:void(null);" onclick="xajax_speedDate('ly')"><?echo $locate->Translate("Last year")?></a>
			<br />&nbsp;
			<?echo $locate->Translate("From")?>: <input type="text" name="sdate" id="sdate" size="20" value="<?echo date("Y-m-d H:i",time()-86400);?>" readonly>
			<INPUT onclick="displayCalendar(document.forms[0].sdate,'yyyy-mm-dd hh:ii',this,true)" type="button" value="<?echo $locate->Translate("Cal")?>">
			<?echo $locate->Translate("To")?>:<input type="text" name="edate" id="edate" size="20" value="<?echo date("Y-m-d H:i",time());?>" readonly>
			<INPUT onclick="displayCalendar(document.forms[0].edate,'yyyy-mm-dd hh:ii',this,true)" type="button" value="<?echo $locate->Translate("Cal")?>">
			<SELECT id="listType" name="listType">
				<OPTION value="none"><?echo $locate->Translate("None")?></OPTION>
				<!--<OPTION value="listdetail"><?echo $locate->Translate("List Detail")?></OPTION>-->
				<OPTION value="sumyear"><?echo $locate->Translate("Sum By Year")?></OPTION>
				<OPTION value="summonth"><?echo $locate->Translate("Sum By Month")?></OPTION>
				<OPTION value="sumday"><?echo $locate->Translate("Sum By Day")?></OPTION>
				<OPTION value="sumhour"><?echo $locate->Translate("Sum By Hour")?></OPTION>
				<OPTION value="sumdest"><?echo $locate->Translate("Sum By Destination")?></OPTION>
				<OPTION value="sumgroup"><?echo $locate->Translate("Sum By Group")?></OPTION>
			</SELECT>
			
			<input type="radio" value="text" name="reporttype" checked><?echo $locate->Translate("Text")?>&nbsp;
			<!--<input type="radio" value="flash" name="reporttype"><?echo $locate->Translate("Flash")?>-->
			<input type="button" onclick="listReport();return false;" value="<?echo $locate->Translate("List")?>">			
		</div>
		</form>
		<br>
		<div id="divGeneralList" style="float:center;"> </div>
		<center>
			<br>			
			<div style="overflow:hidden; zoom:1; margin:auto; width:830px;">
				<div class="jin-fl"><div id='num_chart'></div></div>
				<div class="jin-fl"><div id='time_chart'></div></div>
				<div class="jin-fl"><div id='total_chart'></div></div>
				<div class="jin-fl"><div id='group_chart'></div></div>
				<div class="jin-fl"><div id='cost_chart'></div></div>
				<div class="jin-fl"><div id='gain_chart'></div></div>
			</div>
<!--			<div style="display:none;">
				<?echo $locate->Translate("Amount")?>: <span id="spanTotal" name="spanTotal">0</span> 
				<?echo $locate->Translate("Callshop Cost")?>: <span id="spanCallshopCost" name="spanCallshopCost">0</span>
				<?echo $locate->Translate("Reseller Cost")?>: <span id="spanResellerCost" name="spanResellerCost">0</span>
			</div>
			<?echo $locate->Translate("Amount")?>: <span id="spanCurrencyTotal" name="spanCurrencyTotal">0</span><br />
			<?echo $locate->Translate("Callshop Cost")?>: <span id="spanCurrencyCallshopCost" name="spanCurrencyCallshopCost">0</span><br />
			<?echo $locate->Translate("Reseller Cost")?>: <span id="spanCurrencyResellerCost" name="spanCurrencyResellerCost">0</span><br />
			<input type="button" value="Check Out" name="btnCheckOut" id="btnCheckOut" onclick="xajax_checkOut(xajax.getFormValues('f'));">
		</div>
		

	<div id="divMsg">
		Processing, please wait ...
	</div>
	-->
	</center>
	<div id="divCopyright"></div>
	</body>
</html>
<?php
/*******************************************************************************
* checkout.server.php

* Function Desc
	show sip status and active channels

* 功能描述
	提供SIP分机状态信息和正在进行的通道

* Function Desc

	showGrid
	init				初始化页面元素
	showStatus			显示sip分机状态信息
	showChannelsInfo	显示激活的通道信息

* Revision 0.045  2007/10/18 15:38:00  last modified by solo
* Desc: comment added

********************************************************************************/
require_once ("checkout.common.php");
require_once ("db_connect.php");
require_once ('include/asterevent.class.php');
require_once ('include/asterisk.class.php');
require_once ('include/astercrm.class.php');
require_once ('include/common.class.php');

function init($curpeer){
	$objResponse = new xajaxResponse();
	$peers = array();
	if ($_SESSION['curuser']['usertype'] == 'admin'){
		// set all reseller first
		$reseller = astercrm::getAll('resellergroup');
		$objResponse->addScript("addOption('resellerid','"."0"."','"."All"."');");
		while	($reseller->fetchInto($row)){
			$objResponse->addScript("addOption('resellerid','".$row['id']."','".$row['resellername']."');");
		}

	}else if ($_SESSION['curuser']['usertype'] == 'reseller'){
		// set one reseller
		$objResponse->addScript("addOption('resellerid','".$_SESSION['curuser']['resellerid']."','".""."');");

		// set all group
		$group = astercrm::getAll('accountgroup','resellerid',$_SESSION['curuser']['resellerid']);
		$objResponse->addScript("addOption('groupid','"."0"."','"."All"."');");
		while	($group->fetchInto($row)){
			$objResponse->addScript("addOption('groupid','".$row['id']."','".$row['groupname']."');");
		}

		// get limit status and creditlimit
		$reseller = astercc::readRecord("resellergroup","id",$_SESSION['curuser']['resellerid']);
		if ($reseller){
			$html = "Limit Type:".$reseller['limittype']."(".$reseller['creditlimit'].")";
			$objResponse->addAssign("divLimitStatus","innerHTML",$html);
		}

	}else{
		$objResponse->addScript("addOption('resellerid','".$_SESSION['curuser']['resellerid']."','".""."');");
		$objResponse->addScript("addOption('groupid','".$_SESSION['curuser']['groupid']."','".""."');");

		$clid = astercrm::getAll('clid','groupid',$_SESSION['curuser']['groupid']);
		$objResponse->addScript("addOption('sltBooth','"."0"."','"."All"."');");

		while	($clid->fetchInto($row)){
			if ($curpeer == $row['clid'])
				$objResponse->addScript("addOption('sltBooth','".$row['clid']."','".$row['clid']."',true);");
			else
				$objResponse->addScript("addOption('sltBooth','".$row['clid']."','".$row['clid']."');");
		}
		$objResponse->addScript("addOption('sltBooth','-1','callback');");
		// get limit status and creditlimit
		$accountgroup = astercc::readRecord("accountgroup","id",$_SESSION['curuser']['groupid']);
		if ($accountgroup){
			$html = "Limit Type:".$accountgroup['limittype']."(".$accountgroup['creditlimit'].")";
			$objResponse->addAssign("divLimitStatus","innerHTML",$html);
		}
	}


	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));

	return $objResponse;
}

function setGroup($resellerid){
	global $locate;
	$objResponse = new xajaxResponse();
	$res = astercrm::getAll("accountgroup",'resellerid',$resellerid);
	//添加option
	$objResponse->addScript("addOption('groupid','"."0"."','"."All"."');");
	while ($res->fetchInto($row)) {
		$objResponse->addScript("addOption('groupid','".$row['id']."','".$row['groupname']."');");
	}
	return $objResponse;
}

function setClid($groupid){
	global $locate;
	$objResponse = new xajaxResponse();
	$res = astercrm::getAll("clid",'groupid',$groupid);
	//添加option
	$objResponse->addScript("addOption('sltBooth','"."0"."','"."All"."');");
	while ($res->fetchInto($row)) {
		$objResponse->addScript("addOption('sltBooth','".$row['clid']."','".$row['clid']."');");
	}
	$objResponse->addScript("addOption('sltBooth','-1','callback');");
	return $objResponse;
}

function listCDR($aFormValues){
	$objResponse = new xajaxResponse();
	if ($aFormValues['sltBooth'] == '' && $aFormValues['hidCurpeer'] != ''){
		$aFormValues['sltBooth'] = $aFormValues['hidCurpeer'];
	}
	if ($aFormValues['ckbDetail'] == ""){
		$res = astercc::readReport($aFormValues['resellerid'], $aFormValues['groupid'], $aFormValues['sltBooth'], $aFormValues['sdate'],$aFormValues['edate']);
		if ($res->fetchInto($myreport)){
			if ($_SESSION['curuser']['usertype'] == 'admin' || $_SESSION['curuser']['usertype'] == 'reseller'){
				$html .= "Amount: ".$myreport['credit']."<br>";
				$html .= "Callshop ".$myreport['callshopcredit']."<br>";
				$html .= "Reseller Cost: ".$myreport['resellercredit']."<br>";
			}else if ($_SESSION['curuser']['usertype'] == 'groupadmin' || $_SESSION['curuser']['usertype'] == 'operator'){
				$html .= "Amount: ".$myreport['credit']."<br>";
				$html .= "Callshop ".$myreport['callshopcredit']."<br>";
			}
		}
		$objResponse->addAssign("divUnbilledList","innerHTML",$html);
		return $objResponse;
	}


	//$records = astercc::readAll($aFormValues['sltBooth'], -1,$aFormValues['sdate'],$aFormValues['edate']);
	
	$records = astercc::readAll($aFormValues['resellerid'], $aFormValues['groupid'], $aFormValues['sltBooth'], $aFormValues['sdate'],$aFormValues['edate']);

	$html .= '<form action="" name="f" id="f">';
	$html .= '<table width="99%">';
	$html .= '<tr>
			<td width="60"></td>
			<td width="120">calldate</td>
			<td width="120">clid</td>
			<td width="120">dst</td>
			<td width="70">duration</td>
			<td width="90">disposition</td>
			<td width="70">billsec</td>
			<td width="160">destination</td>
			<td width="360">rate</td>
 			<td width="120">price</td>
 			<td width="70">status</td>
			</tr>';
	$html .= '<tr>
			<td width="60">
				<input type="checkbox" onclick="ckbAllOnClick(this);" id="ckbAll[]" name="ckbAll[]">All
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
 			<td></td>
 			<td></td>
			</tr>';
	
	while	($records->fetchInto($mycdr)){
		$price = '';
		$ratedesc = '';
		$ratedesc = astercc::readRateDesc($mycdr['memo']);

		$callshop_cost = $mycdr['callshopcredit'];
		$reseller_cost = $mycdr['resellercredit'];

		if ($_SESSION['curuser']['usertype'] == 'operator') $callshop_cost = 0;
		if ($_SESSION['curuser']['usertype'] == 'groupadmin') $reseller_cost = 0;

		if ($aFormValues['ckbDetail'] == ""){
			// only get amount
			$calls ++;
			$amount += $mycdr['credit'];
			$total_callshop_cost += $callshop_cost;
			$total_reseller_cost += $reseller_cost;
		}

		
		$html .= '	<tr align="left" id="tr-'.$mycdr['id'].'">
						<td align="right">
							<input type="checkbox" id="ckb[]" name="ckb[]" value="'.$mycdr['id'].'" onclick="ckbOnClick(this);">
							<input type="hidden" id="price-'.$mycdr['id'].'" name="price-'.$mycdr['id'].'" value="'.$mycdr['credit'].'">
							<input type="hidden" id="callshop-'.$mycdr['id'].'" name="callshop-'.$mycdr['id'].'" value="'.$callshop_cost.'">
							<input type="hidden" id="reseller-'.$mycdr['id'].'" name="reseller-'.$mycdr['id'].'" value="'.$reseller_cost.'">
						</td>
						<td>'.$mycdr['calldate'].'</td>
						<td>'.$mycdr['src'].'</td>
						<td>'.$mycdr['dst'].'</td>
						<td>'.$mycdr['duration'].'</td>
						<td>'.$mycdr['disposition'].'</td>
						<td>'.$mycdr['billsec'].'</td>
						<td>'.$rate['destination'].'</td>
						<td>'.$ratedesc.'</td>
						<td>'.$mycdr['credit'].'<br>'.'('.$callshop_cost.')'.'<br>'.'('.$reseller_cost.')</td>';
		if ($peer == '-1'){
			if ($mycdr['dst'] == $mycdr['src']){
				//lega
				$addon = ' [lega]';
			}else{
				//legb
				$addon = ' [legb]';
			}
		}

		if ($mycdr['userfield'] == 'UNBILLED')
			$html .='<td bgcolor="red">'.$mycdr['userfield'].$addon.'</td>';
		else
			$html .='<td>'.$mycdr['userfield'].$addon.'</td>';

		$html .= '</tr>
					<tr bgcolor="gray">
						<td colspan="11" height="1"></td>
					</tr>
				';
	}
	$html .= '<tr>
			<td width="60">
				<input type="checkbox" onclick="ckbAllOnClick(this);" id="ckbAll[]" name="ckbAll[]">All
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
 			<td></td>
 			<td></td>
 			<td></td>
			</tr>';
	$html .= '</table>';
	$html .= '</form>';
	if ($aFormValues['ckbDetail'] == ""){
		$html = "Calls: $calls"."<br>";
		$html .= "Amount: $amount"."<br>";
		$html .= "Callshop Cost: $total_callshop_cost"."<br>";
		$html .= "Reseller Cost: $total_reseller_cost"."<br>";
	}

	$objResponse->addAssign("divUnbilledList","innerHTML",$html);
	$objResponse->addAssign("spanTotal","innerHTML",0);
	return $objResponse;
}

function checkOut($aFormValues){
	$objResponse = new xajaxResponse();
	if ($aFormValues['ckb']){
		foreach ($aFormValues['ckb'] as $id){
			$res =  astercc::setBilled($id);
		}
		$objResponse->addScript("listCDR();");
	}
	return $objResponse;
}

$xajax->processRequests();
?>

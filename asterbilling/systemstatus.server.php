<?php
/*******************************************************************************
********************************************************************************/
require_once ("systemstatus.common.php");
require_once ("db_connect.php");
require_once ('include/xajaxGrid.inc.php');
require_once ('include/asterevent.class.php');
require_once ('include/asterisk.class.php');
require_once ('include/astercrm.class.php');
require_once ('include/common.class.php');

/**
*  initialize page elements
*
*/

function init(){
	global $locate,$config;
	$objResponse = new xajaxResponse();

	$myAsterisk = new Asterisk();
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		$objResponse->addAssign("AMIStatudDiv", "innerHTML", $locate->Translate("AMI connection failed"));
	}

	$group_row = astercrm::getRecord($_SESSION['curuser']['groupid'],'accountgroup');

	if ( $group_row['grouplogo'] != '' && $group_row['grouplogostatus'] ){
		$logoPath = $config['system']['upload_file_path'].'/callshoplogo/'.$group_row['grouplogo'];
		if (is_file($logoPath)){
			$titleHtml = '<img src="'.$logoPath.'" style="float:left;" width="80" height="80">';
		}
	}
	if ( $group_row['grouptitle'] != ''){
		$titleHtml .= '<h1 style="padding: 0 0 0 0;position: relative;font-size: 16pt;">'.$group_row['grouptitle'].'</h1>';
	}
	if ( $group_row['grouptagline'] != ''){
		$titleHtml .= '<h2 style="padding: 0 0 0 0;position: relative;font-size: 11pt;color: #FJDSKB;">'.$group_row['grouptagline'].'</h2>';
	}
	if (isset($titleHtml)){
		$titleHtml .= '<div style="position:absolute;top:85px;left:0px;width:800px"><hr color="#F1F1F1"></div>';
		$objResponse->addAssign("divTitle", "innerHTML", $titleHtml);
	}else{
		$objResponse->addAssign("divTitle", "style.height", '0px');
		$objResponse->addAssign("divMain", "style.top", '0px');
	}

	$_SESSION['status'] = array();

	# 获得当前的channel
	$curchannels = array();
	$curchannels = astercc::checkPeerStatus($_SESSION['curuser']['groupid']);

	$peers = $_SESSION['curuser']['extensions'];

	foreach ($peers as $peer){
		$i++;
		// check if the booth is locked
		$clid = astercc::readRecord('clid','clid',$peer);
		// read booth display
		//$display = astercc::readField('clid','display','clid',$peer);
		$status = $clid['status'];
		$display = $clid['display'];
		if ($curchannels[$peer] && $curchannels[$peer]['creditlimit'] > 0){
			$objResponse->addScript('addDiv("divMainContainer","'.$peer.'","'.$curchannels[$peer]['creditlimit'].'","'.$i.'","'.$status.'","'.$display.'")');
		}else{
			$objResponse->addScript('addDiv("divMainContainer","'.$peer.'","","'.$i.'","'.$status.'","'.$display.'")');
		}
		$objResponse->addScript('xajax_addUnbilled("'.$peer.'");');
	}
if (!isset($_SESSION['callbacks']))
	$_SESSION['callbacks'] = array();

//print_r($_SESSION['callbacks']);
	// get callback from database
	$callback = astercc::getCallback($_SESSION['curuser']['groupid']);
	while	($callback->fetchInto($mycallback)){
		if ($mycallback['dst'] != $mycallback['src']){	 // legB connected
			$_SESSION['callbacks'][$mycallback['dst'].$mycallback['src']] = array('legA' =>$mycallback['src'],'legB' => $mycallback['dst'], 'start' => 1, 'creditLimit' => $mycallback['creditlimit']);
		}
	}
//print_r($_SESSION['callbacks']);

	// get callback from session
	foreach ($_SESSION['callbacks'] as $callback){
		if ($callback['creditlimit'] > 0)
			$objResponse->addScript('addDiv("divMainContainer","Local/'.$callback['legB'].'","'.$callback['creditlimit'] .'","","")');
		else
			$objResponse->addScript('addDiv("divMainContainer","Local/'.$callback['legB'].'","","","")');

		$objResponse->addScript('xajax_addUnbilled("'.$callback['legB'].'","'.$callback['legA'].'");');
	}
//print_r($_SESSION['callbacks']);
	
	//$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin));
	//$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));
	$objResponse->addScript("checkHangup()");

	$objResponse->addScript("xajax_setGroupBalance();");
	$objResponse->addAssign("creditlimittype","value",$config['system']['creditlimittype']);
	return $objResponse;
}

function searchRate($dialprefix){
	$objResponse = new xajaxResponse();
	if ($dialprefix == ''){
		return $objResponse;
	}
	
	$rate = astercc::searchRate($dialprefix,$_SESSION['curuser']['groupid'],$_SESSION['curuser']['resellerid'],"myrate");

	$rateDesc = astercc::readRateDesc($rate);
	// remove the connect charge part
	// $rateDesc = split("seconds",$rateDesc);
	// $rateDesc = $rateDesc[1]." seconds";
	$objResponse->addAssign("divRate","innerHTML",$rate['destination']."(".$rateDesc.")");
	return $objResponse;
}

function setGroupBalance(){
	global $config, $locate;
	$objResponse = new xajaxResponse();
	# 检查session是否存在
	if ($_SESSION['curuser']['groupid'] == ""){
		return $objResponse;
	}

	$group = astercrm::getRecordByField("id",$_SESSION['curuser']['groupid'],'accountgroup');

	$amount = $group['credit_clid'];	//  income
	$creditlimit = $group['creditlimit']; //  limit
	$callshopcredit = $group['credit_group']; // cost
	$curcredit = $group['curcredit']; // current cost

	if ($amount == '') $amount = 0;
	if ($cost == '') $cost = 0;
	$objResponse->addAssign("spanAmount","innerHTML",$amount);

	if ($_SESSION['curuser']['limittype'] == ''){
			$creditlimit = $locate->Translate("no limit");
			$objResponse->addAssign("spanLimitStatus","innerHTML",$creditlimit);
	}else{
		$balance = $creditlimit - $curcredit ;
		if ($balance <= 50) {
			if ($balance <= 0)
				$objResponse->addAssign("spanLimitStatus","innerHTML",$locate->Translate("no credit left all booth locked"));
			else
				$objResponse->addAssign("spanLimitStatus","innerHTML",$locate->Translate("warning no enough credit"));
		}else{
			$objResponse->addAssign("spanLimitStatus","innerHTML",$locate->Translate("normal"));
		}
	}

	if ($_SESSION['curuser']['usertype'] == 'groupadmin'){
		$objResponse->addAssign("spanLimit","innerHTML",$creditlimit);
		$objResponse->addAssign("spanCost","innerHTML",$callshopcredit);
		$objResponse->addAssign("spancurcredit","innerHTML",$curcredit);
	}

	if (is_numeric($config['system']['refreshBalance']) && $config['system']['refreshBalance'] != 0){
		$refreshtime = $config['system']['refreshBalance'] * 1000;
		$objResponse->addScript('setTimeout("xajax_setGroupBalance()",'.$refreshtime.');');
	}
	#$objResponse->addAlert('balance refreshed');
	return $objResponse->getXML();
}

function setStatus($clid,$status){
	$affectrows = astercc::setStatus($clid,$status);
	$objResponse = new xajaxResponse();
	if ($affectrows == 0){
		//$objResponse->addAssign($peer."-limitstatus","value","");
		$objResponse->addAlert($locate->Translate("falied to lock or unlock"));
	}else{
		if ($status == 1){
			$objResponse->addAssign($clid."-lock","style.backgroundColor","");
		}else{
			$objResponse->addAssign($clid."-lock","style.backgroundColor","red");
		}
		//$objResponse->addAlert("lock/unlock success");
	}

	return $objResponse;
}

function setCreditLimit($peer,$channel,$creditlimit){
	if ($creditlimit < 0.01){
		$creditlimit = -10;
	}
	$affectrows = astercc::setCreditLimit($channel,$creditlimit);
	$objResponse = new xajaxResponse();
	if ($affectrows == 0){
		// cant find this channel
//
		$objResponse->addAssign($peer."-limitstatus","value","");
//
	}
	return $objResponse;
}

/**
*  show extension status
*  @return	objResponse		object		xajax response object
*/

function showStatus(){
	// get old status
	$cstatus = $_SESSION['status'];
	$objResponse = new xajaxResponse();
	if ($_SESSION['curuser']['groupid'] == ""){
		return $objResponse;
	}

	$peers = $_SESSION['curuser']['extensions'];
	
	$peerstatus = astercc::checkPeerStatus($_SESSION['curuser']['groupid']);
	//print_r($peerstatus);exit;

	$event = array('ring' => 1, 'dial' => 2, 'ringing' => 3, 'link' => 4);

	foreach ($peers as $peer){

		if ($cstatus[$peer]['disposition'] != $peerstatus[$peer]['disposition']){	// status changed
			if ($peerstatus[$peer]['disposition'] == ''){
				// a hangup event
				$objResponse->addScript("clearCurchannel('".$peer."');");

				// set display name
				$objResponse->addAssign("$peer-displayname","style.backgroundColor","");

				// should reload CDR
				$objResponse->addScript("removeTr('".$peer."');");
				$objResponse->addScript('setTimeout("xajax_addUnbilled(\''.$peer.'\')",1000);');	 //wait daemon write data to cdr
			}else{ 
				// set display name
				$objResponse->addAssign("$peer-displayname","style.backgroundColor","green");

				if( $peerstatus[$peer]['direction'] == 'outbound'){
					$objResponse->addAssign($peer.'-phone','innerHTML',"<img src='images/outbound.gif'>".$peerstatus[$peer]['dst']);
				}else{
					$objResponse->addAssign($peer.'-phone','innerHTML',"<img src='images/inbound.gif'>".$peerstatus[$peer]['src']);
					$objResponse->addAssign($peer.'-phone','style.color','green');
				}
				$objResponse->addAssign($peer.'-startat','innerHTML',$peerstatus[$peer]['starttime']);
				$objResponse->addAssign($peer.'-channel','value',$peerstatus[$peer]['srcchan']);
				if ($peerstatus[$peer]['answertime'] != '0000-00-00 00:00:00'){
					$now = time();
	 				$initSec = $now - strtotime($peerstatus[$peer]['answertime']);
					$objResponse->addScript("putCurrentTime('".$peer."-localanswertime',$initSec);");
				}
			}
		}
		//credit changed
		if ($cstatus[$peer]['credit'] != $peerstatus[$peer]['credit']){
				$objResponse->addAssign($peer.'-price','innerHTML',astercc::creditDigits($peerstatus[$peer]['credit']));
		}
	}


	$callbacks = $_SESSION['callbacks'];
	if (count($callbacks) > 0){
		foreach ($callbacks as $key => $callback){

			$localChan = 'Local/'.$callback['legB'];
			$res = astercc::getCurLocalChan($localChan,$_SESSION['curuser']['groupid']);
//			print $localChan;
//			print "\n";
//			print $callback['start'];
//			print "\n";
//			print $res->numRows();
			if ($res->numRows() == 0){
				if ( $callback['start'] != 0 ){	//hangup
					$objResponse->addScript("clearCurchannel('".$localChan."');");
					$objResponse->addScript("clearCurchannel('".$localChan."-legb"."');");
					//$objResponse->addAlert("clearCurchannel('".$localChan."-legb"."');");

					// should reload CDR
					$objResponse->addScript("removeTr('".$localChan."');");
					$objResponse->addScript('xajax_addUnbilled("'.$callback['legB'].'","'.$callback['legA'].'");');
	
					//$objResponse->addScript('setTimeout(xajax_addUnbilled("'.$localChan.'"),1000);');

					//unset($_SESSION['callbacks'][$key]);
					$callback = null;
				}else{	//not start yet


				}
				$_SESSION['callbacks'][$key]['start'] = 0;
			}else if ($res->numRows() == 1){	 //calling legA
				$_SESSION['callbacks'][$key]['start'] = 1;
				$res->fetchInto($legA);
					$objResponse->addAssign($localChan.'-phone','innerHTML',$legA['dst']);
					$objResponse->addAssign($localChan.'-startat','innerHTML',$legA['starttime']);
					$objResponse->addAssign($localChan.'-channel','value',$legA['srcchan']);
	//				$objResponse->addAlert($legA['answertime']);
					if ($legA['answertime'] != '0000-00-00 00:00:00'){
						$now = time();
		 				$initSec = $now - strtotime($legA['answertime']);

						$objResponse->addScript("putCurrentTime('".$localChan."-localanswertime',$initSec);");
					}
					/*
					if ($legA['dst'] != ''){
						$rate = astercc::readRate($legA['dst'],$_SESSION['curuser']['groupid']);
						$objResponse->addAssign($localChan.'-rateinitial','innerHTML',floor($rate['rateinitial']*100)/100);
						$objResponse->addAssign($localChan.'-initblock','innerHTML',floor($rate['initblock']*100)/100);
						$objResponse->addAssign($localChan.'-billingblock','innerHTML',floor($rate['billingblock']*100)/100);
						$objResponse->addAssign($localChan.'-connectcharge','innerHTML',floor($rate['connectcharge']*100)/100);
					}
					*/
					$objResponse->addAssign($localChan.'-price','innerHTML',astercc::creditDigits($legA['credit']));
			}else if ($res->numRows() == 2){	 //calling legB
				$_SESSION['callbacks'][$key]['start'] = 2;
				$res->fetchInto($legA);
				//**
					$objResponse->addAssign($localChan.'-phone','innerHTML',$legA['dst']);
					$objResponse->addAssign($localChan.'-startat','innerHTML',$legA['starttime']);
					$objResponse->addAssign($localChan.'-channel','value',$legA['srcchan']);
					if ($legA['answertime'] != '0000-00-00 00:00:00'){
						$now = time();
		 				$initSec = $now - strtotime($legA['answertime']);

						$objResponse->addScript("putCurrentTime('".$localChan."-localanswertime',$initSec);");
					}
					/*
					if ($legA['dst'] != ''){
						$rate = astercc::readRate($legA['dst'],$_SESSION['curuser']['groupid']);
						$objResponse->addAssign($localChan.'-rateinitial','innerHTML',floor($rate['rateinitial']*100)/100);
						$objResponse->addAssign($localChan.'-initblock','innerHTML',floor($rate['initblock']*100)/100);
						$objResponse->addAssign($localChan.'-billingblock','innerHTML',floor($rate['billingblock']*100)/100);
						$objResponse->addAssign($localChan.'-connectcharge','innerHTML',floor($rate['connectcharge']*100)/100);
					}
					*/
					$objResponse->addAssign($localChan.'-price','innerHTML',astercc::creditDigits($legA['credit']));

			//**
				$res->fetchInto($legB);
					$objResponse->addAssign($localChan.'-legb-phone','innerHTML',$legB['dst']);
					$objResponse->addAssign($localChan.'-legb-startat','innerHTML',$legB['starttime']);
					$objResponse->addAssign($localChan.'-legb-channel','value',$legB['srcchan']);
					if ($legB['answertime'] != '0000-00-00 00:00:00'){
						$now = time();
		 				$initSec = $now - strtotime($legB['answertime']);
						#print $legB['answertime'];
						$objResponse->addScript("putCurrentTime('".$localChan."-legb-localanswertime',$initSec);");
					}
					/*
					if ($legB['dst'] != ''){
						$rate = astercc::readRate($legB['dst'],$_SESSION['curuser']['groupid']);
						$objResponse->addAssign($localChan.'-legb-rateinitial','innerHTML',floor($rate['rateinitial']*100)/100);
						$objResponse->addAssign($localChan.'-legb-initblock','innerHTML',floor($rate['initblock']*100)/100);
						$objResponse->addAssign($localChan.'-legb-billingblock','innerHTML',floor($rate['billingblock']*100)/100);
						$objResponse->addAssign($localChan.'-legb-connectcharge','innerHTML',floor($rate['connectcharge']*100)/100);
					}
					*/
					$objResponse->addAssign($localChan.'-legb-price','innerHTML',astercc::creditDigits($legB['credit']));
			}
		}
	}

	$_SESSION['status'] = $peerstatus;
	$objResponse->addScript('setTimeout("showStatus()", 2000);');
	$objResponse->addAssign("spanLastRefresh",'innerHTML',date ("Y-m-d H:i:s",time()));
	return $objResponse;
}

function removeLocalChannel($chan_val){
	$objResponse = new xajaxResponse();
	if (is_array($_SESSION['callbacks'])){
		foreach ($_SESSION['callbacks'] as $key=> $callbacks){
			if ('Local/'.$callbacks['legB'] = $chan_val){
				unset($_SESSION['callbacks'][$key]);
				break;
			}
		}
	}
	return $objResponse;
}

function hangup($channel){
	global $config,$locate;
	$myAsterisk = new Asterisk();
	$objResponse = new xajaxResponse();
//	return $objResponse;

	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		return;
	}
	$myAsterisk->Hangup($channel);
	return $objResponse;
}


function invite($src,$dest,$creditLimit){
	global $config;
	$src = trim($src);
	$dest = trim($dest);
	$credit = trim($credit);
	$myAsterisk = new Asterisk();
	$objResponse = new xajaxResponse();
	
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res)
		$objResponse->addAssign("mobileStatus", "innerText", "Failed");
	
	$strChannel = "Local/".$src."@".$config['system']['outcontext']."/n";

	$_SESSION['callbacks'][$src.$dest] = array('legA' =>$dest,'legB' => $src, 'start' => 0, 'creditLimit' => $creditLimit);
	if ($config['system']['allow_dropcall'] == true){
		$myAsterisk->dropCall($sid,array('Channel'=>"$strChannel",
							'WaitTime'=>30,
							'Exten'=>$dest,
							'Context'=>$config['system']['outcontext'],
							'Account'=>$_SESSION['curuser']['accountcode'],
							'Variable'=>"$strVariable",
							'Priority'=>1,
							'MaxRetries'=>0,
							'CallerID'=>$dest));
	}else{
		$myAsterisk->sendCall($strChannel,$dest,$config['system']['outcontext'],1,NULL,NULL,30,$dest,NULL,$_SESSION['curuser']['accountcode']);
	}
	// add to callback table
	$callback['lega'] = $dest;
	$callback['legb'] = $src;
	$callback['credit'] = $creditLimit;
	$callback['groupid'] = $_SESSION['curuser']['groupid'];
	astercc::insertNewCallback($callback);
	return $objResponse->getXML();
}

function addUnbilled($peer,$leg = null){
	$objResponse = new xajaxResponse();
	if ($_SESSION['curuser']['groupid'] ==""){
		return $objResponse;
	}
	$records = astercc::readUnbilled($peer,$leg,$_SESSION['curuser']['groupid']);
	if ($leg != null){
		$peer = 'Local/'.$peer;
	}
	$totalprice = 0;

	while	($records->fetchInto($mycdr)){
		$price = '';
		$ratedesc = '';
		//$rate = astercc::readRate($mycdr['dst'],$_SESSION['curuser']['groupid']);
		$jsscript = "cdr = new Array();";

		$ratedesc = astercc::readRateDesc($mycdr['memo']).'&nbsp;';

		if ($price == '')
			$price = 0;
		$mycdr['destination'] .= '&nbsp;';
		$totalprice += $mycdr['credit'];
		$jsscript .= "cdr['id'] = '".$mycdr['id']."';";
		$jsscript .= "cdr['clid'] = '".$mycdr['clid']."';";
		//check it is inbound or outbound for show Phone in booth
		if ( $mycdr['src'] == $peer ){
			$jsscript .= "cdr['dst'] = '".$mycdr['dst']."';";
			$jsscript .= "cdr['direction'] = 'outbound';";
		}else{
			$jsscript .= "cdr['dst'] = '".$mycdr['src']."';";
			$jsscript .= "cdr['direction'] = 'inbound';";
		}
		$jsscript .= "cdr['startat'] = '".$mycdr['calldate']."';";
		$jsscript .= "cdr['billsec'] = '".$mycdr['billsec']."';";
		$jsscript .= "cdr['destination'] = '".$mycdr['destination']."';";
		$jsscript .= "cdr['rate'] = '".$ratedesc."';";
		$jsscript .= "cdr['price'] = '".astercc::creditDigits($mycdr['credit'])."';";
		$jsscript .= "appendTr('".$peer."-calllog-tbody',cdr);";
		$objResponse->addScript($jsscript);
	}
	$objResponse->addAssign($peer."-unbilled","innerHTML",$totalprice);
	$objResponse->addScript("calculateBalance('".$peer."')");
	return $objResponse;
}

function checkOut($aFormValues,$divId){
	global $locate;
	$objResponse = new xajaxResponse();
	if (isset($aFormValues['cdrid'])){
		foreach ($aFormValues['cdrid'] as $id){
			$res =  astercc::setBilled($id);
			}
		$objResponse->addAlert($locate->Translate("booth_cleared"));
		$objResponse->addAssign($divId."-unbilled","innerHTML",0);
	}
	$objResponse->addScript("removeTr('".$divId."');");
	$objResponse->addScript("calculateBalance('".$divId."');");
	return $objResponse;
}

$xajax->processRequests();
?>

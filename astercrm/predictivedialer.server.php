<?php
/*******************************************************************************
* predictivedialer.server.php

* 账户管理系统后台文件
* predictivedialer management script

* Function Desc
	predictivedialer management script

* 功能描述
	提供帐户管理脚本

* Function Desc
		init				初始化页面元素
		predictiveDialer

* Revision 0.0461  2008/2/1 20:37:00  last modified by solo
* Desc: fix predictive dialer bug

* Revision 0.0455  2007/10/24 20:37:00  last modified by solo
* Desc: add another dial method: sendCall()

* Revision 0.045  2007/10/18 20:10:00  last modified by solo
* Desc: comment added

*/
require_once ("predictivedialer.common.php");
require_once ("db_connect.php");
require_once ('include/xajaxGrid.inc.php');
require_once ('include/asterisk.class.php');
require_once ('include/astercrm.class.php');
require_once ('include/common.class.php');

function init(){
	global $locate,$config,$db;
	$objResponse = new xajaxResponse();

	$myAsterisk = new Asterisk();
	$myAsterisk->config['asmanager'] = $config['asterisk'];
	$res = $myAsterisk->connect();
	if (!$res){
		$objResponse->addAssign("divAMIStatus", "innerHTML", $locate->Translate("AMI_connection_failed"));
	}

	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin,$_SESSION['curuser']['country'],$_SESSION['curuser']['language']));

	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));

	// get all groups
	if($_SESSION['curuser']['usertype'] == 'admin'){
		$groups = astercrm::getAll("astercrm_accountgroup");
	}elseif($_SESSION['curuser']['usertype'] == 'groupadmin'){
		$groups = astercrm::getRecordsByField('id',$_SESSION['curuser']['groupid'],'astercrm_accountgroup');
	}

	while	($groups->fetchInto($group)){
		// get all enabled campaigns
		$query = "SELECT * FROM campaign WHERE enable = 1 AND groupid = ".$group['groupid'];
		$campaigns = $db->query($query);


		$campaignHTML = '';
		while	($campaigns->fetchInto($campaign)){
				// get numbers in diallist
				$query = "SELECT COUNT(*) FROM diallist WHERE campaignid = ".$campaign['id'];
				$phoneNumber = $db->getOne($query);
				
				$has_queue = 0;
				// check if we have a queue in queue_name
				if ($campaign['queuename'] != ""){
					$query = "SELECT id FROM queue_name WHERE queuename = '".$campaign['queuename']."' ";
					$has_queue = $db->getOne($query);
				}

				$status = "";
				$channel_checked = "";
				$queue_checked = "";

				if ($campaign['status'] == "busy"){
					$status = "checked'";
				}

				if ($campaign['limit_type'] == "channel"){
					$channel_checked = "checked";
				}else if ($campaign['limit_type'] == "queue"){
					$queue_checked = "checked";
				}

				$campaignHTML .= '<div class="group01content">';

				if ($has_queue != 0){
					$campaignHTML .= "<div class='group01l'>".'<img src="images/groups_icon02.gif" width="20" height="20" align="absmiddle" /><acronym title="'.$locate->Translate("inexten").':'.$campaign['inexten'].'&nbsp;|&nbsp;'.$locate->Translate("Outcontext").':'.$campaign['outcontext'].'&nbsp;|&nbsp;'.$locate->Translate("Incontext").':'.$campaign['incontext'].'"> '.$campaign['campaignname'].' ( '.$locate->Translate("queue").': '.$campaign['queuename'].' ) ( <span id="numbers-'.$campaign['id'].'">'.$phoneNumber.'</span> '.$locate->Translate("numbers in dial list").' )</acronym> </div>
				<div class="group01r">
				<input type="checkbox" onclick="setStatus(this);" id="'.$campaign['id'].'-ckb" '.$status.'>'.$locate->Translate("Start").'
				<input type="radio" onclick="setLimitType(this);" id="'.$campaign['id'].'-limittpye" name="'.$campaign['id'].'-limittpye" value="channel" '.$channel_checked.'> '.$locate->Translate("Limited by max channel").' 
				<input type="text" value="'.$campaign['max_channel'].'" id="'.$campaign['id'].'-maxchannel" name="'.$campaign['id'].'-maxchannel" size="2" maxlength="2" class="inputlimit" onblur="setMaxChannel(this);">
				<input type="radio" onclick="setLimitType(this);" id="'.$campaign['id'].'-limittpye" name="'.$campaign['id'].'-limittpye" value="queue" '.$queue_checked.'> '.$locate->Translate("Limited by agents and multipled by").' 
				<input type="text" value="'.$campaign['queue_increasement'].'" id="'.$campaign['id'].'-rate" name="'.$campaign['id'].'-rate" size="4" maxlength="4" class="inputlimit" onblur="setQueueRate(this);">
				</div>';
				}else{
					$campaignHTML .= "<div class='group01l'>".'<img src="images/groups_icon02.gif" width="20" height="20" align="absmiddle" /><acronym title="'.$locate->Translate("inexten").':'.$campaign['inexten'].'&nbsp;|&nbsp;'.$locate->Translate("Outcontext").':'.$campaign['outcontext'].'&nbsp;|&nbsp;'.$locate->Translate("Incontext").':'.$campaign['incontext'].'">'.$campaign['campaignname'].' ( '.$locate->Translate("no queue for this campaign").' ) ( <span id="numbers'.$campaign['id'].'">'.$phoneNumber.'</span> '.$locate->Translate("numbers in dial list").' ) </acronym></div>
				<div class="group01r">
				<input type="checkbox"  onclick="setStatus(this);" id="'.$campaign['id'].'-ckb" '.$status.'>'.$locate->Translate("Start").'
				<input type="radio" name="'.$campaign['id'].'-limittpye[]" value="channel" '.$channel_checked.'>
				'.$locate->Translate("Limited by Max Channel").' <input type="text" value="'.$campaign['max_channel'].'" id="'.$campaign['id'].'-maxchannel" name="'.$campaign['id'].'-maxchannel" size="2" maxlength="2" class="inputlimit" onblur="setMaxChannel(this);">
				</div>';
				}
				$campaignHTML .= '</div>';

				$campaignHTML .= '<div class="group01_channel" id="campaign'.$campaign['id'].'" ></div>';
		}

		$divGroup .= '<div class="group01"><img src="images/groups_icon01.gif" align="absmiddle" />'.$group['groupname'].'</div>
												<div id="group'.$group['groupid'].'">'.$campaignHTML.'</div>
											  <div class="group01_channel" id="unknown'.$group['groupid'].'"></div>
											 </div>';
	}
	$objResponse->addAssign("divMain","innerHTML",$divGroup);
	return $objResponse;
}

function setStatus($campaignid, $field, $value){
	global $db;
	$objResponse = new xajaxResponse();
	$query = "UPDATE campaign SET $field = '$value' WHERE id = $campaignid";
	$db->query($query);
	return $objResponse;
}

function predictiveDialer($f){
	global $config,$db,$locate;
	$objResponse = new xajaxResponse();

	$aDyadicArray[] = array($locate->Translate("src"),$locate->Translate("dst"),$locate->Translate("srcchan"),$locate->Translate("dstchan"),$locate->Translate("starttime"),$locate->Translate("answertime"),$locate->Translate("disposition"));

	// 检查系统目前的通话情况

	//if($_SESSION['curuser']['usertype'] == 'admin'){
		$curcdr = astercrm::getAll("curcdr");
	//}elseif($_SESSION['curuser']['usertype'] == 'groupadmin'){
	//	$curcdr = astercrm::getGroupCurcdr();
	//}	
	
	while	($curcdr->fetchInto($row)){
			if ($row['dstchan'] != ""){
				$flag = 0;

				# check if dstchanis in queue_agent
				$target = split("-",$row['dstchan']);
				$target = $target[0];
				$exten = split("/",$target);
				$exten = $exten[1];

				$query = "SELECT queuename FROM queue_agent WHERE agent = '$target' OR agent LIKE 'Local/$exten\@%' ";
				$queuename = $db->getOne($query);
				if ($queuename != ""){
					$query = "SELECT id, groupid FROM campaign WHERE queuename = '".$queuename."' GROUP BY groupid";
					$campaigns = $db->query($query);
					while ($campaigns->fetchInto($campaign)){


						$campaignCDR[$campaign['id']][] = array($row["src"],$row["dst"],$row["srcchan"],$row["dstchan"],$row["starttime"],$row["answertime"],$row["disposition"]);

						//$groupCDR[$campaign['groupid']][] = array($row["src"],$row["dst"],$row["srcchan"],$row["dstchan"],$row["starttime"],$row["answertime"],$row["srcuid"],$row["dstuid"],$row["disposition"]);

						$flag = 1;
					}
				}

				if ($flag == 0){
					$query = "SELECT groupid, campaignid FROM dialedlist WHERE (dialednumber = '".$row['src']."' OR dialednumber = '".$row['dst']."') AND dialedtime > (now() - INTERVAL 7200 SECOND) ";
					$dialedlist = $db->query($query);
					if ($dialedlist->fetchInto($line)){
						if ($line['campaignid'] > 0) {
							$campaignCDR[$line['campaignid']][] = array($row["src"],$row["dst"],$row["srcchan"],$row["dstchan"],$row["starttime"],$row["answertime"],$row["disposition"]);
						}else{
							$groupCDR[$line['groupid']][] = array($row["src"],$row["dst"],$row["srcchan"],$row["dstchan"],$row["starttime"],$row["answertime"],$row["disposition"]);
						}
					}else{
						// check if src/dst belongs to any group
						$query = "SELECT groupid FROM astercrm_account WHERE extension = '".$row['dst']."' OR extension = '".$row['dst']."'  GROUP BY groupid ORDER BY groupid DESC LIMIT 0,1";
						$groupid = $db->getOne($query);
						if ( $groupid > 0 ){
							$groupCDR[$groupid][] = array($row["src"],$row["dst"],$row["srcchan"],$row["dstchan"],$row["starttime"],$row["answertime"],$row["disposition"]);
						}else{
							$systemCDR[] = array($row["src"],$row["dst"],$row["srcchan"],$row["dstchan"],$row["starttime"],$row["answertime"],$row["disposition"]);
						}
					}
				}
			}
		}

		$systemChannels = common::generateTabelHtml(array_merge($aDyadicArray , $systemCDR));

		$objResponse->addAssign("idvUnknowChannels", "innerHTML", nl2br(trim($systemChannels)));

		// clear all group
		$groups = astercrm::getAll("astercrm_accountgroup");
		while	($groups->fetchInto($group)){
			$objResponse->addAssign("unknown".$group['groupid'], "innerHTML", "");
		}

		// clear all campaign
		$campaigns = astercrm::getAll("campaign");
		while	($campaigns->fetchInto($campaign)){

			$campaign_queue_name[$campaign['id']] = $campaign['queuename'];
			$objResponse->addAssign("campaign".$campaign['id'], "innerHTML", "");
		}

		// start assign all CDRs
		foreach ($groupCDR as $key => $value){
			if (is_array($value)){
				$groupChannels = common::generateTabelHtml(array_merge($aDyadicArray , $value));
				$objResponse->addAssign("unknown$key", "innerHTML", nl2br(trim($groupChannels)));
			}else{
				$objResponse->addAssign("unknown$key", "innerHTML", "");
			}
		}

		foreach ($campaignCDR as $key => $value){
			if (is_array($value)){
				$campaignChannels = common::generateTabelHtml(array_merge($aDyadicArray , $value));
				$objResponse->addAssign("campaign$key", "innerHTML", nl2br(trim($campaignChannels)));
			}else{
				$objResponse->addAssign("campaign$key", "innerHTML", "");
			}
		}
	/*
	// 将$f按组别分类
	foreach ($f as $key => $value){
		list ($campaignid, $field) = split("-",$key);
		$predial_campaigns[$campaignid][$field] = $value;
	}

	foreach ($predial_campaigns as $key => $value){
		if ($value['ckb'] == "on"){
			// 查找是否还有待拨号码
			$diallist_num[$key] = astercrm::getCountByField("campaignid", $key, "diallist");
			$num = 0;
			if ($diallist_num[$key]  > 0){
				if ($value['limittpye'][0] == "channel"){
					// 根据并发限制
					// 检查目前该campaign的并发通道
					$exp = $value['maxchannel'] - count($campaignCDR[$key]);
					if (  $exp > 0 ){
						// 可以发起呼叫, 规则为 (差额 +2)/3
						$num = intval(($exp + 2)/3);
						$i = 0;
						while ($i<$num && placeCall($key)) $i++;
					}else{
						// skip this campaign
					}
				}else{
					// 根据agent限制
					// 获取目前agent的数目
					$query = "SELECT COUNT(*) FROM queue_agent WHERE status = 'In use' AND queuename = '".$campaign_queue_name[$key]."' ";
					$busy_agent_num = $db->getOne($query);

					$query = "SELECT COUNT(*) FROM queue_agent WHERE status = 'Not in use' AND queuename = '".$campaign_queue_name[$key]."' ";
					$free_agent_num = $db->getOne($query);
					$totalagent = ($busy_agent_num + $free_agent_num);
					if (is_numeric($value['rate'])){
						$myagent = intval($totalagent * (1+$rate/100));
					}

					$exp = $myagent - count($campaignCDR[$key]);
					if (  $exp > 0 ){
						// 可以发起呼叫, 规则为 (差额 +2)/3
						$num = intval(($exp + 2)/3);
						$i = 0;
						while ($i<$num && placeCall($key)) $i++;
					}else{
						// skip this campaign
					}
				}
			}
			// refresh campaing number
			$objResponse->addAssign("numbers-$key","innerHTML",$diallist_num[$key] - $i);

		}else{
			unset($predial_campaigns[$key]);
		}
	}
	*/
	//exit;
	$check_interval = 2000;
	if ( is_numeric($config['system']['status_check_interval']) ) $check_interval = $config['system']['status_check_interval'] * 1000;

	$objResponse->addScript("setTimeout(\"startDial()\", ".$check_interval.");");	

	return $objResponse;
}

function placeCall($campaignid){
	global $config;

	$myAsterisk = new Asterisk();
	$row =& astercrm::getDialNumber($campaignid);
	
	// 待拨号码为空
	if (!$row) return false;
	//print_r($row);

	$id = $row['id'];
	$groupid = $row['groupid'];
	$campaignid = $row['campaignid'];
	$phoneNum = $row['dialnumber'];
	$trytime = $row['trytime'];
	$assign = $row['assign'];
	$pdcontext = $row['incontext'];
	$outcontext = $row['outcontext'];

	if ($row['inexten'] != ""){
		$pdextension = $row['inexten'];
	}else{
		if ($row['assign'] != ""){
			$pdextension = $row['assign'];
		}else{
			$pdextension = $row['dialnumber'];
		}
	}

	$res = astercrm::deleteRecord($id,"diallist");

	$f['dialednumber'] = $phoneNum;
	$f['dialedby'] = $_SESSION['curuser']['username'];
	$f['groupid'] = $groupid;
	$f['trytime'] = $trytime + 1;
	$f['assign'] = $assign;
	$f['campaignid'] = $campaignid;
	$res = astercrm::insertNewDialedlist($f);

	$actionid=md5(uniqid(""));

	$strChannel = "Local/".$phoneNum."@".$outcontext."/n";
	if ($config['system']['allow_dropcall'] == true){
		$myAsterisk->dropCall($actionid,array('Channel'=>"$strChannel",
									'WaitTime'=>30,
									'Exten'=>$pdextension,
									'Context'=>$pdcontext,
									'Variable'=>"$strVariable",
									'Priority'=>1,
									'MaxRetries'=>0,
									'CallerID'=>$phoneNum));
	}else{
		$myAsterisk->config['asmanager'] = $config['asterisk'];
		$res = $myAsterisk->connect();

		$myAsterisk->sendCall($strChannel,$pdextension,$pdcontext,1,NULL,NULL,30,$phoneNum,NULL,NULL,NULL,$actionid);
	}

	return true;
}

$xajax->processRequests();
?>

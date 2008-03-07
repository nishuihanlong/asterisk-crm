<?php
/*******************************************************************************
********************************************************************************/
require_once ("login.common.php");
require_once ("db_connect.php");
require_once ('include/asterisk.class.php');
require_once ('include/common.class.php');
require_once ('include/astercrm.class.php');

/**
*  function to process form data
*	
*  	@param $aFormValues	(array)			login form data
															$aFormValues['username']
															$aFormValues['password']
															$aFormValues['locate']
*	@return $objResponse
*/

function processForm($aFormValues)
{
	global $config;

	$objResponse = new xajaxResponse();
	global $locate;
	if ($config['system']['validcode'] == 'yes'){
		if (trim($aFormValues['code']) != $_SESSION["Checknum"]){
			$objResponse->addAlert('Invalid code');
			$objResponse->addScript('init();');
			return $objResponse;
		}
	}

	if (trim($aFormValues['username']) == "")
	{
		$objResponse->addAlert($locate->Translate("username_cannot_be_blank"));
		$objResponse->addScript('init();');
		return $objResponse;
	}
	if (trim($aFormValues['password']) == "")
	{
		$objResponse->addAlert($locate->Translate("password_cannot_be_blank"));
		$objResponse->addScript('init();');
		return $objResponse;
	}

	if (array_key_exists("username",$aFormValues))
	{
		if (ereg("[0-9a-zA-Z]+",$aFormValues['username']) && ereg("[0-9a-zA-Z]+",$aFormValues['password']))
		{
		  // passed
			return processAccountData($aFormValues);
		}else{
		  // error
			$objResponse->addAlert($locate->Translate("invalid_string"));
			$objResponse->addScript('init();');
			return $objResponse;
		}

	} else{
		$objResponse = new xajaxResponse();
		return $objResponse;
	}
}

/**
*  function to init login page
*	
*  	@param $aFormValues	(array)			login form data
															$aFormValues['username']
															$aFormValues['password']
															$aFormValues['locate']
*	@return $objResponse
*  @session
															$_SESSION['curuser']['country']
															$_SESSION['curuser']['language']
*  @global
															$locate
*/

function init($aFormValue){
	$objResponse = new xajaxResponse();
	
	global $locate,$config;

	list($_SESSION['curuser']['country'],$_SESSION['curuser']['language']) = split ("_", $aFormValue['locate']);	//get locate parameter

	$locate=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'login');			//init localization class
	$objResponse->addAssign("titleDiv","innerHTML",$locate->Translate("title"));
	$objResponse->addAssign("usernameDiv","innerHTML",$locate->Translate("username"));
	$objResponse->addAssign("passwordDiv","innerHTML",$locate->Translate("password"));
	$objResponse->addAssign("loginButton","value",$locate->Translate("submit"));
	$objResponse->addAssign("loginButton","disabled",false);
	$objResponse->addAssign("onclickMsg","value",$locate->Translate("please_waiting"));
	$objResponse->addScript("xajax.$('username').focus();");
	$objResponse->addScript("imgCode = new Image;imgCode.src = 'showimage.php';document.getElementById('imgCode').src = imgCode.src;");
	$objResponse->addAssign("divCopyright","innerHTML",Common::generateCopyright($skin));
	unset($_SESSION['curuser']['username']);
	unset($_SESSION['curuser']['extensions']);
	unset($_SESSION['curuser']['userid']);
	unset($_SESSION['curuser']['usertype']);
	unset($_SESSION['curuser']['accountcode']);
	unset($_SESSION['curuser']['ipaddress']);
	unset($_SESSION['curuser']['allowcallback']);
	return $objResponse;
}

/**
*  function to verify user data
*	
*  	@param $aFormValues	(array)			login form data
															$aFormValues['username']
															$aFormValues['password']
															$aFormValues['locate']
*	@return $objResponse
*  @session
															$_SESSION['curuser']['username']
															$_SESSION['curuser']['extension']
															$_SESSION['curuser']['extensions']
															$_SESSION['curuser']['country']
															$_SESSION['curuser']['language']
															$_SESSION['curuser']['channel']
															$_SESSION['curuser']['accountcode']
*/
function processAccountData($aFormValues)
{
	global $db,$locate,$config;

	$objResponse = new xajaxResponse();
	
	$bError = false;
	

	$loginError = false;

	if (!$bError)
	{
		$query = "SELECT account.*, accountgroup.accountcode,accountgroup.allowcallback FROM account LEFT JOIN accountgroup ON accountgroup.id = account.groupid  WHERE username='" . $aFormValues['username'] . "'";
		$res = $db->query($query);
		if ($res->fetchInto($list)){
			if ($list['password'] == $aFormValues['password'])
			{
				$_SESSION = array();
				$_SESSION['curuser']['username'] = trim($aFormValues['username']);
				$_SESSION['curuser']['usertype'] = $list['usertype'];
				$_SESSION['curuser']['ipaddress'] = $_SERVER["REMOTE_ADDR"];
 				$_SESSION['curuser']['userid'] = $list['id'];
 				$_SESSION['curuser']['groupid'] = $list['groupid'];
 				$_SESSION['curuser']['resellerid'] = $list['resellerid'];
				$res = astercrm::getCalleridListByID($list['groupid']);
				while	($res->fetchInto($row)){
					$_SESSION['curuser']['extensions'][] = $row['clid'];
				}
				if (!is_array($_SESSION['curuser']['extensions']))
					$_SESSION['curuser']['extensions'] = array();


				$_SESSION['curuser']['allowcallback'] = $list['allowcallback'];
				$_SESSION['curuser']['accountcode'] = $list['accountcode'];

//				if ($list['extensions'] != ''){
//					$_SESSION['curuser']['extensions'] = split(',',$list['extensions']);
//				}
//				else{
//				}

				list($_SESSION['curuser']['country'],$_SESSION['curuser']['language']) = split ("_", $aFormValues['locate']);
/*
	if you dont want check manager status and show device status when user login 
	please uncomment these three line
*/
				$objResponse->addAlert($locate->Translate("login_success"));
				if ($_SESSION['curuser']['usertype'] == 'groupadmin' || $_SESSION['curuser']['usertype'] == 'operator')
					$objResponse->addScript('window.location.href="systemstatus.php";');
				else
					$objResponse->addScript('window.location.href="account.php";');

				return $objResponse;


				//check AMI connection
				$myAsterisk = new Asterisk();
				$myAsterisk->config['asmanager'] = $config['asterisk'];
				$res = $myAsterisk->connect();
				
			
				$html .= $locate->Translate("server_connection_test");
				if ($res){
					$html .= '<font color=green>'.$locate->Translate("pass").'</font><br>';
					$html .= '<b>'.$_SESSION['curuser']['extension'].' '.$locate->Translate("device_status").'</b><br>';
					$html .= asterisk::getPeerIP($_SESSION['curuser']['extension']).'<br>';
					$html .= asterisk::getPeerStatus($_SESSION['curuser']['extension']).'<br>';
				}else{
					$html .= '<font color=red>'.$locate->Translate("no_pass").'</font>';
				}
				$html .= '<input type="button" value="'.$locate->Translate("continue").'" id="btnContinue" name="btnContinue" onclick="window.location.href=\'systemstatus.php\';">';
				$objResponse->addAssign("formDiv","innerHTML",$html);
				$objResponse->addClear("titleDiv","innerHTML");
				$objResponse->addScript("xajax.$('btnContinue').focus();");
			} else{
				$loginError = true;
			}
		} else{
				$loginError = true;
		}


		if (!$loginError){
			return $objResponse;
		} else {
			$objResponse->addAlert($locate->Translate("login_failed"));
			$objResponse->addAssign("loginButton","value",$locate->Translate("submit"));
			$objResponse->addAssign("loginButton","disabled",false);
			return $objResponse;
		}
	} else {
		$objResponse->addAssign("loginButton","value",$locate->Translate("submit"));
		$objResponse->addAssign("loginButton","disabled",false);
	}
	
	return $objResponse;
}


$xajax->processRequests();
?>
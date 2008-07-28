<?php
/*******************************************************************************
* login.server.php
* 用户登入程序文件
* user login function page

* Public Functions List
									processForm

* Private Functions List
									processAccountData

* Revision 0.0456  2007/11/12 10:49:00  modified by solo
* Desc: add $_SESSION['curuser']['channel'], $_SESSION['curuser']['accountcode']

* Revision 0.045  2007/10/8 14:21:00  modified by solo
* Desc: add string check

* Revision 0.044  2007/09/10 14:21:00  modified by solo
* Desc: add $_SESSION['curuser']['usertype'] to save user type: admin | user
* 描述: 增加了保存用户权限的变量: admin | user, 保存在变量$_SESSION['curuser']['usertype']


* Revision 0.044  2007/09/7 19:55:00  modified by solo
* Desc: modify function init, use unset() to clean session, which means everytime user visit login page, he will log out automaticly
* 描述: 修改了init函数, 使用 unset() 函数清除session, 每当用户访问login时, 都会视为自动登出

* Revision 0.044  2007/09/7 17:55:00  modified by solo
* Desc: add some comments
* 描述: 增加了一些注释信息


********************************************************************************/
require_once ("login.common.php");
require_once ("db_connect.php");
require_once ('include/asterisk.class.php');
require_once ('include/astercrm.class.php');
require_once ('include/common.class.php');

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
	global $locate;
	$objResponse = new xajaxResponse();
	if (trim($aFormValues['username']) == "")
	{
		$objResponse->addAlert($locate->Translate("Username cannot be blank"));
		$objResponse->addScript('init();');
		return $objResponse;
	}
	if (trim($aFormValues['password']) == "")
	{
		$objResponse->addAlert($locate->Translate("Password cannot be blank"));
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
			$objResponse->addAlert($locate->Translate("Invalid string"));
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

	list($_SESSION['curuser']['country'],$_SESSION['curuser']['language']) = split ("_", $aFormValue['locate']);	
	//get locate parameter
	$locate=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'login');			//init localization class

	$objResponse->addAssign("titleDiv","innerHTML",$locate->Translate("Title"));
	$objResponse->addAssign("usernameDiv","innerHTML",$locate->Translate("Username"));
	$objResponse->addAssign("passwordDiv","innerHTML",$locate->Translate("Password"));
	$objResponse->addAssign("loginButton","value",$locate->Translate("Submit"));
	$objResponse->addAssign("loginButton","disabled",false);
	$objResponse->addAssign("onclickMsg","value",$locate->Translate("Please waiting"));
	$objResponse->addScript("xajax.$('username').focus();");
	$objResponse->addAssign("divCopyright","innerHTML",Common::generateCopyright($skin));

	unset($_SESSION['curuser']);
	unset($_SESSION['status']);
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
		//$query = "SELECT * FROM account WHERE username='" . $aFormValues['username'] . "'";
		//$res = $db->query($query);
		
		$row = astercrm::getRecordByField("username",$aFormValues['username'],"astercrm_account");
		if ($row['id'] != '' ){
			if ($row['password'] == $aFormValues['password'])
			{
				$_SESSION = array();
				$_SESSION['curuser']['username'] = trim($aFormValues['username']);
				$_SESSION['curuser']['extension'] = $row['extension'];
				$_SESSION['curuser']['usertype'] = $row['usertype'];
				$_SESSION['curuser']['accountcode'] = $row['accountcode'];

				// added by solo 2007-10-90
				$_SESSION['curuser']['channel'] = $row['channel'];
				$_SESSION['curuser']['extensions'] = array();
				$_SESSION['curuser']['groupid'] = $row['groupid'];				

				if ($row['extensions'] != ''){
					$_SESSION['curuser']['extensions'] = split(',',$row['extensions']);
				}

				// if it's a group admin, then add all group extension to it
				if ($row['usertype'] == 'groupadmin'){
					$_SESSION['curuser']['memberExtens'] = array();
					$groupList = astercrm::getGroupMemberListByID($row['groupid']);
					while	($groupList->fetchInto($groupRow)){
						$_SESSION['curuser']['memberExtens'][] = $groupRow['extension'];
					}
				}
				list($_SESSION['curuser']['country'],$_SESSION['curuser']['language']) = split ("_", $aFormValues['locate']);

				// get group information
				$_SESSION['curuser']['group'] = astercrm::getRecordByField("groupid",$row['groupid'],"astercrm_accountgroup");
				if($row['dialinterval'] != 0) {
					$_SESSION['curuser']['dialinterval'] = $row['dialinterval'];
				}else {
					$row_group = astercrm::getRecordByField("groupid",$row['groupid'],"astercrm_accountgroup");
					$_SESSION['curuser']['dialinterval'] = $_SESSION['curuser']['group']['agentinterval'];
				}

/*
	if you dont want check manager status and show device status when user login 
	please uncomment these three line
*/
				//$objResponse->addAlert($locate->Translate("Login success"));
				$objResponse->addScript('window.location.href="portal.php";');
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
				$html .= '<input type="button" value="'.$locate->Translate("continue").'" id="btnContinue" name="btnContinue" onclick="window.location.href=\'portal.php\';">';
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
			$objResponse->addAssign("loginButton","value",$locate->Translate("Submit"));
			$objResponse->addAssign("loginButton","disabled",false);
			return $objResponse;
		}
	} else {
		$objResponse->addAssign("loginButton","value",$locate->Translate("Submit"));
		$objResponse->addAssign("loginButton","disabled",false);
	}
	
	return $objResponse;
}
$xajax->processRequests();
?>
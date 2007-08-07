<?php
require_once ("login.common.php");
require_once ("db_connect.php");

function processForm($aFormValues)
{
	if (array_key_exists("username",$aFormValues))
	{
		return processAccountData($aFormValues);
	}
}

function processAccountData($aFormValues)
{
	global $db;

	$objResponse = new xajaxResponse();
	
	$bError = false;
	
	if (trim($aFormValues['username']) == "")
	{
		$objResponse->addAlert("Please enter a username.");
		$bError = true;
	}
	if (trim($aFormValues['password']) == "")
	{
		$objResponse->addAlert("You may not have a blank password.");
		$bError = true;
	}

	$loginError = false;

	if (!$bError)
	{
		$query = "SELECT * FROM account WHERE username='" . $aFormValues['username'] . "'";
		$res = $db->query($query);

		if ($res->fetchInto($list)){
			if ($list['password'] == $aFormValues['password'])
			{
				$_SESSION = array();
				$_SESSION['curuser']['username'] = trim($aFormValues['username']);
				$_SESSION['curuser']['extension'] = $list['extension'];

				$objResponse->addAlert("Good Boy.");
				$objResponse->addScript("location.href='portal.php';");

			} else{
				$loginError = true;
			}
		} else{
				$loginError = true;
		}


		if (!$loginError){
			return $objResponse;
		} else {
			$objResponse->addAlert("Login error.");
			$objResponse->addAssign("loginButton","value","continue ->");
			$objResponse->addAssign("loginButton","disabled",false);
			return $objResponse;
		}
	} else {
		$objResponse->addAssign("loginButton","value","continue ->");
		$objResponse->addAssign("loginButton","disabled",false);
	}
	
	return $objResponse;
}


$xajax->processRequests();
?>
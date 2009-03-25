<?php
/*******************************************************************************
* preferences.server.php

* 配置管理系统后台文件
* preferences background management script

* Function Desc
	provide preferences management script

* 功能描述
	提供配置管理脚本

* Function Desc
		init				初始化页面元素
		initIni				从配置文件中读取信息填充页面上的input对象
		initLocate			初始化页面上的说明信息
		savePreferences		保存配置文件
		checkDb				检查数据库是否能正确连接
		checkAMI			检查AMI是否能正确连接
		checkSys			检查系统参数是否正确
							目前仅检查了上传目录是否可写

* Revision 0.0456  2007/11/12 15:47:00  last modified by solo
* Desc: page created
********************************************************************************/
require_once ("db_connect.php");
require_once ("profile.common.php");
require_once ("include/asterisk.class.php");
require_once ("include/astercrm.class.php");
require_once ("include/paypal.class.php");

/**
*  initialize page elements
*
*/

function init($get=''){
	global $config,$locate;
	$objResponse = new xajaxResponse();
	
	if($get != ''){
		$get = rtrim($get,',');
		$get = split(',',$get);
		foreach($get as $item_tmp){
			$item = split(':',$item_tmp);
			$get_item[$item[0]] = $item[1];
		}
	}
	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin,$_SESSION['curuser']['country'],$_SESSION['curuser']['language']));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));
	$infoHtml = InfomationHtml();
	$objResponse->addAssign("info","innerHTML",$infoHtml);
	if($_SESSION['curuser']['usertype'] == 'reseller'){
		$paymentinfoHtml = paymentInfoHtml();
		$objResponse->addAssign("paymentInfo","innerHTML",$paymentinfoHtml);
		if($get_item["action"] == 'success'){
			$p = new paypal_class;
			$p->add_field('auth_token',$config['epayment']['pdt_identity_token']);
		}elseif($get_item["action"] == 'cancel'){
			$rechargeInfoHtml = '<table border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F0F0F0" width="600">
				  <tr>
					<td width="26%" height="39" class="td font" align="center">
						'.$locate->Translate('Recharge By Paypal').'
					</td>
					<td width="74%" class="td font" align="center">&nbsp;</td>
				  </tr>
					<tr><td height="10" class="td"></td>
					<td class="td font" align="center">&nbsp;</td>
				  </tr>
				</table>
				<table border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#F0F0F0" id="menu" width="600">
				<tr bgcolor="#F7F7F7">
				<td align="center" valign="top"><b>Your credit order was canceled</b>&nbsp;&nbsp;&nbsp;<a href="javascript:void(null);" onclick="refreshRechargeInfo();">'.$locate->Translate('Return').'</a></td></tr></table> ';
		}else{
			if($config['epayment']['epayment_status'] == 'enable'){
				$rechargeInfoHtml = rechargeHtml();				
			}
		}
		$objResponse->addAssign("rechargeInfo","innerHTML",$rechargeInfoHtml);
	}
	return $objResponse;
}

function InfomationHtml(){
	global $locate;
	if($_SESSION['curuser']['usertype'] == 'reseller'){
		$reseller_row = astercrm::getRecordByID($_SESSION['curuser']['resellerid'],'resellergroup');
		$balance = $reseller_row['creditlimit'] - $reseller_row['curcredit'];
		$html = '<table border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F0F0F0" width="600">
				  <tr>
					<td width="25%" height="39" class="td font" align="center">
						'.$locate->Translate('Reseller Infomation').'
					</td>
					<td width="75%" class="td font" align="center">&nbsp;</td>
				  </tr>
					<tr><td height="10" class="td"></td>
					<td class="td font" align="center">&nbsp;</td>
				  </tr>
				</table>
				<table border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#F0F0F0" id="menu" width="600"> 
				  <tr bgcolor="#F7F7F7">
					<td width="20%" align="right" valign="top" >'.$locate->Translate('Reseller name').':&nbsp;&nbsp;</td>
					<td width="30%" align="center" valign="top" ><b>'.$reseller_row['resellername'].'</b></div></td>
					<td width="20%" align="right" valign="top" ><b>'.$locate->Translate('Accountcode').':&nbsp;&nbsp;</b></div></td>
					<td width="30%" align="center" valign="top" >'.$reseller_row['accountcode'].'</td>
				  </tr>
				  <tr>
					<td width="20%" align="right" valign="top" >'.$locate->Translate('Limittype').':&nbsp;&nbsp;</td>
					<td width="30%" align="center" valign="top" ><b>'.$reseller_row['limittype'].'</b></td>
					<td width="20%" align="right" valign="top" >'.$locate->Translate('Allowcallback').':&nbsp;&nbsp;</td>
					<td width="30%" align="center" valign="top" ><b>'.$reseller_row['allowcallback'].'</b></td>	
				  </tr>
				  <tr bgcolor="#F7F7F7">
					<td width="20%" align="right" valign="top" >'.$locate->Translate('Callshop cost').':&nbsp;&nbsp;</td>
					<td width="30%" align="center" valign="top" ><b>'.$reseller_row['credit_group'].'</b></td>
					<td width="20%" align="right" valign="top" >'.$locate->Translate('Clid cost').':&nbsp;&nbsp;</td>
					<td width="30%" align="center" valign="top" ><b>'.$reseller_row['credit_clid'].'</b></td>	
				  </tr>
				  <tr>
					<td width="20%" align="right" valign="top" >'.$locate->Translate('Total cost').':&nbsp;&nbsp;</td>
					<td width="30%" align="center" valign="top" ><b>'.$reseller_row['credit_reseller'].'</b></td>	
					<td width="20%" align="right" valign="top" >'.$locate->Translate('Current cost').':&nbsp;&nbsp;</td>
					<td width="30%" align="center" valign="top" ><b>'.$reseller_row['curcredit'].'</b></td>	
				  </tr>
				  <tr bgcolor="#F7F7F7">
					<td width="20%" align="right" valign="top" >'.$locate->Translate('Credit limit ').':&nbsp;&nbsp;</td>
					<td width="30%" align="center" valign="top" ><b>'.$reseller_row['creditlimit'].'</b></td>
					<td width="20%" align="right" valign="top" >'.$locate->Translate('Balance').':&nbsp;&nbsp;</td>
					<td width="30%" align="center" valign="top" ><b>'.$balance.'</b></td>    	
				  </tr>
			</table>';
		return $html;
	}
}

function paymentInfoHtml(){
	global $locate;
	$reseller_row = astercrm::getRecordByID($_SESSION['curuser']['resellerid'],'resellergroup');
	$html = '<table border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F0F0F0" width="600">
			  <tr>
				<td width="46%" height="39" class="td font" align="center">'.
					$locate->Translate('Online Payment Receiving Infomation').'
				</td>
				<td width="54%" class="td font" align="center">&nbsp;</td>
			  </tr>
				<tr><td height="10" class="td"></td>
				<td class="td font" align="center">&nbsp;</td>
			  </tr>
			</table>
			<table border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#F0F0F0" id="menu" width="600">
			  <tr bgcolor="#F7F7F7">
				<td width="25%" align="right" valign="top" >'.$locate->Translate('Online payment').':&nbsp;&nbsp;</td>
				<td width="75%" align="center" valign="top" ><b>'.$reseller_row['epayment_status'].'</b></td>
			  </tr>
			  <tr>
				<td width="25%" align="right" valign="top" >'.$locate->Translate('Paypal account').':&nbsp;&nbsp;</td>
				<td width="75%" align="center" valign="top" ><b>'.$reseller_row['epayment_account'].'</b></td>
			  </tr>
			  <tr bgcolor="#F7F7F7">
				<td width="25%" align="right" valign="top" >'.$locate->Translate('Paypal payment url').':&nbsp;&nbsp;</td>
				<td width="75%" align="center" valign="top" ><b>'.$reseller_row['epayment_paypal_url'].'</b></td>
			  </tr>
			  <tr>
				<td  align="right" valign="top" >'.$locate->Translate('Item name').':&nbsp;&nbsp;</td>
				<td  align="center" valign="top" ><b>'.$reseller_row['epayment_item_name'].'</b></td>
			  </tr>
			  <tr bgcolor="#F7F7F7">
				<td  align="right" valign="top" >'.$locate->Translate('Paypal identity token').':&nbsp;&nbsp;</td>
				<td  align="center" valign="top" ><b>'.$reseller_row['epayment_identity_token'].'</b></td>
			  </tr>
			  <tr>
				<td align="right" valign="top" >'.$locate->Translate('Available amount').':&nbsp;&nbsp;</td>
				<td align="center" valign="top" ><b>'.$reseller_row['epayment_amount_package'].'</b></td>	
			  </tr>
			  <tr bgcolor="#F7F7F7">
				<td align="right" valign="top" >'.$locate->Translate('Currency code').':&nbsp;&nbsp;</td>
				<td align="center" valign="top" ><b>'.$reseller_row['epayment_currency_code'].'</b></td>
			  </tr>
			  <tr>
				<td align="right" valign="top" colspan="2"><input type="button" id="epayment_edit" name="epayment_edit" value="'.$locate->Translate('Edit').'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			  </tr>
			</table>';
	return $html;
}

function rechargeHtml(){
	global $config,$locate;
	$html = '';
	if($_SESSION['curuser']['usertype'] == 'reseller'){
		$html .= '<table border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F0F0F0" width="600">
			  <tr>
				<td width="26%" height="39" class="td font" align="center">
					'.$locate->Translate('Recharge By Paypal').'
				</td>
				<td width="74%" class="td font" align="center">&nbsp;</td>
			  </tr>
				<tr><td height="10" class="td"></td>
				<td class="td font" align="center">&nbsp;</td>
			  </tr>
			</table>
			<table border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#F0F0F0" id="menu" width="600">
			  <tr bgcolor="#F7F7F7">
				<td align="center" valign="top" ><span id="recharge_item_name" name="recharge_item_name">'.$config['epayment']['item_name'].'</span>:&nbsp;&nbsp;<span id="recharge_currency_code" id="recharge_currency_code">'.$config['epayment']['currency_code'].'</span>&nbsp;&nbsp;<select id="amount" name="amount">';

		$amountP = split(',',$config['epayment']['amount']);

		foreach ($amountP as $amount ){
			if(is_numeric($amount)) {
				$option .= '<option value="'.$amount.'">'.$amount.'</option>';
			}
		}
		$html .= $option.'</select>&nbsp;&nbsp;<input type="button" value="'.$locate->Translate('Recharge By Paypal').'" onclick="rechargeByPaypal();"></td>
			  </tr>
			</table>';
	}

	return $html;
}

function refreshRechargeInfo(){
	$objResponse = new xajaxResponse();
	$rechargeInfoHtml = rechargeHtml();	
	$objResponse->addAssign("rechargeInfo","innerHTML",$rechargeInfoHtml);
	return $objResponse;
}

function rechargeByPaypal($amount){
	global $config,$locate;

	$objResponse = new xajaxResponse();
	if(!is_numeric($amount)) {
		$objResponse->addAlert($locate->Translate('Please select amount'));
		return $objResponse;
	}

	$paypal_charge = array();
	if($_SESSION['curuser']['usertype'] == 'reseller'){
		if( $config['epayment']['epayment_status'] != 'enable' ){
			$objResponse->addAlert($locate->Translate('The seller does not support online payment'));
		}else{
			$p = new paypal_class;
			$p->paypal_url = $config['epayment']['paypal_payment_url'];
			$p->add_field('business',$config['epayment']['paypal_account']);
			$this_url = $_SERVER['HTTP_REFERER'];
			$this_url = split('\?',$this_url);
			$this_url = $this_url['0'];
			$p->add_field('return',$this_url.'?action=success');
			$p->add_field('cancel_return',$this_url.'?action=cancel');
			$p->add_field('notify_url',$config['epayment']['paypal_account']);
			$p->add_field('item_name',$config['epayment']['item_name']);
			$p->add_field('item_number',$_SESSION['curuser']['resellerid']);
			$p->add_field('amount',$amount);

			$paymentHtml .= '<table border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F0F0F0" width="600">
				  <tr>
					<td width="26%" height="39" class="td font" align="center">
						'.$locate->Translate('Recharge By Paypal').'
					</td>
					<td width="74%" class="td font" align="center">&nbsp;</td>
				  </tr>
					<tr><td height="10" class="td"></td>
					<td class="td font" align="center">&nbsp;</td>
				  </tr>
				</table>
				<table border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#F0F0F0" id="menu" width="600">
				<tr bgcolor="#F7F7F7">
				<td align="center" valign="top"><b>Please wait, your credit order is being processed...</b>'; 

			$paymentHtml .= $p->submit_paypal_post();
			$paymentHtml .= '</td></tr></table>';

			$objResponse->addAssign("rechargeInfo","innerHTML",$paymentHtml);
			$objResponse->addScript("document.getElementById('paymentForm').submit()");
		}
	}
	return $objResponse;
}


$xajax->processRequests();
?>

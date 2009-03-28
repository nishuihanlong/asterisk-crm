<?php
require_once ("db_connect.php");
require_once ("profile.common.php");
require_once ("include/astercrm.class.php");
require_once ('include/paypal.class.php');  // include the class file

	$p = new paypal_class;             // initiate an instance of the class

	$payer = explode(':',$_POST['custom']);
	$userid = $payer['0'];
	$uesrtype = $payer['1'];
	$resellerid = $payer['2'];
	$groupid = $payer['3'];

	if($uesrtype == 'reseller'){
		$p->paypal_url = $config['epayment']['paypal_payment_url'];
	}else{
		$reseller_row = astercrm::getRecordByID($resellerid,'resellergroup');
		$p->paypal_url = $reseller_row['epayment_paypal_url'];
	}

	if ($p->validate_ipn()) {
          
         // Payment has been recieved and IPN is verified.  This is where you
         // update your database to activate or process the order, or setup
         // the database with the user's order details, email an administrator,
         // etc.  You can access a slew of information via the ipn_data() array.
  
         // Check the paypal documentation for specifics on what information
         // is available in the IPN POST variables.  Basically, all the POST vars
         // which paypal sends, which we send back for validation, are now stored
         // in the ipn_data() array.
  
         // For this example, we'll just email ourselves ALL the data.
		 if($p->ipn_data['custom'] != ''){
			 $payer = explode(':',$p->ipn_data['custom']);
			 $userid = $payer['0'];
			 $uesrtype = $payer['1'];
			 $resellerid = $payer['2'];
			 $groupid = $payer['3'];
			 
			 $reseller_row = astercrm::getRecordByID($resellerid,'resellergroup');

			 if($uesrtype == 'reseller'){
				$account = astercrm::getRecordByID($userid,'account');
				$srcCredit = $reseller_row['curcredit'];
				$updateCurCredit = $srcCredit - $p->ipn_data['mc_gross'];
				$sql = "UPDATE resellergroup SET curcredit = $updateCurCredit WHERE id = '".$account['resellerid']."'";
				$mailto = $config['epayment']['notify_mail'];				
				$mailTitle = $locate->Translate('Reseller').': '.$account['username'].' '.$locate->Translate('Paymented').' '.$config['epayment']['currency_code'].' '.$p->ipn_data['mc_gross'].' '.$locate->Translate('for').' '.$config['epayment']['item_name'].','.$locate->Translate('Please check it').' - ipn';

			 }elseif($uesrtype == 'groupadmin'){
				$account = astercrm::getRecordByID($userid,'account');
				$group_row = astercrm::getRecordByID($account['groupid'],'accountgroup');
				$srcCredit = $group_row['curcredit'];
				$updateCurCredit = $srcCredit - $p->ipn_data['mc_gross'];
				$sql = "UPDATE accountgroup SET curcredit = $updateCurCredit WHERE id = '".$account['groupid']."'";
				$mailto = $reseller_row['epayment_notify_mail'];
				$mailTitle = $locate->Translate('Callshop').': '.$account['username'].' '.$locate->Translate('Paymented').' '.$reseller_row['epayment_currency_code'].' '.$p->ipn_data['mc_gross'].' '.$locate->Translate('for').' '.$reseller_row['epayment_item_name'].','.$locate->Translate('Please check it').' - ipn';
			}

			$txn_res = astercrm::getRecordByField('epayment_txn_id',$p->ipn_data['txn_id'],'credithistory');
			
			// check that txn_id has not been previously processed
			if($txn_res['id'] > 0){
				exit();
			}else{
				$res = $db->query($sql);
				if($res){
					$credithistory_sql = "INSERT INTO credithistory SET modifytime=now(),	resellerid='".$account['resellerid']."',groupid='".$account['groupid']."',srccredit='".$srcCredit."',modifystatus='reduce',modifyamount='".$p->ipn_data['mc_gross']."',comment='Recharge By Paypal',operator='".$userid."',epayment_txn_id='".$p->ipn_data['txn_id']."'";
					$credithistory_res=$db->query($credithistory_sql);
				}
				$subject = 'Instant Payment Notification - Recieved Payment';
				$to = $mailto;    //  your email
				$body =  "An instant payment notification was successfully recieved\n";
				$body .= "from ".$p->ipn_data['payer_email'].", send by asterbilling on ".date('m/d/Y');
				$body .= " at ".date('g:i A')."\n\n";
				$body .= $mailTitle."\n\nDetails:\n";

				foreach ($p->ipn_data as $key => $value) {
					if($key != '' && $key != 'custom')	$body .= "\n$key: $value"; 
				}
				mail($to, $subject, $body);				
			}
		 }		
    }
?>
<?  
	header('Content-Type: text/html; charset=utf-8');
	require_once('systemstatus.server.php');
	//require_once ('include/asterevent.class.php');

	$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'systemstatus');

	$reseller = astercc::readField('resellergroup','resellername','id',$_SESSION['curuser']['resellerid']);
	
	$callshop = astercc::readField('accountgroup','groupname','id',$_SESSION['curuser']['groupid']);
	
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

	if (strstr($_REQUEST['peer'],'Local/')) { //for callback
		$peer = ltrim($peer,'Local/');
		foreach ($_SESSION['callbacks'] as $key => $callback) {
			if( $key == $peer.$callback['legA'] && $callback['legB'] == $peer ){
				$leg = $callback['legA'];
			}
		}
	}else{
		$peer = trim($_REQUEST['peer']);
		$leg = trim($_REQUEST['leg']);
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Language" content="utf-8" />
		<LINK href="skin/default/css/layout.css" type=text/css rel=stylesheet>
		<LINK href="skin/default/css/dragresize.css" type=text/css rel=stylesheet>
		<TITLE> <? echo $locate->Translate("Receipt").'-'; echo $peer;?></TITLE>
	</head>
 <SCRIPT LANGUAGE="JavaScript">
		<!--

		-->
 </SCRIPT>
<style rel="stylesheet" type="text/css" media="all" />
	body {
	margin: 9px;
	padding: 0;
	color: black;
	text-decoration: none;
	font-size: 12px;
	font-family: "Courier New";
	}
</style>
 <BODY>
 <? if (isset($titleHtml)){
		$titleHtml .= '';
		echo '<div id="divReceiptTitle" name="divReceiptTitle" style="position:relative;top:2px;height:80px;">'.$titleHtml.'</div><div style="position:relative;left:0px;display:block;"><hr color="#F1F1F1"></div>';
	}
?> 
	<div id="divPrint" align="right">
		<input type="button" onclick="opener.btnClearOnClick('<? echo $peer; ?>',document.getElementById('payType').value);" value="<? echo $locate->Translate("Pay");?>">&nbsp;
		<? echo $locate->Translate("by");?>&nbsp;
		<select id="payType" name="payType">
			<option value="cash"><? echo $locate->Translate("Cash");?></option>
			<option value="credit card"><? echo $locate->Translate("Credit card");?></option>
			<option value="debit card"><? echo $locate->Translate("Debit card");?></option>
			<option value="promotion"><? echo $locate->Translate("Promotion");?></option>
			<option value="other"><? echo $locate->Translate("Other");?></option>
		</select>&nbsp;
		<input type="button" onclick="document.getElementById('divPrint').style.display='none';window.print();window.close();" value="<? echo $locate->Translate("Print");?>">&nbsp;&nbsp;
	</div>

	<div id="divMain" style="position:relative;">
	<div>&nbsp;<? echo $locate->Translate("Reseller");?>:&nbsp;<?echo $reseller;?>
	   <br>
	   &nbsp;<? echo $locate->Translate("Callshop");?>:&nbsp;<?echo $callshop;?>
	   <br>
	   &nbsp;<? echo $locate->Translate("Operator");?>:&nbsp;<?echo $_SESSION['curuser']['username'];?>
	   <? if($_REQUEST['customername'] != '') echo "<br>&nbsp;".$locate->Translate("Member")."&nbsp;:";?><?echo $_REQUEST['customername'];?>
	</div>
	</div>
	<div style="position:relative;">
  <table  width="100%" border="1" align="center" class="adminlist">
    <tr><td colspan="6">&nbsp;</td></tr>
	<tr>
		<th width="15%"><? echo $locate->Translate("Phone");?></th>		
		<th width="20%"><? echo $locate->Translate("Start at");?></th>
		<th width="10%" align="center"><? echo $locate->Translate("Sec");?></th>
		<th width="15%"><? echo $locate->Translate("Destination");?></th>
		<th width="20%"><? echo $locate->Translate("Rate");?></th>
		<th width="10%" align="center"><? echo $locate->Translate("Price");?></th>
		<th width="10%" align="center"><? echo $locate->Translate("Discount");?></th>
	</tr>
	<?
	
	  $total_price = 0;
	  $records = astercc::readUnbilled($peer,$leg,$_SESSION['curuser']['groupid']);
	  while	($records->fetchInto($myreceipt)) {
		  $ratedesc = astercc::readRateDesc($myreceipt['memo']).'&nbsp;';
		  $content = '<tr>';
		  if ($peer == $myreceipt['dst']){
			  if ($myreceipt['billsec'] == 0)
				$content .= '<td><img src="images/noanswer.gif">'.$myreceipt['src'].'</td>';
			  else
				$content .= '<td><img src="images/inbound.gif">'.$myreceipt['src'].'</td>';
		  }else{
			  if ($myreceipt['billsec'] == 0)
				$content .= '<td><img src="images/noanswer.gif">'.$myreceipt['dst'].'</td>';
			  else
				$content .= '<td><img src="images/outbound.gif">'.$myreceipt['dst'].'</td>';
		  }
		  $content .= '
					<td>'.$myreceipt['calldate'].'</td>
					<td align="right">'.$myreceipt['billsec'].'</td>
					<td align="right">'.$myreceipt['destination'].'</td>
					<td align="right">'.$ratedesc.'</td>
					<td align="right">'.astercc::creditDigits($myreceipt['credit']).'</td>
					<td align="right">'.astercc::creditDigits($_REQUEST['discount'],3).'</td>
				</tr>';
		  echo $content;
			$total_price += $myreceipt['credit'];
	  }
      $total_price = $total_price * (1-$_REQUEST['discount']);
	  $total_price = astercc::creditDigits($total_price,2);
	?>
	<tr><td><? echo $locate->Translate("Total");?>:</td>
		<td colspan="5" align="right"><? echo $total_price; ?></td>
	</tr>
  </table>
  <div id="copyright" style="background-repeat:repeat-x;height:64px;margin-top:10px;text-align:center;">
				<ul>
				<li>2007-2009 asterBilling - asterBilling home</li>
				<li>version: 0.097 </li>
				</ul>
  </div>
  </div>
 </BODY>
</html>

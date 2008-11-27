<?  
	header('Content-Type: text/html; charset=utf-8');
	require_once ("db_connect.php");
	require_once ('include/asterevent.class.php');

	$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'systemstatus');

	$reseller = astercc::readField('resellergroup','resellername','id',$_SESSION['curuser']['resellerid']);
	
	$callshop = astercc::readField('accountgroup','groupname','id',$_SESSION['curuser']['groupid']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Language" content="utf-8" />
		<LINK href="skin/default/css/layout.css" type=text/css rel=stylesheet>
		<LINK href="skin/default/css/dragresize.css" type=text/css rel=stylesheet>
		<TITLE> <? echo $locate->Translate("Receipt").'-'; echo $_REQUEST['peer'];?></TITLE>
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
	<div id="divPrint" align="right"><input type="button" onclick="document.getElementById('divPrint').style.display='none';window.print();window.close();" value="<? echo $locate->Translate("Print");?>">&nbsp;&nbsp;</div>

	<div>&nbsp;<? echo $locate->Translate("Reseller");?>:&nbsp;<?echo $reseller;?>
	   <br>
	   &nbsp;<? echo $locate->Translate("Callshop");?>:&nbsp;<?echo $callshop;?>
	   <br>
	   &nbsp;<? echo $locate->Translate("Operator");?>:&nbsp;<?echo $_SESSION['curuser']['username'];?>
	</div>

  <table  width="100%" border="1" align="center" class="adminlist">
    <tr><td colspan="5">&nbsp;</td></tr>
	<tr>
		<th width="15%"><? echo $locate->Translate("Phone");?></th>		
		<th width="25%"><? echo $locate->Translate("Start at");?></th>
		<th width="10%" align="center"><? echo $locate->Translate("Sec");?></th>
		<th width="15%"><? echo $locate->Translate("Destination");?></th>
		<th width="25%"><? echo $locate->Translate("Rate");?></th>
		<th width="10%" align="center"><? echo $locate->Translate("Price");?></th>		
	</tr>
	<?
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
	  $total_price = 0;
	  $records = astercc::readUnbilled($peer,$leg,$_SESSION['curuser']['groupid']);
	  while	($records->fetchInto($myreceipt)) {
		  $ratedesc = astercc::readRateDesc($myreceipt['memo']).'&nbsp;';
		  $content = '<tr>';
		  if ($peer == $myreceipt['dst'])
			  $content .= '<td>'.$myreceipt['src'].'-></td>';
		  else
			  $content .= '<td>->'.$myreceipt['dst'].'</td>';
		  $content .= '
					<td>'.$myreceipt['calldate'].'</td>
					<td align="right">'.$myreceipt['billsec'].'</td>
					<td align="right">'.$myreceipt['destination'].'</td>
					<td align="right">'.$ratedesc.'</td>
					<td align="right">'.astercc::creditDigits($myreceipt['credit']).'</td>
				</tr>';
		  echo $content;
			$total_price += $myreceipt['credit'];
	  }
	  $total_price = astercc::creditDigits($total_price,2);
	?>
	<tr><td><? echo $locate->Translate("Total");?>:</td>
		<td colspan="5" align="right"><? echo $total_price; ?></td>
	</tr>
  </table>
 </BODY>
</html>

<?
/*******************************************************************************
* astercc.class.php
* asterisk events class, read datas from table: curcdr
**/

class astercc extends PEAR
{
	function checkPeerStatus($groupid){
		$curChans =& astercc::getCurChan($groupid);
		$status =  array();
		while ($curChans->fetchInto($list)) {
			$status[$list['src']] = $list;
		}
		return $status;
	}

	function creditDigits($credit){
		return number_format($credit,2);
	}

	function setCreditLimit($channel,$creditlimit){
		global $db;
		$query = "UPDATE curcdr SET creditlimit = $creditlimit WHERE srcchan = '$channel'";
		astercc::events($query);
		$res = $db->query($query);
		return $db->affectedRows();
	}

	function getCallback($groupid){
		global $db;
		$query = "SELECT * FROM curcdr WHERE groupid = $groupid AND LEFT(srcchan,6) = 'Local/'";
		astercc::events($query);
		$res = $db->query($query);
		return $res;
	}

	function insertNewCallback($f){
		global $db;
		$sql= "INSERT INTO callback SET "
				."lega='".$f['lega']."', "
				."legb='".$f['legb']."', "
				."credit='".$f['credit']."',"
				."groupid='".$f['groupid']."', "
				."addtime= now() ";
		$res =& $db->query($sql);
		astercc::events($query);
		return $res;
	}

	function getCurChan($groupid){
		global $db;
		$query = "SELECT * FROM curcdr WHERE groupid = $groupid";
		#$condition = '';
		#foreach ($peers as $peer){
		#	$condition .= " src = '".$peer."' OR";
		#}
		#$query .= substr($condition,0,-2); // delete the last "AND"
		astercc::events($query);
		$res = $db->query($query);
		return $res;
	}

	function getAll($table){
		global $db;
		$query = "SELECT * FROM $table ";
		astercc::events($query);
		$res = $db->query($query);
		return $res;
	}

	function getCurLocalChan($chan, $groupid){
		global $db;
		$query = "SELECT * FROM curcdr WHERE srcchan LIKE '$chan%' AND groupid = $groupid ORDER BY starttime ASC";
//		print $query;
//		exit;
		astercc::events($query);
		$res = $db->query($query);
		return $res;
	}

	function events($event = null){
		if(LOG_ENABLED){
			$now = date("Y-M-d H:i:s");
   		$fd = fopen (FILE_LOG,'a');
			$log = $now." ".$_SERVER["REMOTE_ADDR"] ." - $event \n";
	   	fwrite($fd,$log);
   		fclose($fd);
		}
	}

	function readRate($dst,$groupid, $tbl = 'myrate', $secondGroupid = null){
		global $db;
		$dst = trim($dst);
		if ($secondGroupid != null){
			$sql = "SELECT * FROM $tbl WHERE groupid = $secondGroupid";
		}else{
			$sql = "SELECT * FROM $tbl WHERE groupid = $groupid";
		}
		
		astercc::events($sql);
		$rates = & $db->query($sql);

		$maxprefix = '';
		$myrate = array();
		$default = '';

		while ($rates->fetchInto($list)) {
			if ($list['dialprefix'] == 'default'){
				$default = $list;
				continue;
			}

			$prefixlength = strlen($list['dialprefix']);
			if (substr($dst,0,$prefixlength) == $list['dialprefix']){
				if ($prefixlength > strlen($maxprefix)){
					$myrate = $list;
					$maxprefix = $list['dialprefix'];
				}
			}
		}
		

		if ($secondGroupid != null && $maxprefix == '' && $default ==''){ // did get rate from group
//			print "123456";
//			exit;
			return astercc::readRate($dst,$groupid, $tbl);
		}

		if ($maxprefix == ''){
			return $default;
		}else{
			return $myrate;
		}
	}

	function setBilled($id){
		global $db;
		$sql ="UPDATE mycdr SET userfield ='billed' WHERE id =$id ";
		astercc::events($sql);
		$res = $db->query($sql);
		return $res;
	}

	function readUnbilled($peer,$leg = null,$groupid){
		global $db;

		if ($leg == null){
			$query = "SELECT * FROM mycdr WHERE src = '$peer' AND userfield = 'UNBILLED' AND groupid = $groupid ORDER BY calldate";
		}else{
			/*
			$query = 'SELECT * FROM cdr WHERE 
				src = "'.$peer.' AND dst="'.$leg.'"" 
				AND userfield = "UNBILLED" ORDER BY calldate';
				*/
			$query = "SELECT * FROM mycdr WHERE channel LIKE 'Local/$peer%' AND src = '$leg' AND userfield = 'UNBILLED' AND groupid = $groupid ORDER BY calldate";
			//	print $query;
			//	exit;
		}
		astercc::events($query);
		$res = $db->query($query);
		return $res;
	}

function readAll($peer,$groupid,$sdate = null , $edate = null){
	global $db;
	if ($peer == 'callback'){
		if ($groupid == -1)
			$query = "SELECT * FROM mycdr WHERE LEFT(channel,6) = 'Local/' ";
		else
			$query = "SELECT * FROM mycdr WHERE groupid = $groupid AND LEFT(channel,6) = 'Local/' ";
	}else{
		if ($groupid == -1)
			$query = "SELECT * FROM mycdr WHERE src LIKE '$peer%' ";
		else
			$query = "SELECT * FROM mycdr WHERE src LIKE '$peer%' AND groupid = $groupid ";
	}
	
	if ($sdate != null){
		$query .= " AND calldate >= '$sdate' ";
	}

	if ($edate != null){
		$query .= " AND calldate <= '$edate' ";
	}

	$query .= " ORDER BY calldate";
	astercc::events($query);
	$res = $db->query($query);
	return $res;
}

	function readAmount($groupid,$peer = null, $sdate = null, $edate = null){
		global $db;
		$curYear = Date("Y");
		$curMonth = Date("m");

		if ($sdate == null){
			$sdate = "$curYear-$curMonth-01 00:00:00";
		}

		if ($edate == null){
			$edate = "$curYear-$curMonth-31 23:59:59";
		}

		if ($peer == null)
			$query = "SELECT SUM(credit) FROM mycdr WHERE groupid = $groupid AND calldate >= '$sdate' AND calldate <= '$edate' ";
		else
			$query = "SELECT SUM(credit) FROM mycdr WHERE groupid = $groupid AND calldate >= '$sdate' AND calldate <= '$edate' ";

		astercc::events($query);
		$one = $db->getOne($query);
		return $one;
	}


	function readRateDesc($rate){

		if ($rate['initblock'] != 0){
			$desc .= floor($rate['connectcharge']*100)/100 . ' for first ' . $rate['initblock'] . ' seconds <br/>';
		}
		if ($rate['billingblock'] != 0){
			$desc .= floor(($rate['billingblock'] * $rate['rateinitial'] / 60)*100)/100 . ' per ' . $rate['billingblock'] . ' seconds';
		}
		return $desc;
	}

	function calculatePrice($billsec,$rate){

		$destination = $rate['destination'];
		$rateinitial = $rate['rateinitial'];
		$initblock	 = $rate['initblock'];
		$billingblock = $rate['billingblock'];
		
		if ($billsec > 0 ){
	
			$price += $rate['connectcharge'];
			$billsec -= $rate['initblock'];
	

			if ($billsec > 0){
				if ($rate['billingblock'] != 0){
					if ($billsec % $rate['billingblock'] != 0 )
						$billblock = intval($billsec / $rate['billingblock']) + 1;
					else
						$billblock = intval($billsec / $rate['billingblock']);
				}else{
				}
					
				$price += $billblock * ($rate['billingblock'] * $rate['rateinitial']/60);
			}
		}

		return $price;
	}

	function calculateLimitSec($creditLimit,$rate){

		$destination = $rate['destination'];
		$rateinitial = $rate['rateinitial'];
		$initblock	 = $rate['initblock'];
		$billingblock = $rate['billingblock'];
		
		if ($billsec > 0 ){
	
			$price += $rate['connectcharge'];
			$billsec -= $rate['initblock'];
	

			if ($billsec > 0){
				if ($rate['billingblock'] != 0){
					if ($billsec % $rate['billingblock'] != 0 )
						$billblock = intval($billsec / $rate['billingblock']) + 1;
					else
						$billblock = intval($billsec / $rate['billingblock']);
				}else{
				}
					
				$price += $billblock * ($rate['billingblock'] * $rate['rateinitial']/60);
			}
		}

		return $price;
	}
}
?>
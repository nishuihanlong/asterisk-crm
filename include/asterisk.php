<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'phpagi-asmanager.php');

class Asterisk extends AGI_AsteriskManager{

	function dropCall($sID,$arrayPara){

		$callfile = "";
		$callfile = $callfile."Channel:".$arrayPara['Channel']."\r\n";
		$callfile = $callfile."WaitTime:".$arrayPara['WaitTime']."\r\n";
		$callfile = $callfile."Extension:".$arrayPara['Exten']."\r\n";
		$callfile = $callfile."Context:".$arrayPara['Context']."\r\n";
		$callfile = $callfile."Priority:".$arrayPara['Priority']."\r\n";
		$callfile = $callfile."MaxRetries:".$arrayPara['MaxRetries']."\r\n";
		$callfile = $callfile."CallerID:".$arrayPara['CallerID']."\r\n";
		$callfile = $callfile."ActionID:".$arrayPara['ActionID']."\r\n";

		if ($arrayPara['Variable'] != '')
			foreach ( split("\|",$arrayPara['Variable']) as $strVar)
				$callfile = $callfile."SetVar: $strVar\r\n";


		$filename="/tmp/$sID.call";
		$handle=fopen($filename,"w+");
		fwrite($handle,$callfile);


		system("mv $filename /var/spool/asterisk/outgoing/");
		return ;
	}

	function getSipChannels(){
		global $config;
		$myAsterisk = new Asterisk();
		$myAsterisk->config['asmanager'] = $config['asterisk'];
		$res = $myAsterisk->connect();
		$channels = $myAsterisk->Command("sip show channels");	
		return  $channels['data'];
	}

	function getChannels($verbose = null){
		global $config;
		$myAsterisk = new Asterisk();
		$myAsterisk->config['asmanager'] = $config['asterisk'];
		$res = $myAsterisk->connect();
		$channels = $myAsterisk->Command("show channels");	
		return  $channels['data'];
	}

	function getCommandData($command){
		global $config;
		$myAsterisk = new Asterisk();
		$myAsterisk->config['asmanager'] = $config['asterisk'];
		$res = $myAsterisk->connect();
		$channels = $myAsterisk->Command("show channels verbose");	
		return  $channels['data'];
	}
}
?>
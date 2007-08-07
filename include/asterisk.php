<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'phpagi-asmanager.php');

class Asterisk extends AGI_AsteriskManager{
	function Asterisk(){
		global $asmanager;
		$this->config = $asmanager;
	}

	function dropCall($sID,$arrayPara){

		$callfile = "";
		$callfile = $callfile."Channel:".$arrayPara['Channel']."\r\n";
		$callfile = $callfile."WaitTime:".$arrayPara['WaitTime']."\r\n";
		$callfile = $callfile."Extension:".$arrayPara['Exten']."\r\n";
		$callfile = $callfile."Context:".$arrayPara['Context']."\r\n";
		$callfile = $callfile."Priority:".$arrayPara['Priority']."\r\n";
		$callfile = $callfile."MaxRetries:".$arrayPara['MaxRetries']."\r\n";

		if ($arrayPara['Variable'] != '')
			foreach ( split("\|",$arrayPara['Variable']) as $strVar)
				$callfile = $callfile."SetVar: $strVar\r\n";


		$filename="/tmp/$sID.call";
		$handle=fopen($filename,"w+");
		fwrite($handle,$callfile);


		system("mv $filename /var/spool/asterisk/outgoing/");
		return ;
	}
}
?>
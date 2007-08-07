<html>
<head>
<title>Click-to-Call</title>
</head>
<body>
<?
#Click-To-Call script brought to you by VoipJots.com


#------------------------------------------------------------------------------------------
#edit the below variable values to reflect your system/information
#------------------------------------------------------------------------------------------

#specify the name/ip address of your asterisk box
#if your are hosting this page on your asterisk box, then you can use
#127.0.0.1 as the host IP.  Otherwise, you will need to edit the following
#line in manager.conf, under the Admin user section:
#permit=127.0.0.1/255.255.255.0
#change to:
#permit=127.0.0.1/255.255.255.0,xxx.xxx.xxx.xxx ;(the ip address of the server this page is running on)
$strHost = "210.83.203.100";

#specify the username you want to login with (these users are defined in /etc/asterisk/manager.conf)
#this user is the default AAH AMP user; you shouldn't need to change, if you're using AAH.
$strUser = "solo";

#specify the password for the above user
$strSecret = "123654";

#specify the channel (extension) you want to receive the call requests with
#e.g. SIP/XXX, IAX2/XXXX, ZAP/XXXX, etc
$strChannel = "SIP/8701";

#specify the context to make the outgoing call from.  By default, AAH uses from-internal
#Using from-internal will make you outgoing dialing rules apply
$strContext = "from-sipuser";

#specify the amount of time you want to try calling the specified channel before hangin up
$strWaitTime = "30";

#specify the priority you wish to place on making this call
$strPriority = "1";

#specify the maximum amount of retries
$strMaxRetry = "2";

#--------------------------------------------------------------------------------------------
#Shouldn't need to edit anything below this point to make this script work
#--------------------------------------------------------------------------------------------
#get the phone number from the posted form
$strExten = $_POST['txtphonenumber'];

#specify the caller id for the call
$strCallerId = "Web Call <$strExten>";

$length = strlen($strExten);

if ($length > 3 && is_numeric($strExten))
{
$oSocket = fsockopen($strHost, 7998, $errnum, $errdesc) or die("Connection to host failed");
fputs($oSocket, "Action: login\r\n");
fputs($oSocket, "Events: off\r\n");
fputs($oSocket, "Username: $strUser\r\n");
fputs($oSocket, "Secret: $strSecret\r\n\r\n");
fputs($oSocket, "Action: originate\r\n");
fputs($oSocket, "Channel: $strChannel\r\n");
fputs($oSocket, "WaitTime: $strWaitTime\r\n");
fputs($oSocket, "CallerId: $strCallerId\r\n");
fputs($oSocket, "Exten: $strExten\r\n");
fputs($oSocket, "Context: $strContext\r\n");
fputs($oSocket, "Priority: $strPriority\r\n\r\n");
fputs($oSocket, "Action: Logoff\r\n\r\n");
fclose($oSocket);
?>
<p>
<table width="300" border="1" bordercolor="#630000" cellpadding="3" cellspacing="0">
	<tr><td>
	<font size="2" face="verdana,georgia" color="#630000">We are currently trying to call you.  Please be patient, and wait for
	your phone to ring!<br>If your phone does not ring after 2 minutes, we apologize, but must either be out, or
already on the
	phone.<br><a href="<? echo $_SERVER['PHP_SELF'] ?>">Try Again</a></font>
	</td></tr>
</table>
</p>
<?
}
else
{
?>
<p>
<table width="300" border="1" bordercolor="#630000" cellpadding="3" cellspacing="0">
	<tr><td>
	<font size="2" face="verdana,arial,georgia" color="#630000">Enter your phone number (1XXXXXXXXXX), and we will call you a
	few seconds later!</font>
	<form action="<? echo $_SERVER['PHP_SELF'] ?>" method="post">
		<input type="text" size="20" maxlength="11" name="txtphonenumber"><br>
		<input type="submit" value="Call Us!">
	</form>
	</td></tr>
</table>
</p>
<?
}
?>
</body>
</html>

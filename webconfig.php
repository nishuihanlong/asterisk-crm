<?php
error_reporting(0);
require_once ('config.php');
require_once ('include/localization.class.php');
require_once ('include/xajax.inc.php');
$xajax = new xajax();
$xajax->registerFunction("sellan");
$xajax->registerFunction("addInput");
$xajax->processRequest();

function sellan($language)
{
  global $locate;
  list($_SESSION['curuser']['country'],$_SESSION['curuser']['language'])=split ("_", $language);
  $objResponse = new xajaxResponse();
  //$objResponse->addAlert("$language");
	$locate=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'webconfig');			//init localization class
	$objResponse->addAssign("db_account","innerHTML",$locate->Translate('db_account'));
	$objResponse->addAssign("db_host","innerHTML",$locate->Translate('db_host'));
	$objResponse->addAssign("db_type","innerHTML",$locate->Translate('db_type'));
	$objResponse->addAssign("db_name","innerHTML",$locate->Translate('db_name'));
	$objResponse->addAssign("db_user","innerHTML",$locate->Translate('db_user'));
	$objResponse->addAssign("db_pass","innerHTML",$locate->Translate('db_pass'));
	$objResponse->addAssign("admin_account","innerHTML",$locate->Translate('admin_account'));
	$objResponse->addAssign("admin_name","innerHTML",$locate->Translate('admin_name'));
	$objResponse->addAssign("as_port","innerHTML",$locate->Translate('db_port'));
	$objResponse->addAssign("as_user","innerHTML",$locate->Translate('db_user'));
	$objResponse->addAssign("as_pass","innerHTML",$locate->Translate('db_pass'));
	$objResponse->addAssign("as_host","innerHTML",$locate->Translate('as_host'));
	$objResponse->addAssign("ly_path","innerHTML",$locate->Translate('ly_path'));
	$objResponse->addAssign("ly_format","innerHTML",$locate->Translate('ly_format'));
	$objResponse->addAssign("logde","innerHTML",$locate->Translate('logde'));
	$objResponse->addAssign("logen","innerHTML",$locate->Translate('logen'));
	$objResponse->addAssign("mix_sys","innerHTML",$locate->Translate('mix_sys'));
	$objResponse->addAssign("log_enabled","innerHTML",$locate->Translate('log_enabled'));
	$objResponse->addAssign("log_file_path","innerHTML",$locate->Translate('log_file_path'));
	$objResponse->addAssign("outcontext","innerHTML",$locate->Translate('outcontext'));
	$objResponse->addAssign("incontext","innerHTML",$locate->Translate('incontext'));
	$objResponse->addAssign("sub","value",$locate->Translate('dbsub'));
	return $objResponse;
}


 function write_ini_file($path, $assoc_array)
{
    $content = '';
    $sections = '';
    foreach ($assoc_array as $key => $item)
    {
        if (is_array($item))
        {
            $sections .= "\n[{$key}]\n";
            foreach ($item as $key2 => $item2)
            {
                if (is_numeric($item2) || is_bool($item2))
                    $sections .= "{$key2} = {$item2}\n";
                else
                    $sections .= "{$key2} = \"{$item2}\"\n";
            }      
        }
        else
        {
            if(is_numeric($item) || is_bool($item))
                $content .= "{$key} = {$item}\n";
            else
                $content .= "{$key} = \"{$item}\"\n";
        }
    }      

    $content .= $sections;

    if (!$handle = fopen($path, 'w'))
    {
        return false;
    }
   
    if (!fwrite($handle, $content))
    {
        return false;
    }
   
    fclose($handle);
    return true;
} 
?>
	<script type="text/javascript">
		function sellan(){
			xajax_sellan('cn_ZH');
			return false;
		}
		</script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>astercrm</title>
<?php $xajax->printJavascript('include/'); ?>
<link href="skin/default/css/webconfig.css" rel="stylesheet" type="text/css" />
</head>
<body id="checking" onload="sellan()" >
<div id="logos">
  <div id="logos-inside">astrerCRM </div>
<div id="lang-menu">
  <div id="lang-menu-inside"><img src="skin/default/images/cn.gif" " />
    <input type="radio" name="js-lang" id="js-zh_cn" class="p" checked='checked'  onclick="xajax_sellan('cn_ZH'); return true;"/>
    <label for="js-zh_cn">简体中文</label>&nbsp;&nbsp;&nbsp;&nbsp;
    <img src="skin/default/images/us.gif" />
    <input type="radio" name="js-lang" id="js-en_us" class="p"  onclick="xajax_sellan('en_US'); return true;"/>
    <label for="js-en_us">English</label>
  </div>
</div>
</div>
<form id="js-setting" action="" method="POST">

<table border="0" cellpadding="0" cellspacing="0" style="margin:0 auto;">
<tr>
<td valign="top">
<div id="wrapper">
 <h3><div id="db_account" ></div></h3> 

<table width="450" class="list" border="0" align="CENTER">
<tr>
	<td width="90" align="left" ><div id="db_host" ></div></td>
	<td align="left"><input type="text" name="js-db-host"  value="<?echo $config['database']['dbhost'];?>" /></td>
		
</tr>
<tr>
	<td width="90" align="left"><div id="db_name"></div></td>
	<td align="left"><input type="text" name="js-db-name"  value="<?echo $config['database']['dbname'];?>" />
   </td>
</tr>
<tr>
	<td width="90" align="left"><div id="db_type"></div></td>
	<td align="left"><input type="text" name="js-db-type"  value="<?echo $config['database']['dbtype'];?>" /></td>
</tr>
<tr>
	<td width="90" align="left"><div id="db_user"></div></td>
	<td align="left"><input type="text" name="js-db-user"  value="<?echo $config['database']['username'];?>" /></td>
</tr>
<tr>
	<td width="90" align="left"><div id="db_pass"></div></td>
	<td align="left"><input type="password" name="js-db-pass"  value="<?echo $config['database']['password'];?>" /></td>
</tr>


</table align="CENTER">
<h3 align="CENTER"><div id="admin_account"></div></h3>
<table width="450" class="list" align="CENTER">
<tr>
		<td width="90" align="left"><div id="as_host"></div></td>
	<td align="left"><input type="text" name="js-as-host"  value="<?php echo $config['asterisk']['server'];?>"/></td>
</tr>
<tr>
		<td width="90" align="left"><div id="as_port"></div></td>
	<td align="left"><input type="text" name="js-as-port"  value="<?echo $config['asterisk']['port'];?>" /></td>
</tr>
<tr>
	<td width="90" align="left"><div id="as_user"></div></td>
	<td align="left"><input type="password" name="js-as-user"  value="<?echo $config['asterisk']['username'];?>" /></td>
</tr>
<tr>
	<td width="90" align="left"><div id="as_pass"></div></td>
	<td align="left"><input type="text" name="js-as-pass"  value="<?echo $config['asterisk']['secret'];?>" /></td>
</tr>
<tr> 
	<td width="90" align="left"><div id="ly_path"></div></td>
	<td align="left"><input type="text" name="ly_path"  value="<?echo $config['asterisk']['monitorpath'];?>" /></td>
</tr>
<tr>
	<td width="90" align="left"><div id="ly_format"></div></td>
	<td align="left"><input type="text" name="ly_format"  value="<?echo $config['asterisk']['monitorformat'];?>" /></td>
</tr>
</table>

<h3><div id="mix_sys"></div></h3>
<table width="450" class="list" align="CENTER">
<tr>
	<td width="100" align="left"><div id="log_enabled"></div></td>
	<td align="left">
  <SELECT NAME="log" SIZE="1">
<OPTION VALUE="1" SELECTED>Enabled</OPTION>
<OPTION VALUE="0">Disabled</OPTION>
</SELECT></td>
</tr>
<tr>
	<td width="100" align="left"><div id="log_file_path"></div></td>
	<td align="left"><input type="text" name="log_file_path"  value="<?echo $config['system']['log_file_path'];?>" /></td>
</tr>
<tr>
	<td width="100" align="left"><div id="outcontext"></div></td>
	<td align="left"><input type="text" name="outcontext"  value="<?echo $config['system']['outcontext'];?>" /></td>
</tr>
<tr>
	<td width="90" align="left"><div id="incontext"></div></td>
	<td align="left"><input type="text" name="incontext"  value="<?echo $config['system']['incontext'];?>" /></td>
</tr>
</table>
 <div id="submittedDiv"></div>
<input type="Hidden" name="Hidden" value="1" > 
<input type="submit" name="sub" id="sub" value="" align="CENTER" onclick="xajax_addInput(xajax.getFormValues('js-setting')); return false";>

</form>
</body>
</html>
<?php 

function addInput($aInputData){
global $config,$webcofig;
    $database_type = trim($aInputData['js-db-type']);
    $database_host = trim($aInputData['js-db-host']);
    $database_name = trim($aInputData['js-db-name']);
    $database_user = trim($aInputData['js-db-user']);
    $database_pass = trim($aInputData['js-db-pass']);
    //$asterisk = '[asterisk]';
    $asterisk_host = trim($aInputData['js-as-host']);
    $asterisk_port = trim($aInputData['js-as-port']);
    $asterisk_user = trim($aInputData['js-as-user']);
    $asterisk_pass = trim($aInputData['js-as-pass']);
    $asterisk_path = trim($aInputData['ly_path']);
    $asterisk_format = trim($aInputData['ly_format']);
    //$asterisk = '[system]';
    $asterisk_log = trim($aInputData['log']);
    $asterisk_path = trim($aInputData['log_file_path']);
    $asterisk_outcontext = trim($aInputData['outcontext']);
    $asterisk_incontext = trim($aInputData['incontext']);
    $config['database']['server'] = $database_host ;
    $config['database']['dbtype'] = $database_type ;
    $config['database']['username'] = $database_user ;
    $config['database']['password'] = $database_pass ;

    $config['asterisk']['server'] = $asterisk_host ;
    $config['asterisk']['port'] = $asterisk_port ;
    $config['asterisk']['username'] = $asterisk_user ;
    $config['asterisk']['secret'] = $asterisk_pass;
    $config['asterisk']['monitorpath'] = $asterisk_path ;
    $config['asterisk']['monitorformat'] = $asterisk_format ;
    
    $config['system']['log_enabled'] = $asterisk_log ;
    $config['system']['log_file_path'] = $asterisk_path ;
    $config['system']['outcontext'] = $asterisk_outcontext ;
    $config['system']['incontext'] = $asterisk_incontext ;
    $ini_w = write_ini_file('info.ini',$config);
    $objResponse = new xajaxResponse();
	  //$objResponse->alert("inputData: " . print_r($aInputData, true));
	  $ok = $webcofig->Translate('inok');
	  $no = $webcofig->Translate('inno');
	  if ($ini_w){
    $objResponse->assign("submittedDiv", "innerHTML","$ok");
    }else {
    $objResponse->assign("submittedDiv", "innerHTML","$no");
    }
	 return $objResponse;
}

?>

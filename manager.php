<?php
require_once('manager.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<LINK href="css/style.css" type=text/css rel=stylesheet>
		<meta http-equiv="Content-Language" content="utf-8" />
	</head>
	<body>

	<div id="panel" name="panel">
		<input type="button" value="Extension Manager" onclick="xajax_showGrid(0,<?=ROWSXPAGE?>,'','','');"><br/>
		<input type="button" value="System Monitor" onclick="">
	</div>
		
		<div id="formWrapper">
			<div id="formDiv" name="formDiv" class="formDiv">
			</div>
		</div>

		<div id="grid" name="grid" align="center"> </div>
		<div id="msgZone" name="msgZone" align="center"> </div>
		
		<div id="outputDiv">
		</div>
	</body>
</html>
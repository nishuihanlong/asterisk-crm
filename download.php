<?
header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0',false);
header('Pragma: no-cache');
session_cache_limiter('public, no-store');

session_set_cookie_params(0);
if (!session_id()) session_start();
setcookie('PHPSESSID', session_id());


if ($_SESSION['curuser']['extension'] == '' or  $_SESSION['curuser']['usertype'] != 'admin') 
	header("Location: portal.php");

require_once ("db_connect.php");
require_once ('astercrm.php');


//$filename = $_REQUEST['filename'];
$type = $_REQUEST['type'];
ob_start();
header('Content-type:  application/force-download');
header('Content-Transfer-Encoding:  Binary');
header('Content-disposition:  attachment; filename='.$type.'.csv');


/*
$res = Common::export($GLOBALS['db'],$sql);
	while ($res->fetchInto($row)) {
		foreach ($row as $val){
			$val .= ',';
			if ($val != mb_convert_encoding($val,"UTF-8","UTF-8"))
					$val=mb_convert_encoding($val,"UTF-8","GB2312");
			
			$txtstr .= $val;
		}
		$txtstr .= "\n";
 	}
*/
echo astercrm::exportCSV($type);
ob_end_flush();

?>
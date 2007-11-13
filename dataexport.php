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
require_once ('include/astercrm.class.php');
$sql = $_REQUEST['hidSql'];
if ($sql != mb_convert_encoding($sql,"UTF-8","UTF-8"))
	$sql='"'.mb_convert_encoding($sql,"UTF-8","GB2312").'"';
ob_start();
header('Content-type:  application/force-download');
header('Content-Transfer-Encoding:  Binary');
header('Content-disposition:  attachment; filename=astercrm.csv');
echo astercrm::exportDataToCSV($sql);
ob_end_flush();

?>
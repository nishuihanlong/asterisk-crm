<?php
/*******************************************************************************
* db_connect.php
* 数据库连接文件,
* database connect file
* 功能描述
	使用PEAR连接数据库, 定义了一个全局数据连接变量: $db
* Function Desc
	use PEAR to connect database, define a global database variable: $db


* Revision 0.044  2007/09/7 17:55:00  last modified by solo
* Desc: add some comments
* 描述: 增加了一些注释信息

********************************************************************************/

// If you have the PEAR PHP package, you can comment the next line.
// 在这里设定包含PEAR文件的路径
ini_set('include_path',dirname($_SERVER["SCRIPT_FILENAME"])."/include");

require_once 'DB.php';
require_once 'PEAR.php';
require_once 'config.php';

// define database connection string
// 定义数据库连接字符串
define('SQLC', $config['database']['dbtype']."://".$config['database']['username'].":".$config['database']['password']."@".$config['database']['dbhost']."/".$config['database']['dbname']."");

// set a global variable to save database connection
// 定义全局变量保存数据库连接
$GLOBALS['db'] = DB::connect(SQLC);

// need to check if db connected
// 检查是否连接成功
if (DB::iserror($GLOBALS['db'])){
	die($GLOBALS['db']->getmessage());
}

// change database fetch mode
// 更改数据存取方式
$GLOBALS['db']->setFetchMode(DB_FETCHMODE_ASSOC);

?>
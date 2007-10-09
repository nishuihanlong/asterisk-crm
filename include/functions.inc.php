<?php
/*******************************************************************************
* functions.inc.php
* 公共函数文件
* public functions file
* 功能描述
* Function Desc

* Revision 0.045  2007/10/9 modified by solo
* Desc: page create
* 描述: 页面建立

********************************************************************************/

class Common extends PEAR
{

/**
*  data
*
*  	@param $db		(object)	database handle
*	@param $sql		(string)	sql query string
*	@return $res
*/
	function export($db,$sql){
		$res =& $db->query($sql);
		return $res;
	}
}
?>

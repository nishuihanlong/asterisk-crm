<?
/*******************************************************************************
* portal.grid.inc.php
* portal操作类
* Customer class

* @author			Solo Fu <solo.fu@gmail.com>
* @classVersion		1.0
* @date				18 Oct 2007

* Functions List

	getAllRecords				获取所有记录
	getRecordsFiltered			获取记录集
	getNumRows					获取记录集条数

* Revision 0.045  2007/10/18 15:11:00  last modified by solo
* Desc: deleted function getRecordByID

* Revision 0.045  2007/10/18 13:30:00  last modified by solo
* Desc: page created

********************************************************************************/

require_once 'db_connect.php';
require_once 'portal.common.php';
require_once 'include/astercrm.class.php';

class Customer extends astercrm
{

	/**
	*  Obtiene todos los registros de la tabla paginados.
	*
	*  	@param $start	(int)	Inicio del rango de la p&aacute;gina de datos en la consulta SQL.
	*	@param $limit	(int)	L&iacute;mite del rango de la p&aacute;gina de datos en la consultal SQL.
	*	@param $order 	(string) Campo por el cual se aplicar&aacute; el orden en la consulta SQL.
	*	@return $res 	(object) Objeto que contiene el arreglo del resultado de la consulta SQL.
	*/
	function &getAllRecords($start, $limit, $order = null, $creby = null){
		global $db,$config;

		if ($config['system']['portal_display_type'] == "note"){
			$sql = "SELECT note.id AS id,note.contactid AS contactid,note.customerid AS customerid,note.attitude as attitude, note, priority,customer.customer AS customer,contact.contact AS contact,customer.category AS category,note.cretime AS cretime,note.creby AS creby FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid ";

			$sql .= " WHERE priority>0 AND note.creby = '".$_SESSION['curuser']['username']."' ";

		}else{
			$sql = "SELECT customer.id,
							customer.customer AS customer,
							note.note AS note,
							note.priority AS priority,
							note.attitude AS attitude,
							customer.category AS category,
							customer.contact AS contact,
							customer.cretime as cretime
							FROM customer LEFT JOIN note ON customer.id = note.customerid";

			$sql .= " WHERE customer.creby = '".$_SESSION['curuser']['username']."' ";
		}

		if($order == null){
			$sql .= " ORDER BY cretime DESC LIMIT $start, $limit";//.$_SESSION['ordering'];
		}else{
			$sql .= " ORDER BY $order ".$_SESSION['ordering']." LIMIT $start, $limit";
		}

		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}
	
	/**
	*  Obtiene todos registros de la tabla paginados y aplicando un filtro
	*
	*  @param $start		(int) 		Es el inicio de la p&aacute;gina de datos en la consulta SQL
	*	@param $limit		(int) 		Es el limite de los datos p&aacute;ginados en la consultal SQL.
	*	@param $filter		(string)	Nombre del campo para aplicar el filtro en la consulta SQL
	*	@param $content 	(string)	Contenido a filtrar en la conslta SQL.
	*	@param $order		(string) 	Campo por el cual se aplicar&aacute; el orden en la consulta SQL.
	*	@return $res		(object)	Objeto que contiene el arreglo del resultado de la consulta SQL.
	*/

	function &getRecordsFiltered($start, $limit, $filter = null, $content = null, $order = null, $ordering = ""){
		global $db,$config;
		
		if(($filter != null) and ($content != null)){
			if ($config['system']['portal_display_type'] == "note"){

				$sql = "SELECT note.id AS id, note, priority,customer.customer AS customer,contact.contact AS contact,customer.category AS category,note.cretime AS cretime,note.creby AS creby FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid"
					." WHERE ".$filter." like '%".$content."%' AND priority>0 "
					." AND  note.creby = '".$_SESSION['curuser']['username']."' "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
			}else{
				$sql = "SELECT customer.id AS id,
							customer.customer AS customer,
							customer.category AS category,
							customer.contact AS contact,
							customer.cretime as cretime,
							note.note AS note,
							note.priority AS priority,
							note.attitude AS attitude
						FROM customer
						LEFT JOIN note ON customer.id = note.customerid"
					." WHERE ".$filter." like '%".$content."%'"
					." AND  customer.creby = '".$_SESSION['curuser']['username']."' "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
			}
		}
		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function &getRecordsFilteredMore($start, $limit, $filter, $content, $order,$table, $ordering = ""){
		global $db;

		$i=0;
		$joinstr='';
		foreach ($content as $value){
			$value=trim($value);
			if (strlen($value)!=0 && strlen($filter[$i]) != 0){
				$joinstr.="AND $filter[$i] like '%".$value."%' ";
			}
			$i++;
		}
		if ($config['system']['portal_display_type'] == "note"){
				if ($joinstr!=''){
					$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
						$sql = "SELECT note.id AS id, note, priority,customer.customer AS customer,contact.contact AS contact,customer.category AS category,note.cretime AS cretime,note.creby AS creby FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid"
						." WHERE ".$joinstr."  AND priority>0 "
						." AND  note.creby = '".$_SESSION['curuser']['username']."' "
						." ORDER BY ".$order
						." ".$_SESSION['ordering']
						." LIMIT $start, $limit $ordering";
				}else {
					$sql = "SELECT note.id AS id, note, priority,customer.customer AS customer,contact.contact AS contact,customer.category AS category,note.cretime AS cretime,note.creby AS creby FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid"
						." ORDER BY ".$order
						." ".$_SESSION['ordering']
						." LIMIT $start, $limit $ordering";
				}
			}else{
				if ($joinstr!=''){
					$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
						$sql = "SELECT customer.id AS id,
							customer.customer AS customer,
							customer.category AS category,
							customer.contact AS contact,
							customer.cretime as cretime,
							note.note AS note,
							note.priority AS priority,
							note.attitude AS attitude
						FROM customer
						LEFT JOIN note ON customer.id = note.customerid"
					." WHERE ".$joinstr." "
					." AND  customer.creby = '".$_SESSION['curuser']['username']."' "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
				}else {
					$sql = "SELECT customer.id AS id,
							customer.customer AS customer,
							customer.category AS category,
							customer.contact AS contact,
							customer.cretime as cretime,
							note.note AS note,
							note.priority AS priority,
							note.attitude AS attitude
						FROM customer
						LEFT JOIN note ON customer.id = note.customerid"
					." AND  customer.creby = '".$_SESSION['curuser']['username']."' "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
				}
			}
		echo $sql;
		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}
	
	/**
	*  Devuelte el numero de registros de acuerdo a los par&aacute;metros del filtro
	*
	*	@param $filter	(string)	Nombre del campo para aplicar el filtro en la consulta SQL
	*	@param $order	(string)	Campo por el cual se aplicar&aacute; el orden en la consulta SQL.
	*	@return $row['numrows']	(int) 	N&uacute;mero de registros (l&iacute;neas)
	*/
	
	function &getNumRows($filter = null, $content = null){
		global $db,$config;
		if ($config['system']['portal_display_type'] == "note"){

			$sql = "SELECT COUNT(*) AS numRows FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid  WHERE priority>0  AND note.creby = '".$_SESSION['curuser']['username']."'";
				
			if(($filter != null) and ($content != null)){
				$sql = 	"SELECT COUNT(*) AS numRows "
					."FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid "
					." AND  note.creby = '".$_SESSION['curuser']['username']."' "
					."WHERE ".$filter." like '%".$content."%'";
			}
		}else{

			$sql = "SELECT COUNT(*) AS numRows FROM customer 
						LEFT JOIN note ON customer.id = note.customerid  WHERE customer.creby = '".$_SESSION['curuser']['username']."'";
				
			if(($filter != null) and ($content != null)){
				$sql = 	"SELECT COUNT(*) AS numRows "
					." FROM customer "
					." WHERE "
					."customer.creby = '".$_SESSION['curuser']['username']."'"
					." AND "
					.$filter." like '%".$content."%'";
			}

		}
		
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}

	function &getNumRowsMore($filter = null, $content = null,$table){
		global $db;
		
			$i=0;
			$joinstr='';
			foreach ($content as $value){
				$value=trim($value);
				if (strlen($value)!=0 && strlen($filter[$i]) != 0){
					$joinstr.="AND $filter[$i] like '%".$value."%' ";
				}
				$i++;
			}
			if ($config['system']['portal_display_type'] == "note"){
				if ($joinstr!=''){
					$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
						$sql = 	"SELECT COUNT(*) AS numRows "
								."FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid "
								." AND  note.creby = '".$_SESSION['curuser']['username']."' "
								."WHERE ".$joinstr." ";
				}else {
					$sql = "SELECT COUNT(*) AS numRows FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid  WHERE priority>0  AND note.creby = '".$_SESSION['curuser']['username']."'";
				}
			}else{
				if ($joinstr!=''){
					$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
						$sql = 	"SELECT COUNT(*) AS numRows "
								." FROM customer "
								." WHERE "
								."customer.creby = '".$_SESSION['curuser']['username']."'"
								." AND "
								.$joinstr." ";
				}else {
					$sql = "SELECT COUNT(*) AS numRows FROM customer 
						LEFT JOIN note ON customer.id = note.customerid  WHERE customer.creby = '".$_SESSION['curuser']['username']."'";
				}
			}
		
		
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;
	}
}
?>

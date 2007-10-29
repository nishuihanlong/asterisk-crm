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
		global $db;
		$sql = "SELECT note.id AS id,note.attitude as attitude, note, priority,customer.customer AS customer,contact.contact AS contact,customer.category AS category,note.cretime AS cretime,note.creby AS creby FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid ";

//		if ($creby != null)
		$sql .= " WHERE priority>0 AND note.creby = '".$_SESSION['curuser']['username']."' ";

		if($order == null){
			$sql .= " ORDER BY priority DESC LIMIT $start, $limit";//.$_SESSION['ordering'];
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
		global $db;
		
		if(($filter != null) and ($content != null)){
			$sql = "SELECT note.id AS id, note, priority,customer.customer AS customer,contact.contact AS contact,customer.category AS category,note.cretime AS cretime,note.creby AS creby FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid"
					." WHERE ".$filter." like '%".$content."%' AND priority>0 "
					." AND  note.creby = '".$_SESSION['curuser']['username']."' "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
		}
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
		global $db;
		$sql = "SELECT COUNT(*) AS numRows FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid  WHERE priority>0  AND note.creby = '".$_SESSION['curuser']['username']."'";
			
		if(($filter != null) and ($content != null)){
			$sql = 	"SELECT COUNT(*) AS numRows "
				."FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid "
				." AND  note.creby = '".$_SESSION['curuser']['username']."' "
				."WHERE ".$filter." like '%".$content."%'";
		}
		
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}
}
?>

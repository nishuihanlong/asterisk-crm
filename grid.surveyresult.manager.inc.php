<?
require_once 'db_connect.php';
require_once 'survey.common.php';
require_once 'include/Localization.php';
require_once 'astercrm.php';

/** \brief Customer Class
*

*
* @author	Solo Fu <solo.fu@gmail.com>
* @version	1.0
* @date		13 July 2007
*/

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
		
		$sql = "SELECT surveyresult.*, customer.customer AS customer,contact.contact AS contact, survey.surveyname AS surveyname FROM surveyresult LEFT JOIN customer ON customer.id = surveyresult.customerid LEFT JOIN contact ON contact.id = surveyresult.contactid LEFT JOIN survey ON survey.id = surveyresult.surveyid";

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
		global $db;

		if(($filter != null) and ($content != null)){
			$sql = "SELECT surveyresult.*, customer.customer AS customer,contact.contact AS contact, survey.surveyname AS surveyname FROM surveyresult LEFT JOIN customer ON customer.id = surveyresult.customerid LEFT JOIN contact ON contact.id = surveyresult.contactid LEFT JOIN survey ON survey.id = surveyresult.surveyid "
					." WHERE ".$filter." like '%".$content."%' "
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
		
		$sql = "SELECT COUNT(*) AS numRows FROM surveyresult LEFT JOIN customer ON customer.id = surveyresult.customerid LEFT JOIN contact ON contact.id = surveyresult.contactid LEFT JOIN survey ON survey.id = surveyresult.surveyid ";
		
		if(($filter != null) and ($content != null)){
			$sql = 	"SELECT COUNT(*) AS numRows "
				."FROM surveyresult LEFT JOIN customer ON customer.id = surveyresult.customerid LEFT JOIN contact ON contact.id = surveyresult.contactid LEFT JOIN survey ON survey.id = surveyresult.surveyid  "
				."WHERE ".$filter." like '%$content%'";
		}
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}
}
?>
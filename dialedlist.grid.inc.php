<?
/*******************************************************************************
* survey.grid.inc.php
* survey操作类
* Customer class

* @author			Solo Fu <solo.fu@gmail.com>
* @classVersion		1.0
* @date				18 Oct 2007

* Functions List

	getAllRecords				获取所有记录
	getRecordsFiltered			获取记录集
	getNumRows					获取记录集条数
	formAdd						生成添加dialedlist的HTML语句
	insertNewSurvey				保存survey
	insertNewOption				保存option
	setSurveyEnable				设定survey的可用情况

* Revision 0.0456  2007/11/6 20:30:00  last modified by solo
* Desc: remove function deleteSurvey

* Revision 0.045  2007/10/18 13:30:00  last modified by solo
* Desc: page created

********************************************************************************/
require_once 'db_connect.php';
require_once 'dialedlist.common.php';
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

		$sql = "SELECT dialedlist.*, groupname, campaignname FROM dialedlist LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.groupid = dialedlist.groupid LEFT JOIN campaign ON campaign.id = dialedlist.campaignid";

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " ";
		}else{
			$sql .= " WHERE dialedlist.groupid = ".$_SESSION['curuser']['groupid']." ";
		}

		if($order == null){
			$sql .= " ORDER BY dialedtime DESC LIMIT $start, $limit";//.$_SESSION['ordering'];
		}else{
			$sql .= " ORDER BY $order ".$_SESSION['ordering']." LIMIT $start, $limit";
		}

		Customer::events($sql);
		$res =& $db->query($sql);
//		print_r($res);
//		exit;
		return $res;
	}
	
	function getNoanswerCallsNumber(){
		global $db;

		$sql = "SELECT count(*) FROM dialedlist WHERE answertime ='0000-00-00 00:00:00' ";

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " ";
		}else{
			$sql .= " AND dialedlist.groupid = ".$_SESSION['curuser']['groupid']." ";
		}
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;
	}

	function recycleDialedlist(){
		global $db;

		$i = 0;
		//get phone numbers

		$sql = "SELECT * FROM dialedlist WHERE answertime ='0000-00-00 00:00:00' ";

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " ";
		}else{
			$sql .= " WHERE dialedlist.groupid = ".$_SESSION['curuser']['groupid']." ";
		}
		Customer::events($sql);
		$res =& $db->query($sql);
		$creby = $_SESSION['curuser']['username'];
		while ($res->fetchInto($row)) {
			$number = $row["dialednumber"];
			$groupid = $row["groupid"];
			$assign = $row["assign"];
			$campaignid = $row["campaignid"];
			$query = "INSERT INTO diallist SET dialednumber = '$number', cretime = now(), groupid =$groupid, campaignid=$campaignid, creby =  '$creby', assign = '$assign' ";
			$db->query($query);
			$query = "DELETE FROM dialedlist WHERE id = ".$row['id'];
			$db->query($query);
			$i++;
		}
		return $i;
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

		$sql = "SELECT dialedlist.*, groupname, campaignname FROM dialedlist LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.groupid = dialedlist.groupid LEFT JOIN campaign ON campaign.id = dialedlist.campaignid WHERE ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}else{
			$sql .= " dialedlist.groupid = ".$_SESSION['curuser']['groupid']." ";
		}

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr."  "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
		}
		Customer::events($sql);
		$res =& $db->query($sql);
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

			$sql = "SELECT COUNT(*) FROM dialedlist LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.id = dialedlist.groupid WHERE ";
			if ($_SESSION['curuser']['usertype'] == 'admin'){
				$sql .= " ";
			}else{
				$sql .= " dialedlist.groupid = ".$_SESSION['curuser']['groupid']." AND ";
			}

			if ($joinstr!=''){
				$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
				$sql .= " ".$joinstr;
			}else {
				$sql .= " 1";
			}
		Customer::events($sql);
		$res =& $db->getOne($sql);
//		print $sql;
//		print "\n";
//		print $res;
//		exit;
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
		
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql = " SELECT COUNT(*) FROM dialedlist LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.id = dialedlist.groupid";
		}else{
			$sql = " SELECT COUNT(*) FROM dialedlist LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.id = dialedlist.groupid WHERE dialedlist.groupid = ".$_SESSION['curuser']['groupid']." ";
		}

		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}
}
?>
<?
/*******************************************************************************
* portal.grid.inc.php
* portal操作类
* Customer class

* @author			Solo Fu <solo.fu@gmail.com>
* @classVersion		1.0
* @date				18 Oct 2007

* Functions List

	getAllRecords											获取所有记录
		($start, $limit, $order = null)
	getRecordsFiltered							获取多条件搜索结果记录集
		($start, $limit, $filter, $content, $order)
	getNumRows										 获取多条件搜索结果记录条数
		($filter = null, $content = null)

* Revision 0.0456  2007/12/19 15:11:00  last modified by solo
* Desc: deleted function getRecordsFiltered,getNumRowsMore

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
	function &getAllRecords($start, $limit, $order = null){
		global $db,$config;

		if ($config['system']['portal_display_type'] == "note"){
			$sql = "SELECT 
									note.id AS id,
									note.contactid AS contactid,
									note.customerid AS customerid,
									note.attitude AS attitude, 
									note, 
									priority,
									private,
									customer.customer AS customer,
									contact.contact AS contact,
									customer.category AS category,
									note.cretime AS cretime,
									note.creby AS creby 
									FROM note 
									LEFT JOIN customer ON customer.id = note.customerid 
									LEFT JOIN contact ON contact.id = note.contactid 
									WHERE priority>0 AND note.creby = '".$_SESSION['curuser']['username']."' ";
			
		}else{
			$sql = "SELECT customer.id,
									customer.customer AS customer,
									note.note AS note,
									note.priority AS priority,
									note.attitude AS attitude,
									note.private AS private,
									note.creby AS creby,
									customer.category AS category,
									customer.contact AS contact,
									customer.cretime as cretime
									FROM customer LEFT JOIN note ON customer.id = note.customerid ";
			if($config['system']['detail_level'] != 'all')						
				$sql .= " WHERE customer.groupid = '".$_SESSION['curuser']['groupid']."' ";
		}

		if($order == null){
			$sql .= " ORDER BY cretime DESC LIMIT $start, $limit";
		}else{
			$sql .= " ORDER BY $order ".$_SESSION['ordering']." LIMIT $start, $limit";
		}
		//echo $sql;exit;
		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}
	
	function getRecordsFiltered($start, $limit, $filter, $content, $order){
		global $db,$config;

		$i=0;
		$joinstr='';
		foreach ($content as $value){
			$value = preg_replace("/'/","\\'",$value);
			$value=trim($value);
			if (strlen($value)!=0 && strlen($filter[$i]) != 0){
				$joinstr.="AND $filter[$i] like '%$value%' ";
			}
			$i++;
		}

		if ($config['system']['portal_display_type'] == "note"){
				if ($joinstr != ''){
					$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
					$sql = "SELECT 
											note.id AS id, 
											note, 
											priority,
											private,
											customer.customer AS customer,
											contact.contact AS contact,
											customer.category AS category,
											note.cretime AS cretime,
											note.creby AS creby,
											note.customerid AS customerid,
											note.contactid AS contactid
											FROM note 
											LEFT JOIN customer ON customer.id = note.customerid 
											LEFT JOIN contact ON contact.id = note.contactid
											WHERE $joinstr  
											AND priority>0
											AND note.creby = '".$_SESSION['curuser']['username']."' "
											." ORDER BY $order ".$_SESSION['ordering']
											." LIMIT $start, $limit ";
				}else {
					$sql = "SELECT 
											note.id AS id, 
											note, 
											priority,
											private,
											customer.customer AS customer,
											contact.contact AS contact,
											customer.category AS category,
											note.cretime AS cretime,
											note.creby AS creby ,
											note.customerid AS customerid,
											note.contactid AS contactid
											FROM note 
											LEFT JOIN customer ON customer.id = note.customerid 
											LEFT JOIN contact ON contact.id = note.contactid"
											." AND  note.creby = '".$_SESSION['curuser']['username']."' "
											." ORDER BY $order ".$_SESSION['ordering']
											." LIMIT $start, $limit ";
				}
			}else{
				if ($joinstr != ''){
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
											." ORDER BY $order ".$_SESSION['ordering']
											." LIMIT $start, $limit ";
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
											." ORDER BY $order ".$_SESSION['ordering']
											." LIMIT $start, $limit ";
				}
			}

		astercrm::events($sql);
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
	

	function getNumRows($filter = null, $content = null){
		global $db,$config;
		if ($filter == null){
			if ($config['system']['portal_display_type'] == "note"){
				$sql = "SELECT 
										COUNT(*) AS numRows 
										FROM note 
										LEFT JOIN customer ON customer.id = note.customerid 
										LEFT JOIN contact ON contact.id = note.contactid  
										WHERE priority>0  AND note.creby = '".$_SESSION['curuser']['username']."'";
			}else{
				$sql = "SELECT 
										COUNT(*) AS numRows 
										FROM customer 
										LEFT JOIN note ON customer.id = note.customerid";

				if($config['system']['detail_level'] != 'all')						
					$sql .= " WHERE customer.groupid = '".$_SESSION['curuser']['groupid']."' ";
			}
		}else{
			$i=0;
			$joinstr='';
			foreach ($content as $value){
				$value = preg_replace("/'/","\\'",$value);
				$value=trim($value);
				if (strlen($value)!=0 && strlen($filter[$i]) != 0){
					$joinstr.="AND $filter[$i] like '%".$value."%' ";
				}
				$i++;
			}
			if ($config['system']['portal_display_type'] == "note"){
				if ($joinstr!=''){
					$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
					$sql = 	"SELECT 
												COUNT(*) AS numRows
												FROM note 
												LEFT JOIN customer ON customer.id = note.customerid 
												LEFT JOIN contact ON contact.id = note.contactid 
												WHERE ".$joinstr
												." AND  note.creby = '".$_SESSION['curuser']['username']."' ";
				}else {
					$sql = "SELECT 
											COUNT(*) AS numRows 
											FROM note 
											LEFT JOIN customer ON customer.id = note.customerid 
											LEFT JOIN contact ON contact.id = note.contactid  
											WHERE priority>0  
											AND note.creby = '".$_SESSION['curuser']['username']."'";
				}
			}else{
				if ($joinstr!=''){
					$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
					$sql = 	"SELECT 
												COUNT(*) AS numRows
												FROM customer 
												LEFT JOIN note ON customer.id = note.customerid  
												WHERE ".$joinstr;

					if($config['system']['detail_level'] != 'all')						
						$sql .= " AND customer.groupid = '".$_SESSION['curuser']['groupid']."' ";
				}else {
					$sql = "SELECT 
											COUNT(*) AS numRows 
											FROM customer 
											LEFT JOIN note ON customer.id = note.customerid ";

					if($config['system']['detail_level'] != 'all')						
						$sql .= " WHERE customer.groupid = '".$_SESSION['curuser']['groupid']."' ";
				}
			}
		}

		astercrm::events($sql);
		$res =& $db->getOne($sql);
		return $res;
	}

	function &getNumRowsMorewithstype($filter = null, $content = null,$stype = null,$table){
		global $db,$config;

		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);

			if ($config['system']['portal_display_type'] == "note"){
				if ($joinstr!=''){
					$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
					$sql = 	"SELECT 
												COUNT(*) AS numRows
												FROM note 
												LEFT JOIN customer ON customer.id = note.customerid 
												LEFT JOIN contact ON contact.id = note.contactid 
												WHERE ".$joinstr
												." AND  note.creby = '".$_SESSION['curuser']['username']."' ";
				}else {
					$sql = "SELECT 
											COUNT(*) AS numRows 
											FROM note 
											LEFT JOIN customer ON customer.id = note.customerid 
											LEFT JOIN contact ON contact.id = note.contactid  
											WHERE priority>0  
											AND note.creby = '".$_SESSION['curuser']['username']."'";
				}
			}else{
				if ($joinstr!=''){
					$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
					$sql = 	"SELECT 
												COUNT(*) AS numRows
												FROM customer 
												LEFT JOIN note ON customer.id = note.customerid  
												WHERE ".$joinstr;
					if($config['system']['detail_level'] != 'all')						
						$sql .= " AND customer.groupid = '".$_SESSION['curuser']['groupid']."' ";
				}else {
					$sql = "SELECT 
											COUNT(*) AS numRows 
											FROM customer 
											LEFT JOIN note ON customer.id = note.customerid ";
					if($config['system']['detail_level'] != 'all')						
						$sql .= " WHERE customer.groupid = '".$_SESSION['curuser']['groupid']."' ";
				}
			}
		astercrm::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}

	function &getRecordsFilteredMorewithstype($start, $limit, $filter, $content, $stype = null, $order,$table, $ordering = ""){
		global $db,$config;

		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);

		if ($config['system']['portal_display_type'] == "note"){
				if ($joinstr != ''){
					$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
					$sql = "SELECT 
											note.id AS id, 
											note, 
											priority,
											private,
											customer.customer AS customer,
											contact.contact AS contact,
											customer.category AS category,
											note.cretime AS cretime,
											note.creby AS creby,
											note.customerid AS customerid,
											note.contactid AS contactid
											FROM note 
											LEFT JOIN customer ON customer.id = note.customerid 
											LEFT JOIN contact ON contact.id = note.contactid
											WHERE $joinstr  
											AND priority>0
											AND note.creby = '".$_SESSION['curuser']['username']."' "
											." ORDER BY $order ".$_SESSION['ordering']
											." LIMIT $start, $limit ";
				}else {
					$sql = "SELECT 
											note.id AS id, 
											note, 
											priority,
											private,
											customer.customer AS customer,
											contact.contact AS contact,
											customer.category AS category,
											note.cretime AS cretime,
											note.creby AS creby ,
											note.customerid AS customerid,
											note.contactid AS contactid
											FROM note 
											LEFT JOIN customer ON customer.id = note.customerid 
											LEFT JOIN contact ON contact.id = note.contactid"
											." AND  note.creby = '".$_SESSION['curuser']['username']."' "
											." ORDER BY $order ".$_SESSION['ordering']
											." LIMIT $start, $limit ";
				}
			}else{
				if ($joinstr != ''){
					$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
					$sql = "SELECT customer.id AS id,
											customer.customer AS customer,
											customer.category AS category,
											customer.contact AS contact,
											customer.cretime as cretime,
											note.note AS note,
											note.priority AS priority,
											note.attitude AS attitude,
											note.private AS private,
											note.creby AS creby
											FROM customer
											LEFT JOIN note ON customer.id = note.customerid"
											." WHERE ".$joinstr;

					if($config['system']['detail_level'] != 'all')						
						$sql .= " AND customer.groupid = '".$_SESSION['curuser']['groupid']."' ";
					
					$sql .= " ORDER BY $order ".$_SESSION['ordering']." LIMIT $start, $limit ";
				}else {
					$sql = "SELECT customer.id AS id,
											customer.customer AS customer,
											customer.category AS category,
											customer.contact AS contact,
											customer.cretime as cretime,
											note.note AS note,
											note.priority AS priority,
											note.attitude AS attitude,
											note.private AS private,
											note.creby AS creby
											FROM customer
											LEFT JOIN note ON customer.id = note.customerid ";
					
					if($config['system']['detail_level'] != 'all')						
						$sql .= " WHERE customer.groupid = '".$_SESSION['curuser']['groupid']."' ";

					$sql .= " ORDER BY $order ".$_SESSION['ordering']." LIMIT $start, $limit ";
				}
			}

		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function &getAllSpeedDialRecords(){
		global $db;

		$sql = "SELECT number,description FROM speeddial ";


		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " ";
		}else{
			$sql .= " WHERE groupid = ".$_SESSION['curuser']['groupid']." ";
		}
		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function &getMyMemberStatus(){
		global $db;
		$sql = "SELECT * FROM queue_agent WHERE (agent='Agent/".$_SESSION['curuser']['agent']."' OR Agent LIKE '%".$_SESSION['curuser']['extension']."@%') ";
		
		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function getCampaignResultHtml($dialedlistid,$status = 'NOANSWER'){
		global $db,$locate;
		$sql = "SELECT campaignid FROM dialedlist Where id = $dialedlistid ORDER BY dialtime DESC LIMIT 1";
		astercrm::events($sql);
		$res = & $db->getOne($sql);

		$sql = "SELECT id,resultname FROM campaignresult WHERE campaignid = $res AND parentid = 0 AND status = '".$status."'";
		Customer::events($sql);
		$res =& $db->query($sql);
		$html = '';
		$option = '';
		$n = 0;
		while ($res->fetchInto($row)) {
			$option .='<option value="'.$row['id'].'">'.$row['resultname'].'</option>' ;
			if($n == 0){
				$n++;
				$callresultname = $row['resultname'];
				$curparentid = $row['id'];
			}
		}

		if($curparentid != '' && $curparentid != 0){
			$sql = "SELECT id,resultname FROM campaignresult WHERE parentid = $curparentid AND status = '".$status."'";

			Customer::events($sql);
			$res =& $db->query($sql);
			$secondoption = '';
			$n = 0;
			while ($res->fetchInto($row)) {
				$secondoption .='<option value="'.$row['id'].'">'.$row['resultname'].'</option>' ;
				if($n == 0){
					$n++;
					$callresultname = $row['resultname'];
					$callresultid = $row['id'];
				}
			}
		}

		if($option != ''){
			$html = $locate->Translate("Call Result").':&nbsp;<select id="fcallresult" onchange="setSecondCampaignResult()">'.$option.'</select>&nbsp;';
//
			if($secondoption != ''){
				$html .= '&nbsp;<span id="spnScallresult"><select id="scallresult" onchange="setCallresult(this);">'.$secondoption.'</select></span>';
			}else{
				$html .= '&nbsp;<span id="spnScallresult" style="display:none"><select id="scallresult" onchange="setCallresult(this);">'.$secondoption.'</select></span>';
			}

			$html .= '<input type="hidden" id="dialedlistid" name="dialedlistid" value="'.$dialedlistid.'"><input type="hidden" id="callresultname" name="callresultname" value="'.$callresultname.'">&nbsp;<input type="button" value="'.$locate->Translate("Update").'" onclick="updateCallresult();">';
			
		}
		return $html;
	}

	function getAgentData(){
		global $db;
		$sql = "SELECT * From queue_agent WHERE agent = 'Agent/".$_SESSION['curuser']['agent']."'";
		Customer::events($sql);
		$res =& $db->getRow($sql);
		return $res;
	}
}
?>

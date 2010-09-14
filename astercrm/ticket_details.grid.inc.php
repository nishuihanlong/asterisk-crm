<?php
/*******************************************************************************
* ticket_details.grid.inc.php
* ticket_details操作类
* Customer class

* @author			solo.fu <solo.fu@gmail.com>
* @classVersion		1.0
* @date				18 Aug 2010

* Functions List

	getAllRecords				获取所有记录
	getRecordsFiltered			获取记录集
	getNumRows					获取记录集条数
	formAdd						生成添加account表单的HTML
	formEdit					生成编辑account表单的HTML
	新增 getRecordsFilteredMore  用于获得多条件搜索记录集
	新增 getNumRowsMore          用于获得多条件搜索记录条数

* Revision 0.045  2007/10/18 13:15:00  last modified by solo
* Desc: page created

********************************************************************************/

require_once 'db_connect.php';
require_once 'ticket_details.common.php';
require_once 'include/astercrm.class.php';

class Customer extends astercrm {
	/**
	*  Obtiene todos los registros de la tabla paginados.
	*
	*  	@param $start	(int)	Inicio del rango de la p&aacute;gina de datos en la consulta SQL.
	*	@param $limit	(int)	L&iacute;mite del rango de la p&aacute;gina de datos en la consultal SQL.
	*	@param $order 	(string) Campo por el cual se aplicar&aacute; el orden en la consulta SQL.
	*	@return $res 	(object) Objeto que contiene el arreglo del resultado de la consulta SQL.
	*/
	function &getAllRecords($start, $limit, $order = null, $groupid = null){
		global $db;
		
		$sql = "SELECT ticket_details.*,ticketcategory.ticketname as ticketcategoryname,tickets.ticketname as ticketname, customer,username FROM ticket_details LEFT JOIN tickets AS ticketcategory ON ticketcategory.id = ticket_details.ticketcategoryid LEFT JOIN tickets AS tickets ON tickets.id = ticket_details.ticketid LEFT JOIN customer ON customer.id = ticket_details.customerid LEFT JOIN astercrm_account ON astercrm_account.id = ticket_details.assignto";
		
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " ";
		}else{
			$sql .= " WHERE ticket_details.groupid = '".$_SESSION['curuser']['groupid']."' ";
		}
			
		if($order == null){
			$sql .= " LIMIT $start, $limit";//.$_SESSION['ordering'];
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

	function &getRecordsFilteredMore($start, $limit, $filter, $content, $order, $ordering = ""){
		global $db;		
		$joinstr = Customer::createSqlWithStype($filter,$content,$stype,'ticket_details');//<---- change by your function

		$sql = "SELECT ticket_details.*,ticketcategory.ticketname as ticketcategoryname,tickets.ticketname as ticketname, customer,username FROM ticket_details LEFT JOIN tickets AS ticketcategory ON ticketcategory.id = ticket_details.ticketcategoryid LEFT JOIN tickets AS tickets ON tickets.id = ticket_details.ticketid LEFT JOIN customer ON customer.id = ticket_details.customerid LEFT JOIN astercrm_account ON astercrm_account.id = ticket_details.assignto WHERE ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1";
		}else{
			$sql .= " ticket_details.groupid = '".$_SESSION['curuser']['groupid']."' ";
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
			$sql = " SELECT COUNT(*) FROM ticket_details LEFT JOIN tickets AS ticketcategory ON ticketcategory.id = ticket_details.ticketcategoryid LEFT JOIN tickets AS tickets ON tickets.id = ticket_details.ticketid LEFT JOIN customer ON customer.id = ticket_details.customerid LEFT JOIN astercrm_account ON astercrm_account.id = ticket_details.assignto";
		}else{
			$sql = " SELECT COUNT(*) FROM ticket_details LEFT JOIN tickets AS ticketcategory ON ticketcategory.id = ticket_details.ticketcategoryid LEFT JOIN tickets AS tickets ON tickets.id = ticket_details.ticketid LEFT JOIN customer ON customer.id = ticket_details.customerid LEFT JOIN astercrm_account ON astercrm_account.id = ticket_details.assignto WHERE ticket_details.groupid = '".$_SESSION['curuser']['groupid']."'";
		}
		
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}

	function &getNumRowsMore($filter = null, $content = null){
		global $db;
		$joinstr = Customer::createSqlWithStype($filter,$content,$stype,'ticket_details');//<---- change by your function

		$sql = "SELECT COUNT(*) FROM ticket_details LEFT JOIN tickets AS ticketcategory ON ticketcategory.id = ticket_details.ticketcategoryid LEFT JOIN tickets AS tickets ON tickets.id = ticket_details.ticketid LEFT JOIN customer ON customer.id = ticket_details.customerid LEFT JOIN astercrm_account ON astercrm_account.id = ticket_details.assignto WHERE ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1";
		}else{
			$sql .= " ticket_details.groupid='".$_SESSION['curuser']['groupid']."'";
		}

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr;
		}else {
			$sql .= " 1";
		}
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;
	}

	function &getNumRowsMorewithstype($filter, $content,$stype,$table){
		global $db;
		
		$joinstr = Customer::createSqlWithStype($filter,$content,$stype,'ticket_details');//<---- change by your function

		$sql = "SELECT COUNT(*) FROM ticket_details LEFT JOIN tickets AS ticketcategory ON ticketcategory.id = ticket_details.ticketcategoryid LEFT JOIN tickets AS tickets ON tickets.id = ticket_details.ticketid LEFT JOIN customer ON customer.id = ticket_details.customerid LEFT JOIN astercrm_account ON astercrm_account.id = ticket_details.assignto WHERE ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1";
		}else{
			$sql .= " ticket_details.groupid = '".$_SESSION['curuser']['groupid']."' ";
		}
		
		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr;
		}else {
			$sql .= " 1";
		}
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;
	}

	function &getRecordsFilteredMorewithstype($start, $limit, $filter, $content, $stype,$order,$table){
		global $db;		

		$joinstr = Customer::createSqlWithStype($filter,$content,$stype,'ticket_details');//<---- change by your function

		$sql = "SELECT ticket_details.*,ticketcategory.ticketname as ticketcategoryname,tickets.ticketname as ticketname, customer,username FROM ticket_details LEFT JOIN tickets AS ticketcategory ON ticketcategory.id = ticket_details.ticketcategoryid LEFT JOIN tickets AS tickets ON tickets.id = ticket_details.ticketid LEFT JOIN customer ON customer.id = ticket_details.customerid LEFT JOIN astercrm_account ON astercrm_account.id = ticket_details.assignto WHERE ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1";
		}else{
			$sql .= " ticket_details.groupid = '".$_SESSION['curuser']['groupid']."' ";
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
	
	/**
	*  create a 'where string' with 'like,<,>,=' assign by stype
	*
	*	@param $stype		(array)		assign search type
	*	@param $filter		(array) 	filter in sql
	*	@param $content		(array)		content in sql
	*	@return $joinstr	(string)	sql where string
	*/
	function createSqlWithStype($filter,$content,$stype=array(),$table='',$option='search'){
		$i=0;
		$joinstr='';
		foreach($stype as $type){
			$content[$i] = preg_replace("/'/","\\'",$content[$i]);
			if($filter[$i] != '' && trim($content[$i]) != ''){
				if($filter[$i] == 'ticketcategoryname') {
					$filter[$i] = 'ticketcategory.ticketname';
				} else if($filter[$i] == 'ticketname') {
					$filter[$i] = 'tickets.ticketname';
				}
				if($type == "equal"){
					$joinstr.="AND $filter[$i] = '".trim($content[$i])."' ";
				}elseif($type == "more"){
					$joinstr.="AND $filter[$i] > '".trim($content[$i])."' ";
				}elseif($type == "less"){
					$joinstr.="AND $filter[$i] < '".trim($content[$i])."' ";
				}else{
					$joinstr.="AND $filter[$i] like '%".trim($content[$i])."%' ";
				}
			}
			$i++;
		}
		return $joinstr;
	}


	/**
	*  Imprime la forma para agregar un nuevo registro sobre el DIV identificado por "formDiv".
	*
	*	@param ninguno
	*	@return $html	(string) Devuelve una cadena de caracteres que contiene la forma para insertar 
	*							un nuevo registro.
	*/
	
	function formAdd(){
		global $locate;
		$categoryHtml = Customer::getTicketCategory();
		$customerHtml = Customer::getCustomer();
		$accountHtml = Customer::getAccount();

		$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("TicketCategory Name").'*</td>
					<td align="left">'.$categoryHtml.'</td>
				</tr>
				<tr>
					<td align="left" width="25%">'.$locate->Translate("Ticket Name").'*</td>
					<td id="ticketMsg"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Customer Name").'*</td>
					<td>'.$customerHtml.'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Assignto").'</td>
					<td>'.$accountHtml.'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Status").'</td>
					<td><select id="status" name="status">
						<option value="new">'.$locate->Translate("new").'</option>
						<option value="panding">'.$locate->Translate("panding").'</option>
						<option value="closed">'.$locate->Translate("closed").'</option>
						<option value="cancel">'.$locate->Translate("cancel").'</option>
					</select></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Memo").'</td>
					<td><textarea id="memo" name="memo" cols="40" rows="5"></textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><button id="submitButton" onClick=\'xajax_save(xajax.getFormValues("f"));return false;\'>'.$locate->Translate("continue").'</button></td>
				</tr>
			 </table>
			';
		$html .='
			</form>
			'.$locate->Translate("obligatory_fields").'
			';
		return $html;
	}

	function insertTicketDetail($f) {
		global $db;
		$sql = "INSERT INTO ticket_details SET"
			 ." ticketcategoryid=".$f['ticketcategoryid'].","
			 ." ticketid=".$f['ticketid'].","
			 ." customerid=".$f['customerid'].","
			 ." status='".$f['status']."',"
			 ." assignto=".$f['assignto'].","
			 ." groupid=".$f['groupid'].","
			 ." memo='".$f['memo']."',"
			 ." cretime=now(),"
			 ." creby='".$_SESSION['curuser']['username']."' ;";
		astercrm::events($sql);
		$res = & $db->query($sql);
		return $res;
	}

	/**
	*	get ticketcategory from table tickets 
	*	@param $CategoryId		(int)		tickets's id
	*	@return $html	(string)	create the option by the result of query
	*/
	function getTicketCategory($CategoryId = '') {
		global $db,$locate;
		$sql = "SELECT * FROM tickets ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " WHERE fid=0";
		}else{
			$sql .= " WHERE fid=0 AND groupid IN(0,".$_SESSION['curuser']['groupid'].")";
		}
		astercrm::events($sql);
		$result = & $db->query($sql);

		$html = '<select id="ticketcategoryid" name="ticketcategoryid" onchange="relateBycategoryID(this.value);"><option value="0">'.$locate->Translate('please select').'</option>';
		while($row = $result->fetchRow()) {
			$html .= '<option value="'.$row['id'].'"';
			if($row['id'] == $CategoryId && $CategoryId != '') {
				$html .= ' selected';
			}
			$html .= '>'.$row['ticketname'].'</option>';
		}
		$html .= '</select>';
		return $html;
	}

	/**
	*	get tickets from table tickets by ticketcategoryid
	*	@param $fid		(int)		ticketcategory's id 
	*			$Cid	(int)		current ticket id (for edit)
	*	@return $html	(string)	create the option by the result of query
	*/
	function getTicketByCid($fid,$Cid=0) {
		global $db,$locate;
		if($fid != 0) {
			$fsql = "SELECT groupid FROM tickets WHERE id=$fid";
			$groupid = & $db->getOne($fsql);
		} else {
			$groupid = 0;
		}
		
		$sql = "SELECT * FROM tickets";
		if($fid == 0) {
			$sql .= " WHERE fid=-1";
		} else {
			$sql .= " WHERE fid=$fid";
		}
		astercrm::events($sql);
		$result = & $db->query($sql);
		$html = '<select id="ticketid" name="ticketid">';
		$tmp = '';
		while($row = $result->fetchRow()) {
			$tmp .= '<option value="'.$row['id'].'"';
			if($Cid != 0 && $row['ticketid'] == $Cid) {
				$tmp .= ' selected ';
			}
			$tmp .= '>'.$row['ticketname'].'</option>';
		}
		if($tmp == '') {
			$html .= '<option value="0">'.$locate->Translate('please select').'</option>';
		} else {
			$html .= $tmp;
		}
		$html .= '</select><input type="hidden" id="groupid" name="groupid" value="'.$groupid.'" />';
		return $html;
	}
	
	/**
	*	get customer from table customer
	*	@param $customerid	(int)	 default 0  (for edit)
	*	@return		$html	(string)	create the option by the result of query
	*/
	function getCustomer($customerid=0) {
		global $db,$locate;
		$sql = "select * from customer";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " ";
		}else{
			$sql .= " WHERE groupid = ".$_SESSION['curuser']['groupid']." ";
		}
		astercrm::events($sql);
		$result = & $db->query($sql);
		$html = '<select id="customerid" name="customerid">';
		$tmp = '';
		while($row = $result->fetchRow()) {
			$tmp .= '<option value="'.$row['id'].'"';
			if($customerid != 0 && $row['id'] == $customerid) {
				$tmp .= ' selected';
			}
			$tmp .= '>'.$row['customer'].'</option>';
		}
		if($tmp == '') {
			$html .= '<option value="0">'.$locate->Translate('please select').'</option>';
		} else {
			$html .= $tmp;
		}
		$html .= '</select>';
		return $html;
	}

	/**
	*	get account from table account
	*	@param	$accountid	(int) default 0  (for edit)
	*	@return		$html	(string)	create the option by the result of query
	*/
	function getAccount($accountid =0) {
		global $db,$locate;
		$sql = "SELECT * FROM astercrm_account where username!='admin'";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " ";
		}else{
			$sql .= " AND groupid=".$_SESSION['curuser']['groupid']." ";
		}
		astercrm::events($sql);
		$result = & $db->query($sql);
		$html = '<select id="assignto" name="assignto">';
		$tmp = '';
		while($row = $result->fetchRow()) {
			$tmp .= '<option value="'.$row['id'].'"';
			if($accountid != 0 && $row['id'] == $accountid) {
				$tmp .= ' selected';
			}
			$tmp .= '>'.$row['username'].'</option>';
		}
		if($tmp == '') {
			$html .= '<option value="0">'.$locate->Translate('please select').'</option>';
		} else {
			$html .= $tmp;
		}
		$html .= '</select>';
		return $html;
	}

	/**
	*  Imprime la forma para editar un nuevo registro sobre el DIV identificado por "formDiv".
	*
	*	@param $id		(int)		Identificador del registro a ser editado.
	*	@return $html	(string)	Devuelve una cadena de caracteres que contiene la forma con los datos 
	*								a extraidos de la base de datos para ser editados 
	*/
	function formEdit($id){
		global $locate;
		$result =& Customer::getRecordByID($id,'ticket_details');
		$categoryHtml = Customer::getTicketCategory($result['ticketcategoryid']);
		$customerHtml = Customer::getCustomer($result['customerid']);
		$accountHtml = Customer::getAccount($result['assignto']);

		$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("TicketCategory Name").'*</td>
					<td align="left">'.$categoryHtml.'<input type="hidden" id="id" name="id" value="'.$result['id'].'"><input type="hidden" id="curTicketid" value="'.$result['ticketid'].'"></td>
				</tr>
				<tr>
					<td align="left" width="25%">'.$locate->Translate("Ticket Name").'*</td>
					<td id="ticketMsg"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Customer Name").'*</td>
					<td>'.$customerHtml.'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Assignto").'</td>
					<td>'.$accountHtml.'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Status").'</td>
					<td><select id="status" name="status">
						<option value="new"';
						if($result['status'] == 'new'){$html .= ' selected';}
						$html .='>'.$locate->Translate("new").'</option>
						<option value="panding"';
						if($result['status'] == 'panding'){$html .= ' selected';}
						$html .='>'.$locate->Translate("panding").'</option>
						<option value="closed"';
						if($result['status'] == 'closed'){$html .= ' selected';}
						$html .='>'.$locate->Translate("closed").'</option>
						<option value="cancel"';
						if($result['status'] == 'cancel'){$html .= ' selected';}
						$html .='>'.$locate->Translate("cancel").'</option>
					</select></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Memo").'</td>
					<td><textarea id="memo" name="memo" cols="40" rows="5">'.$result['memo'].'</textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><button id="submitButton" onClick=\'xajax_update(xajax.getFormValues("f"));return false;\'>'.$locate->Translate("continue").'</button></td>
				</tr>
			 </table>
			';
		$html .= '
				</form>
				'.$locate->Translate("obligatory_fields").'
				';
		return $html;
	}

	/**
	*  Actualiza un registro de la tabla.
	*
	*	@param $f	(array)		Arreglo que contiene los datos del formulario pasado.
	*	@return $res	(object)	Devuelve el objeto con la respuesta de la sentencia SQL ejecutada del UPDATE.
	*/
	
	function updateTicketDetail($f){
		global $db;
		$f = astercrm::variableFiler($f);
		
		$query= "UPDATE ticket_details SET "
				."ticketcategoryid=".$f['ticketcategoryid'].", "
				."ticketid=".$f['ticketid'].", "
				."customerid=".$f['customerid'].", "
				."assignto=".$f['assignto'].","
				."status='".$f['status']."', "
				."groupid=".$f['groupid'].","
				."memo='".$f['memo']."' "
				."WHERE id=".$f['id']."";

		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}
}
?>
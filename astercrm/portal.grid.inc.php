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
									customer.cretime as cretime,
									customer.phone as phone,
									customer.mobile as mobile
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
											customer.phone as phone,
											customer.mobile as mobile,
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
											customer.phone as phone,
											customer.mobile as mobile,
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
											customer.phone as phone,
											customer.mobile as mobile,
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
											customer.phone as phone,
											customer.mobile as mobile,
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
		$sql = "SELECT campaignid,dialednumber FROM dialedlist Where id = $dialedlistid ORDER BY dialtime DESC LIMIT 1";
		astercrm::events($sql);
		$result = & $db->query($sql);
		while ($result->fetchInto($rows)) {
			$campaignId = $rows['campaignid'];
			$tmp_callerid = $rows['dialednumber'];
		}

		$sql = "SELECT id,resultname FROM campaignresult WHERE campaignid = ".$campaignId." AND parentid = 0 AND status = '".$status."'";
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

			$html .= '<input type="hidden" id="dialedlistid" name="dialedlistid" value="'.$dialedlistid.'"><input type="hidden" id="tmp60_callerid" name="tmp60_callerid" value="'.$tmp_callerid.'"><input type="hidden" id="callresultname" name="callresultname" value="'.$callresultname.'">&nbsp;<input type="button" value="'.$locate->Translate("Update").'" onclick="updateCallresult();"><span id="updateresultMsg"></span>';
			
		}
		return $html;
	}

	function getAgentData(){
		global $db;
		if($_SESSION['curuser']['channel'] == ''){
			$sql = "SELECT * From queue_agent WHERE agent = 'Agent/".$_SESSION['curuser']['agent']."' OR agent LIKE 'local/".$_SESSION['curuser']['extension']."@%'";
		}else{
			$sql = "SELECT * From queue_agent WHERE agent = 'Agent/".$_SESSION['curuser']['agent']."' OR agent LIKE 'local/".$_SESSION['curuser']['extension']."@%' OR agent = '".$_SESSION['curuser']['channel']."'";
		}
		//echo $sql;exit;
		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
		if(!$res || $res['status'] == 'Unavailable' || $res['status'] == 'Invalid'){//如果无动态座席信息或动态座席未登录,就查静态座席
			$sql = "SELECT * From queue_agent WHERE agent LIKE 'local/".$_SESSION['curuser']['extension']."@%'";
			Customer::events($sql);
			$sres =& $db->getRow($sql);
			if($sres){
				$res = $sres;
			}
		}
	//	print_r($res);exit;
		return $res;
	}

	function formDiallist($dialedlistid){
		global $locate, $db;
		$sql = "SELECT dialednumber, customername,memo FROM dialedlist WHERE id = $dialedlistid";
		Customer::events($sql);
		$row =& $db->getRow($sql);
		$html = '';
		if($row){
			$html = Table::Top($locate->Translate("Customer from Diallist"),"formDiallistPopup");  // <-- Set the title for your form.	
			$html .= '<table border="1" width="100%" class="adminlist" id="d" name="d">
						<tr><td width="45%">&nbsp;'.$locate->Translate("Customer Name").':&nbsp;</td><td>'.$row['customername'].'</td></tr>
						<tr><td>&nbsp;'.$locate->Translate("Pone Number").':&nbsp;</td><td>'.$row['dialednumber'].'</td></tr>
						<tr><td>&nbsp;'.$locate->Translate("Memo").':&nbsp;</td><td>'.$row['memo'].'</td></tr>
					</table>'; // <-- Change by your method
			$html .= Table::Footer();
		}
		return $html;
	}

	function getLastOwnDiallistId(){
		global $db;
		$sql = "SELECT id FROM diallist WHERE diallist.assign ='".$_SESSION['curuser']['extension']."' AND dialtime != '0000-00-00 00:00:00' AND callOrder > 0 ORDER BY dialtime ASC, callOrder DESC, id ASC LIMIT 0,5";
		$res =& $db->query($sql);
		$i = 0;
		while($res->fetchInto($row)){
			$idstr .= $row['id'];
			$i++;
		}

		if($i < 5){
			$limit = 5 - $i;
			$sql = "SELECT id FROM diallist WHERE diallist.assign ='".$_SESSION['curuser']['extension']."' AND dialtime = '0000-00-00 00:00:00' AND callOrder > 0 ORDER BY callOrder DESC, id ASC LIMIT 0,$limit";

			$res =& $db->query($sql);
			while($res->fetchInto($row)){
				$idstr .= $row['id'];
			}
		}
		return $idstr;
	}

	function getAgentWorkStat(){
		global $db;
		$sql = "SELECT COUNT(*) AS count, SUM(billsec) AS billsec FROM mycdr WHERE calldate >= '".date("Y-m-d")." 00:00:00' AND  calldate <= '".date("Y-m-d")." 23:59:59' AND mycdr.astercrm_groupid > 0 AND billsec > 0";
		$res = $db->getRow($sql);
		return $res;
	}

	function getKnowledge(){
	    global $db;
		$sql = "SELECT id,knowledgetitle FROM knowledge WHERE knowledgetitle!=''";
		if($_SESSION['curuser']['usertype'] == 'admin'){
            $sql .= "";
		}else{
            $sql .= " AND (groupid='".$_SESSION['curuser']['groupid']."' OR groupid='0')";
		}
		$res = $db->query($sql);
		return $res;

	}

    function knowledge($knowledgeid){
	    global $db;
        $row = Customer::getRecordByID($knowledgeid,'knowledge');
		$html = '<textarea rows="20" cols="70" id="content" wrap="soft" style="overflow:auto;" readonly>'.$row['content'].'</textarea>';
        return $html;
	}

	function showTicketDetail($customerid) {
		global $db,$locate;
		$sql = "SELECT customer FROM customer WHERE id=$customerid";
		astercrm::events($sql);
		$customername = & $db->getOne($sql);
		
		$statusOption = '<select id="Tstatus" name="Tstatus"><option value="new">'.$locate->Translate("new").'</option><option value="panding">'.$locate->Translate("panding").'</option><option value="closed">'.$locate->Translate("closed").'</option><option value="cancel">'.$locate->Translate("cancel").'</option></select>';
		
		$ticketCategory = Customer::getTicketCategory();
		$html = '<form method="post" name="t" id="t">
					<table border="1" width="100%" class="adminlist">
						<tr>
							<td nowrap align="left">'.$locate->Translate("TicketCategory Name").'</td>
							<td align="left">'.$ticketCategory.'</td>
						</tr>
						<tr>
							<td nowrap align="left">'.$locate->Translate("Ticket Name").'*</td>
							<td align="left" id="ticketMsg"></td>
						</tr>
						<tr>
							<td nowrap align="left">'.$locate->Translate("Customer Name").'</td>
							<td align="left"><input type="hidden" name="customerid" value="'.$customerid.'" />'.$customername.'&nbsp;&nbsp;<a onclick="javascript:AllTicketOfMyself('.$customerid.');return false;" href="?">'.$locate->Translate("Customer Tickets").'</a></td>
						</tr>
						<tr>
							<td nowrap align="left">'.$locate->Translate("Status").'</td>
							<td align="left">'.$statusOption.'</td>
						</tr>
						<tr>
							<td nowrap align="left">'.$locate->Translate("Memo").'</td>
							<td align="left"><textarea cols="40" rows="5" name="Tmemo" id="Tmemo"></textarea></td>
						</tr>
						<tr>
							<td colspan="2" align="center"><button onClick=\'xajax_saveTicket(xajax.getFormValues("t"));return false;\'>'.$locate->Translate("continue").'</button></td>
						</tr>
					</table>';
			$html .='
				</form>
				'.$locate->Translate("obligatory_fields").'
				';
		return $html;
	}

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

	function getTicketByCategory($fid,$Cid=0) {
		global $db,$locate;
		$sql = "SELECT * FROM tickets WHERE ";

		if($fid != 0) {
			$sql .= "fid=$fid";
		} else {
			$sql .= "fid = -1";
		}
		if($fid != 0) {
			$fsql = "SELECT groupid FROM tickets WHERE id=$fid";
			$groupid = & $db->getOne($fsql);
		} else {
			$groupid = 0;
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

	function insertTicket($f) {
		global $db;
		$customer_sql = "select id from astercrm_account where username='".$_SESSION['curuser']['username']."'";
		astercrm::events($customer_sql);
		$customerid = & $db->getOne($customer_sql);
		
		$sql = "insert into ticket_details set"
				." ticketcategoryid=".$f['ticketcategoryid'].", "
				." ticketid=".$f['ticketid'].", "
				." customerid=".$f['customerid'].", "
				." status='".$f['Tstatus']."', "
				." assignto=".$customerid.", "
				." groupid=".$f['groupid'].", "
				." memo='".$f['Tmemo']."', "
				." cretime=now(),"
				." creby='".$_SESSION['curuser']['username']."' ;";
		
		astercrm::events($sql);
		$result = & $db->query($sql);
		return $result;
	}

	function checkAlltickets($cid,$status='') {
		global $db,$locate;
		$sql = "SELECT ticket_details.*,ticketname,customer FROM ticket_details LEFT JOIN tickets ON tickets.id=ticket_details.ticketid LEFT JOIN customer ON customer.id=ticket_details.customerid ";
		if($cid != 0){
			$sql .= "WHERE ticket_details.customerid=$cid ";
		}

		if($status != '') {
			$sql .= " AND ticket_details.status='".$status."'";
		}
		
		astercrm::events($sql);
		$result = & $db->query($sql);

		$ticketHtml .= '<form><table width="100%" border="1" class="adminlist">
						<tr>
							<td>'.$locate->Translate('Ticket Name').'</td>
							<td>'.$locate->Translate('Ticket Status').'</td>
							<td>'.$locate->Translate('Ticket Creby').'</td>
						</tr>';
		while($row = $result->fetchRow()) {
			$ticketHtml .= '<tr>
								<td>'.$row['ticketname'].'</td>
								<td>'.$locate->Translate($row['status']).'</td>
								<td>'.$row['creby'].'</td>
							</tr>';
		}
		$ticketHtml .= '</table></form>';
		return $ticketHtml;
	}

	function getAccountid($username="") {
		global $db;
		$sql = "SELECT id FROM astercrm_account WHERE";
		if($username == "") {
			$sql .= " username='".$_SESSION['curuser']['username']."' ";
		} else {
			$sql .= " username='".$username."'";
		}
		astercrm::events($sql);
		$customerid = & $db->getOne($sql);
		return $customerid;
	}

	/**
	*  Imprime la forma para editar un nuevo registro sobre el DIV identificado por "formDiv".
	*
	*	@param $id		(int)		Identificador del registro a ser editado.
	*	@return $html	(string)	Devuelve una cadena de caracteres que contiene la forma con los datos 
	*								a extraidos de la base de datos para ser editados 
	*/
	function formTicketEdit($id){
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
					<td colspan="2" align="center"><button id="submitButton" onClick=\'xajax_updateCurTicket(xajax.getFormValues("f"));return false;\'>'.$locate->Translate("continue").'</button></td>
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

	function getCustomerid($customer) {
		global $db;
		$sql = "SELECT id FROM customer WHERE customer='".$customer."'";
		astercrm::events($sql);
		$customerid = & $db->getOne($sql);
		return $customerid;
	}

	function getTicketInWork(){
		global $db,$locate;
		$sql = "SELECT COUNT(*) FROM ticket_details LEFT JOIN astercrm_account ON astercrm_account.id = ticket_details.assignto WHERE username='".$_SESSION['curuser']['username']."'";
		$panding_sql = $sql." AND status = 'panding'";
		astercrm::events($panding_sql);
		$panding_num = & $db->getOne($panding_sql);

		$new_sql = $sql." AND status = 'new'";
		astercrm::events($new_sql);
		$new_num = & $db->getOne($new_sql);
		//.$locate->Translate('new').":"   $locate->Translate('panding').":".
		$html = "(".$new_num."/".$panding_num.")";
		return $html;
	}
		
	function updateCurTicket($f){
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

	function generateUniquePin($len=10) {
	
		srand((double)microtime()*1000003);
		$prefix = rand(1000000000,9999999999);
		if(is_numeric($len) && $len > 10 && $len < 20 ){
			$len -= 10;
			$min = 1;
			for($i=1; $i < $len; $i++){
			$min = $min*10;
			}
			$max = ($min*10) - 1;
			$pin = $prefix.rand($min,$max);
						
		}elseif($len <= 10){
			$pin = $prefix;
		}else{
			$pin = $prefix.rand(1000000000,9999999999);
		}		
		return $pin;
	}


	function getMsgInCampaign($groupId) {
		global $db;
		$sql = "SELECT id,campaignname,queuename FROM campaign WHERE queuename != '' AND groupid='$groupId' AND enable= 1 ORDER BY queuename ASC";
		$result = & $db->query($sql);

		$dataArray = array();
		while($row = $result->fetchRow()) {
			$dataArray[] = $row;
		}
		return $dataArray;
	}
}
?>

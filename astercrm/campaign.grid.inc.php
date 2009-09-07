<?
/*******************************************************************************
* campaign.grid.inc.php
* campaign操作类
* Customer class

* @author			Solo Fu <solo.fu@gmail.com>
* @classVersion		1.0
* @date				18 Oct 2007

* Functions List

	getAllRecords				获取所有记录
	getRecordsFiltered			获取记录集
	getNumRows					获取记录集条数
	formAdd						生成添加campaign表单的HTML
	formEdit					生成编辑campaign表单的HTML
	新增getRecordsFilteredMore  用于获得多条件搜索记录集
	新增getNumRowsMore          用于获得多条件搜索记录条数

* Revision 0.0456  2007/10/30 13:15:00  last modified by solo
* Desc: add channel field 

* Revision 0.045  2007/10/18 13:15:00  last modified by solo
* Desc: page created

********************************************************************************/

require_once 'db_connect.php';
require_once 'campaign.common.php';
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
		
		$sql = "SELECT campaign.*, groupname, servers.name as servername FROM campaign LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.groupid = campaign.groupid LEFT JOIN servers ON servers.id = campaign.serverid ";

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " ";
		}else{
			$sql .= " WHERE campaign.groupid = ".$_SESSION['curuser']['groupid']." ";
		}

//		if ($creby != null)
//			$sql .= " WHERE note.creby = '".$_SESSION['curuser']['username']."' ";
			

		if($order == null){
			$sql .= " LIMIT $start, $limit";//.$_SESSION['ordering'];
		}else{
			$sql .= " ORDER BY $order ".$_SESSION['ordering']." LIMIT $start, $limit";
		}

		//echo $sql;
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
			$sql = "SELECT * FROM campaign"
					." WHERE ".$filter." like '%".$content."%' "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
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
			$value = preg_replace("/'/","\\'",$value);
			$value=trim($value);
			if (strlen($value)!=0 && strlen($filter[$i]) != 0){
				$joinstr.="AND $filter[$i] like '%".$value."%' ";
			}
			$i++;
		}

		$sql = "SELECT campaign.*, groupname, servers.name as servername FROM campaign LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.id = campaign.groupid LEFT JOIN servers ON servers.id = campaign.serverid  WHERE ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}else{
			$sql .= " campaign.groupid = ".$_SESSION['curuser']['groupid']." ";
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

	function insertNewCampaign($f){
		global $db;
		$f = astercrm::variableFiler($f);
		$bindqueue = 0;
		if ($f['bindqueue'] =="on"){
			$bindqueue = 1;
		}

		$query= "INSERT INTO campaign SET "
				."campaignname='".$f['campaignname']."', "
				."campaignnote='".$f['campaignnote']."', "
				."enable='".$f['enable']."', "
				."serverid='".$f['serverid']."', "
				."waittime='".$f['waittime']."', "
				."worktime_package_id='".$f['worktime_package_id']."', "
				."outcontext='".$f['outcontext']."', "
				."incontext='".$f['incontext']."', "
				."inexten='".$f['inexten']."', "
				."queuename='".$f['queuename']."', "
				."bindqueue='".$bindqueue."', "
				."maxtrytime='".$f['maxtrytime']."', "
				."recyletime='".$f['recyletime']."', "
				."minduration='".$f['minduration']."', "
				."callerid='".$f['callerid']."', "
				."groupid='".$f['groupid']."', "
				."creby = '".$_SESSION['curuser']['username']."',"
				."cretime = now()";
		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}


	function updateCampaignRecord($f){
		global $db;
		$f = astercrm::variableFiler($f);
		$bindqueue = 0;
		if ($f['bindqueue'] =="on"){
			$bindqueue = 1;
		}

		$query= "UPDATE campaign SET "
				."campaignname='".$f['campaignname']."', "
				."campaignnote='".$f['campaignnote']."', "
				."enable='".$f['enable']."', "	
				."serverid='".$f['serverid']."', "
				."worktime_package_id='".$f['worktime_package_id']."', "
				."waittime='".$f['waittime']."', "
				."outcontext='".$f['outcontext']."', "
				."incontext='".$f['incontext']."', "
				."inexten='".$f['inexten']."', "
				."queuename='".$f['queuename']."', "
				."bindqueue='".$bindqueue."', "
				."maxtrytime='".$f['maxtrytime']."', "
				."recyletime='".$f['recyletime']."', "
				."minduration='".$f['minduration']."', "
				."callerid='".$f['callerid']."', "
				."groupid='".$f['groupid']."' "
				."WHERE id=".$f['id'];
		astercrm::events($query);
		$res =& $db->query($query);
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
			$sql = " SELECT COUNT(*) FROM campaign LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.id = campaign.groupid";
		}else{
			$sql = " SELECT COUNT(*) FROM campaign LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.id = campaign.groupid WHERE campaign.groupid = ".$_SESSION['curuser']['groupid']." ";
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
				$value = preg_replace("/'/","\\'",$value);
				$value=trim($value);
				if (strlen($value)!=0 && strlen($filter[$i]) != 0){
					$joinstr.="AND $filter[$i] like '%".$value."%' ";
				}
				$i++;
			}

			$sql = "SELECT COUNT(*) FROM campaign LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.id = campaign.groupid LEFT JOIN servers ON servers.id = campaign.serverid WHERE ";
			if ($_SESSION['curuser']['usertype'] == 'admin'){
				$sql .= " ";
			}else{
				$sql .= " campaign.groupid = ".$_SESSION['curuser']['groupid']." AND ";
			}

			if ($joinstr!=''){
				$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
				$sql .= " ".$joinstr;
			}else {
				$sql .= " 1";
			}
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;
	}

	function &getRecordsFilteredMorewithstype($start, $limit, $filter, $content, $stype,$order,$table){
		global $db;

		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype,"campaign");

		$sql = "SELECT campaign.*, groupname, servers.name as servername FROM campaign LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.id = campaign.groupid LEFT JOIN servers ON servers.id = campaign.serverid WHERE ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}else{
			$sql .= " campaign.groupid = ".$_SESSION['curuser']['groupid']." ";
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

	function &getNumRowsMorewithstype($filter, $content,$stype,$table){
		global $db;
		
			$joinstr = astercrm::createSqlWithStype($filter,$content,$stype,"campaign");

			$sql = "SELECT COUNT(*) FROM campaign LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.id = campaign.groupid LEFT JOIN servers ON servers.id = campaign.serverid WHERE ";
			if ($_SESSION['curuser']['usertype'] == 'admin'){
				$sql .= " ";
			}else{
				$sql .= " campaign.groupid = ".$_SESSION['curuser']['groupid']." AND ";
			}

			if ($joinstr!=''){
				$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
				$sql .= " ".$joinstr;
			}else {
				$sql .= " 1";
			}
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;
	}

	function getCountAnswered($campaignid){
		global $db;
		$query = "SELECT COUNT(*) FROM dialedlist WHERE campaignid = $campaignid AND answertime > '0000-00-00 00:00:00'";
		Customer::events($query);
		$res =& $db->getOne($query);
		return $res;
	}
	
	/**
	*  Imprime la forma para agregar un nuevo registro sobre el DIV identificado por "formDiv".
	*
	*	@param ninguno
	*	@return $html	(string) Devuelve una cadena de caracteres que contiene la forma para insertar 
	*							un nuevo registro.
	*/
	
	function formAdd(){
			global $locate,$config,$db;

		if ($_SESSION['curuser']['usertype'] == 'admin'){
				$res = Customer::getGroups();
				$grouphtml .= '<select name="groupid" id="groupid">';
				while ($row = $res->fetchRow()) {
						$grouphtml .= '<option value="'.$row['groupid'].'"';
						$grouphtml .='>'.$row['groupname'].'</option>';
				}
				$grouphtml .= '</select>';
		}else{
				$grouphtml .= $_SESSION['curuser']['group']['groupname'].'<input id="groupid" name="groupid" type="hidden" value="'.$_SESSION['curuser']['groupid'].'">';
		}

		$query = "SELECT id,worktimepackage_name From worktimepackages";
		if($_SESSION['curuser']['usertype'] != 'admin'){
			$query .= " Where groupid =".$_SESSION['curuser']['groupid'];
		}

		$worktimepackage_res = $db->query($query);
		$worktimepackagehtml .= '<select name="worktime_package_id" id="worktime_package_id">
						<option value="0">'.$locate->Translate("Any time").'</option>';
		while ($worktimepackage_row = $worktimepackage_res->fetchRow()) {
			$worktimepackagehtml .= '<option value="'.$worktimepackage_row['id'].'"';
			$worktimepackagehtml .='>'.$worktimepackage_row['worktimepackage_name'].'</option>';
		}
		$worktimepackagehtml .= '</select>';
		
		$query = "SELECT id,name From servers";
		$server_res = $db->query($query);
		$serverhtml .= '<select name="serverid" id="serverid">
						<option value="0">'.$locate->Translate("Default Server").'</option>';
		while ($server_row = $server_res->fetchRow()) {
			$serverhtml .= '<option value="'.$server_row['id'].'"';
			$serverhtml .='>'.$server_row['name'].'</option>';
		}
		$serverhtml .= '</select>';

	$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">			
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("Campaign Name").'*</td>
					<td align="left"><input type="text" id="campaignname" name="campaignname" size="30" maxlength="60"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Campaign Note").'</td>
					<td align="left"><input type="text" id="campaignnote" name="campaignnote" size="30" maxlength="255"></td>
				</tr>
				<tr>					
					<td align="left" colspan="2">'.$locate->Translate("Enable").'&nbsp;<input type="radio" id="enable" name="enable" value="1" checked>&nbsp;'.$locate->Translate("Disable").'&nbsp;<input type="radio" id="enable" name="enable" value="0" ></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Asterisk Server").'*</td>
					<td align="left">'.$serverhtml.'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Worktime package").'</td>
					<td align="left">'.$worktimepackagehtml.'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Waitting time").'</td>
					<td align="left"><input type="text" id="waittime" name="waittime" size="30" maxlength="3" value="45"></td>
				</tr>				
				<tr>
					<td nowrap align="left">'.$locate->Translate("Outcontext").'*</td>
					<td align="left"><input type="text" id="outcontext" name="outcontext" size="30" maxlength="60" value="'.$config['system']['outcontext'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Incontext").'*</td>
					<td align="left"><input type="text" id="incontext" name="incontext" size="30" maxlength="60" value="'.$config['system']['incontext'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Inexten").'</td>
					<td align="left"><input type="text" id="inexten" name="inexten" size="30" maxlength="30"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Queue number").'</td>
					<td align="left">
						<input type="text" id="queuename" name="queuename" size="15" maxlength="15">
						<input type="checkbox" name="bindqueue" id="bindqueue">'.$locate->Translate("send calls to this queue directly").'
					</td>
				</tr>
				<!--
				<tr>
					<td nowrap align="left">'.$locate->Translate("CallerID").'</td>
					<td align="left"><input type="text" id="callerid" name="callerid" size="30" maxlength="30"></td>
				</tr>
				-->
				<tr>
					<td nowrap align="left">'.$locate->Translate("Group").'</td>
					<td align="left">'.$grouphtml.'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Max trytime").'</td>
					<td align="left"><input type="text" id="maxtrytime" value="1" name="maxtrytime" size="10" maxlength="10"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Recyle time").'</td>
					<td align="left"><input type="text" id="recyletime" value="3600" name="recyletime" size="10" maxlength="10"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Min Duration").'</td>
					<td align="left"><input type="text" id="minduration" value="0" name="minduration" size="10" maxlength="10"></td>
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

	/**
	*  Imprime la forma para editar un nuevo registro sobre el DIV identificado por "formDiv".
	*
	*	@param $id		(int)		Identificador del registro a ser editado.
	*	@return $html	(string) Devuelve una cadena de caracteres que contiene la forma con los datos 
	*									a extraidos de la base de datos para ser editados 
	*/
	
	function formEdit($id){
		global $locate,$db;
		$campaign =& Customer::getRecordByID($id,'campaign');

		if ($_SESSION['curuser']['usertype'] == 'admin'){ 
				$grouphtml .=	'<select name="groupid" id="groupid" >
																<option value=""></option>';
				$res = Customer::getGroups();
				while ($row = $res->fetchRow()) {
					$grouphtml .= '<option value="'.$row['groupid'].'"';
					if($row['groupid'] == $campaign['groupid']){
						$grouphtml .= ' selected ';
					}
					$grouphtml .= '>'.$row['groupname'].'</option>';
				}
				$grouphtml .= '</select>';
		}else{
				
				$grouphtml .= $_SESSION['curuser']['group']['groupname'].'<input type="hidden" name="groupid" id="groupid" value="'.$_SESSION['curuser']['groupid'].'">';
		}
		$bindqueue = "";
		if ($campaign['bindqueue'] == 1){
			$bindqueue = "checked";
		}

		$query = "SELECT id,name From servers";
		$server_res = $db->query($query);
		$serverhtml .= '<select name="serverid" id="serverid">
						<option value="0">'.$locate->Translate("Default Server").'</option>';
		while ($server_row = $server_res->fetchRow()) {
			$serverhtml .= '<option value="'.$server_row['id'].'"';
				if($server_row['id'] == $campaign['serverid']){
					$serverhtml .= ' selected ';
				}
				$serverhtml .= '>'.$server_row['name'].'</option>';
		}
		$serverhtml .= '</select>';

		$query = "SELECT id,worktimepackage_name From worktimepackages";
		if($_SESSION['curuser']['usertype'] != 'admin'){
			$query .= " Where groupid =".$_SESSION['curuser']['groupid'];
		}
		$worktimepackage_res = $db->query($query);
		$worktimepackagehtml .= '<select name="worktime_package_id" id="worktime_package_id">
						<option value="0">'.$locate->Translate("Any time").'</option>';
		while ($worktimepackage_row = $worktimepackage_res->fetchRow()) {
			$worktimepackagehtml .= '<option value="'.$worktimepackage_row['id'].'"';
			if($worktimepackage_row['id'] == $campaign['worktime_package_id']){
				$worktimepackagehtml .= ' selected ';
			}
			$worktimepackagehtml .='>'.$worktimepackage_row['worktimepackage_name'].'</option>';
		}
		$worktimepackagehtml .= '</select>';

		$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("Campaign Name").'*</td>
					<td align="left"><input type="hidden" id="id" name="id" value="'. $campaign['id'].'"><input type="text" id="campaignname" name="campaignname" size="30" maxlength="60" value="'.$campaign['campaignname'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Campaign Note").'</td>
					<td align="left"><input type="text" id="campaignnote" name="campaignnote" size="30" maxlength="255" value="'.$campaign['campaignnote'].'"></td>
				</tr>
				<tr>					
					<td align="left" colspan="2">'.$locate->Translate("Enable").'&nbsp;<input type="radio" id="enable" name="enable" value="1"';
			if($campaign['enable']) 
				$html .= 'checked>&nbsp;'.$locate->Translate("Disable").'&nbsp;<input type="radio" id="enable" name="enable" value="0" ></td>';
			else
				$html .= '>&nbsp;'.$locate->Translate("Disable").'&nbsp;<input type="radio" id="enable" name="enable" value="0" checked></td>';
			$html .= 
				'</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Asterisk Server").'*</td>
					<td align="left">'.$serverhtml.'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Worktime package").'</td>
					<td align="left">'.$worktimepackagehtml.'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Waitting time").'</td>
					<td align="left"><input type="text" id="waittime" name="waittime" size="30" maxlength="3" value="'.$campaign['waittime'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Outcontext").'*</td>
					<td align="left"><input type="text" id="outcontext" name="outcontext" size="30" maxlength="60" value="'.$campaign['outcontext'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Incontext").'*</td>
					<td align="left"><input type="text" id="incontext" name="incontext" size="30" maxlength="60" value="'.$campaign['incontext'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Inexten").'</td>
					<td align="left"><input type="text" id="inexten" name="inexten" size="30" maxlength="30" value="'.$campaign['inexten'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Queue number").'</td>
					<td align="left">
						<input type="text" id="queuename" name="queuename" size="30" maxlength="30" value="'.$campaign['queuename'].'">
						<input type="checkbox" name="bindqueue" id="bindqueue" '.$bindqueue.'>'.$locate->Translate("send calls to this queue directly").'						
						</td>
				</tr>

				<!--
				<tr>
					<td nowrap align="left">'.$locate->Translate("CallerID").'</td>
					<td align="left"><input type="text" id="callerid" name="callerid" size="30" maxlength="30" value="'.$campaign['callerid'].'"></td>
				</tr>
				-->
				<tr>
					<td nowrap align="left">'.$locate->Translate("Group").'</td>
					<td align="left">'.$grouphtml.'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Max trytime").'</td>
					<td align="left"><input type="text" id="maxtrytime" name="maxtrytime" size="30" maxlength="30" value="'.$campaign['maxtrytime'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Recyle time").'</td>
					<td align="left"><input type="text" id="recyletime" name="recyletime" size="10" maxlength="10" value="'.$campaign['recyletime'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Min Duration").'</td>
					<td align="left"><input type="text" id="minduration" name="minduration" size="10" maxlength="10" value="'.$campaign['minduration'].'"></td>
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
}
?>

<?
/*******************************************************************************
* worktimepackages.grid.inc.php
* worktimepackages操作类
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
require_once 'worktimepackages.common.php';
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
		
		$sql = "SELECT worktimepackages.*,groupname FROM worktimepackages LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.id = worktimepackages.groupid ";

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " ";
		}else{
			$sql .= " WHERE worktimepackages.groupid = ".$_SESSION['curuser']['groupid']." ";
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

	function insertNewWorktimepackage($f){
		global $db;
		$f = astercrm::variableFiler($f);
		$bindqueue = 0;
		if ($f['bindqueue'] =="on"){
			$bindqueue = 1;
		}
		
		$query= "INSERT INTO worktimepackages SET "
				."worktimepackage_name='".$f['worktimepackage_name']."', "
				."worktimepackage_note='".$f['worktimepackage_note']."', "
				."worktimepackage_status='".$f['worktimepackage_status']."', "				
				."groupid='".$f['groupid']."', "
				."creby = '".$_SESSION['curuser']['username']."',"
				."cretime = now()";
		$sltedWorktimes = 
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
				."waittime='".$f['waittime']."', "
				."outcontext='".$f['outcontext']."', "
				."incontext='".$f['incontext']."', "
				."inexten='".$f['inexten']."', "
				."queuename='".$f['queuename']."', "
				."bindqueue='".$bindqueue."', "
				."maxtrytime='".$f['maxtrytime']."', "
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
			$sql = " SELECT COUNT(*) FROM worktimepackages LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.id = worktimepackages.groupid";
		}else{
			$sql = " SELECT COUNT(*) FROM worktimepackages LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.id = worktimepackages.groupid WHERE worktimepackages.groupid = ".$_SESSION['curuser']['groupid']." ";
		}

		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}

	function &getRecordsFilteredMorewithstype($start, $limit, $filter, $content, $stype,$order,$table){
		global $db;

		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype,"worktimepackages");

		$sql = "SELECT worktimepackages.*, groupname FROM worktimepackages LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.id = worktimepackages.groupid WHERE ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}else{
			$sql .= " worktimepackages.groupid = ".$_SESSION['curuser']['groupid']." ";
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
		
			$joinstr = astercrm::createSqlWithStype($filter,$content,$stype,"worktimepackages");

			$sql = "SELECT COUNT(*) FROM worktimepackages LEFT JOIN astercrm_accountgroup ON astercrm_accountgroup.id = worktimepackages.groupid WHERE ";
			if ($_SESSION['curuser']['usertype'] == 'admin'){
				$sql .= " ";
			}else{
				$sql .= " worktimepackages.groupid = ".$_SESSION['curuser']['groupid']." AND ";
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
		
		$query = "SELECT * FROM worktimes";
		if($_SESSION['curuser']['usertype'] != 'admin') $query .= " WHERE groupid = ".$_SESSION['curuser']['groupid'];
		$worktimes_res = $db->query($query);
		$worktimeshtml .= '';
		$i=0;
		while ($worktimes_row = $worktimes_res->fetchRow()) {
			$i++;
			$cur_content = $worktimes_row['id'].'-'.$locate->Translate("from").':'.$worktimes_row['starttime'].'&nbsp;'.$locate->Translate("to").':'.$worktimes_row['endtime'];
			$worktimeshtml .= '<a href="javascript:void(0);" id="op_'.$i.'" onclick="mf_click('.$i.', \''.$cur_content.'\');">'.$cur_content.'</a><input type="hidden" id="worktimeVal_'.$i.'" name="worktimeVal_'.$i.'" value="'.$worktimes_row['id'].'">';			
		}
		$worktimeshtml = '
			<table width="300" border="0" cellpadding="0" cellspacing="0" id="formTable">
				<tr><td width="180"><div id="worktimeAllDiv">'.$worktimeshtml.'</div></td></tr>
				<tr><td><div id="worktimeSltdDiv"></div><input type="hidden" id="sltedWorktimes" name="sltedWorktimes" value=""></td></tr>
			</table>';
		
//echo $worktimeshtml;exit;
	$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("Package Name").'*</td>
					<td align="left"><input type="text" id="worktimepackage_name" name="worktimepackage_name" size="30" maxlength="60"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Status").'*</td>
					<td align="left" colspan="2">'.$locate->Translate("Enable").'&nbsp;<input type="radio" id="worktimepackage_status" name="worktimepackage_status" value="enable" checked>&nbsp;'.$locate->Translate("Disable").'&nbsp;<input type="radio" id="worktimepackage_status" name="worktimepackage_status" value="disabled" ></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Package Note").'</td>
					<td align="left"><input type="text" id="worktimepackage_note" name="worktimepackage_note" size="30" maxlength="255"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Select Worktime").'*</td>
					<td align="left"><div class="worktimeSltDiv">'.$worktimeshtml.'</div></td>
				</tr>				
				<tr>
					<td nowrap align="left">'.$locate->Translate("Group").'*</td>
					<td align="left">'.$grouphtml.'</td>
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

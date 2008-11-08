<?
/*******************************************************************************
* clid.grid.inc.php
* clid操作类
* Customer class

* @author			Solo Fu <solo.fu@gmail.com>
* @classVersion		1.0
* @date				18 Oct 2007

* Functions List

	getAllRecords				获取所有记录
	getRecordsFiltered			获取记录集
	getNumRows					获取记录集条数
	formAdd						生成添加clid表单的HTML
	formEdit					生成编辑clid表单的HTML
	新增getRecordsFilteredMore  用于获得多条件搜索记录集
	新增getNumRowsMore          用于获得多条件搜索记录条数

* Revision 0.0456  2007/10/30 13:15:00  last modified by solo
* Desc: add channel field 

* Revision 0.045  2007/10/18 13:15:00  last modified by solo
* Desc: page created

********************************************************************************/

require_once 'db_connect.php';
require_once 'clid.common.php';
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
		
		$sql = "SELECT clid.*, groupname,resellername FROM clid LEFT JOIN accountgroup ON accountgroup.id = clid.groupid LEFT JOIN resellergroup ON resellergroup.id = clid.resellerid ";

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " ";
		}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
			$sql .= " WHERE clid.resellerid = ".$_SESSION['curuser']['resellerid']." ";
		}else{
			$sql .= " WHERE clid.groupid = ".$_SESSION['curuser']['groupid']." ";
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

		$sql = "SELECT clid.*, groupname,resellername FROM clid LEFT JOIN accountgroup ON accountgroup.id = clid.groupid LEFT JOIN resellergroup ON resellergroup.id = clid.resellerid WHERE ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
			$sql .= " clid.resellerid = ".$_SESSION['curuser']['resellerid']." ";
		}else{
			$sql .= " clid.groupid = ".$_SESSION['curuser']['groupid']." ";
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
			$sql = " SELECT COUNT(*) FROM clid LEFT JOIN accountgroup ON accountgroup.id = clid.groupid LEFT JOIN resellergroup ON resellergroup.id = clid.resellerid";
		}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
			$sql = " SELECT COUNT(*) FROM clid LEFT JOIN accountgroup ON accountgroup.id = clid.groupid LEFT JOIN resellergroup ON resellergroup.id = clid.resellerid WHERE clid.resellerid = ".$_SESSION['curuser']['resellerid']." ";
		}else{
			$sql = " SELECT COUNT(*) FROM clid LEFT JOIN accountgroup ON accountgroup.id = clid.groupid LEFT JOIN resellergroup ON resellergroup.id = clid.resellerid WHERE clid.groupid = ".$_SESSION['curuser']['groupid']." ";
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

		$sql = "SELECT COUNT(*) FROM clid LEFT JOIN accountgroup ON accountgroup.id = clid.groupid LEFT JOIN resellergroup ON resellergroup.id = clid.resellerid WHERE ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
			$sql .= " clid.resellerid = ".$_SESSION['curuser']['resellerid']." ";
		}else{
			$sql .= " clid.groupid = ".$_SESSION['curuser']['groupid']." ";
		}

			if ($joinstr!=''){
				$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
				$sql .= " AND ".$joinstr;
			}
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;
	}

	function &getRecordsFilteredMorewithstype($start, $limit, $filter, $content, $stype,$order,$table){
		global $db;
		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);

		$sql = "SELECT clid.*, groupname,resellername FROM clid LEFT JOIN accountgroup ON accountgroup.id = clid.groupid LEFT JOIN resellergroup ON resellergroup.id = clid.resellerid WHERE ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
			$sql .= " clid.resellerid = ".$_SESSION['curuser']['resellerid']." ";
		}else{
			$sql .= " clid.groupid = ".$_SESSION['curuser']['groupid']." ";
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

		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);

		$sql = "SELECT COUNT(*) FROM clid LEFT JOIN accountgroup ON accountgroup.id = clid.groupid LEFT JOIN resellergroup ON resellergroup.id = clid.resellerid WHERE ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
			$sql .= " clid.resellerid = ".$_SESSION['curuser']['resellerid']." ";
		}else{
			$sql .= " clid.groupid = ".$_SESSION['curuser']['groupid']." ";
		}

			if ($joinstr!=''){
				$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
				$sql .= " AND ".$joinstr;
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
		global $locate,$config;
		
		/*
		if ($_SESSION['curuser']['usertype'] == 'reseller'){
			$group = astercrm::getAll('accountgroup','resellerid',$_SESSION['curuser']['resellerid']);
		}elseif($_SESSION['curuser']['usertype'] == 'admin'){
			$group = astercrm::getAll('accountgroup');
		}

		while	($group->fetchInto($row)){
			$groupoptions .= "<OPTION value='".$row['id']."'>".$row['groupname']."</OPTION>";
		}
		*/
		$reselleroptions = '';
		$reseller = astercrm::getAll('resellergroup');

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$reselleroptions .= '<select id="resellerid" name="resellerid" onchange="setGroup();">';
			$reselleroptions .= '<option value="0"></option>';
			while	($reseller->fetchInto($row)){
				$reselleroptions .= "<OPTION value='".$row['id']."'>".$row['resellername']."</OPTION>";
			}
			$reselleroptions .= '</select>';
		}else{
			while	($reseller->fetchInto($row)){
				if ($row['id'] == $_SESSION['curuser']['resellerid']){
					$reselleroptions .= $row['resellername'].'<input type="hidden" value="'.$row['id'].'" name="resellerid" id="resellerid">';
					break;
				}
			}
		}
		$group = astercrm::getAll('accountgroup','resellerid',$_SESSION['curuser']['resellerid']);
		if ($_SESSION['curuser']['usertype'] == 'admin' || $_SESSION['curuser']['usertype'] == 'reseller'){
			$groupoptions .= '<select id="groupid" name="groupid">';
			$groupoptions .= "<OPTION value='0'></OPTION>";
			while	($group->fetchInto($row)){
				$groupoptions .= "<OPTION value='".$row['id']."'>".$row['groupname']."</OPTION>";
			}
			$groupoptions .= '</select>';
		}else{
			while	($group->fetchInto($row)){
				if ($row['id'] == $_SESSION['curuser']['groupid']){
					$groupoptions .= $row['groupname'].'<input type="hidden" value="'.$row['id'].'" name="groupid" id="groupid">';
					break;
				}
			}
		}

		$statusoptions ='
							<option value="1">'.$locate->Translate("Avaiable").'</option>
							<option value="-1">'.$locate->Translate("Lock").'</option>
						';
		$pin = astercrm::generateUniquePin(intval($config['system']['pin_len']));
		$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("Caller ID").'*</td>
					<td align="left"><input type="text" id="clid" name="clid" size="25" maxlength="30"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Pin Number").'*</td>
					<td align="left"><input type="text" id="pin" name="pin" size="25" maxlength="30" value="'.$pin.'" readonly><input type="hidden" id="pin" name="pin" value="'.$pin.'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Display").'</td>
					<td align="left"><input type="text" id="display" name="display" size="25" maxlength="20"></td>
				</tr>';
			if($config['system']['setclid'] == 1){
				$html .= '<tr>
					<td nowrap align="left">'.$locate->Translate("Credit Limit").'</td>
					<td align="left"><input type="text" id="creditlimit" name="creditlimit" size="25" maxlength="30"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Limit Status").'</td>
					<td align="left">
					<select id="limittype" name="limittype">
						<option value="" selected>'.$locate->Translate("No limit").'</option>
						<option value="prepaid">'.$locate->Translate("Prepaid").'</option>
						<option value="postpaid">'.$locate->Translate("Postpaid").'</option>
					</select>
					</td>
				</tr>';
			}
				$html .= '<tr>
					<td nowrap align="left">'.$locate->Translate("Reseller").'</td>
					<td align="left">'
						.$reselleroptions.
					'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Group").'</td>
					<td align="left">'
						.$groupoptions.
					'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Status").'</td>
					<td align="left">
						<select id="status" name="status">'
						.$statusoptions.
						'</select>
					</td>
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
		global $locate , $config;

		$clid =& Customer::getRecordByID($id,'clid');

		$reselleroptions = '';
		$reseller = astercrm::getAll('resellergroup');

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$reselleroptions .= '<select id="resellerid" name="resellerid" onchange="setGroup();">';
			$reselleroptions .= '<option value="0"></option>';
			while	($reseller->fetchInto($row)){
				if ($row['id'] == $clid['resellerid']){
					$reselleroptions .= "<OPTION value='".$row['id']."' selected>".$row['resellername']."</OPTION>";
				}else{
					$reselleroptions .= "<OPTION value='".$row['id']."' >".$row['resellername']."</OPTION>";
				}
			}
			$reselleroptions .= '</select>';
		}else{
			while	($reseller->fetchInto($row)){
				if ($row['id'] == $clid['resellerid']){
					$reselleroptions .= $row['resellername'].'<input type="hidden" value="'.$row['id'].'" name="resellerid" id="resellerid">';
					break;
				}
			}
		}

		$group = astercrm::getAll('accountgroup','resellerid',$clid['resellerid']);
		if ($_SESSION['curuser']['usertype'] == 'admin' || $_SESSION['curuser']['usertype'] == 'reseller'){
			$groupoptions .= '<select id="groupid" name="groupid">';
			$groupoptions .= "<OPTION value='0'></OPTION>";
			while	($group->fetchInto($row)){
				if ($row['id'] == $clid['groupid']){
					$groupoptions .= "<OPTION value='".$row['id']."' selected>".$row['groupname']."</OPTION>";
				}else{
					$groupoptions .= "<OPTION value='".$row['id']."' >".$row['groupname']."</OPTION>";
				}
			}
			$groupoptions .= '</select>';
		}else{
			while	($group->fetchInto($row)){
				if ($row['id'] == $clid['groupid']){
					$groupoptions .= $row['groupname'].'<input type="hidden" value="'.$row['id'].'" name="groupid" id="groupid">';
					break;
				}
			}
		}

		if ($clid['status'] == 1){
			$statusoptions ='
							<option value="1" selected>'.$locate->Translate("Avaiable").'</option>
							<option value="-1">'.$locate->Translate("Lock").'</option>
						';
		}else{
			$statusoptions ='
							<option value="1">'.$locate->Translate("Avaiable").'</option>
							<option value="-1" selected>'.$locate->Translate("Lock").'</option>
						';
		}

		$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("Caller ID").'*</td>
					<td align="left"><input type="hidden" id="id" name="id" value="'. $clid['id'].'"><input type="text" id="clid" name="clid" size="25" maxlength="30" value="'.$clid['clid'].'" '.$readonly.'></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Pin Number").'*</td>
					<td align="left"><input type="text" id="pin" name="pin" size="25" maxlength="30" value="'.$clid['pin'].'" readonly><input type="hidden" id="pin" name="pin" value="'.$clid['pin'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Display").'</td>
					<td align="left"><input type="text" id="display" name="display" size="25" maxlength="20" value="'.$clid['display'].'"></td>
				</tr>';

				if($config['system']['setclid'] == 1){
					$html .= '<tr>
						<td nowrap align="left">'.$locate->Translate("Credit Limit").'*</td>
						<td align="left"><input type="text" id="creditlimit" name="creditlimit" size="25" maxlength="30" value="'.$clid['creditlimit'].'"></td>
					</tr>

					<tr>
						<td nowrap align="left">'.$locate->Translate("Cur Credit").'</td>
						<td align="left">
						<input type="text" id="curcreditshow" name="curcreditshow" size="25" maxlength="100" value="'.$clid['curcredit'].'" readonly>
						<input type="hidden" id="curcredit" name="curcredit" value="'.$clid['curcredit'].'">
					</td>
					</tr>
					<tr>
						<td nowrap align="left">'.$locate->Translate("Operate").'</td>
						<td align="left">
							<select id="creditmodtype" name="creditmodtype">
								<option value="">'.$locate->Translate("No change").'</option>
								<option value="add">'.$locate->Translate("Refund").'</option>
								<option value="reduce">'.$locate->Translate("Charge").'</option>
							</select>
							<input type="text" id="creditmod" name="creditmod" size="15" maxlength="100" value="" >
						</td>
					</tr>
					<tr>
					<td nowrap align="left">'.$locate->Translate("Limit Status").'</td>
					<td align="left">
					<select id="limittype" name="limittype">';				
					if ($clid['limittype'] == "postpaid"){
						$html .='
							<option value="">'.$locate->Translate("No limit").'</option>
							<option value="prepaid">'.$locate->Translate("Prepaid").'</option>
							<option value="postpaid" selected>'.$locate->Translate("Postpaid").'</option>';
					}elseif( $clid['limittype'] == "prepaid" ){
						$html .='
							<option value="">'.$locate->Translate("No limit").'</option>
							<option value="prepaid" selected>'.$locate->Translate("Prepaid").'</option>
							<option value="postpaid">'.$locate->Translate("Postpaid").'</option>';
					}else{
						$html .='
							<option value="" selected>'.$locate->Translate("No limit").'</option>
							<option value="prepaid">'.$locate->Translate("Prepaid").'</option>
							<option value="postpaid">'.$locate->Translate("Postpaid").'</option>';
					}
				}

				$html .= '<tr>
					<td nowrap align="left">'.$locate->Translate("Reseller").'</td>
					<td align="left">'
						.$reselleroptions.
					'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Group").'</td>
					<td align="left">
					'
						.$groupoptions.
					'
					</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Status").'</td>
					<td align="left">
						<select id="status" name="status">'
						.$statusoptions.
						'</select>
					</td>
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
	*  Imprime la forma para editar un nuevo registro sobre el DIV identificado por "formDiv".
	*
	*	@param $id		(int)		Identificador del registro a ser editado.
	*	@return $html	(string) Devuelve una cadena de caracteres que contiene la forma con los datos 
	*									a extraidos de la base de datos para ser editados 
	*/
	
	function showClidDetail($id){
		global $locate;
		$clid =& Customer::getRecordByID($id,'clid');
		$html = '
			
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("username").'</td>
					<td align="left">'.$clid['username'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("password").'</td>
					<td align="left">'.$clid['password'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("usertype").'</td>
					<td align="left">'.$clid['usertype'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("extensions").'</td>
					<td align="left">'.$clid['extensions'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("pin_code").'</td>
					<td align="left">'.$clid['pincode'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Callback").'</td>
					<td align="left">'.$clid['callback'].'</td>
				</tr>
			 </table>
			';

		return $html;
	}

}
?>

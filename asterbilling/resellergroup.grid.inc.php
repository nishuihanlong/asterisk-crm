<?
/*******************************************************************************
* resellergroup.grid.inc.php
* resellergroup操作类
* Customer class

* @author			Solo Fu <solo.fu@gmail.com>
* @classVersion		1.0
* @date				18 Oct 2007

* Functions List

	getAllRecords				获取所有记录
	getRecordsFiltered			获取记录集
	getNumRows					获取记录集条数
	formAdd						生成添加resellergroup表单的HTML
	formEdit					生成编辑resellergroup表单的HTML
	新增getRecordsFilteredMore  用于获得多条件搜索记录集
	新增getNumRowsMore          用于获得多条件搜索记录条数

* Revision 0.0456  2007/10/30 13:15:00  last modified by solo
* Desc: add channel field 

* Revision 0.045  2007/10/18 13:15:00  last modified by solo
* Desc: page created

********************************************************************************/

require_once 'db_connect.php';
require_once 'resellergroup.common.php';
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
		
		$sql = "SELECT * FROM resellergroup ";

//		if ($creby != null)
//			$sql .= " WHERE note.creby = '".$_SESSION['curuser']['username']."' ";
			

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
	*  insert a record to accountgroup table
	*
	*	@param $f			(array)		array contain customer fields.
	*	@return $res	(object) 	
	*/
	
	function insertNewResellergroup($f){
		global $db;
		$f = astercrm::variableFiler($f);
		$sql= "INSERT INTO resellergroup SET "
				."resellername='".$f['resellername']."', "
				."accountcode='".$f['accountcode']."', "
				."allowcallback='".$f['allowcallback']."', "
				."creditlimit= ".$f['creditlimit'].", "
				."limittype= '".$f['limittype']."', "
				."multiple= '".$f['multiple']."', "
				."addtime = now() ";
		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	/**
	*  update resellergroup table
	*
	*	@param $f			(array)		array contain customer fields.
	*	@return $res		(object) 		object
	*/

	function updateResellergroupRecord($f){
		global $db;
		$f = astercrm::variableFiler($f);
		if ( $f['creditmodtype'] == '' ){
			$newcurcredit = $f['curcredit'];
		}elseif ( $f['creditmodtype'] == 'add' ){
			$newcurcredit = $f['curcredit'] + $f['creditmod'];
			$historysql = "INSERT INTO credithistory SET "
							."modifytime= now(), "
							."resellerid='".$f['resellerid']."', "
							."srccredit='".$f['curcredit']."', "
							."modifystatus= 'add', "
							."modifyamount='".$f['creditmod']."', "
							."comment='".$f['comment']."', "
							."operator='".$_SESSION['curuser']['userid']."'";
							$historyres =& $db->query($historysql);
		}elseif ( $f['creditmodtype'] == 'reduce' ){
			$newcurcredit = $f['curcredit'] - $f['creditmod'];
			$historysql = "INSERT INTO credithistory SET "
							."modifytime= now(), "
							."resellerid='".$f['resellerid']."', "
							."srccredit='".$f['curcredit']."', "
							."modifystatus= 'reduce', "
							."modifyamount='".$f['creditmod']."', "
							."comment='".$f['comment']."', "
							."operator='".$_SESSION['curuser']['userid']."'";
							$historyres =& $db->query($historysql);
		}

		$sql= "UPDATE resellergroup SET "
				."resellername='".$f['resellername']."', "
				."accountcode='".$f['accountcode']."', "
				."curcredit='".$newcurcredit."', "
				."creditlimit='".$f['creditlimit']."', "
				."limittype='".$f['limittype']."', "
				."multiple= '".$f['multiple']."', "
				."allowcallback='".$f['allowcallback']."', "
				."addtime= now() "
				."WHERE id='".$f['resellerid']."'";

		astercrm::events($sql);
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
		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql = "SELECT * FROM resellergroup"
					." WHERE ".$joinstr."  "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
		}else {
			$sql = "SELECT * FROM resellergroup";
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
		
		$sql = "SELECT COUNT(*) AS numRows FROM resellergroup";
		
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
			if ($joinstr!=''){
				$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
				$sql = 'SELECT COUNT(*) AS numRows FROM resellergroup WHERE '.$joinstr;
			}else {
				$sql = "SELECT COUNT(*) AS numRows FROM resellergroup";
			}
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;
	}

	function &getRecordsFilteredMorewithstype($start, $limit, $filter, $content, $stype,$order,$table){
		global $db;

		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql = "SELECT * FROM resellergroup"
					." WHERE ".$joinstr."  "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
		}else {
			$sql = "SELECT * FROM resellergroup";
		}
		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function &getNumRowsMorewithstype($filter, $content,$stype,$table){
		global $db;
		
		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);			

			if ($joinstr!=''){
				$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
				$sql = 'SELECT COUNT(*) AS numRows FROM resellergroup WHERE '.$joinstr;
			}else {
				$sql = "SELECT COUNT(*) AS numRows FROM resellergroup";
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
			global $locate;
	$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("Reseller Name").'*</td>
					<td align="left"><input type="text" id="resellername" name="resellername" size="25" maxlength="30"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Account Code").'</td>
					<td align="left"><input type="text" id="accountcode" name="accountcode" size="25" maxlength="30"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Callback").'</td>
					<td align="left">
					<select id="allowcallback" name="allowcallback">
						<option value="yes">'.$locate->Translate("Yes").'</option>
						<option value="no">'.$locate->Translate("No").'</option>
					</select>
					</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Credit Limit").'</td>
					<td align="left"><input type="text" id="creditlimit" name="creditlimit" size="25" maxlength="30"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Billsec Multiple").'</td>
					<td align="left"><input type="text" id="multiple" name="multiple" size="6" maxlength="6" value="1.0000"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Limit Type").'</td>
					<td align="left">
					<select id="limittype" name="limittype">
						<option value="" selected>'.$locate->Translate("No limit").'</option>
						<option value="prepaid">'.$locate->Translate("Prepaid").'</option>
						<option value="postpaid">'.$locate->Translate("Postpaid").'</option>
					</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><button id="submitButton" onClick=\'xajax_save(xajax.getFormValues("f"));return false;\'>'.$locate->Translate("continue").'</button></td>
				</tr>

			 </table>
			';

		$html .='
			</form>
			'.$locate->Translate("Obligatory Fields").'
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
		global $locate;
		$resellergroup =& Customer::getRecordByID($id,'resellergroup');
		$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			<input type="hidden" id="resellerid" name="resellerid" value='.$resellergroup['id'].'>
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("Reseller Name").'*</td>
					<td align="left"><input type="text" id="resellername" name="resellername" size="25" maxlength="30" value="'.$resellergroup['resellername'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Account Code").'</td>
					<td align="left"><input type="text" id="accountcode" name="accountcode" size="25" maxlength="30" value="'.$resellergroup['accountcode'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Allow Callback").'</td>
					<td align="left">
					<select id="allowcallback" name="allowcallback">';

					if ($resellergroup['allowcallback'] == "yes"){
						$html .= '<option value="yes" selected>'.$locate->Translate("Yes").'</option>';
						$html .= '<option value="no">'.$locate->Translate("No").'</option>';
					}else{
						$html .= '<option value="yes">'.$locate->Translate("Yes").'</option>';
						$html .= '<option value="no" selected>'.$locate->Translate("No").'</option>';
					}

					$html .='
					</select>
					</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Credit Limit").'</td>
					<td align="left"><input type="text" id="creditlimit" name="creditlimit" size="25" maxlength="30" value="'.$resellergroup['creditlimit'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Cur Credit").'</td>
					<td align="left">
						<input type="text" id="curcreditshow" name="curcreditshow" size="15" maxlength="100" value="'.$resellergroup['curcredit'].'" readonly>

						<input type="hidden" id="curcredit" name="curcredit"  value="'.$resellergroup['curcredit'].'">

					</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Operate").'</td>
					<td align="left">
						<select id="creditmodtype" name="creditmodtype" onchange="showComment(this)">
							<option value="">'.$locate->Translate("No change").'</option>
							<option value="add">'.$locate->Translate("Refund").'</option>
							<option value="reduce">'.$locate->Translate("Charge").'</option>
						</select>
						<input type="text" id="creditmod" name="creditmod" size="15" maxlength="100" value="" disabled><p>'.$locate->Translate("Comment").' :&nbsp;<input type="text" id="comment" name="comment" size="18" maxlength="20" value="" disabled></p>
					</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Limit Type").'</td>
					<td align="left">
					<select id="limittype" name="limittype">';
				if ($resellergroup['limittype'] == "postpaid"){
					$html .='
						<option value="">'.$locate->Translate("No limit").'</option>
						<option value="prepaid">'.$locate->Translate("Prepaid").'</option>
						<option value="postpaid" selected>'.$locate->Translate("Postpaid").'</option>';
				}elseif( $resellergroup['limittype'] == "prepaid" ){
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
/*
			$currenttime = date("Y-m-d H:i:s");
			$currentcredit = astercc::readAmount($resellergroup['id'],null,$resellergroup['billingtime'],$currenttime,'resellercredit');
				$html .='
				<tr>
					<td nowrap align="left">'.$locate->Translate("Billing time")."(".$locate->Translate("for postpaid").')</td>
					<td align="left">'.$resellergroup['billingtime'].'<BR><INPUT TYPE="BUTTON" VALUE="'.$locate->Translate("Reset").'" onClick="setBillingtime(\''.$resellergroup['id'].'\',\''.$currenttime.'\');">'."(".$currentcredit.$locate->Translate(" By ").$currenttime.")".'</td>
				</tr>';
*/
				$html .='
				<tr>
					<td nowrap align="left">'.$locate->Translate("Billsec Multiple").'</td>
					<td align="left"><input type="text" id="multiple" name="multiple" size="6" maxlength="6" value="'.$resellergroup['multiple'].'"></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><button id="submitButton" onClick=\'xajax_update(xajax.getFormValues("f"));return false;\'>'.$locate->Translate("continue").'</button></td>
				</tr>

			 </table>
			';

			

		$html .= '
				</form>
				'.$locate->Translate("Obligatory Fields").'
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
	
	function showGroupDetail($id){
		global $locate;
		$resellergroup =& Customer::getRecordByID($id,'resellergroup');
		$html = '
			
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("username").'</td>
					<td align="left">'.$resellergroup['username'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("password").'</td>
					<td align="left">'.$resellergroup['password'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("usertype").'</td>
					<td align="left">'.$resellergroup['usertype'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("extensions").'</td>
					<td align="left">'.$resellergroup['extensions'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("group_code").'</td>
					<td align="left">'.$resellergroup['groupcode'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Callback").'</td>
					<td align="left">'.$resellergroup['callback'].'</td>
				</tr>
			 </table>
			';

		return $html;
	}

}
?>

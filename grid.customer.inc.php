<?
/* NOTE: For this example, the package PEAR is required, you can see http://pear.php.net for more information 
	In addition, in my example  the "include_pah" is modify including the PEAR full path.
	You can to modify the class methods, as you wish you.
	
	But anyway, the full package contain the DB.php and PEAR.php files obtained from PEAR package.
*/

require_once 'db_connect.php';
require_once 'portal.common.php';
require_once 'include/Localization.php';
$GLOBALS['grid_lan']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'gridcustomer');
		if ($_SESSION['curuser']['country'] != '' )
	{
	$GLOBALS['local_grid1']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'gridcustomer');

}else
	$GLOBALS['local_grid1']=new Localization('cn','ZH','gridcustomer');



/** \brief Customer Class
*

*
* @author	Solo Fu <solo.fu@gmail.com>
* @version	1.0
* @date		13 July 2007
*/

class Customer extends PEAR
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
		
		$sql = "SELECT note.id AS id, note, priority,customer.customer AS customer,contact.contact AS contact,customer.category AS category,note.cretime AS cretime,note.creby AS creby FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid ";

//		if ($creby != null)
		$sql .= " WHERE note.creby = '".$_SESSION['curuser']['username']."' ";
			

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
					." WHERE ".$filter." like '%".$content."%' "
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
		
		$sql = "SELECT COUNT(*) AS numRows FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid  WHERE note.creby = '".$_SESSION['curuser']['username']."'";
		
		if(($filter != null) and ($content != null)){
			$sql = 	"SELECT COUNT(*) AS numRows "
				."FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid "
				." AND  note.creby = '".$_SESSION['curuser']['username']."' "
				."WHERE ".$filter." like '%$content%'";
		}
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}
	
	/**
	*  Devuelte el registro de acuerdo al $id pasado.
	*
	*	@param $id	(int)	Identificador del registro para hacer la b&uacute;squeda en la consulta SQL.
	*	@return $row	(array)	Arreglo que contiene los datos del registro resultante de la consulta SQL.
	*/
	
	function &getRecordByID($id){
		global $db;
		
		$sql = "SELECT note.id AS id, note, priority,customer.name AS customer,contact.contact AS contact,customer.category AS category,note.cretime AS cretime,note.creby AS creby , note.customerid, note.contactid, customer.website AS website, contact.position as position FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid "
				." WHERE note.id = $id";
		Customer::events($sql);
		$row =& $db->getRow($sql);
		return $row;
	}


	function &getNoteListByID($id,$type){
		global $db;
		
		if($type == "customer")
			$sql = "SELECT * FROM note WHERE customerid = '$id' ORDER BY cretime DESC";
		else
			$sql = "SELECT * FROM note WHERE contactid = '$id' ORDER BY cretime DESC";

		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

/*
	function &getCustomerByNoteID($id){
		global $db;
		
		$sql = "SELECT * FROM customer "
				." RIGHT JOIN (SELECT customerid FROM note WHERE id = $id ) g ON customer.id = g.customerid";
		Customer::events($sql);
		$row =& $db->getRow($sql);
		return $row;
	}
*/

	function &getCustomerByID($id,$type="customer"){
		global $db;
		if ($type == 'customer')
			$sql = "SELECT * FROM customer WHERE id = $id";
		else
			$sql = "SELECT * FROM customer RIGHT JOIN (SELECT customerid FROM note WHERE id = $id ) g ON customer.id = g.customerid";
		
		Customer::events($sql);
		$row =& $db->getRow($sql);
		return $row;
	}

	function &getNoteByID($id){
		global $db;
		$sql = "SELECT * FROM note WHERE id = $id";
		
		Customer::events($sql);
		$row =& $db->getRow($sql);
		return $row;
	}


	function &getContactListByID($id){
		global $db;
		$sql = "SELECT id,contact FROM contact WHERE customerid='$id'";
		
		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function &getContactByID($id,$type="contact"){
		global $db;
		
		if ($type == 'contact')
			$sql = "SELECT * FROM contact WHERE id = $id";
		else
			$sql = "SELECT * FROM contact RIGHT JOIN (SELECT contactid FROM note WHERE id = $id ) g ON contact.id = g.contactid";

		Customer::events($sql);
		$row =& $db->getRow($sql);
		return $row;
	}

	/**
	*  Inserta un nuevo registro en la tabla.
	*
	*	@param $f	(array)		Arreglo que contiene los datos del formulario pasado.
	*	@return $res	(object) 	Devuelve el objeto con la respuesta de la sentencia SQL ejecutada del INSERT.

	*	@return $customerID	新增客户的id号
	*/
	
	function insertNewCustomer($f){
		global $db;
		
		$sql= "INSERT INTO customer SET "
				."customer='".$f['customer']."', "
				."website='".$f['website']."', "
				."address='".$f['address']."', "
				."zipcode='".$f['zipcode']."', "
				."category='".$f['category']."', "
				."cretime=now(), "
				."creby='".$_SESSION['curuser']['username']."'";
		Customer::events($sql);
		$res =& $db->query($sql);
		$customerID = mysql_insert_id();
		return $customerID;
		//$newID = mysql_insert_id();
		//return $res;
	}

	/**
	*  Insert a new contact
	*
	*	@param $f	(array)		Arreglo que contiene los datos del formulario pasado.
	*	@paran $customerID 	(int)		该contact所属的customer的id

	*	@return $res	(object) 	Devuelve el objeto con la respuesta de la sentencia SQL ejecutada del INSERT.
	*	@return $contactID	(int) 	新增contact的id
	*/
	
	function insertNewContact($f,$customerID){
		global $db;
		
		$sql= "INSERT INTO contact SET "
				."contact='".$f['contact']."', "
				."position='".$f['position']."', "
				."phone='".$f['phone']."', "
				."ext='".$f['ext']."', "
				."phone1='".$f['phone1']."', "
				."ext1='".$f['ext1']."', "
				."phone2='".$f['phone2']."', "
				."ext2='".$f['ext2']."', "
				."mobile='".$f['mobile']."', "
				."fax='".$f['fax']."', "
				."email='".$f['email']."', "
				."cretime=now(), "
				."creby='".$_SESSION['curuser']['username']."', "
				."customerid=". $customerID ;
		Customer::events($sql);
		$res =& $db->query($sql);
		$contactID = mysql_insert_id();
		return $contactID;
//		return $res;
	}

	/**
	*  Insert a new note
	*
	*	@param $f	(array)		Arreglo que contiene los datos del formulario pasado.
	*	@paran $conatctID 	(int)		该 note 所属的 contact 的 id

	*	@return $res	(object) 	Devuelve el objeto con la respuesta de la sentencia SQL ejecutada del INSERT.
	*/
	
	function insertNewNote($f,$customerID,$contactID){
		global $db;
		
		$sql= "INSERT INTO note SET "
				."note='".$f['note']."', "
//				."process='".$f['process']."', "
				."priority=".$f['priority'].", "
				."cretime=now(), "
				."creby='".$_SESSION['curuser']['username']."', "
				."customerid=". $customerID . ", "
				."contactid=". $contactID ;
		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	/**
	*  Actualiza un registro de la tabla.
	*
	*	@param $f	(array)		Arreglo que contiene los datos del formulario pasado.
	*	@return $res	(object)	Devuelve el objeto con la respuesta de la sentencia SQL ejecutada del UPDATE.
	*/
	
	function updateCustomerRecord($f){
		global $db;
		
		$sql= "UPDATE customer SET "
				."customer='".$f['customer']."', "
				."website='".$f['website']."', "
				."address='".$f['address']."', "
				."zipcode='".$f['zipcode']."', "
				."category='".$f['category']."' "
				."WHERE id='".$f['customerid']."'";

		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}


	function updateNoteRecord($f,$type="update"){
		global $db;
		
		if ($type == 'update')

			$sql= "UPDATE note SET "
					."note='".$f['note']."', "
					."priority=".$f['priority']." "
					."WHERE id='".$f['noteid']."'";
		else
			if (empty($f['note']))
				$sql= "UPDATE note SET "
						."priority=".$f['priority']." "
						."WHERE id='".$f['noteid']."'";
			else
				$sql= "UPDATE note SET "
						."note=CONCAT(note,'<br>',now(),'  ".$f['note']." by " .$_SESSION['curuser']['username']. "'), "
						."priority=".$f['priority']." "
						."WHERE id='".$f['noteid']."'";

		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	/**
	*  Actualiza un registro de la tabla.
	*
	*	@param $f	(array)		Arreglo que contiene los datos del formulario pasado.
	*	@return $res	(object)	Devuelve el objeto con la respuesta de la sentencia SQL ejecutada del UPDATE.
	*/
	
	function updateContactRecord($f){
		global $db;
		
		$sql= "UPDATE contact SET "
				."contact='".$f['contact']."', "
				."position='".$f['position']."', "
				."phone='".$f['phone']."', "
				."ext='".$f['ext']."', "
				."phone1='".$f['phone1']."', "
				."ext1='".$f['ext1']."', "
				."phone2='".$f['phone2']."', "
				."ext2='".$f['ext2']."', "
				."mobile='".$f['mobile']."', "
				."fax='".$f['fax']."', "
				."email='".$f['email']."' "
				."WHERE id='".$f['contactid']."'";
		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	
	/**
	*  Borra un registro de la tabla.
	*
	*	@param $id		(int)	Identificador del registro a ser borrado.
	*	@return $res	(object) Devuelve el objeto con la respuesta de la sentencia SQL ejecutada del DELETE.
	*/
	
	function deleteRecord($id){
		global $db;
	
		$sql = "DELETE FROM note WHERE id = $id";
		Customer::events($sql);
		$res =& $db->query($sql);

		return $res;
	}

	function updateField($table,$field,$value,$id){

		global $db;
	
		$sql = "UPDATE $table SET $field='$value' WHERE id='$id'";
		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
		
	}

	
	/**
	*  Imprime la forma para agregar un nuevo registro sobre el DIV identificado por "formDiv".
	*
	*	@param ninguno
	*	@return $html	(string) Devuelve una cadena de caracteres que contiene la forma para insertar 
	*							un nuevo registro.
	*/

	function formAdd($callerid = null,$customerid = null, $contactid = null){
	global $grid_lan;
	$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			
			<table border="1" width="100%" class="adminlist">
			<tr>
				<td nowrap align="left" colspan="2">'.$grid_lan->Translate("Phone_Numbr").''. $callerid .' </td>
			</tr>';
	
	if ($customerid == null){
		$html .= '
				<tr>
					<td nowrap align="left">'.$grid_lan->Translate("Customer").'</td>
					<td align="left"><input type="text" id="customer" name="customer" value="" onkeyup="ajax_showOptions(this,\'getCustomersByLetters\',event)" size="50" maxlength="50" autocomplete="off"><input type="button" value="'.$grid_lan->Translate("Confirm").'" id="btnConfirmCustomer" name="btnConfirmCustomer" onclick="btnConfirmCustomerOnClick();"><input type="hidden" id="customerid" name="customerid" value="'.$grid_lan->Translate("can").'"></td>
				</tr>
				<tr id="websiteTR" name="websiteTR">
					<td nowrap align="left">'.$grid_lan->Translate("Website").'</td>
					<td align="left"><input type="text" id="website" name="website" size="50" maxlength="100" value="http://"><input type="button" value="'.$grid_lan->Translate("go").'" onclick="openWindow(xajax.$(\'website\').value);return false;"></td>
				</tr>
				<tr id="addressTR" name="addressTR">
					<td nowrap align="left">'.$grid_lan->Translate("Address").'</td>
					<td align="left"><input type="text" id="address" name="address" size="50" maxlength="200"></td>
				</tr>
				<tr id="zipcodeTR" name="zipcodeTR">
					<td nowrap align="left">'.$grid_lan->Translate("Zip_Code").'</td>
					<td align="left"><input type="text" id="zipcode" name="zipcode" size="10" maxlength="10"></td>
				</tr>
				<tr id="categoryTR" name="categoryTR">
					<td nowrap align="left">'.$grid_lan->Translate("Category").'</td>
					<td align="left"><input type="text" id="category" name="category" size="35"></td>
				</tr>';
	}else{
		$customer =& Customer::getCustomerByID($customerid);
		$html .= '
				<tr>
					<td nowrap align="left"><a href=? onclick="xajax_showCustomer('. $customerid .');return false;">Customer</a>*</td>
					<td align="left"><input type="text" id="customer" name="customer" value="'. $customer[customer] .'" onkeyup="ajax_showOptions(this,\'getCustomersByLetters\',event)" size="50" maxlength="50" autocomplete="off" readOnly><input type="button" value="Cancel" id="btnConfirmCustomer" name="btnConfirmCustomer" onclick="btnConfirmCustomerOnClick();"><input type="hidden" id="customerid" name="customerid" value="'. $customerid .'"></td>
				</tr>
				';
	}

	if ($contactid == null){
		$html .='
				<tr>
					<td nowrap align="left">'.$grid_lan->Translate("Contact").'</td>
					<td align="left"><input type="text" id="contact" name="contact" value="" onkeyup="ajax_showOptions(this,\'customerid='.$customerid.'&getContactsByLetters\',event)" size="35" maxlength="50" autocomplete="off"><input type="button" value="'.$grid_lan->Translate("Confirm").'" id="btnConfirmContact" name="btnConfirmContact" onclick="btnConfirmContactOnClick();"><input type="hidden" id="contactid" name="contactid" value=""></td>
				</tr>
				<tr name="positionTR" id="positionTR">
					<td nowrap align="left">'.$grid_lan->Translate("Position").'</td>
					<td align="left"><input type="text" id="position" name="position" size="35"></td>
				</tr>
				<tr name="phoneTR" id="phoneTR">
					<td nowrap align="left">'.$grid_lan->Translate("phone").'</td>
					<td align="left"><input type="text" id="phone" name="phone" size="35" value="'. $callerid .'">-<input type="text" id="ext" name="ext" size="6" maxlength="6" value=""></td>
				</tr>
				<tr name="phone1TR" id="phone1TR">
					<td nowrap align="left">'.$grid_lan->Translate("phone1").'</td>
					<td align="left"><input type="text" id="phone1" name="phone1" size="35" value="">-<input type="text" id="ext1" name="ext1" size="6" maxlength="6" value=""></td>
				</tr>
				<tr name="phone2TR" id="phone2TR">
					<td nowrap align="left">'.$grid_lan->Translate("phone2").'</td>
					<td align="left"><input type="text" id="phone2" name="phone2" size="35" value="">-<input type="text" id="ext2" name="ext2" size="6" maxlength="6" value=""></td>
				</tr>
				<tr name="mobileTR" id="mobileTR">
					<td nowrap align="left">'.$grid_lan->Translate("Mobile").'</td>
					<td align="left"><input type="text" id="mobile" name="mobile" size="35"></td>
				</tr>
				<tr name="faxTR" id="faxTR">
					<td nowrap align="left">'.$grid_lan->Translate("Fax").'</td>
					<td align="left"><input type="text" id="fax" name="fax" size="35"></td>
				</tr>
				<tr name="emailTR" id="emailTR">
					<td nowrap align="left">'.$grid_lan->Translate("Email").'</td>
					<td align="left"><input type="text" id="email" name="email" size="35"></td>
				</tr>					
				';
	}else{
		$contact =& Customer::getContactByID($contactid);
		$html .='
				<tr>
					<td nowrap align="left"><a href=? onclick="xajax_showContact('. $contactid .');return false;">'.$grid_lan->Translate("Contact").'</a></td>
					<td align="left">
					<input type="text" id="contact" name="contact" value="'.$contact['contact'].'" onkeyup="ajax_showOptions(this,\'customerid='.$customerid.'&getContactsByLetters\',event)" size="35" maxlength="50" autocomplete="off" readOnly>
					<input type="button" value="Cancel" id="btnConfirmContact" name="btnConfirmContact" onclick="btnConfirmContactOnClick();">
					<input type="hidden" id="contactid" name="contactid" value="'. $contactid .'">
					<input type="hidden" id="contactDetial" name="contactDetial" value="OFF">
					[<a href=? onclick="
						if (xajax.$(\'contactDetial\').value == \'OFF\'){
							xajax.$(\'positionTR\').style.display = \'\';
							xajax.$(\'phoneTR\').style.display = \'\';
							xajax.$(\'phone1TR\').style.display = \'\';
							xajax.$(\'phone2TR\').style.display = \'\';
							xajax.$(\'mobileTR\').style.display = \'\';
							xajax.$(\'faxTR\').style.display = \'\';
							xajax.$(\'emailTR\').style.display = \'\';
							xajax.$(\'contactDetial\').value = \'ON\';
						}else{
							xajax.$(\'positionTR\').style.display = \'none\';
							xajax.$(\'phoneTR\').style.display = \'none\';
							xajax.$(\'phone1TR\').style.display = \'none\';
							xajax.$(\'phone2TR\').style.display = \'none\';
							xajax.$(\'mobileTR\').style.display = \'none\';
							xajax.$(\'faxTR\').style.display = \'none\';
							xajax.$(\'emailTR\').style.display = \'none\';
							xajax.$(\'contactDetial\').value = \'OFF\';
						};
						return false;">
						Detail
					</a>]
					</td>
				</tr>
				<tr name="positionTR" id="positionTR" style="display:none">
					<td nowrap align="left">'.$grid_lan->Translate("Position").'</td>
					<td align="left"><input type="text" id="position" name="position" size="35" value="'.$contact['position'].'"></td>
				</tr>
				<tr name="phoneTR" id="phoneTR" style="display:none">
					<td nowrap align="left">'.$grid_lan->Translate("Phone").'</td>
					<td align="left"><input type="text" id="phone" name="phone" size="35" value="'. $contact['phone'] .'">-<input type="text" id="ext" name="ext" size="6" maxlength="6" value="'.$contact['ext'].'"></td>
				</tr>
				<tr name="phone1TR" id="phone1TR" style="display:none">
					<td nowrap align="left">'.$grid_lan->Translate("Phone1").'</td>
					<td align="left"><input type="text" id="phone1" name="phone1" size="35" value="'. $contact['phone1'] .'">-<input type="text" id="ext1" name="ext1" size="6" maxlength="6" value="'.$contact['ext1'].'"></td>
				</tr>
				<tr name="phone2TR" id="phone2TR" style="display:none">
					<td nowrap align="left">'.$grid_lan->Translate("Phone2").'</td>
					<td align="left"><input type="text" id="phone2" name="phone2" size="35" value="'. $callerid .'">-<input type="text" id="ext2" name="ext2" size="6" maxlength="6" value=""></td>
				</tr>
				<tr name="mobileTR" id="mobileTR" style="display:none">
					<td nowrap align="left">'.$grid_lan->Translate("Mobile").'</td>
					<td align="left"><input type="text" id="mobile" name="mobile" size="35" value="'. $contact['mobile'] .'"></td>
				</tr>
				<tr name="faxTR" id="faxTR" style="display:none">
					<td nowrap align="left">'.$grid_lan->Translate("Fax").'</td>
					<td align="left"><input type="text" id="fax" name="fax" size="35" value="'. $contact['fax'] .'"></td>
				</tr>
				<tr name="emailTR" id="emailTR" style="display:none">
					<td nowrap align="left">'.$grid_lan->Translate("Email").'</td>
					<td align="left"><input type="text" id="email" name="email" size="35" value="'.$contact['email'].'"></td>
				</tr>					
				';
	}

	$html .='
			<tr>
				<td nowrap align="left">'.$grid_lan->Translate("Note").'</td>
				<td align="left"><input type="text" id="note" name="note" size="35"></td>
			</tr>
			<tr>
				<td nowrap align="left">'.$grid_lan->Translate("Priority").'</td>
				<td align="left">
					<select id="priority" name="priority">
						<option value=0>0</option>
						<option value=1>1</option>
						<option value=2>2</option>
						<option value=3>3</option>
						<option value=4>4</option>
						<option value=5 selected>5</option>
						<option value=6>6</option>
						<option value=7>7</option>
						<option value=8>8</option>
						<option value=9>9</option>
						<option value=10>10</option>
					</select> 
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center"><button id="submitButton" onClick=\'xajax_save(xajax.getFormValues("f"));return false;\'>'.$grid_lan->Translate("Continue").'</button></td>
			</tr>
			</table>';
/*
		$noteList =& Customer::getNoteListByID($customerid,'customer');//该用户的所有note
		$html .= '<table border="1" width="100%" class="adminlist" id="customernotelist" name="customernotelist">';
		while	($noteList->fetchInto($row)){
			$html .= '
				<tr><td align="left" width="25">'. $row['creby'] .'
				</td><td>'.$row['note'].'</td><td>'.$row['cretime'].'</td></tr>
				';
		}
		$html .= '</table>';
*/
		$html .='
			</form>
			* Obligatory fields
			';
		
		return $html;
	}

function showNoteList($id,$type){
	$noteList =& Customer::getNoteListByID($id,$type);
	$html = '
			<table border="1" width="100%" class="adminlist">
			';

	while	($noteList->fetchInto($row)){
		$html .= '
			<tr><td align="left" width="25">'. $row['creby'] .'
			</td><td>'.$row['note'].'</td><td>'.$row['cretime'].'</td></tr>
			';
	}
	$html .= '</table>';

	return $html;
}

	/**
	*  Imprime la forma para editar un nuevo registro sobre el DIV identificado por "formDiv".
	*
	*	@param $id		(int)		Identificador del registro a ser editado.
	*	@return $html	(string) Devuelve una cadena de caracteres que contiene la forma con los datos 
	*									a extraidos de la base de datos para ser editados 
	*/
	
	function formEdit($id , $type){
			global $grid_lan;
		if ($type == 'note'){
			$note =& Customer::getNoteByID($id);
			$html = '
					<form method="post" name="f" id="f">
					<input type="hidden" id="noteid"  name="noteid" value="'.$note['id'].'">
					<table border="0" width="100%">
					<tr>
						<td nowrap align="left">Note</td>
						<td align="left">'.$note['note']. '</td>
					</tr>
					<tr>
						<td nowrap align="left">Append</td>
						<td align="left"><input type="text" value="" name="note" id="note" length="35"></td>
					</tr>
					<tr>
						<td nowrap align="left">Priority</td>
						<td align="left">
							<select id="priority" name="priority">
								<option value='. $note['priority'] .' selected>'. $note['priority'] .'</option>
								<option value=0>0</option>
								<option value=1>1</option>
								<option value=2>2</option>
								<option value=3>3</option>
								<option value=4>4</option>
								<option value=5>5</option>
								<option value=6>6</option>
								<option value=7>7</option>
								<option value=8>8</option>
								<option value=9>9</option>
								<option value=10>10</option>
							</select> 
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">[<a href=? onclick="xajax_showCustomer(\'' . $note['customerid'] . '\');return false;">customer</a>]&nbsp;&nbsp;&nbsp;&nbsp;[<a href=? onclick="xajax_showContact(\'' . $note['contactid'] . '\');return false;">'.$grid_lan->Translate("contact").'</a>]</td>
					</tr>
					<tr>
						<td colspan="2" align="center"><button id="btnContinue" name="btnContinue"  onClick=\'xajax_update(xajax.getFormValues("f"),"note");return false;\'>Continue</button></td>
					</tr>
					';

		}elseif ($type == 'customer'){
			$customer =& Customer::getCustomerByID($id);
			$html = '
					<form method="post" name="f" id="f">
					<input type="hidden" id="customerid"  name="customerid" value="'.$customer['id'].'">
					<table border="0" width="100%">
					<tr id="customer" name="customer">
						<td nowrap align="left">'.$grid_lan->Translate("Customer").'</td>
						<td align="left"><input type="text" id="customer" name="customer" size="50" maxlength="100" value="' . $customer['customer'] . '"></td>
					</tr>
					<tr id="websiteTR" name="websiteTR">
						<td nowrap align="left">'.$grid_lan->Translate("Website").'</td>
						<td align="left"><input type="text" id="website" name="website" size="50" maxlength="100" value="' . $customer['website'] . '"><input type="button" value='.$grid_lan->Translate("go").' onclick="openWindow(xajax.$(\'website\').value);return false;"></td>
					</tr>
					<tr id="addressTR" name="addressTR">
						<td nowrap align="left">'.$grid_lan->Translate("Address").'</td>
						<td align="left"><input type="text" id="address" name="address" size="50" maxlength="200" value="' . $customer['address'] . '"></td>
					</tr>
					<tr id="zipcodeTR" name="zipcodeTR">
						<td nowrap align="left">'.$grid_lan->Translate("Zip_Code").'</td>
						<td align="left"><input type="text" id="zipcode" name="zipcode" size="10" maxlength="10" value="' . $customer['zipcode'] . '"></td>
					</tr>
					<tr id="categoryTR" name="categoryTR">
						<td nowrap align="left">'.$grid_lan->Translate("Category").'</td>
						<td align="left"><input type="text" id="category" name="category" size="35"  value="' . $customer['category'] . '"></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><button  id="btnContinue" name="btnContinue"  onClick=\'xajax_update(xajax.getFormValues("f"),"customer");return false;\'>Continue</button></td>
					</tr>
					';
		}else {
			$contact =& Customer::getContactByID($id);
			$html = '
					<form method="post" name="formEdit" id="formEdit">
					<input type="hidden" id="contactid"  name="contactid" value="'.$contact['id'].'">
					<table border="0" width="100%">
					<tr>
						<td nowrap align="left">'.$grid_lan->Translate("contact").'</td>
						<td align="left"><input type="text" id="contact" name="contact" size="35"  value="'.$contact['contact'].'"></td>
					</tr>
					<tr name="positionTR" id="positionTR">
						<td nowrap align="left">'.$grid_lan->Translate("Position").'</td>
						<td align="left"><input type="text" id="position" name="position" size="35"  value="'.$contact['position'].'"></td>
					</tr>
					<tr name="phoneTR" id="phoneTR">
						<td nowrap align="left">'.$grid_lan->Translate("phone").'</td>
						<td align="left"><input type="text" id="phone" name="phone" size="35"  value="'.$contact['phone'].'">-<input type="text" id="ext" name="ext" size="6" maxlength="6"  value="'.$contact['ext'].'"></td>
					</tr>
					<tr name="phone1TR" id="phone1TR">
						<td nowrap align="left">'.$grid_lan->Translate("phone1").'</td>
						<td align="left"><input type="text" id="phone1" name="phone1" size="35"  value="'.$contact['phone1'].'">-<input type="text" id="ext1" name="ext1" size="6" maxlength="6"  value="'.$contact['ext1'].'"></td>
					</tr>
					<tr name="phone2TR" id="phone2TR">
						<td nowrap align="left">'.$grid_lan->Translate("phone2").'</td>
						<td align="left"><input type="text" id="phone2" name="phone2" size="35"  value="'.$contact['phone2'].'">-<input type="text" id="ext2" name="ext2" size="6" maxlength="6"  value="'.$contact['ext2'].'"></td>
					</tr>
					<tr name="mobileTR" id="mobileTR">
						<td nowrap align="left">'.$grid_lan->Translate("Mobile").'</td>
						<td align="left"><input type="text" id="mobile" name="mobile" size="35" value="'.$contact['mobile'].'"></td>
					</tr>
					<tr name="faxTR" id="faxTR">
						<td nowrap align="left">'.$grid_lan->Translate("Fax").'</td>
						<td align="left"><input type="text" id="fax" name="fax" size="35" value="'.$contact['fax'].'"></td>
					</tr>
					<tr name="emailTR" id="emailTR">
						<td nowrap align="left">'.$grid_lan->Translate("Email").'</td>
						<td align="left"><input type="text" id="email" name="email" size="35" value="'.$contact['email'].'"></td>
					</tr>					
					<tr>
						<td colspan="2" align="center"><button id="btnContinue" name="btnContinue"  onClick=\'xajax_update(xajax.getFormValues("formEdit"),"contact");return false;\'>Continue</button></td>
					</tr>
					';
		}

		$html .= '
				</table>
				</form>
				* Obligatory fields
				';

		return $html;
	}
	
	/**
	*  Muestra todos los datos de un registro sobre el DIV identificado por "formDiv".
	*
	*	@param $id		(int)		Identificador del registro a ser mostrado.
	*	@return $html	(string) Devuelve una cadena de caracteres que contiene una tabla con los datos 
	*									a extraidos de la base de datos para ser mostrados 
	*/
	function showCustomerRecord($id,$type="customer"){
    	global $grid_lan;
		$customer =& Customer::getCustomerByID($id,$type);
		$contactList =& Customer::getContactListByID($customer['id']);

		$html = '
				<table border="0" width="100%">
				<tr>
					<td nowrap align="left" width="80">'.$grid_lan->Translate("Customer").'&nbsp;[<a href=? onclick="xajax_showNote(\''.$customer['id'].'\',\'customer\');return false;">'.$grid_lan->Translate("Note").'</a>]</td>
					<td align="left">'.$customer['customer'].'&nbsp;[<a href=? onclick="xajax_edit(\''.$customer['id'].'\',\'\',\'customer\');return false;">'.$grid_lan->Translate("Edit").'</a>]</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$grid_lan->Translate("Address").'</td>
					<td align="left">'.$customer['address'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$grid_lan->Translate("Zip_Code").'</td>
					<td align="left">'.$customer['zipcode'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$grid_lan->Translate("Website").'</td>
					<td align="left"><a href="'.$customer['website'].'" target="_blank">'.$customer['website'].'</a></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$grid_lan->Translate("Category").'</td>
					<td align="left">'.$customer['category'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$grid_lan->Translate("Creattime").'</td>
					<td align="left">'.$customer['cretime'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$grid_lan->Translate("CreatBY").'</td>
					<td align="left">'.$customer['creby'].'</td>
				</tr>
				</table>
					<a href=? onclick="if (xajax.$(\'allContact\').value==\'off\'){xajax.$(\'contactList\').style.display=\'block\';xajax.$(\'allContact\').value=\'on\'}else{xajax.$(\'contactList\').style.display=\'none\';xajax.$(\'allContact\').value=\'off\'} return false;">'.$grid_lan->Translate("Display_All").'</a>
					<input type="hidden" id="allContact" name="allContact" value="off">
				<table border="0" width="100%" id="contactList" name="contactList" style="display:none">
					';

				while	($contactList->fetchInto($row)){
					$html .= '<tr>';
					for ($i=1;$i<5;$i++){
						$html .= '
								<td align="left" width="20%">
									<a href=? onclick="xajax_showContact(\''. $row['id'] .'\');return false;">'. $row['contact'] .'</a>
								</td>
								';
						if (!$contactList->fetchInto($row))
							$html .= '<td>&nbsp;</td>';
					}
					$html .= '</tr>';
				}

				$html .= '
					</table>';

		return $html;

	}

	/**
	*  Muestra todos los datos de un registro sobre el DIV identificado por "formDiv".
	*
	*	@param $id		(int)		Identificador del registro a ser mostrado.
	*	@return $html	(string) Devuelve una cadena de caracteres que contiene una tabla con los datos 
	*									a extraidos de la base de datos para ser mostrados 
	*/
	function showContactRecord($id,$type="contact"){
    	global $grid_lan;
		$contact =& Customer::getContactByID($id,$type);


		$html = '
				<table border="0" width="100%">
				<tr>
					<td nowrap align="left" width="80">'.$grid_lan->Translate("contact").'&nbsp;[<a href=? onclick="xajax_showNote(\''.$contact['id'].'\',\'contact\');return false;">'.$grid_lan->Translate("Note").'</a>]</td>
					<td align="left">'.$contact['contact'].'&nbsp;&nbsp;&nbsp;&nbsp;<span align="right">[<a href=? onclick="xajax_add(xajax.$(\'callerid\').value,xajax.$(\'customerid\').value,\''. $contact['id'] .'\');return false;">'.$grid_lan->Translate("copy").'</a>]</span>&nbsp;&nbsp;[<a href=? onclick="xajax_edit(\''.$contact['id'].'\',\'\',\'contact\');return false;">'.$grid_lan->Translate("Edit").'</a>]</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$grid_lan->Translate("Position").'</td>
					<td align="left">'.$contact['position'].'</td>
				</tr>';

		if ($contact['ext'] == '')
			$html .='
					<tr>
						<td nowrap align="left">'.$grid_lan->Translate("phone1").'</td>
						<td align="left"><a href=? onclick="xajax_dial(\''.$contact['phone'].'\');return false;">'.$contact['phone'].'</a></td>
					</tr>';
		else
			$html .='
					<tr>
						<td nowrap align="left">'.$grid_lan->Translate("phone1").'</td>
						<td align="left"><a href=? onclick="xajax_dial(\''.$contact['phone'].'\');return false;">'.$contact['phone'].'</a> ext: '.$contact['ext'].'</td>
					</tr>';

		if ($contact['phone1'] != '' || $contact['ext1'] != '')
			if ($contact['ext1'] == '')
				$html .='
						<tr>
							<td nowrap align="left">'.$grid_lan->Translate("Phone1").'</td>
							<td align="left"><a href="?" onclick="xajax_dial(\''.$contact['phone1'].'\');return false;">'.$contact['phone1'].'</a></td>
						</tr>';
			else
				$html .='
						<tr>
							<td nowrap align="left">'.$grid_lan->Translate("phone1").'</td>
							<td align="left"><a href="?" onclick="xajax_dial(\''.$contact['phone1'].'\');return false;">'.$contact['phone1'].'</a> ext: '.$contact['ext1'].'</td>
						</tr>';
		
		if ($contact['phone2'] != '' || $contact['ext2'] != '')
			if ($contact['ext2'] == '')
				$html .='
						<tr>
							<td nowrap align="left">'.$grid_lan->Translate("phone2").'</td>
							<td align="left"><a href="?" onclick="xajax_dial(\''.$contact['phone2'].'\');return false;">'.$contact['phone2'].'</a></td>
						</tr>';
			else
				$html .='
						<tr>
							<td nowrap align="left">'.$grid_lan->Translate("phone2").'</td>
							<td align="left"><a href="?" onclick="xajax_dial(\''.$contact['phone2'].'\');return false;">'.$contact['phone2'].'</a> ext: '.$contact['ext2'].'</td>
						</tr>';

		$html .='
				<tr>
					<td nowrap align="left">'.$grid_lan->Translate("Mobile").'</td>
					<td align="left"><a href="?" onclick="xajax_dial(\''.$contact['mobile'].'\');return false;">'.$contact['mobile'].'</a></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$grid_lan->Translate("Fax").'</td>
					<td align="left">'.$contact['fax'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$grid_lan->Translate("Email").'</td>
					<td align="left">'.$contact['email'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$grid_lan->Translate("Creattime").'</td>
					<td align="left">'.$contact['cretime'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$grid_lan->Translate("CreatBY").'</td>
					<td align="left">'.$contact['creby'].'</td>
				</tr>
				</table>';

		return $html;

	}
	
	/**
	*  Verifica si los datos de la forma enviados son correctos de acuerdo a cada validaci&oacute;n en particular.
	*
	*  En este metodo es necesario que sea revisado para hacer las validaciones correspondientes a cada una de las
	*  entradas del formulario.
	*
	*	@param $f	(array)		Arreglo que contiene los datos del formularios procesado.
	*	@param $new	(boolean)	Si recibe el valor de 1 significa que la acci&oacute;n es insertar un nuevo registro,
	* 									de lo	contrario significa que esta editando el registro, por tanto no revisa si la
	*									clave es	repetida.
	*	@return $msg	(string)	Devuelve 0 si todos los datos estan correctos, de lo contrario devuelve el mensaje
	*									correspondiente a la validaci&oacute;n.
	*/
	function checkAllData($f,$new = 0){
		if(empty($f['customer'])) return "The field Customer does not have to be null";
		if(empty($f['contact'])) return "The field Contact does not have to be null";
		if(empty($f['note'])) return "The field Note does not have to be null";
	 	return 0;
	}

	function events($event = null){
		//global $db;
		global $login;
		
		if(LOG_ENABLED){
			$now = date("Y-M-d H:i:s");
   		
			$fd = fopen (FILE_LOG,'a');
			$log = $now." ".$_SERVER["REMOTE_ADDR"] ." - $event \n";
   		fwrite($fd,$log);
   		fclose($fd);
		}
	}


//判断一个表中是否存在某一个
//输入
//$tblName: 表名
//$fldName: 域名
//$myValue: 域值
//$type:数值类型(string或者numeric)
//返回
//值的id,如果为0则说明不存在
	function checkValues($tblName,$fldName,$myValue,$type="string",$fldName1 = null,$myValue1 = null,$type1 = "string"){

		global $db;

		if ($type == "string")
			$sql = "SELECT id FROM $tblName WHERE $fldName='$myValue'";
		else
			$sql = "SELECT id FROM $tblName WHERE $fldName=$myValue";
		
		if ($fldName1 != null)
			if ($type1 == "string")
				$sql .= "AND $fldName1='$myValue1'";
			else
				$sql .= "AND $fldName1=$myValue1";

		
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}
}
?>

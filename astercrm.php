<?
Class astercrm extends PEAR{
	/**
	*  insert a record to customer table
	*
	*	@param $f			(array)		array contain customer fields.
	*	@return $customerid	(object) 	id number for the record just inserted.

	*/
	
	function insertNewCustomer($f){
		global $db;
		
		$sql= "INSERT INTO customer SET "
				."customer='".$f['customer']."', "
				."website='".$f['website']."', "
				."address='".$f['address']."', "
				."zipcode='".$f['zipcode']."', "
				."city='".$f['city']."', "
				."state='".$f['state']."', "
				."contact='".$f['customerContact']."', "
				."contactgender='".$f['customerContactGender']."', "
				."phone='".$f['customerPhone']."', "
				."category='".$f['category']."', "
				."bankname='".$f['bankname']."', "
				."bankaccount='".$f['bankaccount']."', "
				."cretime=now(), "
				."creby='".$_SESSION['curuser']['username']."'";
		astercrm::events($sql);
		$res =& $db->query($sql);
		$customerid = mysql_insert_id();
		return $customerid;
	}

	/**
	*  insert a record to contact table
	*
	*	@param $f			(array)		array contain contact fields.
	*	@param $customerid	(array)		customer id of the new contact
	*	@return $customerid	(object) 	id number for the record just inserted.
	*/
	
	function insertNewContact($f,$customerid){
		global $db;
		
		$sql= "INSERT INTO contact SET "
				."contact='".$f['contact']."', "
				."gender='".$f['gender']."', "
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
				."customerid=". $customerid ;
		astercrm::events($sql);
		$res =& $db->query($sql);
		$contactid = mysql_insert_id();
		return $contactid;
	}


	/**
	*  Insert a new note
	*
	*	@param $f			(array)		array contain note fields.
	*	@paran $customerid 	(int)		customer id of the new note
	*	@paran $contactid 	(int)		contact id of the new note
	*	@return $res	(object) 		object
	*/
	
	function insertNewNote($f,$customerid,$contactid){
		global $db;
		
		$sql= "INSERT INTO note SET "
				."note='".$f['note']."', "
				."priority=".$f['priority'].", "
				."cretime=now(), "
				."creby='".$_SESSION['curuser']['username']."', "
				."customerid=". $customerid . ", "
				."contactid=". $contactid ;
		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}


	/**
	*  update customer table
	*
	*	@param $f			(array)		array contain customer fields.
	*	@return $res		(object) 		object
	*/
	
	function updateCustomerRecord($f){
		global $db;
		$sql= "UPDATE customer SET "
				."customer='".$f['customer']."', "
				."website='".$f['website']."', "
				."address='".$f['address']."', "
				."zipcode='".$f['zipcode']."', "
				."phone='".$f['customerPhone']."', "
				."contact='".$f['customerContact']."', "
				."contactgender='".$f['customerContactGender']."', "
				."state='".$f['state']."', "
				."city='".$f['city']."', "
				."category='".$f['category']."', "
				."bankname='".$f['bankname']."', "
				."bankaccount='".$f['bankaccount']."' "
				."WHERE id='".$f['customerid']."'";

		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	/**
	*  update contact table
	*
	*	@param $f			(array)		array contain contact fields.
	*	@return $res		(object)	object
	*/
	
	function updateContactRecord($f){
		global $db;
		
		$sql= "UPDATE contact SET "
				."contact='".$f['contact']."', "
				."gender='".$f['contactGender']."', "
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
		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	/**
	*  update note table
	*
	*	@param $f			(array)			array contain note fields.
	*	@param $type		(string)		update or append
	*	@return $res		(object) 		object
	*/

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

		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	/**
	*  delete a record form a table
	*
	*	@param  $id			(int)		identity of the record
	*	@param  $table		(string)	table name
	*	@return $res		(object)	object
	*/
	
	function deleteRecord($id,$table){
		global $db;
		
		//backup all datas

		//delete all note
		$sql = "DELETE FROM $table WHERE id = $id";
		astercrm::events($sql);
		$res =& $db->query($sql);

		return $res;
	}

	/**
	*  select a record form a table
	*
	*	@param  $id			(int)		identity of the record
	*	@param  $table		(string)	table name
	*	@return $res		(object)	object
	*/

	function &getRecord($id,$table){
		global $db;
		
		$sql = "SELECT * FROM $table WHERE id = $id";
		astercrm::events($sql);
		$row =& $db->getRow($sql);
		return $row;
	}

	/**
	*  update a field in a table 
	*
	*	@param  $table		(string)	table name
	*	@param  $field		(string)	field need to be updated
	*	@param  $value		(string)	value want to update to
	*	@param  $id			(int)		identity of the record
	*	@return $res		(object)	object
	*/

	function updateField($table,$field,$value,$id){

		global $db;

		$sql = "UPDATE $table SET $field='$value' WHERE id='$id'";
		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
		
	}


	/**
	*  insert a record to asterisk event log file
	*
	*	@param  $event			(string)	the string need to be appended to the log file
	*	@return 
	*/

	function events($event = null){
		if(LOG_ENABLED){
			$now = date("Y-M-d H:i:s");
   		
			$fd = fopen (FILE_LOG,'a');
			$log = $now." ".$_SERVER["REMOTE_ADDR"] ." - $event \n";
			fwrite($fd,$log);
			fclose($fd);
		}
	}

	/**
	*	check if there's a record in a table
	*
	*	@param  $tblName		(string)	table name
	*	@param  $fldName		(string)	field
	*	@param  $myValue		(string)	value
	*	@param  $type			(string)	the value is string(use ' in sql command) or not
	*	@param  $fldName1		(string)	
	*	@param  $myValue1		(string)	
	*	@param  $type1			(string)	
	*	@return $res			(int)		return identity of the record if exsits 
	*/

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

		
		astercrm::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}

	/**
	*	generate a html table contains note list
	*
	*	@param  $id				(int)		identity
	*	@param  $type			(string)	customerid or contactid
	*	@return $html			(string)	HTML include the notes of the customer/contact
	*/

	function showNoteList($id,$type = 'customer'){
		$noteList =& astercrm::getNoteListByID($id,$type);
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
	*	get customer detail from table
	*
	*	@param  $id				(int)		identity
	*	@param  $type			(string)	customerid or noteid
	*	@return $row			(array)		customer data array
	*/

	function &getCustomerByID($id,$type="customer"){
		global $db;
		if ($type == 'customer')
			return astercrm::getRecord($id,'customer');//$sql = "SELECT * FROM customer WHERE id = $id";
		elseif ($type == 'contact')
			$sql = "SELECT * FROM customer RIGHT JOIN (SELECT customerid FROM contact WHERE id = $id ) g ON customer.id = g.customerid";
		else
			$sql = "SELECT * FROM customer RIGHT JOIN (SELECT customerid FROM note WHERE id = $id ) g ON customer.id = g.customerid";
		
		astercrm::events($sql);
		$row =& $db->getRow($sql);
		return $row;
	}

	function &getDialByID($id,$type="diallist"){
		global $db;
		if ($type == 'diallist')
			return astercrm::getRecord($id,'diallist');//$sql = "SELECT * FROM customer WHERE id = $id";
		elseif ($type == 'contact')
			$sql = "SELECT * FROM diallist RIGHT JOIN (SELECT customerid FROM contact WHERE id = $id ) g ON customer.id = g.customerid";
		else
			$sql = "SELECT * FROM diallist RIGHT JOIN (SELECT customerid FROM note WHERE id = $id ) g ON customer.id = g.customerid";
		
		astercrm::events($sql);
		$row =& $db->getRow($sql);
		return $row;
	}

	function &getContactByID($id,$type="contact"){
		global $db;
		
		if ($type == 'contact')
			$sql = "SELECT * FROM contact WHERE id = $id";
		elseif ($type == 'note')
			$sql = "SELECT * FROM contact RIGHT JOIN (SELECT contactid FROM note WHERE id = $id ) g ON contact.id = g.contactid";

		astercrm::events($sql);
		$row =& $db->getRow($sql);
		return $row;
	}


	/**
	*	get contact list from table
	*
	*	@param  $id				(int)		customerid
	*	@return $res			(object)
	*/

	function &getContactListByID($customerid){
		global $db;
		$sql = "SELECT id,contact FROM contact WHERE customerid=$customerid";
		
		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	/**
	*	get note list from table
	*
	*	@param  $id				(int)		identity
	*	@param  $type			(string)	customerid or contactid
	*	@return $res			(object)
	*/

	function &getNoteListByID($id,$type = 'customer'){
		global $db;
		
		if($type == "customer")
			$sql = "SELECT * FROM note WHERE customerid = '$id' ORDER BY cretime DESC";
		else
			$sql = "SELECT * FROM note WHERE contactid = '$id' ORDER BY cretime DESC";

		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function surveyAdd($customerid,$contactid){
		global $locate;
		$html .= '
				<form method="post" name="formSurvey" id="formSurvey">
				';
		$html .= astercrm::generateSurvey();
		$html .= '<div align="right">
					<input type="button" value="'.$locate->Translate("continue").'" name="btnAddSurvey" id="btnAddSurvey" onclick="xajax_saveSurvey(xajax.getFormValues(\'formSurvey\'));return false;">
					<input type="hidden" value="'.$customerid.'" name="customerid" id="customerid">
					<input type="hidden" value="'.$conatctid.'" name="contactid" id="contactid">
					</div>';
		$html .= '
				</form>
				';
//		print $html;
//		exit;
		return $html;
	}

	function noteAdd($customerid,$contactid){
		global $locate;
		$html .= '
				<form method="post" name="f" id="f">
				<table border="1" width="100%" class="adminlist">
					<tr>
						<td nowrap align="left">'.$locate->Translate("note").'</td>
						<td align="left">
							<input type="text" id="note" name="note" size="35">
							<input type="hidden" value="'.$customerid.'" name="customerid" id="customerid">
							<input type="hidden" value="'.$contactid.'" name="contactid" id="contactid">
						</td>
					</tr>
					<tr>
						<td nowrap align="left">'.$locate->Translate("priority").'</td>
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
						<td nowrap colspan=2 align=right><input type="button" id="btnAddNote" name="btnAddNote" value="'.$locate->Translate("continue").'" onclick="xajax_saveNote(xajax.getFormValues(\'f\'));return false;"></td>
					</tr>
				';
			
		$html .='
				</table>
				</form>
				';
		return $html;
	}

	/**
	*  Imprime la forma para agregar un nuevo registro sobre el DIV identificado por "formDiv".
	*
	*	@param callerid
	*	@param customerid
	*	@param contactid
	*	@return $html	(string) Devuelve una cadena de caracteres que contiene la forma para insertar 
	*							un nuevo registro.
	*/

	function formAdd($callerid = null,$customerid = null, $contactid = null){
	global $locate;
	$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			
			<table border="1" width="100%" class="adminlist">
			<tr>
				<td nowrap align="left" colspan="2">'.$locate->Translate("add_record").' <a href=? onclick="dial(\''.$callerid.'\');return false;">'. $callerid .'</a><input type="hidden" value="'.$callerid.'" id="iptcallerid" name="iptcallerid"> </td>
			</tr>';
	
	if ($customerid == null || $customerid ==0){
		$customerid = 0;
		$html .= '
				<tr>
					<td nowrap align="left">'.$locate->Translate("customer_name").'</td>
					<td align="left"><input type="text" id="customer" name="customer" value="" onkeyup="ajax_showOptions(this,\'getCustomersByLetters\',event)" size="50" maxlength="50" autocomplete="off"><input type="button" value="'.$locate->Translate("confirm").'" id="btnConfirmCustomer" name="btnConfirmCustomer" onclick="btnConfirmCustomerOnClick();"><input type="hidden" id="customerid" name="customerid" value="0">
					<input type="hidden" id="customerDetial" name="customerDetial" value="OFF">
					[<a href=? onclick="
						if (xajax.$(\'customerDetial\').value == \'OFF\'){
							xajax.$(\'websiteTR\').style.display = \'\';
							xajax.$(\'stateTR\').style.display = \'\';
							xajax.$(\'cityTR\').style.display = \'\';
							xajax.$(\'addressTR\').style.display = \'\';
							xajax.$(\'zipcodeTR\').style.display = \'\';
							xajax.$(\'customerContactTR\').style.display = \'\';
							xajax.$(\'customerPhoneTR\').style.display = \'\';
							xajax.$(\'categoryTR\').style.display = \'\';
							xajax.$(\'bankNameTR\').style.display = \'\';
							xajax.$(\'bankAccountTR\').style.display = \'\';
							xajax.$(\'customerDetial\').value = \'ON\';
						}else{
							xajax.$(\'websiteTR\').style.display = \'none\';
							xajax.$(\'stateTR\').style.display = \'none\';
							xajax.$(\'cityTR\').style.display = \'none\';
							xajax.$(\'addressTR\').style.display = \'none\';
							xajax.$(\'zipcodeTR\').style.display = \'none\';
							xajax.$(\'customerContactTR\').style.display = \'none\';
							xajax.$(\'customerPhoneTR\').style.display = \'none\';
							xajax.$(\'categoryTR\').style.display = \'none\';
							xajax.$(\'bankNameTR\').style.display = \'none\';
							xajax.$(\'bankAccountTR\').style.display = \'none\';
							xajax.$(\'customerDetial\').value = \'OFF\';
						};
						return false;">
						'.$locate->Translate("detail").'
					</a>]
					</td>
				</tr>
				<tr id="websiteTR" name="websiteTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("website").'</td>
					<td align="left"><input type="text" id="website" name="website" size="35" maxlength="100" value="http://"><input type="button" value="'.$locate->Translate("browser").'" onclick="openWindow(xajax.$(\'website\').value);return false;"></td>
				</tr>
				<tr id="stateTR" name="stateTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("state").'</td>
					<td align="left"><input type="text" id="state" name="state" size="50" maxlength="50"></td>
				</tr>
				<tr id="cityTR" name="cityTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("city").'</td>
					<td align="left"><input type="text" id="city" name="city" size="50" maxlength="50"></td>
				</tr>
				<tr id="addressTR" name="addressTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("address").'</td>
					<td align="left"><input type="text" id="address" name="address" size="50" maxlength="200"></td>
				</tr>
				<tr id="zipcodeTR" name="zipcodeTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("zipcode").'</td>
					<td align="left"><input type="text" id="zipcode" name="zipcode" size="10" maxlength="10"></td>
				</tr>
				<tr id="customerContactTR" name="customerContactTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("customer_contact").'</td>
					<td align="left">
						<input type="text" id="customerContact" name="customerContact" size="35" maxlength="35">
						<select id="customerContactGender" name="customerContactGender">
							<option value="male">'.$locate->Translate("male").'</option>
							<option value="female">'.$locate->Translate("female").'</option>
							<option value="unknown" selected>'.$locate->Translate("unknown").'</option>
						</select>
					</td>
				</tr>
				<tr id="customerPhoneTR" name="customerPhoneTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("customer_phone").'</td>
					<td align="left"><input type="text" id="customerPhone" name="customerPhone" size="35" maxlength="50"></td>
				</tr>
				<tr id="categoryTR" name="categoryTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("category").'</td>
					<td align="left"><input type="text" id="category" name="category" size="35"></td>
				</tr>
				<tr id="bankNameTR" name="bankNameTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("bank_name").'</td>
					<td align="left"><input type="text" id="bankname" name="bankname" size="50"></td>
				</tr>
				<tr id="bankAccountTR" name="bankAccountTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("bank_account").'</td>
					<td align="left"><input type="text" id="bankaccount" name="bankaccount" size="50"></td>
				</tr>';
	}else{
		$customer =& Customer::getCustomerByID($customerid);
		$html .= '
				<tr>
					<td nowrap align="left"><a href=? onclick="xajax_showCustomer('. $customerid .');return false;">'.$locate->Translate("customer_name").'</a></td>
					<td align="left"><input type="text" id="customer" name="customer" value="'. $customer['customer'].'" onkeyup="ajax_showOptions(this,\'getCustomersByLetters\',event)" size="50" maxlength="50" autocomplete="off" readOnly><input type="button" value="'.$locate->Translate("cancel").'" id="btnConfirmCustomer" name="btnConfirmCustomer" onclick="btnConfirmCustomerOnClick();"><input type="hidden" id="customerid" name="customerid" value="'. $customerid .'"></td>
				</tr>
				';
	}

	if ($contactid == null){
			$html .='
				<tr>
					<td nowrap align="left">'.$locate->Translate("contact").'</td>
					<td align="left"><input type="text" id="contact" name="contact" value="" onkeyup="ajax_showOptions(this,\'customerid='.$customerid.'&getContactsByLetters\',event)" size="35" maxlength="50" autocomplete="off"><input id="btnConfirmContact" name="btnConfirmContact" type="button" onclick="btnConfirmContactOnClick();return false;" value="'.$locate->Translate("confirm").'"><input type="hidden" id="contactid" name="contactid" value="">
					<input type="hidden" id="contactDetial" name="contactDetial" value="OFF">
					[<a href=? onclick="
						if (xajax.$(\'contactDetial\').value == \'OFF\'){
							xajax.$(\'genderTR\').style.display = \'\';
							xajax.$(\'positionTR\').style.display = \'\';
							xajax.$(\'phoneTR\').style.display = \'\';
							xajax.$(\'phone1TR\').style.display = \'\';
							xajax.$(\'phone2TR\').style.display = \'\';
							xajax.$(\'mobileTR\').style.display = \'\';
							xajax.$(\'faxTR\').style.display = \'\';
							xajax.$(\'emailTR\').style.display = \'\';
							xajax.$(\'contactDetial\').value = \'ON\';
						}else{
							xajax.$(\'genderTR\').style.display = \'none\';
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
						'.$locate->Translate("detail").'
					</a>]
					</td>
				</tr>
				<tr name="genderTR" id="genderTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("gender").'</td>
					<td align="left">
						<select id="contactGender" name="contactGender">
							<option value="male">'.$locate->Translate("male").'</option>
							<option value="female">'.$locate->Translate("female").'</option>
							<option value="unknown" selected>'.$locate->Translate("unknown").'</option>
						</select>
					</td>
				</tr>
				<tr name="positionTR" id="positionTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("position").'</td>
					<td align="left"><input type="text" id="position" name="position" size="35"></td>
				</tr>
				<tr name="phoneTR" id="phoneTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("phone").'</td>
					<td align="left"><input type="text" id="phone" name="phone" size="35" value="'. $callerid .'">-<input type="text" id="ext" name="ext" size="6" maxlength="6" value=""></td>
				</tr>
				<tr name="phone1TR" id="phone1TR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("phone1").'</td>
					<td align="left"><input type="text" id="phone1" name="phone1" size="35" value="">-<input type="text" id="ext1" name="ext1" size="6" maxlength="6" value=""></td>
				</tr>
				<tr name="phone2TR" id="phone2TR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("phone2").'</td>
					<td align="left"><input type="text" id="phone2" name="phone2" size="35" value="">-<input type="text" id="ext2" name="ext2" size="6" maxlength="6" value=""></td>
				</tr>
				<tr name="mobileTR" id="mobileTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("mobile").'</td>
					<td align="left"><input type="text" id="mobile" name="mobile" size="35"></td>
				</tr>
				<tr name="faxTR" id="faxTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("fax").'</td>
					<td align="left"><input type="text" id="fax" name="fax" size="35"></td>
				</tr>
				<tr name="emailTR" id="emailTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("email").'</td>
					<td align="left"><input type="text" id="email" name="email" size="35"></td>
				</tr>					
				';
	}else{
		$contact =& Customer::getContactByID($contactid);

			$html .='
				<tr>
					<td nowrap align="left"><a href=? onclick="xajax_showContact('. $contactid .');return false;">'.$locate->Translate("contact").'</a></td>
					<td align="left"><input type="text" id="contact" name="contact" value="'. $contact['contact'].'" onkeyup="ajax_showOptions(this,\'getContactsByLetters\',event)" size="50" maxlength="50" autocomplete="off" readOnly><input type="button" value="'.$locate->Translate("cancel").'" id="btnConfirmContact" name="btnConfirmContact" onclick="btnConfirmContactOnClick();"><input type="hidden" id="contactid" name="contactid" value="'. $contactid .'"></td>
				</tr>
				';
	}

	//add survey html
	$html .= '<tr><td colspan="2">';
	//print astercrm::generateSurvey();
	//exit;
	$html .= astercrm::generateSurvey();

	$html .= '</tr></td>';

	//add note html
	$html .='
			<tr>
				<td nowrap align="left">'.$locate->Translate("note").'</td>
				<td align="left"><input type="text" id="note" name="note" size="35"></td>
			</tr>
			<tr>
				<td nowrap align="left">'.$locate->Translate("priority").'</td>
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
				<td colspan="2" align="center"><button id="submitButton" onClick=\'xajax_save(xajax.getFormValues("f"));return false;\'>'.$locate->Translate("continue").'</button></td>
			</tr>';
			
		$html .='
			</table>
			</form>
			'.$locate->Translate("ob_fields").'
			';
		
		return $html;
	}


	function getOptions($surveyid){

		global $db;
		
		$sql= "SELECT * FROM surveyoptions "
				." WHERE "
				."surveyid = " . $surveyid 
				." ORDER BY cretime ASC";

		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function insertNewSurveyResult($surveyid,$surveyoption,$surveynote,$customerID,$contactID){
		global $db;
		
		$sql= "INSERT INTO surveyresult SET "
				."surveyid='".$surveyid."', "
				."surveyoption='".$surveyoption."', "
				."surveynote='".$surveynote."', "
				."customerid='".$customerID."', "
				."contactid='".$contactID."', "
				."cretime=now(), "
				."creby='".$_SESSION['curuser']['username']."'";
		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function generateSurvey(){
		global $db;

		$sql = "SELECT * FROM survey WHERE enable=1 ORDER BY cretime DESC LIMIT 0,1";
		astercrm::events($sql);
		$res =& $db->getRow($sql);
		if (!$res)
			return '';

		//get survey title and id
		$surveytitle = $res['surveyname'];
		$surveyid = $res['id'];

		$html = "<table width='100%'>";
		$html .= "<tr><td>$surveytitle<input type='hidden' value='$surveyid' name='surveyid' id='surveyid'></td></tr>";
		

		//get survey options
		$options = astercrm::getOptions($surveyid);
		if (!$options)
			return '';
		else {
			while ($options->fetchInto($row)) {
				$html .= "<tr><td><input type='radio' value='".$row['surveyoption']."' id='surveyoption' name='surveyoption'>".$row['surveyoption']."</td></tr>";
			}
			$html .= "<tr><td><input type='text' value='' id='surveynote' name='surveynote' size='50'></td></tr>";
		}

		$html .= "</table>";
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
		global $locate;
		if ($type == 'note'){
			$note =& Customer::getRecord($id,'note');
			for ($i=0;$i<11;$i++){
				$options .= "<option value='$i' ";
				if (trim($note['priority']) == $i)
					$options .= 'selected>';
				else
					$options .= '>';

				$options .= $i."</option>";
			}
		//	print $options;
		//	exit;
			$html = '
					<form method="post" name="f" id="f">
					<input type="hidden" id="noteid"  name="noteid" value="'.$note['id'].'">
					<table border="0" width="100%">
					<tr>
						<td nowrap align="left">'.$locate->Translate("note").'</td>
						<td align="left">'.$note['note']. '</td>
					</tr>
					<tr>
						<td nowrap align="left">'.$locate->Translate("append").'</td>
						<td align="left"><input type="text" value="" name="note" id="note" length="35"></td>
					</tr>
					<tr>
						<td nowrap align="left">'.$locate->Translate("priority").'</td>
						<td align="left">
							<select id="priority" name="priority">'.$options.'</select>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">[<a href=? onclick="xajax_showCustomer(\'' . $note['customerid'] . '\');return false;">'.$locate->Translate("customer").'</a>]&nbsp;&nbsp;&nbsp;&nbsp;[<a href=? onclick="xajax_showContact(\'' . $note['contactid'] . '\');return false;">'.$locate->Translate("contact").'</a>]</td>
					</tr>
					<tr>
						<td colspan="2" align="center"><button id="btnContinue" name="btnContinue"  onClick=\'xajax_update(xajax.getFormValues("f"),"note");return false;\'>'.$locate->Translate("continue").'</button></td>
					</tr>
					';

		}elseif ($type == 'customer'){
			$customer =& Customer::getCustomerByID($id);
			if ($customer['contactgender'] == 'male')
				$customerMaleSelected = 'selected';
			elseif ($customer['contactgender'] == 'female')
				$customerFemaleSelected = 'selected';
			else
				$customerUnknownSelected = 'selected';

			$html = '
					<form method="post" name="frmCustomerEdit" id="frmCustomerEdit">
					<table border="0" width="100%">
					<tr id="customer" name="customer">
						<td nowrap align="left">'.$locate->Translate("customer_name").'</td>
						<td align="left"><input type="text" id="customer" name="customer" size="50" maxlength="100" value="' . $customer['customer'] . '"><input type="hidden" id="customerid"  name="customerid" value="'.$customer['id'].'">
</td>
					</tr>
					<tr id="websiteTR" name="websiteTR">
						<td nowrap align="left">'.$locate->Translate("website").'</td>
						<td align="left"><input type="text" id="website" name="website" size="35" maxlength="100" value="' . $customer['website'] . '"><input type="button" value="'.$locate->Translate("browser").'"  onclick="openWindow(xajax.$(\'website\').value);return false;"></td>
					</tr>
					<tr id="stateTR" name="stateTR">
						<td nowrap align="left">'.$locate->Translate("state").'</td>
						<td align="left"><input type="text" id="state" name="state" size="50" maxlength="50" value="'.$customer['state'].'"></td>
					</tr>
					<tr id="cityTR" name="cityTR">
						<td nowrap align="left">'.$locate->Translate("city").'</td>
						<td align="left"><input type="text" id="city" name="city" size="50" maxlength="50" value="'.$customer['city'].'"></td>
					</tr>
					<tr id="addressTR" name="addressTR">
						<td nowrap align="left">'.$locate->Translate("address").'</td>
						<td align="left"><input type="text" id="address" name="address" size="50" maxlength="200" value="' . $customer['address'] . '"></td>
					</tr>
					<tr id="zipcodeTR" name="zipcodeTR">
						<td nowrap align="left">'.$locate->Translate("zipcode").'</td>
						<td align="left"><input type="text" id="zipcode" name="zipcode" size="10" maxlength="10" value="' . $customer['zipcode'] . '"></td>
					</tr>
					<tr id="customerContactTR" name="customerContactTR">
						<td nowrap align="left">'.$locate->Translate("customer_contact").'</td>
						<td align="left"><input type="text" id="customerContact" name="customerContact" size="35" maxlength="35" value="' . $customer['contact'] . '">

						<select id="customerContactGender" name="customerContactGender">
							<option value="male" '.$customerMaleSelected.'>'.$locate->Translate("male").'</option>
							<option value="female" '.$customerFemaleSelected.'>'.$locate->Translate("female").'</option>
							<option value="unknown" '.$customerUnknownSelected.'>'.$locate->Translate("unknown").'</option>
						</select>
						
						</td>
					</tr>
					<tr id="customerPhoneTR" name="customerPhoneTR">
						<td nowrap align="left">'.$locate->Translate("customer_phone").'</td>
						<td align="left"><input type="text" id="customerPhone" name="customerPhone" size="35" maxlength="50"  value="' . $customer['phone'] . '"></td>
					</tr>
					<tr id="categoryTR" name="categoryTR">
						<td nowrap align="left">'.$locate->Translate("category").'</td>
						<td align="left"><input type="text" id="category" name="category" size="35"  value="' . $customer['category'] . '"></td>
					</tr>
					<tr id="bankNameTR" name="bankNameTR">
						<td nowrap align="left">'.$locate->Translate("bank_name").'</td>
						<td align="left"><input type="text" id="bankname" name="bankname" size="50"  value="' . $customer['bankname'] . '"></td>
					</tr>
					<tr id="bankAccountTR" name="bankAccountTR">
						<td nowrap align="left">'.$locate->Translate("bank_account").'</td>
						<td align="left"><input type="text" id="bankaccount" name="bankaccount" size="50"  value="' . $customer['bankaccount'] . '"></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><button  id="btnContinue" name="btnContinue"  onClick=\'xajax_update(xajax.getFormValues("frmCustomerEdit"),"customer");return false;\'>'.$locate->Translate("continue").'</button></td>
					</tr>
					';
		}else {
			$contact =& Customer::getContactByID($id);
			if ($contact['gender'] == 'male')
				$maleSelected = 'selected';
			elseif ($contact['gender'] == 'female')
				$femaleSelected = 'selected';
			else
				$unknownSelected = 'selected';

			$html = '
					<form method="post" name="formEdit" id="formEdit">
					<table border="0" width="100%">
					<tr>
						<td nowrap align="left">'.$locate->Translate("contact").'</td>
						<td align="left"><input type="text" id="contact" name="contact" size="35"  value="'.$contact['contact'].'"><input type="hidden" id="contactid"  name="contactid" value="'.$contact['id'].'">
</td>
					</tr>
					<tr name="genderTR" id="genderTR">
						<td nowrap align="left">'.$locate->Translate("gender").'</td>
						<td align="left">
							<select id="contactGender" name="contactGender">
								<option value="male" '.$maleSelected.'>'.$locate->Translate("male").'</option>
								<option value="female" '.$femaleSelected.'>'.$locate->Translate("female").'</option>
								<option value="unknown" '.$unknownSelected.'>'.$locate->Translate("unknown").'</option>
							</select>
						</td>
					</tr>
					<tr name="positionTR" id="positionTR">
						<td nowrap align="left">'.$locate->Translate("position").'</td>
						<td align="left"><input type="text" id="position" name="position" size="35"  value="'.$contact['position'].'"></td>
					</tr>
					<tr name="phoneTR" id="phoneTR">
						<td nowrap align="left">'.$locate->Translate("phone").'</td>
						<td align="left"><input type="text" id="phone" name="phone" size="35"  value="'.$contact['phone'].'">-<input type="text" id="ext" name="ext" size="6" maxlength="6"  value="'.$contact['ext'].'"></td>
					</tr>
					<tr name="phone1TR" id="phone1TR">
						<td nowrap align="left">'.$locate->Translate("phone1").'</td>
						<td align="left"><input type="text" id="phone1" name="phone1" size="35"  value="'.$contact['phone1'].'">-<input type="text" id="ext1" name="ext1" size="6" maxlength="6"  value="'.$contact['ext1'].'"></td>
					</tr>
					<tr name="phone2TR" id="phone2TR">
						<td nowrap align="left">'.$locate->Translate("phone2").'</td>
						<td align="left"><input type="text" id="phone2" name="phone2" size="35"  value="'.$contact['phone2'].'">-<input type="text" id="ext2" name="ext2" size="6" maxlength="6"  value="'.$contact['ext2'].'"></td>
					</tr>
					<tr name="mobileTR" id="mobileTR">
						<td nowrap align="left">'.$locate->Translate("mobile").'</td>
						<td align="left"><input type="text" id="mobile" name="mobile" size="35" value="'.$contact['mobile'].'"></td>
					</tr>
					<tr name="faxTR" id="faxTR">
						<td nowrap align="left">'.$locate->Translate("fax").'</td>
						<td align="left"><input type="text" id="fax" name="fax" size="35" value="'.$contact['fax'].'"></td>
					</tr>
					<tr name="emailTR" id="emailTR">
						<td nowrap align="left">'.$locate->Translate("email").'</td>
						<td align="left"><input type="text" id="email" name="email" size="35" value="'.$contact['email'].'"></td>
					</tr>					
					<tr>
						<td colspan="2" align="center"><button id="btnContinue" name="btnContinue"  onClick=\'xajax_update(xajax.getFormValues("formEdit"),"contact");return false;\'>'.$locate->Translate("continue").'</button></td>
					</tr>
					';
		}

		$html .= '
				</table>
				</form>
				'.$locate->Translate("ob_fields").'
				';

		return $html;
	}
	
	function formEditDial($id , $type){
		global $locate;
		if ($type == 'note'){
			$note =& Diallist::getRecordByID($id,'note');
			for ($i=0;$i<11;$i++){
				$options .= "<option value='$i' ";
				if (trim($note['priority']) == $i)
					$options .= 'selected>';
				else
					$options .= '>';

				$options .= $i."</option>";
			}
		//	print $options;
		//	exit;
			$html = '
					<form method="post" name="f" id="f">
					<input type="hidden" id="noteid"  name="noteid" value="'.$note['id'].'">
					<table border="0" width="100%">
					<tr>
						<td nowrap align="left">'.$locate->Translate("note").'</td>
						<td align="left">'.$note['note']. '</td>
					</tr>
					<tr>
						<td nowrap align="left">'.$locate->Translate("append").'</td>
						<td align="left"><input type="text" value="" name="note" id="note" length="35"></td>
					</tr>
					<tr>
						<td nowrap align="left">'.$locate->Translate("priority").'</td>
						<td align="left">
							<select id="priority" name="priority">'.$options.'</select>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">[<a href=? onclick="xajax_showCustomer(\'' . $note['customerid'] . '\');return false;">'.$locate->Translate("customer").'</a>]&nbsp;&nbsp;&nbsp;&nbsp;[<a href=? onclick="xajax_showContact(\'' . $note['contactid'] . '\');return false;">'.$locate->Translate("contact").'</a>]</td>
					</tr>
					<tr>
						<td colspan="2" align="center"><button id="btnContinue" name="btnContinue"  onClick=\'xajax_update(xajax.getFormValues("f"),"note");return false;\'>'.$locate->Translate("continue").'</button></td>
					</tr>
					';

		}elseif ($type == 'customer'){
			$customer =& Diallist::getCustomerByID($id);
			if ($customer['contactgender'] == 'male')
				$customerMaleSelected = 'selected';
			elseif ($customer['contactgender'] == 'female')
				$customerFemaleSelected = 'selected';
			else
				$customerUnknownSelected = 'selected';

			$html = '
					<form method="post" name="frmCustomerEdit" id="frmCustomerEdit">
					<table border="0" width="100%">
					<tr id="customer" name="customer">
						<td nowrap align="left">'.$locate->Translate("customer_name").'</td>
						<td align="left"><input type="text" id="customer" name="customer" size="50" maxlength="100" value="' . $customer['customer'] . '"><input type="hidden" id="customerid"  name="customerid" value="'.$customer['id'].'">
</td>
					</tr>
					<tr id="websiteTR" name="websiteTR">
						<td nowrap align="left">'.$locate->Translate("website").'</td>
						<td align="left"><input type="text" id="website" name="website" size="35" maxlength="100" value="' . $customer['website'] . '"><input type="button" value="'.$locate->Translate("browser").'"  onclick="openWindow(xajax.$(\'website\').value);return false;"></td>
					</tr>
					<tr id="stateTR" name="stateTR">
						<td nowrap align="left">'.$locate->Translate("state").'</td>
						<td align="left"><input type="text" id="state" name="state" size="50" maxlength="50" value="'.$customer['state'].'"></td>
					</tr>
					<tr id="cityTR" name="cityTR">
						<td nowrap align="left">'.$locate->Translate("city").'</td>
						<td align="left"><input type="text" id="city" name="city" size="50" maxlength="50" value="'.$customer['city'].'"></td>
					</tr>
					<tr id="addressTR" name="addressTR">
						<td nowrap align="left">'.$locate->Translate("address").'</td>
						<td align="left"><input type="text" id="address" name="address" size="50" maxlength="200" value="' . $customer['address'] . '"></td>
					</tr>
					<tr id="zipcodeTR" name="zipcodeTR">
						<td nowrap align="left">'.$locate->Translate("zipcode").'</td>
						<td align="left"><input type="text" id="zipcode" name="zipcode" size="10" maxlength="10" value="' . $customer['zipcode'] . '"></td>
					</tr>
					<tr id="customerContactTR" name="customerContactTR">
						<td nowrap align="left">'.$locate->Translate("customer_contact").'</td>
						<td align="left"><input type="text" id="customerContact" name="customerContact" size="35" maxlength="35" value="' . $customer['contact'] . '">

						<select id="customerContactGender" name="customerContactGender">
							<option value="male" '.$customerMaleSelected.'>'.$locate->Translate("male").'</option>
							<option value="female" '.$customerFemaleSelected.'>'.$locate->Translate("female").'</option>
							<option value="unknown" '.$customerUnknownSelected.'>'.$locate->Translate("unknown").'</option>
						</select>
						
						</td>
					</tr>
					<tr id="customerPhoneTR" name="customerPhoneTR">
						<td nowrap align="left">'.$locate->Translate("customer_phone").'</td>
						<td align="left"><input type="text" id="customerPhone" name="customerPhone" size="35" maxlength="50"  value="' . $customer['phone'] . '"></td>
					</tr>
					<tr id="categoryTR" name="categoryTR">
						<td nowrap align="left">'.$locate->Translate("category").'</td>
						<td align="left"><input type="text" id="category" name="category" size="35"  value="' . $customer['category'] . '"></td>
					</tr>
					<tr id="bankNameTR" name="bankNameTR">
						<td nowrap align="left">'.$locate->Translate("bank_name").'</td>
						<td align="left"><input type="text" id="bankname" name="bankname" size="50"  value="' . $customer['bankname'] . '"></td>
					</tr>
					<tr id="bankAccountTR" name="bankAccountTR">
						<td nowrap align="left">'.$locate->Translate("bank_account").'</td>
						<td align="left"><input type="text" id="bankaccount" name="bankaccount" size="50"  value="' . $customer['bankaccount'] . '"></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><button  id="btnContinue" name="btnContinue"  onClick=\'xajax_update(xajax.getFormValues("frmCustomerEdit"),"customer");return false;\'>'.$locate->Translate("continue").'</button></td>
					</tr>
					';
		}else {
			$contact =& Diallist::getContactByID($id);
			if ($contact['gender'] == 'male')
				$maleSelected = 'selected';
			elseif ($contact['gender'] == 'female')
				$femaleSelected = 'selected';
			else
				$unknownSelected = 'selected';

			$html = '
					<form method="post" name="formEdit" id="formEdit">
					<table border="0" width="100%">
					<tr>
						<td nowrap align="left">'.$locate->Translate("contact").'</td>
						<td align="left"><input type="text" id="contact" name="contact" size="35"  value="'.$contact['contact'].'"><input type="hidden" id="contactid"  name="contactid" value="'.$contact['id'].'">
</td>
					</tr>
					<tr name="genderTR" id="genderTR">
						<td nowrap align="left">'.$locate->Translate("gender").'</td>
						<td align="left">
							<select id="contactGender" name="contactGender">
								<option value="male" '.$maleSelected.'>'.$locate->Translate("male").'</option>
								<option value="female" '.$femaleSelected.'>'.$locate->Translate("female").'</option>
								<option value="unknown" '.$unknownSelected.'>'.$locate->Translate("unknown").'</option>
							</select>
						</td>
					</tr>
					<tr name="positionTR" id="positionTR">
						<td nowrap align="left">'.$locate->Translate("position").'</td>
						<td align="left"><input type="text" id="position" name="position" size="35"  value="'.$contact['position'].'"></td>
					</tr>
					<tr name="phoneTR" id="phoneTR">
						<td nowrap align="left">'.$locate->Translate("phone").'</td>
						<td align="left"><input type="text" id="phone" name="phone" size="35"  value="'.$contact['phone'].'">-<input type="text" id="ext" name="ext" size="6" maxlength="6"  value="'.$contact['ext'].'"></td>
					</tr>
					<tr name="phone1TR" id="phone1TR">
						<td nowrap align="left">'.$locate->Translate("phone1").'</td>
						<td align="left"><input type="text" id="phone1" name="phone1" size="35"  value="'.$contact['phone1'].'">-<input type="text" id="ext1" name="ext1" size="6" maxlength="6"  value="'.$contact['ext1'].'"></td>
					</tr>
					<tr name="phone2TR" id="phone2TR">
						<td nowrap align="left">'.$locate->Translate("phone2").'</td>
						<td align="left"><input type="text" id="phone2" name="phone2" size="35"  value="'.$contact['phone2'].'">-<input type="text" id="ext2" name="ext2" size="6" maxlength="6"  value="'.$contact['ext2'].'"></td>
					</tr>
					<tr name="mobileTR" id="mobileTR">
						<td nowrap align="left">'.$locate->Translate("mobile").'</td>
						<td align="left"><input type="text" id="mobile" name="mobile" size="35" value="'.$contact['mobile'].'"></td>
					</tr>
					<tr name="faxTR" id="faxTR">
						<td nowrap align="left">'.$locate->Translate("fax").'</td>
						<td align="left"><input type="text" id="fax" name="fax" size="35" value="'.$contact['fax'].'"></td>
					</tr>
					<tr name="emailTR" id="emailTR">
						<td nowrap align="left">'.$locate->Translate("email").'</td>
						<td align="left"><input type="text" id="email" name="email" size="35" value="'.$contact['email'].'"></td>
					</tr>					
					<tr>
						<td colspan="2" align="center"><button id="btnContinue" name="btnContinue"  onClick=\'xajax_update(xajax.getFormValues("formEdit"),"contact");return false;\'>'.$locate->Translate("continue").'</button></td>
					</tr>
					';
		}

		$html .= '
				</table>
				</form>
				'.$locate->Translate("ob_fields").'
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
    	global $locate;
		$customer =& Customer::getCustomerByID($id,$type);
		$contactList =& Customer::getContactListByID($customer['id']);

		$html = '
				<table border="0" width="100%">
				<tr>
					<td nowrap align="left" width="80">'.$locate->Translate("customer_name").'&nbsp;[<a href=? onclick="xajax_showNote(\''.$customer['id'].'\',\'customer\');return false;">'.$locate->Translate("note").'</a>]</td>
					<td align="left">'.$customer['customer'].'&nbsp;[<a href=? onclick="xajax_edit(\''.$customer['id'].'\',\'\',\'customer\');return false;">'.$locate->Translate("edit").'</a>]</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("state").'</td>
					<td align="left">'.$customer['state'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("city").'</td>
					<td align="left">'.$customer['city'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("address").'</td>
					<td align="left">'.$customer['address'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("zipcode").'</td>
					<td align="left">'.$customer['zipcode'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("website").'</td>
					<td align="left"><a href="'.$customer['website'].'" target="_blank">'.$customer['website'].'</a></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("customer_contact").'</td>
					<td align="left">'.$customer['contact'].'&nbsp;&nbsp;('.$locate->Translate($customer['contactgender']).')</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("customer_phone").'</td>
					<td align="left">'.$customer['phone'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("category").'</td>
					<td align="left">'.$customer['category'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("bank_name").'</td>
					<td align="left">'.$customer['bankname'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("bank_account").'</td>
					<td align="left">'.$customer['bankaccount'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("create_time").'</td>
					<td align="left">'.$customer['cretime'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("create_by").'</td>
					<td align="left">'.$customer['creby'].'</td>
				</tr>
				<tr>
					<td colspan=2>
						<table width="100%">
							<tr>
							<td>
					<a href=? onclick="if (xajax.$(\'allContact\').value==\'off\'){xajax.$(\'contactList\').style.display=\'block\';xajax.$(\'allContact\').value=\'on\'}else{xajax.$(\'contactList\').style.display=\'none\';xajax.$(\'allContact\').value=\'off\'} return false;">'.$locate->Translate("display_all").'</a>
							</td>
							<td>
							<a href="?" onclick="xajax_noteAdd(\''.$customer['id'].'\',0);return false;">'.$locate->Translate("add_note").'</a>
							</td>
							<td>
							<a href="?" onclick="xajax_surveyAdd(\''.$customer['id'].'\',0);return false;">'.$locate->Translate("add_survey").'</a>
							</td>					<input type="hidden" id="allContact" name="allContact" value="off">
							</tr>
						</table>
					</td>
				</tr>
				</table>
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
	
	function showDialRecord($id,$type="diallist"){
    	global $locate;
		$customer =& Diallist::getDialByID($id,$type);
		$contactList =& Diallist::getContactListByID($customer['id']);

		$html = '
				<table border="0" width="100%">
				<tr>
					<td nowrap align="left">dialnumber</td>
					<td align="left">'.$customer['dialnumber'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">assign</td>
					<td align="left">'.$customer['assign'].'</td>
				</tr>
				</table>
				<table border="0" width="100%" id="contactList" name="contactList" style="display:none">
					';

				/*while	($contactList->fetchInto($row)){
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
				}*/

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
    	global $locate;
		$contact =& Customer::getContactByID($id,$type);
		if ($contact['id'] == '' )
			return '';
		$html = '
				<table border="0" width="100%">
				<tr>
					<td nowrap align="left" width="80">'.$locate->Translate("contact").'&nbsp;[<a href=? onclick="xajax_showNote(\''.$contact['id'].'\',\'contact\');return false;">'.$locate->Translate("note").'</a>]</td>
					<td align="left">'.$contact['contact'].'&nbsp;&nbsp;&nbsp;&nbsp;<span align="right">[<a href=? onclick="contactCopy(\''.$contact['id'].'\');;return false;">'.$locate->Translate("copy").'</a>]</span>&nbsp;&nbsp;[<a href=? onclick="xajax_edit(\''.$contact['id'].'\',\'\',\'contact\');return false;">'.$locate->Translate("edit").'</a>]</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("gender").'</td>
					<td align="left">'.$locate->Translate($contact['gender']).'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("position").'</td>
					<td align="left">'.$contact['position'].'</td>
				</tr>';

		if ($contact['ext'] == '')
			$html .='
					<tr>
						<td nowrap align="left">'.$locate->Translate("phone").'</td>
						<td align="left"><a href=? onclick="xajax_dial(\''.$contact['phone'].'\');return false;">'.$contact['phone'].'</a></td>
					</tr>';
		else
			$html .='
					<tr>
						<td nowrap align="left">'.$locate->Translate("phone").'</td>
						<td align="left"><a href=? onclick="xajax_dial(\''.$contact['phone'].'\');return false;">'.$contact['phone'].'</a> ext: '.$contact['ext'].'</td>
					</tr>';

		if ($contact['phone1'] != '' || $contact['ext1'] != '')
			if ($contact['ext1'] == '')
				$html .='
						<tr>
							<td nowrap align="left">'.$locate->Translate("phone1").'</td>
							<td align="left"><a href="?" onclick="xajax_dial(\''.$contact['phone1'].'\');return false;">'.$contact['phone1'].'</a></td>
						</tr>';
			else
				$html .='
						<tr>
							<td nowrap align="left">'.$locate->Translate("phone1").'</td>
							<td align="left"><a href="?" onclick="xajax_dial(\''.$contact['phone1'].'\');return false;">'.$contact['phone1'].'</a> ext: '.$contact['ext1'].'</td>
						</tr>';
		
		if ($contact['phone2'] != '' || $contact['ext2'] != '')
			if ($contact['ext2'] == '')
				$html .='
						<tr>
							<td nowrap align="left">'.$locate->Translate("phone2").'</td>
							<td align="left"><a href="?" onclick="xajax_dial(\''.$contact['phone2'].'\');return false;">'.$contact['phone2'].'</a></td>
						</tr>';
			else
				$html .='
						<tr>
							<td nowrap align="left">'.$locate->Translate("phone2").'</td>
							<td align="left"><a href="?" onclick="xajax_dial(\''.$contact['phone2'].'\');return false;">'.$contact['phone2'].'</a> ext: '.$contact['ext2'].'</td>
						</tr>';

		$html .='
				<tr>
					<td nowrap align="left">'.$locate->Translate("mobile").'</td>
					<td align="left"><a href="?" onclick="xajax_dial(\''.$contact['mobile'].'\');return false;">'.$contact['mobile'].'</a></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("fax").'</td>
					<td align="left">'.$contact['fax'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("email").'</td>
					<td align="left">'.$contact['email'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("create_time").'</td>
					<td align="left">'.$contact['cretime'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("create_by").'</td>
					<td align="left">'.$contact['creby'].'</td>
				</tr>
				</table>';

		return $html;
	}

	/**
	*  export datas to csv format
	*
	*	@param $type		(string)		data to be exported
	*	@return $txtstr		(string) 		csv format datas
	*/

	function exportCSV($type = 'customer'){
		global $db;

		if ($type == 'customer')
			$sql = 'SELECT * FROM customer';
		elseif ($type == 'contact')
			$sql = 'SELECT contact.*,customer.customer FROM contact LEFT JOIN customer ON customer.id = contact.customerid';
		else
			$sql = 'SELECT contact.contact,customer.customer,note.* FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid';

		//astercrm::events($sql);
		$res =& $db->query($sql);
		while ($res->fetchInto($row)) {
			foreach ($row as $val){
				$val .= ',';
				if ($val != mb_convert_encoding($val,"UTF-8","UTF-8"))
						$val=mb_convert_encoding($val,"UTF-8","GB2312");
				
				$txtstr .= $val;
			}
			$txtstr .= "\n";
		}
		return $txtstr;
	}
}
?>
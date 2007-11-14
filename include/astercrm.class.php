<?
/*******************************************************************************
* astercrm.php
* astercrm公用类
* astercrm class

* Public Functions List

			insertNewCustomer		向customer表插入数据
			insertNewContact		向contact表插入数据
			insertNewNote			向note表插入数据
			insertNewSurveyResult	向surveyresult表插入数据
			insertNewAccount
			insertNewDiallist

			updateCustomerRecord	更新customer表数据
			updateContactRecord		更新contact表数据
			updateNoteRecord		更新note表数据
			updateAccountRecord

			deleteRecord			从表中删除数据(以id作为标识)
			getRecord				从表中读取数据(以id作为标识)
			updateField				更新表中的数据(以id作为标识)
			events					日志记录
			checkValues				根据条件从数据库中检索是否有符合条件的记录
			showNoteList			生成note列表的HTML文件
			getCustomerByID			根据customerid获取customer记录信息或者根据noteid获取与之相关的customer信息
			getContactByID			根据contactid获取contact记录信息或者根据noteid获取与之相关的contact信息
			getContactListByID		根据customerid获取与之邦定的contact记录
			getRecordByID			根据id获取记录
			surveyAdd				生成添加survey的HTML语法
			noteAdd					生成添加note的HTML语法
			formAdd					生成添加综合信息(包括customer, contact, survey, note)的HTML语法
			formEdit				生成综合信息编辑的HTML语法, 
									包括编辑customer, contact以及添加note
			getOptions				读取survey的所有option

			showCustomerRecord		生成显示customer信息的HTML语法
			showContactRecord		生成显示contact信息的HTML语法

			exportCSV				生成csv文件内容, 目前支持导出customer, contact
			getCustomerByCallerid	根据callerid查找customer表看是否有匹配的id

			variableFiler			用于转译变量, 自动加\
			新增exportDataToCSV     得到要导出的sql语句的结果集，转换为符合csv格式的文本字符串
			新增getSql              得到多条件搜索的sql语句
			
* Private Functions List
			generateSurvey			生成添加survey的HTML语法
			getNoteListByID			根据customerid或者contactid获取与之邦定的note记录



* Revision 0.0456  2007/11/8 10:11:00  last modified by solo
* Desc: add a new function getTableStructure

* Revision 0.0456  2007/11/7 11:30:00  last modified by solo
* Desc: add a new function variableFiler

* Revision 0.0456  2007/11/7 10:30:00  last modified by solo
* Desc: replace input with textarea in note field

* Revision 0.0456  2007/10/30 13:30:00  last modified by solo
* Desc: modified function insertNewAccount,updateAccountRecord

* Revision 0.045  2007/10/18 13:30:00  last modified by solo
* Desc: add function insertNewDiallist


********************************************************************************/

/** \brief astercrm Class
*

*
* @author	Solo Fu <solo.fu@gmail.com>
* @version	1.0
* @date		13 Auguest 2007
*/


Class astercrm extends PEAR{
	
	function getAllExtension(){
		global $db;
		$query = "select extension from account";
		$res =& $db->query($query);
		return  $res;
	}

	/**
	*	get table structure
	*	
	*	@param	$table		string	table name
	*	@return $structure	array	table structure
	*
	*/
	function getTableStructure($tableName){
		global $db;
		$query = "select * from $tableName LIMIT 0,2";
		$res =& $db->query($query);
		return  $db->tableInfo($res);
	}

	/**
	*  filer variables befor mysql query
	*
	*
	*
	*/

	function variableFiler($var){
		if (is_array($var)){
			$newVar = array();
			foreach ($var as  $key=>$value){
				$value = addslashes($value);
				$newVar[$key] = $value;
			}
		}else{
			$newVar = addslashes($var);
		}
		return $newVar;
	}

	/**
	*  insert a record to customer table
	*
	*	@param $f			(array)		array contain customer fields.
	*	@return $customerid	(object) 	id number for the record just inserted.
	*/
	
	function insertNewCustomer($f){
		global $db;
		$f = astercrm::variableFiler($f);
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
				."bankzip='".$f['bankzip']."', "
				."bankaccount='".$f['bankaccount']."', "
				."bankaccountname='".$f['bankaccountname']."', "
				."fax='".$f['mainFax']."', "
				."mobile='".$f['mainMobile']."', "
				."email='".$f['mainEmail']."', "
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
		$f = astercrm::variableFiler($f);
		
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
		$f = astercrm::variableFiler($f);
		//print_r($f);
		$sql= "INSERT INTO note SET "
				."note='".$f['note']."', "
				."attitude='".$f['attitude']."', "
				."priority=".$f['priority'].", "
				."cretime=now(), "
				."creby='".$_SESSION['curuser']['username']."', "
				."customerid=". $customerid . ", "
				."contactid=". $contactid ;
		//print $sql;
		//exit;
		astercrm::events($sql);

		$res =& $db->query($sql);
		return $res;
	}

	/**
	*  Inserta un nuevo registro en la tabla.
	*
	*	@param $f	(array)		Arreglo que contiene los datos del formulario pasado.
	*	@return $res	(object) 	Devuelve el objeto con la respuesta de la sentencia SQL ejecutada del INSERT.

	*/
	
	function insertNewAccount($f){
		global $db;
		$f = astercrm::variableFiler($f);
		$sql= "INSERT INTO account SET "
				."username='".$f['username']."', "
				."password='".$f['password']."', "
				."extension='".$f['extension']."',"
				."channel='".$f['channel']."',"			// added 2007/10/30 by solo
				."usertype='".$f['usertype']."',"
				."extensions='".$f['extensions']."', "	// added 2007/11/12 by solo
				."accountcode='".$f['accountcode']."'";

		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function insertNewDiallist($f){
		global $db;
		$f = astercrm::variableFiler($f);
		
		$sql= "INSERT INTO diallist SET "
				."dialnumber='".$f['dialnumber']."', "
				."assign='".$f['assign']."'";

		Customer::events($sql);
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
		$f = astercrm::variableFiler($f);
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
				."bankzip='".$f['bankzip']."', "
				."fax='".$f['mainFax']."', "
				."mobile='".$f['mainMobile']."', "
				."email='".$f['mainEmail']."', "
				."bankaccount='".$f['bankaccount']."', "
				."bankaccountname='".$f['bankaccountname']."' "
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
		$f = astercrm::variableFiler($f);
		
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
	*  if $type is update, this function would use new data to replace the old one
	*  or else astercrm would append new data to note field
	*
	*	@param $f			(array)			array contain note fields.
	*	@param $type		(string)		update or append
	*	@return $res		(object) 		object
	*/

	function updateNoteRecord($f,$type="update"){
		global $db;
		$f = astercrm::variableFiler($f);
		
		if ($type == 'update')

			$sql= "UPDATE note SET "
					."note='".$f['note']."', "
					."priority=".$f['priority']." ,"
					."attitude='".$f['attitude']."' "
					."WHERE id='".$f['noteid']."'";
		else
			if (empty($f['note']))
				$sql= "UPDATE note SET "
						."attitude='".$f['attitude']."', "
						."priority=".$f['priority']." "
						."WHERE id='".$f['noteid']."'";
			else
				$sql= "UPDATE note SET "
						."note=CONCAT(note,'<br>',now(),'  ".$f['note']." by " .$_SESSION['curuser']['username']. "'), "
						."attitude='".$f['attitude']."', "
						."priority=".$f['priority']." "
						."WHERE id='".$f['noteid']."'";

		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	/**
	*  Actualiza un registro de la tabla.
	*
	*	@param $f	(array)		Arreglo que contiene los datos del formulario pasado.
	*	@return $res	(object)	Devuelve el objeto con la respuesta de la sentencia SQL ejecutada del UPDATE.
	*/
	
	function updateAccountRecord($f){
		global $db;
		$f = astercrm::variableFiler($f);
		
		$sql= "UPDATE account SET "
				."username='".$f['username']."', "
				."password='".$f['password']."', "
				."extension='".$f['extension']."', "
				."usertype='".$f['usertype']."', "
				."channel='".$f['channel']."', "	// added 2007/10/30 by solo
				."extensions='".$f['extensions']."', "
				."accountcode='".$f['accountcode']."' "	// added 2007/11/12 by solo
				."WHERE id='".$f['id']."'";

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
		$f = astercrm::variableFiler($f);

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
	*	@return $id				(int)		return identity of the record if exsits or else return '' 
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
		$id =& $db->getOne($sql);
		return $id;		
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
				</td><td>'.nl2br($row['note']).'</td><td>'.$row['cretime'].'</td></tr>
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

	/**
	*	get conatct detail 
	*
	*	@param  $id				(int)		identity
	*	@param  $type			(string)	contactid or noteid
	*	@return $row			(array)		conatct data array
	*/

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

	/**
	*	get contact list which are binding to a specific customer
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

	/**
	*	general survey add html
	*
	*	@param  $customerid		(int)		customerid
	*	@param  $contactid		(int)		contactid
	*	@return $html			(array)		HTML
	*/

	function surveyAdd($customerid,$contactid){
		global $locate;
		$html .= '
				<form method="post" name="formSurvey" id="formSurvey">
				';
		$surveyHTML   =& astercrm::generateSurvey();
		$html .= $surveyHTML;
		$html .= '<div align="right">
					<input type="button" value="'.$locate->Translate("continue").'" name="btnAddSurvey" id="btnAddSurvey" onclick="xajax_saveSurvey(xajax.getFormValues(\'formSurvey\'));return false;">
					<input type="hidden" value="'.$customerid.'" name="customerid" id="customerid">
					<input type="hidden" value="'.$conatctid.'" name="contactid" id="contactid">
					</div>';
		$html .= '
				</form>
				';
		return $html;
	}


	/**
	*	general note add html
	*
	*	@param  $customerid		(int)		customerid
	*	@param  $contactid		(int)		contactid
	*	@return $html			(array)		HTML
	*/

	function noteAdd($customerid,$contactid){
		global $locate;
		$html .= '
				<form method="post" name="formNote" id="formNote">
				<table border="1" width="100%" class="adminlist">
					<tr>
						<td nowrap align="left">'.$locate->Translate("note").'</td>
						<td align="left">
							<textarea rows="4" cols="50" id="note" name="note" wrap="soft" style="overflow:auto"></textarea>
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

							&nbsp;  <input type="radio" name="attitude"   value="10"/><img src="skin/default/images/1.gif" width="25px" height="25px" border="0" /> 
							<input type="radio" name="attitude" value="5"/><img src="skin/default/images/2.gif" width="25px" height="25px" border="0" /> 
							<input type="radio" name="attitude"  value="-1"/><img src="skin/default/images/3.gif" width="25px" height="25px" border="0" />
							<input type="radio" name="attitude"  value="0" checked/> <img src="skin/default/images/4.gif" width="25px" height="25px" border="0" />
						</td>
					</tr>
					<tr>
						<td nowrap colspan=2 align=right><input type="button" id="btnAddNote" name="btnAddNote" value="'.$locate->Translate("continue").'" onclick="xajax_saveNote(xajax.getFormValues(\'formNote\'));return false;"></td>
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
					<td align="left"><input type="text" id="customer" name="customer" value="" onkeyup="ajax_showOptions(this,\'getCustomersByLetters\',event)" size="35" maxlength="50" autocomplete="off"><br /><input type="button" value="'.$locate->Translate("confirm").'" id="btnConfirmCustomer" name="btnConfirmCustomer" onclick="btnConfirmCustomerOnClick();"><input type="hidden" id="customerid" name="customerid" value="0">
					<input type="hidden" id="customerDetial" name="customerDetial" value="OFF">
					[<a href=? onclick="
						if (xajax.$(\'customerDetial\').value == \'OFF\'){
							xajax.$(\'websiteTR\').style.display = \'\';
							xajax.$(\'stateTR\').style.display = \'\';
							xajax.$(\'cityTR\').style.display = \'\';
							xajax.$(\'addressTR\').style.display = \'\';
							
							xajax.$(\'customerContactTR\').style.display = \'\';
							xajax.$(\'customerPhoneTR\').style.display = \'\';
							xajax.$(\'categoryTR\').style.display = \'\';
							
							xajax.$(\'customerDetial\').value = \'ON\';
							xajax.$(\'mainMobileTR\').style.display = \'\';
							xajax.$(\'mainFaxTR\').style.display = \'\';
							xajax.$(\'mainEmailTR\').style.display = \'\';
						}else{
							xajax.$(\'websiteTR\').style.display = \'none\';
							xajax.$(\'stateTR\').style.display = \'none\';
							xajax.$(\'cityTR\').style.display = \'none\';
							xajax.$(\'addressTR\').style.display = \'none\';
							
							xajax.$(\'customerContactTR\').style.display = \'none\';
							xajax.$(\'customerPhoneTR\').style.display = \'none\';
							xajax.$(\'categoryTR\').style.display = \'none\';
							
							xajax.$(\'customerDetial\').value = \'OFF\';
							xajax.$(\'mainMobileTR\').style.display = \'none\';
							xajax.$(\'mainFaxTR\').style.display = \'none\';
							xajax.$(\'mainEmailTR\').style.display = \'none\';
						};
						return false;">
						'.$locate->Translate("detail").'
					</a>] &nbsp; [<a href=? onclick="
							if (xajax.$(\'bankDetial\').value == \'OFF\'){
								xajax.$(\'bankNameTR\').style.display = \'\';
								xajax.$(\'bankZipTR\').style.display = \'\';
								xajax.$(\'bankAccountTR\').style.display = \'\';
								xajax.$(\'bankAccountNameTR\').style.display = \'\';
								xajax.$(\'bankDetial\').value = \'ON\';
							}else{
								xajax.$(\'bankNameTR\').style.display = \'none\';
								xajax.$(\'bankZipTR\').style.display = \'none\';
								xajax.$(\'bankAccountTR\').style.display = \'none\';
								xajax.$(\'bankAccountNameTR\').style.display = \'none\';
								xajax.$(\'bankDetial\').value = \'OFF\';
							}
							return false;">'.$locate->Translate("bank").'</a>]
					</td>
				</tr>
				<tr id="customerContactTR" name="customerContactTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("customer_contact").'</td>
					<td align="left">
						<input type="text" id="customerContact" name="customerContact" size="35" maxlength="35"><br>
						<select id="customerContactGender" name="customerContactGender">
							<option value="male">'.$locate->Translate("male").'</option>
							<option value="female">'.$locate->Translate("female").'</option>
							<option value="unknown" selected>'.$locate->Translate("unknown").'</option>
						</select>
					</td>
				</tr>
				<tr id="addressTR" name="addressTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("address").'</td>
					<td align="left"><input type="text" id="address" name="address" size="35" maxlength="200"></td>
				</tr>
				<tr id="cityTR" name="cityTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("zipcode").'/'.$locate->Translate("city").'</td>
					<td align="left"> <input type="text" id="zipcode" name="zipcode" size="10" maxlength="10">&nbsp;&nbsp;<input type="text" id="city" name="city" size="17" maxlength="50"></td>
				</tr>
				<tr id="stateTR" name="stateTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("state").'</td>
					<td align="left"><input type="text" id="state" name="state" size="35" maxlength="50"></td>
				</tr>
				<tr id="customerPhoneTR" name="customerPhoneTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("customer_phone").'</td>
					<td align="left"><input type="text" id="customerPhone" name="customerPhone" size="35" maxlength="50"></td>
				</tr>
				<tr name="mainMobileTR" id="mainMobileTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("mobile").'</td>
					<td align="left"><input type="text" id="mainMobile" name="mainMobile" size="35"></td>
				</tr>
				<tr name="mainEmailTR" id="mainEmailTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("email").'</td>
					<td align="left"><input type="text" id="mainEmail" name="mainEmail" size="35"></td>
				</tr>				
				<tr id="websiteTR" name="websiteTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("website").'</td>
					<td align="left"><input type="text" id="website" name="website" size="35" maxlength="100" value="http://"><br><input type="button" value="'.$locate->Translate("browser").'" onclick="openWindow(xajax.$(\'website\').value);return false;"></td>
				</tr>
				<!--<tr id="zipcodeTR" name="zipcodeTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("zipcode").'</td>
					<td align="left"><input type="text" id="zipcode" name="zipcode" size="10" maxlength="10"></td>
				</tr>-->
				<!--******新增的3个字段*****-->
				<tr name="mainFaxTR" id="mainFaxTR" style="display:none">
					<td nowrap align="left">'.$locate->Translate("fax").'</td>
					<td align="left"><input type="text" id="mainFax" name="mainFax" size="35"></td>
				</tr>
				<!--*********************************************************-->
				<tr id="categoryTR" name="categoryTR" style="display:none">
					<td nowrap align="left" style="border-bottom:1px double orange;">'.$locate->Translate("category").'</td>
					<td align="left" style="border-bottom:1px double orange"><input type="text" id="category" name="category" size="35"></td>
				</tr>';
				/*
				*  control bank data
				*/
				$html .='
					
						<input type="hidden" id="bankDetial" name="bankDetial" value="OFF">
					<!--********************-->
					
					<tr id="bankAccountNameTR" name="bankAccountNameTR" style="display:none">
						<td nowrap align="left">'.$locate->Translate("bank_account_name").'</td>
						<td align="left"><input type="text" id="bankaccountname" name="bankaccountname" size="35"></td>
					</tr>
					<tr id="bankNameTR" name="bankNameTR" style="display:none">
					<td nowrap align="left" style="border-top:1px double orange;">'.$locate->Translate("bank_name").'</td>
					<td align="left" style="border-top:1px double orange"><input type="text" id="bankname" name="bankname" size="35"></td>
					</tr>
					<tr id="bankZipTR" name="bankZipTR" style="display:none">
						<td nowrap align="left">'.$locate->Translate("bank_zip").'</td>
						<td align="left"><input type="text" id="bankzip" name="bankzip" size="35"></td>
					</tr>
					<tr id="bankAccountTR" name="bankAccountTR" style="display:none">
						<td nowrap align="left">'.$locate->Translate("bank_account").'</td>
						<td align="left"><input type="text" id="bankaccount" name="bankaccount" size="35"></td>
					</tr>	
					<!--********************-->
					';
	}else{
		$customer =& astercrm::getCustomerByID($customerid);
		$html .= '
				<tr>
					<td nowrap align="left"><a href=? onclick="xajax_showCustomer('. $customerid .');return false;">'.$locate->Translate("customer_name").'</a></td>
					<td align="left"><input type="text" id="customer" name="customer" value="'. $customer['customer'].'" onkeyup="ajax_showOptions(this,\'getCustomersByLetters\',event)" size="35" maxlength="50" autocomplete="off" readOnly><input type="button" value="'.$locate->Translate("cancel").'" id="btnConfirmCustomer" name="btnConfirmCustomer" onclick="btnConfirmCustomerOnClick();"><input type="hidden" id="customerid" name="customerid" value="'. $customerid .'"></td>
				</tr>
				';
	}
	if(ENABLE_CONTACT != '0'){ //控制contact模块的显示与隐藏
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
			$contact =& astercrm::getContactByID($contactid);

				$html .='
					<tr>
						<td nowrap align="left"><a href=? onclick="xajax_showContact('. $contactid .');return false;">'.$locate->Translate("contact").'</a></td>
						<td align="left"><input type="text" id="contact" name="contact" value="'. $contact['contact'].'" onkeyup="ajax_showOptions(this,\'getContactsByLetters\',event)" size="35" maxlength="50" autocomplete="off" readOnly><input type="button" value="'.$locate->Translate("cancel").'" id="btnConfirmContact" name="btnConfirmContact" onclick="btnConfirmContactOnClick();"><input type="hidden" id="contactid" name="contactid" value="'. $contactid .'"></td>
					</tr>
					';
		}
	}

	//add survey html
	$html .= '<tr><td colspan="2">';

	$surveyHTML =& astercrm::generateSurvey();
	$html .= $surveyHTML;

	$html .= '</tr></td>';
	//if(!defined('HOME_DIR')) define('HOME_DIR',dirname(dirname(__FILE__)));
	//add note html
	$html .='
			<tr>
				<td nowrap align="left">'.$locate->Translate("note").'</td>
				<td align="left">
					<textarea rows="4" cols="50" id="note" name="note" wrap="soft" style="overflow:auto;"></textarea>
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
					&nbsp;  <input type="radio" name="attitude"   value="10"/><img src="skin/default/images/10.gif" width="25px" height="25px" border="0" /> 
					<input type="radio" name="attitude" value="5"/><img src="skin/default/images/5.gif" width="25px" height="25px" border="0" /> 
					<input type="radio" name="attitude"  value="-1"/><img src="skin/default/images/-1.gif" width="25px" height="25px" border="0" />
					<input type="radio" name="attitude"  value="0" checked/> <img src="skin/default/images/0.gif" width="25px" height="25px" border="0" />
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

	/**
	*  Devuelte el registro de acuerdo al $id pasado.
	*
	*	@param $id	(int)	Identificador del registro para hacer la b&uacute;squeda en la consulta SQL.
	*	@return $row	(array)	Arreglo que contiene los datos del registro resultante de la consulta SQL.
	*/
	
	function &getRecordByID($id,$table){
		global $db;
		
		$sql = "SELECT * FROM $table "
				." WHERE id = $id";
		Customer::events($sql);
		$row =& $db->getRow($sql);
		return $row;
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

	/**
	*  generate HTML to add survey
	*  HTML include survey title, all survey options, survey note
	*	@return $html	(string)	
	*							
	*/

	function &generateSurvey(){
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
		$options =& astercrm::getOptions($surveyid);
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
			$note =& astercrm::getRecord($id,'note');
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
						<td align="left">'.nl2br($note['note']). '</td>
					</tr>
					<tr>
						<td nowrap align="left">'.$locate->Translate("append").'</td>
						<td align="left"><textarea rows="4" cols="50" id="note" name="note" wrap="soft" style="overflow:auto"></textarea></td>
					</tr>
					<tr>
						<td nowrap align="left">'.$locate->Translate("priority").'</td>
						<td align="left">
							<select id="priority" name="priority">'.$options.'</select>

							&nbsp;  <input type="radio" name="attitude"   value="10" ';
							if($note['attitude'] == '10'){
								$html .= 'checked';
							}
							$html .= '/><img src="skin/default/images/10.gif" width="25px" height="25px" border="0" /> 
							<input type="radio" name="attitude"  value="5" ';
							if($note['attitude'] == '5'){
								$html .= 'checked';
							}
							$html .= ' /><img src="skin/default/images/5.gif" width="25px" height="25px" border="0" /> 
							<input type="radio" name="attitude"  value="-1" ';
							if($note['attitude'] == '-1'){
								$html .= 'checked';
							}
							$html .= ' 
							/><img src="skin/default/images/-1.gif" width="25px" height="25px" border="0" />
							<input type="radio" name="attitude"  value="0" ';
							if($note['attitude'] == '0'){
								$html .= 'checked';
							}
							$html .= ' 
							/> <img src="skin/default/images/0.gif" width="25px" height="25px" border="0" />
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
			$customer =& astercrm::getCustomerByID($id);
			if ($customer['contactgender'] == 'male')
				$customerMaleSelected = 'selected';
			elseif ($customer['contactgender'] == 'female')
				$customerFemaleSelected = 'selected';
			else
				$customerUnknownSelected = 'selected';

			$html = '
					<form method="post" name="frmCustomerEdit" id="frmCustomerEdit">
					<table border="0" width="100%">
					<tr id="customerTR" name="customerTR">
						<td nowrap align="left">'.$locate->Translate("customer_name").'</td>
						<td align="left"><input type="text" id="customer" name="customer" size="35" maxlength="100" value="' . $customer['customer'] . '"><input type="hidden" id="customerid"  name="customerid" value="'.$customer['id'].'">
</td>
					</tr>
					<tr id="websiteTR" name="websiteTR">
						<td nowrap align="left">'.$locate->Translate("website").'</td>
						<td align="left"><input type="text" id="website" name="website" size="35" maxlength="100" value="' . $customer['website'] . '"><input type="button" value="'.$locate->Translate("browser").'"  onclick="openWindow(xajax.$(\'website\').value);return false;"></td>
					</tr>
					<tr id="stateTR" name="stateTR">
						<td nowrap align="left">'.$locate->Translate("state").'</td>
						<td align="left"><input type="text" id="state" name="state" size="35" maxlength="50" value="'.$customer['state'].'"></td>
					</tr>
					<tr id="cityTR" name="cityTR">
						<td nowrap align="left">'.$locate->Translate("city").'</td>
						<td align="left"><input type="text" id="city" name="city" size="35" maxlength="50" value="'.$customer['city'].'"></td>
					</tr>
					<tr id="addressTR" name="addressTR">
						<td nowrap align="left">'.$locate->Translate("address").'</td>
						<td align="left"><input type="text" id="address" name="address" size="35" maxlength="200" value="' . $customer['address'] . '"></td>
					</tr>
					<tr id="zipcodeTR" name="zipcodeTR">
						<td nowrap align="left">'.$locate->Translate("zipcode").'</td>
						<td align="left"><input type="text" id="zipcode" name="zipcode" size="10" maxlength="10" value="' . $customer['zipcode'] . '"></td>
					</tr>
					<!--*********************************************************-->
					<tr name="mainMobileTR" id="mainMobileTR">
						<td nowrap align="left">'.$locate->Translate("mobile").'</td>
						<td align="left"><input type="text" id="mainMobile" name="mainMobile" size="35" value="' . $customer['mobile'] . '"></td>
					</tr>
					<tr name="mainFaxTR" id="mainFaxTR" >
						<td nowrap align="left">'.$locate->Translate("fax").'</td>
						<td align="left"><input type="text" id="mainFax" name="mainFax" size="35" value="' . $customer['fax'] . '"></td>
					</tr>
					<tr name="mainEmailTR" id="mainEmailTR">
						<td nowrap align="left">'.$locate->Translate("email").'</td>
						<td align="left"><input type="text" id="mainEmail" name="mainEmail" size="35" value="' . $customer['email'] . '"></td>
					</tr>				
					<!--*********************************************************-->
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
					<tr id="bankNameTR" name="bankNameTR">
						<td nowrap align="left">'.$locate->Translate("bank_name").'</td>
						<td align="left"><input type="text" id="bankname" name="bankname" size="35"  value="' . $customer['bankname'] . '"></td>
					</tr>
					<tr id="bankZipTR" name="bankZipTR">
						<td nowrap align="left">'.$locate->Translate("bank_zip").'</td>
						<td align="left"><input type="text" id="bankzip" name="bankzip" size="35"  value="' . $customer['bankzip'] . '"></td>
					</tr>
					<tr id="bankAccountNameTR" name="bankAccountNameTR">
						<td nowrap align="left">'.$locate->Translate("bank_account_name").'</td>
						<td align="left"><input type="text" id="bankaccountname" name="bankaccountname" size="35" value="' . $customer['bankaccountname'] . '"></td>
					</tr>
					<tr id="bankAccountTR" name="bankAccountTR">
						<td nowrap align="left">'.$locate->Translate("bank_account").'</td>
						<td align="left"><input type="text" id="bankaccount" name="bankaccount" size="35"  value="' . $customer['bankaccount'] . '"></td>
					</tr>
					<tr id="customerPhoneTR" name="customerPhoneTR">
						<td nowrap align="left">'.$locate->Translate("customer_phone").'</td>
						<td align="left"><input type="text" id="customerPhone" name="customerPhone" size="35" maxlength="50"  value="' . $customer['phone'] . '"></td>
					</tr>
					<tr id="categoryTR" name="categoryTR">
						<td nowrap align="left">'.$locate->Translate("category").'</td>
						<td align="left"><input type="text" id="category" name="category" size="35"  value="' . $customer['category'] . '"></td>
					</tr>

					<tr>
						<td colspan="2" align="center"><button  id="btnContinue" name="btnContinue"  onClick=\'xajax_update(xajax.getFormValues("frmCustomerEdit"),"customer");return false;\'>'.$locate->Translate("continue").'</button></td>
					</tr>
					';
		}else {
			$contact =& astercrm::getContactByID($id);
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
		$customer =& astercrm::getCustomerByID($id,$type);
		$contactList =& astercrm::getContactListByID($customer['id']);
		$html = '
				<table border="0" width="100%">
				<tr>
					<td nowrap align="left" width="80">'.$locate->Translate("customer_name").'&nbsp;[<a href=? onclick="xajax_showNote(\''.$customer['id'].'\',\'customer\');return false;">'.$locate->Translate("note").'</a>]</td>
					<td align="left">'.$customer['customer'].'&nbsp;[<a href=? onclick="xajax_edit(\''.$customer['id'].'\',\'customer\');return false;">'.$locate->Translate("edit").'</a>]</td>
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
				<!--**********************-->
				<tr>
					<td nowrap align="left">'.$locate->Translate("mobile").'</td>
					<td align="left"><a href=? onclick="xajax_dial(\''.$customer['mobile'].'\');return false;">'.$customer['mobile'].'</a></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("fax").'</td>
					<td align="left"><a href=? onclick="xajax_dial(\''.$customer['fax'].'\');return false;">'.$customer['fax'].'</a></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("email").'</td>
					<td align="left">'.$customer['email'].'</td>
				</tr>	
				<!--**********************-->
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
					<td nowrap align="left">'.$locate->Translate("bank_name").'</td>
					<td align="left">'.$customer['bankname'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("bank_zip").'</td>
					<td align="left">'.$customer['bankzip'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("bank_account_name").'</td>
					<td align="left">'.$customer['bankaccountname'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("bank_account").'</td>
					<td align="left">'.$customer['bankaccount'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("customer_phone").'</td>
					<td align="left"><a href=? onclick="xajax_dial(\''.$customer['phone'].'\');return false;">'.$customer['phone'].'</a></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("category").'</td>
					<td align="left">'.$customer['category'].'</td>
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
	*  Muestra todos los datos de un registro sobre el DIV identificado por "formDiv".
	*
	*	@param $id		(int)		Identificador del registro a ser mostrado.
	*	@return $html	(string) Devuelve una cadena de caracteres que contiene una tabla con los datos 
	*									a extraidos de la base de datos para ser mostrados 
	*/
	function showContactRecord($id,$type="contact"){
    	global $locate;
		$contact =& astercrm::getContactByID($id,$type);
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

		astercrm::events($sql);
		$res =& $db->query($sql);
		while ($res->fetchInto($row)) {
			foreach ($row as $val){
				$val .= ',';
				if ($val != mb_convert_encoding($val,"UTF-8","UTF-8"))
						$val='"'.mb_convert_encoding($val,"UTF-8","GB2312").'"';
				
				$txtstr .= '"'.$val.'"';
			}
			$txtstr .= "\n";
		}
		return $txtstr;
	}

	function exportDataToCSV($sql){
		global $db;
		astercrm::events($sql);
		$res =& $db->query($sql);
		while ($res->fetchInto($row)) {
			foreach ($row as $val){
				if ($val != mb_convert_encoding($val,"UTF-8","UTF-8"))
						$val='"'.mb_convert_encoding($val,"UTF-8","GB2312").'"';
				
				$txtstr .= '"'.$val.'"'.',';
			}
			$txtstr .= "\n";
		}
		return $txtstr;
	}

	/**
	*  return customerid if match a phonenumber
	*
	*	@param $type		(string)		data to be exported
	*	@return $txtstr		(string) 		csv format datas
	*/

	function getCustomerByCallerid($callerid){
		global $db;
		$sql = "SELECT id FROM customer WHERE phone LIKE '%$callerid'";
		$customerid =& $db->getOne($sql);
		astercrm::events($sql);
		return $customerid;
	}

	function getSql($searchContent,$searchField,$table){
		global $db;
		$i=0;
		$joinstr='';
		foreach ($searchContent as $value){
			$value=trim($value);
			if (strlen($value)!=0 && $searchField[$i] != null){
				//if ($value != mb_convert_encoding($value,"UTF-8","UTF-8"))
				//	$value='"'.mb_convert_encoding($value,"UTF-8","GB2312").'"';
				$joinstr.="AND $searchField[$i] like '%".$value."%' ";
			}
			$i++;
		}
		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND');
			$sql = 'SELECT * FROM '.$table.' WHERE '.$joinstr;
		}else {
			$sql = 'SELECT * FROM '.$table.'';
		}
		//if ($sql != mb_convert_encoding($sql,"UTF-8","UTF-8")){
		//	$sql='"'.mb_convert_encoding($sql,"UTF-8","GB2312").'"';
		//}		
		return $sql;
	}
}
?>
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
			insertNewDialedlist
			insertNewAccountgroup    向accountgroup表插入数据
			insertNewCampaign
			insertNewMonitor			向monitorrecord表插入数据

			updateCustomerRecord	更新customer表数据
			updateContactRecord		更新contact表数据
			updateNoteRecord		更新note表数据
			updateAccountRecord
			updateAccountgroupRecord  更新accountgroup表数据
			updateRecords		更新数据
			updateCampaignRecord

			deleteRecord			从表中删除数据(以id作为标识)
			updateField				更新表中的数据(以id作为标识)
			events					日志记录
			checkValues				根据条件从数据库中检索是否有符合条件的记录
			showNoteList			生成note列表的HTML文件

			getCustomerByID			根据customerid获取customer记录信息或者根据noteid获取与之相关的customer信息
			getContactByID			根据contactid获取contact记录信息或者根据noteid获取与之相关的contact信息
			getContactListByID		根据customerid获取与之邦定的contact记录

			getGroupCurcdr			取出当前groupadmin所在group所包含的所有exten和agent的curcdr记录

			getRecord				从表中读取数据(以id作为标识)
			getRecordByID			根据id获取记录
			getRecordByField($field,$value,$table)
					根据某一条件获得记录
			getCountByField($field,$value,$table)
					根据某一条件获得记录数目
			getCustomerByCallerid	根据callerid查找customer表看是否有匹配的id
			getRecordsByGroupid

			getTableRecords				从表中读取数据
			getSql              得到多条件搜索的sql语句
			getGroupMemberListByID 得到组成员 
			getOptions				读取survey的所有option
			getNoteListByID			根据customerid或者contactid获取与之邦定的note记录

			surveyAdd				生成添加survey的HTML语法
			noteAdd					生成添加note的HTML语法
			formAdd					生成添加综合信息(包括customer, contact, survey, note)的HTML语法
			formEdit				生成综合信息编辑的HTML语法, 
									包括编辑customer, contact以及添加note

			showCustomerRecord		生成显示customer信息的HTML语法
			showContactRecord		生成显示contact信息的HTML语法

			exportCSV				生成csv文件内容, 目前支持导出customer, contact

			variableFiler			用于转译变量, 自动加\
			exportDataToCSV     得到要导出的sql语句的结果集，转换为符合csv格式的文本字符串
			createSqlWithStype	根据filter,content,searchtype生成查询条件语句

			----------------2008-6 by donnie---------------------------------------
			formDiallistAdd			生成customer对应的diallist的html
			getDiallistNumRowsMorewithstype   customer对应的diallist多条件带搜索类型的记录数
			getDiallistFilteredMorewithstype  customer对应的diallist多条件带搜索类型的结果集
			getDiallistNumRowsMore	得到customer对应的diallist多条件搜索记录数
			getDiallistFilteredMore customer对应的diallist多条件搜索结果集
			getDiallistNumRows		得到customer对应的diallist全部记录数
			getAllDiallist			customer对应的diallist全部结果集
			createDiallistGrid		生成customer对应的diallist列表
			getCdrRecordsFilteredMorewithstype   得到customer对应的CDR多条件带搜索类型的结果集
			getCdrNumRowsMorewithstype   得到customer对应的CDR多条件带搜索类型的记录数
			getCdrNumRowsMore		得到customer对应的CDR多条件搜索记录数
			getCdrRecordsFilteredMore	得到customer对应的CDR多条件搜索结果集
			getCdrNumRows			得到customer对应的CDR全部记录数
			getAllCdrRecords		得到customer对应的CDR全部结果集
			createCdrGrid			生成customer对应的CDR列表
			getRecNumRows
			getAllRecRecords
			getRecNumRowsMore
			getRecRecordsFilteredMore
			getRecNumRowsMorewithstype
			getRecRecordsFilteredMorewithstype
			createRecordsGrid
			--------------------------------------------------------------------------
			
* Private Functions List
			generateSurvey			生成添加survey的HTML语法


* Revision 0.047  2008/2/24 10:11:00  last modified by solo
* Desc: add a new function insertNewMonitor

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

	function getTrunkinfo($trunk,$trunkdid = ''){
		global $db;
		if($trunkdid != ''){
			$query = "SELECT * FROM trunkinfo WHERE didnumber = '$trunkdid'";
			astercrm::events($query);
			$res =& $db->getRow($query);
			if($res) return $res;
		}

		$query = "SELECT * FROM trunkinfo WHERE trunkchannel = '$trunk'";
		
		astercrm::events($query);
		$res =& $db->getRow($query);
		return $res;
	}

	function insertNewMonitor($callerid,$filename,$uniqueid){
		global $db;
		$query= "INSERT INTO monitorrecord SET "
				."callerid='".$callerid."', "
				."filename='".$filename."', "
				."cretime=now(), "
				."groupid = ".$_SESSION['curuser']['groupid'].", "
				."extension = ".$_SESSION['curuser']['extension'].", "
				."uniqueid = '".$uniqueid."', "
				."creby='".$_SESSION['curuser']['username']."'";
		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}

	function getAll($table,$field = '', $value = ''){
		global $db;
		if (trim($field) != '' && trim($value) != ''){
			$query = "SELECT * FROM $table WHERE $field = '$value' ";
		}else{
			$query = "SELECT * FROM $table ";
		}
		astercrm::events($query);
		$res = $db->query($query);
		return $res;
	}

	function getGroups(){
		global $db;
		$sql = "SELECT * FROM astercrm_accountgroup";
		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function getGroupById($groupid){
		global $db;
		$sql = "SELECT groupname  FROM astercrm_accountgroup WHERE id = $groupid";
		astercrm::events($sql);
		$res =& $db->getRow($sql);
		return $res;
	}

	/**
	* update table values
	*	
	*	@param	$table				string	table name
	*	@param	$field					string	field name
	*	@param	$old_val		string	old value
	*	@param	$new_val		string	new value
	*
	$res = astercrm::updateRecords('accountgroup','groupid',$id,0);

	*/
	function updateRecords($table,$field,$old_val,$new_val){
		global $db;
		$query = "UPDATE $table SET $field = '$new_val' WHERE $field = '$old_val'";
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

	function getTableRecords($tableName){
		global $db;
		$query = "select * from $tableName";
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
		$query= "INSERT INTO customer SET "
				."customer='".$f['customer']."', "
				."website='".$f['website']."', "
				."country='".$f['country']."', "
				."address='".$f['address']."', "
				."zipcode='".$f['zipcode']."', "
				."city='".$f['city']."', "
				."state='".$f['state']."', "
				."contact='".$f['customerContact']."', "
				."contactgender='".$f['customerContactGender']."', "
				."phone='".$f['customerPhone']."', "
				."phone_ext='".$f['customerPhone_ext']."', "
				."category='".$f['category']."', "
				."bankname='".$f['bankname']."', "
				."bankzip='".$f['bankzip']."', "
				."bankaccount='".$f['bankaccount']."', "
				."bankaccountname='".$f['bankaccountname']."', "
				."fax='".$f['mainFax']."', "
				."fax_ext='".$f['mainFax_ext']."', "
				."mobile='".$f['mainMobile']."', "
				."email='".$f['mainEmail']."', "
				."cretime=now(), "
				."groupid = ".$_SESSION['curuser']['groupid'].", "
				."creby='".$_SESSION['curuser']['username']."'";
		astercrm::events($query);
		$res =& $db->query($query);
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
		
		$query= "INSERT INTO contact SET "
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
				."fax_ext='".$f['fax_ext']."', "
				."email='".$f['email']."', "
				."cretime=now(), "
				."creby='".$_SESSION['curuser']['username']."', "
				."groupid = ".$_SESSION['curuser']['groupid'].", "
				."customerid=". $customerid ;
		astercrm::events($query);
		$res =& $db->query($query);
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
		$query= "INSERT INTO note SET "
				."note='".$f['note']."', "
				."attitude='".$f['attitude']."', "
				."priority=".$f['priority'].", "
				."cretime=now(), "
				."creby='".$_SESSION['curuser']['username']."', "
				."groupid = ".$_SESSION['curuser']['groupid'].", "
				."customerid=". $customerid . ", "
				."contactid=". $contactid ;
		//print $query;
		//exit;
		astercrm::events($query);

		$res =& $db->query($query);
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
		$query= "INSERT INTO astercrm_account SET "
				."username='".$f['username']."', "
				."password='".$f['password']."', "
				."firstname='".$f['firstname']."',"
				."lastname='".$f['lastname']."',"
				."extension='".$f['extension']."',"
				."agent = '".$f['agent']."',"
				."channel='".$f['channel']."',"			// added 2007/10/30 by solo
				."usertype='".$f['usertype']."',"
				."extensions='".$f['extensions']."', "	// added 2007/11/12 by solo
				."groupid='".$f['groupid']."', "	// added 2007/11/12 by solo
				."dialinterval='".$f['dialinterval']."', "
				."accountcode='".$f['accountcode']."'";

		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}

	function insertNewAccountgroup($f){
		global $db;
		$f = astercrm::variableFiler($f);
		$query= "INSERT INTO astercrm_accountgroup SET "
				."groupname='".$f['groupname']."', "
				."groupid='".$f['groupid']."', "
				."creby = '".$_SESSION['curuser']['username']."',"
				."cretime = now(),"
				."agentinterval='".$f['agentinterval']."',"
				."groupnote='".$f['groupnote']."',"
				."pdcontext='".$f['pdcontext']."',"
				."pdextension='".$f['pdextensions']."' ";		// added 2007/10/30 by solo
		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}

	function insertNewCampaign($f){
		global $db;
		$f = astercrm::variableFiler($f);
		$query= "INSERT INTO campaign SET "
				."campaignname='".$f['campaignname']."', "
				."campaignnote='".$f['campaignnote']."', "
				."enable='".$f['enable']."', "
				."outcontext='".$f['outcontext']."', "
				."incontext='".$f['incontext']."', "
				."inexten='".$f['inexten']."', "
				."queuename='".$f['queuename']."', "
				."maxtrytime='".$f['maxtrytime']."', "
				."groupid='".$f['groupid']."', "
				."creby = '".$_SESSION['curuser']['username']."',"
				."cretime = now()";
		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}

	function insertNewDiallist($f){
		global $db;
		$f = astercrm::variableFiler($f);
		
		$query= "INSERT INTO diallist SET "
				."dialnumber='".$f['dialnumber']."', "
				."groupid='".$f['groupid']."', "
				."dialtime='".$f['dialtime']."', "
				."creby='".$_SESSION['curuser']['username']."', "
				."cretime= now(), "
				."campaignid= ".$f['campaignid'].", "
				."assign='".$f['assign']."'";
		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}

	function insertNewDialedlist($f){
		global $db;
		$f = astercrm::variableFiler($f);
		
		$query = 'INSERT INTO dialedlist (dialednumber,dialedby,dialedtime,groupid,campaignid,trytime,assign) VALUES ("'.$f['dialednumber'].'","'.$f['dialedby'].'",now(),'.$f['groupid'].','.$f['campaignid'].','.$f['trytime'].',"'.$f['assign'].'")';
		astercrm::events($query);
		$res =& $db->query($query);
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
		$query= "UPDATE customer SET "
				."customer='".$f['customer']."', "
				."website='".$f['website']."', "
				."country='".$f['country']."', "
				."address='".$f['address']."', "
				."zipcode='".$f['zipcode']."', "
				."phone='".$f['customerPhone']."', "
				."phone_ext='".$f['customerPhone_ext']."', "
				."contact='".$f['customerContact']."', "
				."contactgender='".$f['customerContactGender']."', "
				."state='".$f['state']."', "
				."city='".$f['city']."', "
				."category='".$f['category']."', "
				."bankname='".$f['bankname']."', "
				."bankzip='".$f['bankzip']."', "
				."fax='".$f['mainFax']."', "
				."fax_ext='".$f['mainFax_ext']."', "
				."mobile='".$f['mainMobile']."', "
				."email='".$f['mainEmail']."', "
				."bankaccount='".$f['bankaccount']."', "
				."bankaccountname='".$f['bankaccountname']."' "
				."WHERE id='".$f['customerid']."'";

		astercrm::events($query);
		$res =& $db->query($query);
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
		
		$query= "UPDATE contact SET "
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
				."fax_ext='".$f['fax_ext']."', "
				."email='".$f['email']."' "
				."WHERE id='".$f['contactid']."'";
		astercrm::events($query);
		$res =& $db->query($query);
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

			$query= "UPDATE note SET "
					."note='".$f['note']."', "
					."priority=".$f['priority']." ,"
					."attitude='".$f['attitude']."' "
					."WHERE id='".$f['noteid']."'";
		else
			if (empty($f['note']))
				$query= "UPDATE note SET "
						."attitude='".$f['attitude']."', "
						."priority=".$f['priority']." "
						."WHERE id='".$f['noteid']."'";
			else
				$query= "UPDATE note SET "
						."note=CONCAT(note,'<br>',now(),' ".$f['note']." by " .$_SESSION['curuser']['username']. "'), "
						."attitude='".$f['attitude']."', "
						."priority=".$f['priority']." "
						."WHERE id='".$f['noteid']."'";
		astercrm::events($query);
		$res =& $db->query($query);
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
		
		$query= "UPDATE astercrm_account SET "
				."username='".$f['username']."', "
				."password='".$f['password']."', "
				."firstname='".$f['firstname']."', "
				."lastname='".$f['lastname']."', "
				."extension='".$f['extension']."', "
				."agent ='".$f['agent']."', "
				."usertype='".$f['usertype']."', "
				."channel='".$f['channel']."', "	// added 2007/10/30 by solo
				."extensions='".$f['extensions']."', "
				."groupid='".$f['groupid']."', "     // new add 2007-11-15
				."dialinterval='".$f['dialinterval']."', "
				."accountcode='".$f['accountcode']."' "	// added 2007/11/12 by solo
				."WHERE id='".$f['id']."'";

		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}

	function updateAccountgroupRecord($f){
		global $db;
		$f = astercrm::variableFiler($f);
		
		$query= "UPDATE astercrm_accountgroup SET "
				."groupname='".$f['groupname']."', "
				."groupid='".$f['groupid']."', "
				."agentinterval='".$f['agentinterval']."', "
				."groupnote='".$f['groupnote']."',"
				."pdcontext='".$f['pdcontext']."', "
				."pdextension='".$f['pdextensions']."' "
				."WHERE id='".$f['id']."'";
		
		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}

	function updateDiallistRecord($f){
		global $db;
		$f = astercrm::variableFiler($f);
		
		$query= "UPDATE diallist SET "
				."dialnumber='".$f['dialnumber']."', "
				."groupid='".$f['groupid']."', "
				."dialtime='".$f['dialtime']."', "
				."campaignid= ".$f['campaignid'].", "
				."assign='".$f['assign']."'"
				."WHERE id='".$f['id']."'";
		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}

	function updateCampaignRecord($f){
		global $db;
		$f = astercrm::variableFiler($f);
		
		$query= "UPDATE campaign SET "
				."campaignname='".$f['campaignname']."', "
				."campaignnote='".$f['campaignnote']."', "
				."enable='".$f['enable']."', "				
				."outcontext='".$f['outcontext']."', "
				."incontext='".$f['incontext']."', "
				."inexten='".$f['inexten']."', "
				."queuename='".$f['queuename']."', "
				."maxtrytime='".$f['maxtrytime']."', "
				."groupid='".$f['groupid']."' "
				."WHERE id=".$f['id'];
		astercrm::events($query);
		$res =& $db->query($query);
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
		
		$query = "SELECT * FROM $table WHERE id = $id";
		astercrm::events($query);
		$row =& $db->getRow($query);
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

		$query = "UPDATE $table SET $field='$value' WHERE id='$id'";
		astercrm::events($query);
		$res =& $db->query($query);
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
			$query = "SELECT id FROM $tblName WHERE $fldName='$myValue'";
		else
			$query = "SELECT id FROM $tblName WHERE $fldName=$myValue";
		
		if ($fldName1 != null)
			if ($type1 == "string")
				$query .= "AND $fldName1='$myValue1'";
			else
				$query .= "AND $fldName1=$myValue1";

		
		astercrm::events($query);
		$id =& $db->getOne($query);
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
			return astercrm::getRecordById($id,'customer');//$query = "SELECT * FROM customer WHERE id = $id";
		elseif ($type == 'contact')
			$query = "SELECT * FROM customer RIGHT JOIN (SELECT customerid FROM contact WHERE id = $id ) g ON customer.id = g.customerid";
		else
			$query = "SELECT * FROM customer RIGHT JOIN (SELECT customerid FROM note WHERE id = $id ) g ON customer.id = g.customerid";
		
		astercrm::events($query);
		$row =& $db->getRow($query);
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
			$query = "SELECT * FROM contact WHERE id = $id";
		elseif ($type == 'note')
			$query = "SELECT * FROM contact RIGHT JOIN (SELECT contactid FROM note WHERE id = $id ) g ON contact.id = g.contactid";
		
		astercrm::events($query);
		$row =& $db->getRow($query);
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
		$query = "SELECT * FROM contact WHERE customerid=$customerid";
		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}

	function getGroupMemberListByID($groupid = null){
		global $db;
		if ($groupid == null)
			$query = "SELECT id,username,extension,agent FROM astercrm_account";
		else
			$query = "SELECT id,username,extension,agent FROM astercrm_account WHERE groupid =$groupid";
		astercrm::events($query);
		$res =& $db->query($query);
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
			$query = "SELECT * FROM note WHERE customerid = '$id' ORDER BY cretime DESC";
		else
			$query = "SELECT * FROM note WHERE contactid = '$id' ORDER BY cretime DESC";

		astercrm::events($query);
		$res =& $db->query($query);
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

							&nbsp;  <input type="radio" name="attitude"   value="10"/><img src="skin/default/images/10.gif" width="25px" height="25px" border="0" /> 
							<input type="radio" name="attitude" value="5"/><img src="skin/default/images/5.gif" width="25px" height="25px" border="0" /> 
							<input type="radio" name="attitude"  value="-1"/><img src="skin/default/images/-1.gif" width="25px" height="25px" border="0" />
							<input type="radio" name="attitude"  value="0" checked/> <img src="skin/default/images/0.gif" width="25px" height="25px" border="0" />
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
	global $locate,$config;
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
					<input type="hidden" id="hidAddCustomerDetails" name="hidAddCustomerDetails" value="OFF">
					[<a href=? onclick="
						if (xajax.$(\'hidAddCustomerDetails\').value == \'OFF\'){
							showObj(\'trAddCustomerDetails\');
							xajax.$(\'hidAddCustomerDetails\').value = \'ON\';
						}else{
							hideObj(\'trAddCustomerDetails\');
							xajax.$(\'hidAddCustomerDetails\').value = \'OFF\';
						};
						return false;">
						'.$locate->Translate("detail").'
					</a>] &nbsp; [<a href=? onclick="
							if (xajax.$(\'hidAddBankDetails\').value == \'OFF\'){
								showObj(\'trAddBankDetails\');
								xajax.$(\'hidAddBankDetails\').value = \'ON\';
							}else{
								hideObj(\'trAddBankDetails\');
								xajax.$(\'hidAddBankDetails\').value = \'OFF\';
							}
							return false;">'.$locate->Translate("bank").'</a>]
					</td>
				</tr>
				<tr id="trAddCustomerDetails" name="trAddCustomerDetails" style="display:none">
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
				<tr id="trAddCustomerDetails" name="trAddCustomerDetails" style="display:none">
					<td nowrap align="left">'.$locate->Translate("address").'</td>
					<td align="left"><input type="text" id="address" name="address" size="35" maxlength="200"></td>
				</tr>
				<tr id="trAddCustomerDetails" name="trAddCustomerDetails" style="display:none">
					<td nowrap align="left">'.$locate->Translate("zipcode").'/'.$locate->Translate("city").'</td>
					<td align="left"> <input type="text" id="zipcode" name="zipcode" size="10" maxlength="10">&nbsp;&nbsp;<input type="text" id="city" name="city" size="17" maxlength="50"></td>
				</tr>
				<tr id="trAddCustomerDetails" name="trAddCustomerDetails" style="display:none">
					<td nowrap align="left">'.$locate->Translate("state").'</td>
					<td align="left"><input type="text" id="state" name="state" size="35" maxlength="50"></td>
				</tr>
				<tr id="trAddCustomerDetails" name="trAddCustomerDetails" style="display:none">
					<td nowrap align="left">'.$locate->Translate("country").'</td>
					<td align="left"><input type="text" id="country" name="country" size="35" maxlength="50"></td>
				</tr>
				<tr id="trAddCustomerDetails" name="trAddCustomerDetails" style="display:none">
					<td nowrap align="left">'.$locate->Translate("customer_phone").'</td>
					<td align="left"><input type="text" id="customerPhone" name="customerPhone" size="35" maxlength="50">-<input type="text" id="customerPhone_ext" name="customerPhone_ext" size="8" maxlength="8"></td>
				</tr>
				<tr name="trAddCustomerDetails" id="trAddCustomerDetails" style="display:none">
					<td nowrap align="left">'.$locate->Translate("mobile").'</td>
					<td align="left"><input type="text" id="mainMobile" name="mainMobile" size="35"></td>
				</tr>
				<tr name="trAddCustomerDetails" id="trAddCustomerDetails" style="display:none">
					<td nowrap align="left">'.$locate->Translate("email").'</td>
					<td align="left"><input type="text" id="mainEmail" name="mainEmail" size="35"></td>
				</tr>				
				<tr id="trAddCustomerDetails" name="trAddCustomerDetails" style="display:none">
					<td nowrap align="left">'.$locate->Translate("website").'</td>
					<td align="left"><input type="text" id="website" name="website" size="35" maxlength="100" value="http://"><br><input type="button" value="'.$locate->Translate("browser").'" onclick="openWindow(xajax.$(\'website\').value);return false;"></td>
				</tr>
				<!--<tr id="trAddCustomerDetails" name="trAddCustomerDetails" style="display:none">
					<td nowrap align="left">'.$locate->Translate("zipcode").'</td>
					<td align="left"><input type="text" id="zipcode" name="zipcode" size="10" maxlength="10"></td>
				</tr>-->
				<tr name="trAddCustomerDetails" id="trAddCustomerDetails" style="display:none">
					<td nowrap align="left">'.$locate->Translate("fax").'</td>
					<td align="left"><input type="text" id="mainFax" name="mainFax" size="35">-<input type="text" id="mainFax_ext" name="mainFax_ext" size="8" maxlength="8"></td>
				</tr>
				<tr id="trAddCustomerDetails" name="trAddCustomerDetails" style="display:none">
					<td nowrap align="left" style="border-bottom:1px double orange;">'.$locate->Translate("category").'</td>
					<td align="left" style="border-bottom:1px double orange"><input type="text" id="category" name="category" size="35"></td>
				</tr>';
				/*
				*  control bank data
				*/
				$html .='
					
						<input type="hidden" id="hidAddBankDetails" name="hidAddBankDetails" value="OFF">
					<!--********************-->
					
					<tr id="trAddBankDetails" name="trAddBankDetails" style="display:none">
						<td nowrap align="left">'.$locate->Translate("bank_account_name").'</td>
						<td align="left"><input type="text" id="bankaccountname" name="bankaccountname" size="35"></td>
					</tr>
					<tr id="trAddBankDetails" name="trAddBankDetails" style="display:none">
					<td nowrap align="left" style="border-top:1px double orange;">'.$locate->Translate("bank_name").'</td>
					<td align="left" style="border-top:1px double orange"><input type="text" id="bankname" name="bankname" size="35"></td>
					</tr>
					<tr id="trAddBankDetails" name="trAddBankDetails" style="display:none">
						<td nowrap align="left">'.$locate->Translate("bank_zip").'</td>
						<td align="left"><input type="text" id="bankzip" name="bankzip" size="35"></td>
					</tr>
					<tr id="trAddBankDetails" name="trAddBankDetails" style="display:none">
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
					<td align="left"><input type="text" id="customer" name="customer" value="'. $customer['customer'].'" onkeyup="ajax_showOptions(this,\'getCustomersByLetters\',event)" size="35" maxlength="50" autocomplete="off" readOnly><BR /><input type="button" value="'.$locate->Translate("cancel").'" id="btnConfirmCustomer" name="btnConfirmCustomer" onclick="btnConfirmCustomerOnClick();"><input type="hidden" id="customerid" name="customerid" value="'. $customerid .'"></td>
				</tr>
				';
	}
	if($config['system']['enable_contact'] != '0'){ //控制contact模块的显示与隐藏
		if ($contactid == null){
				$html .='
					<tr>
						<td nowrap align="left">'.$locate->Translate("contact").'</td>
						<td align="left"><input type="text" id="contact" name="contact" value="" onkeyup="ajax_showOptions(this,\'customerid='.$customerid.'&getContactsByLetters\',event)" size="35" maxlength="50" autocomplete="off"><BR /><input id="btnConfirmContact" name="btnConfirmContact" type="button" onclick="btnConfirmContactOnClick();return false;" value="'.$locate->Translate("confirm").'"><input type="hidden" id="contactid" name="contactid" value="">
						<input type="hidden" id="contactDetail" name="contactDetail" value="OFF">
						[<a href=? onclick="
							if (xajax.$(\'contactDetail\').value == \'OFF\'){
								xajax.$(\'genderTR\').style.display = \'\';
								xajax.$(\'positionTR\').style.display = \'\';
								xajax.$(\'phoneTR\').style.display = \'\';
								xajax.$(\'phone1TR\').style.display = \'\';
								xajax.$(\'phone2TR\').style.display = \'\';
								xajax.$(\'mobileTR\').style.display = \'\';
								xajax.$(\'faxTR\').style.display = \'\';
								xajax.$(\'emailTR\').style.display = \'\';
								xajax.$(\'contactDetail\').value = \'ON\';
							}else{
								xajax.$(\'genderTR\').style.display = \'none\';
								xajax.$(\'positionTR\').style.display = \'none\';
								xajax.$(\'phoneTR\').style.display = \'none\';
								xajax.$(\'phone1TR\').style.display = \'none\';
								xajax.$(\'phone2TR\').style.display = \'none\';
								xajax.$(\'mobileTR\').style.display = \'none\';
								xajax.$(\'faxTR\').style.display = \'none\';
								xajax.$(\'emailTR\').style.display = \'none\';
								xajax.$(\'contactDetail\').value = \'OFF\';
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
						<td align="left"><input type="text" id="phone" name="phone" size="35" value="'. $callerid .'">-<input type="text" id="ext" name="ext" size="8" maxlength="8" value=""></td>
					</tr>
					<tr name="phone1TR" id="phone1TR" style="display:none">
						<td nowrap align="left">'.$locate->Translate("phone1").'</td>
						<td align="left"><input type="text" id="phone1" name="phone1" size="35" value="">-<input type="text" id="ext1" name="ext1" size="8" maxlength="8" value=""></td>
					</tr>
					<tr name="phone2TR" id="phone2TR" style="display:none">
						<td nowrap align="left">'.$locate->Translate("phone2").'</td>
						<td align="left"><input type="text" id="phone2" name="phone2" size="35" value="">-<input type="text" id="ext2" name="ext2" size="8" maxlength="8" value=""></td>
					</tr>
					<tr name="mobileTR" id="mobileTR" style="display:none">
						<td nowrap align="left">'.$locate->Translate("mobile").'</td>
						<td align="left"><input type="text" id="mobile" name="mobile" size="35"></td>
					</tr>
					<tr name="faxTR" id="faxTR" style="display:none">
						<td nowrap align="left">'.$locate->Translate("fax").'</td>
						<td align="left"><input type="text" id="fax" name="fax" size="35">-<input type="text" id="fax_ext" name="fax_ext" size="8" maxlength="8" value=""></td>
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
		
		$query = "SELECT * FROM $table "
				." WHERE id = $id";
		astercrm::events($query);
		$row =& $db->getRow($query);
		return $row;
	}

	function getRecordByField($field,$value,$table){
		global $db;
		$value = preg_replace("/'/","\\'",$value);
		if (is_numeric($value)){
			$query = "SELECT * FROM $table WHERE $field = $value ";
		}else{
			$query = "SELECT * FROM $table WHERE $field = '$value' ";
		}
		if($table == 'diallist') $query .= " ORDER BY id ASC ";
		$query .= " LIMIT 0,1 ";
		astercrm::events($query);
		$row =& $db->getRow($query);
		return $row;
	}

	function getRecordsByField($field,$value,$table){
		global $db;
		$value = preg_replace("/'/","\\'",$value);
		if (is_numeric($value)){
			$query = "SELECT * FROM $table WHERE $field = $value ";
		}else{
			$query = "SELECT * FROM $table WHERE $field = '$value' ";
		}
		if($table == 'diallist') $query .= " ORDER BY id ASC ";
		astercrm::events($query);
		$row =& $db->query($query);
		return $row;
	}

	function getCountByField($field = '',$value = '',$table){
		global $db;
		$value = preg_replace("/'/","\\'",$value);
		if (is_numeric($value)){
			$query = "SELECT count(*) FROM $table WHERE $field = $value";
		}else{
			if ($field != '' || $value != '')
				$query = "SELECT count(*) FROM $table WHERE $field = '$value'";
			else
				$query = "SELECT count(*) FROM $table ";
		}
		astercrm::events($query);
		$row =& $db->getOne($query);
		return $row;
	}

	function getOptions($surveyid){

		global $db;
		
		$query= "SELECT * FROM surveyoptions "
				." WHERE "
				."surveyid = " . $surveyid 
				." ORDER BY cretime ASC";

		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}

	function insertNewSurveyResult($surveyid,$surveyoption,$surveynote,$customerID,$contactID){
		global $db;
		
		$query= "INSERT INTO surveyresult SET "
				."surveyid='".$surveyid."', "
				."surveyoption='".$surveyoption."', "
				."surveynote='".$surveynote."', "
				."customerid='".$customerID."', "
				."contactid='".$contactID."', "
				."cretime=now(), "
				."creby='".$_SESSION['curuser']['username']."'";
		astercrm::events($query);
		$res =& $db->query($query);
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

		$query = "SELECT * FROM survey WHERE enable=1 AND groupid = ".$_SESSION['curuser']['groupid']." ORDER BY cretime DESC LIMIT 0,1";
		astercrm::events($query);
		$res =& $db->getRow($query);
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
		global $locate; global $db;
		if ($type == 'note'){
			$note =& astercrm::getRecordById($id,'note');
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
						<td align="left"><input type="text" id="customer" name="customer" size="35" maxlength="100" value="' . $customer['customer'] . '">
						<input type="hidden" id="customerid"  name="customerid" value="'.$customer['id'].'"><BR />
						<input type="hidden" id="hidEditCustomerDetails" name="hidEditCustomerDetails" value="ON">
						<input type="hidden" id="hidEditBankDetails" name="hidEditBankDetails" value="ON">
					[<a href=? onclick="
						if (xajax.$(\'hidEditCustomerDetails\').value == \'OFF\'){
							showObj(\'trEditCustomerDetails\');
							xajax.$(\'hidEditCustomerDetails\').value = \'ON\';
						}else{
							hideObj(\'trEditCustomerDetails\');
							xajax.$(\'hidEditCustomerDetails\').value = \'OFF\';
						};
						return false;">
						'.$locate->Translate("detail").'
					</a>] &nbsp; [<a href=? onclick="
							if (xajax.$(\'hidEditBankDetails\').value == \'OFF\'){
								showObj(\'trEditBankDetails\');
								xajax.$(\'hidEditBankDetails\').value = \'ON\';
							}else{
								hideObj(\'trEditBankDetails\');
								xajax.$(\'hidEditBankDetails\').value = \'OFF\';
							}
							return false;">'.$locate->Translate("bank").'</a>]
						</td>
					</tr>
					<tr id="trEditCustomerDetails" name="trEditCustomerDetails">
						<td nowrap align="left">'.$locate->Translate("customer_contact").'</td>
						<td align="left"><input type="text" id="customerContact" name="customerContact" size="35" maxlength="35" value="' . $customer['contact'] . '"><BR />

						<select id="customerContactGender" name="customerContactGender">
							<option value="male" '.$customerMaleSelected.'>'.$locate->Translate("male").'</option>
							<option value="female" '.$customerFemaleSelected.'>'.$locate->Translate("female").'</option>
							<option value="unknown" '.$customerUnknownSelected.'>'.$locate->Translate("unknown").'</option>
						</select>
						
						</td>
					</tr>					
					<tr id="trEditCustomerDetails" name="trEditCustomerDetails">
						<td nowrap align="left">'.$locate->Translate("address").'</td>
						<td align="left"><input type="text" id="address" name="address" size="35" maxlength="200" value="' . $customer['address'] . '"></td>
					</tr>
					<tr id="trEditCustomerDetails" name="trEditCustomerDetails">
						<td nowrap align="left">'.$locate->Translate("zipcode").'/'.$locate->Translate("city").'</td>
						<td align="left"><input type="text" id="zipcode" name="zipcode" size="10" maxlength="10" value="' . $customer['zipcode'] . '">/<input type="text" id="city" name="city" size="17" maxlength="50" value="'.$customer['city'].'"></td>
					</tr>
					<tr id="trEditCustomerDetails" name="trEditCustomerDetails">
						<td nowrap align="left">'.$locate->Translate("state").'</td>
						<td align="left"><input type="text" id="state" name="state" size="35" maxlength="50" value="'.$customer['state'].'"></td>
					</tr>
					<tr id="trEditCustomerDetails" name="trEditCustomerDetails">
						<td nowrap align="left">'.$locate->Translate("country").'</td>
						<td align="left"><input type="text" id="country" name="country" size="35" maxlength="50" value="' . $customer['country'] . '"></td>
					</tr>
					<tr id="trEditCustomerDetails" name="trEditCustomerDetails">
						<td nowrap align="left">'.$locate->Translate("customer_phone").'</td>
						<td align="left"><input type="text" id="customerPhone" name="customerPhone" size="35" maxlength="50"  value="' . $customer['phone'] . '">-<input type="text" id="customerPhone_ext" name="customerPhone_ext" size="8" maxlength="8"  value="' . $customer['phone_ext'] . '"></td>
					</tr>
					<tr name="trEditCustomerDetails" id="trEditCustomerDetails">
						<td nowrap align="left">'.$locate->Translate("mobile").'</td>
						<td align="left"><input type="text" id="mainMobile" name="mainMobile" size="35" value="' . $customer['mobile'] . '"></td>
					</tr>
					<tr name="trEditCustomerDetails" id="trEditCustomerDetails">
						<td nowrap align="left">'.$locate->Translate("email").'</td>
						<td align="left"><input type="text" id="mainEmail" name="mainEmail" size="35" value="' . $customer['email'] . '"></td>
					</tr>				
					<tr id="trEditCustomerDetails" name="trEditCustomerDetails">
						<td nowrap align="left">'.$locate->Translate("website").'</td>
						<td align="left"><input type="text" id="website" name="website" size="35" maxlength="100" value="' . $customer['website'] . '"><BR /><input type="button" value="'.$locate->Translate("browser").'"  onclick="openWindow(xajax.$(\'website\').value);return false;"></td>
					</tr>
					<tr id="trEditCustomerDetails" name="trEditCustomerDetails">
						<td nowrap align="left">'.$locate->Translate("category").'</td>
						<td align="left"><input type="text" id="category" name="category" size="35"  value="' . $customer['category'] . '"></td>
					</tr>

					<tr name="trEditCustomerDetails" id="trEditCustomerDetails" >
						<td nowrap align="left">'.$locate->Translate("fax").'</td>
						<td align="left"><input type="text" id="mainFax" name="mainFax" size="35" value="' . $customer['fax'] . '"><input type="text" id="mainFax_ext" name="mainFax_ext" maxlength="8" size="8" value="' . $customer['fax_ext'] . '"></td>
					</tr>
					<!--*********************************************************-->
					<tr id="trEditBankDetails" name="trEditBankDetails">
						<td nowrap align="left">'.$locate->Translate("bank_name").'</td>
						<td align="left"><input type="text" id="bankname" name="bankname" size="35"  value="' . $customer['bankname'] . '"></td>
					</tr>
					<tr id="trEditBankDetails" name="trEditBankDetails">
						<td nowrap align="left">'.$locate->Translate("bank_zip").'</td>
						<td align="left"><input type="text" id="bankzip" name="bankzip" size="35"  value="' . $customer['bankzip'] . '"></td>
					</tr>
					<tr id="trEditBankDetails" name="trEditBankDetails">
						<td nowrap align="left">'.$locate->Translate("bank_account_name").'</td>
						<td align="left"><input type="text" id="bankaccountname" name="bankaccountname" size="35" value="' . $customer['bankaccountname'] . '"></td>
					</tr>
					<tr id="trEditBankDetails" name="trEditBankDetails">
						<td nowrap align="left">'.$locate->Translate("bank_account").'</td>
						<td align="left"><input type="text" id="bankaccount" name="bankaccount" size="35"  value="' . $customer['bankaccount'] . '"></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><button  id="btnContinue" name="btnContinue"  onClick=\'xajax_update(xajax.getFormValues("frmCustomerEdit"),"customer");return false;\'>'.$locate->Translate("continue").'</button></td>
					</tr>
					';
		}elseif ($type == 'diallist'){
			$diallist =& astercrm::getRecordByField('id',$id,'diallist');
			if ($_SESSION['curuser']['usertype'] == 'admin'){
				$res = Customer::getGroups();
				$groupoptions .= '<select name="groupid" id="groupid" onchange="setCampaign();">';
				while ($row = $res->fetchRow()) {
						$groupoptions .= '<option value="'.$row['groupid'].'"';
						if($row['groupid'] == $diallist['groupid']) $groupoptions .='selected';
						$groupoptions .='>'.$row['groupname'].'</option>';
				}				
				$groupoptions .= '</select>';
				$sql = "SELECT * FROM campaign WHERE groupid ='".$diallist['groupid']."'";			
				$res = & $db->query($sql);

				$campaignoptions .= '<select name="campaignid" id="campaignid" >';
				while ($campaign = $res->fetchRow()) {
					$campaignoptions .= '<option value="'.$campaign['id'].'"';
					if($campaign['id'] == $diallist['campaignid']) $campaignoptions .='selected';
					$campaignoptions .='>'.$campaign['campaignname'].'</option>';
				}				
				$campaignoptions .= '</select>';
			}elseif($_SESSION['curuser']['usertype'] == 'groupadmin'){
				$groupoptions .= $_SESSION['curuser']['group']['groupname'].'<input id="groupid" name="groupid" type="hidden" value="'.$_SESSION['curuser']['groupid'].'">';			$res = Customer::getRecordsByField('groupid',$_SESSION['curuser']['groupid'],'astercrm_account');
				$assignoptions .= '<select name="assign" id="assign">';
				while ($row = $res->fetchRow()) {
						$assignoptions .= '<option value="'.$row['extension'].'"';
						$assignoptions .='>'.$row['extension'].'</option>';
				}				
				$assignoptions .= '</select>';
				
				$sql = "SELECT * FROM campaign WHERE groupid ='".$diallist['groupid']."'";			
				$res = & $db->query($sql);

				$campaignoptions .= '<select name="campaignid" id="campaignid" >';
				while ($campaign = $res->fetchRow()) {
					$campaignoptions .= '<option value="'.$campaign['id'].'"';
					if($campaign['id'] == $diallist['campaignid']) $campaignoptions .='selected';
					$campaignoptions .='>'.$campaign['campaignname'].'</option>';
				}				
				$campaignoptions .= '</select>';
			}else{
				$groupoptions .= $_SESSION['curuser']['group']['groupname'].'<input id="groupid" name="groupid" type="hidden" value="'.$_SESSION['curuser']['groupid'].'">';

				$assignoptions = '<input type="text" id="assign" name="assign" size="35" value="'.$diallist['assign'].'" disabled><input type="hidden" id="assign" name="assign" value="'.$diallist['assign'].'">';
				
				$sql = "SELECT * FROM campaign WHERE groupid ='".$diallist['groupid']."'";			
				$res = & $db->query($sql);

				$campaignoptions .= '<select name="campaignid" id="campaignid" >';
				while ($campaign = $res->fetchRow()) {
					$campaignoptions .= '<option value="'.$campaign['id'].'"';
					if($campaign['id'] == $diallist['campaignid']) $campaignoptions .='selected';
					$campaignoptions .='>'.$campaign['campaignname'].'</option>';
				}				
				$campaignoptions .= '</select>';
			}

			$html = '
				<!-- No edit the next line -->
				<form method="post" name="formeditDiallist" id="formeditDiallist">
				
				<table border="1" width="100%" class="adminlist">
					<tr>
						<td nowrap align="left">'.$locate->Translate("number").'</td>
						<td align="left">
							<input type="text" id="dialnumber" name="dialnumber" size="35" value="'.$diallist['dialnumber'].'" disabled><input type="hidden" id="dialnumber" name="dialnumber" value="'.$diallist['dialnumber'].'" >
							<input type="hidden" id="id"  name="id" value="'.$diallist['id'].'">
						</td>
					</tr>
					<tr>
						<td nowrap align="left">'.$locate->Translate("Assign To").'</td>
						<td align="left">
							'.$assignoptions.'
						</td>
					</tr>
					<tr>
						<td nowrap align="left">'.$locate->Translate("Dialtime").'</td>
						<td align="left">
							<input type="text" name="dialtime" size="20" value="'.$diallist['dialtime'].'">
			<INPUT onclick="displayCalendar(document.getElementById(\'dialtime\'),\'yyyy-mm-dd hh:ii\',this,true)" type="button" value="Cal">
						</td>
					</tr>';
			$html .= '
					<tr>
						<td align="left" width="25%">'.$locate->Translate("Group Name").'</td>
						<td>'.$groupoptions.'</td>
					</tr>';
			$html .= '
					<tr>
						<td align="left" width="25%">'.$locate->Translate("Campaign Name").'</td>
						<td>'.$campaignoptions.'</td>
					</tr>';
			$html .= '
					<tr>
						<td nowrap colspan=2 align=right><input type="button" id="btnAddDiallist" name="btnAddDiallist" value="'.$locate->Translate("continue").'" onclick="xajax_saveDiallist(xajax.getFormValues(\'formeditDiallist\'));return false;"></td>
					</tr>
				<table>
				</form>
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
						<td align="left"><input type="text" id="phone" name="phone" size="35"  value="'.$contact['phone'].'">-<input type="text" id="ext" name="ext" size="8" maxlength="8"  value="'.$contact['ext'].'"></td>
					</tr>
					<tr name="phone1TR" id="phone1TR">
						<td nowrap align="left">'.$locate->Translate("phone1").'</td>
						<td align="left"><input type="text" id="phone1" name="phone1" size="35"  value="'.$contact['phone1'].'">-<input type="text" id="ext1" name="ext1" size="8" maxlength="8"  value="'.$contact['ext1'].'"></td>
					</tr>
					<tr name="phone2TR" id="phone2TR">
						<td nowrap align="left">'.$locate->Translate("phone2").'</td>
						<td align="left"><input type="text" id="phone2" name="phone2" size="35"  value="'.$contact['phone2'].'">-<input type="text" id="ext2" name="ext2" size="8" maxlength="8"  value="'.$contact['ext2'].'"></td>
					</tr>
					<tr name="mobileTR" id="mobileTR">
						<td nowrap align="left">'.$locate->Translate("mobile").'</td>
						<td align="left"><input type="text" id="mobile" name="mobile" size="35" value="'.$contact['mobile'].'"></td>
					</tr>
					<tr name="faxTR" id="faxTR">
						<td nowrap align="left">'.$locate->Translate("fax").'</td>
						<td align="left"><input type="text" id="fax" name="fax" size="35" value="'.$contact['fax'].'">-<input type="text" id="fax_ext" name="fax_ext" size="8" maxlength="8" value="'.$contact['fax_ext'].'"></td>
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
					<td nowrap align="left" width="160">'.$locate->Translate("customer_name").'&nbsp;[<a href=? onclick="xajax_showNote(\''.$customer['id'].'\',\'customer\');return false;">'.$locate->Translate("note").'</a>]</td>
					<td align="left">'.$customer['customer'].'&nbsp;[<a href=? onclick="xajax_edit(\''.$customer['id'].'\',\'customer\');return false;">'.$locate->Translate("edit").'</a>]&nbsp; [<a href=? onclick="
							if (xajax.$(\'hidCustomerBankDetails\').value == \'OFF\'){
								showObj(\'trCustomerBankDetails\');
								xajax.$(\'hidCustomerBankDetails\').value = \'ON\';
							}else{
								hideObj(\'trCustomerBankDetails\');
								xajax.$(\'hidCustomerBankDetails\').value = \'OFF\';
							}
							return false;">'.$locate->Translate("bank").'</a>]<input type="hidden" value="OFF" name="hidCustomerBankDetails" id="hidCustomerBankDetails">&nbsp;[<a href=? onclick="xajax_showCdr(\''.$customer['id'].'\',\'out\');return false;">'.$locate->Translate("outbound").'</a>]&nbsp;[<a href=? onclick="xajax_showCdr(\''.$customer['id'].'\',\'in\');return false;">'.$locate->Translate("inbound").'</a>]&nbsp;[<a href=? onclick="xajax_showDiallist(\''.$_SESSION['curuser']['extension'].'\',\''.$customer['id'].'\');return false;">'.$locate->Translate("diallist").'</a>]&nbsp;[<a href=? onclick="xajax_showRecords(\''.$customer['id'].'\');return false;">'.$locate->Translate("monitors").'</a>]</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("city").'/'.$locate->Translate("state").'/'.$locate->Translate("country").'['.$locate->Translate("zipcode").']'.'</td>
					<td align="left">'.$customer['city'].'/'.$customer['state'].'/'.$customer['country'].'['.$customer['zipcode'].']'.'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("address").
						' | <a href="?" onclick="showMap(\''.$customer['city'].' '.$customer['state'].
						' '.$customer['zipcode'].' '.$customer['address'].'\');return false;">Map</a>'.
					'</td>
					<td align="left">'.$customer['address'].'</td>
				</tr>
				<!--**********************-->
				<tr>
					<td nowrap align="left">'.$locate->Translate("mobile").'</td>
					<td align="left"><a href=? onclick="dial(\''.$customer['mobile'].'\',\'\',xajax.getFormValues(\'myForm\'));return false;">'.$customer['mobile'].'</a></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("fax").'</td>
					<td align="left"><a href=? onclick="dial(\''.$customer['fax'].'\',\'\',xajax.getFormValues(\'myForm\'));return false;">'.$customer['fax'].'</a>-<a href=? onclick="dial(\''.$customer['fax'].'\',\'\',xajax.getFormValues(\'myForm\')\''.$customer['fax_ext'].'\');return false;">'.$customer['fax_ext'].'</a></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("email").'</td>
					<td align="left">'.$customer['email'].'</td>
				</tr>	
				<!--**********************-->
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
					<td align="left"><a href=? onclick="dial(\''.$customer['phone'].'\',\'\',xajax.getFormValues(\'myForm\'));return false;">'.$customer['phone'].'</a>-<a href=? onclick="dial(\''.$customer['phone'].'\',\'\',xajax.getFormValues(\'myForm\'),\''.$customer['phone_ext'].'\');return false;">'.$customer['phone_ext'].'</a></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("category").'</td>
					<td align="left">'.$customer['category'].'</td>
				</tr>
				<tr id="trCustomerBankDetails" name="trCustomerBankDetails" style="display:none">
					<td nowrap align="left">'.$locate->Translate("bank_name").'</td>
					<td align="left">'.$customer['bankname'].'</td>
				</tr>
				<tr id="trCustomerBankDetails" name="trCustomerBankDetails" style="display:none">
					<td nowrap align="left">'.$locate->Translate("bank_zip").'</td>
					<td align="left">'.$customer['bankzip'].'</td>
				</tr>
				<tr id="trCustomerBankDetails" name="trCustomerBankDetails" style="display:none">
					<td nowrap align="left">'.$locate->Translate("bank_account_name").'</td>
					<td align="left">'.$customer['bankaccountname'].'</td>
				</tr>
				<tr id="trCustomerBankDetails" name="trCustomerBankDetails" style="display:none">
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

	function getRecordsByGroupid($groupid = null, $table){
		global $db;

		if ($groupid == null){
			$query = "SELECT * FROM $table ORDER BY id" ;
		}else{
			$query = "SELECT * FROM $table WHERE groupid = $groupid ORDER BY id";
		}
		$row =& $db->query($query);
		return $row;
	}

	function getDialNumber($campaignid = ''){
		global $db;
		$query = "SELECT diallist.*,campaign.incontext, campaign.inexten, campaign.outcontext, campaign.queuename FROM diallist LEFT JOIN campaign ON campaign.id = diallist.campaignid WHERE diallist.campaignid = $campaignid ";
		$query .=  " ORDER BY diallist.id DESC	LIMIT 0,1";

		$row =& $db->getRow($query);

		return $row;
	}

	function getCustomerphoneSqlByid($customerid,$feild,$type = '',$feild1=''){

		$res_customer =astercrm::getRecordById($customerid,'customer');
		$res_contact =astercrm::getContactListByID($customerid);

		if($feild1 == ''){
			$sql = '';
			if ($res_customer['phone'] != '') $sql .= " $type $feild='".$res_customer['phone']."' ";
			if ($res_customer['mobile'] != '') $sql .= " $type $feild='".$res_customer['mobile']."' ";
			while ($res_contact->fetchInto($row)) {
				if ($row['phone'] != '') $sql .= " $type $feild='".$row['phone']."' ";
				if ($row['phone1'] != '') $sql .= " $type $feild='".$row['phone1']."' ";
				if ($row['phone2'] != '') $sql .= " $type $feild='".$row['phone2']."' ";
				if ($row['mobile'] != '') $sql .= " $type $feild='".$row['mobile']."' ";
			}
			if($sql != '') $sql = ltrim($sql,"\ ".$type);
		}else{
			$sql = '';
			if ($res_customer['phone'] != '') $sql .= " $type $feild='".$res_customer['phone']."' $type $feild1='".$res_customer['phone']."' ";
			if ($res_customer['mobile'] != '') $sql .= " $type $feild='".$res_customer['mobile']."' $type $feild1='".$res_customer['mobile']."' ";
			while ($res_contact->fetchInto($row)) {
				if ($row['phone'] != '') $sql .= " $type $feild='".$row['phone']."' $type $feild1='".$row['phone']."' ";
				if ($row['phone1'] != '') $sql .= " $type $feild='".$row['phone1']."' $type $feild1='".$row['phone1']."' ";
				if ($row['phone2'] != '') $sql .= " $type $feild='".$row['phone2']."' $type $feild1='".$row['phone2']."' ";
				if ($row['mobile'] != '') $sql .= " $type $feild='".$row['mobile']."' ";
			}
			if($sql != '') $sql = ltrim($sql,"\ ".$type);
		}
		return $sql;
	}

	function getGroupCurcdr() {
		global $db;
		foreach ($_SESSION['curuser']['memberExtens'] as $value){
			$memberextena .= "'".$value."',";
			$memberextenb .= "'LOCAL/".$value."',";
			$memberextenc .= "'SIP/".$value."',";
			$memberextend .= "'IAX/".$value."',";
		}
		foreach ($_SESSION['curuser']['memberAgents'] as $value){
			$memberagents .= "'AGENT/".$value."',";				
		}
		$memberextens = rtrim($memberextena.$memberextenb.$memberextenc.$memberextend,',');
		$memberagents = rtrim($memberagents,',');
		$query = "SELECT * FROM curcdr WHERE src in ($memberextens) OR dst in ($memberextens) OR dstchan in ($memberagents)";
		astercrm::events($query);
		$row =& $db->query($query);
		return $row;		
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
		$query = "DELETE FROM $table WHERE id = $id";
		astercrm::events($query);
		$res =& $db->query($query);

		return $res;
	}

	/**
	*  delete records form a table
	*
	*	@param  $field			(string)
	*	@param  $value			(string)
	*	@param  $table			(string)	table name
	*	@return $res		(object)	object
	*/
	
	function deleteRecords($field,$value,$table){
		global $db;
		
		//backup all datas

		//delete all note
		$query = "DELETE FROM $table WHERE $field = '$value'";
		astercrm::events($query);
		$res =& $db->query($query);

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
						<td align="left"><a href=? onclick="dial(\''.$contact['phone'].'\',\'\',xajax.getFormValues(\'myForm\'));return false;">'.$contact['phone'].'</a></td>
					</tr>';
		else
			$html .='
					<tr>
						<td nowrap align="left">'.$locate->Translate("phone").'</td>
						<td align="left"><a href=? onclick="dial(\''.$contact['phone'].'\',\'\',xajax.getFormValues(\'myForm\'));return false;">'.$contact['phone'].'</a> ext: '.$contact['ext'].'</td>
					</tr>';

		if ($contact['phone1'] != '' || $contact['ext1'] != '')
			if ($contact['ext1'] == '')
				$html .='
						<tr>
							<td nowrap align="left">'.$locate->Translate("phone1").'</td>
							<td align="left"><a href="?" onclick="dial(\''.$contact['phone1'].'\',\'\',xajax.getFormValues(\'myForm\'));return false;">'.$contact['phone1'].'</a></td>
						</tr>';
			else
				$html .='
						<tr>
							<td nowrap align="left">'.$locate->Translate("phone1").'</td>
							<td align="left"><a href="?" onclick="dial(\''.$contact['phone1'].'\',\'\',xajax.getFormValues(\'myForm\'));return false;">'.$contact['phone1'].'</a> ext: '.$contact['ext1'].'</td>
						</tr>';
		
		if ($contact['phone2'] != '' || $contact['ext2'] != '')
			if ($contact['ext2'] == '')
				$html .='
						<tr>
							<td nowrap align="left">'.$locate->Translate("phone2").'</td>
							<td align="left"><a href="?" onclick="dial(\''.$contact['phone2'].'\',\'\',xajax.getFormValues(\'myForm\'));return false;">'.$contact['phone2'].'</a></td>
						</tr>';
			else
				$html .='
						<tr>
							<td nowrap align="left">'.$locate->Translate("phone2").'</td>
							<td align="left"><a href="?" onclick="dial(\''.$contact['phone2'].'\',\'\',xajax.getFormValues(\'myForm\'));return false;">'.$contact['phone2'].'</a> ext: '.$contact['ext2'].'</td>
						</tr>';

		$html .='
				<tr>
					<td nowrap align="left">'.$locate->Translate("mobile").'</td>
					<td align="left"><a href="?" onclick="dial(\''.$contact['mobile'].'\',\'\',xajax.getFormValues(\'myForm\'));return false;">'.$contact['mobile'].'</a></td>
				</tr>';
			if ($contact['fax'] != '' || $contact['fax_ext'] != ''){
				if ($contact['fax_ext'] != ''){
					$html .='
					<tr>
						<td nowrap align="left">'.$locate->Translate("fax").'</td>
						<td align="left">'.$contact['fax'].' ext: '.$contact['fax_ext'].'</td>
					</tr>';
				}else{
					$html .='
					<tr>
						<td nowrap align="left">'.$locate->Translate("fax").'</td>
						<td align="left">'.$contact['fax'].'</td>
					</tr>';
				}
			}
		$html .='
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
			$query = 'SELECT * FROM customer';
		elseif ($type == 'contact')
			$query = 'SELECT contact.*,customer.customer FROM contact LEFT JOIN customer ON customer.id = contact.customerid';
		else
			$query = 'SELECT contact.contact,customer.customer,note.* FROM note LEFT JOIN customer ON customer.id = note.customerid LEFT JOIN contact ON contact.id = note.contactid';

		astercrm::events($query);
		$res =& $db->query($query);
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

	function exportDataToCSV($query){
		global $db;
		astercrm::events($query);
		$res =& $db->query($query);		
		$first = 'yes';
		while ($res->fetchInto($row)) {
			$first_line = '';
			foreach ($row as $key => $val){
				if($first == 'yes'){
					$first_line .= '"'.$key.'"'.',';
				}
				if ($val != mb_convert_encoding($val,"UTF-8","UTF-8"))
						$val='"'.mb_convert_encoding($val,"UTF-8","GB2312").'"';
				
				$txtstr .= '"'.$val.'"'.',';
			}
			if($first_line != ''){
				$first_line .= "\n";
				$txtstr = $first_line.$txtstr;
				$first = 'no';
			}			
			$txtstr .= "\n";
		}
		return $txtstr;
	}

	/**
	*  create a 'where string' with 'like,<,>,=' assign by stype 
	*
	*	@param $stype		(array)		assign search type
	*	@param $filter		(array) 	filter in sql
	*	@param $content		(array)		content in sql
	*	@return $joinstr	(string)	sql where string
	*/
	function createSqlWithStype($filter,$content,$stype){

		$i=0;
		$joinstr='';
		foreach($stype as $type){
			$content[$i] = preg_replace("/'/","\\'",$content[$i]);
			if($filter[$i] != '' && trim($content[$i]) != ''){
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
	*  return customerid if match a phonenumber
	*
	*	@param $type		(string)		data to be exported
	*	@return $txtstr		(string) 		csv format datas
	*/

	function getCustomerByCallerid($callerid,$groupid = ''){
		global $db;
		$callerid = preg_replace("/'/","\\'",$callerid);
		$query = "SELECT id FROM customer WHERE phone LIKE '%$callerid' OR mobile LIKE '%$callerid' ";
		astercrm::events($query);
		$customerid =& $db->getOne($query);
		return $customerid;
	}

	function getContactByCallerid($callerid,$groupid = ''){
		global $db;
		$callerid = preg_replace("/'/","\\'",$callerid);
		if ($groupid == '')
			$query = "SELECT id,customerid FROM contact WHERE phone LIKE '%$callerid' OR phone1 LIKE '%$callerid' OR phone2 LIKE '%$callerid' OR mobile LIKE '%$callerid' LIMIT 0,1";
		else
			$query = "SELECT id,customerid FROM contact WHERE phone LIKE '%$callerid' OR phone1 LIKE '%$callerid' OR phone2 LIKE '%$callerid' OR mobile LIKE '%$callerid' AND groupid=$groupid LIMIT 0,1";
		astercrm::events($query);
		$row =& $db->getRow($query);
		return $row;
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
			$query = 'SELECT * FROM '.$table.' WHERE '.$joinstr;
		}else {
			$query = 'SELECT * FROM '.$table.'';
		}
		//if ($query != mb_convert_encoding($query,"UTF-8","UTF-8")){
		//	$query='"'.mb_convert_encoding($query,"UTF-8","GB2312").'"';
		//}		
		return $query;
	}

	function addNewRemind($f){ //增加提醒
		global $db;
		$f = astercrm::variableFiler($f);
		$remindtime = $f['remindtime'];
		$touser = trim($f['touser']);
		//if($touser == ''){
		$touser = $_SESSION['curuser']['username'];
		//}
		$query= "INSERT INTO remind SET "
				."title='".$f['remindtitle']."', "
				."content='".$f['content']."', "
				."remindtime='".$remindtime."',"   //提醒时间
				."remindtype='".$f['remindtype']."',"	//提醒类别
				."priority='".$f['priority']."',"       //紧急程度
				."username='".$f['username']."', "	// 归属人
				."remindabout='".$f['remindabout']."', "	// 相关内容
				."readed=0, "	// added 2007/11/12 by solo
				."touser='".$touser."', "	// added 2007/11/12 by solo
				."creby='".$_SESSION['curuser']['username']."', "
				."cretime=now() ";

		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}

	function updateRemind($f){  //修改提醒
		global $db;
		$f = astercrm::variableFiler($f);
		$remindtime = $f['remindtime'];
		$touser = trim($f['touser']);
		//if($touser == ''){
		$touser = $_SESSION['curuser']['username'];
		//}
		$query= "UPDATE remind SET "
				."title='".$f['remindtitle']."', "
				."content='".$f['content']."', "
				."remindtime='".$remindtime."',"   //提醒时间
				."remindtype='".$f['remindtype']."',"	//提醒类别
				."priority='".$f['priority']."',"       //紧急程度
				."username='".$f['username']."', "	// 归属人
				."remindabout='".$f['remindabout']."', "	// 相关内容
				."touser='".$touser."'  "	// added 2007/11/12 by solo
				."WHERE id='".$f['id']."'";

		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}

	function db2html($string){
		return str_replace(chr(13),'<br>',$string);
	}

	function createCdrGrid($customerid='',$cdrtype='',$start = 0, $limit = 1, $filter = null, $content = null, $stype = null, $order = null, $divName = "formCdr", $ordering = ""){
		global $locate;
		$_SESSION['ordering'] = $ordering;
		if($filter == null || $content == null || (!is_array($content) && $content == 'Array') || (!is_array(filter) && $filter == 'Array')){
			$content = null;
			$filter = null;
			$numRows =& astercrm::getCdrNumRows($customerid,$cdrtype);
			$arreglo =& astercrm::getAllCdrRecords($customerid,$cdrtype,$start,$limit,$order);
		}else{
			foreach($content as $value){
				if(trim($value) != ""){  //搜索内容有值
					$flag = "1";
					break;
				}
			}
			foreach($filter as $value){
				if(trim($value) != ""){  //搜索条件有值
					$flag2 = "1";
					break;
				}
			}
			foreach($stype as $value){
				if(trim($value) != ""){  //搜索方式有值
					$flag3 = "1";
					break;
				}
			}
			if($flag != "1" || $flag2 != "1" ){  //无值	
				$order = null;
				$numRows =& astercrm::getCdrNumRows($customerid,$cdrtype);
				$arreglo =& astercrm::getAllCdrRecords($customerid,$cdrtype,$start,$limit,$order);
			}elseif($flag3 != 1 ){  //未选择搜索方式
				$order = "calldate";
				$numRows =& astercrm::getCdrNumRowsMore($customerid,$cdrtype,$filter, $content);
				$arreglo =& astercrm::getCdrRecordsFilteredMore($customerid,$cdrtype,$start, $limit, $filter, $content, $order);
			}else{
				$order = "calldate";
				$numRows =& astercrm::getCdrNumRowsMorewithstype($customerid,$cdrtype,$filter, $content,$stype);
				$arreglo =& astercrm::getCdrRecordsFilteredMorewithstype($customerid,$cdrtype,$start, $limit, $filter, $content, $stype,$order);
			}
		}	
		// Databse Table: fields
		if($cdrtype=='recent'){
			$fields = array();
			$fields[] = 'calldate';
			$fields[] = 'src';
			$fields[] = 'dst';			
			$fields[] = 'didnumber';
			$fields[] = 'dstchannel';
			$fields[] = 'duration';
			$fields[] = 'billsec';
			$fields[] = 'record';

			// HTML table: Headers showed
			$headers = array();
			$headers[] = $locate->Translate("Calldate").'<br>';
			$headers[] = $locate->Translate("Src").'<br>';
			$headers[] = $locate->Translate("Dst").'<br>';
			$headers[] = $locate->Translate("Callee Id").'<br>';
			$headers[] = $locate->Translate("Agent").'<br>';
			$headers[] = $locate->Translate("Duration").'<br>';
			$headers[] = $locate->Translate("Billsec").'<br>';
			$headers[] = $locate->Translate("record").'<br>';

			// HTML table: hearders attributes
			$attribsHeader = array();
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';

			// HTML Table: columns attributes
			$attribsCols = array();
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';

			// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
			$eventHeader = array();
			$eventHeader[]= 'onClick=\'xajax_showRecentCdr("","'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","calldate","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showRecentCdr("","'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","src","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showRecentCdr("","'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","dst","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showRecentCdr("","'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","didnumber","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showRecentCdr("","'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","dstchannel","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showRecentCdr("","'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","duration","'.$divName.'","ORDERING");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showRecentCdr("","'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","billsec","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showRecentCdr("","'.$cdrtype.'",0,'.$limit.',"'.$filter.'","id","billsec","'.$divName.'","ORDERING","'.$stype.'");return false;\'';			

			// Select Box: fields table.
			$fieldsFromSearch = array();
			$fieldsFromSearch[] = 'src';
			$fieldsFromSearch[] = 'calldate';
			$fieldsFromSearch[] = 'dst';
			$fieldsFromSearch[] = 'didnumber';
			$fieldsFromSearch[] = 'billsec';

			// Selecct Box: Labels showed on search select box.
			$fieldsFromSearchShowAs = array();
			$fieldsFromSearchShowAs[] = $locate->Translate("src");
			$fieldsFromSearchShowAs[] = $locate->Translate("calldate");
			$fieldsFromSearchShowAs[] = $locate->Translate("dst");
			$fieldsFromSearchShowAs[] = $locate->Translate("callee id");
			$fieldsFromSearchShowAs[] = $locate->Translate("billsec");

			// Create object whit 5 cols and all data arrays set before.
			$table = new ScrollTable(7,$start,$limit,$filter,$numRows,$content,$order,$customerid,$cdrtype);
			$table->setHeader('title',$headers,$attribsHeader,$eventHeader,$edit=false,$delete=false,$detail=false);
			$table->setAttribsCols($attribsCols);
			$table->addRowSearchMore("mycdr",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit,0,0,$typeFromSearch,$typeFromSearchShowAs,$stype);

			while ($arreglo->fetchInto($row)) {
			// Change here by the name of fields of its database table
				$rowc = array();
				$rowc[] = $row['monitorid'];
				$rowc[] = $row['calldate'];
				$rowc[] = $row['src'];
				$rowc[] = $row['dst'];
				$rowc[] = $row['didnumber'];
				if(strstr($row['dstchannel'],'AGENT')){
					$agent = split('/',$row['dstchannel']);
					$rowc[] = $agent['1'];
				}else{
					$rowc[]='';
				}
				$rowc[] = $row['duration'];
				$rowc[] = $row['billsec'];
				$rowc['filename'] = $row['filename'];
				$table->addRow("mycdr",$rowc,false,false,false,$divName,$fields);
			}
			$html = $table->render('static');
		}else{
			$fields = array();
			$fields[] = 'calldate';
			$fields[] = 'src';
			$fields[] = 'dst';
			$fields[] = 'didnumber';
			$fields[] = 'dstchannel';
			$fields[] = 'duration';
			$fields[] = 'billsec';
			$fields[] = 'disposition';
			$fields[] = 'credit';
			$fileds[] = 'destination';
			$fileds[] = 'memo';

			// HTML table: Headers showed
			$headers = array();
			$headers[] = $locate->Translate("Calldate").'<br>';
			$headers[] = $locate->Translate("Src").'<br>';
			$headers[] = $locate->Translate("Dst").'<br>';
			$headers[] = $locate->Translate("Callee Id").'<br>';
			$headers[] = $locate->Translate("Agent").'<br>';
			$headers[] = $locate->Translate("Duration").'<br>';
			$headers[] = $locate->Translate("Billsec").'<br>';
			$headers[] = $locate->Translate("Disposition").'<br>';
			$headers[] = $locate->Translate("credit").'<br>';
			$headers[] = $locate->Translate("destination").'<br>';
			$headers[] = $locate->Translate("memo").'<br>';

			// HTML table: hearders attributes
			$attribsHeader = array();
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';
			$attribsHeader[] = 'width=""';

			// HTML Table: columns attributes
			$attribsCols = array();
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';
			$attribsCols[] = 'style="text-align: left"';

			// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
			$eventHeader = array();
			$eventHeader[]= 'onClick=\'xajax_showCdr('.$customerid.',"'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","calldate","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showCdr('.$customerid.',"'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","src","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showCdr('.$customerid.',"'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","dst","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showCdr('.$customerid.',"'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","didnumber","'.$divName.'","ORDERING");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showCdr('.$customerid.',"'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","dstchannel","'.$divName.'","ORDERING");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showCdr('.$customerid.',"'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","duration","'.$divName.'","ORDERING");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showCdr('.$customerid.',"'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","billsec","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showCdr('.$customerid.',"'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","disposition","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showCdr('.$customerid.',"'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","credit","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showCdr('.$customerid.',"'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","destination","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
			$eventHeader[]= 'onClick=\'xajax_showCdr('.$customerid.',"'.$cdrtype.'",0,'.$limit.',"'.$filter.'","'.$content.'","memo","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
			
			// Select Box: type table.
			$typeFromSearch = array();
			$typeFromSearch[] = 'like';
			$typeFromSearch[] = 'equal';
			$typeFromSearch[] = 'more';
			$typeFromSearch[] = 'less';

			// Selecct Box: Labels showed on searchtype select box.
			$typeFromSearchShowAs = array();
			$typeFromSearchShowAs[] = 'like';
			$typeFromSearchShowAs[] = '=';
			$typeFromSearchShowAs[] = '>';
			$typeFromSearchShowAs[] = '<';

			// Select Box: fields table.
			$fieldsFromSearch = array();
			$fieldsFromSearch[] = 'src';
			$fieldsFromSearch[] = 'calldate';
			$fieldsFromSearch[] = 'dst';
			$fieldsFromSearch[] = 'didnumber';
			$fieldsFromSearch[] = 'billsec';
			$fieldsFromSearch[] = 'disposition';
			$fieldsFromSearch[] = 'credit';
			$fieldsFromSearch[] = 'destination';
			$fieldsFromSearch[] = 'memo';

			// Selecct Box: Labels showed on search select box.
			$fieldsFromSearchShowAs = array();
			$fieldsFromSearchShowAs[] = $locate->Translate("src");
			$fieldsFromSearchShowAs[] = $locate->Translate("calldate");
			$fieldsFromSearchShowAs[] = $locate->Translate("dst");
			$fieldsFromSearchShowAs[] = $locate->Translate("callee id");
			$fieldsFromSearchShowAs[] = $locate->Translate("billsec");
			$fieldsFromSearchShowAs[] = $locate->Translate("disposition");
			$fieldsFromSearchShowAs[] = $locate->Translate("credit");
			$fieldsFromSearchShowAs[] = $locate->Translate("destination");
			$fieldsFromSearchShowAs[] = $locate->Translate("memo");


			// Create object whit 5 cols and all data arrays set before.
			$table = new ScrollTable(9,$start,$limit,$filter,$numRows,$content,$order,$customerid,$cdrtype);
			$table->setHeader('title',$headers,$attribsHeader,$eventHeader,$edit=false,$delete=false,$detail=false);
			$table->setAttribsCols($attribsCols);
			$table->addRowSearchMore("mycdr",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit,0,0,$typeFromSearch,$typeFromSearchShowAs,$stype);

			while ($arreglo->fetchInto($row)) {
			// Change here by the name of fields of its database table
				$rowc = array();
				$rowc[] = $row['id'];
				$rowc[] = $row['calldate'];
				$rowc[] = $row['src'];
				$rowc[] = $row['dst'];
				$rowc[] = $row['didnumber'];
				if(strstr($row['dstchannel'],'AGENT')){
					$agent = split('/',$row['dstchannel']);
					$rowc[] = $agent['1'];
				}else{
					$rowc[]='';
				}
				$rowc[] = $row['duration'];
				$rowc[] = $row['billsec'];
				$rowc[] = $row['disposition'];
				$rowc[] = $row['credit'];
				$rowc[] = $row['destination'];
				$rowc[] = $row['memo'];
				$table->addRow("mycdr",$rowc,false,false,false,$divName,$fields);
			}
			$html = $table->render();
		}
		// End Editable Zone		
		return $html;
	}

	
	function &getAllCdrRecords($customerid='',$cdrtype='',$start, $limit, $order = null, $creby = null){
		global $db;
		if($cdrtype == 'recent'){
			if($_SESSION['curuser']['extension'] != ''){
				$sql = "SELECT mycdr.*,monitorrecord.filename as filename,monitorrecord.id as monitorid FROM mycdr LEFT JOIN monitorrecord ON mycdr.srcuid = monitorrecord.uniqueid or mycdr.dstuid = monitorrecord.uniqueid WHERE (mycdr.src = '".$_SESSION['curuser']['extension']."' OR mycdr.dst ='".$_SESSION['curuser']['extension']."' OR dstchannel = 'AGENT/".$_SESSION['curuser']['agent']."') AND mycdr.src != '' AND mycdr.dst != '' AND mycdr.src != '<unknown>' AND mycdr.dst != '<unknown>' AND dstchannel != ''";
				if($order == null || is_array($order)){
					$sql .= " ORDER by mycdr.calldate DESC LIMIT $start, $limit";//.$_SESSION['ordering'];
				}else{
					$sql .= " ORDER BY mycdr.".$order." ".$_SESSION['ordering']." LIMIT $start, $limit";
				}
				//echo $sql;exit;
				astercrm::events($sql);
				$res =& $db->query($sql);
				return $res;
			}else{
				$sql = "SELECT * FROM mycdr WHERE id = '0'";
				astercrm::events($sql);
				$res =& $db->query($sql);
				return $res;
			}
		}
		if($customerid != ''){
			if($cdrtype == 'out'){
				$sql = astercrm::getCustomerphoneSqlByid($customerid,'dst','OR');
				$sql = "(".$sql.") AND dstchannel != '' AND src != dst ";
			}else{
				$sql = astercrm::getCustomerphoneSqlByid($customerid,'src','OR');
				$sql = "(".$sql.") AND dstchannel != '' ";
			}
		}

		if($_SESSION['curuser']['usertype'] == 'admin' && $customerid == ''){
			$sql = "SELECT * FROM mycdr WHERE dstchannel != '' ";
		}elseif ($_SESSION['curuser']['usertype'] == 'groupadmin' && $customerid == ''){
			$group_str = '';
			foreach($_SESSION['curuser']['memberExtens'] as $value){
				$group_str .= "OR src = '".$value."' OR dst = '".$value."' ";
			}
			foreach($_SESSION['curuser']['memberAgents'] as $value){
				$group_str .= "OR dstchannel = 'AGNET/".$value."' ";
			}
			if($group_str != ''){
				$sql = "SELECT * FROM mycdr WHERE (".ltrim($group_str,"\ OR").") AND dstchannel != '' ";
			}else {
				$sql = "SELECT * FROM mycdr WHERE id = '0'";
			}
		}else{			
			if($sql != '' ) {
				$sql = "SELECT * FROM mycdr WHERE ".$sql;
			}else {
				$sql = "SELECT * FROM mycdr WHERE id = '0'";
			}
		}

		if($order == null || is_array($order)){
			$sql .= " ORDER by calldate DESC LIMIT $start, $limit";//.$_SESSION['ordering'];
		}else{
			$sql .= " ORDER BY ".$order." ".$_SESSION['ordering']." LIMIT $start, $limit";
		}
		//echo $sql;exit;
		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function &getCdrNumRows($customerid='',$cdrtype='',$filter = null, $content = null){
		global $db;
		if($cdrtype == 'recent'){
			if($_SESSION['curuser']['extension'] != ''){
				$sql = "SELECT COUNT(*) FROM mycdr WHERE (src = '".$_SESSION['curuser']['extension']."' OR dst ='".$_SESSION['curuser']['extension']."' OR dstchannel = 'AGENT/".$_SESSION['curuser']['agent']."') AND src != '' AND dst != '' AND src != '<unknown>' AND dst != '<unknown>' AND dstchannel != '' ";				
				astercrm::events($sql);
				$res =& $db->getOne($sql);
				return $res;
			}else{
				$sql = "SELECT COUNT(*) FROM mycdr WHERE id = '0'";
				astercrm::events($sql);
				$res =& $db->getOne($sql);
				return $res;
			}
		}
		if($customerid != ''){
			if ($cdrtype == 'out'){
				$sql = astercrm::getCustomerphoneSqlByid($customerid,'dst','OR');
				$sql = "(".$sql.") AND dstchannel != '' AND src != dst ";
			}else{
				$sql = astercrm::getCustomerphoneSqlByid($customerid,'src','OR');
				$sql = "(".$sql.") AND dstchannel != '' ";
			}
		}

		if($_SESSION['curuser']['usertype'] == 'admin' && $customerid == ''){
			$sql = "SELECT COUNT(*) FROM mycdr WHERE dstchannel != '' ";
		}elseif ($_SESSION['curuser']['usertype'] == 'groupadmin' && $customerid == ''){
			$group_str = '';
			foreach($_SESSION['curuser']['memberExtens'] as $value){
				$group_str .= "OR src = '".$value."' OR dst = '".$value."' ";
			}
			foreach($_SESSION['curuser']['memberAgents'] as $value){
				$group_str .= "OR dstchannel = 'AGNET/".$value."' ";
			}
			if($group_str != ''){
				$sql = "SELECT COUNT(*) FROM mycdr WHERE (".ltrim($group_str,"\ OR").") AND dstchannel != '' ";
			}else {
				return '0';
			}
		}else{
			if($sql != '' ) {
				$sql = "SELECT COUNT(*) FROM mycdr WHERE ".$sql;
			}else {
				return '0';
			}
		}
		
		astercrm::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}

	function &getCdrRecordsFilteredMore($customerid='',$cdrtype='',$start, $limit, $filter, $content, $order,$table = '', $ordering = ""){
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
		if($customerid != ''){
			if($cdrtype == 'out'){
				$sql = astercrm::getCustomerphoneSqlByid($customerid,'dst','OR');
				$sql = "(".$sql.") AND dstchannel != '' AND src != dst ";
			}else{
				$sql = astercrm::getCustomerphoneSqlByid($customerid,'src','OR');
				$sql = "(".$sql.") AND dstchannel != '' ";
			}
		}
		if($_SESSION['curuser']['usertype'] == 'admin' && $customerid == ''){
			$sql = "SELECT * FROM mycdr WHERE dstchannel != '' ";
		}elseif ($_SESSION['curuser']['usertype'] == 'groupadmin' && $customerid == ''){
			$group_str = '';
			foreach($_SESSION['curuser']['memberExtens'] as $value){
				$group_str .= "OR src = '".$value."' OR dst = '".$value."' ";
			}
			foreach($_SESSION['curuser']['memberAgents'] as $value){
				$group_str .= "OR dstchannel = 'AGNET/".$value."' ";
			}

			if($group_str != ''){
				$sql = "SELECT * FROM mycdr WHERE (".ltrim($group_str,"\ OR").") AND dstchannel != '' ";
			}else {
				$sql = "SELECT * FROM mycdr WHERE id = '0'";
			}
		}else{
			if($sql != '' ) {
				$sql = "SELECT * FROM mycdr WHERE (".$sql.")";
			}else {
				$sql = "SELECT * FROM mycdr WHERE id = '0'";
			}
		}

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr."  ";
		}

		$sql .= " ORDER BY ".$order
					." DESC LIMIT $start, $limit $ordering";

		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function &getCdrNumRowsMore($customerid='',$cdrtype='',$filter = null, $content = null,$table = ''){
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
		if($customerid != ''){
			if ($cdrtype == 'out'){
				$sql = astercrm::getCustomerphoneSqlByid($customerid,'dst','OR');
				$sql = "(".$sql.") AND dstchannel != '' AND src != dst ";
			}else{
				$sql = astercrm::getCustomerphoneSqlByid($customerid,'src','OR');
				$sql = "(".$sql.") AND dstchannel != '' ";
			}
		}

		if($_SESSION['curuser']['usertype'] == 'admin' && $customerid == ''){
			$sql = "SELECT COUNT(*) FROM mycdr  WHERE dstchannel != '' ";
		}elseif ($_SESSION['curuser']['usertype'] == 'groupadmin' && $customerid == ''){
			$group_str = '';
			foreach($_SESSION['curuser']['memberExtens'] as $value){
				$group_str .= "OR src = '".$value."' OR dst = '".$value."' ";
			}
			foreach($_SESSION['curuser']['memberAgents'] as $value){
				$group_str .= "OR dstchannel = 'AGNET/".$value."' ";
			}
			if($group_str != ''){
				$sql = "SELECT COUNT(*) FROM mycdr WHERE (".ltrim($group_str,"\ OR").") AND dstchannel != '' ";
			}else {
				return '0';
			}
		}else{
			if($sql != '' ) {
				$sql = "SELECT COUNT(*) FROM mycdr WHERE (".$sql.")";
			}else {
				return '0';
			}
		}
		if ($joinstr!=''){
			$sql .= " ".$joinstr;
		}

		astercrm::events($sql);
		$res =& $db->getOne($sql);		
		return $res;
	}

	function &getCdrNumRowsMorewithstype($customerid,$cdrtype,$filter, $content,$stype){
		global $db;

		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);

		if($customerid != ''){
			if ($cdrtype == 'out'){
				$sql = astercrm::getCustomerphoneSqlByid($customerid,'dst','OR');
				$sql = "(".$sql.") AND dstchannel != '' AND src != dst ";
			}else{
				$sql = astercrm::getCustomerphoneSqlByid($customerid,'src','OR');
				$sql = "(".$sql.") AND dstchannel != '' ";
			}
		}

		if($_SESSION['curuser']['usertype'] == 'admin' && $customerid == ''){
			$sql = "SELECT COUNT(*) FROM mycdr WHERE dstchannel != '' ";
		}elseif ($_SESSION['curuser']['usertype'] == 'groupadmin' && $customerid == ''){
			$group_str = '';
			foreach($_SESSION['curuser']['memberExtens'] as $value){
				$group_str .= "OR src = '".$value."' OR dst = '".$value."' ";
			}
			foreach($_SESSION['curuser']['memberAgents'] as $value){
				$group_str .= "OR dstchannel = 'AGNET/".$value."' ";
			}
			if($group_str != ''){
				$sql = "SELECT COUNT(*) FROM mycdr WHERE (".ltrim($group_str,"\ OR").") AND dstchannel != '' ";
			}else {
				return '0';
			}
		}else{
			if($sql != '' ) {
				$sql = "SELECT COUNT(*) FROM mycdr WHERE (".$sql.")";
			}else {
				return '0';
			}
		}
		if ($joinstr!=''){
			$sql .= " ".$joinstr;
		}

		astercrm::events($sql);
		$res =& $db->getOne($sql);		
		return $res;
	}

	function &getCdrRecordsFilteredMorewithstype($customerid,$cdrtype,$start, $limit, $filter, $content, $stype,$order){
		global $db;
		
		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);

		if($customerid != ''){
			if($cdrtype == 'out'){
				$sql = astercrm::getCustomerphoneSqlByid($customerid,'dst','OR');
				$sql = "(".$sql.") AND dstchannel != '' AND src != dst ";
			}else{
				$sql = astercrm::getCustomerphoneSqlByid($customerid,'src','OR');
				$sql = "(".$sql.") AND dstchannel != '' ";
			}
		}
		if($_SESSION['curuser']['usertype'] == 'admin' && $customerid == ''){
			$sql = "SELECT * FROM mycdr WHERE dstchannel != '' ";
		}elseif ($_SESSION['curuser']['usertype'] == 'groupadmin' && $customerid == ''){
			$group_str = '';
			foreach($_SESSION['curuser']['memberExtens'] as $value){
				$group_str .= "OR src = '".$value."' OR dst = '".$value."' ";
			}
			foreach($_SESSION['curuser']['memberAgents'] as $value){
				$group_str .= "OR dstchannel = 'AGNET/".$value."' ";
			}
			if($group_str != ''){
				$sql = "SELECT * FROM mycdr WHERE (".ltrim($group_str,"\ OR").") AND dstchannel != '' ";
			}else {
				$sql = "SELECT * FROM mycdr WHERE id = '0'";
			}
		}else{
			if($sql != '' ) {
				$sql = "SELECT * FROM mycdr WHERE (".$sql.")";
			}else {
				$sql = "SELECT * FROM mycdr WHERE id = '0'";
			}
		}

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr."  ";
		}

		$sql .= " ORDER BY ".$order
					." DESC LIMIT $start, $limit $ordering";

		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function createDiallistGrid($userexten,$customerid,$start = 0, $limit = 1, $filter = null, $content = null, $stype = null, $order = null, $divName = "formDiallist", $ordering = ""){
		global $locate;
		$_SESSION['ordering'] = $ordering;
		if($filter == null || $content == null || (!is_array($content) && $content == 'Array') || (!is_array(filter) && $filter == 'Array')){
			$content = null;
			$filter = null;
			$numRows =& Customer::getDiallistNumRows($userexten,$customerid);
			$arreglo =& Customer::getAllDiallist($userexten,$customerid,$start,$limit,$order);
		}else{
			foreach($content as $value){
				if(trim($value) != ""){  //搜索内容有值
					$flag = "1";
					break;
				}
			}
			foreach($filter as $value){
				if(trim($value) != ""){  //搜索条件有值
					$flag2 = "1";
					break;
				}
			}
			foreach($stype as $value){
				if(trim($value) != ""){  //搜索方式有值
					$flag3 = "1";
					break;
				}
			}
			if($flag != "1" || $flag2 != "1" ){  //无值	
				$order = null;
				$numRows =& Customer::getDiallistNumRows($userexten,$customerid);
				$arreglo =& Customer::getAllDiallist($userexten,$customerid,$start,$limit,$order);
			}elseif($flag3 != 1 ){  //未选择搜索方式
				$order = "dialtime";
				$numRows =& Customer::getDiallistNumRowsMore($userexten,$customerid,$filter, $content);
				$arreglo =& Customer::getDiallistFilteredMore($userexten,$customerid,$start, $limit, $filter, $content, $order);
			}else{
				$order = "dialtime";
				$numRows =& Customer::getDiallistNumRowsMorewithstype($userexten,$customerid,$filter, $content,$stype);
				$arreglo =& Customer::getDiallistFilteredMorewithstype($userexten,$customerid,$start, $limit, $filter, $content, $stype,$order);
			}
		}	

		// Editable zone

		// Databse Table: fields
		$fields = array();
		$fields[] = 'dialnumber';
		$fields[] = 'dialtime';
		$fields[] = 'status';
		$fields[] = 'trytime';
		$fields[] = 'creby';
		$fields[] = 'cretime';
		$fileds[] = 'campaignname';
		$fileds[] = 'campaignnote';
		$fieeds[] = 'inexten';

		// HTML table: Headers showed
		$headers = array();
		$headers[] = $locate->Translate("Dialnumber");
		$headers[] = $locate->Translate("Dialtime");
		$headers[] = $locate->Translate("Status");
		$headers[] = $locate->Translate("Trytime");
		$headers[] = $locate->Translate("Creby");
		$headers[] = $locate->Translate("Cretime");
		$headers[] = $locate->Translate("Campaignname");
		$headers[] = $locate->Translate("Campaignnote");
		$headers[] = $locate->Translate("Inexten");

		// HTML table: hearders attributes
		$attribsHeader = array();
		$attribsHeader[] = 'width="13%"';
		$attribsHeader[] = 'width="10%"';
		$attribsHeader[] = 'width="13%"';
		$attribsHeader[] = 'width="10%"';
		$attribsHeader[] = 'width="10%"';
		$attribsHeader[] = 'width="12%"';
		$attribsHeader[] = 'width="10%"';
		$attribsHeader[] = 'width="12%"';
		$attribsHeader[] = 'width="10%"';

		// HTML Table: columns attributes
		$attribsCols = array();
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';

		// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
		$eventHeader = array();
		$eventHeader[]= 'onClick=\'xajax_showDiallist("'.$userexten.'","'.$customerid.'",0,'.$limit.',"'.$filter.'","'.$content.'","dialnumber","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showDiallist("'.$userexten.'","'.$customerid.'",0,'.$limit.',"'.$filter.'","'.$content.'","dialtime","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showDiallist("'.$userexten.'","'.$customerid.'",0,'.$limit.',"'.$filter.'","'.$content.'","status","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showDiallist("'.$userexten.'","'.$customerid.'",0,'.$limit.',"'.$filter.'","'.$content.'","trytime","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showDiallist("'.$userexten.'","'.$customerid.'",0,'.$limit.',"'.$filter.'","'.$content.'","creby","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showDiallist("'.$userexten.'","'.$customerid.'",0,'.$limit.',"'.$filter.'","'.$content.'","cretime","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showDiallist("'.$userexten.'","'.$customerid.'",0,'.$limit.',"'.$filter.'","'.$content.'","diallist.id","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showDiallist("'.$userexten.'","'.$customerid.'",0,'.$limit.',"'.$filter.'","'.$content.'","diallist.id","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showDiallist("'.$userexten.'","'.$customerid.'",0,'.$limit.',"'.$filter.'","'.$content.'","diallist.id","'.$divName.'","ORDERING","'.$stype.'");return false;\'';

		// Select Box: type table.
		$typeFromSearch = array();
		$typeFromSearch[] = 'like';
		$typeFromSearch[] = 'equal';
		$typeFromSearch[] = 'more';
		$typeFromSearch[] = 'less';

		// Selecct Box: Labels showed on searchtype select box.
		$typeFromSearchShowAs = array();
		$typeFromSearchShowAs[] = 'like';
		$typeFromSearchShowAs[] = '=';
		$typeFromSearchShowAs[] = '>';
		$typeFromSearchShowAs[] = '<';

		// Select Box: fields table.
		$fieldsFromSearch = array();
		$fieldsFromSearch[] = 'dialnumber';
		$fieldsFromSearch[] = 'dialtime';
		$fieldsFromSearch[] = 'status';
		$fieldsFromSearch[] = 'trytime';
		$fieldsFromSearch[] = 'creby';
		$fieldsFromSearch[] = 'cretime';

		// Selecct Box: Labels showed on search select box.
		$fieldsFromSearchShowAs = array();
		$fieldsFromSearchShowAs[] = $locate->Translate("dialnumber");
		$fieldsFromSearchShowAs[] = $locate->Translate("dialtime");
		$fieldsFromSearchShowAs[] = $locate->Translate("status");
		$fieldsFromSearchShowAs[] = $locate->Translate("trytime");
		$fieldsFromSearchShowAs[] = $locate->Translate("creby");
		$fieldsFromSearchShowAs[] = $locate->Translate("cretime");

		// Create object whit 5 cols and all data arrays set before.
		$table = new ScrollTable(11,$start,$limit,$filter,$numRows,$content,$order,$customerid,'',$userexten,'diallist');
		$table->setHeader('title',$headers,$attribsHeader,$eventHeader,$edit=1,$delete=1,$detail=false);
		$table->setAttribsCols($attribsCols);
		$table->addRowSearchMore("diallist",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit,"1",0,$typeFromSearch,$typeFromSearchShowAs,$stype);

		while ($arreglo->fetchInto($row)) {
		// Change here by the name of fields of its database table
			$rowc = array();
			$rowc[] = $row['id'];
			$rowc[] = $row['dialnumber'];
			$rowc[] = $row['dialtime'];
			$rowc[] = $row['status'];
			$rowc[] = $row['trytime'];
			$rowc[] = $row['creby'];
			$rowc[] = $row['cretime'];
			$rowc[] = $row['campaignname'];
			$rowc[] = $row['campaignnote'];
			$rowc[] = $row['inexten'];
			$table->addRow("diallist",$rowc,1,1,false,$divName,$fields,$row['creby']);
		}
		
		// End Editable Zone
		
		$html = $table->render();
		
		return $html;
	}

	
	function &getAllDiallist($userexten,$customerid,$start, $limit, $order = null, $creby = null){
		global $db;
		
		$sql = astercrm::getCustomerphoneSqlByid($customerid,'dialnumber','OR');

		if( $sql != '') {
			$sql = "SELECT diallist.*,campaign.campaignname,campaign.campaignnote, campaign.inexten FROM diallist LEFT JOIN campaign ON diallist.campaignid = campaign.id WHERE diallist.assign ='".$userexten."' AND (".$sql.")";
		}else{
			$sql = "SELECT * FROM diallist WHERE id = '0' ";
		}
		
		if($order == null || is_array($order)){
			$sql .= " ORDER by diallist.id DESC LIMIT $start, $limit";//.$_SESSION['ordering'];
		}else{
			$sql .= " ORDER BY diallist.".$order." ".$_SESSION['ordering']." LIMIT $start, $limit";
		}
		
		astercrm::events($sql);
		$res =& $db->query($sql);		
		return $res;
	}

	function &getDiallistNumRows($userexten,$customerid,$filter = null, $content = null){
		global $db;
		
		$sql = astercrm::getCustomerphoneSqlByid($customerid,'dialnumber','OR');
		
		if( $sql != '') {
			$sql = "SELECT COUNT(*) FROM diallist WHERE assign ='".$userexten."' AND (".$sql.")";
		}else{
			return '0';
		}

		astercrm::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}

	function &getDiallistFilteredMore($userexten,$customerid,$start, $limit, $filter, $content, $order,$table = '', $ordering = ""){
		global $db;

		$sql = astercrm::getCustomerphoneSqlByid($customerid,'dialnumber','OR');
				
		$i=0;
		$joinstr='';
		foreach ($content as $value){
			$value = preg_replace("/'/","\\'",$value);
			$value=trim($value);
			if (strlen($value)!=0 && strlen($filter[$i]) != 0){
				$joinstr.="AND diallist.$filter[$i] like '%".$value."%' ";
			}
			$i++;
		}
		
		if( $sql != '') {
			$sql = "SELECT diallist.*,campaign.campaignname,campaign.campaignnote,campaign.inexten FROM diallist LEFT JOIN campaign ON diallist.campaignid = campaign.id WHERE diallist.assign ='".$userexten."' AND (".$sql.")";
		}else{
			$sql = "SELECT * FROM diallist WHERE id = '0' ";
		}

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr."  ";
		}

		$sql .= " ORDER BY diallist.".$order
					." DESC LIMIT $start, $limit $ordering";

		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function &getDiallistNumRowsMore($userexten,$customerid,$filter = null, $content = null){
		global $db;

		$sql = astercrm::getCustomerphoneSqlByid($customerid,'dialnumber','OR');

		$i=0;
		$joinstr='';
		foreach ($content as $value){
			$value = preg_replace("/'/","\\'",$value);
			$value=trim($value);
			if (strlen($value)!=0 && strlen($filter[$i]) != 0){
				$joinstr.="AND diallist.$filter[$i] like '%".$value."%' ";
			}
			$i++;
		}
		
		if( $sql != '') {
			$sql = "SELECT COUNT(*) FROM diallist WHERE assign ='".$userexten."' AND (".$sql.") ";
		}else{
			return '0';
		}
		
		if ($joinstr!=''){
			$sql .= " ".$joinstr;
		}

		astercrm::events($sql);
		$res =& $db->getOne($sql);		
		return $res;
	}

	function &getDiallistNumRowsMorewithstype($userexten,$customerid,$filter, $content,$stype){
		global $db;

		$sql = astercrm::getCustomerphoneSqlByid($customerid,'dialnumber','OR');

		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);
		
		if( $sql != '') {
			$sql = "SELECT COUNT(*) FROM diallist WHERE assign ='".$userexten."' AND (".$sql.") ";
		}else{
			return '0';
		}
		
		if ($joinstr!=''){
			$sql .= " ".$joinstr;
		}

		astercrm::events($sql);
		$res =& $db->getOne($sql);		
		return $res;
	}

	function &getDiallistFilteredMorewithstype($userexten,$customerid,$start, $limit, $filter, $content, $stype,$order){
		global $db;

		$sql = astercrm::getCustomerphoneSqlByid($customerid,'dialnumber','OR');
				
		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);
		
		if( $sql != '') {
			$sql = "SELECT diallist.*,campaign.campaignname,campaign.campaignnote,campaign.inexten FROM diallist LEFT JOIN campaign ON diallist.campaignid = campaign.id WHERE diallist.assign ='".$userexten."' AND (".$sql.")";
		}else{
			$sql = "SELECT * FROM diallist WHERE id = '0' ";
		}

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr."  ";
		}

		$sql .= " ORDER BY diallist.".$order
					." DESC LIMIT $start, $limit $ordering";

		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function formDiallistAdd($userexten,$customerid){
		global $locate;
		if ($_SESSION['curuser']['usertype'] == 'admin'){
				$res = Customer::getGroups();
				$groupoptions .= '<select name="groupid" id="groupid" onchange="setCampaign();">';
				while ($row = $res->fetchRow()) {
						$groupoptions .= '<option value="'.$row['groupid'].'"';
						$groupoptions .='>'.$row['groupname'].'</option>';
				}				
				$groupoptions .= '</select>';	
				$assignoptions = '<input type="text" id="assign" name="assign" size="35"">';
		}elseif($_SESSION['curuser']['usertype'] == 'groupadmin'){
				$groupoptions .= $_SESSION['curuser']['group']['groupname'].'<input id="groupid" name="groupid" type="hidden" value="'.$_SESSION['curuser']['groupid'].'">';	
				$res = Customer::getRecordsByField('groupid',$_SESSION['curuser']['groupid'],'astercrm_account');
				$assignoptions .= '<select name="assign" id="assign">';
				while ($row = $res->fetchRow()) {
						$assignoptions .= '<option value="'.$row['extension'].'"';
						$assignoptions .='>'.$row['extension'].'</option>';
				}				
				$assignoptions .= '</select>';
		}else{
				$groupoptions .= $_SESSION['curuser']['group']['groupname'].'<input id="groupid" name="groupid" type="hidden" value="'.$_SESSION['curuser']['groupid'].'">';	
				$assignoptions = '<input type="text" id="assign" name="assign" size="35" value="'.$_SESSION['curuser']['extension'].'" disabled><input type="hidden" id="assign" name="assign" value="'.$_SESSION['curuser']['extension'].'">';
		}

		$res_customer =astercrm::getRecordById($customerid,'customer');
		$res_contact =astercrm::getContactListByID($customerid);
		$numberblank = '<select name="dialnumber" id="dialnumber">';
		if ($res_customer['phone'] != '') $numberblank .= '<option value="'.$res_customer['phone'].'">'.$res_customer['phone'].'</option>';
		if ($res_customer['mobile'] != '') $numberblank .= '<option value="'.$res_customer['mobile'].'">'.$res_customer['mobile'].'</option>';
		while ($res_contact->fetchInto($row)) {
			if ($row['phone'] != '') $numberblank .= '<option value="'.$row['phone'].'">'.$row['phone'].'</option>';
			if ($row['phone1'] != '') $numberblank .= '<option value="'.$row['phone1'].'">'.$row['phone1'].'</option>';
			if ($row['phone2'] != '') $numberblank .= '<option value="'.$row['phone2'].'">'.$row['phone2'].'</option>';
			if ($row['mobile'] != '') $numberblank .= '<option value="'.$row['mobile'].'">'.$row['mobile'].'</option>';
		}
		$numberblank .= '</select>';

		$html = '
				<!-- No edit the next line -->
				<form method="post" name="formaddDiallist" id="formaddDiallist">
				
				<table border="1" width="100%" class="adminlist">
					<tr>
						<td nowrap align="left">'.$locate->Translate("number").'</td>
						<td align="left">'.$numberblank.'</td>
					</tr>
					<tr>
						<td nowrap align="left">'.$locate->Translate("Assign To").'</td>
						<td align="left">
							'.$assignoptions.'
						</td>
					</tr>
					<tr>
						<td nowrap align="left">'.$locate->Translate("Dialtime").'</td>
						<td align="left">
							<input type="text" name="dialtime" size="20" value="'.date("Y-m-d H:i",time()).'">
			<INPUT onclick="displayCalendar(document.getElementById(\'dialtime\'),\'yyyy-mm-dd hh:ii\',this,true)" type="button" value="Cal">
						</td>
					</tr>';
		$html .= '
					<tr>
						<td align="left" width="25%">'.$locate->Translate("Group Name").'</td>
						<td>'.$groupoptions.'</td>
					</tr>';
		$html .= '
					<tr>
						<td align="left" width="25%">'.$locate->Translate("Campaign Name").'</td>
						<td><SELECT id="campaignid" name="campaignid"></SELECT></td>
					</tr>';
		$html .= '
					<tr>
						<td nowrap colspan=2 align=right><input type="button" id="btnAddDiallist" name="btnAddDiallist" value="'.$locate->Translate("continue").'" onclick="xajax_saveDiallist(xajax.getFormValues(\'formaddDiallist\'),\''.$userexten.'\',\''.$customerid.'\');return false;"></td>
					</tr>
				<table>
				</form>
				';
		return $html;
	}

	function createRecordsGrid($customerid='',$start = 0, $limit = 1, $filter = null, $content = null, $order = null, $divName = "formRecords", $ordering = "",$stype=null ){
		global $locate;
		$_SESSION['ordering'] = $ordering;
		if($filter == null || $content == null || (!is_array($content) && $content == 'Array') || (!is_array(filter) && $filter == 'Array')){
			$content = null;
			$filter = null;
			$numRows =& astercrm::getRecNumRows($customerid);
			$arreglo =& astercrm::getAllRecRecords($customerid,$start,$limit,$order);
		}else{
			foreach($content as $value){
				if(trim($value) != ""){  //搜索内容有值
					$flag = "1";
					break;
				}
			}
			foreach($filter as $value){
				if(trim($value) != ""){  //搜索条件有值
					$flag2 = "1";
					break;
				}
			}
			foreach($stype as $value){
				if(trim($value) != ""){  //搜索方式有值
					$flag3 = "1";
					break;
				}
			}
			if($flag != "1" || $flag2 != "1" ){  //无值	
				$order = null;
				$numRows =& astercrm::getRecNumRows($customerid);
				$arreglo =& astercrm::getAllRecRecords($customerid,$start,$limit,$order);
			}elseif($flag3 != 1 ){  //未选择搜索方式
				$order = "monitorrecord.id";
				$numRows =& astercrm::getRecNumRowsMore($customerid,$filter, $content);
				$arreglo =& astercrm::getRecRecordsFilteredMore($customerid,$start, $limit, $filter, $content, $order);
			}else{
				$order = "monitorrecord.id";
				$numRows =& astercrm::getRecNumRowsMorewithstype($customerid,$filter, $content,$stype);
				$arreglo =& astercrm::getRecRecordsFilteredMorewithstype($customerid,$start, $limit, $filter, $content, $stype,$order);
			}
		}	
		// Databse Table: fields
		$fields = array();
		$fields[] = 'calldate';
		$fields[] = 'src';
		$fields[] = 'dst';
		$fields[] = 'didnumber';
		$fields[] = 'dstchannel';
		$fields[] = 'duration';
		$fields[] = 'billsec';
		$fields[] = 'filename';
		$fields[] = 'creby';

		// HTML table: Headers showed
		$headers = array();
		$headers[] = $locate->Translate("Calldate");
		$headers[] = $locate->Translate("Src");
		$headers[] = $locate->Translate("Dst");
		$headers[] = $locate->Translate("Callee Id");
		$headers[] = $locate->Translate("Agent");
		$headers[] = $locate->Translate("Duration");
		$headers[] = $locate->Translate("Billsec");
		$headers[] = $locate->Translate("filename");
		$headers[] = $locate->Translate("creby");

		// HTML table: hearders attributes
		$attribsHeader = array();
		$attribsHeader[] = 'width="11%"';
		$attribsHeader[] = 'width="11%"';
		$attribsHeader[] = 'width="11%"';
		$attribsHeader[] = 'width="12%"';
		$attribsHeader[] = 'width="11%"';
		$attribsHeader[] = 'width="10%"';
		$attribsHeader[] = 'width="10%"';
		$attribsHeader[] = 'width="12%"';
		$attribsHeader[] = 'width="10%"';


		// HTML Table: columns attributes
		$attribsCols = array();
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';
		$attribsCols[] = 'style="text-align: left"';

		// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
		$eventHeader = array();
		$eventHeader[]= 'onClick=\'xajax_showRecords('.$customerid.',0,'.$limit.',"'.$filter.'","'.$content.'","mycdr.calldate","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showRecords('.$customerid.',0,'.$limit.',"'.$filter.'","'.$content.'","mycdr.src","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showRecords('.$customerid.',0,'.$limit.',"'.$filter.'","'.$content.'","mycdr.dst","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showRecords('.$customerid.',0,'.$limit.',"'.$filter.'","'.$content.'","mycdr.didnumber","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showRecords('.$customerid.',0,'.$limit.',"'.$filter.'","'.$content.'","mycdr.dstchannel","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showRecords('.$customerid.',0,'.$limit.',"'.$filter.'","'.$content.'","mycdr.duration","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showRecords('.$customerid.',0,'.$limit.',"'.$filter.'","'.$content.'","mycdr.billsec","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showRecords('.$customerid.',0,'.$limit.',"'.$filter.'","'.$content.'","monitorrecord.filename","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showRecords('.$customerid.',0,'.$limit.',"'.$filter.'","'.$content.'","monitorrecord.creby","'.$divName.'","ORDERING","'.$stype.'");return false;\'';
		
		
		// Select Box: type table.
		$typeFromSearch = array();
		$typeFromSearch[] = 'like';
		$typeFromSearch[] = 'equal';
		$typeFromSearch[] = 'more';
		$typeFromSearch[] = 'less';

		// Selecct Box: Labels showed on searchtype select box.
		$typeFromSearchShowAs = array();
		$typeFromSearchShowAs[] = 'like';
		$typeFromSearchShowAs[] = '=';
		$typeFromSearchShowAs[] = '>';
		$typeFromSearchShowAs[] = '<';

		// Select Box: fields table.
		$fieldsFromSearch = array();
		$fieldsFromSearch[] = 'src';
		$fieldsFromSearch[] = 'calldate';
		$fieldsFromSearch[] = 'dst';
		$fieldsFromSearch[] = 'didnumber';
		$fieldsFromSearch[] = 'billsec';
		$fieldsFromSearch[] = 'filename';
		$fieldsFromSearch[] = 'creby';

		// Selecct Box: Labels showed on search select box.
		$fieldsFromSearchShowAs = array();
		$fieldsFromSearchShowAs[] = $locate->Translate("src");
		$fieldsFromSearchShowAs[] = $locate->Translate("calldate");
		$fieldsFromSearchShowAs[] = $locate->Translate("dst");
		$fieldsFromSearchShowAs[] = $locate->Translate("callee id");
		$fieldsFromSearchShowAs[] = $locate->Translate("billsec");
		$fieldsFromSearchShowAs[] = $locate->Translate("filename");
		$fieldsFromSearchShowAs[] = $locate->Translate("creby");

		// Create object whit 5 cols and all data arrays set before.
		$table = new ScrollTable(7,$start,$limit,$filter,$numRows,$content,$order,$customerid,'','','monitorrecord');
		$table->setHeader('title',$headers,$attribsHeader,$eventHeader,$edit=false,$delete=false,$detail=false);
		$table->setAttribsCols($attribsCols);
		$table->addRowSearchMore("monitorrecord",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit,0,0,$typeFromSearch,$typeFromSearchShowAs,$stype);

		while ($arreglo->fetchInto($row)) {
		// Change here by the name of fields of its database table
			$rowc = array();
			$rowc[] = $row['id'];
			$rowc[] = $row['calldate'];
			$rowc[] = $row['src'];
			$rowc[] = $row['dst'];
			$rowc[] = $row['didnumber'];
			if(strstr($row['dstchannel'],'AGENT')){
				$agent = split('/',$row['dstchannel']);
				$rowc[] = $agent['1'];
			}else{
				$rowc[]='';
			}
			$rowc[] = $row['duration'];
			$rowc[] = $row['billsec'];
			$rowc['filename'] = $row['filename'];
			$rowc[] = $row['creby'];
			$table->addRow("monitorrecord",$rowc,false,false,false,$divName,$fields);
		}
		//donnie
		// End Editable Zone
		
		$html = $table->render();
		
		return $html;
	}

	function &getAllRecRecords($customerid='',$start, $limit, $order = null, $creby = null){
		global $db;
		if($customerid != ''){
			$sql = astercrm::getCustomerphoneSqlByid($customerid,'dst','OR','src');
		}

		if($_SESSION['curuser']['usertype'] == 'admin' && $customerid == ''){
			$sql = "SELECT mycdr.calldate,mycdr.src,mycdr.dst,mycdr.didnumber,mycdr.dstchannel,mycdr.duration,mycdr.billsec,monitorrecord.id,monitorrecord.filename,monitorrecord.creby FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE  monitorrecord.uniqueid != '' AND mycdr.dstchannel != '' ";
		}elseif ($_SESSION['curuser']['usertype'] == 'groupadmin' && $customerid == ''){
			$group_str = '';
			foreach($_SESSION['curuser']['memberExtens'] as $value){
				$group_str .= "OR src = '".$value."' OR dst = '".$value."' ";
			}
			foreach($_SESSION['curuser']['memberAgents'] as $value){
				$group_str .= "OR dstchannel = 'AGNET/".$value."' ";
			}
			if($group_str != ''){
				$sql = "SELECT mycdr.calldate,mycdr.src,mycdr.dst,mycdr.didnumber,mycdr.dstchannel,mycdr.duration,mycdr.billsec,monitorrecord.id,monitorrecord.filename,monitorrecord.creby FROM  mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE monitorrecord.uniqueid != '' AND (".ltrim($group_str,"\ OR").") AND mycdr.dstchannel != '' ";
			}else {
				$sql = "SELECT * FROM monitorrecord WHERE id = '0'";
			}
		}else{			
			if($sql != '' ) {
				if($_SESSION['curuser']['usertype'] != 'admin' && $_SESSION['curuser']['usertype'] != 'groupadmin'){
					$sql = "SELECT mycdr.calldate,mycdr.src,mycdr.dst,mycdr.didnumber,mycdr.dstchannel,mycdr.duration,mycdr.billsec,monitorrecord.id,monitorrecord.filename,monitorrecord.creby FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE monitorrecord.uniqueid != '' AND (".$sql.") AND monitorrecord.creby = '".$_SESSION['curuser']['username']."' AND mycdr.dstchannel != '' ";
				}else{
					$sql = "SELECT mycdr.calldate,mycdr.src,mycdr.dst,mycdr.didnumber,mycdr.dstchannel,mycdr.duration,mycdr.billsec,monitorrecord.id,monitorrecord.filename,monitorrecord.creby FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE monitorrecord.uniqueid != '' AND (".$sql.")  AND mycdr.dstchannel != '' ";
				}
			}else {
				$sql = "SELECT * FROM monitorrecord WHERE id = '0'";
			}
		}

		if($order == null || is_array($order)){
			$sql .= " ORDER by monitorrecord.id DESC LIMIT $start, $limit";//.$_SESSION['ordering'];
		}else{
			$sql .= " ORDER BY ".$order." ".$_SESSION['ordering']." LIMIT $start, $limit";
		}

		//echo $sql;exit;
		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function &getRecNumRows($customerid='',$filter = null, $content = null){
		global $db;
		if($customerid != ''){
				$sql = astercrm::getCustomerphoneSqlByid($customerid,'dst','OR','src');
		}

		if($_SESSION['curuser']['usertype'] == 'admin' && $customerid == ''){
			$sql = "SELECT COUNT(*) FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE monitorrecord.uniqueid != ''  AND mycdr.dstchannel != '' ";
		}elseif ($_SESSION['curuser']['usertype'] == 'groupadmin' && $customerid == ''){
			$group_str = '';
			foreach($_SESSION['curuser']['memberExtens'] as $value){
				$group_str .= "OR src = '".$value."' OR dst = '".$value."' ";
			}
			foreach($_SESSION['curuser']['memberAgents'] as $value){
				$group_str .= "OR dstchannel = 'AGNET/".$value."' ";
			}
			if($group_str != ''){
				$sql = "SELECT COUNT(*) FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE  monitorrecord.uniqueid != '' AND (".ltrim($group_str,"\ OR").")  AND mycdr.dstchannel != '' ";
			}else {
				return '0';
			}
		}else{
			if($sql != '' ) {
				if($_SESSION['curuser']['usertype'] != 'admin' && $_SESSION['curuser']['usertype'] != 'groupadmin'){
					$sql = "SELECT COUNT(*) FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE  monitorrecord.uniqueid != '' AND (".$sql.") AND monitorrecord.creby = '".$_SESSION['curuser']['username']."' AND mycdr.dstchannel != '' ";
				}else{
					$sql = "SELECT COUNT(*) FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE  monitorrecord.uniqueid != '' AND (".$sql.") AND mycdr.dstchannel != '' ";
				}
			}else {
				return '0';
			}
		}
		astercrm::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}

	function &getRecRecordsFilteredMore($customerid='',$start, $limit, $filter, $content, $order,$table = '', $ordering = ""){
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
		if($customerid != ''){
			$sql = astercrm::getCustomerphoneSqlByid($customerid,'dst','OR','src');
		}
		if($_SESSION['curuser']['usertype'] == 'admin' && $customerid == ''){
			$sql = "SELECT mycdr.calldate,mycdr.src,mycdr.dst,mycdr.didnumber,mycdr.dstchannel,mycdr.duration,mycdr.billsec,monitorrecord.id,monitorrecord.filename,monitorrecord.creby FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE  monitorrecord.uniqueid != ''  AND mycdr.dstchannel != '' ";
		}elseif ($_SESSION['curuser']['usertype'] == 'groupadmin' && $customerid == ''){
			$group_str = '';
			foreach($_SESSION['curuser']['memberExtens'] as $value){
				$group_str .= "OR src = '".$value."' OR dst = '".$value."' ";
			}
			foreach($_SESSION['curuser']['memberAgents'] as $value){
				$group_str .= "OR dstchannel = 'AGNET/".$value."' ";
			}
			if($group_str != ''){
				$sql = "SELECT mycdr.calldate,mycdr.src,mycdr.dst,mycdr.didnumber,mycdr.dstchannel,mycdr.duration,mycdr.billsec,monitorrecord.id,monitorrecord.filename,monitorrecord.creby FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE  monitorrecord.uniqueid != '' AND (".ltrim($group_str,"\ OR").")  AND mycdr.dstchannel != '' ";
			}else {
				$sql = "SELECT * FROM monitorrecord WHERE id = '0'";
			}
		}else{
			if($sql != '' ) {
				if($_SESSION['curuser']['usertype'] != 'admin' && $_SESSION['curuser']['usertype'] != 'groupadmin'){
					$sql = "SELECT mycdr.calldate,mycdr.src,mycdr.dst,mycdr.didnumber,mycdr.dstchannel,mycdr.duration,mycdr.billsec,monitorrecord.id,monitorrecord.filename,monitorrecord.creby FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE monitorrecord.uniqueid != '' AND (".$sql.") AND monitorrecord.creby = '".$_SESSION['curuser']['username']."' AND mycdr.dstchannel != '' ";
				}else{
					$sql = "SELECT mycdr.calldate,mycdr.src,mycdr.dst,mycdr.didnumber,mycdr.dstchannel,mycdr.duration,mycdr.billsec,monitorrecord.id,monitorrecord.filename,monitorrecord.creby FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE monitorrecord.uniqueid != '' AND (".$sql.") AND mycdr.dstchannel != '' ";
				}
			}else {
				$sql = "SELECT * FROM monitorrecord WHERE id = '0'";
			}
		}

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr."  ";
		}

		$sql .= " ORDER BY ".$order
					." DESC LIMIT $start, $limit $ordering";

		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function &getRecNumRowsMore($customerid='',$filter = null, $content = null,$table = ''){
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
		if($customerid != ''){
			$sql = astercrm::getCustomerphoneSqlByid($customerid,'dst','OR','src');
		}

		if($_SESSION['curuser']['usertype'] == 'admin' && $customerid == ''){
			$sql = "SELECT COUNT(*) FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE  monitorrecord.uniqueid != '' AND mycdr.dstchannel != ''  ";
		}elseif ($_SESSION['curuser']['usertype'] == 'groupadmin' && $customerid == ''){
			$group_str = '';
			foreach($_SESSION['curuser']['memberExtens'] as $value){
				$group_str .= "OR src = '".$value."' OR dst = '".$value."' ";
			}
			foreach($_SESSION['curuser']['memberAgents'] as $value){
				$group_str .= "OR dstchannel = 'AGNET/".$value."' ";
			}
			if($group_str != ''){
				$sql = "SELECT COUNT(*) FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE  monitorrecord.uniqueid != '' AND (".ltrim($group_str,"\ OR").") AND mycdr.dstchannel != ''  ";
			}else {
				return '0';
			}
		}else{
			if($sql != '' ) {
				if($_SESSION['curuser']['usertype'] != 'admin' && $_SESSION['curuser']['usertype'] != 'groupadmin'){
					$sql = "SELECT COUNT(*) FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE  monitorrecord.uniqueid != '' AND (".$sql.") AND monitorrecord.creby = '".$_SESSION['curuser']['username']."' AND mycdr.dstchannel != '' ";
				}else{
					$sql = "SELECT COUNT(*) FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE  monitorrecord.uniqueid != '' AND (".$sql.") AND mycdr.dstchannel != '' ";
				}
			}else {
				return '0';
			}
		}
		if ($joinstr!=''){
			$sql .= " ".$joinstr;
		}

		astercrm::events($sql);
		$res =& $db->getOne($sql);		
		return $res;
	}

	function &getRecNumRowsMorewithstype($customerid,$filter, $content,$stype){
		global $db;

		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);

		if($customerid != ''){
			$sql = astercrm::getCustomerphoneSqlByid($customerid,'dst','OR','src');
		}

		if($_SESSION['curuser']['usertype'] == 'admin' && $customerid == ''){
			$sql = "SELECT COUNT(*) FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE  monitorrecord.uniqueid != ''  AND mycdr.dstchannel != '' ";
		}elseif ($_SESSION['curuser']['usertype'] == 'groupadmin' && $customerid == ''){
			$group_str = '';
			foreach($_SESSION['curuser']['memberExtens'] as $value){
				$group_str .= "OR src = '".$value."' OR dst = '".$value."' ";
			}
			foreach($_SESSION['curuser']['memberAgents'] as $value){
				$group_str .= "OR dstchannel = 'AGNET/".$value."' ";
			}
			if($group_str != ''){
				$sql = "SELECT COUNT(*) FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE monitorrecord.uniqueid != '' AND (".ltrim($group_str,"\ OR").")  AND mycdr.dstchannel != '' ";
			}else {
				return '0';
			}
		}else{
			if($sql != '' ) {
				if($_SESSION['curuser']['usertype'] != 'admin' && $_SESSION['curuser']['usertype'] != 'groupadmin'){
					$sql = "SELECT COUNT(*) FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE  monitorrecord.uniqueid != '' AND (".$sql.") AND monitorrecord.creby = '".$_SESSION['curuser']['username']."' AND mycdr.dstchannel != '' ";
				}else{
					$sql = "SELECT COUNT(*) FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE  monitorrecord.uniqueid != '' AND (".$sql.") AND mycdr.dstchannel != '' ";
				}
			}else {
				return '0';
			}
		}
		if ($joinstr!=''){
			$sql .= " ".$joinstr;
		}

		astercrm::events($sql);
		$res =& $db->getOne($sql);		
		return $res;
	}

	function &getRecRecordsFilteredMorewithstype($customerid,$start, $limit, $filter, $content, $stype,$order){
		global $db;
		
		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);

		if($customerid != ''){
			$sql = astercrm::getCustomerphoneSqlByid($customerid,'dst','OR','src');
		}
		if($_SESSION['curuser']['usertype'] == 'admin' && $customerid == ''){
			$sql = "SELECT mycdr.calldate,mycdr.src,mycdr.dst,mycdr.didnumber,mycdr.dstchannel,mycdr.duration,mycdr.billsec,monitorrecord.id,monitorrecord.filename,monitorrecord.creby FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE  monitorrecord.uniqueid != ''  AND mycdr.dstchannel != '' ";
		}elseif ($_SESSION['curuser']['usertype'] == 'groupadmin' && $customerid == ''){
			$group_str = '';
			foreach($_SESSION['curuser']['memberExtens'] as $value){
				$group_str .= "OR src = '".$value."' OR dst = '".$value."' ";
			}
			foreach($_SESSION['curuser']['memberAgents'] as $value){
				$group_str .= "OR dstchannel = 'AGNET/".$value."' ";
			}
			if($group_str != ''){
				$sql = "SELECT mycdr.calldate,mycdr.src,mycdr.dst,mycdr.didnumber,mycdr.dstchannel,mycdr.duration,mycdr.billsec,monitorrecord.id,monitorrecord.filename,monitorrecord.creby FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE monitorrecord.uniqueid != '' AND (".ltrim($group_str,"\ OR").")  AND mycdr.dstchannel != '' ";
			}else {
				$sql = "SELECT * FROM monitorrecord WHERE id = '0'";
			}
		}else{
			if($sql != '' ) {
				if($_SESSION['curuser']['usertype'] != 'admin' && $_SESSION['curuser']['usertype'] != 'groupadmin'){
					$sql = "SELECT mycdr.calldate,mycdr.src,mycdr.dst,mycdr.didnumber,mycdr.dstchannel,mycdr.duration,mycdr.billsec,monitorrecord.id,monitorrecord.filename,monitorrecord.creby FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE monitorrecord.uniqueid != '' AND (".$sql.") AND monitorrecord.creby = '".$_SESSION['curuser']['username']."' AND mycdr.dstchannel != '' ";
				}else{
					$sql = "SELECT mycdr.calldate,mycdr.src,mycdr.dst,mycdr.didnumber,mycdr.dstchannel,mycdr.duration,mycdr.billsec,monitorrecord.id,monitorrecord.filename,monitorrecord.creby FROM mycdr LEFT JOIN monitorrecord ON (mycdr.srcuid = monitorrecord.uniqueid OR mycdr.dstuid = monitorrecord.uniqueid) WHERE monitorrecord.uniqueid != '' AND (".$sql.")  AND mycdr.dstchannel != '' ";
				}
			}else {
				$sql = "SELECT * FROM monitorrecord WHERE id = '0'";
			}
		}

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr."  ";
		}

		$sql .= " ORDER BY ".$order
					." DESC LIMIT $start, $limit $ordering";

		astercrm::events($sql);
		$res =& $db->query($sql);
		return $res;
	}
}
?>
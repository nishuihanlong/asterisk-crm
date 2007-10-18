<?
/*******************************************************************************
* astercrm.server.common.php
* xajax.Grid类的共用函数, 适用于包含customer,contact,note信息的界面
*							customer.*
*							contact.*
*							note.*
*							survey.*
* astercrm

* Functions List

			noteAdd					显示增加note的表单
			saveNote				保存note
			surveyAdd				显示增加survey result的表单
			saveSurvey				保存survey result结果
			showCustomer			显示详细customer信息的表单
			showContact				显示详细contact信息的表单
			showNote				显示详细note信息的表单
			save					主保存函数
									可用于插入customer, contact, survey result 和 note
			update					主更新函数, 可用于更新customer, contact 和 note
			updateField				更新某一域的函数
			updateField				将表格对象更改为可修改记录的inputbox对象
			add						主显示函数
									显示同时增加customer, contact, survey result 和 note
			showGrid				显示grid表格
			delete					从数据库中删除一条记录
			edit
*/

function noteAdd($customerid,$contactid){
	global $locate;
	$html = Table::Top($locate->Translate("add_note"),"formNoteInfo"); 			
	$html .= Customer::noteAdd($customerid,$contactid); 		
	$html .= Table::Footer();
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("formNoteInfo", "style.visibility", "visible");
	$objResponse->addAssign("formNoteInfo", "innerHTML", $html);	
	return $objResponse->getXML();
}

function surveyAdd($customerid,$contactid){
	global $locate;

	$html = Table::Top($locate->Translate("add_survey"),"formNoteInfo"); 			
	$html .= Customer::surveyAdd($customerid,$contactid); 		
	$html .= Table::Footer();
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("formNoteInfo", "style.visibility", "visible");
	$objResponse->addAssign("formNoteInfo", "innerHTML", $html);	
	return $objResponse->getXML();
}

function showCustomer($id = 0, $type="customer"){
	global $locate;
	$objResponse = new xajaxResponse();
	if($id != 0 && $id != null ){
		$html = Table::Top($locate->Translate("customer_detail"),"formCustomerInfo"); 			
		$html .= Customer::showCustomerRecord($id,$type); 		
		$html .= Table::Footer();
		$objResponse->addAssign("formCustomerInfo", "style.visibility", "visible");
		$objResponse->addAssign("formCustomerInfo", "innerHTML", $html);	
		return $objResponse->getXML();
	}else
		return $objResponse->getXML();
}

function showNote($id = '', $type="customer"){
	global $locate;
	if($id != ''){
		$html = Table::Top($locate->Translate("note_detail"),"formNoteInfo"); 			
		$html .= Customer::showNoteList($id,$type); 		
		$html .= Table::Footer();
		$objResponse = new xajaxResponse();
		$objResponse->addAssign("formNoteInfo", "style.visibility", "visible");
		$objResponse->addAssign("formNoteInfo", "innerHTML", $html);	
		return $objResponse->getXML();
	}
}

function showContact($id = null, $type="contact"){
	global $locate;
	$objResponse = new xajaxResponse();

	if($id != null ){
		$html = Table::Top($locate->Translate("contact_detail"),"formContactInfo"); 
		$contactHTML .= Customer::showContactRecord($id,$type);

		if ($contactHTML == '')
			return $objResponse->getXML();
		else
			$html .= $contactHTML;

		$html .= Table::Footer();
		$objResponse->addAssign("formContactInfo", "style.visibility", "visible");
		$objResponse->addAssign("formContactInfo", "innerHTML", $html);	
		return $objResponse->getXML();
	}
}

function saveNote($f){
	$objResponse = new xajaxResponse();
	global $locate;
	$respOk = Customer::insertNewNote($f,$f['customerid'],$f['contactid']);
	if ($respOk){
		$objResponse->addAssign("formNoteInfo", "style.visibility", "hidden");
		$objResponse->addClear("formNoteInfo", "innerHTML");	

		$html = createGrid(0,ROWSXPAGE);
		$objResponse->addAssign("grid", "innerHTML", $html);
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("a_new_note_added"));

	}else
		$objResponse->addAlert('can not add note');

	return $objResponse;
}

function saveSurvey($f){
	$objResponse = new xajaxResponse();
	global $locate;
	if ($f['surveyoption'] != '' || $f['surveynote'] != ''){
		$respOk = Customer::insertNewSurveyResult($f['surveyid'],$f['surveyoption'],$f['surveynote'],$f['customerid'],$f['contactid']); 
		if ($respOk){
			$objResponse->addAlert('add a new survey');
			$objResponse->addAssign("formNoteInfo", "style.visibility", "hidden");
			$objResponse->addClear("formNoteInfo", "innerHTML");	
		}else
			$objResponse->addAlert('can not add survey');
	}
	return $objResponse;
}

function save($f){
	$objResponse = new xajaxResponse();
	global $locate,$config;

	$f['customer'] = trim($f['customer']);
	$f['contact'] = trim($f['contact']);

	if (empty($f['customer']) && empty($f['contact']))
		return $objResponse;
	
	if(empty($f['customer'])) {
		$customerID = 0;
	} else{
	

		if ($f['customerid'] == '' || $f['customerid'] == 0){
			if ($config['system']['allow_same_data'] == false){
				//检查是否有完全匹配的customer记录
				$customer = Customer::checkValues("customer","customer",$f['customer']);
			}else{
				$customer = '';
			}

			//有完全匹配的话就取这个customerid
			if ($customer != ''){
				$respOk = $customer;
				$objResponse->addAlert($locate->Translate("found_customer_replaced"));
			}else{
				$respOk = Customer::insertNewCustomer($f); // insert a new customer record
				if (!$respOk){
					$objResponse->addAlert($locate->Translate("customer_add_error"));
					return $objResponse;
				}
				$objResponse->addAlert($locate->Translate("a_new_customer_added"));
			}
		} else{
			$respOk = $f['customerid'];
		}
		$customerID = $respOk;
	}

	if(empty($f['contact'])) {
		$contactID = 0;
	} else{
		if ($f['contactid'] == ''){

			if ($config['system']['allow_same_data'] == false){
				//检查是否有完全匹配的contact记录
				$contact = Customer::checkValues("contact","contact",$f['contact'],"string","customerid",$customerID,"int");
			}else{
				$contact = '';
			}

			//有完全匹配的话就取这个contactid
			if ($contact != ''){
				$respOk = $contact;
				$objResponse->addAlert($locate->Translate("found_contact_replaced"));
			}else{
				$respOk = Customer::insertNewContact($f,$customerID); // insert a new contact record
				if (!$respOk){
					$objResponse->addAlert($locate->Translate("contact_add_error"));
					return $objResponse;
				}
				$objResponse->addAlert($locate->Translate("a_new_contact_added"));
			}
		}else{
			$respOk = $f['contactid'];

			$res =& Customer::getContactByID($respOk);
			if ($res){
				$contactCustomerID = $res['customerid'];
				if ($contactCustomerID == 0 && $customerID ==0)
				{
				}else{
					$res =& Customer::updateField('contact','customerid',$customerID,$f['contactid']);
					if ($res){
						$objResponse->addAlert($locate->Translate("a_contact_binding"));
					}
				}
			}
		}
		$contactID = $respOk;
	}

	if ($f['surveyoption'] != '' || $f['surveynote'] != ''){
		$respOk = Customer::insertNewSurveyResult($f['surveyid'],$f['surveyoption'],$f['surveynote'],$customerID,$contactID); 
		$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("survey_added"));
	}
	
	if ($respOk)

	if(empty($f['note'])) {

	} else{

		$respOk = Customer::insertNewNote($f,$customerID,$contactID); // add a new Note
		if ($respOk){
			$html = createGrid(0,ROWSXPAGE);
			$objResponse->addAssign("grid", "innerHTML", $html);
			$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("a_new_note_added"));
		}else{
			$objResponse->addAlert($locate->Translate("note_add_error"));
			return $objResponse;
		}
	}


	$objResponse->addAssign("formDiv", "style.visibility", "hidden");
	$objResponse->addAssign("formCustomerInfo", "style.visibility", "hidden");
	$objResponse->addAssign("formContactInfo", "style.visibility", "hidden");

	$objResponse->addClear("formDiv", "innerHTML");

	$objResponse->addClear("formCustomerInfo", "innerHTML");
	$objResponse->addClear("formContactInfo", "innerHTML");

	return $objResponse->getXML();
}

function delete($id = null, $table_DB = null){
	global $locate;
	Customer::deleteRecord($id,$table_DB);
	$html = createGrid(0,ROWSXPAGE);
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("grid", "innerHTML", $html);
	$objResponse->addAssign("msgZone", "innerHTML", $locate->Translate("record_deleted")); 
	return $objResponse->getXML();
}

function update($f, $type){
	$objResponse = new xajaxResponse();

	if ($type == 'note'){
		$respOk = Customer::updateNoteRecord($f,"append");
	}elseif ($type == 'customer'){
		if (empty($f['customer']))
			$message = "The field Customer does not have to be null";
		else{
			$respOk = Customer::updateCustomerRecord($f);
			if (!$respOk)
				$message = 'update customer table failed';
		}
	}elseif ($type == 'contact'){
		if (empty($f['contact']))
			$message = "The field Contact does not have to be null";
		else
			$respOk = Customer::updateContactRecord($f);
	}else{
		$message = 'error: no current type set';
	}

	if(!$message){
		if($respOk){
			$html = createGrid(0,ROWSXPAGE);
			$objResponse->addAssign("grid", "innerHTML", $html);
			$objResponse->addAssign("msgZone", "innerHTML", "A record has been updated");
			$objResponse->addAssign("formEditInfo", "style.visibility", "hidden");
		}else{
			$objResponse->addAssign("msgZone", "innerHTML", "The record could not be updated");
		}
	}else{
		$objResponse->addAlert($message);
	}
	
	return $objResponse->getXML();
}

function updateField($table, $field, $cell, $value, $id){
	$objResponse = new xajaxResponse();
	$objResponse->addAssign($cell, "innerHTML", $value);

	Customer::updateField($table,$field,$value,$id);
	return $objResponse->getXML();
}

function editField($table, $field, $cell, $value, $id){
	$objResponse = new xajaxResponse();
	
	$html =' <input type="text" id="input'.$cell.'" value="'.$value.'" size="'.(strlen($value)+5).'"'
			.' onBlur="xajax_updateField(\''.$table.'\',\''.$field.'\',\''.$cell.'\',document.getElementById(\'input'.$cell.'\').value,\''.$id.'\');"'
			.' style="background-color: #CCCCCC; border: 1px solid #666666;">';
	$objResponse->addAssign($cell, "innerHTML", $html);
	$objResponse->addScript("document.getElementById('input$cell').focus();");
	return $objResponse->getXML();
}

function add($callerid = null,$customerid = null,$contactid = null){
	global $locate;
	$objResponse = new xajaxResponse();
//	return $objResponse;

	$html = Table::Top($locate->Translate("add_record"),"formDiv");  // <-- Set the title for your form.
//	$html .= Customer::formAdd($callerid,$customerid,$contactid);  // <-- Change by your method
	$html .= Customer::formAdd($callerid,$customerid,$contactid);
//	$objResponse->addAlert($callerid);
	// End edit zone
	$html .= Table::Footer();
	$objResponse->addAssign("formDiv", "style.visibility", "visible");
	$objResponse->addAssign("formDiv", "innerHTML", $html);
	
	return $objResponse->getXML();
}

function showGrid($start = 0, $limit = 1,$filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	
	$html = createGrid($start, $limit,$filter, $content, $order, $divName, $ordering);
	$objResponse = new xajaxResponse();
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);
	
	return $objResponse->getXML();
}

/**
*  show edit form
*  @param	id			int			id
*  @param	type		sting		customer/contact/note
*  @return	objResponse	object		xajax response object
*/

function edit($id = null, $type = "note"){
	global $locate;

	// Edit zone
	$html = Table::Top($locate->Translate("edit_record"),"formEditInfo");
	$html .= Customer::formEdit($id, $type);
	$html .= Table::Footer();
   	// End edit zone

	$objResponse = new xajaxResponse();
	$objResponse->addAssign("formEditInfo", "style.visibility", "visible");
	$objResponse->addAssign("formEditInfo", "innerHTML", $html);
	return $objResponse->getXML();
}

?>
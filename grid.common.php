<?

function showCustomer($id = null, $type="customer"){
	global $locate;
	if($id != null){
		$html = Table::Top($locate->Translate("customer_detail"),"formCustomerInfo"); 			
		$html .= Customer::showCustomerRecord($id,$type); 		
		$html .= Table::Footer();
		$objResponse = new xajaxResponse();
		$objResponse->addAssign("formCustomerInfo", "style.visibility", "visible");
		$objResponse->addAssign("formCustomerInfo", "innerHTML", $html);	
		return $objResponse->getXML();
	}
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

function save($f){
	$objResponse = new xajaxResponse();
	global $locate;
//	print $f['surveyoption'];
//	exit;
	if (empty($f['customer']) && empty($f['contact']))
		return $objResponse;
	if(empty($f['customer'])) {
//		$objResponse->addAlert($locate->Translate("customer_cant_null"));
//		return $objResponse;
		$customerID = 0;
	} else{
		if ($f['customerid'] == '' || $f['customerid'] == 0){
			$respOk = Customer::insertNewCustomer($f); // insert a new customer record
//			$objResponse->addAlert($respOk);
//			return $objResponse;
			if (!$respOk){
				$objResponse->addAlert($locate->Translate("customer_add_error"));
				return $objResponse;
			}
			$objResponse->addAlert($locate->Translate("a_new_customer_added"));
		} else{
//			$respOk = Customer::updateCustomerRecord($f); // update a customer record
//			if (!$respOk){
//				$objResponse->addAlert($locate->Translate("customer_update_error"));
//				return $objResponse;
//			}
			$respOk = $f['customerid'];
		}
		$customerID = $respOk;
	}

	if(empty($f['contact'])) {
		$contactID = 0;
	} else{
		if ($f['contactid'] == ''){
			$respOk = Customer::insertNewContact($f,$customerID); // insert a new contact record
			if (!$respOk){
				$objResponse->addAlert($locate->Translate("contact_add_error"));
				return $objResponse;
			}
			$objResponse->addAlert($locate->Translate("a_new_contact_added"));
		}else{
//			$respOk = Customer::updateContactRecord($f); // update a contact record
//			if (!$respOk){
//				$objResponse->addAlert($locate->Translate("contact_update_error"));
//				return $objResponse;
//			}
			$respOk = $f['contactid'];
			//获取该contact的customerid
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

function update($f, $type){
	$objResponse = new xajaxResponse();

	if ($type == 'note'){
		$respOk = Customer::updateNoteRecord($f,"append");
	}elseif ($type == 'customer'){
		if (empty($f['customer']))
			$message = "The field Customer does not have to be null";
		else
			$respOk = Customer::updateCustomerRecord($f);
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

?>
<?php
/*******************************************************************************
* import.server.php
* import函数信息文件
* import parameter file
* 功能描述
* Function Desc
	init()  页面初始化
	selectTable()  选择表
	submitForm()  将csv，xsl格式文件数据插入数据库
	showDivMainRight() 显示csv，xsl格式文件数据
	getGridData() 得到显示csv，xsl格式文件数据的HTML语法
	getDiallistBar() 得到显示diallist导入框的HTML语法
	getImportResource() 得到要插入表的sql语句，存入数组
	parseRowToSql() 得到sql语句和分区，存入数组
	getSourceData()得到excel文件的所有行数据，返回数组

* Revision 0.046  2007/11/8 8:33:00  modified by yunshida
* 描述: 取消了session的使用, 重新整理了流程

* Revision 0.045  2007/10/22 13:39:00  modified by yunshida
* Desc:
* 描述: 增加了包含include/common.class.php, 在init函数中增加了初始化对象divNav和divCopyright


* Revision 0.045  2007/10/18 15:25:00  modified by yunshida
* Desc: page create
* 描述: 页面建立

********************************************************************************/
require_once ("db_connect.php");
require_once ("import.common.php");
require_once ('include/excel.class.php');
require_once ('include/common.class.php');
require_once ('include/astercrm.class.php');
/**
*  function to init import page
*
*
*  @return $objResponse
*
*/
function init($fileName){
	global $locate,$config;
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("spanSelectFile","innerHTML", $locate->Translate("please_select_file"));
	
	$file_list = getExistfilelist();
	$objResponse->addScript("addOption('filelist','0','".$locate->Translate('select a existent file')."');");
	foreach ( $file_list as $value ) {
		$objResponse->addScript("addOption('filelist','".$value."','".$value."');");
	}
	$objResponse->addAssign("btnUpload","value",$locate->Translate("upload"));
	$objResponse->addAssign("btnImportData","value",$locate->Translate("import"));

	$objResponse->addAssign("spanFileManager","innerHTML", $locate->Translate("file_manager"));

	$objResponse->addAssign("hidOnUploadMsg","value",$locate->Translate("uploading"));
	$objResponse->addAssign("hidOnSubmitMsg","value",$locate->Translate("data_importing"));

	if ($_SESSION['curuser']['usertype'] == 'admin') {
		$tableList = "<select name='sltTable' id='sltTable' onchange='selectTable(this.value);' >
							<option value=''>".$locate->Translate("selecttable")."</option>
							<option value='resellerrate'>resellerrate</option>
							<option value='callshoprate'>callshoprate</option>
							<option value='myrate'>myrate</option>
						</select>";
	}elseif($_SESSION['curuser']['usertype'] == 'reseller'){
		$tableList = "<select name='sltTable' id='sltTable' onchange='selectTable(this.value);' >
							<option value=''>".$locate->Translate("selecttable")."</option>
							<option value='callshoprate'>callshoprate</option>
							<option value='myrate'>myrate</option>
						</select>";
	}elseif($_SESSION['curuser']['usertype'] == 'groupadmin'){
		$tableList = "<select name='sltTable' id='sltTable' onchange='selectTable(this.value);' >
							<option value=''>".$locate->Translate("selecttable")."</option>
							<option value='myrate'>myrate</option>
						</select>";
	}


	$objResponse->addAssign("divTables","innerHTML",$tableList);
	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin));
	$objResponse->addAssign("divGrid", "innerHTML", '');
	//$objResponse->addScript("xajax_showDivMainRight(document.getElementById('hidFileName').value);");
	//$objResponse->loadXML(showDivMainRight($fileName));
	//$objResponse->addAssign("divDiallistImport", "innerHTML", '');

	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));
	$objResponse->loadXML(showDivMainRight($fileName));

	if ($_SESSION['curuser']['usertype'] == 'admin') {

		// add all reseller
		$res = astercrm::getAll('resellergroup');
		$objResponse->addScript("addOption('resellerid','0','".$locate->Translate("All")."');");
		while ($row = $res->fetchRow()) {
			$objResponse->addScript("addOption('resellerid','".$row['id']."','".$row['resellername']."');");
		}

	}elseif($_SESSION['curuser']['usertype'] == 'reseller'){
		// add self
		$objResponse->addScript("addOption('resellerid','".$_SESSION['curuser']['resellerid']."','".""."');");
		// add groups
		$objResponse->addScript("addOption('groupid','0','".$locate->Translate("All")."');");
		$res = astercrm::getAll('accountgroup',"resellerid",$_SESSION['curuser']['resellerid']);
		while ($row = $res->fetchRow()) {
			$objResponse->addScript("addOption('groupid','".$row['id']."','".$row['groupname']."');");
		}
	}else{
		// add self
		$objResponse->addScript("addOption('resellerid','".$_SESSION['curuser']['resellerid']."','".""."');");
		$objResponse->addScript("addOption('groupid','".$_SESSION['curuser']['groupid']."','".""."');");
	}


	$objResponse->addScript("setCampaign();");

	return $objResponse;
}

function getExistfilelist(){
	global $locate,$config;
	$uploaddir = opendir($config['system']['upload_file_path']);
	$file_list = array();
	while( false !== ($files = readdir($uploaddir)) ) {
		$ext_tmp = split("\.",$files);
		$n = count($ext_tmp) - 1;
		$ext =  $ext_tmp[$n];
		$ext = strtolower($ext);
		if ( $ext == 'xls' ||  $ext == 'csv' )
			$file_list[] = $files;
	}
	return $file_list;
}

function setGroup($resellerid){
	global $locate;
	$objResponse = new xajaxResponse();
	$res = astercrm::getAll("accountgroup",'resellerid',$resellerid);
	$objResponse->addScript("addOption('groupid','0','".$locate->Translate("All")."');");
	//添加option
	while ($res->fetchInto($row)) {
		$objResponse->addScript("addOption('groupid','".$row['id']."','".$row['groupname']."');");
	}
	return $objResponse;
}

/**
*  function to show divMainRight
*
*  @para	$filename		string
*  @return	$objResponse	object
*
*/
function showDivMainRight($filename){
	global $locate,$config;
	$objResponse = new xajaxResponse();

	$filePath = $config['system']['upload_file_path'].$filename;

	if(is_file($filePath)){	//check if file exsits
		$dataContent = getGridHTML($filePath);
		$objResponse->addAssign("divGrid", "innerHTML", $dataContent['gridHTML']);

		$diallistBar = getDiallistBar($dataContent['columnNumber']);
		$objResponse->addAssign("divDiallistImport", "innerHTML", $diallistBar);

		$objResponse->addAssign("btnImportData", "disabled", false);
	}else{
		$objResponse->addAssign("divDiallistImport", "innerHTML", '');
		$objResponse->addAssign("divGrid", "innerHTML", '');
		$objResponse->addAssign("divMessage", "innerHTML",'');
	}

	return $objResponse;
}

/**
*  function to show table div
*
*  	@param $table	string		tablename
															customer
															contact
*  @return $objResponse
*
*/

function selectTable($tableName){
	global $locate,$db;

	$tableStructure = astercrm::getTableStructure($tableName);

	$HTML .= "<ul class='ulstyle'>";
	$i = 0;
	foreach($tableStructure as $row){
		$type_arr = explode(' ',$row['flags']);
		if(!in_array('auto_increment',$type_arr))
		{
			if ($row['name'] == "creby" || $row['name'] == "cretime" || $row['name'] == "addtime" || $row['name'] == "groupid" || $row['name'] == "resellerid"){

			}else{
				$HTML .= "<li height='20px'>";
				$HTML .= $i.":&nbsp;&nbsp;".$row['name'];
				$HTML .= "</li>";
				$i++;
			}
		}
	}
	$HTML .= "</ul>";
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("divTableFields", "innerHTML", $HTML);
	$objResponse->addAssign("hidTableName","value",$tableName);
	$objResponse->addAssign("hidMaxTableColumnNum","value",$i-1);
	return $objResponse;
}

/**
*  function to insert data to database from excel
*
*  	@param $aFormValues	(array)			insert form excel
	if import datas to diallist					$aFormValues['chkAdd']
	if assign extnesion to phone numbers		$aFormValues['chkAssign']
	assign which extensions to phone numbers	$aFormValues['assign']
	import which field							$aFormValues['dialListField']
*	@return $objResponse
*
*/

function submitForm($aFormValues){
	global $locate,$db,$config;
	$objResponse = new xajaxResponse();
	$order = $aFormValues['order']; //得到的排序数字，数组形式，要添加到数据库的列
	$fileName = $aFormValues['hidFileName'];
	$tableName = $aFormValues['hidTableName'];
	$flag = 0;
	foreach($order as $value){  //判断是否有要导入的数据
		if(trim($value) != ''){
			$flag = 1;
			break;
		}
	}
	//如果没有任何选择, 就退出
	if($flag != 1){
		$objResponse->addScript('init();');
		return $objResponse;
	}
	
	//对提交的数据进行校验
	$orderNum = count($order);
	if($orderNum > 0)			//如果要导入表
	{
		$arrRepeat = array_count_values($order);
		foreach($arrRepeat as $key=>$value){
			if($key != '' && $value > 1){	//数据重复
				$objResponse->addAlert($locate->Translate('field_cant_repeat'));
				$objResponse->addScript('init();');
				return $objResponse;
			}
		}
	}
	for($j=0;$j<$orderNum;$j++){
		if(trim($order[$j]) != ''){
			if(trim($order[$j]) > $aFormValues['hidMaxTableColumnNum']){  //最大值校验
				$objResponse->addAlert($locate->Translate('field_overflow'));
				$objResponse->addScript('init();');
				return $objResponse;
			}
			if (!ereg("[0-9]+",trim($order[$j]))){ //是否为数字
				$objResponse->addAlert($locate->Translate('field_must_digits'));
				$objResponse->addScript('init();');
				return $objResponse;
			}
		}
	}

	$tableStructure = astercrm::getTableStructure($tableName);
	$filePath = $config['system']['upload_file_path'].$fileName;//数据文件存放路径

	$affectRows= 0;  //计数据库影响结果变量
	$x = 0;  //计数变量
	$date = date('Y-m-d H:i:s'); //当前时间
	$groupid = $aFormValues['groupid'];
	$resellerid = $aFormValues['resellerid'];
	//$campaignid = $aFormValues['campaignid'];
	//print $groupid;

	$x = 0;
	$arrData = getImportResource($filePath,$order,$tableName,$tableStructure,$dialListField,$date,$groupid,$resellerid);
	foreach($arrData as $data){
		$strSql = $data['strSql'];					//得到插入选择表的sql语句
		//print $strSql;
		//exit;

		if($tableName != '' && $strSql != '' ){
			$res = $db->query($strSql);  
			$tableAffectRows += $db->affectedRows();   //得到影响的数据条数
		}
	}
	if($tableAffectRows< 0){
		$tableAffectRows= 0;
	}

	$resultMsg = $tableName.' : '.$tableAffectRows.' '.$locate->Translate('records_inserted')."<br>";

	//delete upload file
	//@ unlink($filePath);

	$objResponse->addAlert($locate->Translate('success'));
	$objResponse->addScript("document.getElementById('btnImportData').disabled = false;");
	$objResponse->addAssign("divResultMsg", "innerHTML",$resultMsg);
	$objResponse->addScript("init();");
	return $objResponse;
}

/**
*  function to show divDiallistImport
*/
function getDiallistBar($columnNum){
	global $locate;
	$HTML = "";
	$HTML .= "<br />";
	$HTML .= "
					<table cellspacing='0' cellpadding='0' border='0' width='100%' style='text-align:center;'>
						<tr>
							<td>
								<input type='checkbox' value='1' name='chkAdd' id='chkAdd' onclick='chkAddOnClick();'/>
								&nbsp;&nbsp; ".$locate->Translate('add')."
								<select name='dialListField' id='dialListField' disabled>
									<option value=''></option>";
	for ($c=0; $c < $columnNum; $c++) {
		$HTML .= "<option value='$c'>$c</option>";
	}
	$HTML .= "
								</select> ".$locate->Translate('todiallist')." &nbsp;&nbsp;
								<input type='checkbox' value='1' name='chkAssign' id='chkAssign' onclick='chkAssignOnClick();' disabled/> ".$locate->Translate('area')."
								<input type='text' name='assign' id='assign' style='border:1px double #cccccc;width:200px;heiht:12px;' disabled />
							</td>
						</tr>
					</table>";
	return $HTML;
}


function getImportResource($filePath,$order,$tableName,$tableStructure,$dialListField,$date,$groupid,$resellerid){
	$arrData = getSourceData($filePath);
	foreach($arrData as $arrRow){
		$arrAll[] = parseRowToSql($arrRow,$order,$dialListField,$tableStructure,$tableName,$date,$groupid,$resellerid);
	}
	return $arrAll;
}

//循环列数据，得到sql
function parseRowToSql($arrRow,$order,$dialListField,$tableStructure,$tableName,$date,$groupid,$resellerid){
	$fieldName = '';
	$strData = '';
	//print_r($tableStructure);
	//exit;
	for ($j=0;$j<count($arrRow);$j++)
	{
		//if ($arrRow[$j] != mb_convert_encoding($arrRow[$j],"UTF-8","UTF-8"))
		//	$arrRow[$j]=mb_convert_encoding($arrRow[$j],"UTF-8","GB2312");

		$fieldOrder = trim($order[$j]);//得到字段顺序号

		if($fieldOrder != ''){
			//print $filedOrder;
			$fieldName .= $tableStructure[$fieldOrder]['name'].',';
			if ( $tableStructure[$fieldOrder]['name'] == 'billingblock' ) { //是否选择了billingblock 字段
				$billingblock_flag = 'yes';
			}
			$strData .= '"'.$arrRow[$j].'"'.',';
		}
		if(isset($dialListField) && $dialListField != ''){
			if($dialListField == $j)
				$dialListValue = $arrRow[$j];
		}
	}
	if ( $billingblock_flag != 'yes' ) {
		$fieldName .= 'billingblock,';
		$strData .= '"60",';
	}
	$fieldName = substr($fieldName,0,strlen($fieldName)-1);
	$strData = substr($strData,0,strlen($strData)-1);

	if ($fieldName != ""){
		if ($tableName == 'resellerrate'){
			$strSql = "INSERT INTO $tableName ($fieldName,addtime,resellerid) VALUES ($strData, now(), '$resellerid')";
		}elseif ($tableName == 'callshoprate' || $tableName == 'myrate'){
			$strSql = "INSERT INTO $tableName ($fieldName,addtime,resellerid,groupid) VALUES ($strData, now(), '$resellerid', '$groupid')";
		}
	}
	return array('strSql'=>$strSql,'dialListValue'=>$dialListValue);
}

function csv_string_to_array($str){

   $expr="/,(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/";

   $results=preg_split($expr,trim($str));

    return preg_replace(array("/^\"(.*)\"$/","/\"\"/"),array("$1",""),$results);
}


//得到excel文件的所有行数据，返回数组结构的数据

/**
*	get file data from a file
*	@param		$filePath			filepath, could be a csv file or a xsl file
*	@return		$arrData			data in the file
**/
function getSourceData($filePath){  
	$type = substr($filePath,-3);
	if($type == 'csv'){  //csv 格式文件
		$handle = fopen($filePath,"r");  //打开csv文件,得到句柄
		while (($data = fgets($handle)) !== FALSE) { 
			$arrData[] = csv_string_to_array($data);
		}

	}elseif($type == 'xls'){  //xls格式文件
		Read_Excel_File($filePath,$return);
		for ($i=0;$i<count($return[Sheet1]);$i++){
			$arrData[] = $return[Sheet1][$i];
		}
	}
	return $arrData;
}


/**
*	get HTML codes for a file
*	@param		$filePath		string		filepath, could be a csv file or a xsl file
*	@return		$HTML			array		
*								array['gridHTML']		HTML code to display a grid table
*								array['columnNumber']	columnNumber of the data
**/

function getGridHTML($filePath){
	$data_array = getSourceData($filePath);
	$row = 0;
	$HTML .= "<table cellspacing='1' cellpadding='0' border='0' width='100%'		style='text-align:left'>";
	foreach($data_array as $data_arr){
		$num = count($data_arr);
		$row++;
		$HTML .= "<input type='hidden' name='CHECK' value='1'/>";
		
		$HTML .= "<tr>";
		for ($c=0; $c < $num; $c++)
		{
			//if ($data_arr[$c] != mb_convert_encoding($data_arr[$c],"UTF-8","UTF-8"))
			//		$data_arr[$c]=mb_convert_encoding($data_arr[$c],"UTF-8","GB2312");
			if($row % 2 != 0){
				$HTML .= "<td bgcolor='#ffffff' height='25px'>&nbsp;".trim($data_arr[$c])."</td>";
			}else{
				$HTML .= "<td bgcolor='#efefef' height='25px'>&nbsp;".trim($data_arr[$c])."</td>";
			}
		}
		$HTML .= "</tr>";
		if($row == 8)
			break;
	}
	$HTML .= "<tr>";
	for ($c=0; $c < $num; $c++) {
		$HTML .= "<td bgcolor='#F0F8FF' height='25px'>
						&nbsp;<input type='text' style='width:20px;border:1px double #cccccc;height:12px;' name='order[]'  />
					</td>";
	}
	$HTML .= "</tr>";
	$HTML .= "<tr>";
	for ($c=0; $c < $num; $c++) {
		$HTML .= "<td height='20px' align='left'><font color='#000000'><b>$c</b></font></td>";
	}
	$HTML .= "</tr>";
	$HTML .= "</table>";

	
	return array('gridHTML'=>$HTML,'columnNumber'=>$num);
}

$xajax->processRequests();

?>

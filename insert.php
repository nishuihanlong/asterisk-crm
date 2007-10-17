<?php
	session_start();
	require_once ('include/Localization.php');
	require_once ("include/excel_class.php");
	$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'csv');
	if(isset($_POST['CHECK']) && trim($_POST['CHECK']) == '1'){
		$file_name = $_SESSION['filename'];
		$type = substr($file_name,-3);
		include_once('config.php');
		if($_SESSION['action'] == 'customer'){
			$table = 'customer';
		}elseif($_SESSION['action'] == 'contact'){
			$table = 'contact';
		}
		$order = $_POST['order'];
		for($j=0;$j<count($order);$j++){
			if(trim($order[$j]) != ''){
				if(trim($order[$j]) > $_SESSION['MAX_NUM']){
					$font = $locate->Translate('font');
					alert($font);
					return;
				}
				if($_SESSION['edq'] == $order[$j]){
					$repeat = $locate->Translate('repeat');
					alert($repeat);
					return;
				}else{
					$_SESSION['edq'] = $order[$j];
				}
			}
		}
		$link = mysql_connect($config['database']['dbhost'],$config['database']['username'],
$config['database']['password']);
		$dbname = $config['database']['dbname'];
		mysql_select_db($dbname, $link) or die("Could not set $dbname: " . mysql_error());
		$res = mysql_query("select * from $table", $link);
		$fields_num = mysql_num_fields($res);
		$file_path = UPLOAD_IMAGE_PATH.$_SESSION['filename'];
		$handle = fopen($file_path,"r");
		$v = 0;
		$date = date('Y-m-d H:i:s');
		//********************************
		if($_POST['myCheckBox'] != '' && $_POST['myCheckBox'] == '1'){
			$mytext = trim($_POST['mytext']); //数字
			//$field_name = mysql_field_name($res, $mytext);
		}
		if($_POST['myCheckBox2'] != '' && $_POST['myCheckBox2'] == '1'){
			$mytext2 = trim($_POST['mytext2']); //分区,以','号分隔的字符串
			$area_array = explode(',',$mytext2);
			$area_num = count($area_array);//得到分区数
		}
		//********************************
		if($type == 'csv'){
			while($data = fgetcsv($handle, 1000, ",")){
				$row_num_csv = count($data);  
				$v++;
				$mysql_field_name = '';
				$data_str = '';
				for($i=0;$i<$row_num_csv;$i++){
					if ($data[$i] != mb_convert_encoding($data[$i],"UTF-8","UTF-8"))
						$data[$i]=mb_convert_encoding($data[$i],"UTF-8","GB2312");
					$field_order = trim($order[$i]);//字段顺序号
					if($field_order != ''){
						$mysql_field_name .= @mysql_field_name($res, $field_order).',';
						$data_str .= '"'.$data[$i].'"'.',';
					}
					if(isset($mytext) && $mytext != ''){
						if($mytext == $i)
							$array = $data[$i];
					}
				}
				$mysql_field_name = substr($mysql_field_name,0,strlen($mysql_field_name)-1);
				$data_str = substr($data_str,0,strlen($data_str)-1);
				$sql_str = "insert into $table ($mysql_field_name,cretime,creby) values ($data_str,'".$date."','".$_SESSION['curuser']['username']."')";

				if(isset($mytext) && trim($mytext) != ''){
					if($mytext2 != '' && isset($mytext2)){
						$random_num = rand(0,$area_num-1);
						$random_area = $area_array[$random_num];
						$sql_string = "insert into diallist (dialnumber,assign) values ('".$array."','".$random_area."')";
					}else{
						$sql_string = "insert into diallist (dialnumber) values ('".$array."')";
					}
				}
				$rs = @mysql_query($sql_str);
				$rs2 = @mysql_query($sql_string);
			}
		}elseif($type == 'xls'){
			Read_Excel_File($file_path,$return);
			for ($i=0;$i<count($return[Sheet1]);$i++)
			{
				$mysql_field_name = '';
				$data_str = '';
				$row_num_xls = count($return[Sheet1][$i]);  //列数
				for ($j=0;$j<$row_num_xls;$j++)
				{
					if ($return[Sheet1][$i][$j] != mb_convert_encoding($return[Sheet1][$i][$j],"UTF-8","UTF-8"))
						$return[Sheet1][$i][$j]=mb_convert_encoding($return[Sheet1][$i][$j],"UTF-8","GB2312");
					$field_order = trim($order[$j]);//得到字段顺序号
					if($field_order != ''){
						$mysql_field_name .= @mysql_field_name($res, $field_order).',';
						$data_str .= '"'.$return[Sheet1][$i][$j].'"'.',';
					}
					if(isset($mytext) && $mytext != ''){
						if($mytext == $i)
							$array = $return[Sheet1][$i][$j];
					}
				}
				$mysql_field_name = substr($mysql_field_name,0,strlen($mysql_field_name)-1);
				$data_str = substr($data_str,0,strlen($data_str)-1);
				$sql_str = "insert into $table ($mysql_field_name,cretime,creby) values ($data_str,'".$date."','".$_SESSION['curuser']['username']."')";

				if(isset($mytext) && trim($mytext) != ''){
					if($mytext2 != '' && isset($mytext2)){
						$random_num = rand(0,$area_num-1);
						$random_area = $area_array[$random_num];
						$sql_string = "insert into diallist (dialnumber,assign) values ('".$array."','".$random_area."')";
					}else{
						$sql_string = "insert into diallist (dialnumber) values ('".$array."')";
					}
				}
				$rs = @mysql_query($sql_str);
				$rs2 = @mysql_query($sql_string);
			}
		}
		unset($_SESSION['filename']);
		alert($locate->Translate('success'));
	}
	function alert($str){
		echo  "<script language='javascript'>";
		echo  "alert('".$str."');";
		echo  "javascript:history.go(-1);";
		echo  "</script>";
	}

?>
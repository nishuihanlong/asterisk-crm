<?php
/*
	1: id
	2: customer
	3: address
	4: zipcode
	5: website
	6: category
	7: city
	8: state
	9: phone
	10: contact
	11: contactgender
	12: cretime
	13: creby
*/
session_start();
	if(isset($_POST['CHECK']) && trim($_POST['CHECK']) == '1'){
		include_once('config.php');
		$order = $_POST['order'];
		for($j=0;$j<count($order);$j++){
			if(trim($order[$j]) != ''){
				if(trim($order[$j]) > $_SESSION['MAX_NUM']){
					alert("字段号超过指定范围！");
					return;
				}
				if($_SESSION['edq'] == $order[$j]){
					alert("不允许添加重复字段号！");
					return;
				}else{
					$_SESSION['edq'] = $order[$j];
				}
			}
		}
		$link = mysql_connect('localhost', "asteriskuser", "movingon");
		$dbname = "asterisk";
		mysql_select_db($dbname, $link) or die("Could not set $dbname: " . mysql_error());
		$res = mysql_query("select * from contact", $link);
		$fields_num = mysql_num_fields($res);
		$file_path = UPLOAD_IMAGE_PATH.$_SESSION['filename'];
		$handle = fopen($file_path,"r");
		$v = 0;
		$date = date('Y-m-d H:i:s');
		//********************************
		if($_POST['myCheckBox'] != '' && $_POST['myCheckBox'] == '1'){
			$mytext = trim($_POST['mytext']); //数字
			$field_name = mysql_field_name($res, $mytext);
		}
		if($_POST['myCheckBox2'] != '' && $_POST['myCheckBox2'] == '1'){
			$mytext2 = trim($_POST['mytext2']); //分区,以','号分隔的字符串
			$area_array = explode(',',$mytext2);
			$area_num = count($area_array);//得到分区数
		}
		//********************************
		while($data = fgetcsv($handle, 1000, ",")){
			$row_num_csv = count($data);  
			$v++;
			if($v != 1){
				$mysql_field_name = '';
				$data_str = '';
				for($i=0;$i<$row_num_csv;$i++){
					if ($data[$i] != mb_convert_encoding($data[$i],"UTF-8","UTF-8"))
						$data[$i]=mb_convert_encoding($data[$i],"UTF-8","GB2312");
					$field_order = trim($order[$i]);//字段顺序号
					if($field_order != ''){
						$mysql_field_name .= @mysql_field_name($res, $field_order).',';
						$data_str .= '"'.$data[$i].'"'.',';
						if($field_name == mysql_field_name($res, $field_order)){
							$array= $data[$i];
						}
					}
					
				}
				$mysql_field_name = substr($mysql_field_name,0,strlen($mysql_field_name)-1);
				$data_str = substr($data_str,0,strlen($data_str)-1);
				$sql_str = "insert into contact ($mysql_field_name,cretime,creby) values ($data_str,'".$date."','".$_SESSION['curuser']['username']."')";

				if(isset($field_name) && trim($field_name) != ''){
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
				if($rs && $rs2){
					//echo $sql_str;
					//echo '<br />';
				}else{
					echo '失败';
				}
				echo '<br />';
			}
		}
		unset($_SESSION['filename']);
		alert("操作成功");
	}
	function alert($str){
		echo  "<script language='javascript'>";
		echo  "alert('".$str."');";
		echo  "javascript:history.go(-1);";
		echo  "</script>";
	}

?>
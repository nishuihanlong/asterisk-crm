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
	if(isset($_POST['CHECK']) && trim($_POST['CHECK']) == '1'){
		include_once('config.php');
		$order = $_POST['order'];
		for($j=0;$j<count($order);$j++){
			if($order[$j] == ''){
				alert("不能为空");
				return;
			}/*elseif(!isDigit($order[$j])){
				echo "<script language='javascript'>";
				echo "alert('只能输入数字');";
				echo "javascript:history.go(-1);";
				echo "</script>";
				return;
			}*/
		}
		$link = mysql_connect('localhost', "asteriskuser", "movingon");
		$dbname = "asterisk";
		mysql_select_db($dbname, $link) or die("Could not set $dbname: " . mysql_error());
		$res = mysql_query("select * from customer", $link);
		$fields_num = mysql_num_fields($res);
		$file_path = UPLOAD_IMAGE_PATH.$_SESSION['filename'];
		$handle = fopen($file_path,"r");
		$v = 0;
		$date = date('Y-m-d H:i:s');
		while($data = fgetcsv($handle, 1000, ",")){
			$row_num_csv = count($data);  
			$v++;
			if($v != 1){
				$mysql_field_name = '';
				$data_str = '';
				for($i=0;$i<$row_num_csv;$i++){
					$field_order = $order[$i];
					$mysql_field_name .= mysql_field_name($res, $field_order).',';
					$data_str .= '"'.$data[$i].'"'.',';
				}
				$mysql_field_name = substr($mysql_field_name,0,strlen($mysql_field_name)-1);
				$data_str = substr($data_str,0,strlen($data_str)-1);
				$sql_str = "insert into customer ($mysql_field_name,cretime,creby) values ($data_str,'".$date."','".$_SESSION['curuser']['username']."')";
				$rs = @mysql_query($sql_str);
				if($rs){
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
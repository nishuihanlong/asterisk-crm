<?
/*******************************************************************************
* common.class.php
* 通用类
* common class

* Public Functions List

			generateCopyright	生成版权信息HTML代码
			generateManageNav	生成管理界面导航HTML代码
			generateTabelHtml	生成表格HTML代码
			read_ini_file
			write_ini_file

* Private Functions List


* Revision 0.0456  2007/11/15  modified by solo
* Desc: add two new function to operate ini file

* Revision 0.045  2007/10/18  modified by solo
* Desc: page created


********************************************************************************/


require_once ('localization.class.php');

if ($_SESSION['curuser']['country'] != '' ){
	$GLOBALS['locate_common']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['country'],'common.class');
}else{
	$GLOBALS['locate_common']=new Localization('en','US','common.class');
}


class Common{

	function generateCopyright($skin){
		global $locate_common;

		$html .='
				<div align="center">
					<table class="copyright" id="tblCopyright">
					<tr>
						<td>
							©2007 astercc - <a href="http://www.astercrm.org" target="_blank">astercc home</a><br>
							version: 0.04
						</td>
					</tr>
					</table>
				</dvi>
				';
		return $html;
	}

	function generateManageNav($skin){
		global $locate_common;
/*



	
		$html .= "<a href='contact.php' >".$locate_common->Translate("contact_manager")."</a> | ";
		
		$html .= "<a href='note.php' >".$locate_common->Translate("note_manager")."</a> | ";
		
		$html .= "<a href='diallist.php' >".$locate_common->Translate("diallist_manager")."</a> | ";

*/
		$html .= '<div class="">';
		$html .= 'Username:'.$_SESSION['curuser']['username'];
		$html .= '&nbsp;&nbsp;User type:'.$_SESSION['curuser']['usertype'];
		$html .= '</div>';

		$html .= '<div class="">';


		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$html .= '
							<a href="account.php">Account</a> | <a href="accountgroup.php">Account Group</a> | <a href="resellergroup.php">Reseller Group</a> | <a href="checkout.php" >Report</a> | <a href="rate.php" >Rate to Customer</a> | <a href="callshoprate.php">Rate to Callshop</a> | <a href="resellerrate.php">Rate to Reseller</a> | <a href="clid.php">Clid</a> | <a href="import.php">Import</a> | <a href="cdr.php">CDR</a>';
		}elseif($_SESSION['curuser']['usertype'] == 'reseller'){
			$html .= '
							<a href="account.php">Account</a> | <a href="accountgroup.php">Account Group</a> | <a href="checkout.php" >Report</a> | <a href="rate.php" >Rate to Customer</a> | <a href="callshoprate.php">Rate to Callshop</a> | <a href="resellerrate.php">Rate to Reseller</a> | <a href="clid.php">Clid</a> | <a href="import.php">Import</a> | <a href="cdr.php">CDR</a>';
		}elseif($_SESSION['curuser']['usertype'] == 'groupadmin'){
			$html .= '
							<a href="account.php">Account</a> | <a href="checkout.php" >Report</a> | <a href="callshoprate.php">Rate to Callshop</a> | <a href="rate.php" >Rate to Customer</a>  | <a href="clid.php">Clid</a> | <a href="import.php">Import</a> | <a href="cdr.php">CDR</a>';
		}elseif($_SESSION['curuser']['usertype'] == 'clid'){
			$html .= '&nbsp;&nbsp;<strong>Clid CDR</strong>';
		}else{
			// for operator 
			$html .= '
							<a href="checkout.php" >Report</a> | <a href="statistics.php" >Statistics</a> | <a href="rate.php" >Rate to Customer</a>';
		}

		$html .= ' | <a href="login.php" onclick="if (confirm(\'are u sure to exit?\')){}else{return false;}">Logout</a>';
		$html .= '</div>';

		return $html;
	}

//	生成显示一个数组内容的HTML代码
	function generateTabelHtml($aDyadicArray,$thArray = null){
		if (!is_Array($aDyadicArray))
			return '';
		$html .= "<table class='myTable'>";
		$myArray = array_shift($aDyadicArray);
		foreach ($myArray as $field){
			$html .= "<th>";
			$html .= $field;
			$html .= "</th>";
		}

		foreach ($aDyadicArray as $myArray){
			$html .="<tr>";
			foreach ($myArray as $field){
				$html .= "<td>";
				$html .= $field;
				$html .= "</td>";
			}
			$html .="</tr>";
		}
		$html .= "</table>";
		return $html;
	}

    function read_ini_file($f, &$r)
    {
        $null = "";
        $r=$null;
        $first_char = "";
        $sec=$null;
        $comment_chars=";#";
        $num_comments = "0";
        $num_newline = "0";

        //Read to end of file with the newlines still attached into $f
        $f = @file($f);
        if ($f === false) {
            return -2;
        }
        // Process all lines from 0 to count($f)
        for ($i=0; $i<@count($f); $i++)
        {
            $w=@trim($f[$i]);
            $first_char = @substr($w,0,1);
            if ($w)
            {
                if ((@substr($w,0,1)=="[") and (@substr($w,-1,1))=="]") {
                    $sec=@substr($w,1,@strlen($w)-2);
                    $num_comments = 0;
                    $num_newline = 0;
                }
                else if ((stristr($comment_chars, $first_char) == true)) {
                    $r[$sec]["Comment_".$num_comments]=$w;
                    $num_comments = $num_comments +1;
                }                
                else {
                    // Look for the = char to allow us to split the section into key and value
                    $w=@explode("=",$w);
                    $k=@trim($w[0]);
                    unset($w[0]);
                    $v=@trim(@implode("=",$w));
                    // look for the new lines
                    if ((@substr($v,0,1)=="\"") and (@substr($v,-1,1)=="\"")) {
                        $v=@substr($v,1,@strlen($v)-2);
                    }
                    
                    $r[$sec][$k]=$v;
                    
                }
            }
            else {
                $r[$sec]["Newline_".$num_newline]=$w;
                $num_newline = $num_newline +1;
            }
        }
        return 1;
    }

    function beginsWith( $str, $sub ) {
        return ( substr( $str, 0, strlen( $sub ) ) === $sub );
    } 

	
    function write_ini_file($path, $assoc_arr) {
        $content = "";
        foreach ($assoc_arr as $key=>$elem) {
            if (is_array($elem)) {
                if ($key != '') {
                    $content .= "[".$key."]\r\n";                    
                }
                
                foreach ($elem as $key2=>$elem2) {
                    if (Common::beginsWith($key2,'Comment_') == 1 && Common::beginsWith($elem2,';')) {
                        $content .= $elem2."\r\n";
                    }
                    else if (Common::beginsWith($key2,'Newline_') == 1 && ($elem2 == '')) {
                        $content .= $elem2."\r\n";
                    }
                    else {
                        $content .= $key2." = ".$elem2."\r\n";
                    }
                }
            }
            else {
                $content .= $key." = ".$elem."\r\n";
            }
        }

        if (!$handle = fopen($path, 'w')) {
            return -2;
        }
        if (!fwrite($handle, $content)) {
            return -2;
        }
        fclose($handle);
        return 1;
    }

}
?>
<?
class Localization{
	var $filePath;

	function Localization($language,$country,$page){
		$this->filePath = "language/".$page."_".$language."_".$country.".php";
	}
	

	function Translate($str){
		$source = $str;
		$filePath = dirname(dirname(__FILE__))."/include/".$this->filePath;
		if (file_exists($filePath)){
			require $this->filePath;
			$str =str_replace(" ","_",strtolower($str));
			if ($$str != "")
				return $$str;
			else
				return $source;
		}else{
			return $str;
		}
	}
}
?>
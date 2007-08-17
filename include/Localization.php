<?
class Localization
	{
	var $country;
	var $language;
	var $page;

	function Localization($language,$country,$page)
		{
		$this->country=$country;
		$this->language=$language;
		$this->page = $page;
		}
	 
	function Translate($str)
		{
		include ('language/'.($this->page)."_".($this->language)."_".($this->country).".inc");
		return $$str;
		}
	}
?>
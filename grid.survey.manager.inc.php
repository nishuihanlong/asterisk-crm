<?
require_once 'db_connect.php';
require_once 'survey.common.php';
require_once 'include/Localization.php';
require_once 'astercrm.php';

/** \brief Customer Class
*

*
* @author	Solo Fu <solo.fu@gmail.com>
* @version	1.0
* @date		13 July 2007
*/

class Customer extends astercrm
{

	/**
	*  Obtiene todos los registros de la tabla paginados.
	*
	*  	@param $start	(int)	Inicio del rango de la p&aacute;gina de datos en la consulta SQL.
	*	@param $limit	(int)	L&iacute;mite del rango de la p&aacute;gina de datos en la consultal SQL.
	*	@param $order 	(string) Campo por el cual se aplicar&aacute; el orden en la consulta SQL.
	*	@return $res 	(object) Objeto que contiene el arreglo del resultado de la consulta SQL.
	*/
	function &getAllRecords($start, $limit, $order = null, $creby = null){
		global $db;
		
		$sql = "SELECT * FROM survey";

		if($order == null){
			$sql .= " ORDER BY cretime DESC LIMIT $start, $limit";//.$_SESSION['ordering'];
		}else{
			$sql .= " ORDER BY $order ".$_SESSION['ordering']." LIMIT $start, $limit";
		}

		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}
	
	/**
	*  Obtiene todos registros de la tabla paginados y aplicando un filtro
	*
	*  @param $start		(int) 		Es el inicio de la p&aacute;gina de datos en la consulta SQL
	*	@param $limit		(int) 		Es el limite de los datos p&aacute;ginados en la consultal SQL.
	*	@param $filter		(string)	Nombre del campo para aplicar el filtro en la consulta SQL
	*	@param $content 	(string)	Contenido a filtrar en la conslta SQL.
	*	@param $order		(string) 	Campo por el cual se aplicar&aacute; el orden en la consulta SQL.
	*	@return $res		(object)	Objeto que contiene el arreglo del resultado de la consulta SQL.
	*/

	function &getRecordsFiltered($start, $limit, $filter = null, $content = null, $order = null, $ordering = ""){
		global $db;
		
		if(($filter != null) and ($content != null)){
			$sql = "SELECT * FROM survey "
					." WHERE ".$filter." like '%".$content."%' "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
		}
		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}
	
	/**
	*  Devuelte el numero de registros de acuerdo a los par&aacute;metros del filtro
	*
	*	@param $filter	(string)	Nombre del campo para aplicar el filtro en la consulta SQL
	*	@param $order	(string)	Campo por el cual se aplicar&aacute; el orden en la consulta SQL.
	*	@return $row['numrows']	(int) 	N&uacute;mero de registros (l&iacute;neas)
	*/
	
	function &getNumRows($filter = null, $content = null){
		global $db;
		
		$sql = "SELECT COUNT(*) AS numRows FROM survey ";
		
		if(($filter != null) and ($content != null)){
			$sql = 	"SELECT COUNT(*) AS numRows "
				."FROM survey "
				."WHERE ".$filter." like '%$content%'";
		}
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}


	function formAdd($surveyid = 0){
		global $locate;
		$html = '
				<!-- No edit the next line -->
				<form method="post" name="f" id="f">
				
				<table border="1" width="100%" class="adminlist">
				';

		$html .= '<tr><td colspan=2>
					'. $locate->Translate("survey_title") .'
				</td></tr>';

		if ($surveyid == 0){
			$html .= '<tr><td colspan=2>
						<input type="text" size="50" maxlangth="100" id="surveyname" name="surveyname"/>
					 </td></tr>';
		}else{
			$survey = Customer::getRecord($surveyid,'survey');
		   	$nameCell = "TitleCol";
//			$html .= '<tr><td colspan=2>
//						<input type="hidden" id="surveyid" name="surveyid" value="'.$surveyid.'"/>'.$survey['surveyname'].'
//					 </td></tr>';

			$html .= '<tr><td colspan="2" id="'.$nameCell.'" style="cursor: pointer;"  onDblClick="xajax_editField(\'survey\',\'surveyname\',\''.$nameCell.'\',\''.$survey['surveyname'].'\',\''.$survey['id'].'\');return false">'.$survey['surveyname'].'<input type="hidden" id="surveyid" name="surveyid" value="'.$surveyid.'"/></td></tr>';

		}

		$options = Customer::getOptions($surveyid);

		if ($options){
			$ind = 0;
			while	($options->fetchInto($row)){
				$nameRow = "formDivRow".$row['id'];
			   	$nameCell = $nameRow."Col".$ind;
				
				$html .= '<tr id="'.$nameRow.'" >'."\n";

//<td id="'.$nameCell.'" style="cursor: pointer;" '.$this->colAttrib[$ind-1].' onDblClick="xajax_editField(\''.$table.'\',\''.$fields[$ind-1].'\',\''.$nameCell.'\',\''.$value.'\',\''.$arr[0].'\');return false">'.$value.'</td>
				$html .= '
					<tr><td align="left" width="10%">'. $locate->Translate("option") .'
					</td><td id="'.$nameCell.'" style="cursor: pointer;"  onDblClick="xajax_editField(\'surveyoptions\',\'surveyoption\',\''.$nameCell.'\',\''.$row['surveyoption'].'\',\''.$row['id'].'\');return false">'.$row['surveyoption'].'</td></tr>
					';
				$ind++;

			}
		}

		$html .= '<tr><td colspan=2>
					'.$locate->Translate("option").'
				 </td></tr>';

		$html .= '<tr><td colspan=2>
					<input type="text" size="50" maxlength="100" id="surveyoption" name="surveyoption"/>
					<input type="button" value="'.$locate->Translate("add_record").'" onclick="addOption(\'f\');return false;">
				 </td></tr>';

		$html .= '
				</table>
				</form>
				';
		return $html;
	}

	function insertNewSurvey($surveyname){
		global $db;
		
		$sql= "INSERT INTO survey SET "
				."surveyname='".$surveyname."', "
				."cretime=now(), "
				."creby='".$_SESSION['curuser']['username']."'";
		astercrm::events($sql);
		$res =& $db->query($sql);
		$surveyid = mysql_insert_id();
		return $surveyid;
	}

	function insertNewOption($option,$surveyid){
		global $db;
		
		$sql= "INSERT INTO surveyoptions SET "
				."surveyoption='".$option."', "
				."surveyid='".$surveyid."', "
				."cretime=now(), "
				."creby='".$_SESSION['curuser']['username']."'";
		astercrm::events($sql);
		$res =& $db->query($sql);
		$optionid = mysql_insert_id();
		return $optionid;
	}

	function deleteSurvey($surveyid){

		Customer::deleteRecord($surveyid,'survey');
		global $db;
		
		$sql= "DELETE FROM surveyoptions "
				." WHERE "
				."surveyid = " . $surveyid ;
		$res =& $db->query($sql);
		return $res;
	}
}
?>
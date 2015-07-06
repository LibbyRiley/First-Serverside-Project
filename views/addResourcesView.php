<?php

//	This class adds new resources to the database.
	class AddResourcesView extends View 
	{
		protected function displayContent()
		{	//	display the content, including the form and resources that are already in the resources table.
		$html .= '<section class="content" id="add-resources">'."\n";
		
		//	check if the submit resource form has been submitted
		if($_POST['submitRes'])
		{
			$result = $this->model->processAddResource();
			
		/*	echo '<pre>';
			print_r($result);
			print_r($_FILES);
			print_r($_POST);
			echo '</pre>';	*/
			
		}
		
		//	display the resources form
		$html .= $this->displayResourcesForm('submitRes', 'Submit', $result, $_POST);
		//	display the resources that are already in the db
		$html .= $this->displayResources();
		
		$html .= '</section>'."\n";
		return $html;
		
		}
		
		public function displayResourcesForm($mode, $buttonName, $result, $resource)
		{	//	this function displays the resources form.
			//	when submitted, the values get inserted into the resources table.
			if(is_array($result))
			{
				extract($result);
			}
			extract($resource);
			$html .= '<div id="form">'."\n";
			$html .= '<div>'.$msg.'</div>';
			$html .= '	<form id="edit_form" method="post" action="'.
				htmlentities($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">'."\n";
			$html .= '		<fieldset>'."\n";
			$html .= '		<legend><h4>'.$this->pageInfo['pageHeading'].': </h4></legend>'."\n";
			$html .= '		<input type="hidden" name="MAX_FILE_SIZE" value="2000000" />'."\n";								
			//   the following 2 hidden fields are used for edit challenge
			$html .= '		<input type="hidden" name="resourceID" value="'.$resourceID.'" />'."\n";
			$html .= '		<label for="resourceTitle">Resource Title : </label>'."\n";
			$html .= '		<input type = "text" name="resourceTitle" id = "resourceTitle" value="'.htmlentities(stripslashes($resourceTitle),ENT_QUOTES).'" />'."\n";
			$html .= '		<div id="rTitleMsg" class="error"> '.$rTitleMsg.'</div>'."\n";		
			$html .= '		<div class="clear"></div>'."\n";
			$html .= '		<label for="resourceLink">URL : </label>'."\n";
			$html .= '		<input type = "text" name="resourceLink" id = "resourceLink" value="'.htmlentities(stripslashes($resourceLink),ENT_QUOTES).'" />'."\n";
			$html .= '		<div id="rLinkMsg" class="error"> '.$rLinkMsg.'</div>'."\n";		
			$html .= '		<div class="clear"></div>'."\n";
			
			$html .= '		<div><input class="submit" type="submit" name="'.$mode.'" value="'.$buttonName.'"/></div>'."\n";
			$html .= '		<div class="clear"></div>'."\n";
			$html .= '	</fieldset>'."\n";
			$html .= '</form>'."\n";
			$html .= '</div>'."\n";
			return $html;
		
		
		}
		
		
	
		
		private function displayResources()
		{	//	this function displays all of the resources (Title and Link) that are in the resources table.
		
			$this->resources = $this->model->getAllResources();
			$html = '';
			$html .= '<div class="resources" >'."\n";
			$html .= '	<h4>All Resources</h4>'."\n";
			foreach($this->resources as $resource)
			{
				$html .= '	<p class="resource-link"><a href="http://'.$resource['resourceLink'].'">'.$resource['resourceTitle'].'</a></p>'."\n";
			}
			$html .= '</div>'."\n";
		
			return $html;
		}
	}
?>
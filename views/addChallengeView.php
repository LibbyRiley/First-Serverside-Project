<?php

//	this class contains the method to display the page to allows admin to... 
//	add another challenge to the challenges table

class AddChallengeView extends View
{

	protected function displayContent()
	{	$html = '<section class="content" id="add-challenge">'."\n";
		//	check if admin is not logged in, and if so...
		//	restrict access
		if(!$this->model->adminLoggedIn)
		{
			$html .= '<p>Sorry, but this is a restricted page.</p>'."\n";
			return $html;
			
		}
			
		//	check if the resources form has been submitted
/*		if($_POST['submitResource'])
		{
			$result = $this->model->addResourcesToChallenge();
			
		}	*/
		//	check if the challenge form has been submitted
		if($_POST['Add'])
		{
			$result = $this->model->processAddChallenge();
			//	if challengeImage has been returned with a result array...
			//	store it in $_POST so we can display it on the form.
			if($result['challengeImage'])
			{
				$_POST['challengeImage'] = $result['challengeImage'];
			}
	/*		echo '<pre>';
			print_r($result);
			print_r($_FILES);
			print_r($_POST);
			echo '</pre>';		*/
			
			
		}
		$html .= $this->displayChallengeForm('Add','Add this Challenge!', $result, $_POST);
		$html .= $this->displayResourcesToCheck($_POST['resourceCheck']);
		$html .= '</section>'."\n";
		return $html;
	
	}

	protected function displayChallengeForm($mode, $buttonName, $result, $challenge)
	{	//	display the add challenge form.
		if(is_array($result))
		{
			extract($result);
		}
		extract($challenge);
		$html .= '<div id="form">'."\n";
		$html .= '<div>'.$msg.'</div>';
		$html .= '	<form id="edit_form" method="post" action="'.
			htmlentities($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">'."\n";
		$html .= '		<fieldset>'."\n";
		$html .= '		<legend><h4>'.$this->pageInfo['pageHeading'].': </h4></legend>'."\n";
		$html .= '		<input type="hidden" name="MAX_FILE_SIZE" value="2000000" />'."\n";								
		//   the following 2 hidden fields are used for edit challenge
		if ($mode == 'Edit')
		{
			$html .= '		<input type="hidden" name="challengeID" value="'.$challengeID.'" />'."\n";
			$html .= '		<input type="hidden" name="challengeImage" value="'.$challengeImage.'" />'."\n";
		}
		$html .= '		<label for="challengeTitle">Challenge Title : </label></br>'."\n";
		$html .= '		<input type = "text" name="challengeTitle" id = "challengeTitle" '.'value="'.htmlentities(stripslashes($challengeTitle),ENT_QUOTES).'" />'."\n";
		$html .= '		<div id="cTitleMsg" class="error"> '.$cTitleMsg.'</div>'."\n";		
		$html .= '		<div class="clear"></div>'."\n";
		$html .= '		<label for="challengeByline">Byline : </label>'."\n";
		$html .= '		<textarea rows=5" cols="30" name="challengeByline" id="challengeByline">'.
				htmlentities(stripslashes($challengeByline),ENT_QUOTES).'</textarea>'."\n";
		$html .= '		<div id="cBylineMsg" class="error"> '.$cBylineMsg.'</div>'."\n";
		$html .= '		<div class="clear"></div>'."\n";
		$html .= '		<label for="challengeDescription">Description : </label>'."\n";
		$html .= '		<textarea rows="10" cols="30" name="challengeDescription" id="challengeDescription">'.
				htmlentities(stripslashes($challengeDescription),ENT_QUOTES).'</textarea>'."\n";
		$html .= '		<div id="cdescMsg" class="error"> '.$cDescMsg.'</div>'."\n";
		$html .= '		<div class="clear"></div>'."\n";
		$html .= '		<label for="challengeResourceHeading">Resources Heading : </label></br>'."\n";;
		$html .= '		<input type = "text" name="challengeResourceHeading" id = "challengeResourceHeading" '.
				'value="'.htmlentities(stripslashes($challengeResourceHeading),ENT_QUOTES).'" />'."\n";
		$html .= '		<div id="rHeadingMsg" class="error"> '.$rHeadingMsg.'</div>'."\n";		
		$html .= '		<div class="clear"></div>'."\n";
		
		
		
		//	if the submit resources button has been clicked
		if($_POST['submitResource'])
		{
			$rsCheck = $_POST['resourceCheck'];
		
			$html .= 	$this->displayCheckedResources($rsCheck);
			
		}
		else if ($mode == 'Edit') {
			$rsCheck = $this->model->getResourceIdsByCID($challengeID);
		
		} else {
			$html .= '		<p><strong>Select the resources to go with this challenge from the list on the right.</strong></p>'."\n";
		}
		
		$html .= '		<div><label for="challengeImage">Upload Image : </label><br />'."\n";
		$html .= '		<input type="file" name="challengeImage" /></div>'."\n";
		$cImageMsg = $uploadMsg ? $uploadMsg : $cImageMsg;
		$html .= '		<div class="error"> '.$cImageMsg.'</div>'."\n";
		$html .= '		<div class="clear"></div>'."\n";
		
		//	check if the image is available, display it.
		if($challengeImage)
		{
			$html .= '		<div'."\n";
			$html .= '		<img src="uploads/thumbnails'.$challengeImage.'"/></div>'."\n";
		} else {
			$html .= '		<div>&nbsp;</div>'."\n";
		}
		
		$html .= '		<div><label for="challengeText">Text : </label><br />'."\n";
		$html .= '		<textarea rows=5" cols="30" name="challengeText">'.htmlentities(stripslashes($challengeText),ENT_QUOTES).'</textarea/></div>'."\n";
		$html .= '		<div class="error"> '.$cTextMsg.'</div>'."\n";
		$html .= '		<div class="clear"></div>'."\n";
		
		$html .= '		<div><input class="submit" type="submit" name="'.$mode.'" value="'.$buttonName.'"/></div>'."\n";
		$html .= '		<div class="clear"></div>'."\n";
		$html .= '	</fieldset>'."\n";
		$html .= '</form>'."\n";
	   
		
		$html .= '</div>'."\n";
		return $html;
	
	
	}
	
	public function displayResourcesToCheck($rsCheck)
	{	//	display the resources in a checklist on the right
		$this->resources = $this->model->getAllResources();
		$html = '';
		$html .= '<div class="resources" >'."\n";
		$html .= '	<h4>Select the resources to display with this challenge.</h4>'."\n";
		$html .= '	<form method="post" action="#">'."\n";
		foreach($this->resources as $resource)
		{
			$html .= '	<div class="resource">'."\n";
			if (is_array($rsCheck)) {
				if (in_array($resource['resourceID'], $rsCheck)) {	//	check the boxes that are already linked to a challenge on db
					$checked = ' checked="checked"';
				}
				else {
					$checked = '';
				}
			}	
			$html .= '	<input type="checkbox" class="resource" name="resourceCheck[]" value="'.$resource['resourceID'].'" '.$checked.'/> <strong>'.$resource['resourceTitle'].'</strong> </br>('.$resource['resourceLink'].')'."\n";
			$html .= '	</div>'."\n";
		}
		$html .= '	<input type="submit" name="submitResource" value="Submit Resources"/>'."\n";
		$html .= '	</form>'."\n";
		$html .= '</div>'."\n";
	
		return $html;
	
	}
	
	public function displayCheckedResources($rsCheck)
	{	//	put the ids of the resources that have been checked into hidden input fields.
		$html = '<fieldset>'."\n";
		$html .= '	<legend><strong>Resources</strong></legend>'."\n";
		$html .= $this->displayResourceTitles();
		foreach($rsCheck as $resourceID)
		{
			$html .= '	<input type="hidden" name="resource[]" value="'.$resourceID.'" />'."\n";
	
		}
		$html .= '</fieldset>'."\n";
		return $html;
	}
	
	public function displayResourceTitles() // 	will read the resources table for each resourceID
	{	
		$this->resources = $this->model->getResources($_POST['resourceCheck']);
		
		foreach($this->resources as $resource)
		{	//	displays each title that has been checked
			$html .= '<p>'.$resource['resourceTitle'].''."\n";
		}
		return $html;
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

}

?>
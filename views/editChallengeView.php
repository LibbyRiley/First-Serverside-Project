<?php

//	this class contains the method to display the page to allows admin to... 
//	EDIT the challenge from the challenges table based on the challengeID given on the url.

include 'views/addChallengeView.php';

class EditChallengeView extends AddChallengeView
{
	protected function displayContent()
	{   
		$html = '<section class="content" id="edit-challenge">'."\n";
	
		
		//	check if admin is not logged in, and if so...
		//	restrict access
		if(!$this->model->adminLoggedIn)
		{
			$html .= '<p>Sorry, but this is a restricted page.</p>'."\n";
			return $html;
			
		}
		
		$cID = $_POST['challengeID'] ? $_POST['challengeID'] : $_GET['cID'];
		
		//	check if the challenge form has been submitted
		if($_POST['Edit'])
		{
			$result = $this->model->processUpdateChallenge();
			$challenge = $_POST;
		//	echo '<pre>';
		//	print_r($result);
		//	print_r($_FILES);
		//	echo '</pre>';
			
			//	if challengeImage has been returned with a result array...
			//	store it in $_POST so we can display it on the form.
			if($result['challengeImage'])
			{
				$_POST['challengeImage'] = $result['challengeImage'];
			}
			
		} else {

			//	get the information for the latest challenge 
			//	(which is the one the admin is editing)
			$challenge = $this->model->getLatestChallenge($_GET['cID']);			
		}
		
		//	display the edit challenge form containing the challenge info in the fields
		$html .= $this->displayChallengeForm('Edit', 'Edit this Challenge', $result, $challenge);
		//	display the resources with the current ones for the challenge already checked
		//	and dispay in the form.
		
		$rsCheck = $this->model->getResourceIdsByCID($cID);
		$html .= $this->displayResourcesToCheck($rsCheck);
		$html .= '</section>'."\n";
		
		return $html;
	
	}
}

?>
<?php

	//	this class contains the method to dispay the latest challenge.
	class ChallengeView extends View 
	{
		
		private $challenge; // contains the challenge info
		private $cID;	// contains the challengeID
		
		protected function displayContent()
		{	
			$html = '<section class="content" id="challenge">'."\n";
			
			//	gets the latest challenge from the chalenges table on the db
			$this->challenge = $this->model->getLatestChallenge($cID);	
			
			//	checks to see if the admin is logged in
			if($this->model->adminLoggedIn)
			{	//	if the admin is logged in, displays edit/delete options
				$html .= '<div class="button"><a href="index.php?page=editChallenge&amp;cID='.$this->challenge['challengeID'].'">EDIT THIS CHALLENGE</a></div>'."\n";
				$html .= '<div class="button"><a href="index.php?page=deleteChallenge&amp;cID='.$this->challenge['challengeID'].'">DELETE THIS CHALLENGE</a></div>'."\n";
			}
			
			//	checks to see if there has been a challenge array returned
			if(is_array($this->challenge))
			{	
				$this->cID = $this->challenge['challengeID'];
				//	gets the resources that are for that challenge
				//	by selecting resources on the challenges_resources table that have current challengeID
				$this->resources = $this->model->getResourcesByCID($this->cID);	

				//	displays the challenge
				$html .= $this->displayChallenge($this->challenge);	
				
			} else {
			
				$html .= '<p>Sorry, there is no challenge available.</p>'."\n";
			}
			
			//	How to Play
			$html .= $this->displayHowToPlay();
			
			
			//	Resources for Challenge
			if(is_array($this->resources))
			{
				//	if there are resources for this challenge
				//	displays the resources
				$html .= $this->displayResources();
			} else {
				$html .= '<p>Sorry, there are no resources available.</p>'."\n";
			}
			
			$html .= '</div>'."\n";
			
			//	Resources for typesetting (constant)
			$html .= '			<div class="resources" id="typesetting">'."\n";
			$html .= '				<h4>Resources ~ Typesetting</h4>'."\n";
			$html .= '				<p class="resource-link"><a href="http://www.computerarts.co.uk/features/114-new-pro-tips-type">114 Tips for Type</a></p>'."\n";
			$html .= '				<p class="resource-link"><a href="http://typophile.com/node/12369">Typophile How-To Section</a></p>'."\n";
			$html .= '			</div>'."\n";
			$html .= '		</section>'."\n";
			
			return $html;
			
		}	
			
			
		private function displayChallenge($challenge)
		{	//	function that displays the challenge		
			$html = '';
			$challenge = $this->challenge;
			
			$html .= '<p class="date-content">'.$challenge['challengeDate'].' ~ Have a go at this week\'s challenge!</p>'."\n";
			$html .= '<div>'."\n";
			$html .= '	<h3>'.$challenge['challengeTitle'].'</h3>'."\n";
			$html .= '	<img class="challenge-image" src="uploads/images/'.$challenge['challengeImage'].'" alt="'.$challenge['challengeImageAlt'].'"/>'."\n";
			$html .= '	<h4>'.$challenge['challengeByline'].'</h4>'."\n";
			$html .= '	<p>'.$challenge['challengeDescription'].'</p>'."\n";	
			
			
			$html .= '<div class="button"><a href="index.php?page=challengeText&amp;cID='.$this->challenge['challengeID'].'">SEE TEXT</a></div>'."\n";
			$html .= '<div class="button"><a href="index.php?page=challengeEntries&amp;cID='.$this->challenge['challengeID'].'">SEE ENTRIES FOR THIS CHALLENGE</a></div>'."\n";
			
		
			return $html;
		}	
		
		
		private function displayResources()
		{	//	function that displays the resources
			$html = '';
			$html .= '<div class="resources" >'."\n";
			$html .= '	<h4>Resources ~ '.$this->challenge['challengeResourceHeading'].'</h4>'."\n";
			foreach($this->resources as $resource)
			{
				$html .= '	<p class="resource-link"><a href="http://'.$resource['resourceLink'].'">'.$resource['resourceTitle'].'</a></p>'."\n";
			}
		
			return $html;
		}
	}
?>
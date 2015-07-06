<?php
//	this class contains the method to display the page to allow the admin to delete
//	the challenge from the challenges table, together with the images based on the challenge id given in the url.

class DeleteChallengeView extends View
{

	private $msg;	// holds the message for the delete process
	protected function displayContent()
	{
		$html = '<section class="content" id="challenge">'."\n";
		//	check if admin is not logged in, and if so...
		//	restrict access
		if(!$this->model->adminLoggedIn)
		{
			$html .= '<p>Sorry, but this is a restricted page.</p>'."\n";
			return $html;
			
		}
		
		//	check if any of the form buttons have been clicked
		if($_POST['confirm']){	//	if deletion is confirmed
			$result = $this->model->processDeleteChallenge();
			$html .= '<p>'.$result['msg'].'</p>'."\n";
			return $html;
		} else if($_POST['cancel']) {	//	if deletion is cancelled
			header('Location: index.php?page=challenge');
		}
		
		$challenge = $this->model->getLatestChallenge($_GET['cID']);
		$html .= $this->displayDeleteChallengeForm($challenge);
		$html .= '		</section>'."\n";
		return $html;
	}	
			
			private function displayDeleteChallengeForm($challenge)
			{
				$html .= '<div id="delete-challenge">'."\n";
				$html .= '	<h3>'.$challenge['challengeTitle'].'</h3>'."\n";
				$html .= '	<h4>'.$challenge['challengeByline'].'</h4>'."\n";
				$html .= '	<img src="uploads/thumbnails/'.$challenge['challengeImage'].'" alt="'.htmlentities($challenge['challengeTitle'],ENT_QUOTES).'" /><p>'.$challenge['challengeDescription'].'</p>'."\n";
				$html .= '<div class="clear"></div>'."\n";
				$html .= '<div id="delete-form">'."\n";
				$html .= '<h4 class="orange">Do you want to delete this challenge?</h4>'."\n";
				$html .= '<form method="post" action="'.htmlentities($_SERVER['REQUEST_URI']).'">'."\n";
				$html .= '<input type="hidden" name="challengeID" value="'.$challenge['challengeID'].'" />'."\n";
				$html .= '<input type="hidden" name="challengeImage" value="'.$challenge['challengeImage'].'" />'."\n";
				$html .= '<div class="button"><input type="submit" class="submit" name="confirm" value="Yes" /></div>'."\n";
				$html .= '<div class="button"><input type="submit" class="submit" name="cancel" value="No" /></div>'."\n";
				$html .= '</form>'."\n";
				$html .= '</div>'."\n";
				$html .= '<p class="failedMessage">'.$this->msg.'</p>'."\n";
				$html .= '</div>'."\n";
				return $html;
			}
			
		
		

}
?>
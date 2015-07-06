<?php



class UploadDesignView extends View
{

	protected function displayContent()
	{
		$html .= '<section class="content" id="upload-design">'."\n";
		
		$this->user = $this->model->getUserByID($_SESSION['userID']);
		
		if(is_array($this->user))
		{
			$this->uID = $this->user['userID'];
			$challenge = $this->model->getLatestChallenge($_GET['cID']);
			
			
			if($_POST['Upload'])
			{
				$result = $this->model->processUploadDesign();
			
			}
			
			$html .= $this->displayUploadDesignForm('Upload', 'Upload Design!', $challenge, $result, $_POST);
			
			
			
			$html .= $this->displayChallengeThumb($challenge);
		} else {
		
			$html .= '<h4>Sorry, this page is unavailable to guests.</h4>'."\n";
			$html .= '<h4><a href="index.php?page=login" class="orange-hover">Login here</a> or <a href="index.php?page=signup" class="orange-hover">Sign up!</a></h4>'."\n";
		}
		
		$html .= '</section>'."\n";
		return $html;
	}
	
	private function displayUploadDesignForm($mode, $buttonName, $challenge, $result, $design)
	{
		if(is_array($result))
		{
			extract($result);
		}
		extract($design);
		if($msg)
		{
			$html .= '<div id="errorMsg" class="error"> '.$msg.'</div>'."\n";
		}
		$html .= '<div id="form">'."\n";
		$html .= '	<form method="post" action="'.htmlentities($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">'."\n";
		$html .= '	<fieldset>'."\n";
		$html .= '		<legend><h4>Upload a design:</h4></legend>'."\n";
		$html .= '		<input type="hidden" name="userID" id="userID" value="'.$_SESSION['userID'].'"/>'."\n";
		$html .= '		<input type="hidden" name="challengeID" id="challengeID" value="'.$challenge['challengeID'].'"/>'."\n";
		$html .= '		<label for="designTitle">Title</label>'."\n";
		$html .= '		<input type="text" name="designTitle" id="designTitle" value="'.htmlentities(stripslashes($designTitle),ENT_QUOTES).'"/>'."\n";
		$html .= '		<div class="clear"></div>'."\n";
		$html .= '		<label for="designDescription">Description</label>'."\n";
		$html .= '		<input type="text-area" name="designDescription" id="designDescription" value="'.htmlentities(stripslashes($designDescription),ENT_QUOTES).'" />'."\n";
		$html .= '		<div class="clear"></div>'."\n";
		$html .= '		<label for="designKeywords">Keywords</label>'."\n";
		$html .= '		<input type="text-area" name="designKeywords" id="designKeywords" value="'.htmlentities(stripslashes($designKeywords),ENT_QUOTES).'"/>'."\n";
		$html .= '		<div class="clear"></div>'."\n";
		$html .= '		<label for="designPath">Upload Design</label>'."\n";
		$html .= '		<input type="file" name="designPath"/>'."\n";
		$html .= '		<div class="clear"></div>'."\n";
		$html .= '		<input class="submit" type="submit" name="'.$mode.'" value="'.$buttonName.'" />'."\n";
		$html .= '		<p>(All fields are required.)</p>'."\n";
		$html .= '	</fieldset>'."\n";
		$html .= '	</form>'."\n";
		$html .= '</div>'."\n";
	

		return $html;
	}
	
	private function displayChallengeThumb($challenge)
	{
	
		$html .= '<div id="display-challenge">'."\n";
		$html .= '	<h4 class="color-black">'.$challenge['challengeTitle'].'</h4>'."\n";
		$html .= '	<h4>'.$challenge['challengeByline'].'</h4>'."\n";
		$html .= '	<img src="uploads/thumbnails/'.$challenge['challengeImage'].'" alt="'.htmlentities($challenge['challengeTitle'],ENT_QUOTES).'" /><p>'.$challenge['challengeDescription'].'</p>'."\n";
		$html .= '<div class="clear"></div>'."\n";
		$html .= '</div>'."\n";
		return $html;
	
	}

}


?>
<?php
//	this class contains the method to display the page to allow admin to... 
//	add the new winner to the home page


class AddWinnerView extends View
{
	protected function displayContent()
	{
		$html = '<section class="content" id="add-winner">'."\n";
		//	check if admin is not logged in, and if so...
		//	restrict access
		if(!$this->model->adminLoggedIn)
		{
			$html .= '<p>Sorry, but this is a restricted page.</p>'."\n";
			return $html;
			
		}
		
		if($_POST['AddWinner']) 
		{
			$result = $this->model->processAddWinner();
			
			if($result['challengeImage'])
			{
				$_POST['challengeImage'] = $result['challengeImage'];
			}
		
		}
		//	get info from the designs table for the design that has the ID stored in $_GET
		$this->design = $this->model->getDesignByID($_GET['dID']);
		//	get info from the users table for the user who uploaded the design
		$this->winner = $this->model->getUserByID($this->design['userID']);
		echo '<pre>';
			print_r($this->design);
			print_r($this->winner);
		echo '</pre>';
		
		
		
		//	display the form that contains the info we will display on the homepage winner showcase.
		$html .= $this->displayWinnerForm('Add Winner', $this->design, $this->winner, $result);
		
		$html .= '</section>'."\n";
		
		return $html;
	}
	
	protected function displayWinnerForm($buttonName, $design, $winner, $result)
	{
		$html .= '<div id="form">'."\n";
		$html .= '	<form method="post" action="'.htmlentities($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">'."\n";
		$html .= '	<fieldset>'."\n";
		$html .= '		<legend><h4>Add Winner:</h4></legend>'."\n";
		$html .= '		<input type="hidden" name="userID" id="userID" value="'.$winner['userID'].'"/>'."\n";
		$html .= '		<input type="hidden" name="designID" id="designID" value="'.$design['designID'].'"/>'."\n";
		$html .= '		<input type="hidden" name="challengeID" id="challengeID" value="'.$winner['challengeID'].'"/>'."\n";
		$html .= '		<p><strong>Winner:</strong> '.$winner['userName'].'</p>'."\n";
		$html .= '		<img src="uploads/thumbnails/'.$winner['userPic'].'"/>'."\n";
		$html .= '		<p><strong>Occupation:</strong> '.$winner['userOccupation'].'</p>'."\n";
		$html .= '		<p><strong>Location:</strong> '.$winner['userLocation'].'</p>'."\n";
		$html .= '		<label for="winnerAbout"><strong>About:</strong> </label>'."\n";
		$html .= '		<textarea rows="10" cols="30"" name="winnerAbout" id="winnerAbout">'.htmlentities(stripslashes($winner['userBio']),ENT_QUOTES).'</textarea>'."\n";
		$html .= '		<div id="wAboutMsg" class="error"> '.$wAboutMsg.'</div>'."\n";
		$html .= '		<div class="clear"></div>'."\n";
		$html .= '		<label for="winnerQuote"><strong>Quote:</strong> </label>'."\n";
		$html .= '		<textarea rows="10" cols="30"" name="winnerQuote" id="winnerQuote">'.htmlentities(stripslashes($winner['winnerQuote']),ENT_QUOTES).'</textarea>'."\n";
		$html .= '		<div id="wQuoteMsg" class="error"> '.$wQuoteMsg.'</div>'."\n";
		$html .= '		<div class="clear"></div>'."\n";
		
		$html .= '		<p><strong>Design:</strong> '.$design['designTitle'].'</p>'."\n";
		$html .= '		<img src="uploads/thumbnails/'.$design['designPath'].'"/>'."\n";
		$html .= '		<p><strong>Description:</strong> '.$design['designDescription'].'</p>'."\n";
		//	upload detail view of image (created by admin)
		$html .= '		<div><label for="winnerImage">Upload Image : </label><br />'."\n";
		$html .= '		<input type="file" name="winnerImage" /></div>'."\n";
		$wImageMsg = $uploadMsg ? $uploadMsg : $wImageMsg;
		$html .= '		<div class="error"> '.$wImageMsg.'</div>'."\n";
		$html .= '		<div class="clear"></div>'."\n";
		
		$html .= '		<input class="submit" type="submit" name="AddWinner" value="'.$buttonName.'" />'."\n";
		$html .= '	</fieldset>'."\n";
		$html .= '	</form>'."\n";
		$html .= '</div>'."\n";
	

		return $html;
		
		
		
	}


}


?>
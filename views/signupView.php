<?php


	class SignupView extends View 
	{
		protected function displayContent()
		{
			$html = '<section class="content" id="sign-up">'."\n";
			
			if($_POST['signup'])	//	if the signup button has been pressed
			{
				$result = $this->model->processAddUser();
				//echo '<pre>';
				//print_r($result);
				//print_r($_FILES);
				//print_r($_POST);
				//echo '</pre>';
				
				//	if userPicPath has been returned with a result array...
				//	store it in $_POST so we can display it on the form.
				if($result['userPic'])
				{
					$_POST['userPic'] = $result['userPic'];
				}
				
			}
			$html .= '	<p class="failedMessage">'.$this->model->loginMsg.'</p>'."\n";
			$html .= $this->displaySignUpForm('signup', 'Sign Up!', $result, $_POST);
			
			$html .= '</section>'."\n";
			
			return $html;	
		}
		
		private function displaySignUpForm($mode, $buttonName, $result, $user)
		{
			if(is_array($result))
			{
				extract($result);
			}
			$msg .= $imsg;
			extract($user);
			$html .= '<div>'.$msg.'</div>'."\n";
			$html .= '<div id="form">'."\n";
			$html .= '	<form method="post" action="'.htmlentities($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">'."\n";
			$html .= '	<fieldset>'."\n";
			$html .= '		<legend><h4>'.$this->pageInfo['pageHeading'].'</h4></legend>'."\n";
			$html .= '		<input type="hidden" name="userID" value="'.$userID.'" />'."\n";
			$html .= '		<label for="userName">Username</label>'."\n";
			$html .= '		<input type="text" name="userName" id="userName" value="'.htmlentities(stripslashes($userName),ENT_QUOTES).'"/>'."\n";
			$html .= '		<div id="userNameMsg" class="error"> '.$userNameMsg.'</div>'."\n";
			$html .= '		<div class="clear"></div>'."\n";
			$html .= '		<label for="userEmail">Email Address</label>'."\n";
			$html .= '		<input type="text" name="userEmail" id="userEmail" value="'.htmlentities(stripslashes($userEmail),ENT_QUOTES).'"/>'."\n";
			$html .= '		<div id="userEmailMsg" class="error"> '.$userEmailMsg.'</div>'."\n";
			$html .= '		<div class="clear"></div>'."\n";
			$html .= '		<label for="userPassword">Password</label>'."\n";
			$html .= '		<input type="password" name="userPassword" id="userPassword" value="'.htmlentities(stripslashes($userPassword),ENT_QUOTES).'"/>'."\n";
			$html .= '		<div id="userPasswordMsg" class="error"> '.$userPasswordMsg.'</div>'."\n";
			$html .= '		<div class="clear"></div>'."\n";
			$html .= '		<label for="userConfirm">Confirm Password</label>'."\n";
			$html .= '		<input type="password" name="userConfirm" id="userConfirm" value="'.htmlentities(stripslashes($userConfirm),ENT_QUOTES).'"/>'."\n";
			$html .= '		<div id="userConfirmMsg" class="error"> '.$userConfirmMsg.'</div>'."\n";
			$html .= '		<div id="passwordMsg" class="error"> '.$passwordMsg.'</div>'."\n";
			$html .= '		<div class="clear"></div>'."\n";

			$html .= '		<label for="userPic">Upload Profile Picture</label>'."\n";
			$html .= '		<input type="file" name="userPic"/>'."\n";
			$userPicMsg = $uploadMsg ? $uploadMsg : $userPicMsg;
			$html .= '		<div class="error"> '.$userPicMsg.'</div>'."\n";
			$html .= '		<div class="clear"></div>'."\n";
			if($uPic)
			{
				$html .= '<div'."\n";
				$html .= '<img src="uploads/thumbnails'.$userPic.'"/></div>'."\n";
			} else {
				$html .= '<div>&nbsp;</div>'."\n";
			}
			
			$html .= '		<p>(All fields are required.)</p>'."\n";
			$html .= '		<input class="submit" type="submit" name="'.$mode.'" value="'.$buttonName.'" />'."\n";
			
			$html .= '		<p><a href="index.php?page=login">Already a member? Login here!</a></p>'."\n";
			$html .= '	</fieldset>'."\n";
			$html .= '	</form>'."\n";
			$html .= '</div>'."\n";
			
			return $html;
		}
		

	
	}

?>
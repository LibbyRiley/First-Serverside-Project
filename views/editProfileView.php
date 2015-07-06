<?php

//	this class contains the method to edit the user's profile information
//	and update the users table with the new info
class EditProfileView extends View 
	{
	
		private $user;	// holds the information about the user
		
		protected function displayContent()
		{	//	displays the edit profile content
		
			$html .= '<section class="content" id="user-profile">'."\n";
			
			$this->user = $this->model->getUserByID($_SESSION['userID']);
			
			if(is_array($this->user))
			{
			
			//	check if the user form has been submitted
				if($_POST['Edit'])
				{	//	if form has been submitted
					//	process the update
					$result = $this->model->processUpdateUser();
					$user = $_POST;
					if($result['userPic'])
					{
						$_POST['userPic'] = $result['userPic'];
					}
					
				} else {
					$user = $this->user;			
				}
				
				//	display the profile form
				$html .= $this->displayProfileForm('Edit', 'Update my Profile!', $result, $user);
				
			} else {
			
				$html .= '<h4>Sorry, this page is unavailable to guests.</h4>'."\n";
				$html .= '<h4><a href="index.php?page=login" class="orange-hover">Login here</a> or <a href="index.php?page=signup" class="orange-hover">Sign up!</a></h4>'."\n";
			}
			
			$html .= '</section>'."\n";
			return $html;
		}
		
		private function displayProfileForm($mode, $buttonName, $result, $user)
		{	//	function to display the profile form
			
			if(is_array($result))
			{
				extract($result);
			}
			extract($user);
			if($msg)
			{
				$html .= '<div id="errorMsg" class="error"> '.$msg.'</div>'."\n";
			}
			$html .= '<div id="form">'."\n";
			$html .= '	<form id="edit_form" method="post" action="'.
				htmlentities($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">'."\n";
			$html .= '		<fieldset>'."\n";
			$html .= '		<legend><h4>'.$this->pageInfo['pageHeading'].': </h4></legend>'."\n";
			$html .= '		<input type="hidden" name="MAX_FILE_SIZE" value="2000000" />'."\n";	
			$html .= '		<input type="hidden" name="userID" value="'.$user['userID'].'" />'."\n";
			//	edit username
			$html .= '		<label for="userName">*Username:</label>'."\n";
			$html .= '		<input type="text" name="userName" id="userName" value="'.htmlentities(stripslashes($userName),ENT_QUOTES).'"/>'."\n";
			$html .= '		<div id="userNameMsg" class="error"> '.$userNameMsg.'</div>'."\n";
			$html .= '		<div class="clear"></div>'."\n";
			
			//	edit email
			$html .= '		<label for="userEmail">*Email:</label>'."\n";
			$html .= '		<input type="email" name="userEmail" id="userEmail" value="'.htmlentities(stripslashes($userEmail),ENT_QUOTES).'"/>'."\n";
			$html .= '		<div id="userEmailMsg" class="error"> '.$userEmailMsg.'</div>'."\n";
			$html .= '		<div class="clear"></div>'."\n";
			//	edit occupation
			$html .= '		<label for="userOccupation">Occupation:</label>'."\n";
			$html .= '		<input type="text" name="userOccupation" id="userOccupation" value="'.htmlentities(stripslashes($userOccupation),ENT_QUOTES).'"/>'."\n";
			$html .= '		<div id="userOccupationMsg" class="error"> '.$userOccupationMsg.'</div>'."\n";
			$html .= '		<div class="clear"></div>'."\n";
			//	edit location
			$html .= '		<label for="userLocation">Location:</label>'."\n";
			$html .= '		<input type="text" name="userLocation" id="userLocation" value="'.htmlentities(stripslashes($userLocation),ENT_QUOTES).'"/>'."\n";
			$html .= '		<div id="userLocationMsg" class="error"> '.$userLocationMsg.'</div>'."\n";
			$html .= '		<div class="clear"></div>'."\n";
			//	edit bio
			$html .= '		<label for="userBio">Write a little bit about yourself:</label>'."\n";
			$html .= '		<textarea rows="10" cols="30"" name="userBio" id="userBio">'.htmlentities(stripslashes($userBio),ENT_QUOTES).'</textarea>'."\n";
			$html .= '		<div id="userBioMsg" class="error"> '.$userBioMsg.'</div>'."\n";
			$html .= '		<div class="clear"></div>'."\n";
			//	edit website
			$html .= '		<label for="userWebsite">Website:</label>'."\n";
			$html .= '		<input type="text" name="userWebsite" id="userWebsite" value="'.htmlentities(stripslashes($userWebsite),ENT_QUOTES).'"/>'."\n";
			$html .= '		<div id="userWebsiteMsg" class="error"> '.$userWebsiteMsg.'</div>'."\n";
			$html .= '		<div class="clear"></div>'."\n";
			
			$html .= '		<label for="userPic">Upload Profile Picture</label>'."\n";
			if($user['userPic'])
			{
				$html .= '<img src="uploads/thumbnails/'.$user['userPic'].'" alt="'.$user['userPic'].'"/>'."\n";
			} 
			$html .= '<input type="file" name="userPic" />'."\n";
			
//			$userPicMsg = $uploadMsg ? $uploadMsg : $userPicMsg;
//			$html .= '		<div class="error"> '.$userPicMsg.'</div>'."\n";
			$html .= '		<div class="clear"></div>'."\n";

			$html .= '		<div><input class="submit" type="submit" name="'.$mode.'" value="'.$buttonName.'"/></div>'."\n";
			$html .= '		<p>* Required fields.</p>'."\n";
			$html .= '	</fieldset>'."\n";
			$html .= '</form>'."\n";
		   
			
			$html .= '</div>'."\n";
			return $html;
		
		
		}
	}
?>
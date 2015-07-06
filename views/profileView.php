<?php

class ProfileView extends View 
	{
	
		private $user;	// holds the information about the user	
		private $designs;	//	holds the information about all the designs
		
		
		protected function displayContent()
		{
			$html .= '<section class="content" id="user-profile">'."\n";
			
			//	get the user info of logged in user
			$this->user = $this->model->getUserByID($_SESSION['userID']);
			
			if(is_array($this->user))
			{
				$this->uID = $this->user['userID'];
				
				//	get the designs uploaded by the user
				$this->designs = $this->model->getDesignsByUserID($this->uID);	
				// display user info
				$html .= $this->displayUser($this->user);
				
				// if this user has designs on the db, display them, or show message.
				if(is_array($this->designs))
				{
					$html .= $this->displayDesigns();
					
				} else {
				
					$html .= '<h4>This user has not uploaded any designs.</h4>'."\n";
				}
				
				
			} else {
			
				$html .= '<h4>Sorry, this page is unavailable to guests.</h4>'."\n";
				$html .= '<h4><a href="index.php?page=login" class="orange-hover">Login here</a> or <a href="index.php?page=signup" class="orange-hover">Sign up!</a></h4>'."\n";
			}
			
		
			$html .= '</section>'."\n";
			return $html;
		}
		
		public function displayUser($user)
		{	//	function to display the user information in the bio half of the profile page.
			$html .= '';
			$user = $this->user;
			$html .= '<div id="user">'."\n";
			$html .= '	<h3>'.$user['userName'].'</h3>'."\n";
			$html .= '	<img src="uploads/images/'.$user['userPic'].'" class="user-pic"/>'."\n";
			if($user['userOccupation'] == ' ')
			{
				$html .= '	<h4>Oh dear, your profile is looking a bit bare...</h4>'."\n";
				$html .= '	<p class="button"><a href="index.php?page=editProfile&amp;uID='.$this->user['userID'].'">Edit Profile</a></p>'."\n";
				
			} else {
				$html .= '	<h4>'.$user['userOccupation'].'</h4>'."\n";
				$html .= '	<h4>'.$user['userLocation'].'</h4>'."\n";
				$html .= '	<p>'.$user['userBio'].'</p>'."\n";
				$html .= '	<h4><a href="#" class="orange-hover">'.$user['userWebsite'].'</a></h4>'."\n";
				$html .= '	<p class="button"><a href="index.php?page=editProfile&amp;uID='.$this->user['userID'].'">Edit Profile</a></p>'."\n";
				
			}
			
			$html .= '	<p class="button" id="orange-button"><a href="index.php?page=uploadDesign">Upload New Design</a></p>'."\n";
			$html .= '</div>'."\n";
			return $html;
		}
		
		public function displayDesigns()
		{	//	function to display the designs uploaded by this user
			
			$html .= '<div>'."\n";
			
			$html .= '	<h4>Designs:</h4>'."\n";
			foreach($this->designs as $design)
			{
				$html .= '<a href="index.php?page=designView&amp;dID='.$design['designID'].'">'."\n";
				$html .= '	<div class="entry">'."\n";
				$html .= '		<img src="uploads/thumbnails/'.$design['designPath'].'" alt="'.$design['designPath'].'"/>'."\n";
				$html .= '		<h4>'.$design['designTitle'].'</h4>'."\n";
				$html .= '	</div>'."\n";
				$html .= '</a>'."\n";
			}
			return $html;
		}
	}
?>
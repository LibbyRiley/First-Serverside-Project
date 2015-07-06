<?php

	//	this class contains the method to display the homepage view
	class HomeView extends View 
	{
		protected function displayContent()
		{
			$html .= '<!--[if lt IE 9]><div class="hidden">'."\n";
			$html .= '<![endif]-->'."\n";
			$html .= '<div id="banners-content">'."\n";
			$html .= '	<a href="index.php?page=signup">'."\n";
			$html .= '		<div id="left-banner" class="banner">'."\n";
			$html .= '			<p>SIGN UP AND TAKE PART IN THE CHALLENGE!</p>'."\n";
			$html .= '		</div>'."\n";
			$html .= '	</a>'."\n";
			$html .= '	<a href="index.php?page=challenge">'."\n";
			$html .= '		<div id="right-banner" class="banner">'."\n";
			$html .= '			<p>CHECK OUT THIS WEEK\'S CHALLENGE!</p>'."\n";
			$html .= '		</div>'."\n";
			$html .= '	</a>'."\n";
			$html .= '</div>'."\n";
			$html .= '<!--[if lt IE 9]></div>'."\n";
			$html .= '<![endif]-->'."\n";
			
			
			$html .= '<section class="content" id="home">'."\n";

			//	get the latest winner fronm the winners table
			$this->winner = $this->model->getLatestWinner();
			
			
			if(is_array($this->winner))
			{
				$this->user = $this->model->getUserByID($this->winner['userID']);
				$this->design = $this->model->getDesignByID($this->winner['designID']);
				$html .= $this->displayWinner($this->winner, $this->user, $this->design);
			} else {
				$html .= '<h3>Winner Showcase</h3>'."\n";
				$html .= '<h4>There are no winners yet as the first challenge has not ended.</h4>'."\n";
				$html .= '<h4><a href="index.php?page=challenge" class="orange-hover">View the challenge.</h4>'."\n";
				$html .= '<h4><a href="index.php?page=login" class="orange-hover">Login</a> or <a href="index.php?page=signup" class="orange-hover">Sign Up</a></h4>'."\n";
			}
			$html .= '</section>'."\n";
			return $html;
		}
		
		
		private function displayWinner($winner, $user, $design)
		{
			//this function displays the winner information and design images
			$html .= '<p class="date-content">Showcase of the best design submitted last week:</p>'."\n";
			$html .= '	<h3 class="winner-name">'.$user['userName'].'</h3>'."\n";
			$html .= '	<p class="winner-details">'.$user['userOccupation'].' from '.$user['userLocation'].'</p>'."\n";
			$html .= '	<a href="index.php?page=designView&amp;dID='.$design['designID'].'"><img src="uploads/images/'.$winner['winnerImage'].'" class="winner-design" alt="winning image"/></a>'."\n";
			$html .= '	<div class="winner-profile">'."\n";
			$html .= '		<img src="uploads/images/'.$user['userPic'].'" class="winner-profile-pic" alt="picture of the winner\'s face"/>'."\n";
			$html .= '		<div class="winner-about">'."\n";
			$html .= '			<h4>ABOUT THE DESIGNER</h4>'."\n";
			$html .= '			<p>'.$winner['winnerAbout'].'</p>'."\n";
			$html .= '		</div>'."\n";
			$html .= '		<p class="winner-quote">"'.$winner['winnerQuote'].'"</p>'."\n";
			$html .= '	</div>'."\n";
			$html .= '	<div class="clear"></div>'."\n";
			
			return $html;
		
		}
		
		
	
	}



?>
<?php

	//	this class displays the large version of the design requested.	
	class DesignView extends View 
	{
		private $design;	//	holds the design info
		private $user;	//	holds the user info. This doesnt work???
		protected function displayContent()
		{
			$html .= '<section class="content" id="design">'."\n";
			
			
			//	get the design info from the designs table by the id
			$this->design = $this->model->getDesignByID($_GET['dID']);
			
			//	get the user info from the users table by the userID of the design
			//	This doesnt work???
			$this->user = $this->model->getUserByID($this->design['userID']);
			
			//	if there is an array for the design
			if(is_array($this->design))
			{
				//	displays the design, and info
				$html .= $this->displayDesign($this->design, $this->user);
			} else {
				$html .= '<p>Sorry, this design is not available.</p>'."\n";
			}
			$html .= '</section>'."\n";
			return $html;
		
		}
		
		public function displayDesign($design, $user)
		{	//	this function displays the design
			$html .= '<div class="single-design">'."\n";
			$html .= '	<h4 class="color-black">Designer: '.$user['userName'].'</h4>'."\n";
			$html .= '	<h3>'.$design['designTitle'].'</h3>'."\n";
			
			$html .= '	<h4>'.$design['designDescription'].'</h4>'."\n";
			$html .= '	<img src="uploads/images/'.$design['designPath'].'" alt="'.$design['designPath'].'"/>'."\n";
			if($this->model->adminLoggedIn)
			{
				$html .= '	<p class="button"><a href="index.php?page=addWinner&amp;dID='.$design['designID'].'">Make this a winning design</a></p>'."\n";
				
			}
			$html .= '</div>'."\n";
		
			return $html;
		
		
		}
		
	}
	
?>
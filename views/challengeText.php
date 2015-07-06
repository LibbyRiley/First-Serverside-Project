<?php
	
	class TextView extends View 
	{
		
		protected function displayContent()
		{
			$html .= '<section class="content" id="search">'."\n";
			
			$html .= '	<h3>'.$this->pageInfo['pageHeading'].'</h3>'."\n";
			
	
			$this->challenge = $this->model->getChallengeByID($_GET['cID']);
			
			$html .= $this->displayChallengeText($this->challenge);
			
			$html .= '</section>'."\n";
			return $html;
		
		}
		
		public function displayChallengeText($challenge)
		{
			$html .= '<div>'."\n";
			$html .= '	<h4> Text for '.$challenge['challengeTitle'].' challenge</h3>'."\n";
			$html .= '	<p>'.$challenge['challengeText'].'</p>'."\n";	
			return $html;
		
		
		}
		
	}
	
?>
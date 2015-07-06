<?php
	
	class EntriesView extends View 
	{
		protected function displayContent()
		{
			$html .= '<section class="content" id="search">'."\n";
			
			$html .= '	<h3>'.$this->pageInfo['pageHeading'].'</h3>'."\n";
			
			
			$this->designs = $this->model->getDesignsByCID($_GET['cID']);
			
			if(is_array($this->designs))
			{
				$html .= $this->displaySearchResults($this->designs);
				
			} else {
			
				$html .= '<h4>Sorry, there are no designs for this challenge.</h4>'."\n";
			}
			
			
			$html .= '</section>'."\n";
			return $html;
		
		}
		
		public function displaySearchResults($designs)
		{
			$html .= '<div>'."\n";
			
			foreach($designs as $design)
			{
				$html .= '	<a href="index.php?page=designView&amp;dID='.$design['designID'].'">'."\n";
				$html .= '	<div class="entry">'."\n";
				$html .= '		<img src="uploads/thumbnails/'.$design['designPath'].'" alt="'.$design['designPath'].'"/>'."\n";
				$html .= '		<h4>'.$design['designTitle'].'</h4>'."\n";
				$html .= '	</div>'."\n";
				$html .= '	</a>'."\n";
			}
			return $html;
		
		
		}
		
	}
	
?>
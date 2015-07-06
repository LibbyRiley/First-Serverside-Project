<?php

//	This class contains the method that displays the content for the about page.
	class AboutView extends View 
	{
		protected function displayContent()
		{ //	 displays the about page content, including the  "how to play" information
			$html = '<section class="content" id="about">'."\n";
			$html .= '	<h3>'.$this->pageInfo['pageHeading'].'</h3>'."\n";
			$html .= $this->displayHowToPlay();
			$html .= '	<h4><span class="orange">TYPE CIRCUS</span> is a place for established and aspiring designers to compete their typography skills against each other.'."\n";
			$html .= '	</h4>'."\n";
			$html .= '	<div>'."\n";
			$html .= '		<p>We believe that typography is absolutely the most important skill is a designer’s toolbox. Here at <strong>TYPE CIRCUS</strong> you can practice those skills in a friendly & supportive community.</p>'."\n";
			$html .= '		<p>Our members are knowledgable and want to help you learn, so if you have any questions or want some honest critique of your entries just ask!</p>'."\n";
			$html .= '		<p><strong>TYPE CIRCUS</strong> tries to be a good place for resources too so if you find an article that you think our members could benefit from let us know!</p>'."\n";
			$html .= '		<h4 id="so-sign-up">SO SIGN UP NOW AND HAVE A GO AT THE LATEST CHALLENGE!</h4>'."\n";
			$html .= '	</div>'."\n";
			$html .= '</section>'."\n";
			return $html;
		}

	
	}



?>
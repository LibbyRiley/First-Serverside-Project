<?php

abstract class View {

	protected $pageInfo;		//	holds the data read from the pages table
	protected $model;			//	holds the object for the db class
	
	public function __construct($info, $model) 
	{

		$this -> pageInfo = $info;
		$this -> model   = $model;

	}
	
	
	
	public function displayPage() 
	{	//	display page, with appropriate header, contents and footer. Also check user session. 
		
		$this->model->checkUserSession();

		$html  = $this->displayHeader();
		$html .= $this->displayContent();
		$html .= $this->displayFooter();
		return $html;
	}
	
	
	private function displayHeader()
	{	//	display header with appropriate nav display and user available links
		$html .= '<!DOCTYPE html>'."\n";
		$html .= '<html>'."\n";
		$html .= 	'<head>'."\n";
		$html .= 		'<meta charset="utf-8"/>'."\n";
		$html .= 		'<title>'.$this->pageInfo['pageTitle'].'</title>'."\n";
		$html .= 		'<meta name="description" content="'.$this->pageInfo['pageDescription'].'"/>'."\n";
		$html .= 		'<meta name="keywords" content="'.$this->pageInfo['pageKeywords'].'"/>'."\n";
		//	stylesheet links 
		$html .= 		'<link rel="stylesheet" type="text/css" href="css/style.css"/>'."\n";
		//	webfont links 
		$html .= 		'<link href=\'http://fonts.googleapis.com/css?family=Oswald:400,700\' rel=\'stylesheet\' type=\'text/css\'>'."\n";
		$html .= 		'<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->'."\n";
		$html .= 		'<link rel="icon" type="image/png" href="images/favicon.jpg">'."\n";
		$html .= 	'</head>'."\n";
		$html .= '<body>'."\n";
		$html .= 	'<header>'."\n";
		
		$html .= 		'<div id="login-signup-search">'."\n";
		$html .= 		$this->displayLoginInfo();
		$html .= 			'<p>';
		

		//	This if statement checks to see if a user is logged in,
		//	and if they are it checks their userPrivilege,
		//	and displays the appropriate links.
		if($this->model->loggedIn)
		{	//	check to see if a user is logged in
			if($this->model->adminLoggedIn)
			{	//	check to see if the user has admin access
				//	display admin panel link
				$html .= '<a href="index.php?page=admin">ADMIN PANEL</a> | ';
			} else {
				//	otherwise display 'my profile' link
				$html .= '<a href="index.php?page=profile&amp;uID='.$_SESSION['userID'].'">MY PROFILE</a> | ';
			}
			//	show 'logout' link when anyone is logged in
			$html .= '<a href="index.php?page=logout">LOGOUT</a>'."\n";
		} else {
			// or if no one is logged in, show 'login' and 'signup' links
			$html .= '<a href="index.php?page=login">LOGIN</a> | '."\n";
			$html .= '<a href="index.php?page=signup">SIGN UP</a>'."\n";

		}
		
		
		$html .= 	'</p>'."\n";		
		$html .= 	'<form method="post" action="index.php?page=search" >'."\n";
		$html .= 		'<input type="search" name="search"/>'."\n";
		$html .= 		'<input type="submit" name="searchSubmit" value="Search" />'."\n";	
		$html .= 	'</form>'."\n";
		
		$html .= '</div>'."\n";
		
		$html .= '<section id="header-content">'."\n";
		$html .= 	'<h1><a href="index.php?=home">TYPE CIRCUS</a></h1>'."\n";
		$html .= 	'<h2>A Weekly Typesetting Challenge</h2>'."\n";
		if($_GET['page'] == 'home')
		{
			$html .= 	'<p>Each week <strong>TYPE CIRCUS</strong> sets a new typographic challenge for our members. It\'s a fun and engaging way to practice those essential typesetting skills and to showcase your talents to other designers.</p>'."\n";
			$html .= 	'<p><strong><a href="index.php?page=signup">Sign up now and check out the latest challenge!</a></strong></p>'."\n";
		}
		$html .= 	$this->displayNav();
		$html .= '</section>'."\n";
		$html .= '</header>'."\n";
		return $html;
	}
	
	
	
	abstract protected function displayContent();	//	display appropriate content for the requested page
	
	private function displayLoginInfo()
	{	// checks whether admin or user NEEDS EDITING FOR ADMIN OR USER.
		
		if($this->model->loggedIn) 
		{
			$html = '<p id="loginInfo">You are logged in as '.$_SESSION['userName'].'</p>'."\n";
			return $html;
		}
	}
	
	private function displayNav()
	{	//	displays navigation with current page highlighted etc HTML NEEDS EDITING
		$links = array('home', 'challenge', 'about'); 
		$html .= '<nav>'."\n";
		$html .= '<ul>'."\n";
		foreach($links as $link) {
		
			$html .= '<a href="index.php?page='.$link.'">';
			$html .= '<li ';
			$html .= ($this->pageInfo['pageName'] == $link) ? ' class="current-page"' : '';
			$html .= '>'.strtoupper($link).'</li></a>'."\n";
		}
		$html .= '</ul>'."\n";
		$html .= '</nav>'."\n";
		return $html;
		
	}
	
	private function displayFooter()
	{	//	displays footer with navigation NEEDS HTML AND DISPLAY NAV FUNCTION
		$html .= '<footer>'."\n";
		$html .= 	$this->displayNav();
		$html .= '	<div id="social-media-links">'."\n";
		$html .= '		<ul>'."\n";
		$html .= '			<li><a href="#" id="fb-icon">Facebook</a></li>'."\n";
		$html .= '			<li><a href="#" id="twt-icon">Twitter</a></li>'."\n";
		$html .= '			<li><a href="#" id="tmblr-icon">Tumblr</a></li>'."\n";
		$html .= '			<li><a href="#" id="rss-icon">RSS Feed</a></li>'."\n";
		$html .= '		</ul>'."\n";
		$html .= '	</div>'."\n";
		$html .= '</footer>'."\n";
		
		$html .= '</body>'."\n";
		
		$html .= '</html>'."\n";
		return $html;
	}
	
	public function displayHowToPlay()
	{
		$html .= '<div class="resources" id="how-to-play">'."\n";
		$html .= '	<h4>How do I play?</h4>'."\n";
		$html .= '	<p><strong>To have a go and join in the fun, check out the current challenge and then:</strong></p>'."\n";
		$html .= '	<ul>'."\n";
		$html .= '		<li>Create a design that is no larger than 800x800 in jpeg format and <a href="index.php?page=uploadDesign">upload it here.</a></li>'."\n";
		$html .= '		<li>Upload as many designs as you want, but aim for your best.</li>'."\n";
		$html .= '		<li>Submissions can be created however you want (digitally, hand-drawn, photo, etc.) and can be as polished or rough as you want, but keep in mind the requirements in the challenge.</li>'."\n";
		$html .= '		<li>Keep it family-friendly.</li>'."\n";
		$html .= '		<li>Have fun and experiment.</li>'."\n";
		$html .= '		<li>Follow <a href="#">@typecircus</a> for the new challenge each week and announcements of the winners.</li>'."\n";
		$html .= '	</ul>'."\n";
		$html .= '</div>'."\n";
		return $html;
	}
		
}
?>
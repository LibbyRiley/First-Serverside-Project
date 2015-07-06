<?php

	//	this class contains the methods to display the login form if 'login', or 'logout'
	//	or if the admin is logged in, it displays the admin panel ('admin')
	
	class LoginView extends View 
	{
		protected function displayContent()
		{
		
			$html = '';
			$html = '<section class="content" id="login">'."\n";
			//	check if admin is logged in
			if($this->model->adminLoggedIn)
			{
				$html .= '<h3>ADMIN PANEL</h3>'."\n";
				$html .= '<h4 class="add-border-top">Add a new challenge to the database.</br>This will change the content of the Challenge Page.</h4>'."\n";
				$html .= '<div class="button" id="orange-button"><a href="index.php?page=addChallenge">ADD NEW CHALLENGE</a></div>'."\n";
				$html .= '<h4 class="add-border-top">Add new resources to the database.</br>This will give you more resources to place on the Challenge Page.</h4>'."\n";
				$html .= '<div class="button" id="orange-button"><a href="index.php?page=addResources">ADD RESOURCES</a></div>'."\n";
			} else {
				$html .= '<p class="failed-message">'.$this->model->loginMsg.'</p>'."\n";
				$html .= $this->displayLoginForm($result, $_POST);
				
			}
			
			$html .= '</section>'."\n";
			return $html;
			
			
			
		}

		private function displayLoginForm($result, $_POST)
		{
			
			$html .= '<div id="form">'."\n";
			$html .= '	<form method="post" action="'.htmlentities($_SERVER['REQUEST_URI']).'">'."\n";
			$html .= '	<fieldset>'."\n";
			$html .= '		<legend><h4>Login</h4></legend>'."\n";
			$html .= '		<label for="userName">Username</label>'."\n";
			$html .= '		<input type="text" name="userName" id="userName" />'."\n";
			$html .= '		<div class="clear"></div>'."\n";
			$html .= '		<label for="userPassword">Password</label>'."\n";
			$html .= '		<input type="password" name="userPassword" id="userPassword" />'."\n";
			$html .= '		<div class="clear"></div>'."\n";
			$html .= '		<input class="submit" type="submit" name="login" value="Login" />'."\n";
			$html .= '		<p><a href="index.php?page=signup">Not a member yet? Sign up here!</a></p>'."\n";
			$html .= '	</fieldset>'."\n";
			$html .= '	</form>'."\n";
			$html .= '</div>'."\n";
			
			
			return $html;
		}
	}


?>
<?php
// This script acts as the controller and loads the page to display.

// session starting!
session_start();

include 'classes/viewClass.php';
include 'classes/modelClass.php';

class pageSelector
{
	public function run()
	{
		if(!$_GET['page']) 
		{ //if page is not available from the url...
			$_GET['page'] = 'home'; // ... set it to home.
		}
			
			$model = new Model; // instantiate the Dbase class
			
			//	test to see if the page is edit page.
			if($_GET['page'] == 'editPage')
			{
				$pageInfo = $model->getPageInfo($_GET['edit']);  // read the content from the pages table
			} else {
				$pageInfo = $model->getPageInfo($_GET['page']);  // read the content from the pages table
			}
			
			// go to page
			
			switch ($_GET['page']) 
			{
				case 'home': include 'views/homeView.php';
					$view = new HomeView($pageInfo, $model);
					break;
				case 'challenge': include 'views/challengeView.php';
					$view = new ChallengeView($pageInfo, $model);
					break;
				case 'addChallenge': include 'views/addChallengeView.php';
					$view = new AddChallengeView($pageInfo, $model);
					break;
				case 'editChallenge': include 'views/editChallengeView.php';
					$view = new EditChallengeView($pageInfo, $model);
					break;
				case 'deleteChallenge': include 'views/deleteChallengeView.php';
					$view = new DeleteChallengeView($pageInfo, $model);
					break;
				case 'about': include 'views/aboutView.php';
					$view = new AboutView($pageInfo, $model);
					break;
				case 'logout':
				case 'admin': 
				case 'login': include 'views/loginView.php';
					$view = new LoginView($pageInfo, $model);
					break;
				case 'signup': include 'views/signupView.php';
					$view = new SignupView($pageInfo, $model);
					break;
				case 'profile': include 'views/profileView.php';
					$view = new ProfileView($pageInfo, $model);
					break;
				case 'editProfile': include 'views/editProfileView.php';
					$view = new EditProfileView($pageInfo, $model);
					break;
				case 'uploadDesign': include 'views/uploadDesignView.php';
					$view = new UploadDesignView($pageInfo, $model);
					break;
				
				case 'editDesign': include 'views/editDesignView.php';
					$view = new EditDesignView($pageInfo, $model);
					break;
				case 'download': include 'views/downloadView.php';
					$view = new DownloadView($pageInfo, $model);
					break;
				case 'addWinner': include 'views/addWinnerView.php';
					$view = new AddWinnerView($pageInfo, $model);
					break;
				case 'addResources': include 'views/addResourcesView.php';
					$view = new AddResourcesView($pageInfo, $model);
					break;
				case 'search': include 'views/searchView.php';
					$view = new SearchView($pageInfo, $model);
					break;
				case 'designView': include 'views/designView.php';
					$view = new DesignView($pageInfo, $model);
					break;	
				case 'challengeText': include 'views/challengeText.php';
					$view = new TextView($pageInfo, $model);
					break;
				case 'challengeEntries': include 'views/challengeEntries.php';
					$view = new EntriesView($pageInfo, $model);
					break;
					
					
				
					
			}
			
			echo $view->displayPage();
	}
}

$pageSelect = new pageSelector;
$pageSelect->run();

?>
































































































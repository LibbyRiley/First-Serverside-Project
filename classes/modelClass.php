<?php

//	This script extends the Dbase class for validation 
//	and user or admin accessible functions - add/edit/delete (pages, designs, and challenges)
//  and processes new user signup
include 'classes/dbClass.php';
include 'classes/uploadClass.php';
include 'classes/resizeImageClass.php';


class Model extends Dbase
{
	private $validate;	//	holds the object for the validate class
	public $loginMsg;	//	this is the message to be displayed on the login form
	public $adminLoggedIn;	//	boolean variable which is set to true if admin is logged in
	public $loggedIn;	//	boolean variable which is set to true if any user is logged in
	
	
	public function __construct()
	{
		parent::__construct();	//	call the constructor of the parent class
		$pagesToValidate = array('addChallenge', 'editChallenge', 'signup', 'addResources', 'uploadDesign', 'editProfile', 'addWinner');
		//	NEEDS ARRAY OF PAGES TO VALIDATE.
		if(in_array($_GET['page'], $pagesToValidate)) 
		{
			include 'classes/validateClass.php';
			$this->validate = new Validate;
		}
	}
	
	
	
	public function checkUserSession()
	{
		//	check if user is logging out
		if($_GET['page'] == 'logout')
		{
			unset($_SESSION['userName']);
			unset($_SESSION['userPrivilege']);
			unset($_SESSION['userID']);
			$this->loggedIn = false;
			$this->adminLoggedIn = false;
			$this->loginMsg = 'You have successfully logged out.';
		}
		//	check if login form has been submitted
		if($_POST['login'])
		{
			if($_POST['userName'] && $_POST['userPassword'])
			{
				$this->validateUser();
				
				
				//	if admin login is not successful...
				if($this->loggedIn) {
					if (!$this->adminLoggedIn) {
						header('Location: index.php?page=profile&uID='.$_SESSION['userID']);
					}	
				}
				else {
				
					$this->loginMsg = 'Sorry, login failed!';
				}
				
				
				
			} else {
				$this->loginMsg = 'Please enter username and password';
			}
		} else {
			
			if($_SESSION['userName'])
			{	//	check to see if a user is logged in
				$this->loggedIn = true;
				
			//	check if admin is logged in
				if($_SESSION['userPrivilege'] == 'admin')
				{
					$this->adminLoggedIn = true;
				}
			}
		
		}
	
	}
	
	public function processAddUser()
	{	
		//	validate the challenge form entries
		$vresult = $this->validateNewUser();
		//	test if the validation result is not good, then exit out of the function
		if(!$vresult['ok'])
		{
			return $vresult;
			
		}
		
		
		//	call the function upload and resize the image
		$uresult = $this->uploadAndResizeUserPic();
		//	check if file upload and resize is successful
		if($uresult['ok'])
		{
			//	$uresult['msg'] = 'Image uploaded/resized successfully. ';
			$iresult = $this->insertUser($uresult['userPic']);
			
			if ($iresult['userID']) {		
				$_SESSION['userName'] = $_POST['userName'];
				$_SESSION['userPrivilege'] = 'user';
				$_SESSION['userID'] = $iresult['userID'];
				$this->loggedIn = true;
				header('Location: index.php?page=profile&uID='.$iresult['userID']);
				exit;
			}	
		
		} else {
			$iresult['msg'] = 'Unable to upload/resize image. ';
		}
		$userResult = array_merge($uresult, $iresult);
		return $userResult;
		
	}
	
	private function validateNewUser()
	{	//	this function validates the input from the signup form

		$result['userNameMsg'] = $this->validate->checkRequired($_POST['userName']);
		$result['userPasswordMsg'] = $this->validate->checkRequired($_POST['userPassword']);
		$result['userConfirmMsg'] = $this->validate->checkRequired($_POST['userConfirm']);
		$result['passwordMsg'] = $this->validate->checkMatchingPasswords(($_POST['userPassword']), ($_POST['userConfirm']));
		$result['userEmailMsg'] = $this->validate->checkRequired($_POST['userEmail']);
		$result['userPicMsg'] = $this->validate->checkRequired($_POST['userPic']);
		
		$result['ok'] = $this->validate->checkErrorMessages($result);
		
		$user = $this->getUser();
			
			
		if(is_array($user))
		{
			$_SESSION['userName'] = $user['userName'];
			$_SESSION['userPrivilege'] = $user['userPrivilege'];
			$_SESSION['userID'] = $user['userID'];
			$this->loggedIn = true;
			if($user['userPrivilege'] == 'admin') {
				$this->adminLoggedIn = true;
			}
		}
			
		return $result;
	}
	
	public function processUpdateUser()
	{
		//	when updating user, validates u
		$vresult = $this->validateUpdatedUser();
		//	test if the validation result is not good, then exit out of the function
		if(!$vresult['ok'])
		{
			return $vresult;
		}
		//	call the function upload and resize the image if an image is being uploaded from the form.
		if($_FILES['userPic']['name'])
		{
			$uresult = $this->uploadAndResizeUserPic();
			//	check if file upload and resize is successful
			if($uresult['ok'])
			{
				if($uresult['userPic'] != $_POST['userPic'])
				{
					@unlink('uploads/images/'.$_POST['userPic']);
					@unlink('uploads/thumbnails/'.$_POST['userPic']);
				}
				$_POST['userPic'] = $uresult['userPic'];
				$uresult['msg'] = '';
			} else {
				$uresult['msg'] = 'Unable to upload/resize image. ';
			}
		}
		$uresult['msg'] .= $this->updateUser();
		
		return $uresult;
	
	}
	
	
	private function validateUpdatedUser()
	{
		
		$result['userNameMsg'] = $this->validate->checkRequired($_POST['userName']);
		$result['userEmailMsg'] = $this->validate->checkRequired($_POST['userEmail']);
		$result['ok'] = $this->validate->checkErrorMessages($result);
		
		return $result;

	
	}
	
	private function uploadAndResizeUserPic()
	{	//	this function uploads and resizes the user's profile picture	
	
		if(!$_FILES['userPic']['name'])
		{	//	if $_FILES is not set, there is nothing to process, so exit
			return false;
		}
		$imgsPath = 'uploads/images';
		$thumbImgsPath = 'uploads/thumbnails';
		$fileTypes = array
		(
			'image/jpeg',
			'image/jpg',
			'image/pjpeg',
			'image/gif',
			'image/png'
		);
		$upload = new Upload('userPic',$fileTypes,$imgsPath);
		//	instantiate the upload class and call the function isUploaded to process the actual uploading.
		$returnFile = $upload->isUploaded();
		//	if no file has been uploaded
		if(!returnFile)
		{
			$result['uploadMsg'] = $upload->msg;
			$result['ok'] = false;
			return $result;
		}
		//	proceed with resize, set the file paths for big images and thumbnails.
			
		$fileName = baseName($returnFile);
		$bigPath = $imgsPath.'/'.$fileName;
		$thumbPath = $thumbImgsPath.'/'.$fileName;
		copy($returnFile, $thumbPath);
		//	if copy fails, exit
		if(!file_exists($thumbPath))
		{
			return false;
		} 
		$imgInfo = getimagesize($returnFile);
		//	resize longer side of image to 150px
		if($imgInfo[0]>150 || $imgInfo[1]>150)
		{
			$resizeObj = new ResizeImage($thumbPath, 150, $thumbImgsPath,'');
			if(!$resizeObj->resize())
			{	//	if resize to 150px unsuccessful
				echo 'Unable to resize image to 150px.';
			}
				
		}
		rename($returnFile, $bigPath);
			
		if($imgInfo[0]>400 || $imgInfo[1]>400)
		{
			$resizeObj = new ResizeImage($bigPath, 400, $imgsPath,'');
			if(!$resizeObj->resize())
			{	//	if resize to 400px unsuccessful
				echo 'Unable to resize image to 400px.';
			}
				
		}
		//	final check that resized images exist in their respective folders.
		if(file_exists($thumbPath) && file_exists($bigPath))
		{
			$result['userPic'] = basename($thumbPath);
			$result['ok'] = true;
			return $result;
		} else {
			return false;
		}
			
	}
	
	
	
	private function validateUser()
	{	//	this function validates the input from the login against the users table 
		
			$user = $this->getUser();
			
			
			if(is_array($user))
			{
				$_SESSION['userName'] = $user['userName'];
				$_SESSION['userPrivilege'] = $user['userPrivilege'];
				$_SESSION['userID'] = $user['userID'];
				$this->loggedIn = true;
				if($user['userPrivilege'] == 'admin') {
					$this->adminLoggedIn = true;
				}
			}
		
		
		return $result;
		
	}
	
	
	private function validatePage()
	{	//	checks whether there are any error messages on the page 
		//	using 'checkRequired'(validateClass.php) and 'checkErrorMessages'(validateClass.php)
	
		extract($_POST);
		$result['pTitleMsg'] = $this->validate->checkRequired($pageTitle);
		
		$result['ok'] = $this->validate->checkErrorMessages($result);
		return $result;
	}
	
	
	
	public function processEditPage()
	{	//	when editing page, validates page (See 'validatePage' above) 
		//	and then updates page (See 'updatePage' in dbClass.php)
		$result = $this->validatePage();
		if($result['ok'])
		{
			$result['msg'] = $this->updatePage();
			
		}
		
		return $result;
	}


	
	private function uploadAndResizeImage()
	{	//	this function uploads and resizes the challenge images	
		if(!$_FILES['challengeImage']['name'])
		{	//	if $_FILES is not set, there is nothing to process, so exit
			return false;
		}
		$imgsPath = 'uploads/images';
		$thumbImgsPath = 'uploads/thumbnails';
		$fileTypes = array
		(
			'image/jpeg',
			'image/jpg',
			'image/pjpeg',
			'image/gif',
			'image/png'
		);
		$upload = new Upload('challengeImage',$fileTypes,$imgsPath);
		//	instantiate the upload class and call the function isUploaded to process the actual uploading.
		$returnFile = $upload->isUploaded();
		//	if no file has been uploaded
		if(!returnFile)
		{
			$result['uploadMsg'] = $upload->msg;
			$result['ok'] = false;
			return $result;
		}
		//	proceed with resize, set the file paths for big images and thumbnails.
			
		$fileName = baseName($returnFile);
		$bigPath = $imgsPath.'/'.$fileName;
		$thumbPath = $thumbImgsPath.'/'.$fileName;
		copy($returnFile, $thumbPath);
		//	if copy fails, exit
		if(!file_exists($thumbPath))
		{
			return false;
		} 
		$imgInfo = getimagesize($returnFile);
		//	resize longer side of image to 150px
		if($imgInfo[0]>150 || $imgInfo[1]>150)
		{
			$resizeObj = new ResizeImage($thumbPath, 150, $thumbImgsPath,'');
			if(!$resizeObj->resize())
			{	//	if resize to 150px unsuccessful
				echo 'Unable to resize image to 150px.';
			}
				
		}
		rename($returnFile, $bigPath);
			
		if($imgInfo[0]>400 || $imgInfo[1]>400)
		{
			$resizeObj = new ResizeImage($bigPath, 400, $imgsPath,'');
			if(!$resizeObj->resize())
			{	//	if resize to 400px unsuccessful
				echo 'Unable to resize image to 400px.';
			}
				
		}
		//	final check that resized images exist in their respective folders.
		if(file_exists($thumbPath) && file_exists($bigPath))
		{
			$result['challengeImage'] = basename($thumbPath);
			$result['ok'] = true;
			return $result;
		} else {
			return false;
		}
			
	}
	
	
	
	
	//	PUT ADD, EDIT, DELETE, and VALIDATE CHALLENGE FUNCTIONS HERE (based on the Bakery's 'challenge' functions)
	public function validateChallenge($mode)
	{	//	this function validates the entries in the challenge form and..
		//	ensuring that all the fields have been filled out.
		
		$result['cTitleMsg'] = $this->validate->checkRequired($_POST['challengeTitle']);
		$result['cBylineMsg'] = $this->validate->checkRequired($_POST['challengeByline']);
		$result['cDescMsg'] = $this->validate->checkRequired($_POST['challengeDescription']);
		$result['rHeadingMsg'] = $this->validate->checkRequired($_POST['challengeResourceHeading']);
		if($mode == 'Add')
	 	{
	 		$result['cImageMsg'] = $this->validate->checkRequired($_FILES['challengeImage']['name']);
	 	}
		//	check if there is at leaset one error messages in the array of error messages($result)
		$result['ok'] = $this->validate->checkErrorMessages($result);
		return $result;
	
	}
	
	public function processAddChallenge()
	{	//	when adding challenge, validates challenge (See validateChallenge above)
		$vresult = $this->validateChallenge('Add');
		//	test if the validation result is not good, then exit out of the function
		if(!$vresult['ok'])
		{
			return $vresult;
		}
		//	call the function upload and resize the image
		$iresult = $this->uploadAndResizeImage();
		//	check if file upload and resize is successful
		if($iresult['ok'])
		{
			$iresult['msg'] = 'Image uploaded/resized successfully. ';
			$iresult['msg'] .= $this->insertChallenge($iresult['challengeImage']);
		
		} else {
			$iresult['msg'] = 'Unable to upload/resize image. ';
		}
		return $iresult;
	
	}
	
	public function processUpdateChallenge()
	{	//	when adding challenge, validates challenge (See validateChallenge above)
		$vresult = $this->validateChallenge('Edit');
		//	test if the validation result is not good, then exit out of the function
		if(!$vresult['ok'])
		{
			return $vresult;
		}
		//	call the function upload and resize the image if an image is being uploaded from the form.
		if($_FILES['challengeImage']['name'])
		{
			$uresult = $this->uploadAndResizeImage();
			//	check if file upload and resize is successful
			if($uresult['ok'])
			{
				if($uresult['challengeImage'] != $_POST['challengeImage'])
				{
					@unlink('uploads/images/'.$_POST['challengeImage']);
					@unlink('uploads/thumbnails/'.$_POST['challengeImage']);
				}
				$_POST['challengeImage'] = $uresult['challengeImage'];
				$uresult['msg'] = 'Image uploaded/resized successfully. ';
			} else {
				$uresult['msg'] = 'Unable to upload/resize image. ';
			}
		}
		$uresult['msg'] .= $this->updateChallenge($_POST['challengeID']);
		return $uresult;
	}
	
	
	
	//	Add, and validate new resources. //
	
	public function processAddResource()
	{
	
	//	when adding resource, validates resource (See validateresource below)
		$vresult = $this->validateResource('submitRes');
		//	test if the validation result is not good, then exit out of the function
		if(!$vresult['ok'])
		{
			return $vresult;
		}
		$result = $this->insertResource();	//	
		return $result;
	
	}
	
	
	//	validate new resource
	public function validateResource($mode)
	{
		extract($_POST);
		$result['rTitleMsg'] = $this->validate->checkRequired($_POST['resourceTitle']);
		$result['rLinkMsg'] = $this->validate->checkRequired($_POST['resourceLink']);
		
		$result['ok'] = $this->validate->checkErrorMessages($result);
		
		return $result;
	
	}
	
	
	//	process uploading a new design entry
	public function processUploadDesign()
	{
		//	send to validate
		$vresult = $this->validateDesign('Upload');
		
		if(!$vresult['ok'])
		{
			return $vresult;
		}
		
		$iresult = $this->uploadAndResizeDesign();
		//	check if file upload and resize is successful
		if($iresult['ok'])
		{
			$iresult['msg'] = 'Image uploaded/resized successfully. ';
			$iresult['msg'] .= $this->insertDesign($iresult['designPath']);
			header('Location: index.php?page=profile&uID='.$_SESSION['userID']);
		
		} else {
			$iresult['msg'] = 'Unable to upload/resize image. ';
		}
		return $iresult;
	
	
	}
	
	//	Validate new design
	public function validateDesign()
	{
		extract($_POST);
		$result['designTitle'] = $this->validate->checkRequired($_POST['designTitle']);
		$result['designDescription'] = $this->validate->checkRequired($_POST['designDescription']);
		$result['designKeywords'] = $this->validate->checkRequired($_POST['designKeywords']);
		$result['designPath'] = $this->validate->checkRequired($_FILES['designPath']['name']);
		
		$result['ok'] = $this->validate->checkErrorMessages($result);
		
		return $result;
	
	}
	
	private function uploadAndResizeDesign()
	{	//	this function uploads and resizes the challenge images	
		if(!$_FILES['designPath']['name'])
		{	//	if $_FILES is not set, there is nothing to process, so exit
			return false;
		}
		$imgsPath = 'uploads/images';
		$thumbImgsPath = 'uploads/thumbnails';
		$fileTypes = array
		(
			'image/jpeg',
			'image/jpg',
			'image/pjpeg',
			'image/gif',
			'image/png'
		);
		$upload = new Upload('designPath',$fileTypes,$imgsPath);
		//	instantiate the upload class and call the function isUploaded to process the actual uploading.
		$returnFile = $upload->isUploaded();
		//	if no file has been uploaded
		if(!returnFile)
		{
			$result['uploadMsg'] = $upload->msg;
			$result['ok'] = false;
			return $result;
		}
		//	proceed with resize, set the file paths for big images and thumbnails.
			
		$fileName = baseName($returnFile);
		$bigPath = $imgsPath.'/'.$fileName;
		$thumbPath = $thumbImgsPath.'/'.$fileName;
		copy($returnFile, $thumbPath);
		//	if copy fails, exit
		if(!file_exists($thumbPath))
		{
			return false;
		} 
		$imgInfo = getimagesize($returnFile);
		//	resize longer side of image to 150px
		if($imgInfo[0]>150 || $imgInfo[1]>150)
		{
			$resizeObj = new ResizeImage($thumbPath, 150, $thumbImgsPath,'');
			if(!$resizeObj->resize())
			{	//	if resize to 150px unsuccessful
				echo 'Unable to resize image to 150px.';
			}
				
		}
		rename($returnFile, $bigPath);
			
		if($imgInfo[0]>800 || $imgInfo[1]>800)
		{
			$resizeObj = new ResizeImage($bigPath, 800, $imgsPath,'');
			if(!$resizeObj->resize())
			{	//	if resize to 800px unsuccessful
				echo 'Unable to resize image to 800px.';
			}
				
		}
		//	final check that resized images exist in their respective folders.
		if(file_exists($thumbPath) && file_exists($bigPath))
		{
			$result['designPath'] = basename($thumbPath);
			$result['ok'] = true;
			return $result;
		} else {
			return false;
		}
			
	}
	
	public function processAddWinner()
	{
		//	when adding winner, validates about and quote 
		$vresult = $this->validateWinner();
		//	test if the validation result is not good, then exit out of the function
		if(!$vresult['ok'])
		{
			return $vresult;
		}
		
		$iresult = $this->uploadAndResizeWinnerImage();
		//	check if file upload and resize is successful
		if($iresult['ok'])
		{
			$iresult['msg'] = 'Image uploaded/resized successfully. ';
			$iresult['msg'] .= $this->insertWinner($iresult['winnerImage']);
		
		} else {
			$iresult['msg'] = 'Unable to upload/resize image. ';
		}
		
		return $iresult;
	}
	
	
	public function validateWinner()
	{
		extract($_POST);
		$result['wAboutMsg'] = $this->validate->checkRequired($_POST['winnerAbout']);
		$result['wQuoteMsg'] = $this->validate->checkRequired($_POST['winnerQuote']);
		$result['wImage'] = $this->validate->checkRequired($_FILES['winnerImage']['name']);

		$result['ok'] = $this->validate->checkErrorMessages($result);
		
		return $result;
	
	}
	
	private function uploadAndResizeWinnerImage()
	{	//	this function uploads and resizes the challenge images	
		if(!$_FILES['winnerImage']['name'])
		{	//	if $_FILES is not set, there is nothing to process, so exit
			return false;
		}
		$imgsPath = 'uploads/images';
		$thumbImgsPath = 'uploads/thumbnails';
		$fileTypes = array
		(
			'image/jpeg',
			'image/jpg',
			'image/pjpeg',
			'image/gif',
			'image/png'
		);
		$upload = new Upload('winnerImage',$fileTypes,$imgsPath);
		//	instantiate the upload class and call the function isUploaded to process the actual uploading.
		$returnFile = $upload->isUploaded();
		//	if no file has been uploaded
		if(!returnFile)
		{
			$result['uploadMsg'] = $upload->msg;
			$result['ok'] = false;
			return $result;
		}
		//	proceed with resize, set the file paths for big images and thumbnails.
			
		$fileName = baseName($returnFile);
		$bigPath = $imgsPath.'/'.$fileName;
		$thumbPath = $thumbImgsPath.'/'.$fileName;
		copy($returnFile, $thumbPath);
		//	if copy fails, exit
		if(!file_exists($thumbPath))
		{
			return false;
		} 
		$imgInfo = getimagesize($returnFile);
		//	resize longer side of image to 150px
		if($imgInfo[0]>150 || $imgInfo[1]>150)
		{
			$resizeObj = new ResizeImage($thumbPath, 150, $thumbImgsPath,'');
			if(!$resizeObj->resize())
			{	//	if resize to 150px unsuccessful
				echo 'Unable to resize image to 150px.';
			}
				
		}
		rename($returnFile, $bigPath);
			
		if($imgInfo[0]>800 || $imgInfo[1]>800)
		{
			$resizeObj = new ResizeImage($bigPath, 800, $imgsPath,'');
			if(!$resizeObj->resize())
			{	//	if resize to 800px unsuccessful
				echo 'Unable to resize image to 800px.';
			}
				
		}
		//	final check that resized images exist in their respective folders.
		if(file_exists($thumbPath) && file_exists($bigPath))
		{
			$result['winnerImage'] = basename($thumbPath);
			$result['ok'] = true;
			return $result;
		} else {
			return false;
		}
			
	}
	
	
	
	

}
 

 






















?>
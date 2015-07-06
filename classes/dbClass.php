<?php
// This class contains all the methods to connect to the database
// and query the tables of the database

	include '../config.php';
	
	class Dbase
	{
		private $db;
		
		public function __construct()
		//	this function establishes a database connection
		{	
			try {
				$this->db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);	
				if (mysqli_connect_errno()) {
					throw new Exception("Unable to establish database connection");
				}
			}	
			catch(Exception $e)
			{
				die($e->getMessage());
			}
		}
		
		
		
		public function sanitiseInput()
		{
			foreach($_POST as &$post)
			{
				if (!is_array($post)) {
					$post = $this->db->real_escape_string($post);
				}	
			}
		}
		
		
		public function getPageInfo($page) 
		{ // reads the page record corresponding to the page passed
			$qry = "SELECT pageID, pageName, pageTitle, pageHeading, pageKeywords, pageDescription, pageContent FROM pages WHERE pageName = '$page'";
			$rs = $this->db->query($qry);
			if($rs) {
				if($rs->num_rows>0) {
					$pageInfo = $rs->fetch_assoc();
					return $pageInfo;
				} else {
					echo 'This page does not exist';
				}
			} else {
				echo 'Error executing query'.$qry;
			}
		}
			
		
		public function updatePage()
		{
			if(!get_magic_quotes_gpc())
			{
				$this->sanitiseInput();
			}
			extract($_POST);
			$qry = "UPDATE pages SET pageTitle = '$pageTitle', pageHeading = '$pageHeading', ,pageByline = 'pageByline', pageKeywords = '$pageKeywords', pageDescription = '$pageDescription', pageContent = '$pageContent' WHERE pageName = '$pageName'";
			
			$rs = $this->db->query($qry);
			if($rs && $this->db->affected_rows > 0) 
			{
				$msg = 'Page updated.';
			} else {
				echo 'Error updating page '.$qry;
			}
			return $msg;
		}
		
		
		public function getUser()
		{	//	get user info using the username and password entered.
			extract ($_POST);
			$password = sha1($userPassword);	//	to encrypt the password from the form.
			$qry = "SELECT userID, userName, userPrivilege FROM users WHERE userName = '$userName' and userPassword ='$password'";
			$rs = $this->db->query($qry);
			if($rs) {
				if($rs->num_rows>0) {
					$user = $rs->fetch_assoc();
					return $user;
				}
			} else {
				$msg = 'Error executing query'.$qry;
			}
		}
		
		
		public function getUserByID($uID)
		{
			$qry = "SELECT userID, userName, userOccupation, userLocation, userEmail, userPic, userWebsite, userBio FROM users WHERE userID = $uID";
			$rs = $this->db->query($qry);
			if($rs) {
				if($rs->num_rows>0) 
				{
					$user = $rs->fetch_assoc();
					
					return $user;
				} else {
					$msg = 'There is no user found';
				}
			} else {
				$msg = 'Error executing query'.$qry;
			}
		}
		
		public function getLatestChallenge($cID) 
		{	// reads the challenges record to get info about all the challenges
			$qry = "SELECT challengeID, challengeTitle, challengeDescription, challengeImage, challengeByline, challengeImageAlt, challengeDate, challengeResourceHeading, challengeText FROM challenges ORDER BY challengeID DESC LIMIT 0,1";
			$rs = $this->db->query($qry);
			if($rs) {
				if($rs->num_rows>0) 
				{
					$challenge = $rs->fetch_assoc();
					
					return $challenge;
				} else {
					$msg = 'There are no challenges found';
				}
			} else {
				echo 'Error executing query'.$qry;
			}
		}
		
		public function getChallengeByID($cID) 
		{	// reads the challenges record to get info about all the challenges
			$qry = "SELECT challengeID, challengeTitle, challengeDescription, challengeImage, challengeByline, challengeImageAlt, challengeDate, challengeResourceHeading, challengeText FROM challenges WHERE challengeID = '$cID'";
			$rs = $this->db->query($qry);
			if($rs) {
				if($rs->num_rows>0) 
				{
					$challenge = $rs->fetch_assoc();
					
					return $challenge;
				} else {
					$msg = 'There are no challenges found';
				}
			} else {
				echo 'Error executing query'.$qry;
			}
		}
		
		
		public function getResourcesByCID($cID)
		{	//	reads resources record and the challenges_resources record to get the resources (Title and Link) related to the current challenge.
		$qry = "SELECT resourceID, resourceTitle, resourceLink FROM resources WHERE resourceID IN (SELECT resourceID FROM challenges_resources WHERE challengeID = $cID) ";
		$rs = $this->db->query($qry);
			if($rs) {
				if($rs->num_rows>0) 
				{
				
					$resources = array();
					while ($row = $rs->fetch_assoc()) 
					{
					$resources[] = $row;
					}
					return $resources;
				} else {
					$msg = 'There are no resources found';
				}
			} else {
				echo 'Error executing query'.$qry;
			}
		
		}
		
		public function getResourceIdsByCID($cID)
		{	//	reads resources record and the challenges_resources record to get the resources (Title and Link) related to the current challenge.
			$qry = "SELECT resourceID FROM resources WHERE resourceID IN (SELECT resourceID FROM challenges_resources WHERE challengeID = $cID)";
			$rs = $this->db->query($qry);
			if($rs) {
				if($rs->num_rows>0) 
				{
				
					$resources = array();
					while ($row = $rs->fetch_assoc()) 
					{
					$resources[] = $row['resourceID'];
					}
					return $resources;
				} else {
					$msg = 'There are no resources found';
				}
			} else {
				echo 'Error executing query'.$qry;
			}
		
		}
		
		public function updateChallenge($challengeID)
		{	//	insert new challenge to the database
			if(!get_magic_quotes_gpc())
			{
				$this->sanitiseInput();
			}
			extract($_POST);
			$qry = "UPDATE challenges SET challengeTitle = '$challengeTitle', challengeByline = '$challengeByline', challengeDescription = '$challengeDescription', challengeImage = '$challengeImage', challengeResourceHeading = '$challengeResourceHeading', challengeText = '$challengeText' WHERE challengeID = '$challengeID'";
			//	test that a record has been created
			$rs = $this->db->query($qry);
			if($rs && $this->db->affected_rows > 0) 
			{
				$msg = 'Challenge record updated.';
			
				$this->updateChallengeResources($challengeID);
			} else {
				$msg = 'Error inserting challenge '.$qry;
			}
			return $msg;
			
		}
		
		public function updateChallengeResources($challengeID)
		{
						//  get all resources that are currently in the challenges_resources table based on the challengeID
			$resIDs = $this->getResourceIdsByCID($challengeID);
			if (is_array($_POST['resource'])) {
				foreach($_POST['resource'] as $resourceID)
				{
					if(is_array($resIDs))	//	? where is $resIDs coming from ?
					{				
						if (!in_array($resourceID, $resIDs)) {
							//	insert the resource in the table
							$msg = $this->insertUpdatedChallengeResources($challengeID, $resourceID);
						}
					}	
				}
			}
			
			if(is_array($resIDs))
			{
				foreach($resIDs as $resID)
				{
					if(is_array($_POST['resource']))
					{
						if(!in_array($resID, $_POST['resource']))
						{
							// delete the resource from the table
							$msg .= $this->deleteChallengeResources($resID);
						}
					}
				}
			}
			return $msg;
			
		}
		
		public function insertUpdatedChallengeResources($cID, $rID)
		{	//	insert updated resource IDs to Challenges_Resources table to record where challengeID == $cID
			extract($_POST);
			$resCount = count($resource);	//	counts number of resourceID records to insert
			$qry = '';
			//	create loop to create individual reocrds in the tble access $_POST['resource']
			for($i = 0; $i < $resCount; $i++)
			{
				$qry .= "UPDATE challenges_resources SET resourceID = $resource[$i], $challengeID)WHERE challengeID = $cID";
			}
	
			$rs = $this->db->query($qry);
			if($rs && $this->db->affected_rows > 0) 
			{
				$msg = 'Resources have been added/updated!';
			} else {
				$msg = 'Error inserting resources '.$qry;
			}
			return $msg;
			
		}
		
		public function insertChallenge($challengeImage)
		{	//	insert new challenge to the database
			if(!get_magic_quotes_gpc())
			{
				$this->sanitiseInput();
			}
			extract($_POST);
			$qry = "INSERT INTO challenges VALUES (NULL, '$challengeTitle', '$challengeByline', '$challengeDescription', 'CURDATE()', '$challengeImage', ' ', '$challengeResourceHeading', '$challengeText')";
			//	test that a record has been created
			$rs = $this->db->query($qry);
			if($rs && $this->db->affected_rows > 0) 
			{
				$msg = 'New challenge record created.';
				$challengeID = $this->db->insert_id;  // pls check syntx
				$this->insertChallengeResources($challengeID);
			} else {
				$msg = 'Error inserting challenge '.$qry;
			}
			return $msg;
			
		}
		
		
		
		public function insertChallengeResources($challengeID)
		{
			extract($_POST);
			$resCount = count($resource);	//	counts number of resourceID records to insert
			$qry = 'INSERT INTO challenges_resources VALUES ';
			//	create loop to create individual reocrds in the tble access $_POST['resource']
			for($i = 0; $i < $resCount - 1; $i++)
			{
//				$qry .= "INSERT INTO challenges_resources VALUES (NULL, $resource[$i], $challengeID)";
				$qry .= "(NULL, $resource[$i], $challengeID), ";
			}
			$qry .= "(NULL, $resource[$i], $challengeID) ";
			$rs = $this->db->query($qry);
			if($rs && $this->db->affected_rows > 0) 
			{
				$msg = 'Resources have been added/updated!';
			} else {
				$msg = 'Error inserting resources '.$qry;
			}
			return $msg;
			
		}
		
		public function deleteChallengeResources()
		{
			$qry = "DELETE FROM challenge_resources WHERE resourcesID = $resID";
			$rs = $this->db->query($qry);
			if ($rs)
			{
				if($this->db->affected_rows>0)
				{
					$result['msg'] = 'Resource successfully updated.';
					$result['ok'] = true;
				} else {
					$result['msg'] = 'No resources deleted.';
					$result['ok'] = false;
				
				}
				return $result;
			} else {
				$msg = 'Error executing query'.$qry;
				return false;
			
			}
		
		}
		
		
		
		public function deleteChallenge()
		{
			$cID = $_POST['challengeID'];
			$qry = "DELETE FROM challenges WHERE challengeID = $cID";
			$rs = $this->db->query($qry);
			if ($rs)
			{
				if($this->db->affected_rows>0)
				{
					$result['msg'] = 'Challenge successfully deleted.';
					$result['ok'] = true;
				} else {
					$result['msg'] = 'No challenge deleted.';
					$result['ok'] = false;
				
				}
				return $result;
			} else {
				$msg = 'Error executing query'.$qry;
				return false;
			
			}
		}
		
		public function insertUser($userPic) 
		{
			//	insert new user to the database
			if(!get_magic_quotes_gpc())
			{
				$this->sanitiseInput();
			}
			extract($_POST);
			
			$userPassword = sha1($userPassword);
			$qry = "INSERT INTO users VALUES (NULL, '$userName', '$userPassword', ' ', ' ', '$userEmail', 'user', '$userPic', ' ', ' ')";
			//	test that a record has been created
			$rs = $this->db->query($qry);
			if($rs && $this->db->affected_rows > 0) 
			{
				
				$result['imsg'] = 'You have been signed up for Type Circus!';
				$result['userID'] = $this->db->insert_id;	
				$result['ok'] = true;
			} else {
				$result['imsg'] = 'This username has already been taken. Please try a new username.';
				$result['ok'] = false;
			}
			return $result;
			
		}
		
		
		public function updateUser()
		{
			//	updating user info to the user table  on the database
			if(!get_magic_quotes_gpc())
			{
				$this->sanitiseInput();
			}
			extract($_POST);
			$userPassword = $sha1[$userPassword];
			$qry = "UPDATE users SET userName = '$userName', userEmail = '$userEmail', userOccupation = '$userOccupation', userLocation = '$userLocation', userBio = '$userBio', userWebsite = '$userWebsite', userPic = '$userPic' WHERE userID = '$userID'";
			//	test that a record has been created
			$rs = $this->db->query($qry);
			if($rs && $this->db->affected_rows > 0) 
			{
				$msg = 'User info has been updated.';
			} else {
				//echo 'Error updating user info '.$qry;
				$msg = 'Error updating user info';
			}
			return $msg;
			
		}
		
		
			
		public function getDesignsByUserID($uID)
		
		{	// reads the designs record to get info about all the designs where userID = $uID
			$qry = "SELECT designID, designTitle, designDescription, designPath, challengeID FROM designs WHERE userID = '$uID'";
			$rs = $this->db->query($qry);
			if($rs) {
				if($rs->num_rows>0) 
				{
					$designs = array();
					while($row = $rs->fetch_assoc())
					{
						$designs[] = $row;
					}
					return $designs;
				} else {
					$msg = 'There are no designs found';
				}
			} else {
				$msg = 'Error executing query'.$qry;
			}
		}
		
		
		
		public function getAllResources()
		{	//	reads resources record to get the resources (Title and Link)
		$qry = "SELECT resourceID, resourceTitle, resourceLink FROM resources";
		$rs = $this->db->query($qry);
			if($rs) {
				if($rs->num_rows>0) 
				{
				
					$resources = array();
					while ($row = $rs->fetch_assoc()) 
					{
						$resources[] = $row;
					}
					return $resources;
				} else {
					$msg = 'There are no resources found';
				}
			} else {
				$msg = 'Error executing query'.$qry;
			}
		
		}
		
		
		public function insertResource()
		{	//	inserts new resource into resource table
		
			if(!get_magic_quotes_gpc())
			{
				$this->sanitiseInput();
			}
			extract($_POST);
		
			$qry = "INSERT INTO resources VALUES (NULL, '$resourceTitle', 'resourceLink')";
			//	test that a record has been created
			$rs = $this->db->query($qry);
			if($rs && $this->db->affected_rows > 0) 
			{
				
				$msg = 'New resource has been added!';
			} else {
				$msg = 'Error inserting resource '.$qry;
			}
			return $msg;
		
		
		}
		
		
		public function getLatestWinner() 
		{	// reads the challenges record to get info about all the challenges
			$qry = "SELECT winnerID, designID, winnerAbout, winnerQuote, userID, winnerImage FROM winners ORDER BY winnerID DESC LIMIT 0,1";
			$rs = $this->db->query($qry);
			if($rs) {
				if($rs->num_rows>0) 
				{
					$winner = $rs->fetch_assoc();
					
					return $winner;
				} else {
					$msg = 'There is no winner found';
				}
			} else {
				$msg = 'Error executing query'.$qry;
			}
		}
		
		
		
		public function insertWinner($winnerImage)
		{
			if(!get_magic_quotes_gpc())
			{
				$this->sanitiseInput();
			}
			extract($_POST);
			$qry = "INSERT INTO winners VALUES (NULL, '$designID', '$winnerAbout', '$winnerQuote', '$userID', '$winnerImage')";
			//	test that a record has been created
			$rs = $this->db->query($qry);
			if($rs && $this->db->affected_rows > 0) 
			{
				$msg = 'Winner has been uploaded';
			} else {
				$msg = 'Error inserting new winner '.$qry;
			}
			return $msg;
		
		}
		
		public function insertDesign($designPath)
		{
			if(!get_magic_quotes_gpc())
			{
				$this->sanitiseInput();
			}
			extract($_POST);
			$qry = "INSERT INTO designs VALUES (NULL, '$designTitle', '$designDescription', '$designKeywords', '$userID', '$challengeID', '$designPath')";
			//	test that a record has been created
			$rs = $this->db->query($qry);
			if($rs && $this->db->affected_rows > 0) 
			{
				$msg = 'Design has been uploaded';
			} else {
				$msg = 'Error inserting new design '.$qry;
			}
			return $msg;
		
		}
		
		public function searchDB($search)
		{	//	search the db for design that match keywords
		
			$qry = "SELECT designID, designTitle, designDescription, designPath FROM designs WHERE designKeywords LIKE '%$search%'";
			$rs = $this->db->query($qry);
			if($rs) {
				if($rs->num_rows>0) 
				{
					$designs = array();
					while ($row = $rs->fetch_assoc()) 
					{
						$designs[] = $row;
					}
					
					return $designs;
				} else {
					$msg = 'There are no matching results found';
				}
			} else {
				$msg = 'Error executing query'.$qry;
			}
		
		
		
		}
		
		public function getDesignByID($dID)
		
		{	// reads the designs record to get info about the design where designID = $dID
			$qry = "SELECT designID, designTitle, designDescription, designPath, userID, challengeID FROM designs WHERE designID = '$dID'";
			$rs = $this->db->query($qry);
			if($rs) {
				if($rs->num_rows>0) 
				{
					$design = $rs->fetch_assoc();
					
					return $design;
				} else {
					$msg = 'There are no designs found';
				}
			} else {
				$msg = 'Error executing query'.$qry;
			}
		}
		
		
		public function getDesignsByCID($cID)
		
		{	// reads the designs record to get info about all the designs where userID = $uID
			$qry = "SELECT designID, designTitle, designDescription, designPath, challengeID FROM designs WHERE challengeID = '$cID'";
			$rs = $this->db->query($qry);
			if($rs) {
				if($rs->num_rows>0) 
				{
					$designs = array();
					while($row = $rs->fetch_assoc())
					{
						$designs[] = $row;
					}
					return $designs;
				} else {
					$msg = 'There are no designs found';
				}
			} else {
				$msg = 'Error executing query'.$qry;
			}
		}
		
		
		public function getResources($resources)
		{	//	get resources info from resources table where resourceID = any that are sent from $resources
			$resStr = implode(',', $resources);
			$qry = "SELECT resourceID, resourceTitle, resourceLink FROM resources WHERE resourceID IN (".$resStr.") ";
			$rs = $this->db->query($qry);
			if($rs) {
				if($rs->num_rows>0) 
				{
				
					$resources = array();
					while ($row = $rs->fetch_assoc()) 
					{
						$resources[] = $row;
					}
					return $resources;
				} else {
					$msg = 'There are no resources found';
				}
			} else {
				$msg = 'Error executing query'.$qry;
			}
			
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>
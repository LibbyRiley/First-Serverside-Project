<?php
/*
**	This class contains the method to upload a file
*/
class Upload
{
	private $fileName;			//	name of the file from the upload form
	private $fileTypes=array();	//	array of valid file types for upload
	private $folderPath;		//	folder where uploaded files will be moved
	public $msg;				//	messages generated from this class
	
	public function __construct($fileName, $fileTypes, $folderPath)
	{
		$this->fileName = $fileName;
		$this->fileTypes = $fileTypes;
		$this->folderPath = $folderPath;
	}
	
	public function isUploaded()
	//	this function will contain the statements to process file upload
	//	and returns the name of the file upon successful upload
	//	or returns false otherwise.  It also sets the upload message ($this->msg)
	{
		$this->msg = '';
			//	check if the name of the file uploaded is not available
		if (!$_FILES[$this->fileName]['name'])	{
			$this->msg .= 'File name not available';
			return false;
		}
			//	test for error in uploading
		if ($_FILES[$this->fileName]['error']) {	
			switch($_FILES[$this->fileName]['error']) {
				case 1: $this->msg .= 'File exceeds PHP\'s maximum upload size<br />';
						return false;
				case 2: $this->msg .= 'File exceeds maximum upload file set in the form<br />';
						return false;
				case 3: $this->msg .= 'File partially uploaded<br />';
						return false;
				case 4: $this->msg .= 'No file uploaded<br />';	
						return false;
			}	
		}
			//	check if file type is invalid
		$type = $_FILES[$this->fileName]['type'];	//	get the type of the uploaded file
		if (!in_array($type, $this->fileTypes)) {
			$this->msg .= 'Wrong file type<br />';
			return false;
		}
			//	check if file did not reach the server (temp location)
		if (!is_uploaded_file($_FILES[$this->fileName]['tmp_name'])) {
			$this->msg .= 'File did not reach temporary location on the server<br />';
			return false;
		}
		
		$fleName = $_FILES[$this->fileName]['name'];
		$flePath = $this->folderPath ? $this->folderPath.'/'.$fleName : $fleName;
			//	test if a file with the same name exist, and rename if it does
		if (file_exists($flePath)) {
			$newName = uniqid('LD').$fleName;
			$this->msg .= 'File '.$fleName.' already exists, renamed to '.$newName.'<br />';
			$flePath = $this->folderPath ? $this->folderPath.'/'.$newName : $newName;
		}
			//	move file from temporary location to the path specified	
			//	and test if it's not successful
		if (!move_uploaded_file($_FILES[$this->fileName]['tmp_name'], $flePath)) {	
			$this->msg .= 'Error in moving file to specified location<br />';
			return false;
		}	
			//	check if file does not exist on the destination folder
		if (!file_exists($flePath)) {
			$this->msg .= 'File did not reach destination folder<br />';
			return false;
		}
		
		$this->msg .= 'File '.$_FILES[$this->fileName]['name'].' uploaded successfully<br />';
		return $flePath;
	}
}






?>
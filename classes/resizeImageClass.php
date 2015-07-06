<?php
class ResizeImage
{
	private $imageName;		//	name of the file returned for the upload class
	private $dimension;		//	desired dimension of the longer side
	private $destFolder;	//	path of the folder where resized image will be saved
	private $prefix;		//	prefix that will be attached to the name of the resized image
	public $msg;			//	message that will be set in this class
	
	public function __construct($imageName, $dimension, $destFolder, $prefix='')
	{
		$this->imageName = $imageName;
		$this->dimension = $dimension;
		$this->destFolder = $destFolder;
		$this->prefix = $prefix;
	}
	
	public function resize()
	{	
		$this->msg = '';
				//	check if the file is not available
		if (!file_exists($this->imageName)) {
			$this->msg .= 'Sorry, source file not found';
			return false;
		}
				//  calculate image aspect ratio and get new width and height
		$imageInfo = getimagesize($this->imageName); //	get image info
/*		echo '<pre>';
		print_r($imageInfo);
		echo '</pre>';  */
		$origW = $imageInfo[0];
		$origH = $imageInfo[1];
				//	test which is the longer side, and set it to the desired dimension 
		if ($origH > $origW) {		
			$newH = $this->dimension;
			$newW = ($origW * $newH) / $origH; 
		}
		else {
			$newW = $this->dimension;
			$newH = ($origH * $newW) / $origW;
		}
				//	set full file path of the thumb based on destination folder
		$fleName = basename($this->imageName);
		$dFolder = $this->destFolder ? $this->destFolder.'/' : '';
		$dFile = $this->prefix ? $this->prefix.'_'.$fleName : $fleName;
		$fullPath = $dFolder.''.$dFile;
				//	check file mime type and call corresponding resize function
		if ($imageInfo['mime'] == 'image/jpeg') {
			if ($this->resizeJpeg($newW, $newH, $origW, $origH, $fullPath)) {
				//	return full path to indicate successful resize
				return $fullPath;
			}
			else {
				return false;
			}
		}
		if ($imageInfo['mime'] == 'image/gif') {
			if ($this->resizeGif($newW, $newH, $origW, $origH, $fullPath)) {
				//	return full path to indicate successful resize
				return $fullPath;
			}
			else {
				return false;
			}
		}			
	}
	
	private function resizeJpeg($newW, $newH, $origW, $origH, $fullPath)
	{
		$im = ImageCreateTrueColor($newW, $newH);
		$baseImage = ImageCreateFromJpeg($this->imageName);
		if (imagecopyresampled($im, $baseImage, 0, 0, 0, 0, $newW, $newH, $origW, $origH)) {
			//	resizing is successful, save the resized image to $fullPath
			imageJpeg($im, $fullPath);
			if (file_exists($fullPath)) {
				$this->msg .= 'Thumb file created<br />';
				imagedestroy($im);
				return true;
			}
			else {
				$this->msg .= 'Failure in creating thumb file<br />';
			}
		}	
		else {
			//	resizing fails
			$this->msg .= 'Unable to resize image <br />';
			return false;
		}
	}
	
	private function resizeGif($newW, $newH, $origW, $origH, $fullPath)
	{
		$im = ImageCreateTrueColor($newW, $newH);
		$baseImage = ImageCreateFromGif($this->imageName);
		if (imagecopyresampled($im, $baseImage, 0, 0, 0, 0, $newW, $newH, $origW, $origH)) {
			//	resizing is successful, save the resized image to $fullPath
			imageGif($im, $fullPath);
			if (file_exists($fullPath)) {
				$this->msg .= 'Thumb file created<br />';
				imagedestroy($im);
				return true;
			}
			else {
				$this->msg .= 'Failure in creating thumb file<br />';
			}
		}	
		else {
			//	resizing fails
			$this->msg .= 'Unable to resize image <br />';
			return false;
		}
	}
	
	
	
	
	
	
	
	
} 
?>
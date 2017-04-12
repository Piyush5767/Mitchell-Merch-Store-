<?php
class resolution extends CI_Controller 
{
	
	 function __construct(){
        parent::__construct();
    }

	function index() {
		$imgpath =  base_url().'assets/front.png'; 
		$im = new Imagick();
		$im->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
		$im->setImageResolution(300,300);
		$im->readImage($imgpath);
		$im->setImageFormat("png");
		header("Content-Type: image/png");
		echo $im;	
	}
}
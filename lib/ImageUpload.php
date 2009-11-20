<?php

namespace Empathy;

class ImageUpload
{
  public $error;
  public $target;
  public $target_dir;
  public $file;
  public $deriv;
  public $orig;
  public $origX;
  public $origY;
  public $quality;
  public $gallery;
  
  public function __construct($gallery, $upload, $deriv)
  {
    $this->gallery = $gallery;
    if($this->gallery != '')
      {
	//$this->target_dir = DOC_ROOT."/public_html/img/$this->gallery/";
	$this->target_dir = DOC_ROOT."/public_html/uploads/";
      }
    else
      {
	$this->target_dir = DOC_ROOT."/public_html/uploads/";
      }	    

    if($upload)
      {
	$this->quality = 85;
	$this->error = '';
	if(sizeof($deriv) < 1)
	  {
	    $this->deriv = array(array('l_', 800, 600),
				 array('tn_', 200, 200),
				 array('mid_', 500, 500));		    
	  }
	else
	  {
	    $this->deriv = $deriv;
	  }
	$this->upload();
	if($this->error == '')
	  {
	    $this->create();
	    foreach($this->deriv as $item)
	      { 
		$this->makeDerived($item[0], $item[1], $item[2]);
	      }
	    imageDestroy($this->orig);     	  
	  }    
      }
  }

  public function create()
  {
    $this->orig = imagecreatefromjpeg($this->target);
    $this->origX = imagesx($this->orig);
    $this->origY = imagesy($this->orig);
  }


  public function makeDerived($prefix, $max_width, $max_height)
  {      
    if($max_width < 300 || $max_height < 300)
      {
	$quality = 100;
      }
    else
      {
	$quality = $this->quality;
      }
    if($this->origX > $max_width || $this->origY > $max_height)
      {
	$factorX = $max_width / $this->origX;
	$factorY = $max_height / $this->origY;
	if($factorX < $factorY)
	  {
	    $factor = $factorX;
	  }
	else
	  {
	    $factor = $factorY;
	  }    
      }
    else
      {
	$factor = 1;
      }
    $newX = $this->origX * $factor;
    $newY = $this->origY * $factor;    
    $img = imagecreatetruecolor($newX, $newY);
    imagecopyresampled($img, $this->orig, 0, 0, 0, 0, $newX, $newY, $this->origX, $this->origY);    
    $newTarget = $this->target_dir.$prefix.$this->file;    
    imagejpeg($img, $newTarget, $quality);
    imagedestroy($img);     
  }

  public function remove($files)
  {     
    $success_arr = array();
    $all_files = array();
       
    foreach($files as $file)
      {
	$all_files = array_merge($all_files, glob($this->target_dir.'*'.$file));
      }   
    foreach($all_files as $file)
      {
	array_push($success_arr, @unlink($file));	
      }    
    if(in_array(false, $success_arr))
      {
	$success = false;
      }    
    else
      {
	$success = true;
      }
    return $success;
  }  

  public function upload()
  {
    if($_FILES['file']['name'] == '')
      {
	$this->error .= "Problem uploading file. Empty file?";		       
      }
    else
      {
	$name_array = explode('.', $_FILES['file']['name']);
	$size = sizeof($name_array);
	$ext = $name_array[$size-1];
    
	/* check for jpeg */
	$imgInfo = getImageSize($_FILES['file']['tmp_name']);
	
	if(!preg_match('/(jpg|jpeg)/', $ext) || $imgInfo['mime'] != 'image/jpeg')
	  {
	    $this->error .= "Invalid file format.";
	  }
	else
	  {
	    $name = '';
	    if(sizeof($name_array) > 2)
	      {
		for($i = 0; $i < $size-1; $i++)
		  {
		    $name .= $name_array[$i];
		    if($i+1 != $size-1)
		      {
			$name .= '.';
		      }
		  }
	      }
	    else
	      {
		$name = $name_array[0];
	      }

	    $this->target = $this->target_dir.$name.".".$ext;	    
	    // deal with duplicates
	    $i = 1;
	    while(file_exists($this->target))
	      {
		$this->target = $this->target_dir.$name."_".$i++.".".$ext;
	      }	    
	    $this->file = substr($this->target, strlen($this->target_dir));	    
	    if(!@move_uploaded_file($_FILES['file']['tmp_name'], $this->target))
	      {
		$this->error .= "Internal error";
	      }
	  }    
      }
  }      

 
}
?>
<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

class remositoryThumbnails {
	private $fileid=0;
	private $filetitle='';
	private $maxcount=0;
	private $filepath='';
	private $count=0;
	private $thumb_paths=array();
	private $img_paths=array();
	private $img_texts=array();
	private $thumb_URLs=array();
	private $img_URLs=array();
	private $allfree=array();
	private $freecount = 0;
	private $allow_large=1;

	public function __construct ($file) {
		$interface = remositoryInterface::getInstance();
		srand((double)microtime()*1000000);
		$this->fileid=$file->id;
		$this->filetitle = $file->filetitle;
		$repository = remositoryRepository::getInstance();
		$this->maxcount = $repository->Max_Thumbnails;
		$this->allow_large = $repository->Allow_Large_Images;
		$this->filepath = remositoryThumbnails::baseFilePath().remositoryThumbnails::dirPattern().$file->id.'/';
		if ($this->maxcount) $this->findFiles();
		elseif ($file->screenurl) {
			$this->thumb_URLs[] = $file->screenurl;
			$this->img_URLs[] = $file->screenurl;
			$filepath = str_replace($interface->getCfg('live_site'), $interface->getCfg('absolute_path'), $file->screenurl);
			$this->thumb_paths[] = $filepath;
			$this->img_paths[] = $filepath;
			$this->count = 1;
		}
		else $this->count = 0;
	}

	public static function baseFilePath () {
		return '/components/com_remository_files/';
	}

	public static function dirPattern () {
		return 'file_image_';
	}
	
	public function getFreeCount () {
		return $this->freecount;
	}
	
	public function getMaxCount () {
		return $this->maxcount;
	}
	
	public function getCount () {
		return $this->count;
	}

	public function deleteAllThumbnails () {
		if ($this->count == 0) return;
		$html = "\n\t\t\t<div class='remositorythumbset'>";
		for ($item=0; $item<$this->count; $item++) $this->deleteThumbFile($item);
	}
	
	private function findFiles () {
		$interface = remositoryInterface::getInstance();
		$thumb_pattern = 'th_'.$this->fileid.'_';
		if ($this->fileid) {
			$dir = new remositoryDirectory($interface->getCfg('absolute_path').$this->filepath);
			$thumbfiles = $dir->listFiles($thumb_pattern);
		}
		else $thumbfiles = array();
		$this->count = 0;
		foreach ($thumbfiles as $i=>$thumb) {
			$numberandext = remositoryAbstract::lastPart($thumb,'_');
			$k = intval(remositoryAbstract::allButLast($numberandext,'.'));
			$marker[$k] = 'X';
			$thumbpath = $this->filepath.$thumb;
			$this->thumb_paths[$i] = $interface->getCfg('absolute_path').$thumbpath;
			$this->thumb_URLs[$i] = $interface->getCfg('live_site').$thumbpath;
			$imagefile = str_replace('th_','img_',$thumbpath);
			$textfile = $interface->getCfg('absolute_path').str_replace('th_', 'txt_', $thumbpath);
			if (file_exists($interface->getCfg('absolute_path').$imagefile)) {
				$this->img_paths[$i] = $interface->getCfg('absolute_path').$imagefile;
				$this->img_URLs[$i] = $interface->getCfg('live_site').$imagefile;
			}
			else {
				$this->img_paths[$i] = $this->thumb_paths[$i];
				$this->img_URLs[$i] = $this->thumb_URLs[$i];
			}
			if (file_exists($textfile)) {
				$fp = fopen($textfile, 'rb');
			    if ($fp) {
			        $this->img_texts[$i] = fgets($fp);
			        fclose($fp);
			    }
			}
			$this->count = $i+1;
		}
		$this->freecount = 0;
		$this->allfree = array();
		for ($i=1; $i<=$this->maxcount AND ($this->count + $this->freecount) < $this->maxcount; $i++) {
			if (!isset($marker[$i])) {
				$this->allfree[] = substr($i+100,1);
				$this->freecount++;
			}
		}
	}

	private function getNextFree () {
		return array_shift($this->allfree);
	}

	public function addImage ($physicalFile, $fileid, $legend='', $remositoryFile=false) {
		$interface = remositoryInterface::getInstance();
		$nextfree = $this->getNextFree();
		if ($nextfree) {
			$filepath = $interface->getCfg('absolute_path').$this->filepath;
			$file_to_fix = $filepath.uniqid('', true).$physicalFile->proper_name;
			$file_ext = remositoryAbstract::lastPart($file_to_fix,'.');
			if ($file_ext != 'png' AND $file_ext != 'jpg' AND $file_ext != 'jpeg' AND $file_ext != 'gif') {
				if (!$remositoryFile) echo _DOWN_THUMB_WRONG_TYPE;
			}
			else {
				if (!is_dir($filepath)) mkdir($filepath);
				if ($remositoryFile) $physicalFile->copyToFileSystem($file_to_fix);
				else $physicalFile->fileToFile($file_to_fix, 0, false, false);
				if (file_exists($file_to_fix)) {
					$large_image = $filepath.'img_'.$fileid.'_'.$nextfree.'.'.$file_ext;
					$small_image = $filepath.'th_'.$fileid.'_'.$nextfree.'.'.$file_ext;
					$repository = remositoryRepository::getInstance();
					$this->imgresize($file_to_fix, $large_image, $repository->Large_Image_Width, $repository->Large_Image_Height, true);
					$physicalimage = new remositoryPhysicalFile();
					$physicalimage->setData($large_image);
					$physicalimage->setPerms();
					$this->imgresize($file_to_fix, $small_image, $repository->Small_Image_Width, $repository->Small_Image_Height, true);
					$physicalimage->setData($small_image);
					$physicalimage->setPerms();
					unlink ($file_to_fix);
					if ($legend) {
					    $legend_path = $filepath.'txt_'.$fileid.'_'.$nextfree.'.'.$file_ext;
						$fp = fopen($legend_path, 'wb');
					    if ($fp) {
					        fputs($fp, $legend);
					        fclose($fp);
					    }
					}
				}
				else echo '<h3>'._ERR1.'</h3>';
			}
		}
	}
	
	public function addAutoThumbnail ($file, $upload) {
		if ($this->getFreeCount()) {
			$graphics = array('png','gif','jpg');
			if (in_array($file->filetype, $graphics)) $this->addImage($upload, $file->id, '', true);
			else remositoryInterface::getInstance()->triggerMambots('onRemositoryAutoThumbnail', $file, $upload, $this);
		}
	}

	public function displayThumbnail ($item, $link=true) {
		if ($this->count == 0) return '&nbsp;';
		$html = "\n\t\t\t<div class='remositorythumbnail'>";
		if ($link) {
		    if ($this->allow_large) {
		    	$alt_text = 'View Full Sized Screenshot';
		    	if ($this->maxcount) {
					$imginfo = getimagesize($this->img_paths[$item]);
					$imgw = $imginfo[0]+20;
					$imgh = $imginfo[1]+20;
		    	}
		    	else {
					$repository = remositoryRepository::getInstance();
					$imgw = $repository->Large_Image_Width;
					$imgh = $repository->Large_Image_Height;
		    	}
				$imglink = $this->img_URLs[$item];
				//get the x and y size of the full sized image
				$html .= "\n\t\t\t\t<a href=\"javascript:void(0)\" onclick=\"window.open('$imglink','FullSize','width=$imgw,height=$imgh,toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=0,copyhistory=0')\">";
			}
			else $alt_text = 'Full screenshots disabled';
		}
		else $alt_text = 'Thumbnail image';
		if ($this->maxcount) {
			$thinfo = getimagesize($this->thumb_paths[$item]);
			$thw = $thinfo[0];
			$thh = $thinfo[1];
		}
		else {
			$repository = remositoryRepository::getInstance();
			$thw = $repository->Small_Image_Width;
			$thh = $repository->Small_Image_Height;
		}
		$thlink = $this->thumb_URLs[$item];
		$heightspec = $thh ? "height=\"$thh\"" : '';
		$html .= "\n\t\t\t\t<img width=\"$thw\" $heightspec alt=\"$alt_text\" src='$thlink' />";
		//create a thumbnail image linked to full size image
		if ($link AND $this->allow_large) $html .= '</a>';
		if (isset($this->img_texts[$item])) $html .= "\n\t\t\t\t<div>{$this->img_texts[$item]}</div>";
		$html .= "\n\t\t\t<!-- End of remositorythumbnail -->";
		$html .= "\n\t\t\t</div>";
		return $html;
	}

	public function displayOneThumbnail () {
		if ($this->count == 0) return '';
		if ($this->count > 1) $item = mt_rand(0,$this->count-1);
		else $item = 0;
		$repository = remositoryRepository::getInstance();
		if (isset($this->thumb_URLs[$item])) {
			$thinfo = getimagesize($this->thumb_paths[$item]);
			$thw = $thinfo[0];
			$thh = $thinfo[1];
			$heightspec = $thh ? "height=\"$thh\"" : '';
			$filelink = $repository->RemositoryBasicFunctionURL('fileinfo',$this->fileid);
			$alt = htmlspecialchars($this->filetitle);
			return <<<SINGLE_THUMBNAIL
		
			<div class="remositorythumbnail">
				<a href="$filelink">
					<img width="$thw" $heightspec alt="$alt" src="{$this->thumb_URLs[$item]}" />
				</a>
			</div>
			
SINGLE_THUMBNAIL;

		}
		else {
			return $repository->RemositoryImageURL('blank.gif', $repository->Small_Image_Width, $repository->Small_Image_Height);
		}
	}
	
	public function oneThumbnailLink () {
		if ($this->count == 0) return '';
		if ($this->count > 1) $item = mt_rand(0,$this->count-1);
		else $item = 0;
		return isset($this->thumb_URLs[$item]) ? $this->thumb_URLs[$item] : '';
	}

	public function displayAllThumbnails () {
		if ($this->count == 0) return '';
		$html = "\n\t\t\t<div class='remositorythumbset'>";
		for ($item=0; $item<$this->count; $item++) {
			$html .= '<div>'.$this->displayThumbnail($item).'</div>';
//			if (($item+1) % _THUMBNAILS_PER_COLUMN == 0) echo '</tr><tr><td>&nbsp;</td></tr><tr>';
		}
		$html .= "\n\t\t\t<!-- End of remositorythumbset -->";
		$html .= "\n\t\t\t</div>";
		return $html;
	}

	public function displayAllThumbnailsDeletable () {
		$html = '';
		for ($item=0; $item<$this->count; $item++) {
			$html .= "\n\t\t\t<div class='remositorydelthumb'>";
			$html .= $this->displayThumbnail($item, false);
			$html .= "\n\t\t\t\t<p><input class='button' type='submit' name='delete_thumb_".$item."' value='"._DOWN_DELETE_THUMBNAIL."' /></p>";
			$html .= "\n\t\t\t</div>";
		}
		return $html;
	}

	public function deleteThumbFile ($item) {
		@chmod($this->thumb_paths[$item], 0646);
		unlink($this->thumb_paths[$item]);
		@chmod($this->img_paths[$item], 0646);
		unlink($this->img_paths[$item]);
	}

	public function moveAllThumbnails ($fileid) {
		if ($this->count == 0) return;
		$newpath = remositoryThumbnails::baseFilePath().remositoryThumbnails::dirPattern().$fileid.'/';
		for ($item=0; $item<$this->count; $item++) {
			@rename($this->thumb_paths[$item], $newpath.preg_replace('/^.+[\\\\\\/]/', '', $this->thumb_paths[$item]));
			@rename($this->img_paths[$item], $newpath.preg_replace('/^.+[\\\\\\/]/', '', $this->img_paths[$item]));
		}
	}

		//This function will resize any PNG or JPG image to whatever size you specify
	//It will keep aspect ratios.
	//usage imgresize(/path/to/sourcefile.png,$destfilename,150,150);
	private function imgresize($origfile,$newfile,$new_w,$new_h, $highQuality=false)
	{
		if (!function_exists('imagecreate')) {
			copy ($origfile, $newfile);
			return;
		}
		//determine starting type and create blank
		//you could also add gif and bmp in here
		$type=remositoryAbstract::lastPart($origfile,'.');
		if ($type=="jpg" || $type=="jpeg") $src_img = imagecreatefromjpeg($origfile);
		if ($type=="png") $src_img = imagecreatefrompng($origfile);
        if ($type=="gif") $src_img = imagecreatefromgif($origfile);

		//grab original sizes
		$old_x = imagesx($src_img);
		$old_y = imagesy($src_img);

		// math to figure aspect ratio
		$ratio = min($new_w/$old_x, $new_h/$old_y);
		$thumb_h = $old_y * $ratio;
		$thumb_w = $old_x * $ratio;

		//generate a blank final image
		$dst_img = ImageCreateTrueColor($thumb_w,$thumb_h);
		//this resamples the original image and I think uses bicub to create a new image
		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);

		// $waterfile = '/home/myremos/www/hit/images/Sample-trans.png';
		if (!empty($waterfile)) {
			$watermark = imagecreatefrompng($waterfile);
			$sized_water = ImageCreateTrueColor($thumb_w,$thumb_h);
			// Match desired image size
			imagecopyresampled($sized_water, $watermark, 0, 0, 0, 0, $thumb_w, $thumb_h, imagesx($watermark), imagesy($watermark));
			imagedestroy($watermark);
			// Make the background transparent
			imagecolortransparent($sized_water, imagecolorallocate($sized_water, 0, 0, 0));
			imagecopymerge($dst_img, $sized_water, 0, 0, 0, 0, $thumb_w, $thumb_h, 50);
			imagedestroy($sized_water);
		}

		//unlink it if it already exists in destination
		if(file_exists($newfile)) {
			chmod($newfile, 0646);
			unlink($newfile);
		}

		//create the final file
		if ($type=="png") {
			if ($highQuality) imagepng($dst_img,$newfile,0);
			else imagepng($dst_img,$newfile);
		}
		elseif ($type=="gif") imagegif($dst_img,$newfile);
		else {
			if ($highQuality) imagejpeg($dst_img,$newfile,100);
			else imagejpeg($dst_img,$newfile);
		}

		//free up memory
		imagedestroy($dst_img);
		imagedestroy($src_img);
	}

}
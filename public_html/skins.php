<?php
function flip(&$img)
{
	$size_x = imagesx($img);
	$size_y = imagesy($img);
	$temp = imagecreatetruecolor($size_x, $size_y);
	$x = imagecopyresampled($temp, $img, 0, 0, ($size_x-1), 0, $size_x, $size_y, 0-$size_x, $size_y);
	return $temp;
}

// File and new size
$filename = 'http://minecraft.net/skin/' . $_GET['user'] . '.png';
$usecache=false;
$cachepath = 'skincache/'.md5($_GET['user']); 
if(strlen(@file_get_contents($cachepath)) != 0){
	$usecache=true;
	$filename = $cachepath;
}else if(strlen(@file_get_contents($filename)) == 0){
	$filename = "http://www.craftstats.com/images/defaultskin.png";
}else{
	$img = @file_get_contents($filename);
	@file_put_contents($cachepath,$img);
}

// Content type
header('Content-Type: image/png');

if(isset($_GET['head'])){
$w = $_GET['size'];
$h = $w;
}else{
$w = $_GET['size'];
$h = $w*2;
}


// Load
$rendered = imagecreatetruecolor($w, $h);
$source = imagecreatefrompng($filename);
$b = $w/2;
$s = 8;

// Fill the new image with pink and set pink as the transparent colour
$pink = imagecolorallocate($rendered, 255, 0, 255);
imagefilledrectangle($rendered, 0, 0, $w, $h, $pink);
imagecolortransparent($rendered, $pink);

// Create a flipped version of the image
$fsource = flip($source);

// Annatomy of an imagecopyresampled function
// $dst_image , $src_image , $dst_x , $dst_y , $src_x , $src_y , $dst_w , $dst_h , $src_w , $src_h

// Check if we want to render the back or front
if(isset($_GET['head'])){
$w = $_GET['size'];
$h = $w*2;
$b = $w;
imagecopyresampled($rendered, $source, 0, 0, $s, $s, $b, $b, $s, $s);
imagecopyresampled($rendered, $source, 0, 0, $s*5, $s, $b, $b, $s, $s);
}else if (isset($_GET['back'])){
	// Render the back

	// Copy head
	imagecopyresampled($rendered, $source, $b / 2, 0, $s * 3, $s, $b, $b, $s, $s);

	// Copy the head accesory
	imagecopyresampled($rendered, $source, $b / 2, 0, $s * 7, $s, $b, $b, $s, $s);

	// Copy the body
	imagecopyresampled($rendered, $source, $b / 2, $b, $s * 4, $s * 2.5, $b, $b * 1.5, $s, $s * 1.5);

	// Copy the left arm
	imagecopyresampled($rendered, $source, $b * 1.5, $b, $s * 6.5, $s * 2.5, $b / 2, $b * 1.5, $s / 2, $s * 1.5);

	// Copy the right arm
	imagecopyresampled($rendered, $fsource, 0, $b, $s * 1, $s * 2.5, $b / 2, $b * 1.5, $s / 2, $s * 1.5);

	// Copy the left leg
	imagecopyresampled($rendered, $source, $b / 2, $b * 2.5, $s * 1.5, $s * 2.5, $b / 2, $b * 1.5, $s / 2, $s * 1.5);

	// Copy the right leg
	imagecopyresampled($rendered, $fsource,$b * 1, $b * 2.5, $s * 6, $s * 2.5, $b / 2, $b * 1.5, $s / 2, $s * 1.5);
}
else
{
	// Render the front

	// Copy head
	imagecopyresampled($rendered, $source, $b / 2, 0, $s, $s, $b, $b, $s, $s);

	// Copy the head accesory
	imagecopyresampled($rendered, $source, $b / 2, 0, $s * 5, $s, $b, $b, $s, $s);

	// Copy the body
	imagecopyresampled($rendered, $source, $b / 2, $b, $s * 2.5, $s * 2.5, $b, $b * 1.5, $s, $s * 1.5);

	// Copy the left arm
	imagecopyresampled($rendered, $source, $b * 1.5, $b, $s * 5.5, $s * 2.5, $b / 2, $b * 1.5, $s / 2, $s * 1.5);

	// Copy the right arm
	imagecopyresampled($rendered, $fsource, 0, $b, $s * 2, $s * 2.5, $b / 2, $b * 1.5, $s / 2, $s * 1.5);

	// Copy the left leg
	imagecopyresampled($rendered, $source, $b / 2, $b * 2.5, $s / 2, $s * 2.5, $b / 2, $b * 1.5, $s / 2, $s * 1.5);

	// Copy the right leg
	imagecopyresampled($rendered, $fsource, $b * 1, $b * 2.5, $s * 7, $s * 2.5, $b / 2, $b * 1.5, $s / 2, $s * 1.5);
}

// Output to the browser
imagepng($rendered);

?>
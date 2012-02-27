<?php

/**
 * Image manipulation class, particularly for resizing an image.
 */
class Helper_Image {

	public function getJpg() {
		$imgConfig = Zend_Registry::getInstance()->config->image;
		$generatorPath = $imgConfig->wkhtmltoimage->generatorPath;
		$html = str_replace('%','&#37', $this->html);
		$largeJpg = `printf "$html" | $generatorPath --crop-h 1100 - -`;
		
		
		$gdLargeImg = imagecreatefromstring($largeJpg);
		$originalWidth = 1040;
		$originalHeight = 1100;
		$width = $imgConfig->preview->width;
		$height = $imgConfig->preview->height;
		$gdSmallImg = imagecreatetruecolor($width, $height);
		imagecopyresampled($gdSmallImg, $gdLargeImg, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

		return $gdSmallImg;
	}

	public static function resize($inputFilename, $outputFilename, $maxWidth, $maxHeight, $stretch = false) {
		$originalImage = imagecreatefromstring(file_get_contents($inputFilename));
		$originalWidth = imagesx($originalImage);
		$originalHeight = imagesy($originalImage);

		if (!$stretch && $originalWidth < $maxWidth && $originalHeight < $maxHeight) {
			$outputImage = imagecreatetruecolor($originalWidth, $originalHeight);
			imagecolortransparent($outputImage, imagecolorallocatealpha($outputImage, 0, 0, 0, 127));
			imagealphablending($outputImage, false);
			imagesavealpha($outputImage, true);
			imagecopyresampled($outputImage, $originalImage, 0, 0, 0, 0, $originalWidth, $originalHeight, $originalWidth, $originalHeight);
			imagepng($outputImage, $outputFilename, 0);
		} else {
			$targetRatio = $maxWidth / $maxHeight;
			$originalRatio = $originalWidth / $originalHeight;
			if ($originalRatio > $targetRatio) {
				$width = $maxWidth;
				$height = ($width * $originalHeight) / $originalWidth;
			} else {
				$height = $maxHeight;
				$width = ($height * $originalWidth) / $originalHeight;
			}
			$outputImage = imagecreatetruecolor($width, $height);
			imagecolortransparent($outputImage, imagecolorallocatealpha($outputImage, 0, 0, 0, 127));
			imagealphablending($outputImage, false);
			imagesavealpha($outputImage, true);
			imagecopyresampled($outputImage, $originalImage, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
			imagepng($outputImage, $outputFilename, 0);
		}
	}
	
	public static function remove($fileName) {
		if (file_exists($fileName)) {
			unlink($fileName);
			return true;
		}
		
		return false;
	}
	
	public static function show($fileName) {
		$image = imagecreatefromstring(file_get_contents($fileName));
		header('Content-type: image/jpeg');
		imagejpeg($image, null, 100);
		imagedestroy($image);
	}

	public static function showFromString($str) {
		//header('Content-type: image/jpeg');
		$img = imagecreatefromstring($str);
		imagejpeg($img, null, 100);
		imagedestroy($img);
	}

}

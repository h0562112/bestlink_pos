<?php

namespace Picqer\Barcode;

use Picqer\Barcode\Exceptions\BarcodeException;

class BarcodeGeneratorPNG extends BarcodeGenerator
{

    /**
     * Return a PNG image representation of barcode (requires GD or Imagick library).
     *
     * @param string $code code to print
     * @param string $type type of barcode:
     * @param int $widthFactor Width of a single bar element in pixels.
     * @param int $totalHeight Height of a single bar element in pixels.
     * @param array $color RGB (0-255) foreground color for bar elements (background is transparent).
     * @return string image data or false in case of error.
     * @public
     */
	//2020/3/12 $postype 於圖片上方印串接廠商字串
    public function getBarcode($code, $type, $postype="", $widthFactor = 2, $totalHeight = 30, $color = array(0, 0, 0))
    {
        $barcodeData = $this->getBarcodeData($code, $type);

        // calculate image size
        $width = ($barcodeData['maxWidth'] * $widthFactor);
        $height = $totalHeight;

        if (function_exists('imagecreate')) {
            // GD library
            $imagick = false;
            $png = imagecreate($width, $height+49);
			//2020/3/12 $height+22 >> $height+44
            $colorBackground = imagecolorallocate($png, 255, 255, 255);
            imagecolortransparent($png, $colorBackground);
            $colorForeground = imagecolorallocate($png, $color[0], $color[1], $color[2]);
			//2020/2/5自行修正部分，於圖片下方加入置中文字
			$text			 = $code;
			$font_size       = 20;
			if(file_exists('C://Windows/Fonts/msjhbd.ttc')){//2020/3/13 微軟正黑體
				$font_path       = 'C://Windows/Fonts/msjhbd.ttc';
			}
			else if(file_exists('C://Windows/Fonts/msjhbd.ttf')){//2020/3/13 微軟正黑體
				$font_path       = 'C://Windows/Fonts/msjhbd.ttf';
			}
			else if(file_exists('C://Windows/Fonts/msjhbd.otf')){//2020/3/13 微軟正黑體
				$font_path       = 'C://Windows/Fonts/msjhbd.otf';
			}
			else if(file_exists('C://Windows/Fonts/Arial.ttc')){//2020/3/13
				$font_path       = 'C://Windows/Fonts/Arial.ttc';
			}
			else if(file_exists('C://Windows/Fonts/Arial.otf')){//2020/3/13
				$font_path       = 'C://Windows/Fonts/Arial.otf';
			}
			else{//2020/3/13
				$font_path       = 'C://Windows/Fonts/Arial.ttf';
			}
			$angle           = 0;
			$bbox            = imagettfbbox($font_size, $angle, $font_path, $text );
			$font_width      = $bbox[4] / 2;
			$x  = $width / 2 - $font_width;
			imagettftext($png,$font_size,$angle,$x,$height+49,$colorForeground,$font_path,$text);
			//2020/2/5自行修正部分

			$text			 = $postype;
			//$font_size       = 18;//2020/3/13 同上
			//$font_path       = 'C://Windows/Fonts/Arial.ttf';//2020/3/13 同上
			//$angle           = 0;//2020/3/13 同上
			$bbox            = imagettfbbox($font_size, $angle, $font_path, $text );
			$font_width      = $bbox[4] / 2;
			$x  = $width / 2 - $font_width;
			imagettftext($png,$font_size,$angle,$x,22,$colorForeground,$font_path,$text);
			//2020/3/12於圖片上方印串接廠商字串
        } elseif (extension_loaded('imagick')) {
            $imagick = true;
            $colorForeground = new \imagickpixel('rgb(' . $color[0] . ',' . $color[1] . ',' . $color[2] . ')');
            $png = new \Imagick();
            $png->newImage($width, $height, 'none', 'png');
            $imageMagickObject = new \imagickdraw();
            $imageMagickObject->setFillColor($colorForeground);
        } else {
            throw new BarcodeException('Neither gd-lib or imagick are installed!');
        }

        // print bars
        $positionHorizontal = 0;
        foreach ($barcodeData['bars'] as $bar) {
            $bw = round(($bar['width'] * $widthFactor), 3);
            $bh = round(($bar['height'] * $totalHeight / $barcodeData['maxHeight']), 3);
            if ($bar['drawBar']) {
                $y = round(($bar['positionVertical'] * $totalHeight / $barcodeData['maxHeight']), 3)+27;//2020/3/12 +27 的部分是為了將上方空出空間，印出串接廠商字串
                // draw a vertical bar
                if ($imagick && isset($imageMagickObject)) {
                    $imageMagickObject->rectangle($positionHorizontal, $y, ($positionHorizontal + $bw), ($y + $bh));
                } else {
                    imagefilledrectangle($png, $positionHorizontal, $y, ($positionHorizontal + $bw) - 1, ($y + $bh),
                        $colorForeground);
                }
            }
            $positionHorizontal += $bw;
        }
        ob_start();
        if ($imagick && isset($imageMagickObject)) {
            $png->drawImage($imageMagickObject);
            echo $png;
        } else {
            imagepng($png);
            imagedestroy($png);
        }
        $image = ob_get_clean();

        return $image;
    }
	public function getBarcodeNoText($code, $type, $postype="", $widthFactor = 2, $totalHeight = 30, $color = array(0, 0, 0))
    {
        $barcodeData = $this->getBarcodeData($code, $type);

        // calculate image size
        $width = ($barcodeData['maxWidth'] * $widthFactor);
        $height = $totalHeight;

        if (function_exists('imagecreate')) {
            // GD library
            $imagick = false;
            $png = imagecreate($width, $height+49);
			//2020/3/12 $height+22 >> $height+44
            $colorBackground = imagecolorallocate($png, 255, 255, 255);
            imagecolortransparent($png, $colorBackground);
            $colorForeground = imagecolorallocate($png, $color[0], $color[1], $color[2]);
        } elseif (extension_loaded('imagick')) {
            $imagick = true;
            $colorForeground = new \imagickpixel('rgb(' . $color[0] . ',' . $color[1] . ',' . $color[2] . ')');
            $png = new \Imagick();
            $png->newImage($width, $height, 'none', 'png');
            $imageMagickObject = new \imagickdraw();
            $imageMagickObject->setFillColor($colorForeground);
        } else {
            throw new BarcodeException('Neither gd-lib or imagick are installed!');
        }

        // print bars
        $positionHorizontal = 0;
        foreach ($barcodeData['bars'] as $bar) {
            $bw = round(($bar['width'] * $widthFactor), 3);
            $bh = round(($bar['height'] * $totalHeight / $barcodeData['maxHeight']), 3);
            if ($bar['drawBar']) {
                $y = round(($bar['positionVertical'] * $totalHeight / $barcodeData['maxHeight']), 3)+27;//2020/3/12 +27 的部分是為了將上方空出空間，印出串接廠商字串
                // draw a vertical bar
                if ($imagick && isset($imageMagickObject)) {
                    $imageMagickObject->rectangle($positionHorizontal, $y, ($positionHorizontal + $bw), ($y + $bh));
                } else {
                    imagefilledrectangle($png, $positionHorizontal, $y, ($positionHorizontal + $bw) - 1, ($y + $bh),
                        $colorForeground);
                }
            }
            $positionHorizontal += $bw;
        }
        ob_start();
        if ($imagick && isset($imageMagickObject)) {
            $png->drawImage($imageMagickObject);
            echo $png;
        } else {
            imagepng($png);
            imagedestroy($png);
        }
        $image = ob_get_clean();

        return $image;
    }
}
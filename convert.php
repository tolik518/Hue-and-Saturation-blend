<?php
	
	function validatergb($key){  //doesnt really need to be a own function
		$key = ($key < 0 ? 0 : $key);
		$key = ($key > 255 ? 255 : $key);
		return $key;
	}

	function rgb2hsl($R = 0, $G = 0, $B = 0){  //formulas taken from here: http://www.rapidtables.com/convert/color/rgb-to-hsl.htm
		$R = validatergb($R);
		$G = validatergb($G);
		$B = validatergb($B);

		$R /= 255.0;
		$G /= 255.0;
		$B /= 255.0;
				
		$Cmax = max([$R, $G, $B]);
		$Cmin = min([$R, $G, $B]);
		$Cdelta = $Cmax - $Cmin;
		
		if ($Cdelta == 0){
			$H = 0;
		} else {
			switch ($Cmax){
				case $R:
					$H = fmod((($G-$B)/$Cdelta),6);
					break;
				case $G:
					$H = (($B-$R)/$Cdelta)+2.0;
					break;
				case $B:
					$H = (($R-$G)/$Cdelta)+4.0;
					break;
				default:
					$H = 0;
			}
		}
		$H *= 60;
		$L = ($Cmax + $Cmin) / 2;
		$S = ($Cdelta == 0 ? 0 : ($Cdelta/(1-(abs(2*$L - 1)))))*100;
		
		$L *= 100;
		
		return array($H, $S, $L);
	}
	
	function hsl2rgb($H = 0, $S = 0, $L = 0){  //formulas taken from here: http://www.rapidtables.com/convert/color/hsl-to-rgb.htm
		
		$H = ($H < 0 ? $H += 360 : $H);  //if $H is smaller than 0, then add 360 because it is measured in degree (sice hue is on a circular model)
		
		$H /= 60.0;
		$S /= 100.0;
		$L /= 100.0;
		
		$C = (1-abs(2*$L-1))*$S;
		$X = $C*(1-abs(fmod($H,2)-1));
		$m = $L-($C/2);
		
		$R = 0;
		$G = 0;
		$B = 0;
		
		if (($H >= 0)&&($H < 1)){
			$R = $C; $G = $X;
		} elseif (($H >= 1)&&($H < 2)){
			$R = $X; $G = $C;
		} elseif (($H >= 2)&&($H < 3)){
			$G = $C; $B = $X;
		} elseif (($H >= 3)&&($H < 4)){
			$G = $X; $B = $C;
		} elseif (($H >= 4)&&($H < 5)){
			$R = $X; $B = $C;
		} elseif (($H >= 5)&&($H <= 6)){
			$R = $C; $B = $X;
		}
	
		$R = ($R + $m) * 255;
		$G = ($G + $m) * 255;
		$B = ($B + $m) * 255;

		return array($R, $G, $B);
	}							
	
	function hue($R1, $G1, $B1, $R2, $G2, $B2, $mode = 0){  //Takes either the hue ($mode=0) or hue and saturation ($mode=1) of the given color
		$HSL1 = rgb2hsl($R1, $G1, $B1);
		$HSL2 = rgb2hsl($R2, $G2, $B2);
				
		$H = $HSL2[0];
		$S = ($mode == 1 ? $HSL2[1] : $HSL1[1]);  //picture seem to turn red when r=g=b  &mode=0&r=0&g=0&b=0
		$L = $HSL1[2];
		
		$RGB = hsl2rgb($H, $S, $L);
		return array($RGB[0], $RGB[1], $RGB[2]);
	}
	
	function shifthue($image, $mode, $R, $G, $B){  //hue() function over every pixel of the image; goes for every pixel; 
		$img = imagecreatefrompng($image);
		if (($R != NULL)&&($G != NULL)&&($B != NULL)){
			$msk = file_get_contents($image.".txt");  //$msk is a textfile that say which pixel should stay untouched
			$t = 0;  //index for the mask
			$size = getimagesize($image);
			for ($i = 0; $i < $size[0]; $i++){
				for ($j = 0; $j < $size[1]; $j++){
					if(($msk[$t] == "1")||($msk == NULL)){  //draw a pixel if there is no mask file OR if the char in the mask says "1"
						$rgb = imagecolorsforindex($img, imagecolorat($img, $i, $j)); //colors from the current pixel are red
						$R1 = $rgb['red'];
						$G1 = $rgb['green'];
						$B1 = $rgb['blue'];
						$hue = hue($R1, $G1, $B1, $R, $G, $B, $mode); //new color is calculated
						
						$colorInt  = round($hue[2]);
						$colorInt += round($hue[1])<<8;
						$colorInt += round($hue[0])<<16;  //RGB is saved in a int so we can give it to "imagesetpixel()"
						
						imagesetpixel($img, $i, $j, $colorInt);
					}
					$t++;
				}
			}
		}
		return $img;
	}
	
	function output($image, $mode, $R, $G, $B){
		header('Content-Type: image/png');  //comment this out for debugging purposes  
		$img = shifthue($image, $mode, $R, $G, $B);
		imagepng($img);
		imagedestroy($img);
	}
	
	output("img/".str_replace('/','',$_GET["img"]), $_GET["mode"], $_GET["r"], $_GET["g"], $_GET["b"]);
?>

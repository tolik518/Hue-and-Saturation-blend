<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css"/>
		<script type"text/javascript">
			function initialize(){
				imgElem = document.getElementById("image");
				width = imgElem.naturalWidth;
				height = imgElem.naturalHeight;
			
				imgElem.width = width*2;
				imgElem.height = height*2;
			}
			
			function generateImage(){
				imgElem = document.getElementById("image");
				R = document.getElementById("R").value;
				G = document.getElementById("G").value;
				B = document.getElementById("B").value;
				mode = (document.getElementById("mode").checked ? 1 : 0);
				imageID = document.getElementById("imageID").options[document.getElementById("imageID").selectedIndex].innerHTML;
				img = "convert.php?img=" + imageID + "&mode=" + mode + "&r=" + R + "&g=" + G +"&b=" + B;
					
				imgElem.setAttribute("src", img);
				
				imgElem.onload = function(){
					initialize();
				}
			}
		</script>
	</head>
	<body onload="initialize()">
		<div class="content">
			<div class="input">
				<form>
				<?php
					echo '<select id="imageID" size="1">'.PHP_EOL;
					$files = glob('img/*.{png}', GLOB_BRACE);	//all .png files from the img/ folder are being dropped in a dropdown menu
					$value = 0;
					foreach($files as $file) {	
						 echo '<option value='.$value.'>'.str_replace('img/','',$file).'</option>'.PHP_EOL;
						 $value++;
					}
					echo '</select>'.PHP_EOL;
				?>
					<p>R: <input type="number" min="0" value="150" max="255" id="R"></p>
					<p>G: <input type="number" min="0" value="0" max="255" id="G"></p>
					<p>B: <input type="number" min="0" value="250" max="255" id="B"></p>
					<p><input type="checkbox" id="mode" text="test"> <label for="mode">Alternative mode</label></p>
					<p>Some sprites may using a mask and are only partially colored. (ex. 106, 117, 45, 48 etc.)</p>
					<p><input class="button" type="button" value="Generate" onClick="JavaScript:generateImage()"></p>
				</form>
			</div>
			<div class="output">
				<img id="image" src="convert.php?img=0.png">
			</div>
		</div>
	</body>
</html>

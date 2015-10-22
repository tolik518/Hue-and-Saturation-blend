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
			<form>
			<?php
				echo	'<select id="imageID" size="1">';
				$files = glob('img/*.{png}', GLOB_BRACE);
				$value = 0;
				foreach($files as $file) {	
					 echo '<option value='.$value.'>'.$file.'</option>';
					 $value++;
				}
				echo '</select>';
			?>
				<p>R: <input type="number" min="0" value="150" max="255" id="R"></p>
				<p>G: <input type="number" min="0" value="0" max="255" id="G"></p>
				<p>B: <input type="number" min="0" value="250" max="255" id="B"></p>
				<p><input type="checkbox" id="mode"> Alternative mode?</p>
				<p><input class="button" type="button" value="Generate" onClick="JavaScript:generateImage()"></p>
			</form>
			<img id="image" src="convert.php?img=img/0.png">
		</div>
	</body>
</html>

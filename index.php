<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>LTAT</title>
	<link rel="stylesheet" href="css/style.css">
	<link id="theme-stylesheet" rel="stylesheet" href="css/dark-template.css">
</head>
<body>
	<main class="row">
		<div class="thumbnail-sidebar">
			<ul class="slide-list">
				<?php
					require_once 'libs/slides.php';
					echo renderSlideItems(__DIR__ . '/slides');
				?>
			</ul>
			<div class="sidebar-controls">
				<button class="theme-toggle" type="button" aria-label="Toggle theme" aria-pressed="true" title="Theme">🌚</button>
				<a class="export-button" href="libs/export.php" aria-label="Export slides" title="Export">📦</a>
				<div class="info-control">
					<button class="info-button" type="button" aria-label="Show information" aria-expanded="false" aria-controls="info-popover" title="Info">🙏</button>
					<div class="info-popover" id="info-popover" role="dialog" aria-label="Information" hidden>
						<p><strong>Tool for Awesome Talks</strong> is a project by <a href="http://sarahgarcin.com/" target="_blank">Sarah Garcin</a>, which I have adapted to better suit my needs.</p>
					</div>
				</div>
			</div>
		</div>

		<div class="preview">
			<div class="image-wrapper">
				
			</div>
		</div>
	</main>


	<script src="js/jquery-4.0.0.min.js" type="text/javascript"></script>
	<script src="js/script.js" type="text/javascript"></script>

</body>
</html>
<?php

require_once __DIR__ . '/slides.php';

function failExport($message, $archivePath = null)
{
  if ($archivePath !== null && is_file($archivePath)) {
    unlink($archivePath);
  }
  http_response_code(500);
  header('Content-Type: text/plain; charset=UTF-8');
  echo $message;
  exit;
}

if (!class_exists('ZipArchive')) {
  failExport('Slide export requires the PHP Zip extension.');
}

$archivePath = tempnam(sys_get_temp_dir(), 'tat-export-');
if ($archivePath === false) {
  failExport('Could not create a temporary archive for slide export.');
}

$archive = new ZipArchive();
if ($archive->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
  failExport('Could not open the slide export archive.', $archivePath);
}

$staticPage = '<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Presentation</title>
	<link rel="stylesheet" href="css/style.css">
	<link id="theme-stylesheet" rel="stylesheet" href="css/dark-template.css">
</head>
<body>
	<main class="row">
		<div class="thumbnail-sidebar">
			<ul class="slide-list">' . renderSlideItems(dirname(__DIR__) . '/slides') . '</ul>
			<div class="sidebar-controls">
					<button class="theme-toggle" type="button" aria-label="Toggle theme" aria-pressed="true" title="Theme">🌚</button>
					<div class="info-control">
						<button class="info-button" type="button" aria-label="Show information" aria-expanded="false" aria-controls="info-popover" title="Info">🙏</button>
						<div class="info-popover" id="info-popover" role="dialog" aria-label="Information" hidden>
							<p><strong>Tool for Awesome Talks</strong> is a project by <a href="http://sarahgarcin.com/" target="_blank">Sarah Garcin</a>, which I have adapted to better suit my needs.</p>
						</div>
					</div>
				</div>
			</div>
		<div class="preview"><div class="image-wrapper"></div></div>
	</main>
	<script src="js/jquery-4.0.0.min.js"></script>
	<script src="js/script.js"></script>
</body>
</html>';

if (!$archive->addFromString('index.html', $staticPage)) {
  failExport('Could not add the static presentation page to the archive.', $archivePath);
}

$assets = [
  'css/style.css',
  'css/light-template.css',
  'css/dark-template.css',
  'js/jquery-4.0.0.min.js',
  'js/script.js',
];

foreach ($assets as $asset) {
  $sourcePath = dirname(__DIR__) . '/' . $asset;
  if (!is_file($sourcePath) || !$archive->addFile($sourcePath, $asset)) {
    failExport("Could not add required export asset: {$asset}", $archivePath);
  }
}

$slidesPath = dirname(__DIR__) . '/slides';
if (!is_dir($slidesPath) || !is_readable($slidesPath)) {
  failExport('The slides directory is missing or unreadable.', $archivePath);
}

if (!$archive->addEmptyDir('slides')) {
  failExport('Could not add the slides folder to the export archive.', $archivePath);
}
$iterator = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator($slidesPath, FilesystemIterator::SKIP_DOTS),
  RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($iterator as $file) {
  if (!$file->isFile() || $file->isLink() || substr($file->getFilename(), 0, 1) === '.') {
    continue;
  }

  $relativePath = substr($file->getPathname(), strlen($slidesPath) + 1);
  if (!$archive->addFile($file->getPathname(), 'slides/' . $relativePath)) {
    failExport("Could not add slide asset to export: {$relativePath}", $archivePath);
  }
}

if (!$archive->close()) {
  failExport('Could not finish creating the slide export archive.', $archivePath);
}

register_shutdown_function(static function () use ($archivePath) {
  if (is_file($archivePath)) {
    unlink($archivePath);
  }
});

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="presentation.zip"');
header('Content-Length: ' . filesize($archivePath));
if (readfile($archivePath) === false) {
  error_log('Failed to stream the generated presentation ZIP.');
}

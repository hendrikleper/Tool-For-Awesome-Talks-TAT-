<?php

require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/Parsedown.php';

function renderSlideItems($slidesDirectory, $assetPath = 'slides')
{
  $slides = getFileList($slidesDirectory);
  array_multisort(array_column($slides, 'name'), SORT_ASC, $slides);
  $markdown = new Parsedown();
  $items = [];
  $directoryPrefix = rtrim($slidesDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

  foreach ($slides as $slide) {
    if (!is_file($slide['name'])) {
      continue;
    }

    $fileName = basename($slide['name']);
    $filePath = $slide['name'];
    $assetFilePath = rtrim($assetPath, '/') . '/' . substr($filePath, strlen($directoryPrefix));
    $safeFileName = htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8');
    $safeFilePath = htmlspecialchars($assetFilePath, ENT_QUOTES, 'UTF-8');
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($extension === 'md') {
      if (preg_match('/\.(jpg|png|gif)\.md$/i', $fileName)) {
        continue;
      }

      $content = file_get_contents($filePath);
      if ($content === false) {
        throw new RuntimeException("Could not read slide file: {$filePath}");
      }
      $items[] = "<li class='text-slide'>{$markdown->text($content)}</li>";
    } elseif (in_array($extension, ['mp4', 'webm', 'ogg'], true)) {
      $items[] = "<li class='video' data-video='{$safeFilePath}'><h1>{$safeFileName}</h1></li>";
    } elseif (in_array($extension, ['mp3', 'wav'], true)) {
      $items[] = "<li class='audio' data-audio='{$safeFilePath}'><h1>{$safeFileName}</h1></li>";
    } elseif ($extension === 'pdf') {
      $items[] = "<li class='pdf' data-pdf='{$safeFilePath}'><h1>{$safeFileName}</h1></li>";
    } elseif (in_array($extension, ['jpg', 'png', 'gif'], true)) {
      $captionPath = $filePath . '.md';
      $item = "<li class='image'><figure data-thumb='{$safeFilePath}' data-image='{$safeFilePath}'";

      if (is_file($captionPath)) {
        $caption = file_get_contents($captionPath);
        if ($caption === false) {
          throw new RuntimeException("Could not read image caption: {$captionPath}");
        }
        $item .= " data-caption='true'><img src='{$safeFilePath}'/>";
        $item .= "<figcaption>{$markdown->text($caption)}</figcaption>";
      } else {
        $item .= "><img src='{$safeFilePath}'/>";
      }

      $items[] = $item . '</figure></li>';
    }
  }

  return implode("\n", $items);
}

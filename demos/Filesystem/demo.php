<?php

require_once '../bootstrap.php';

$path = '../../src';

#$whitelist = array('php'); // List of file extensions to filter

#$iterator = new \Kaloa\Filesystem\ExtensionFilter(
#                new RecursiveIteratorIterator(
#                        new RecursiveDirectoryIterator($path)),
#                $whitelist);

$whitelist = array('text/x-php'); // List of file extensions to filter

$iterator = new \Kaloa\Filesystem\MimeTypeFilter(
                new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($path)),
                $whitelist);

$files = iterator_to_array($iterator);

usort($files, function ($a, $b) {
    return strcmp($a->getPathname(), $b->getPathname());
});

$i = 1;

$linesTotal = 0;

foreach ($files as $file) {
    /* @var $file SplFileInfo */

    $content = file_get_contents($file->getPathname());

    $linesCount = substr_count($content, "\n");

    $linesTotal += $linesCount;

    echo '<h2>' . $i . '. ' . $file->getPathname() . ' (' . $linesCount . ')</h2>';
    echo '<pre><code>' . highlight_string($content, true) . '</code></pre>';

    $i++;
}

echo '<p>Lines total: ' . $linesTotal . '</p>';

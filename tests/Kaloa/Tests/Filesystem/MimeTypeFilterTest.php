<?php

/*
 * This file is part of the kaloa/filesystem package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests\Filesystem;

use Kaloa\Filesystem\MimeTypeFilter;
use PHPUnit_Framework_TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 *
 */
class MimeTypeFilterTest extends PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $path = __DIR__ . '/test-files';

        $whitelist = array(
            'text/php',
            'text/x-php',        // Ubuntu 14.04 (?)
            'application/php',
            'application/x-php'
        );

        $iterator = new MimeTypeFilter(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path)
            ),
            $whitelist
        );

        $files = iterator_to_array($iterator);

        $this->assertEquals(1, count($files));

        $whitelist = array(
            'image/png'
        );

        $iterator = new MimeTypeFilter(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path)
            ),
            $whitelist
        );

        $files = iterator_to_array($iterator);

        $this->assertEquals(0, count($files));
    }
}

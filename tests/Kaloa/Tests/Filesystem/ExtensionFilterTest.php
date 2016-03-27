<?php

/*
 * This file is part of the kaloa/filesystem package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests\Filesystem;

use Kaloa\Filesystem\ExtensionFilter;
use PHPUnit_Framework_TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 *
 */
class ExtensionFilterTest extends PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $path = __DIR__ . '/test-files';

        $whitelist = array('php');

        $iterator = new ExtensionFilter(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path)
            ),
            $whitelist
        );

        $files = iterator_to_array($iterator);

        $this->assertEquals(1, count($files));

        $whitelist = array('png');

        $iterator = new ExtensionFilter(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path)
            ),
            $whitelist
        );

        $files = iterator_to_array($iterator);

        $this->assertEquals(0, count($files));
    }
}

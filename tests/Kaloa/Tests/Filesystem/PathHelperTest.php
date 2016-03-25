<?php

/*
 * This file is part of the kaloa/filesystem package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests;

use Kaloa\Filesystem\PathHelper;
use PHPUnit_Framework_TestCase;

/**
 *
 */
class PathHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testNormalize()
    {
        $ph = new PathHelper();

        $f = function ($s) use ($ph) {
            return $ph->normalize($s);
        };

        $this->assertEquals('.',     $f(''));
        $this->assertEquals('.',     $f('.'));
        $this->assertEquals('..',     $f('../'));
        $this->assertEquals('.',     $f('./'));
        $this->assertEquals('/',     $f('/'));
        $this->assertEquals('/',     $f('///'));
        $this->assertEquals('/',     $f('//././/'));
        $this->assertEquals('dir',  $f('dir'));
        $this->assertEquals('/dir', $f('/dir'));
        $this->assertEquals('/dir', $f('//dir'));
        $this->assertEquals('dir',  $f('dir/'));
        $this->assertEquals('dir',  $f('dir/.'));
        $this->assertEquals('dir',  $f('dir/.///'));
        $this->assertEquals('.',  $f('./dir1/..'));
        $this->assertEquals('.',  $f('././//./dir1/../dir2/..//'));
        $this->assertEquals('dir',  $f('dir/./'));
        $this->assertEquals('dir',  $f('dir/.//'));
        $this->assertEquals('dir',  $f('./dir'));
        $this->assertEquals('../dir', $f('../dir'));
        $this->assertEquals('../dir', $f('./../dir'));

        // ../ tests
        $this->assertEquals('/dir1', $f('/dir/../dir1'));
        $this->assertEquals('..',    $f('./dir/../dir1/../..///.//'));
        $this->assertEquals('/',     $f('/dir1/dir2/dir3/../../..'));
        $this->assertEquals('/dir1', $f('/dir1/dir2/dir3/dir4/../../../'));
        $this->assertEquals('dir1',  $f('./dir1/dir2/dir3/dir4/../../../'));
        $this->assertEquals('dir1',  $f('./dir1/.//dir2/../dir3//dir4/.././/..'));
        $this->assertEquals('/',     $f('/../../'));
        $this->assertEquals('/dir',  $f('/../../dir/'));
        $this->assertEquals('../..', $f('../../'));

        $this->assertEquals('/dir1/dir2', $f('///dir1/../dir1/dir2/'));
        $this->assertEquals('dir1',       $f('./dir1/.//dir2/../dir3//dir4/.././/..'));
        $this->assertEquals('..',         $f('dir1/../../'));
        $this->assertEquals('/',          $f('  /./dir1/./dir2///../../../// '));

        // http://www.php.net/manual/en/function.realpath.php#84012
        $this->assertEquals('this/a/test/is', $f('this/is/../a/./test/.///is'));

        // Test for handling of "\" path separators
        $this->assertEquals('/',          $f('  \\.\\dir1\\.\\dir2\\\\\\..\\..\\..\\\\\\ '));
    }

    /**
     *
     */
    public function testNormalizeAdditional()
    {
        $ph = new PathHelper();

        $f = function ($s) use ($ph) {
            return $ph->normalize($s);
        };

        $this->assertEquals('dir1', $f($f('./dir1/.//') . '/' . $f('dir2/../dir3//dir4/.././/..')));
    }

    /**
     *
     */
    public function testNormalizeThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $ph = new PathHelper();
        $ph->normalize(42);
    }

    /**
     *
     */
    public function testNormalizeDirectorySeparatorsThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $ph = new PathHelper();
        $ph->normalizeDirectorySeparators(42);
    }
}

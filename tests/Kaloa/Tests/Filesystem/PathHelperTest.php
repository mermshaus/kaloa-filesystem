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
    public function testNormalizePaths()
    {
        $ph = new PathHelper();

        $f = function ($s) use ($ph) {
            return $ph->normalize($s);
        };

        $this->assertSame('.',      $f(''));
        $this->assertSame('.',      $f('.'));
        $this->assertSame('..',     $f('../'));
        $this->assertSame('.',      $f('./'));
        $this->assertSame('/',      $f('/'));
        $this->assertSame('/',      $f('///'));
        $this->assertSame('/',      $f('//././/'));
        $this->assertSame('dir',    $f('dir'));
        $this->assertSame('/dir',   $f('/dir'));
        $this->assertSame('/dir',   $f('//dir'));
        $this->assertSame('dir',    $f('dir/'));
        $this->assertSame('dir',    $f('dir/.'));
        $this->assertSame('dir',    $f('dir/.///'));
        $this->assertSame('.',      $f('./dir1/..'));
        $this->assertSame('.',      $f('././//./dir1/../dir2/..//'));
        $this->assertSame('dir',    $f('dir/./'));
        $this->assertSame('dir',    $f('dir/.//'));
        $this->assertSame('dir',    $f('./dir'));
        $this->assertSame('../dir', $f('../dir'));
        $this->assertSame('../dir', $f('./../dir'));

        // ../ tests
        $this->assertSame('/dir1', $f('/dir/../dir1'));
        $this->assertSame('..',    $f('./dir/../dir1/../..///.//'));
        $this->assertSame('/',     $f('/dir1/dir2/dir3/../../..'));
        $this->assertSame('/dir1', $f('/dir1/dir2/dir3/dir4/../../../'));
        $this->assertSame('dir1',  $f('./dir1/dir2/dir3/dir4/../../../'));
        $this->assertSame('dir1',  $f('./dir1/.//dir2/../dir3//dir4/.././/..'));
        $this->assertSame('/',     $f('/../../'));
        $this->assertSame('/dir',  $f('/../../dir/'));
        $this->assertSame('../..', $f('../../'));

        $this->assertSame('/dir1/dir2', $f('///dir1/../dir1/dir2/'));
        $this->assertSame('dir1',       $f('./dir1/.//dir2/../dir3//dir4/.././/..'));
        $this->assertSame('..',         $f('dir1/../../'));
        $this->assertSame('../..',      $f('./dir1/../../../'));
        $this->assertSame('/',          $f('  /./dir1/./dir2///../../../// '));

        // http://www.php.net/manual/en/function.realpath.php#84012
        $this->assertSame('this/a/test/is', $f('this/is/../a/./test/.///is'));

        // Test for handling of "\" path separators
        $this->assertSame('/', $f('  \\.\\dir1\\.\\dir2\\\\\\..\\..\\..\\\\\\ '));

        $this->assertSame('/', $f($f($f($f('  \\.\\dir1\\.\\dir2\\\\\\..\\..\\..\\\\\\ ')))));
    }

    /**
     *
     */
    public function testNormalizeWindowsSystem()
    {
        $ph = new PathHelper(PathHelper::SYSTEM_WINDOWS);

        $f = function ($s) use ($ph) {
            return $ph->normalize($s);
        };

        $this->assertSame('.',      $f(''));
        $this->assertSame('.',      $f('.'));
        $this->assertSame('..',     $f('../'));
        $this->assertSame('.',      $f('./'));
        $this->assertSame('c:/',    $f('/'));
        $this->assertSame('c:/',    $f('///'));
        $this->assertSame('c:/',    $f('//././/'));
        $this->assertSame('dir',    $f('dir'));
        $this->assertSame('c:/dir', $f('/dir'));
        $this->assertSame('c:/dir', $f('//dir'));
        $this->assertSame('dir',    $f('dir/'));
        $this->assertSame('dir',    $f('dir/.'));
        $this->assertSame('dir',    $f('dir/.///'));
        $this->assertSame('.',      $f('./dir1/..'));
        $this->assertSame('.',      $f('././//./dir1/../dir2/..//'));
        $this->assertSame('dir',    $f('dir/./'));
        $this->assertSame('dir',    $f('dir/.//'));
        $this->assertSame('dir',    $f('./dir'));
        $this->assertSame('../dir', $f('../dir'));
        $this->assertSame('../dir', $f('./../dir'));

        // ../ tests
        $this->assertSame('d:/dir1', $f('d:/dir/../dir1'));
        $this->assertSame('..',      $f('./dir/../dir1/../..///.//'));
        $this->assertSame('z:/',     $f('z:/dir1/dir2/dir3/../../..'));
        $this->assertSame('c:/dir1', $f('/dir1/dir2/dir3/dir4/../../../'));
        $this->assertSame('dir1',    $f('./dir1/dir2/dir3/dir4/../../../'));
        $this->assertSame('dir1',    $f('./dir1/.//dir2/../dir3//dir4/.././/..'));
        $this->assertSame('c:/',     $f('c:/../../'));
        $this->assertSame('k:/dir',  $f('k:/../../dir/'));
        $this->assertSame('../..',   $f('../../'));

        $this->assertSame('c:/',    $f('C:\\foo\\bar\\..\\..\\'));
        $this->assertSame('c:/foo', $f(' C:\\foo\\bar\\.. '));

        $this->assertSame('c:/foo', $f($f($f(' C:\\foo\\bar\\.. '))));
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

        $this->assertSame('dir1', $f($f('./dir1/.//') . '/' . $f('dir2/../dir3//dir4/.././/..')));
    }

    /**
     *
     */
    public function testNormalizeAdditionalWindowsSystem()
    {
        $ph = new PathHelper(PathHelper::SYSTEM_WINDOWS);

        $f = function ($s) use ($ph) {
            return $ph->normalize($s);
        };

        $this->assertSame('dir1', $f($f('./dir1/.//') . '/' . $f('dir2/..\\dir3//dir4\\.././/..')));
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
    public function testConstructorThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            '$system must be on of the Kaloa\Filesystem\PathHelper::SYSTEM_* constants'
        );

        new PathHelper('invalid');
    }
}

<?php

/*
 * This file is part of the kaloa/filesystem package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Filesystem;

use InvalidArgumentException;

/**
 *
 */
final class PathHelper
{
    /**
     *
     */
    const SYSTEM_AUTODETECT = 0x0;

    /**
     *
     */
    const SYSTEM_UNIX = 0x1;

    /**
     *
     */
    const SYSTEM_WINDOWS = 0x2;

    /**
     * @var bool
     */
    private $isWindowsSystem;

    /**
     * @param int $system
     */
    public function __construct($system = self::SYSTEM_AUTODETECT)
    {
        if (!in_array(
            $system,
            array(
                self::SYSTEM_AUTODETECT,
                self::SYSTEM_UNIX,
                self::SYSTEM_WINDOWS
            ),
            true
        )) {
            throw new InvalidArgumentException(sprintf(
                '$system must be on of the %s::SYSTEM_* constants',
                get_class($this)
            ));
        }

        $tmp = false;

        if (self::SYSTEM_AUTODETECT === $system) {
            $tmp = ('win' === strtolower(substr(PHP_OS, 0, 3)));
        } elseif (self::SYSTEM_WINDOWS === $system) {
            $tmp = true;
        }

        $this->isWindowsSystem = $tmp;
    }

    /**
     *
     * @param string $path
     * @return string
     * @throws InvalidArgumentException
     */
    public function normalize($path)
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException('$path must be of type string');
        }

        $path = trim($path);

        $path = str_replace('\\', '/', $path);

        $isAbsolutePath = false;
        $windowsDriveLetter = '';

        if ($this->isWindowsSystem) {
            $matches = array();
            if (1 === preg_match('~\A([A-Za-z]):(\z|/.*)~', $path, $matches)) {
                $isAbsolutePath = true;
                $windowsDriveLetter = strtolower($matches[1]);
                $path = $matches[2];
            } elseif (substr($path, 0, 1) === '/') {
                $isAbsolutePath = true;
                $windowsDriveLetter = 'c';
            }
        } else {
            $isAbsolutePath = (substr($path, 0, 1) === '/');
        }

        $components = explode('/', $path);

        $newComponents = array();

        foreach ($components as $component) {
            switch ($component) {
                case '':
                case '.':
                    // Discard
                    break;
                case '..':
                    $c = count($newComponents);
                    if (
                        ($c === 0 && !$isAbsolutePath)
                        || ($c > 0 && $newComponents[$c - 1] === '..')
                    ) {
                        // Relative paths may start with ".." components
                        $newComponents[] = $component;
                    } else {
                        array_pop($newComponents);
                    }
                    break;
                default:
                    $newComponents[] = $component;
                    break;
            }
        }

        $newPath = ($isAbsolutePath ? '/' : '') . implode('/', $newComponents);

        if ($isAbsolutePath && $this->isWindowsSystem) {
            $newPath = $windowsDriveLetter . ':' . $newPath;
        }

        if ($newPath === '') {
            $newPath = '.';
        }

        return $newPath;
    }
}

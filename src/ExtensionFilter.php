<?php

/*
 * This file is part of the kaloa/filesystem package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Filesystem;

use FilterIterator;
use Iterator;
use SplFileInfo;

/**
 * Example:
 *
 * <pre>
 * $path      = '/a/directory';
 * $whitelist = array('txt'); // List of file extensions to filter
 *
 * $iterator = new FileExtensionFilterIterator(
 *                 new RecursiveIteratorIterator(
 *                     new RecursiveDirectoryIterator($path)),
 *                 $whitelist);
 *
 * foreach ($iterator as $file) {
 *     echo $file, "<br />";
 * }
 * </pre>
 *
 * @author Marc Ermshaus <marc@ermshaus.org>
 */
final class ExtensionFilter extends FilterIterator
{
    /**
     * List of allowed file extensions
     *
     * @var array
     */
    private $whitelist;

    /**
     *
     * @param Iterator $iterator
     * @param array $whitelist
     */
    public function __construct(Iterator $iterator, array $whitelist)
    {
        parent::__construct($iterator);
        $this->whitelist = $whitelist;
    }

    /**
     *
     * @return boolean
     */
    public function accept()
    {
        /* @var $fileInfo SplFileInfo */
        $fileInfo = parent::current();

        // Allow only files
        if (!$fileInfo->isFile()) {
            return false;
        }

        // Only allow file extensions from $whitelist
        $pi = pathinfo($fileInfo->getFilename());
        if (!in_array(strtolower($pi['extension']), $this->whitelist)) {
            return false;
        }

        return true;
    }
}

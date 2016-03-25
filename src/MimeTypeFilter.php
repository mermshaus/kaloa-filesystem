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

final class MimeTypeFilter extends FilterIterator
{
    /**
     * List of allowed file extensions
     *
     * @var array
     */
    protected $whitelist;

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

        $finfo = finfo_open(\FILEINFO_MIME_TYPE);
        $type = finfo_file($finfo, $fileInfo->getPathname());
        finfo_close($finfo);

        // Only allow MIME types from $whitelist
        if (!in_array($type, $this->whitelist)) {
            return false;
        }

        return true;
    }
}

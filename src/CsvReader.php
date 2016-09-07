<?php

/*
 * This file is part of the kaloa/filesystem package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Filesystem;

use Exception;

/**
 *
 */
final class CsvReader
{
    /**
     * @var resource
     */
    private $stream;

    /**
     * @var string
     */
    private $delimiter;

    /**
     * @var string
     */
    private $enclosure;

    /**
     * @var string
     */
    private $escape;

    /**
     * @var string
     */
    private $encoding;

    /**
     * @var int
     */
    private $fetchRowLineCounter = 0;

    /**
     * @var array
     */
    private $fetchRowKeys = array();

    /**
     * @param resource $stream
     * @param string $encoding
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @throws Exception
     */
    public function __construct($stream, $encoding = 'UTF-8', $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        $this->stream = $stream;

        $this->assertStream();

        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape    = $escape;
        $this->encoding  = $encoding;
    }

    /**
     * @return array
     */
    public function fetchAll()
    {
        $data = array();

        while ($row = $this->getNextRow()) {
            $data[] = $this->negotiateEncoding($row);
        }

        return $data;
    }

    /**
     * @param array $keys If not empty, use as array keys. Otherwise read keys
     *                    from first line of input data
     * @return array
     * @throws Exception
     */
    public function fetchAllAssoc(array $keys = array())
    {
        $headerFlag = true;
        $keysCount = count($keys);

        if ($keysCount > 0) {
            $headerFlag = false;
        }

        $data = array();

        $rowCounter = 0;

        while ($row = $this->getNextRow()) {
            $rowCounter++;

            if ($headerFlag) {
                $headerFlag = false;
                $keys = $this->negotiateEncoding($row);
                $keysCount = count($keys);
                continue;
            }

            if (count($row) !== $keysCount) {
                throw new Exception(sprintf(
                    'Malformed row (%s) in CSV input',
                    $rowCounter
                ));
            }

            $data[] = array_combine($keys, $this->negotiateEncoding($row));
        }

        return $data;
    }

    /**
     * @return array|bool
     */
    public function fetch()
    {
        $row = $this->getNextRow();

        if (is_array($row)) {
            $row = $this->negotiateEncoding($row);
        }

        return $row;
    }

    /**
     * @param array $keys
     * @return array|bool
     * @throws Exception
     */
    public function fetchAssoc(array $keys = array())
    {
        $headerFlag = empty($keys);

        $row = $this->getNextRow();

        if (!is_array($row)) {
            // EOF
            return false;
        }

        if (0 === $this->fetchRowLineCounter) {
            if ($headerFlag) {
                $this->fetchRowKeys = $this->negotiateEncoding($row);
                $this->fetchRowLineCounter++;
                return $this->{__FUNCTION__}($keys);
            } else {
                $this->fetchRowKeys = $keys;
            }
        }

        $this->fetchRowLineCounter++;

        if (count($row) !== count($this->fetchRowKeys)) {
            throw new Exception(sprintf(
                'Malformed row (%s) in CSV input',
                $this->fetchRowLineCounter
            ));
        }

        return array_combine($this->fetchRowKeys, $this->negotiateEncoding($row));
    }

    /**
     * @return array|bool
     */
    private function getNextRow()
    {
        $this->assertStream();

        $tmp = fgetcsv($this->stream, 0, $this->delimiter, $this->enclosure, $this->escape);

        return $tmp;
    }

    /**
     * @throws Exception
     */
    private function assertStream()
    {
        set_error_handler(function () {}, E_WARNING);
        $tmp = get_resource_type($this->stream);
        restore_error_handler();

        if (!is_string($tmp) || 'stream' !== $tmp) {
            throw new Exception('Supplied input is not a valid stream resource');
        }
    }

    /**
     * @param array $elements
     * @return array
     */
    private function negotiateEncoding(array $elements)
    {
        if ('UTF-8' !== $this->encoding) {
            foreach ($elements as &$element) {
                $element = mb_convert_encoding($element, 'UTF-8', $this->encoding);
            }
            unset($element);
        }

        return $elements;
    }
}

<?php

/*
 * This file is part of the kaloa/filesystem package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests\Filesystem;

use Kaloa\Filesystem\CsvReader;
use PHPUnit_Framework_TestCase;

/**
 *
 */
class CsvReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testCanRead()
    {
        $stream = fopen(__DIR__ . '/csv-files/simple.csv', 'rb');
        $reader = new CsvReader($stream);
        $data = $reader->fetchAllAssoc();
        fclose($stream);

        $expected = array();
        $expected[] = array('Col a' => 'value 1a', 'Col b' => 'value 1b');
        $expected[] = array('Col a' => 'value 2a', 'Col b' => 'value 2b');

        $this->assertSame($expected, $data);
    }

    /**
     *
     */
    public function testCanReadWithCustomDelimiters()
    {
        $stream = fopen(__DIR__ . '/csv-files/custom-delimiters.csv', 'rb');
        $reader = new CsvReader($stream, 'UTF-8', ':', '|');
        $data = $reader->fetchAllAssoc();
        fclose($stream);

        $expected = array();
        $expected[] = array('Col a' => 'value 1a', 'Col b' => 'value 1b');
        $expected[] = array('Col a' => 'value 2a', 'Col b' => 'value 2b');

        $this->assertSame($expected, $data);
    }

    /**
     *
     */
    public function testCanReadWithOnlyHeaders()
    {
        $csvData = <<<'CSV'
foo,bar,"foo bar"
CSV;

        $dataUri = $this->convertToDataUri($csvData);

        $stream = fopen($dataUri, 'rb');

        $reader = new CsvReader($stream);

        $data = $reader->fetchAllAssoc();

        fclose($stream);

        $expected = array();

        $this->assertSame($expected, $data);

    }

    /**
     *
     */
    public function testCanReadWithoutHeaders()
    {
        $stream = fopen(__DIR__ . '/csv-files/no-headers.csv', 'rb');
        $reader = new CsvReader($stream);
        $data = $reader->fetchAll();
        fclose($stream);

        $expected = array();
        $expected[] = array('value 1a', 'value 1b');
        $expected[] = array('value 2a', 'value 2b');

        $this->assertSame($expected, $data);
    }

    /**
     *
     */
    public function testCanReadInStreamingMode()
    {
        $stream = fopen(__DIR__ . '/csv-files/custom-delimiters.csv', 'rb');
        $reader = new CsvReader($stream, 'UTF-8', ':', '|');
        $data = array();

        while ($row = $reader->fetchAssoc()) {
            $data[] = $row;
        }

        fclose($stream);

        $expected = array();
        $expected[] = array('Col a' => 'value 1a', 'Col b' => 'value 1b');
        $expected[] = array('Col a' => 'value 2a', 'Col b' => 'value 2b');

        $this->assertSame($expected, $data);

        // Test non-associative fetch method

        $stream = fopen(__DIR__ . '/csv-files/custom-delimiters.csv', 'rb');
        $reader = new CsvReader($stream, 'UTF-8', ':', '|');
        $data = array();

        while ($row = $reader->fetch()) {
            $data[] = $row;
        }

        fclose($stream);

        $expected = array();
        $expected[] = array('Col a', 'Col b');
        $expected[] = array('value 1a', 'value 1b');
        $expected[] = array('value 2a', 'value 2b');

        $this->assertSame($expected, $data);
    }

    /**
     *
     */
    public function testCanReadInStreamingModeWithCustomKeys()
    {
        $stream = fopen(__DIR__ . '/csv-files/no-headers.csv', 'rb');
        $reader = new CsvReader($stream);

        $data = array();

        while ($row = $reader->fetchAssoc(array('Col a', 'Col b'))) {
            $data[] = $row;
        }

        fclose($stream);

        $expected = array();
        $expected[] = array('Col a' => 'value 1a', 'Col b' => 'value 1b');
        $expected[] = array('Col a' => 'value 2a', 'Col b' => 'value 2b');

        $this->assertSame($expected, $data);
    }

    /**
     *
     */
    public function testCanReadDataStreamInStreamingMode()
    {
        $csvData = <<<'CSV'
|Col a|:|Col b|
|value 1a|:|value 1b|
|value 2a|:|value 2b|
CSV;

        $dataUri = $this->convertToDataUri($csvData);

        $stream = fopen($dataUri, 'rb');

        $reader = new CsvReader($stream, 'UTF-8', ':', '|');

        $data = array();

        while ($row = $reader->fetchAssoc()) {
            $data[] = $row;
        }

        fclose($stream);

        $expected = array();
        $expected[] = array('Col a' => 'value 1a', 'Col b' => 'value 1b');
        $expected[] = array('Col a' => 'value 2a', 'Col b' => 'value 2b');

        $this->assertSame($expected, $data);
    }

    /**
     *
     */
    public function testThrowsExceptionOnInvalidStream()
    {
        $this->setExpectedException('Exception');

        new CsvReader('invalid');
    }

    /**
     *
     */
    public function testCanConvertCharset()
    {
        $csvData = <<<CSV
"Thomas Müller","FC Bayern München",Sturm
"Julian Weigl","Borussia Dortmund",Mittelfeld
"Serge Gnabry","Werder Bremen",Sturm
"Gianluigi Buffon",Juventus,Tor
"Shkodran Mustafi",Arsenal,Abwehr
"Mario Gómez","VfL Wolfsburg",Sturm
CSV;

        $csvData = mb_convert_encoding($csvData, 'ISO-8859-1', 'UTF-8');

        $this->assertSame(true, strpos($csvData, "M\xFCller") >= 0);
        $this->assertSame(true, strpos($csvData, "M\xFCnchen") >= 0);

        $dataUri = $this->convertToDataUri($csvData);

        $stream = fopen($dataUri, 'rb');

        $reader = new CsvReader($stream, 'ISO-8859-1', ',', '"', '\\');

        $data = array();

        while ($row = $reader->fetchAssoc(array('name', 'team', 'position'))) {
            $data[] = $row;
        }

        fclose($stream);

        $expected = array('name' => 'Thomas Müller', 'team' => 'FC Bayern München', 'position' => 'Sturm');
        $this->assertSame($expected, $data[0]);

        $expected = array('name' => 'Mario Gómez', 'team' => 'VfL Wolfsburg', 'position' => 'Sturm');
        $this->assertSame($expected, $data[5]);
    }

    /**
     *
     */
    public function testFetchAllAfterEofReturnsEmptyArray()
    {
        $stream = fopen(__DIR__ . '/csv-files/simple.csv', 'rb');
        $reader = new CsvReader($stream);
        $reader->fetchAllAssoc();

        $this->assertSame(array(), $reader->fetchAllAssoc());

        fclose($stream);

        $this->setExpectedException('Exception');
        $reader->fetchAllAssoc();
    }

    /**
     * @param $string
     * @return string
     */
    private function convertToDataUri($string)
    {
        return 'data://text/plain;base64,' . base64_encode($string);
    }
}

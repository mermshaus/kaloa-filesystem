# kaloa/filesystem

[![Latest Version](https://img.shields.io/github/release/mermshaus/kaloa-filesystem.svg?style=flat-square)](https://github.com/mermshaus/kaloa-filesystem/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/mermshaus/kaloa-filesystem/blob/master/LICENSE)
[![Build Status](https://img.shields.io/travis/mermshaus/kaloa-filesystem/master.svg?style=flat-square)](https://travis-ci.org/mermshaus/kaloa-filesystem)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/mermshaus/kaloa-filesystem.svg?style=flat-square)](https://scrutinizer-ci.com/g/mermshaus/kaloa-filesystem/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/mermshaus/kaloa-filesystem.svg?style=flat-square)](https://scrutinizer-ci.com/g/mermshaus/kaloa-filesystem)
[![Total Downloads](https://img.shields.io/packagist/dt/mermshaus/kaloa-filesystem.svg?style=flat-square)](https://packagist.org/packages/kaloa/filesystem)


## Install

Via Composer:

~~~ bash
$ composer require kaloa/filesystem
~~~


## Requirements

The following PHP versions are supported:

- PHP 5.3
- PHP 5.4
- PHP 5.5
- PHP 5.6
- PHP 7
- HHVM


## Documentation

### Usage

#### CsvReader

~~~ php
use Kaloa\Filesystem\CsvReader;

// General usage. Read all CSV rows from a stream into a numeric array.

$stream = fopen(__DIR__ . '/file.csv');
$csvReader = new CsvReader($stream);
$data = $csvReader->fetchAll();
fclose($stream);

// Read all CSV rows from a stream into an associative array.
// The first row from the input will be used as keys.

$data = $csvReader->fetchAllAssoc();

// If the file doesn't contain a row with keys, keys can be provided
// manually.

$data = $csvReader->fetchAllAssoc(array('id', 'title', 'date_added'));

// There's also a streaming mode available.

while ($row = $csvReader->fetch()) {
    // ...
}

// Streaming works with associative arrays, too.

while ($row = $csvReader->fetchAssoc()) {
    // ...
}

// Respectively:

while ($row = $csvReader->fetchAssoc(array('id', 'title', 'date_added'))) {
    // ...
}

// The reader automatically converts all input data to UTF-8. Differing input
// encodings may be defined in the constructor.

$csvReader = new CsvReader($iso88591stream, 'ISO-8859-1');

// The same goes for non-standard delimiter, enclosure and escape characters.

$csvReader = new CsvReader($stream, 'UTF-8', ':', '|', '%');
~~~


## Testing

(Tools are not included in this package.)

~~~ bash
$ phpunit
~~~

Further quality assurance:

~~~ bash
$ phpcs --standard=PSR2 ./src
$ phpmd ./src text codesize,design,naming
~~~


## Credits

- [Marc Ermshaus](https://github.com/mermshaus)


## License

The package is published under the MIT License. See [LICENSE](https://github.com/mermshaus/kaloa-filesystem/blob/master/LICENSE) for full license info.

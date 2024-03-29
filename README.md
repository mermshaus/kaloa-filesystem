# kaloa/filesystem

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
- PHP 7.0
- PHP 7.1
- HHVM


## Documentation

### CsvReader

#### Goals

- Provide an interface to read CSV data from streams into associative arrays.

#### Usage

Read all CSV rows from a stream into a numeric array. This is also a general usage example.

~~~ php
use Kaloa\Filesystem\CsvReader;

$stream = fopen(__DIR__ . '/file.csv', 'rb');

$csvReader = new CsvReader($stream);
$data = $csvReader->fetchAll();

fclose($stream);
~~~

Read all CSV rows from a stream into an associative array. The first row from the input will be used as keys.

~~~ php
$data = $csvReader->fetchAllAssoc();
~~~

If the file doesn't contain a row with keys, keys can be provided manually. The first row will be seen as regular data.

~~~ php
$data = $csvReader->fetchAllAssoc(array('id', 'title', 'date_added'));
~~~

There’s also a streaming mode available.

~~~ php
while ($row = $csvReader->fetch()) {
    // ...
}
~~~

Streaming works with associative arrays, too. Here, the first call to `fetchAssoc` will transparently read the first two rows from the input to read both the keys and the first data row.

~~~ php
while ($row = $csvReader->fetchAssoc()) {
    // ...
}
~~~

Respectively:

~~~ php
while ($row = $csvReader->fetchAssoc(array('id', 'title', 'date_added'))) {
    // ...
}
~~~

The reader class is intended to always return UTF-8 data. Differing CSV input encodings will be converted automatically if the input encoding is specified in the constructor.

~~~ php
$csvReader = new CsvReader($iso88591Stream, 'ISO-8859-1');
~~~

There’s also support for non-standard delimiter, enclosure and escape characters.

~~~ php
$csvReader = new CsvReader($stream, 'UTF-8', ':', '|', '%');
~~~

#### Further notes

- The `fetch` and `fetchAll` methods accept rows with varying numbers of fields in the same stream. The `fetchAssoc` and `fetchAllAssoc` methods will throw an exception if the number of fields in a row differs from the number of keys.
- It is not possible to change the names of the keys while iterating over input data with `fetchAssoc`. The reader always uses the keys from the first call to `fetchAssoc`.
- Calls to different `fetch*` methods must not be mixed. Currently, the code doesn’t prevent this, but it’s very likely that such functionality will be added in a future release.

#### Recipes

For usage with the reader, PHP strings can be converted to streams using the [data protocol](http://php.net/manual/en/wrappers.data.php).

~~~ php
$csvString = <<<'CSV'
"Col a","Col b"
"value 1a","value 1b"
"value 2a","value 2b"
CSV;

$dataUri = 'data://text/plain;base64,' . base64_encode($csvString);

$stream = fopen($dataUri, 'rb');

$reader = new CsvReader($stream);
~~~

### PathHelper

#### Goals

- “`realpath` for non-existing files” is the general idea.
- Resolve relative components (`.` and `..`) of “virtual” filesystem paths which don’t point to existing files or directories.
- Generate a normalized representation for such paths.
- The code has to be able to work with both absolute and relative paths on multiple systems.
- It has to be able to deal with absolute paths on Windows. (A path like `c:\foo` is to be considered absolute on Windows but relative on Linux. `c:\foo` is a valid filename on Linux.)

#### Usage

~~~ php
use Kaloa\Filesystem\PathHelper;

$pathHelper = new PathHelper();

$pathHelper->normalize('./dir1/dir2/dir3/dir4/../../../'); // "dir1"
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

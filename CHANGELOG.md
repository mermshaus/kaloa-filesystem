# Changelog

## [0.2.1] - 2022-06-17

- Added suitable version of PHPUnit as dev dependency.

## [0.2.0] - 2016-09-08

### Added

- Added `CsvReader`, a stream-based reader for CSV data that supports loading directly into associative arrays.
- Added an optional constructor parameter to `PathHelper` to specify the operating system. If not specified, the code tries to detect it automatically.
- Added support for absolute paths on Windows to `PathHelper::normalize`.

### Removed

- Removed method `normalizeDirectorySeparators` from `PathHelper`. Directory separators will now always be returned as `/` which also works on Windows.
- Removed `require-dev` dependencies to PHPUnit and other tools from `composer.json`. This might avoid unnecessary version conflicts.


[0.2.1]: https://github.com/mermshaus/kaloa-filesystem/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/mermshaus/kaloa-filesystem/compare/v0.1.0...v0.2.0

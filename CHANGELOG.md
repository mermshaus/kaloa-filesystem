# Changelog

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](http://semver.org/).


## [0.2.0] - 2016-09-08

### Added

- Added `CsvReader`, a stream-based reader for CSV data that supports loading directly into associative arrays.
- Added an optional constructor parameter to `PathHelper` to specify the operating system. If not specified, the code tries to detect it automatically. 
- Added support for absolute paths on Windows to `PathHelper::normalize`.

### Removed

- Removed method `normalizeDirectorySeparators` from `PathHelper`. Directory separators will now always be returned as `/` which also works on Windows.
- Removed `require-dev` dependencies to PHPUnit and other tools from `composer.json`. This might avoid unnecessary version conflicts.  


[0.2.0]: https://github.com/mermshaus/kaloa-filesystem/compare/v0.1.0...v0.2.0

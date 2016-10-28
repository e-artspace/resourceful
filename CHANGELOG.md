# resourceful Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [2.1.0] 2016-29-10

### Changed

- schema added at route policy middleware in crudControllerProvider
- StoreHelpers from traits to a standalone class


## [2.0.0] 2016-28-10

### Changed

- enforced id univocity on create
- removed not needed constructors and constructor paramethers
- code & tests refactory for a better silex conformance and to improve extensibility

### Added

- smoke tests with postman
- assertion in code

## [1.1.0] 2016-22-10

### Changed

- all private variables changed to protetected to allow class reuse
- git fetch upstream and  git merge upstream/master @ 1a8e69271c4a2771dbdc88b6d4437a46ef8f1113


## [1.0.3] 2016-09-29

### Changed

- git fetch upstream and  git merge upstream/master @ f5fee9a2226771b898ead2c4d31e7156796cf16f

## [1.0.2] 2016-09-28

### Changed

- some typos
- restored original code in IndexControllerProvider and move $app->flush in the example.
- buf fixed in Changelog


## [1.0.1] 2016-09-28

### Changed

- README improvements
- CHANGELOG improvement

## 1.0.0 2016-09-28

First release. From original project:

### Added

- vagrant support
- examples for code and schema
- License file

### Changed

- moved tests in a separate directory according with travis and scrutinizer standards
- allows to force id for a created item (if not present create an unique id)
- README improvements


[Unreleased]: https://github.com/e-artspace/resourceful/compare/2.1.0...HEAD
[2.1.0]: https://github.com/e-artspace/resourceful/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/e-artspace/resourceful/compare/1.1.0...2.0.0
[1.1.0]: https://github.com/e-artspace/resourceful/compare/1.0.3...1.1.0
[1.0.3]: https://github.com/e-artspace/resourceful/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/e-artspace/resourceful/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/e-artspace/resourceful/compare/1.0.0...1.0.1



# Change Log


## 3.0.0 - 2015-01-13

### Added

- PHP 5.4 minimum requirement
- byte value check in configuration sections

### Changed

- Refactors Connectors (interface changed)
- Closes down API
- Updates tests (uses PhpSpec and Behat)
- Major API change (BC break!)
- Configuration moved to a different package
- Event moved to a different package
- `isState` method is renamed to `checkState` (in both `Supervisor` and `Process`)
- Process must wait for the response of stop in `restart`
- `Section`s now use the name property instead of option
- `Section`s are able to return/set separate properties as well
- Updates dependencies
- Process object is immutable

### Removed

- Ability to pass `Process` object into `Supervisor` method calls: in case of different connector instances it could have led to an inconsistent state
- Ability to construct `Process` object from name, use `Process::get` instead
- Memory usage check form `Process`
- Fluent interfaces
- `setCredentials` method from `Connector` interface
- `isLocal` method from `Connector` interface


## 2.0.1 - 2014-07-13

### Changed

- Updates dependencies


## 2.0.0 - 2014-07-13

### Added

- Zend XML-RPC connector
- `AbstractNamedSection`

### Changed

- Uses Guzzle as HTTP Client by default
- Event and Event Listener restructure
- Major test changes (unit, functional)

### Removed

- HTTP client parts
- API from `Supervisor`


## 1.2.0 - 2014-05-06

### Changed

- Code coverage improved
- Unit tests improved
- Travis build improved
- Minor fixes


## 1.1.1 - 2014-01-29

### Changed

- Unit tests moved into Test namespace
- Fixed license issues


## 1.1.0 - 2014-01-20

### Added

- Symfony Commands
- Symfony Console Application
- Event Listeners
- `isLocal` to Connectors and Supervisor
- `SupervisorException`
- `RpcInterfaceSection`

### Changed

- Improved unit tests
- Fixed several bugs

### Removed

- `ResponseException`


## 1.0.0 - 2014-01-17

### Added

- Initial release
- Supervisor
- Configuration

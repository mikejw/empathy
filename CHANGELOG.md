
# Changelog

## [4.1.3] - 2025-08-09

### Fixed

- Improved JSONView response object checks


## [4.1.2] - 2025-08-09

### Fixed

- Add Unprocessable Entity JSONView return code


## [4.1.1] - 2025-08-03

### Fixed

- Build position insensitive insert SQL


## [4.1.0] - 2025-06-10

### Added

- Perform section URI caching


## [4.0.5] - 2025-06-07

### Fixed

- Update `.gitignore` file


## [4.0.4] - 2025-06-06

### Fixed

- Fail to load hidden sections


## [4.0.3] - 2025-06-05

### Fixed

- Check for `POST` values before attempting to assign them


## [4.0.2] - 2025-06-02

### Fixed

- Load only found model properties


## [4.0.1] - 2025-05-31

### Fixed

- Attempt to load default dynamic uri only when uri string is empty
- Handle bad default dynamic uri error


## [4.0.0] - 2025-05-28

### Changed

- Ignore deprecated errors when not `dev` environment (and handling errors)
- Upgrades and fixes for PHP 8.4
- Update URL validation regex

### Fixed

- Fix `EntityTest.php` `getAllCustom` call


## [3.1.1] - 2025-05-26

### Fixed

- Use fixed elibs version


## [3.1.0] - 2025-05-23

### Added

- Add default `favicon.ico`


## [3.0.0] - 2025-05-23

### Changed

- Tidy up `Model.php` class (Model class removed from `elib-base`)


## [2.1.2] - 2025-05-20

### Fixed

- Set `dev_rand` string when admin module


## [2.1.1] - 2025-05-19

### Fixed

- Improve ORM code


## [2.1.0] - 2025-05-17

### Added

- Create plugin `find` method


## [2.0.2] - 2025-05-16

### Fixed

- Introduce more robust id handling for `load` method


## [2.0.1] - 2025-05-16

### Fixed

- Produce simpler `pre` output without language class


## [2.0.0] - 2025-05-16

_Breaking changes for anything that relies on Empathy ORM Model (`Empathy\MVC\Entity` class)._

### Changed

- Tidy of overall `Entity.php` class


## [1.4.1] - 2025-05-15

### Fixed

- Give filtered image tags `img-fluid` class by default


## [1.4.0] - 2025-05-15

### Changed

- Allow `pre` tags in filtered HTML as well as small fixes for other tags


## [1.3.1] - 2025-05-12

### Fixed

- Perform view assignments from controller during pre-event


## [1.3.0] - 2025-05-12

### Added

- Move plugin init to controller to allow for controller-level plugin whitelists (see internal `empathy.php` controller)


## [1.2.0] - 2025-05-11

### Added

- Check for and execute `assertHost` when environnent variable is set


## [1.1.4] - 2025-05-08

### Fixed

- Clean up CLI help
- Catch all throwables in main exception handling
- Set HTTP status with safe exceptions


## [1.1.3] - 2025-05-02

### Fixed

- Upgrade to `tinymce` 7


## [1.1.2] - 2025-04-30

### Fixed

- Allow database connections when port is specified


## [1.1.1] - 2025-04-30

### Fixed

- Update version number


## [1.1.0] - 2025-04-22

_`dynamic_module_default_uri` `config.yml` setting will be documented soon in the ELib CMS extension._

### Added

- Introduce new `dynamic_module_default_uri` boot option config setting

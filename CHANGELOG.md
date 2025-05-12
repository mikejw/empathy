
# Changelog


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

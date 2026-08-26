# Changelog
All notable changes to this project will be documented in this file.

## 1.0.0

### Added

- Initial release

## 33.0.8

### Fixed

- Use an empty password in `completeLogin()` instead of the literal `"empty"`:
  the non-empty fake password was propagated by core's `UserLoggedInListener`
  (`updatePasswords`) onto every existing app token of the impersonated user,
  invalidating them all and forcing every sync client to reconnect.

## 31.0.0 - Fork by Nicolas Varlot

### Changed

- Fork of the original project for fixes and improvements
- Fixed deprecated ILogger interface
- More modern routes

### Authors

- Original author: Benjamin Sonntag
- Fork and improvements: Nicolas Varlot
# Changelog
All notable changes to this project will be documented in this file.

## 3.1.0 – 2021-07-08
### Added
- Nextcloud 21+22 support
- PHP 8.0 support
### Changed
- Moved CI testing to Github actions
### Fixed
- Updated NPM dependencies (some with vulnerabilites)
- Migrated to NPM 7 + NodeJS 14
- Removed unused PHP imports

## 3.0.0 – 2020-08-24
### Added
- Nextcloud 20 support
### Changed
- New and updated translations
### Fixed
- Database table layout (data loss unlikely but possible during migration)

## 2.1.0 – 2020-04-03
### Added
- Nextcloud 19 support
### Changed
- New and updated translations
### Fixed
- JavaScript vulnerabilities in dependency

## 2.0.0 – 2019-12-13
### Added
- php7.4 support
### Changed
- New and updated translations
### Fixed
- Event dispatcher incompatibility
- JavaScript vulnerability in `serialize-javascript` dependency
### Removed
- php7.1

## 1.0.0 – 2019-09-10
### Added
- Nextcloud 18 support
- Ability to generate codes on the UI as sub admin
### Changed
- New and updated translations
### Removed
- Nextcloud 16 support

## 0.4.1 – 2019-08-28
### Changed
- New and updated translations
### Fixed
- Vulnerabilities in npm dependencies

## 0.4.0 – 2019-06-25
### Added
- Nextcloud 17 support
- Ability to create codes from the web UI
### Fixed
- Compatibility with Mysql 10.2+

## 0.3.0 – 2018-11-20
### Added
- php7.3 support
### Removed
- Nextcloud 15 support
- php7.1 support
- postgres support (was never tested, does not pass CI)
- mysql >= 10.2 support (will come back with Nextcloud 17)

## 0.2.0 – 2018-11-20
### Added
- Enhanced login screen including an icon
### Removed
- Nextcloud 14 support

## 0.1.0 – 2018-10-08
### Added
- Initial implementation
- Initial documentation

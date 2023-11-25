# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.5.2] - 2023-11-25

### Added

- Added method ```AbstractController::getControllerName()```.

## [0.5.1] - 2023-10-02

### Changed

- Added support for regular expressions on ```View\Menu\Item::$paths```

## [0.5] - 2023-09-30

### Added

- Added cache support.

### Fixed

- Improved recognition of active state of menu items.
- Added resources recursively to support adding multiple resources.

## [0.4] - 2023-09-24

### Added

- Added simple intrusion detection system.

### Fixed

- Fixed handling of json requests.
- Fixed source code comments

## [0.3.1] - 2023-08-21

### Added

- Added wildcard option to menu items active state.
- Added configuration ```Platform.DefaultController.*``` to change default controller and action.

## [0.3] - 2023-07-15

### Added

- Added support for sub items.

### Fixed

- Improved PHP8.2 compatibility.

## [0.1.2] - 2023-07-11

### Added

- Added AbstractController::render()

## [0.1.1] - 2023-07-07

### Fixed

- Improved handling of cache directories.

## [0.1] - 2023-06-27

### Added

- Added front messaging.
- Added generic menu items.

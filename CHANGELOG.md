# Change Log

## 2.2.2

* Fix PHP 8.4 deprecations (implicit nullable)

## 2.2.1

* Allow Symfony 7

## 2.2.0

* Allow to use a date/time string in default expiration time configuration

## 2.1.0

* Minor **BC break**: use the new classes from `spatie/url-signer` 2.1.0 (`DateTimeInterface` instead of `DateTime` in `sign`)

## 2.0.0

* Upgrade to `spatie/url-signer` 2.x
* Minimal PHP version is now 8.1
* Expiration time is now in seconds instead of days

## 1.2.0

### New Features

* Allow Symfony 6

## 1.1.0

### New Features

* Add support for absolute URLs

## 1.0.2

### Bug Fixes

* Add optional expiration to `UrlSignerInterface`

## 1.0.1

### Chores

* Remove `league/uri-components` from `composer.json`

## 1.0.0

* Initial version

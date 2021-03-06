
<p align="center">
  <img src="https://packagist.org/bundles/packagistweb/img/logo-small.png?v=1584028323">
</p>

<p align="center">
<a href="https://github.com/ahinkle/packagist-latest-version/actions"><img src="https://github.com/ahinkle/packagist-latest-version/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/ahinkle/packagist-latest-version"><img src="https://img.shields.io/packagist/v/ahinkle/packagist-latest-version.svg?style=flat" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/ahinkle/packagist-latest-version"><img src="https://styleci.io/repos/248259797/shield?branch=master" alt="Style CI"></a>
<a href="https://packagist.org/packages/ahinkle/packagist-latest-version"><img src="https://img.shields.io/packagist/dt/ahinkle/packagist-latest-version.svg?style=flat" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/ahinkle/packagist-latest-version"><img src="https://img.shields.io/badge/License-MIT-brightgreen.svg" alt="License"></a>
</p>


# Composer Packagist Latest Version

This package provides an easy way to retrieve the latest stable release from composer packages via [the Packagist API](https://packagist.org/apidoc). This package was inspired by [this tweet](https://twitter.com/seldaek/status/1240285841492148225) after discovering that it's not possible to retrieve the latest non-development tagged release.

This package will return the _highest_ tagged non-developmental release. e.g. When there is a 2.1.0 release then a new 1.2.1 release is posted, this will continue to show that 2.1.0 is still the latest version.


## Installation

You can install the package via composer:

```bash
composer require ahinkle/packagist-latest-version
```

## Usage

``` php
$client = new \GuzzleHttp\Client();

$packagist = new PackagistLatestVersion($client);

$packagist->getLatestRelease('laravel/framework');
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email ahinkle10@gmail.com instead of using the issue tracker.

## Credits

- [Andy Hinkle](https://github.com/ahinkle)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
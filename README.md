# Laravel Plus

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]


This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what
PSRs you support to avoid any confusion with users and contributors.

## Install

Via Composer

``` bash
$ composer require maikovisky/laravel-plus
```

### Edit config/app.php

``` php
'providers' => [
    ...
    Maikovisky\LaravelPlus\LaravelPlusServiceProvider::class,
    Prettus\Repository\Providers\RepositoryServiceProvider::class,
    DaveJamesMiller\Breadcrumbs\ServiceProvider::class,
],

...

'aliases' => [
    // ...
    'Breadcrumbs' => DaveJamesMiller\Breadcrumbs\Facade::class,
    //...
],  

```


## Usage

Installing a layout.

``` bash
$ php artisan layout:install 
```

Creating a new CRUD. Create a controller, model, repository and views (index, show and edit).

``` bash
$ php artisan crud:new [Name] [tableName]
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email maikovisky@gmail.com instead of using the issue tracker.

## Credits

- [:author_name][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/maikovisky/laravel-plus.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/maikovisky/laravel-plus/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/maikovisky/laravel-plus.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/maikovisky/laravel-plus.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/maikovisky/laravel-plus.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/maikovisky/laravel-plus
[link-travis]: https://travis-ci.org/maikovisky/laravel-plus
[link-scrutinizer]: https://scrutinizer-ci.com/g/maikovisky/laravel-plus/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/maikovisky/laravel-plus
[link-downloads]: https://packagist.org/packages/maikovisky/laravel-plus
[link-author]: https://github.com/maikovisky
[link-contributors]: ../../contributors

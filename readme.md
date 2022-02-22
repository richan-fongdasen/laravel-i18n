[![Build](https://github.com/richan-fongdasen/laravel-i18n/actions/workflows/main.yml/badge.svg?branch=master)](https://github.com/richan-fongdasen/laravel-i18n/actions/workflows/main.yml) 
[![codecov](https://codecov.io/gh/richan-fongdasen/laravel-i18n/branch/master/graph/badge.svg)](https://codecov.io/gh/richan-fongdasen/laravel-i18n)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/richan-fongdasen/laravel-i18n/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/richan-fongdasen/laravel-i18n/?branch=master)
[![StyleCI Analysis Status](https://github.styleci.io/repos/135787392/shield?branch=master)](https://github.styleci.io/repos/135787392)
[![Total Downloads](https://poser.pugx.org/richan-fongdasen/laravel-i18n/d/total.svg)](https://packagist.org/packages/richan-fongdasen/laravel-i18n)
[![Latest Stable Version](https://poser.pugx.org/richan-fongdasen/laravel-i18n/v/stable.svg)](https://packagist.org/packages/richan-fongdasen/laravel-i18n)
[![License: MIT](https://poser.pugx.org/laravel/framework/license.svg)](https://opensource.org/licenses/MIT)

# Laravel I18n

> Simple Route localization and Eloquent translation in Laravel

## Synopsis

This package provides easy ways to setup database translation and route localization. 

## Table of contents

* [Setup](#setup)
* [Publish package assets](#publish-package-assets)
* [Configuration](#configuration)
* [Usage](#usage)
* [Credits](#credits)
* [License](#license)

## Setup

Install the package via Composer :
```sh
$ composer require richan-fongdasen/laravel-i18n
```

## Publish package assets

Publish the package asset files using this ``php artisan`` command

```sh
$ php artisan vendor:publish --provider="RichanFongdasen\I18n\ServiceProvider"
```

The command above would create three new files in your application, such as:
* New configuration file ``/config/i18n.php``
* New database migration ``/database/migrations/0000_00_00_000000_create_languages_table.php``
* New languages json file ``/storage/i18n/languages.json``

## Configuration

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Language repository driver
    |--------------------------------------------------------------------------
    |
    | Define your language repository driver here. The available options are:
    | database and json.
    |
    */

    'driver' => 'json',

    /*
    |--------------------------------------------------------------------------
    | Language repository datasource
    |--------------------------------------------------------------------------
    |
    | Define your language repository datasource here.
    |   - Define your database table name, when you're using database driver.
    |   - Define your json file path, when you're using json driver.
    |
    */

    'language_datasource' => storage_path('i18n/languages.json'),
//    'language_datasource' => 'languages',

    /*
    |--------------------------------------------------------------------------
    | Cache duration
    |--------------------------------------------------------------------------
    |
    | Define how long we should cache the language dataset in seconds.
    |
    */

    'cache_duration' => 60 * 60 * 24,

    /*
    |--------------------------------------------------------------------------
    | Language key
    |--------------------------------------------------------------------------
    |
    | Define which language key in datasource that we should use.
    | Available options are:
    |   - language, ie: en, es, de, etc.
    |   - ietfCode, ie: en-US, en-UK, de-DE, etc.
    |
    */

    'language_key' => 'language',

    /*
    |--------------------------------------------------------------------------
    | API query key
    |--------------------------------------------------------------------------
    |
    | Define the query parameter name which will be used as language selector
    | in every API request.
    | e.g: http://localhost:8000/api/articles?lang=en
    |
    */

    'api_query_key' => 'lang',

    /*
    |--------------------------------------------------------------------------
    | Language negotiator class
    |--------------------------------------------------------------------------
    |
    | Define your language negotiator class here.
    | The class should implement LanguageNegotiator contract / interface.
    |
    */

    'negotiator' => \RichanFongdasen\I18n\Negotiators\BrowserNegotiator::class,

    /*
    |--------------------------------------------------------------------------
    | Locale URL segment number
    |--------------------------------------------------------------------------
    |
    | Define which url segment number that will be used to put the current
    | locale information. URL segment is started with '1'.
    | e.g: http://my-application.app/en/home
    |
    */
   
    'locale_url_segment' => 1,

    /*
    |--------------------------------------------------------------------------
    | Translation table suffix
    |--------------------------------------------------------------------------
    |
    | Define your preferred suffix to be appended to your database's
    | translation table name.
    |
    */

    'translation_table_suffix' => 'translations',

    /*
    |--------------------------------------------------------------------------
    | Enable Store to the cache
    |--------------------------------------------------------------------------
    |
    | Toggle store locale to the cache
    |
    */

    'enable_cache' => env('I18N_ENABLE_CACHE', true),

];
```

## Usage

This section is currently under construction.

## Credits

* [mcamara/laravel-localization](https://github.com/mcamara/laravel-localization) - Route localization concepts in this repository was inspired by this package.
* [Wico Chandra](https://github.com/wicochandra) - Database translation concepts in this repository was inspired by his `Model Translation` concept.
* [dimsav/laravel-translatable](https://github.com/dimsav/laravel-translatable) - Some of database translation concepts in this repository was inspired by this package.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
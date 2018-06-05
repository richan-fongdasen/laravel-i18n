<?php

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

    /*
    |--------------------------------------------------------------------------
    | Cache duration
    |--------------------------------------------------------------------------
    |
    | Define how long we should cache the language dataset.
    |
    */

    'cache_duration' => 1440,

    /*
    |--------------------------------------------------------------------------
    | Language key
    |--------------------------------------------------------------------------
    |
    | Define which language key in datasource that we should use.
    | Available options:
    |   - language, ie: en, es, de, etc.
    |   - ietfCode, ie: en-US, en-UK, de-DE, etc.
    |
    */

    'language_key' => 'language',

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
    |
    */
   
    'locale_url_segment' => 1,

    /*
    |--------------------------------------------------------------------------
    | Fallback language
    |--------------------------------------------------------------------------
    |
    | Define your preferred fallback language, which will be used when
    | Language Negotiator failed to recommend any supported language.
    |
    */

    'fallback_language' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Translation table suffix
    |--------------------------------------------------------------------------
    |
    | Define your preferred suffix to be appended to your database's
    | translation table name.
    |
    */
    'translation_table_suffix' => 'translations'
];

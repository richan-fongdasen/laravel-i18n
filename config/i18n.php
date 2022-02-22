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

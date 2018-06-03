<?php

namespace RichanFongdasen\I18n\Eloquent;

use Illuminate\Database\Eloquent\Model;

class TranslationModel extends Model
{
    /**
     * Enable mass assignment for this
     * model
     *
     * @var boolean
     */
    protected static $unguarded = true;

    /**
     * Disable timestamp in any translation
     * model
     *
     * @var boolean
     */
    public $timestamps = false;
}

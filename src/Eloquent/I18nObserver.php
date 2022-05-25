<?php

namespace RichanFongdasen\I18n\Eloquent;

use ErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use RichanFongdasen\I18n\Contracts\TranslatableModel;

class I18nObserver
{
    /**
     * Handle the TranslatableModel "saved" event.
     *
     * @param \RichanFongdasen\I18n\Contracts\TranslatableModel $model
     *
     * @throws \ErrorException
     *
     * @return void
     */
    public function retrieved(TranslatableModel $model): void
    {
        $model->translateTo(App::getLocale());
    }

    /**
     * Handle the TranslatableModel "saved" event.
     *
     * @param \RichanFongdasen\I18n\Contracts\TranslatableModel $model
     *
     * @return void
     * @throws ErrorException
     */
    public function saved(TranslatableModel $model): void
    {
        if (!($model->getAttribute('translations') instanceof Collection)) {
            return;
        }

        $model->getAttribute('translations')->each(function ($translation) use ($model) {
            if (!($translation instanceof Model)) {
                throw new ErrorException('Invalid translation model, the given model is not an instance of Eloquent Model.');
            }

            if ($translation->isDirty()) {
                $translation->setAttribute($model->getForeignKey(), $model->getKey())
                    ->setTable($model->getTranslationTable())
                    ->save();
            }
        });
    }
}

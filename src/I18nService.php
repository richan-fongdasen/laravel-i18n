<?php

namespace RichanFongdasen\I18n;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RichanFongdasen\I18n\Contracts\LocaleRepository;
use RichanFongdasen\I18n\Contracts\TranslatableModel;
use RichanFongdasen\I18n\Eloquent\TranslationModel;

class I18nService
{
    /**
     * The LocaleRepository instance.
     *
     * @var LocaleRepository
     */
    protected LocaleRepository $repository;

    /**
     * The I18nRouter instance.
     *
     * @var I18nRouter
     */
    protected I18nRouter $router;

    /**
     * I18nService constructor.
     *
     * @param LocaleRepository $repository
     * @param Request          $request
     */
    public function __construct(LocaleRepository $repository, Request $request)
    {
        $this->repository = $repository;
        $this->router = new I18nRouter($request, $this);
    }

    public function createTranslation(TranslatableModel $model, Locale $locale): TranslationModel
    {
        return (new TranslationModel())
            ->setTable($model->getTranslationTable())
            ->fill([
                $model->getForeignKey() => $model->getKey(),
                'locale'                => $locale->getKey(),
            ]);
    }

    /**
     * Get all locale collection.
     *
     * @return Collection
     */
    public function getAllLocale(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get the default locale.
     *
     * @throws \ErrorException
     *
     * @return Locale
     */
    public function getDefaultLocale(): Locale
    {
        return $this->repository->default();
    }

    /**
     * Get locale based on the given key.
     *
     * @param string $key
     *
     * @return Locale|null
     */
    public function getLocale(string $key): ?Locale
    {
        $key = Str::replace('_', '-', $key);

        return $this->repository->get($key);
    }

    /**
     * Get locale keys in array based on the given attribute name.
     * If there was no attribute name specified, it will use
     * the default attribute name defined in config i18n.language_key.
     *
     * @param string|null $attributeName
     *
     * @return array|null
     */
    public function getLocaleKeys(?string $attributeName = null): ?array
    {
        return $this->repository->getKeys($attributeName);
    }

    /**
     * Guess the translation table name for the given model class name.
     *
     * @param string $modelName
     *
     * @return string
     */
    public function guessTranslationTable(string $modelName): string
    {
        $suffix = (string) config('i18n.translation_table_suffix');

        return Str::snake($modelName).'_'.$suffix;
    }

    /**
     * Returns the I18nRouter instance.
     *
     * @return I18nRouter
     */
    public function router(): I18nRouter
    {
        return $this->router;
    }
}

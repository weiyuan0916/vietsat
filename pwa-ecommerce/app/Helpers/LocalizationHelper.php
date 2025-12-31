<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;

class LocalizationHelper
{
    /**
     * Get localized text with optional parameters
     *
     * @param string $key
     * @param array $parameters
     * @param string|null $locale
     * @return string
     */
    public static function get(string $key, array $parameters = [], ?string $locale = null): string
    {
        return __($key, $parameters, $locale);
    }

    /**
     * Get home page localized text
     *
     * @param string $section
     * @param string $key
     * @param array $parameters
     * @return string
     */
    public static function home(string $section, string $key, array $parameters = []): string
    {
        return self::get("home.{$section}.{$key}", $parameters);
    }

    /**
     * Get navigation localized text
     *
     * @param string $key
     * @param array $parameters
     * @return string
     */
    public static function nav(string $key, array $parameters = []): string
    {
        return self::home('nav', $key, $parameters);
    }

    /**
     * Get menu localized text
     *
     * @param string $key
     * @param array $parameters
     * @return string
     */
    public static function menu(string $key, array $parameters = []): string
    {
        return self::home('menu', $key, $parameters);
    }

    /**
     * Get hero section localized text
     *
     * @param string $key
     * @param array $parameters
     * @return string
     */
    public static function hero(string $key, array $parameters = []): string
    {
        return self::home('hero', $key, $parameters);
    }

    /**
     * Get footer localized text
     *
     * @param string $key
     * @param array $parameters
     * @return string
     */
    public static function footer(string $key, array $parameters = []): string
    {
        return self::home('footer', $key, $parameters);
    }

    /**
     * Get common localized text
     *
     * @param string $key
     * @param array $parameters
     * @return string
     */
    public static function common(string $key, array $parameters = []): string
    {
        return self::home('common', $key, $parameters);
    }

    /**
     * Get current locale
     *
     * @return string
     */
    public static function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Get all supported locales
     *
     * @return array
     */
    public static function getSupportedLocales(): array
    {
        return config('app.supported_locales', ['en' => 'English']);
    }

    /**
     * Check if locale is supported
     *
     * @param string $locale
     * @return bool
     */
    public static function isSupportedLocale(string $locale): bool
    {
        return array_key_exists($locale, self::getSupportedLocales());
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return void
     */
    public static function setLocale(string $locale): void
    {
        if (self::isSupportedLocale($locale)) {
            App::setLocale($locale);
        }
    }

    /**
     * Get localized brand name with replacement
     *
     * @return string
     */
    public static function getBrandName(): string
    {
        return self::footer('brand_name');
    }

    /**
     * Get localized brand description with brand name replacement
     *
     * @return string
     */
    public static function getBrandDescription(): string
    {
        return self::footer('brand_description', [
            'brand' => self::getBrandName()
        ]);
    }
}

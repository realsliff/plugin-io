<?php

namespace IO\Helper;

use IO\Services\TemplateConfigService;
use IO\Services\UrlBuilder\UrlQuery;
use Plenty\Modules\Frontend\Services\AccountService;
use Plenty\Modules\ShopBuilder\Helper\ShopBuilderRequest;
use Plenty\Modules\Webshop\Contracts\LocalizationRepositoryContract;
use Plenty\Modules\Webshop\Contracts\WebstoreConfigurationRepositoryContract;
use Plenty\Plugin\Application;
use Plenty\Plugin\CachingRepository;
use Plenty\Plugin\Translation\Translator;

/**
 * Class Utils
 *
 * General utility class for often used functions.
 *
 * @package IO\Helper
 */
class Utils
{
    /**
     * Get the plenty id (The plenty id is often a large number)
     * @return int
     */
    public static function getPlentyId()
    {
        /** @var Application $app */
        $app = pluginApp(Application::class);
        return (int)$app->getPlentyId();
    }

    /**
     * Get the webstore id (The webstore id is often a low number)
     * @return int
     */
    public static function getWebstoreId()
    {
        /** @var Application $app */
        $app = pluginApp(Application::class);
        return (int)$app->getWebstoreId();
    }

    /**
     * Get the currently active language
     * @return string
     */
    public static function getLang()
    {
        /** @var LocalizationRepositoryContract $localizationRepository */
        $localizationRepository = pluginApp(LocalizationRepositoryContract::class);
        return $localizationRepository->getLanguage();
    }

    /**
     * Get the default language
     * @return string
     */
    public static function getDefaultLang()
    {
        /** @var WebstoreConfigurationRepositoryContract $webstoreConfigurationRepository */
        $webstoreConfigurationRepository = pluginApp(WebstoreConfigurationRepositoryContract::class);
        return $webstoreConfigurationRepository->getWebstoreConfiguration()->defaultLanguage;
    }

    /**
     * Get all enabled languages
     * @return array
     */
    public static function getLanguageList()
    {
        /** @var WebstoreConfigurationRepositoryContract $webstoreConfigurationRepository */
        $webstoreConfigurationRepository = pluginApp(WebstoreConfigurationRepositoryContract::class);
        return $webstoreConfigurationRepository->getActiveLanguageList();
    }

    /**
     * Check if the admin preview is active
     * @return bool
     */
    public static function isAdminPreview()
    {
        /** @var Application $app */
        $app = pluginApp(Application::class);
        return $app->isAdminPreview();
    }

    /**
     * Check if the current request is a shopbuilder request
     * @return bool
     */
    public static function isShopBuilder()
    {
        /** @var ShopBuilderRequest $sbRequest */
        $sbRequest = pluginApp(ShopBuilderRequest::class);
        return $sbRequest->isShopBuilder();
    }

    /**
     * Check if the current contact is logged in
     * @return bool
     */
    public static function isContactLoggedIn()
    {
        /** @var AccountService $accountService */
        $accountService = pluginApp(AccountService::class);
        return $accountService->getIsAccountLoggedIn();
    }

    /**
     * Get a value from the template config
     * @param string $key Key for the setting
     * @param mixed $default Default value, if the setting is empty
     * @return mixed|null
     */
    public static function getTemplateConfig($key, $default = null)
    {
        /** @var TemplateConfigService $templateConfigService */
        $templateConfigService = pluginApp(TemplateConfigService::class);
        return $templateConfigService->get($key, $default);
    }

    /**
     * Translate a multilingualism key
     * @param string $key Multilingualism key to be translated
     * @param array $params Additional parameters for the translation
     * @param string|null $locale Locale for translation. If null, use active locale
     * @return array|string|null
     */
    public static function translate($key, $params = [], $locale = null)
    {
        /** @var Translator $translator */
        $translator = pluginApp(Translator::class);
        return $translator->trans($key, $params, $locale);
    }

    /**
     * Transform a absolute url into a relative url
     * @param string|null $path An absolute url
     * @param bool $includeLanguage Should the url include the language (Default: false)
     * @return string|null
     */
    public static function makeRelativeUrl($path = null, $includeLanguage = false)
    {
        /** @var UrlQuery $query */
        $query = pluginApp(UrlQuery::class, ['path' => $path]);
        return $query->toRelativeUrl($includeLanguage);
    }

    /**
     * Put a value into the redis cache
     * @param string $key A cache key
     * @param mixed $value The value to be cached
     * @param int $timeInMinutes How long should the value be cached in minutes?
     */
    public static function putCacheKey($key, $value, $timeInMinutes)
    {
         /** @var Application $app */
        $app = pluginApp(Application::class);

        /** @var CachingRepository $cachingRepository */
        $cachingRepository = pluginApp(CachingRepository::class);

        $key = $app->getPlentyId() . '_' . pluginSetId() . '_' . $key;
        $cachingRepository->put($key,$value, $timeInMinutes);
    }

    /**
     * Get a value from the redis cache
     * @param string $key A cache key
     * @param mixed $defaultValue A default value, if the cache value does not exist
     * @return mixed
     */
    public static function getCacheKey($key, $defaultValue = null)
    {
         /** @var Application $app */
        $app = pluginApp(Application::class);
        /** @var CachingRepository $cachingRepository */
        $cachingRepository = pluginApp(CachingRepository::class);

        $key = $app->getPlentyId() . '_' . pluginSetId() . '_' . $key;
        return $cachingRepository->get($key, $defaultValue);
    }
}

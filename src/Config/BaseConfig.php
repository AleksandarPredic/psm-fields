<?php

namespace PSMFields\Config;

use PSMFields\Traits\SingletonTrait;

/**
 * Class BaseConfig represents library basic config
 * @package PSMFields\Config
 */
class BaseConfig
{
    use SingletonTrait;

    /**
     * Library version
     * @var string
     */
    protected $version = '0.9.0';

    /**
     * Root url of the library.
     * @var string
     */
    private $rootUrl;

    /**
     * BaseConfig constructor.
     */
    private function __construct()
    {
        $this->rootUrl = get_theme_file_uri('psm-fields');
    }

    /**
     * Return library textdomain
     * @return string
     */
    public function getTextDomain()
    {
        return 'psm-fields';
    }

    /**
     * Return assets url without backslash at the end
     * @return string
     */
    public function getAssetsUrl()
    {
        return $this->rootUrl . '/assets';
    }

    /**
     * Return library version
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
}

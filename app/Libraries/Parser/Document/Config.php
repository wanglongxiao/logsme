<?php

namespace App\Libraries\Parser\Document;

class Config
{
    /* Site configurations */
    const CONFIG_DEFAULT = 'default';
    const CONFIG_LISTVERSE = 'listverse';
    const CONFIG_BUZZFEED = 'buzzfeed';
    const CONFIG_THECHIVE = 'thechive';
    const CONFIG_TECHCRUNCH = 'techcrunch';
    const CONFIG_LIFEHACKER = 'lifehacker';
    const CONFIG_THEVERGE = 'theverge';
    const CONFIG_WIKIPEDIA = 'wikipedia';
    const CONFIG_MASHABLE = 'mashable';
    const CONFIG_NEXTWEB = 'thenextweb';
    const CONFIG_GIZMODO = 'gizmodo';
    const CONFIG_BUSSESSINSIDER = 'businessinsider';
    const CONFIG_BOINGBOING = 'boingboing';

    const CONFIG_HUFFINGTONPOST = 'huffingtonpost';

    const CONFIG_BOSTON_BIGPIC = 'boston';

    const CONFIG_POLYGON = 'polygon';
    const CONFIG_LAUGHINGSQUID = 'laughingsquid';
    const CONFIG_BOREDPANDA = 'boredpanda';
    const CONFIG_FUNNYORDIE = 'funnyordie';
    const CONFIG_XAXOR = 'xaxor';

    const CONFIG_TMZ = 'tmz';
    const CONFIG_CRACKED = 'cracked';
    const CONFIG_JUSTJARED = 'justjared';

    const CONFIG_SMITTENKITCHEN = 'smittenkitchen';

    const CONFIG_FUBIZ = 'fubiz';
    const CONFIG_FASTCODESIGN = 'fastcodesign';

    // configName => siteUrl (regex)
    static public $siteConfigs = array(
        self::CONFIG_LISTVERSE => 'listverse.com',
        self::CONFIG_BUZZFEED => 'buzzfeed.com',
        self::CONFIG_THECHIVE => 'thechive.com',
        self::CONFIG_TECHCRUNCH => 'techcrunch.com',
        self::CONFIG_LIFEHACKER => 'lifehacker.com',
        self::CONFIG_THEVERGE => 'theverge.com',
        self::CONFIG_WIKIPEDIA => 'wikipedia.org',
        self::CONFIG_MASHABLE => 'mashable.com',
        self::CONFIG_NEXTWEB => 'thenextweb.com',
        self::CONFIG_GIZMODO => 'gizmodo.com',
        self::CONFIG_BUSSESSINSIDER => 'businessinsider.com',
        self::CONFIG_BOINGBOING => 'boingboing.net',
        
        self::CONFIG_HUFFINGTONPOST => 'huffingtonpost.com',

        self::CONFIG_BOSTON_BIGPIC => 'boston.com\/bigpicture',

        self::CONFIG_POLYGON => 'polygon.com',
        self::CONFIG_LAUGHINGSQUID => 'laughingsquid.com',
        self::CONFIG_BOREDPANDA => 'boredpanda.com',
        self::CONFIG_FUNNYORDIE => 'funnyordie.com',
        self::CONFIG_XAXOR => 'xaxor.com',

        self::CONFIG_TMZ => 'tmz.com',
        self::CONFIG_CRACKED => 'cracked.com',
        self::CONFIG_JUSTJARED => 'justjared.com',

        self::CONFIG_SMITTENKITCHEN => 'smittenkitchen.com',

        self::CONFIG_FUBIZ => 'fubiz.net',
        self::CONFIG_FASTCODESIGN => 'fastcodesign.com',
    );

    /* Setting constant */

    const TEST_NODE_TAGS = 'testNodeTags';
    const IGNORED_CLASSES = 'ignoredClasses';
    const IGNORED_TAGS = 'ignoredTags';
    const ALLOWED_TAGS = 'allowedTags';
    const ALLOWED_ATTRS = 'allowedAttrs';
    const OVERRIDE_CLASSES = 'overrideClasses';


    const HANDLING_MAP = 'handlingMap';
    const IMAGE_HANDLING = 'imageHandling';
    const IH_A_TAG = 'aTagImage';
    const IH_SRC_HACK = 'imgSrcHack';
    const IH_POSTFIX_REPLACE = 'postFixReplace';

    const VIDEO_HANDLING = 'videoHandling';
    const VH_A_TAG = 'aTagVideo';

    const SETTING_TYPE_ARRAY = 'array';
    const SETTING_TYPE_BOOL = 'bool';
    const SETTING_TYPE_DIRECT = 'direct';



    static public $available_setting = array(
        self::TEST_NODE_TAGS => self::SETTING_TYPE_ARRAY,
        self::IGNORED_CLASSES => self::SETTING_TYPE_ARRAY,
        self::IGNORED_TAGS => self::SETTING_TYPE_ARRAY,
        self::ALLOWED_ATTRS => self::SETTING_TYPE_ARRAY,
        self::IMAGE_HANDLING => self::SETTING_TYPE_ARRAY,
        self::VIDEO_HANDLING => self::SETTING_TYPE_ARRAY,
        self::HANDLING_MAP => self::SETTING_TYPE_DIRECT
    );

    /**
     *
     * 
     */
    static function getConfigNameByUrl($url)
    {
        $foundConfigName = self::CONFIG_DEFAULT;

        foreach (self::$siteConfigs as $configName => $siteUrl) {
            $regexTags = sprintf("/^(?:(http|https):\/\/).*?(%s)\/.*/", $siteUrl);
            if (preg_match($regexTags, $url)) {
                $foundConfigName = $configName;
            }
        }

        return $foundConfigName;
    }

    static function getConfigByRequestUrl($url)
    {
        $foundConfigName = self::CONFIG_DEFAULT;

        foreach (self::$siteConfigs as $configName => $siteUrl) {
            $regexTags = sprintf("/^(?:(http|https):\/\/).*?(%s)\/.*/", $siteUrl);
            if (preg_match($regexTags, $url)) {
                $foundConfigName = $configName;
            }
        }

        return self::get($foundConfigName);
    }

    /**
     * Get given configuration, will auto merge default config
     * @param String $configName
     * @return Array
     */
    static function get($configName = self::CONFIG_DEFAULT)
    {
        $parserConfig = self::load(self::CONFIG_DEFAULT);

        if ($configName != self::CONFIG_DEFAULT) {
            $parserConfig = self::merge($parserConfig, $configName);
        }

        return $parserConfig;
    }

    /**
     * Load config from file
     * @param String $configName
     * @return Array $parserConfig
     */
    static function load($configName = self::CONFIG_DEFAULT)
    {  
        $parserConfig = array();

        if (!empty($configName)) {
            $configJson = file_get_contents(self::_getConfigFilePath($configName));
            if ($configJson) {
                $parserConfig = json_decode($configJson, true);
            }
        }

        return $parserConfig;
    }

    /**
     * Merge new configure into given config
     * @param Array $config
     * @param String $newConfigName
     * @return Array $parserConfig
     */
    static function merge($config, $newConfigName) 
    {
        if (empty($config)) {
            $config = array();            
        }

        if ($newConfigName != self::CONFIG_DEFAULT) {
            $newConfig = self::load($newConfigName);

            foreach (self::$available_setting as $key => $type) {
                if (isset($newConfig[$key]) && !isset($config[$key])) {
                    $config[$key] = $newConfig[$key];
                } else if (isset($newConfig[$key]) && isset($config[$key])) {
                    switch ($type) {
                        case self::SETTING_TYPE_ARRAY:
                            $config[$key] = array_merge($config[$key], $newConfig[$key]);
                            break;
                        default:
                            $config[$key] = $newConfig[$key];
                            break;
                    }                    
                }
            }            

            // Override default setting for some websites that using bad class name
            foreach ($config[self::IGNORED_CLASSES] as $key => $class) {
                if (isset($newConfig[self::OVERRIDE_CLASSES]) && in_array($class, $newConfig[self::OVERRIDE_CLASSES])) {
                    unset($config[self::IGNORED_CLASSES][$key]);
                }
            }
        }

        return $config;
    }

    /**
     * Get config file path by give config name
     * @param String $configName
     * @return String
     */
    static function _getConfigFilePath($configName)
    {
        return dirname(__FILE__)."/Configs/$configName.json";
    }
}
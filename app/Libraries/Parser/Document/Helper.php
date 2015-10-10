<?php

namespace App\Libraries\Parser\Document;

use App\Libraries\Parser\Document;
use App\Libraries\Parser\Video;

class Helper
{
    function __construct()
    {

    }

    static function convertDOMDoctoHTML($dom)
    {
        return mb_convert_encoding($dom->saveHTML(), Document::DOM_DEFAULT_CHARSET, "HTML-ENTITIES");
    }

    static function convertHTMLtoDOMDoc($html)
    {
        // Create DOM document
        $dom = new \DomDocument();
        try {
            if (!@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', Document::DOM_DEFAULT_CHARSET))) {
                throw new Exception("Parse HTML Error!");
            }

            $dom->encoding = Document::DOM_DEFAULT_CHARSET;

            return $dom;
        } catch (Exception $e) {
            // TODO : log error
        }

        return null; 
    }

    static function convertISO8601DurationToSeconds($duration)
    {
        return ($duration->y * 365 * 24 * 60 * 60) + 
               ($duration->m * 30 * 24 * 60 * 60) + 
               ($duration->d * 24 * 60 * 60) + 
               ($duration->h * 60 * 60) + 
               ($duration->i * 60) + 
               $duration->s;
    }

    static function convertSecondsToISO8601Duration($seconds)
    {
        $day = floor($seconds/86400);
        $hour = floor($seconds/3600 %24);
        $min = floor($seconds/60 % 60);
        $sec = $seconds %60;

        return "P".($seconds < 86400 ? "T":"").($day > 0 ? $day."D":"").($hour > 0 ? $hour."H":"").($min > 0 ? $min."M":"").($sec > 0 ? $sec."S":"");
    }

    static function convertSecondsToFrontendDuration($seconds)
    {
        $day = floor($seconds/86400);
        $hour = floor($seconds/3600 %24);
        $min = floor($seconds/60 % 60);
        $sec = $seconds %60;

        $str = "";

        if ($hour > 0) {
            $str .= (strlen($hour) > 1 || empty($str) ? $hour : "0$hour" ).":";
        }

        $str .= ((strlen($min) > 1 || empty($str)) && $min > 0 ? $min : "0$min" ).":";

        if ($sec > 0 || !empty($str)) {
            $str .= ((strlen($sec) > 1 || empty($str)) && $sec > 0 ? $sec : "0$sec" );
        }

        return $str;
    }

    static function isValidUrl($url)
    {
        $isValid = false;
		
        // 
        if (strpos($url,'http') === false) {
            $url = preg_replace("/^(\/\/)*/", "http://", $url);
        }

        if (filter_var($url, FILTER_VALIDATE_URL) !== FALSE) {
            return true;
        }
        return $isValid;
    }


    /**
     * Get query params from URL
     * @param String $url
     * @return Array $queryParams
     */
    static function getQueryParamsFromURL($url)
    {
        $urlInfo = parse_url($url);
        $queryParams = array();
        if (isset($urlInfo['query'])) {
            $options = explode("&", $urlInfo['query']);

            foreach ($options as $assignment) {
                $keyValue = explode("=", $assignment);
                if (count($keyValue == 2)) {
                    $queryParams[$keyValue[0]] = $keyValue[1];                    
                }
            }
        }

        return $queryParams;
    }

    /**
     * Get embed youtube video script
     * @param String $youtubeID
     * @return String
     */
    static function getEmbedYoutubePlayer($youtubeId)
    {
        return '<iframe type="text/html" data-external-provider="'.Video::EXTERNAL_PROVIDER_YOUTUBE.'" data-external-id="'.$youtubeId.'" src="http://www.youtube.com/embed/'.$youtubeId.'" frameborder="0" allowfullscreen></iframe>';
    }

    /**
     * Get embed vimeo video script
     * @param String $vimeoId
     * @return String
     */
    static function getEmbedVimeoPlayer($vimeoId)
    {
        return '<iframe type="text/html" data-external-provider="'.Video::EXTERNAL_PROVIDER_VIMEO.'" data-external-id="'.$vimeoId.'" src="http://player.vimeo.com/video/'.$vimeoId.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }

    /**
     * Get Vimeo ID from url
     * @param String $url
     * @return String - vimeo video ID
     */
    static function getVimeoIDfromURL($url)
    {
        if (empty($url)) return '';
        $match = self::getVimeoInfoFromURL($url);
        return empty($match) ? '' : $match[3];
    }

    static function getVimeoInfoFromURL($url)
    {
        $regex = "/(?:www\.|player\.)?vimeo.com\/(?:channels\/|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/)?(\d+)(#t=(\d+))?/";
        preg_match($regex, $url, $match);
        return $match;
    }

    static function getVimeoVideoStarttimeFromURL($url)
    {
        if (empty($url)) return 0;
        $match = self::getVimeoInfoFromURL($url);
        return empty($match) || count($match) < 5 ? 0 : intval($match[5]);
    }

    static function isVimeoVideo($url)
    {
        $isVimeoVideo = self::getVimeoIDfromURL($url);
        return !empty($isVimeoVideo);
    }

    /**
     * Get Youtube ID from url https://gist.github.com/afeld/1254889
     * @param String $url 
     * @return String - youtube video ID
     */ 
    static function getYoutubeIDfromURL($url)
    {
        if (empty($url)) return '';
        $match = self::getYoutubeInfoFromURL($url);        
        return empty($match) ? '' : $match[5];
    }

    static function getYoutubeVideoStarttimeFromURL($url)
    {
        if (empty($url)) return 0;
        $match = self::getYoutubeInfoFromURL($url);
        return empty($match) || count($match) < 7 ? 0 : intval($match[7]);
    }

    static function getYoutubeInfoFromURL($url)
    {
        $regex = "/(youtu\.be\/|youtube\.com\/(watch\?(.*&)?v=|(embed|v)\/))([^\?&\"\'\>\#]+)(.*\#t=(\d+))?/";
        preg_match($regex, $url, $match);
        return $match;
    }

    static function isYoutubeVideo($url)
    {
        $isYoutubeVideo = self::getYoutubeIDfromURL($url);
        return !empty($isYoutubeVideo);
    }

    // Judge youku.com video
    static function getYoukuIDfromURL($url)
    {
    	if (empty($url)) return '';
    	$match = self::getYoukuInfoFromURL($url);
    	return empty($match) ? '' : $match[5];
    }
    
    static function getYoukuInfoFromURL($url)
    {
    	$regex = "/(youku\.com\/(watch\?(.*&)?v=|(embed|v)\/))([^\?&\"\'\>\#]+)(.*\#t=(\d+))?/";
    	preg_match($regex, $url, $match);
    	return $match;
    }
    
    static function isYoukuVideo($url)
    {
    	$isYoukuVideo = self::getYoukuIDfromURL($url);
        return !empty($isYoukuVideo);
    }

    /**
     * Check if given url is image url (return false for wikimedia file permalink)
     * @param String $url
     * @return String
     */
    static function isImageUrl($url)
    {
        return preg_match("/^(?:(http|https):\/\/)*((?!wikimedia).)*((?!File\:).)*\.(jpg|gif|png|tiff)$/i", $url);
    }
    
    /**
     * Make video iframe responsive by bootstrap
     */
    static function betterHandleVideoIframe($html)
    {
    	$html = str_ireplace("<iframe", "<div class=\"embed-responsive embed-responsive-16by9\"><iframe", $html);
    	$html = str_ireplace("</iframe>", "</iframe></div>", $html);
    	return $html;
    }
    
}
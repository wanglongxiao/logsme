<?php

namespace App\Libraries\Parser;

/**
* 
*/
class Video
{
    protected $apiResponse = null;

    protected $provider = "";

    protected $url = "";
    protected $title = "";
    protected $thumbnail = "";
    protected $description = "";
    protected $duration = "";
    protected $interval = "";
    protected $starttime = 0;
    protected $content = "";
    protected $player ="";
    protected $externalView = 0;
    protected $externalId = "";

    const PROVIDER_YOUTUBE = 1;
    const PROVIDER_VIMEO = 2;

    /**
     * @param String $apiResponse - Response from remote server
     */
    function __construct($videoUrl, $apiResponse)
    {
        $this->apiResponse = json_decode($apiResponse, true);
        $this->url = $videoUrl;

        if (Document\Helper::isYoutubeVideo($videoUrl)) {
            $this->provider = self::PROVIDER_YOUTUBE;
        } else if (Document\Helper::isVimeoVideo($videoUrl)) {
            $this->provider = self::PROVIDER_VIMEO;
        }
    }

    public function getVideoInfo()
    {
        // var_dump($this->provider);
        $this->getVideoId($this->url);

        if ($this->provider == self::PROVIDER_YOUTUBE) {
            $this->getYoutubeVideoInfo();
        } else if ($this->provider == self::PROVIDER_VIMEO) {
            $this->getVimeoVideoInfo();
        } else {
            return array("msg" => "Unsupported provider");
        }

        return array(
            "title" => $this->title,
            "canonical" => $this->title,
            "thumbnail" => $this->thumbnail,
            "description" => substr($this->description, 0, 250),
            "videoExternalProvider" => $this->provider,
            "videoExternalId" => $this->externalId,
            "videoExternalView" => $this->externalView,
            "videoDuration" => $this->duration,
            "videoInterval" => $this->interval,
            "videoStarttime" => $this->starttime,
            "videoEndtime" => $this->interval,
            "hasVideo" => true,
            "hasImage" => false,
            "videos" => array($this->url),
            "images" => array(),
            "content" => $this->player,
            "okay" => true,
        );
    }

    protected function getVimeoVideoInfo()
    {
        if (Document\Helper::isVimeoVideo($this->url) && count($this->apiResponse) > 0) {

            $item = $this->apiResponse[0];
            
            if (isset($item['thumbnail_large'])) {
                $this->thumbnail = $item['thumbnail_large'];
            } else if (isset($item['thumbnail_medium'])) {
                $this->thumbnail = $item['thumbnail_medium'];
            } else if (isset($item['thumbnail_small'])) {
                $this->thumbnail = $item['thumbnail_small'];
            }

            $this->title = $item['title'];
            $this->interval = $item['duration'];
            $this->duration = Document\Helper::convertSecondsToISO8601Duration(\DateInterval::createFromDateString($this->interval.' seconds')->s);
            // $this->starttime = Document\Helper::getVimeoVideoStarttimeFromURL($this->url);
            $this->description = $item['description'];
            $this->externalView = $item['stats_number_of_plays'];
            $this->player = Document\Helper::getEmbedVimeoPlayer(Document\Helper::getVimeoIDfromURL($this->url));

            // if ($this->starttime >= $this->interval) {
                // $this->starttime = 0;
            // }
        }
    }

    protected function getYoutubeVideoInfo()
    {
        if (Document\Helper::isYoutubeVideo($this->url) && !empty($this->apiResponse['items'])) {

            $snippet = $this->apiResponse['items'][0]['snippet'];
            $detail = $this->apiResponse['items'][0];
            
            if (isset($snippet['thumbnails']['standard'])) {
                $this->thumbnail = $snippet['thumbnails']['standard']['url'];
            } else if (isset($snippet['thumbnails']['high'])) {
                $this->thumbnail = $snippet['thumbnails']['high']['url'];
            } else if (isset($snippet['thumbnails']['medium'])) {
                $this->thumbnail = $snippet['thumbnails']['medium']['url'];
            } else {
                $this->thumbnail = $snippet['thumbnails']['default']['url'];
            }
            
            $this->title = $snippet['title'];
            $this->duration = $detail['contentDetails']['duration'];
            $this->interval = Document\Helper::convertISO8601DurationToSeconds(new \DateInterval($this->duration));
            // $this->starttime = Document\Helper::getYoutubeVideoStarttimeFromURL($this->url);
            $this->description = $snippet['description'];
            $this->externalView = $detail['statistics']['viewCount'];
            $this->player = Document\Helper::getEmbedYoutubePlayer(Document\Helper::getYoutubeIDfromURL($this->url));

            // if ($this->starttime >= $this->interval) {
                // $this->starttime = 0;
            // }
        }
    }

    protected function getVideoId($url)
    {
        $videoId = "";
        if (Document\Helper::isYoutubeVideo($url)) {
            $videoId = Document\Helper::getYoutubeIDfromURL($url);
        } else if (Document\Helper::isVimeoVideo($url)) {
            $videoId = Document\Helper::getVimeoIDfromURL($url);
        }

        $this->externalId = $videoId;
        return $videoId;
    }

    static function getVideoAPIEndpoint($url)
    {
        $videoId = "";

        if (Document\Helper::isYoutubeVideo($url)) {
            $videoId = Document\Helper::getYoutubeIDfromURL($url);
            if (!empty($videoId)) {
                return self::getYoutubeAPIEndpointUrl($videoId);
            }
        } else if (Document\Helper::isVimeoVideo($url)) {
            $videoId = Document\Helper::getVimeoIDfromURL($url);
            if (!empty($videoId)) {
                return self::getVimeoAPIEndpointUrl($videoId);
            }
        }

        return "";
    }

    static function getVideoAPIEndpointByVid($type, $videoId) {
        if ($type == self::PROVIDER_YOUTUBE) {
            return self::getYoutubeAPIEndpointUrl($videoId);
        } else {
            return self::getVimeoAPIEndpointUrl($videoId);
        }
    }

    static function getYoutubeAPIEndpointUrl($videoId)
    {
        return "https://www.googleapis.com/youtube/v3/videos?part=id%2Csnippet%2Cplayer%2Cstatistics%2CcontentDetails&id=".$videoId."&key=".\Config::get('core.youtube.apiKey');
    }

    static function getVimeoAPIEndpointUrl($videoId)
    {
        return "http://vimeo.com/api/v2/video/".$videoId.".json";
    }

}
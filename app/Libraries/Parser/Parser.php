<?php

namespace App\Libraries\Parser;

/**
* 
*/
class Parser
{
    const USER_AGENT = "Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36";

    protected $requestUrl = null;

    /**
     * @var \Core\Repositories\PostRepositoryInterface
     */
    protected $postRepo;

    function __construct($requestUrl, $postRepo)
    {
        $this->requestUrl = $requestUrl;
        $this->postRepo = $postRepo;
    }

    public function fetch($type, $params = array())
    {
        if (empty($this->requestUrl)) {
            return $this->_throwError('Missing request url');
        }

        $endpoint = $this->requestUrl;

        if ($this->postRepo->isValidPostType($type) && $type == Post::TYPE_VIDEO) {
            $endpoint = Video::getVideoAPIEndpoint($this->requestUrl);

            if (empty($endpoint)) {
                return $this->_throwError("Not a valid video url");
            }
        }

        $response = $this->getFromEndpoint($endpoint);
        if ($response['okay']) {
            $source = $response['data'];

            if ($this->postRepo->isValidPostType($type) && $type == Post::TYPE_VIDEO) {
                /**
                 * Get response from video API
                 **/
                $video = new Video($this->requestUrl, $source);
                $data = $video->getVideoInfo();
                $data['url'] = $this->requestUrl;
                return $data;
            } else {
                /**
                 * Parse body by Source_Parser
                 */
                $configName = Document\Config::getConfigNameByUrl($this->requestUrl);
                $parser = new Document($source, $configName);
                $metaOnly = isset($params['metaOnly'])? $params['metaOnly']: null;
                $dev = isset($params['dev']) ? $params['dev']: null;
                $data = $parser->parseContent($metaOnly, $dev);
                $data['url'] = $this->requestUrl;
                return $data;
            }


        } else {
            return $response;
        }
    }

    public function getFromEndpoint($requestUrl)
    {
        try {
            $handle = curl_init();
            curl_setopt_array($handle, array(
                CURLOPT_USERAGENT => self::USER_AGENT,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HEADER  => false,
                CURLOPT_HTTPGET => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_URL => $requestUrl
            ));

            if( ! $response = curl_exec($handle)) { 
                return $this->_throwError("Error when connecting to url [".curl_error($handle)."]");
            }

            curl_close($handle);


            return array(
                "okay" => true,
                "data" => $response
            );

        } catch (Exception $e) {
            $errMsg = 'ERROR : '.$e->getMessage();
            return $this->_throwError($errMsg);
        }
    }

    private function _throwError($msg)
    {
        return array(
            'okay' => false,
            'msg' => $msg
        );
    }
}

?>
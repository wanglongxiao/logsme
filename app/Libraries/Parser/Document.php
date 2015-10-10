<?php

namespace App\Libraries\Parser;

use Sunra\PhpSimple\HtmlDomParser;

/**
* 
*/
class Document
{
    // May not be useful
    const ATTR_CONTENT_SCORE = "score";

    // Parse failed message
    const MESSAGE_CAN_NOT_GET = "Unable to parse this page for content.";

    // Only support UTF-8
    const DOM_DEFAULT_CHARSET = "utf-8";

    // DOM
    protected $DOM = null;

    // Source to be parsed
    protected $source = "";

    // Node's parent nodes
    private $parentNodes = array();

    // Document OG info
    private $_OGInfo = array();

    private $allowedTags = Array("p", "h1", "h2", "h3", "h4", "h5", "img", "pre", "iframe", "blockquote", "b", "i", "strong", "u", "li", "ul", "embed", "video");

    private $images = Array();
    private $videos = Array();

    // Config
    private $config = Array();
    
    // Traget Url Hostname
    private $targetHost = "";

    /**
     * @param String $source - input html string
     * @param String $input_char - string encoding, default "utf-8" can be ignored
     */
    function __construct($source, $config = Document\Config::CONFIG_DEFAULT, $targetHost = "")
    {    
    	$this->targetHost = $targetHost;
    	$this->config = Document\Config::get($config);
        $this->source = $source;
        \Log::info(__Method__." Load HTML with config name [$config]");

        // Convert source to UTF-8 format
        $source = mb_convert_encoding($source, 'HTML-ENTITIES', self::DOM_DEFAULT_CHARSET);
        $source = $this->prepareSource($source);

        // Create DOM document
        $this->DOM = new \DOMDocument('1.0', self::DOM_DEFAULT_CHARSET);
        try {
            //libxml_use_internal_errors(true);
            if (!@$this->DOM->loadHTML('<?xml encoding="'.self::DOM_DEFAULT_CHARSET.'">'.$source)) {
                throw new \Exception("Parse HTML Error!");
            }

            foreach ($this->DOM->childNodes as $item) {
                if ($item->nodeType == XML_PI_NODE) {
                    $this->DOM->removeChild($item); // remove hack
                }
            }

            // insert proper
            $this->DOM->encoding = self::DOM_DEFAULT_CHARSET;
        } catch (\Exception $e) {
            \Log::error(__Method__." Unable to construct with HTML source");
        }
    }


    /**
     * Pre-process HTML tags, converting br and font to paragraph
     * @return String $string
     */
    private function prepareSource($string)
    {
        preg_match("/charset=([\w|\-]+);?/", $string, $match);
        if (isset($match[1])) {
            $string = preg_replace("/charset=([\w|\-]+);?/", "", $string, 1);
        }

        // Replace all doubled-up <BR> tags with <P> tags, and remove fonts.
        $string = preg_replace("/<br\/?>[ \r\n\s]*<br\/?>/i", "</p><p>", $string);
        $string = preg_replace("/<\/?font[^>]*>/i", "", $string);

        return trim($string);
    }


    /**
     * Grab OG information and store (with PHP Dom Document)
     */
    private function _parseOGInfo() 
    {
        foreach ($this->DOM->getElementsByTagName("meta") as $meta) {
            $property = $meta->getAttribute('property');
            $content = $meta->getAttribute('content');

            preg_match("/^og:(.*)/i", $property, $match);

            if (!empty($match)) {
                $ogType = $match[1];

                if ($ogType == 'image') {
                    $this->_OGInfo['images'][] = $content;
                } else {
                    $this->_OGInfo[$ogType] = $content;
                }
            }
        }

        \Log::debug(__Method__." Parsed source OG Info [".json_encode($this->_OGInfo)."]");
    }

    /* For calculating content area */

    /**
     * Pre-filter ignored dom element for faster page parse
     */
    private function _preprocessPageFilter()
    {
        $html = HtmlDomParser::str_get_html(Document\Helper::convertDOMDoctoHTML($this->DOM));

        $body = $html->find("body", 0);
        if ($body) {
            foreach ($body->find("*") as $element) {
                $element->outertext = $this->_filterNode($element);
            }
        }

        $this->DOM = Document\Helper::convertHTMLtoDOMDoc($html->save());
    }

    /**
     * Recursively filter Dom node with ignored classes, tags and IDs
     * @param $node
     */
    private function _filterNode($node)
    {
        $html = '';

        $tagName    = $node->tag;
        $className  = $node->getAttribute('class') ? : '';
        $id         = $node->getAttribute('id') ? : '';
        $style      = $node->getAttribute('style') ? : '';

        $tagTestStr = $tagName;
        $ignoredTagsStr = implode("|", $this->config[Document\Config::IGNORED_TAGS]);
        $regexTags = sprintf('/(%s)/i', $ignoredTagsStr);

        $classTestString = $className."|".$id;
        $ignoresClassesStr = implode("|", $this->config[Document\Config::IGNORED_CLASSES]);
        $regexClasses = sprintf('/(%s)/i', $ignoresClassesStr);

        $regexStyleHidden = "/(display).*\:.*(none)\;$/";

        if ($tagName == 'comment' || 
            preg_match($regexTags, $tagTestStr, $matches) || 
            preg_match($regexClasses, $classTestString, $matches) ||
            preg_match($regexStyleHidden, $style, $matches)) {
            // Ignored class name found, remove node and ignore children
        } else {
            // Not found, going to check children
            $childNodes = $node->children();

            if (count($childNodes) > 0) {
                $i = 0;
                $tempNode = $node->innertext;
                foreach ($childNodes as $childNode) {
                    $tempChild = $childNode->outertext;
                    $tempNode = str_replace($tempChild, "{{tagHolder$i}}", $tempNode);
                    $newChildNode = $this->_filterNode($childNode);
                    $tempNode = str_replace("{{tagHolder$i}}", $newChildNode, $tempNode);
                    $i++;
                }
                $node->innertext = $tempNode;
            } else {
                // No children, return node  
            }
            
            $html .= $node->outertext;
        }

        return $html;
    }

    /**
     * Getting nodes for getTagNodesForCalculation
     * @param $nodeAry
     * @param $tagName
     * @return PHP Dom Nodes $contentArea
     */
    private function _getNodesWithTagName($nodeAry, $tagName)
    {
        $nodes = $this->DOM->getElementsByTagName($tagName);
        
        $i = 0;
        while ($node = $nodes->item($i++)) {
            $nodeAry[] = $node;
        }

        return $nodeAry;
    }

    /**
     * Return tags node to be included in content score calculation by config
     * @return Array of DOM Document $nodes
     */
    private function getTagNodesForCalculation()
    {
        $nodeTags = $this->config[Document\Config::TEST_NODE_TAGS];

        if (empty($nodeTags)) {
            $nodeTags = array("p", "div", "article");
        }

        $nodes = array();
        foreach ($nodeTags as $tag) {
            $nodes = $this->_getNodesWithTagName($nodes, $tag);            
        }

        return $nodes;
    }

    /**
     * Get main content view by calculating content score
     * Readability https://github.com/feelinglucky/php-readability
     * @return DOM Document
     */
    private function _getMainContent() 
    {
        $nodes = $this->getTagNodesForCalculation();

        foreach ($nodes as $node) {

            $parentNode   = $node->parentNode;
            $contentScore = intval($parentNode->getAttribute(self::ATTR_CONTENT_SCORE));
            $className    = $parentNode->getAttribute("class");
            $id           = $parentNode->getAttribute("id");

            // Give rank for specified classnames
            if (preg_match("/(comment|meta|footer|footnote)/i", $className)) {
                $contentScore -= 50;
            } else if(preg_match(
                "/((^|\\s)(post|hentry|entry[-]?(content|text|body|image|title)?|article[-]?(content|text|body)?)(\\s|$))/i",
                $className)) {
                $contentScore += 25;
            }

            // Give rank for specified IDs
            if (preg_match("/(comment|meta|footer|footnote)/i", $id)) {
                $contentScore -= 50;
            } else if (preg_match(
                "/^(post|hentry|entry[-]?(content|text|body|image|title)?|article[-]?(content|text|body)?)$/i",
                $id)) {
                $contentScore += 25;
            }

            // Add points for the node found

            // Add points for any node has some words
            if (strlen($node->nodeValue) > 10) {
                $contentScore += strlen($node->nodeValue);
            }

            // Save parentNode score
            $parentNode->setAttribute(self::ATTR_CONTENT_SCORE, $contentScore);

            // Keep parentNod for fast access
            array_push($this->parentNodes, $parentNode);
        }

        $topBox = null;
        
        // Assignment from index for performance. 
        for ($i = 0, $len = sizeof($this->parentNodes); $i < $len; $i++) {
            $parentNode      = $this->parentNodes[$i];
            $contentScore    = intval($parentNode->getAttribute(self::ATTR_CONTENT_SCORE));
            $orgContentScore = intval($topBox ? $topBox->getAttribute(self::ATTR_CONTENT_SCORE) : 0);

            if ($contentScore && $contentScore > $orgContentScore) {
                $topBox = $parentNode;
            }
        }

        return $topBox;
    }

    /**
     * Grab og:title, page title or try to get title from DOM (with PHP Dom Document)
     * @return String
     */
    private function _getArticleTitle() 
    {    
        if (isset($_OGInfo['title'])) {
            return $_OGInfo['title'];
        }

        foreach ($this->DOM->getElementsByTagName("title") as $node) {
            return $node->nodeValue;
        }

        return '';
    }

    /**
     * Get article summary from output content (trim from response content)
     * @param String $mainContent - expect html code here
     * @param int $length - maximum word count to summary cut off
     */
    private function _getArticleSummary($mainContent = '', $length = 250)
    {
        if (isset($this->_OGInfo['description'])) {
            return $this->_OGInfo['description'];
        }

        $summary = '';

        if (!empty($mainContent)) {
            $html = HtmlDomParser::str_get_html($mainContent);
            $string = $html->plaintext;
            $summary = preg_replace('/\s+/', ' ', $string);
            $summary = substr($summary, 0, $length);
            $summary = mb_convert_encoding("$summary", 'UTF-8', 'UTF-8');            
        }

        return $summary;
    }

    /*
     * Get all image urls from given container 
     * @param DOMNode $container
     * @return Array $images - array of image urls
     */
    private function _extractImages($container)
    {
        $html = HtmlDomParser::str_get_html(Document\Helper::convertDOMDoctoHTML($container));
        
        $images = array();
        foreach ($html->find("img") as $image) {
        	$imageUrl = $image->getAttribute("src");
        	if ($imageUrl == NULL or $imageUrl == "") {
        		$imageUrl = $image->getAttribute("data-src");
        	}
        	
        	// check $imageUrl includes hostname or not , change to full url path
        	$imgHost = parse_url($imageUrl, PHP_URL_HOST);
        	if ($imgHost == false || $imgHost == NULL) {
        		$imageUrl = "http://".$this->targetHost."/".$imageUrl;
        	}
        	
            // Return only valid image url
            if (Document\Helper::isValidUrl($imageUrl) && !in_array($imageUrl, $images))
            {
                $images[] = $imageUrl;
                // added by Alex Wang , 201510
                // convert possible bigger image to responsive, replace img style to bootstrap "class='img-responsive'"
                $width = 0;
                $size = getimagesize($imageUrl);
                // get width,  array(7) { [0]=> int(300) [1]=> int(225) ... }
                $width = $size[0];
                // Threshold is 300
                if ($width >= 300) {
                	$imageTagHtml = "<img src='$imageUrl' class='img-responsive'>";
                	$image->outertext = $imageTagHtml;
                } else {
                	// remove small image from html
                	$image->outertext = '';
                }
                
            } else {
                $image->outertext = '';
            }
        }

        $this->setImages($images);

        return Document\Helper::convertHTMLtoDOMDoc($html->save());
    }

    /*
     * Find article thumbnail from given image url array
     * @param Array $images - array of image urls
     * @return String $thumbnail - url of the biggest image
     */
    private function _getArticleThumbnail($images = array())
    {
        $thumbnail = '';
        $size = 0;

        // Step 1: Try to get og:image as thumbnail
        if (isset($this->_OGInfo['images']) && count($this->_OGInfo['images']) > 0) {
            return $this->_OGInfo['images'][0];
        }

        // Syep 2: Try to output first image if possible
        if (count($images) > 0) {
            return $images[0];
        }
        
        return $thumbnail;
    }

    /**
     * Extract video object from dom
     * @param DOM Document $container
     */
    private function _extractVideos($container)
    {
        // Not implemented
        $videos = array();
        $html = HtmlDomParser::str_get_html(Document\Helper::convertDOMDoctoHTML($container));        

        foreach ($html->find('iframe') as $element) {
            $iframeUrl = $element->getAttribute('src');
            if ($iframeUrl == NULL or $iframeUrl == "") {
            	$iframeUrl = $element->getAttribute("data-src");
            }

            // check $iframeUrl includes hostname or not , change to full url path
            $iframeHost = parse_url($iframeUrl, PHP_URL_HOST);
            if ($iframeHost == false || $iframeHost == NULL) {
            	$iframeUrl = "http://".$this->targetHost."/".$iframeUrl;
            }
            
            if (Document\Helper::isValidUrl($iframeUrl)) {

                if (Document\Helper::isYoutubeVideo($iframeUrl)) {
                    //$videoId = Document\Helper::getYoutubeIDfromURL($iframeUrl);
                    //$element->setAttribute("data-external-provider", "youtube");
                    //$element->setAttribute("data-external-id", $videoId);                    
                    $videos[] = $iframeUrl;
                }

                if (Document\Helper::isVimeoVideo($iframeUrl)) {
                    //$videoId = Document\Helper::getVimeoIDfromURL($iframeUrl);
                    //$element->setAttribute("data-external-provider", "vimeo");
                    //$element->setAttribute("data-external-id", $videoId);
                    $videos[] = $iframeUrl;
                }
                
                if (Document\Helper::isYoukuVideo($iframeUrl)) {
                	//$videoId = Document\Helper::getYoukuIDfromURL($iframeUrl);
                	//$element->setAttribute("data-external-provider", "youku");
                	//$element->setAttribute("data-external-id", $videoId);
                	$videos[] = $iframeUrl;
                }
            }
            
            // added by AlexWang, 201510
            $iframeTagHtml = "<iframe class='embed-responsive-item' src='$iframeUrl'></iframe>";
            $element->outertext = $iframeTagHtml;
        }

        // TODO: Support embed and video tag

        $this->setVideos($videos);

        return Document\Helper::convertHTMLtoDOMDoc($html->save());
        
       
    }

    /* For better output result */

    /**
     * Remove DOM elements with ignored tag name (with Simple PHP Dom Document)
     * @param $article - new article DOM Document
     * @return DOMDocument
     */
    private function _removeIgnoredTag($article)
    {
        $html = HtmlDomParser::str_get_html(Document\Helper::convertDOMDoctoHTML($article));

        foreach ($this->config[Document\Config::IGNORED_TAGS] as $tagName) {
            $tags = $html->find($tagName);

            foreach ($tags as $tag) {
                if ($tag->parent()->tag != "pre") {
                    $tag->outertext = '';
                }
            }
        }

        return Document\Helper::convertHTMLtoDOMDoc($html->save());
    }

    /**
     * Remove DOM attr
     * @param $article - new article DOM Document (with PHP Dom Document)
     * @return DOMDocument
     */
    private function _removeIgnoredAttr($article)
    {
        $tags = $article->getElementsByTagName("*");

        // Remove all class and style from tags
        $domx = new \DOMXPath($article);
        $allowedTagsString = $this->allowedTags;
        $allowedTagsString = array_map(function($tag){
            return "//".$tag;
        }, $allowedTagsString);        
        $expression = implode("|", $allowedTagsString)."[@style or @class]";
        $items = $domx->query($expression);
        foreach($items as $item) {
            $item->removeAttribute("style");
            $item->removeAttribute("class");
        }

        // Remove all attributes except allowed tags
        $i = 0;
        while($tag = $tags->item($i++)) {
            if ($tag->hasAttributes()) {
                foreach ($tag->attributes as $attr) {
                    if (!in_array($attr->name, $this->config[Document\Config::ALLOWED_ATTRS])) {
                        $tag->removeAttribute($attr->name);
                    }
                }
            }
        }

        return $article;
    }

    /*
     * Filter out unsupported type for editor (by Simple PHP Dom parser)
     * @param DOM Document $container
     */
    private function _convertAllowedContent($container)
    {
        $newHTML = Document\Helper::convertDOMDoctoHTML($container);
        $html = HtmlDomParser::str_get_html($newHTML);

        // Filter comments
        // TODO: Change to use purifier
        foreach($html->find('comment') as $element) {
            $parentTag = $element->parent()->tag;
            
            // Ignore comment under <pre> tag
            if ($parentTag != "pre") {
                $element->outertext = '';                
            }
        }

        $html = HtmlDomParser::str_get_html($html->save());

        // Filter special characters like next line, tab, etc
        foreach($html->find('div') as $element) {
            $innertext = trim($element->plaintext);
            if (empty($innertext)) {
                $element->outertext = '';
            } else {
                $element->innertext = str_replace(array("\r", "\n", "\t", "\v"), '', $element->innertext);
            }
        }

        $html = HtmlDomParser::str_get_html($html->save());

        // Encode data under <pre> tag
        // TODO: Change to use purifier
        foreach ($html->find('pre') as $element) {
            $element->innertext = htmlentities($element->innertext, ENT_QUOTES, "UTF-8");
        }

        $html = HtmlDomParser::str_get_html($html->save());

        // Convert tag to p
        foreach ($html->find('*') as $element) {
            $element->outertext = $this->_replaceNodeWithTag($element);
        }

        $html = HtmlDomParser::str_get_html($html->save());

        // Only allow youtube and vimeo iframe, filter out the rest
        // TODO: Change to use purifier
        foreach ($html->find('iframe') as $element) {
            $iframeUrl = $element->getAttribute('src');
            if (strpos($iframeUrl,'http') === false) {
                $iframeUrl = preg_replace("/^(\/\/)*/", "http://", $iframeUrl);
            }

            if (!Document\Helper::isValidUrl($iframeUrl) || 
                preg_match("/^.*(vimeo\.com|youtube\.com|youtu\.be)\/.*/", $iframeUrl) <= 0) {
                $element->outertext = '';
            }
        }

        return $html->save();
    }

    /**
     * Recursive method to convert tags to allowed tags ($this->allowedTags) (with simple PHP Dom parser)
     * @param $node - current level dom node
     */
    private function _replaceNodeWithTag($node)
    {
        $html = '';
        $childNodes = $node->children();

        if (count($childNodes) > 0) {
            foreach ($childNodes as $childNode) {

                $content = '';
                if (in_array($childNode->tag, $this->allowedTags)) { 

                    // Convert h1 and h2 to h3 and h4
                    if ($childNode->tag == 'h1') {
                        $childNode->outertext = "<h3>{$childNode->innertext}</h3>";
                    } else if ($childNode->tag == 'h2') {
                        $childNode->outertext = "<h4>{$childNode->innertext}</h4>";
                    }

                    if (!empty($childNode->innertext) || $childNode->tag == "img" || $childNode->tag == "iframe") { // Image and iframe always has no innertext
                        $html .= $childNode->outertext;
                    }
                } else {
                    // Not allowed tag, check it's child and convert
                    $childContent = $this->_replaceNodeWithTag($childNode);
                    $content = trim(preg_replace('/\s+/', ' ', $childContent));

                    if (!empty($content)) {
                        $content = $childContent;
                        // Check if <p> tag already generated, give it one if not
                        if (preg_match("%(<p[^>]*>.*?</p>)%i", $content, $match) == 0) {
                            $content = "<p>".$content."</p>";
                        }

                        $html .= $content;
                    }
                }
            }
        } else {
            // This is the end item of node tree, return result if not emtpy
            // Return node with original tag if 
            if (in_array($node->tag, $this->allowedTags)) {
                if (!empty($node->innertext)) {
                    $html = $node->outertext;                    
                }
            } else {
                $content = trim(preg_replace('/\s+/', ' ', $node->innertext));
                if (!empty($content)) {
                    $html = $content;
                }
            }
        }

        $html = trim($html);
        // Safe check again to prevent unused <p> to break layout
        if (!empty($html) && preg_match("%(<p[^>]*>.*?</p>)%i", $html, $match) == 0) {
            $html = "<p>".$html."</p>";
        }

        return $html;
    }

    /**
     * Image hack for some sites
     * @param Dom Document $container
     * @return Dom Document $container
     */
    private function _customHandlingOnImage($container)
    {
        $html = HtmlDomParser::str_get_html(Document\Helper::convertDOMDoctoHTML($container));

        $imageHandling = $this->config[Document\Config::IMAGE_HANDLING];
        $handlingMap = $this->config[Document\Config::HANDLING_MAP];

        if (!empty($imageHandling) && !empty($handlingMap)) {

            $imageChanged = array();

            // Image link hack
            if (in_array(Document\Config::IH_A_TAG, $imageHandling) &&
                isset($handlingMap[Document\Config::IH_A_TAG])) {

                // Hack here to check 
                $this->_extractImages($container);
                $fetchedImages = $this->getImages();

                $attrName = $handlingMap[Document\Config::IH_A_TAG];
                foreach ($html->find("a") as $link) {
                    $linkTo = $link->getAttribute($attrName);
                    if (Document\Helper::isImageUrl($linkTo) &&
                        !in_array($linkTo, $fetchedImages)) {

                        $link->outertext = "<img src='$linkTo' />";

                    }
                }

                // Save html
                $html = HtmlDomParser::str_get_html($html->save());
            }

            // Image tag hack
            
            // Handling Image src hack (copy real image src from tag attribute to src)
            if (in_array(Document\Config::IH_SRC_HACK, $imageHandling) &&
                isset($handlingMap[Document\Config::IH_SRC_HACK])) {

                $attrNames = $handlingMap[Document\Config::IH_SRC_HACK];

                // Fix if multiple image hack is needed
                if (!is_array($attrNames)) {
                    $attrNames = array($attrNames);
                }

                foreach ($html->find("img") as $image) {
                    $imageUrl = $image->getAttribute("src");

                    foreach ($attrNames as $attrName) {
                        if ($image->getAttribute($attrName)) {
                            $image->setAttribute("src", $image->getAttribute($attrName));
                        }
                    }
                }

                // Save html
                $html = HtmlDomParser::str_get_html($html->save());
            }

            // Handling GIF post fix replace
            if (in_array(Document\Config::IH_POSTFIX_REPLACE, $imageHandling) &&
                isset($handlingMap[Document\Config::IH_POSTFIX_REPLACE])) { 

                $replaceSetting = $handlingMap[Document\Config::IH_POSTFIX_REPLACE];
                $regex = sprintf("/\%s.gif$/", $replaceSetting['from']);
                foreach ($html->find("img") as $image) {
                    $imageUrl = $image->getAttribute("src");
                    $imageUrl = preg_replace($regex, $replaceSetting['to'], $imageUrl);
                    $image->outertext = "<img src=$imageUrl />";
                }

                // Save html
                $html = HtmlDomParser::str_get_html($html->save());
            }

            // Save and convert back to DOM Doc
            $container = Document\Helper::convertHTMLtoDOMDoc($html->save());
        }

        return $container;
    }

    /**
     * Video hack for some sites
     * @param Dom Document $container
     * @return Dom Document $container
     */
    private function _customHandlingOnVideo($container)
    {
        $html = HtmlDomParser::str_get_html(Document\Helper::convertDOMDoctoHTML($container));

        $videoHandling = $this->config[Document\Config::VIDEO_HANDLING];

        if (!empty($videoHandling)) {

            // Video link hack
            if (in_array(Document\Config::VH_A_TAG, $videoHandling)) {

                $lastVideoId = '';

                foreach ($html->find('a') as $videoLink) {
                    $videoUrl = $videoLink->getAttribute('href');            
                    if (Document\Helper::isValidUrl($videoUrl)) {

                        if (Document\Helper::isYoutubeVideo($videoUrl)) {
                            $videoId = Document\Helper::getYoutubeIDfromURL($videoUrl);
                            $script = Document\Helper::getEmbedYoutubePlayer($videoId);
                            if ($videoId != $lastVideoId) {
                                $videoLink->outertext = $script;                                
                            } else {
                                $videoLink->outertext = '';
                            }
                            $lastVideoId = $videoId;
                        }

                        if (Document\Helper::isVimeoVideo($videoUrl)) {
                            $videoId = Document\Helper::getVimeoIDfromURL($videoUrl);
                            $script = Document\Helper::getEmbedVimeoPlayer($videoId);
                            if ($videoId != $lastVideoId) {
                                $videoLink->outertext = $script;                                
                            } else {
                                $videoLink->outertext = '';
                            }
                            $lastVideoId = $videoId;
                        }                        

                    }
                }
            }

        }

        return Document\Helper::convertHTMLtoDOMDoc($html->save());
    }
    
    

    /* Main function */

    /**
     * Parse stored HTML and return site content
     */
    public function parseContent($metaOnly = false, $dev)
    {
        if (!$this->DOM) return false;

        // Set default value if caller does not care
        if ($metaOnly === null) $metaOnly = false;
        if ($dev === null) $dev = '0';

        // Parse available OG info
        $this->_parseOGInfo();

        if ($metaOnly) {
            $title = $this->_getArticleTitle();
            $response = array(
                'okay' => true,
                'title' => $title,
                'canonical' => $title,
                'thumbnail' => $this->_getArticleThumbnail(),
                'description' => $this->_getArticleSummary()
            );
            \Log::debug(__Method__." get META data only, going to return [".json_encode($response)."]");
            return $response;
        }

        // Pre-filter ignored dom element for faster page parse
        $this->_preprocessPageFilter();

        // Get article title
        $articleTitle = $this->_getArticleTitle();

        $canonical = $articleTitle;

        // Parse page main content by using Readability content score calculation
        $mainContent = $this->_getMainContent();

        //Check if we found suitable content.
        if($mainContent === null) {
            \Log::error(__Method__." Unable to find main content");
            return array('msg' => 'Unable to find main content');
        }

        // Create new DOMDocument
        $article = new \DOMDocument('1.0', self::DOM_DEFAULT_CHARSET);
        $article->appendChild($article->importNode($mainContent, true));

        // Development only
        if ($dev && $dev == '1') {
            echo Document\Helper::convertDOMDoctoHTML($article);
            exit;
        }

        // Filter unused tags
        // TODO: Change to use purifier
        $this->_removeIgnoredTag($article);

        // Filter unused attribtues
        // TODO: Change to use purifier
        $this->_removeIgnoredAttr($article);

        // Image fixing workflow
        $article = $this->_customHandlingOnImage($article);

        // Get image data from content
        $article = $this->_extractImages($article);

        // Get largest image as thumbnail
        $thumbnail = $this->_getArticleThumbnail($this->getImages());

        // Video fixing workflow
        $article = $this->_customHandlingOnVideo($article);

        // Get videos from content
        $article = $this->_extractVideos($article);

        // Convert unallowed tag to p, make filter, enrich content
        $contentHTML = $this->_convertAllowedContent($article);
        $contentHTML = Document\Helper::betterHandleVideoIframe($contentHTML);
        
        // Get article summary, expect contentHTML is plain html
        $summary = $this->_getArticleSummary($contentHTML);

        // $html = Document\Helper::convertDOMDoctoHTML($contentHTML);
        // echo $contentHTML;
        // $html = \Purifier::clean($contentHTML);
        // echo $html;
        // exit;

        // Development only
        if ($dev && $dev == '2') {
            echo $contentHTML;
            exit;
        }

        // Return response
        $response = array(
            'okay' => true,
            'title' => strip_tags($articleTitle),
            'canonical' => $canonical,
            'thumbnail' => $thumbnail,
            'description' => substr(strip_tags($summary), 0, 250),
            'hasVideo' => count($this->getVideos()) > 0,
            'hasImage' => count($this->getImages()) > 0,
            'videos' => $this->getVideos(),
            'images' => $this->getImages(),
        	'content' => strip_tags ($contentHTML,'<p><br><div><img><video><embed><iframe>')
        );

        return $response;
    }


    
    // Property methods

    /**
     * Get images
     * @return Array $images - array of image urls
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set images
     * @param Array $images - array of image urls
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * Get videos
     * @return Array $videos - array of video urls
     */
    public function getVideos()
    {
        return $this->videos;
    }

    /**
     * Set videos
     * @param Array $videos - array of video urls
     */
    public function setVideos($videos)
    {
        $this->videos = $videos;
    }





}

?>
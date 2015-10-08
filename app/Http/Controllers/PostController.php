<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Post;

class PostController extends Controller
{
	
	const ITEMS_PER_PAGE = 50;
	
	private $userAgents = array(
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.152 Safari/537.36'
	);
	
	/**
     * Get data from DB
     */
    public static function getListByFilter($terms, $limit = self::ITEMS_PER_PAGE, $offset = 0, $orderby = 'id', $order = 'desc')
    {
    	$data = array();
    	
    	$query = Post::select();
    	if (isset($terms) && is_array($terms) ) {
    		foreach($terms as $column => $value)
    		{
    			$query->where($column, '=', $value);
    		}
    	}
    	$query->orderBy($orderby, $order);
    	$query->take($limit)->skip($offset);
	    $data = $query->get();
    	return $data;
    }
    

    /**
     * Responds to requests to GET /
     */
    public static function getList()
    {
    	$type = "text/html";
    	$terms = array(
    		'isapproved' => 1,
    		'ispublished' => 0
    	);
    	 
    	/*
    	//$data = Post::all();
    	$data = Post::where('isapproved', 1)
    	->orderBy('id', 'desc')
    	->get();
    	*/
    	$data = self::getListByFilter($terms);
    	 
    	return response()->view('list', ['data' => $data])->header('Content-Type', $type);
    	//
    }
    
    /**
     * Responds to requests to GET /post/1
     */
    public static function getPost($id)
    {
    	//echo "#### ".$id."<br>";
    	
    	$post = new Post;
    	if ($id != null || $id != "") {
    		$post = Post::findOrFail($id);
    	}
    	$post = $post->attributesToArray();
    	
    	return response()->view('post', ['data' => $post, 'url' => $post['url'] ]);
    }
    
    /**
     * Responds to requests to GET /delete/1
     */
    public static function deletePost($id)
    {
    	//echo "#### ".$id."<br>";
    	 
    	if ($id != null || $id != "") {
    		$post = Post::findOrFail($id);
    		$post->delete();
    	}
    
    	return redirect('/');
    }
    
    /**
     * Update Multi rows
     */
    public static function updateByIds($terms, $ids)
    {
    	if (isset($terms) && is_array($terms) && isset($ids) && is_array($ids)) {
    		/*
    		$updateVals = array();
    		foreach($terms as $column => $value)
    		{
    			array_push($updateVals, "'".$column."' => '".$value."'");
    		}
    		$updateVal = implode(",", $updateVals);
    		*/
    		Post::whereIn('id', $ids)->update($terms);
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * Set post 'isapproved' true
     */
    public static function setApprovedById($id)
    {
    	$tearms = array(
    		'isapproved' => 1
    	);
    	self::updateByIds($terms, array($id));
    	
    	//return redirect('post/'.$id);
    }
    
    /**
     * Set post 'ispublished' false
     */
    public static function setUnPublishedById($id)
    {
    	$tearms = array(
    			'ispublished' => 0
    	);
    	self::updateByIds($terms, array($id));
    	 
    	//return redirect('post/'.$id);
    }
    
    /**
     * Responds to requests to POST /create
     */
    public function createPost(Request $request)
    {
    	/*
    	 echo "### create ###"."<br>";
    	 $input = $request->all();
    	 var_dump($input);die();
    	 */
    	 
    	$post = Post::firstOrNew(['url' => $request->url]);
    	//$post = new Post;
    	/*
    	try {
    	$post = Post::where('url', '=', $request->url)->firstOrFail();
    	} catch (Exception $e) {
    	$post = new Post;
    	}
    	*/
    	$url = urldecode(trim($request->url));
    	$parse = parse_url($url);
    	$sourcedomain =  $parse['host'];
    	 
    	$post->url = $url;
    	$post->sourcedomain = $sourcedomain;
    	$post->title = trim($request->title);
    	$post->description = trim($request->description);
    	$post->tags = trim($request->tags);
    	$post->isfeatured = $request->isfeatured;
    	$post->hasvideo = $request->hasvideo;
    	$post->ogimage = trim($request->ogimage);
    	$post->content = trim($request->editor1);
    	 
    	// default show on list, for upload all post by self
    	$post->isapproved = 1;
    
    	$post->save();
    
    	return redirect('/');
    }    
    
    /**
    * Responds to requests to POST /update
    */
    public function updatePost(Request $request)
    {
    
    if ($request->id != null && $request->id != "") {
    
    $id = $request->id;
    $post = Post::find($id);
    
    $url = urldecode(trim($request->url));
    $parse = parse_url($url);
    $sourcedomain =  $parse['host'];
    
    $post->url = $url;
    $post->sourcedomain = $sourcedomain;
    $post->title = trim($request->title);
    $post->description = trim($request->description);
    $post->tags = trim($request->tags);
    $post->isfeatured = $request->isfeatured;
    $post->hasvideo = $request->hasvideo;
    $post->ogimage = trim($request->ogimage);
    $post->content = trim($request->editor1);
    
    $post->save();
    
    return redirect('post/'.$id);
    }
    
    return redirect('/');
    }

    /**
     * Responds to requests to GET /fetch
     */
    public function fetchPost(Request $request)
    {
    	$url = $request->url;
    	
    	//TODO only support url with "http://" or "https://", check url is http/https or not
    	//echo "#### ".$url."<br>";
    	
    	if ($url != NULL && $url != "") {
    		$res = $this->fetchDataFromUrl($url);
	    	//var_dump($res);
	    	if ($res !== false) {
	    		//return response()->json($res);
	    		return response()->view('post', ['data' => $res, 'url' => $url]);
	    	} else {
	    		return response()->json(['error' => 400]);
	    	}
    	}
    	
    	return response()->view('post');
    }

    /**
     * fetch web url and parse
     */
    private function fetchDataFromUrl($url)
    {
    	try {
    		$options = array(
    				'http' => array(
    						'method' => 'GET',
    						'header' => implode("\r\n", array(
    								'accept: text/html,application/xhtml+xml,application/xml;',
    								'cache-control:max-age=0',
    								'Accept-language: en-US,en;q=0.8',
    								'User-Agent: ' . $this->userAgents[rand(0, count($this->userAgents) - 1)]
    						))
    				)
    		);
    		$context = stream_context_create($options);
    		$content = file_get_contents($url, false, $context);
    
    		$doc = new \App\Libraries\Parser\Document($content);
    		$res = $doc->parseContent(false, null);
    		if ($res['okay']) {
    			$images = $res['images'];
    			$parsedUrl = parse_url($url);
    			for ($i = count($images) - 1; $i >= 0; $i--) {
    				if (strpos($images[$i], 'http') !== 0) {
    					/*
    					 $parsedImages = parse_url($images[$i]);
    					 $parsedImages['scheme'] = $parsedUrl['scheme'];
    					 $parsedImages['host'] = $parsedUrl['host'];
    					 $images[$i] = http_build_url($parsedImages);
    					 */
    					array_splice($images, $i, 1);
    				}
    			}
    			$res['images'] = $images;
    
    			return $res;
    		} else {
    			return false;
    		}
    	} catch (Exception $e) {
    		Log::error('Cannot fetch data from url: ' . $url . '; ' . $e);
    		return false;
    	}
    }
    

}
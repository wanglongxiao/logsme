<?php
// delete the \n of mysql
$content = str_replace("&lt;","<",$data['content']);
$content = str_replace("&gt;",">",$content);
$content = str_replace("&amp;","&",$content);
$content = str_replace("&quot;","'",$content);
$content = str_replace("&nbsp;"," ",$content);

// parser tags
$alltags = Config::get("weixin.tags");
$tags = "";
if ($data['tags'] != "") {
	$tags = explode("," , $data['tags']);
	// keep existed array index only
	$tags = array_intersect($tags,array_keys($alltags));
}

$url = "http://".env("DOMAINNAME")."/post/".$data['id'];

// get referer domain
$refererdomain = "";
if (isset($_SERVER['HTTP_REFERER'])) {
	$referer = parse_url($_SERVER['HTTP_REFERER']);
	$refererdomain = $referer['host'];
}
?>

@if (!$ispreview)

	@include('header')
	
	@include('navbar')
	
	<div class="container">
		<!--  
		<img src='{{ $data['ogimage'] }}' width="320" class="img-thumbnail img-responsive">
		-->
		<div class="panel panel-default">
		 	<div class="panel-body">
		 		
		 		<div class="media">
				  <div class="media-body">
				  	<h3 class="media-heading">{{ $data['title'] }}</h3>
				  	<hr>
				  	@if ($tags != 0)
				    @foreach ($tags as $tag)
				    	<a href="/tag/{{ $tag }}" style="margin:0 0 5px 5px;"><span class="label label-primary">{{ $alltags[$tag] }}</span></a>
				    @endforeach
				    @endif
				    @if ($isadmin)
				  	<a href='/admin/delete/{{ $data["id"] }}' style="margin:0 0 5px 5px;">[Delete]</a>
				  	<a href='/admin/edit/{{ $data["id"] }}' style="margin:0 0 5px 5px;">[Edit]</a>
				  	@endif
				    <div class="bdsharebuttonbox" style="float:right;"><a href="{{$url}}" class="bds_more" data-cmd="more"></a><a href="{{$url}}" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a><a href="{{$url}}" class="bds_sqq" data-cmd="sqq" title="分享到QQ好友"></a><a href="{{$url}}" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="{{$url}}" class="bds_fbook" data-cmd="fbook" title="分享到Facebook"></a></div>
				    <br><br>
				    <?php echo $content; ?>
				  </div>
				</div>
				
			</div>
		</div>
	</div>
	
	@include('footer')

@else

	@if ($refererdomain == env("DOMAINNAME"))
	
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="myModalLabel">{{ $data['title'] }}</h3>
      </div>
      <div class="modal-body">
		@if ($tags != 0)
	    	@foreach ($tags as $tag)
	    		<a href="/tag/{{ $tag }}" style="float:right; margin:0 0 5px 5px;"><span class="label label-primary">{{ $alltags[$tag] }}</span></a>
			@endforeach
		@endif
		<br><br>
		<?php echo $content; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
      
	@else
	
		<html>
		<body>
		<script>
			window.location.href = '{{ url("http://".env("DOMAINNAME")) }}';
		</script>
		</body>
		</html>
	
	@endif
	
@endif

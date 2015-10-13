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
				  	@if ($isadmin)
				  	<a href='/admin/delete/{{ $data["id"] }}' style="float:right; margin:0 0 5px 5px;">[Delete]</a>
				  	<a href='/admin/edit/{{ $data["id"] }}' style="float:right; margin:0 0 5px 5px;">[Edit]</a>
				  	@endif
				    @if ($tags != 0)
				    @foreach ($tags as $tag)
				    	<a href="/tag/{{ $tag }}" style="float:right; margin:0 0 5px 5px;"><span class="label label-primary">{{ $alltags[$tag] }}</span></a>
				    @endforeach
				    @endif
				    <hr>
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
		<br>
		<?php echo $content; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
      
	@else
	
		<html><body><?php echo $promolink; ?></body></html>
	
	@endif
	
@endif

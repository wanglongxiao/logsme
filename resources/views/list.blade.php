@include('header')

@include('navbar')

<div class="container">
@foreach ($data as $item)

<?php
// delete the \n of mysql
$content = str_replace("&lt;","<",$item->content);
$content = str_replace("&gt;",">",$content);
$content = str_replace("&amp;","&",$content);
$content = str_replace("&quot;","'",$content);
$content = str_replace("&nbsp;"," ",$content);

$url = "http://".env("DOMAINNAME")."/post/".$item->id;

// parser tags
$alltags = Config::get("weixin.tags");
$tags = "";
if ($item->tags != "") {
	$tags = explode("," , $item->tags);
	// keep existed array index only
	$tags = array_intersect($tags,array_keys($alltags));
}
?>

		<div class="panel panel-default">
 		<div class="panel-body">

		<div class="row">
	  		<div class="col-md-12">
	  			<a href="/post/{{ $item->id }}"><img src='{{ $item->ogimage }}' class="img-responsive"></a>
	  		</div>
	  	</div>
	  	<div class="row">
	 		<div class="col-md-12">
	 			<p><a href="/post/{{ $item->id }}"><h3>{{ $item->title }}</h3></a></p>
	 		 	<p>{{ $item->description }}</p>
	 		 	<div>
	 		 		
	 		 		@if ($tags != 0)
	    			@foreach ($tags as $tag)
	    				<a href="/tag/{{ $tag }}"><span class="label label-primary">{{ $alltags[$tag] }}</span></a>
					@endforeach
					@endif
					
					<a href="/post/{{ $item->id }}/preview" data-toggle="modal" data-target="#basicModal-{{ $item->id }}" class="label label-info" role="span">预览文章</a>	
					
					<div class="bdsharebuttonbox" style="float:right;"><a href="{{$url}}" class="bds_more" data-cmd="more"></a><a href="{{$url}}" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a><a href="{{$url}}" class="bds_sqq" data-cmd="sqq" title="分享到QQ好友"></a><a href="{{$url}}" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="{{$url}}" class="bds_fbook" data-cmd="fbook" title="分享到Facebook"></a></div>
					
					@if ($isadmin)
						<br>
						@if ($item->ispublished == 0)
						<a href='/list?ispublished=0'><span class="label label-default">NotPublish</span></a>
						@else
						<a href='/list?ispublished=1'><span class="label label-success">Published</span></a>
						@endif
						@if ($item->isfeatured == 0)
						<a href='/list?isfeatured=0'><span class="label label-default">NotFeature</span></a>
						@else
						<a href='/list?isfeatured=1'><span class="label label-primary">Featured</span></a>
						@endif
						<a href='/admin/edit/{{ $item->id }}'>[Edit]</a>
						<a href='/admin/delete/{{ $item->id }}'>[Delete]</a>
					@endif
				</div>
	 		</div>
		</div>
		</div>
		</div>

<!-- Modal -->
<div class="modal fade" id="basicModal-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
		<!-- remote content here : /post/id/preview -->
    </div>
  </div>
</div>
		
@endforeach

</div>

<script type="text/javascript">
$('div.modal').on('show.bs.modal', function() {
	var modal = this;
	var hash = modal.id;
	window.location.hash = hash;
	window.onhashchange = function() {
		if (!location.hash){
			$(modal).modal('hide');
		}
	}
});

$('div.modal').on('hide', function() {
	var hash = this.id;
	history.pushState('', document.title, window.location.pathname);
});
</script>

@include('footer')

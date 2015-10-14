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
	  			<a href='/post/{{ $item->id }}'><img src='{{ $item->ogimage }}' class="img-responsive"></a>
	  		</div>
	  	</div>
	  	<div class="row">
	 		<div class="col-md-12">
	 		 	<p class="lead"><h3>{{ $item->title }}</h3></p>
	 		 	<p>{{ $item->description }}</p>
	 		 	<p>
	 		 		@if ($tags != 0)
	    			@foreach ($tags as $tag)
	    				<a href="/tag/{{ $tag }}"><span class="label label-primary">{{ $alltags[$tag] }}</span></a>
					@endforeach
					@endif
					@if ($isadmin)
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
					@endif
					<span style="float:right; margin:0 0 5px 5px;">
						<a href="/post/{{ $item->id }}/preview" data-toggle="modal" data-target="#basicModal-{{ $item->id }}">[预览]</a>
						<a href='/post/{{ $item->id }}'>[打开]</a>
						@if ($isadmin)
   						<a href='/admin/edit/{{ $item->id }}'>[Edit]</a>
						<a href='/admin/delete/{{ $item->id }}'>[Delete]</a>
						@endif
					</span>
				</p>
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

@include('footer')

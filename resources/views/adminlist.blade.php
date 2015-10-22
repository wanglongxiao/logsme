@include('header')

@include('navbar')

@if (Session::get('loginuser') != env('ADMINEMAIL'))
<script>
	window.location.href = '{{ url("http://".env("DOMAINNAME")) }}';
</script>
@endif

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
	  		<div class="col-md-3">
	  			<a href="/post/{{ $item->id }}"><img src='{{ $item->ogimage }}' class="img-responsive"></a>
	  		</div>
	 		<div class="col-md-9">
	 			<p><a href="/post/{{ $item->id }}"><h3>{{ $item->title }}</h3></a></p>
	 		 	<p>{{ $item->description }}</p>
	 		 	<div>
	 		 		
	 		 		@if ($tags != 0)
	    			@foreach ($tags as $tag)
	    				<a href="/tag/{{ $tag }}"><span class="label label-primary">{{ $alltags[$tag] }}</span></a>
					@endforeach
					@endif
					
					@if ($isadmin)
						@if ($item->isapproved == 0)
						<a href='/list?isapproved=0'><span class="label label-default">NotApprove</span></a>
						@else
						<a href='/list?isapproved=1'><span class="label label-warning">Approved</span></a>
						@endif
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
						@if ($item->hasvideo == 0)
						<a href='/list?type=img'><span class="label label-default">NotVideo</span></a>
						@else
						<a href='/list?type=vid'><span class="label label-info">HasVideo</span></a>
						@endif
						<a href='/admin/edit/{{ $item->id }}'>[Edit]</a>
						<a href='/admin/delete/{{ $item->id }}'>[Delete]</a>
					@endif
				</div>
	 		</div>
		</div>
		</div>
		</div>
		
@endforeach

</div>

@include('footer')

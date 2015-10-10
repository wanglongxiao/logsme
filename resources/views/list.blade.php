@include('header')

@include('navbar')

<div class="container">
	LIST<br><br><br>
</div>

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
$tags = "";
if ($item->tags != "") {
	$tags = explode("," , $item->tags);
}
?>

		<div class="panel panel-default">
 		<div class="panel-body">

		<div class="row">
	  		<div class="col-md-12">
	  			<img src='{{ $item->ogimage }}' class="img-thumbnail img-responsive">
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
					@if ($item->ispublished == 0)
					<span class="label label-default">NotPublish</span>
					@else
					<span class="label label-success">Published</span>
					@endif
					@if ($item->isfeatured == 0)
					<span class="label label-default">NotFeature</span>
					@else
					<span class="label label-primary">Featured</span>
					@endif
					<span style="float:right; margin:0 0 3px 3px;">
						<a href="#" data-toggle="modal" data-target="#basicModal-{{ $item->id }}">[View]</a>
						<a href='/post/{{ $item->id }}'>[Post]</a>  
   						<a href='/edit/{{ $item->id }}'>[Edit]</a>  
						<a href='/delete/{{ $item->id }}'>[Delete]</a>
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
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="myModalLabel">{{ $item->title }}</h3>
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
    </div>
  </div>
</div>
		
@endforeach
</div>

@include('footer')

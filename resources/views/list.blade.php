@include('header')

@include('navbar')

<div class="container">
	LIST<br><br><br>
</div>

<div class="container">
@foreach ($data as $item)

		<div class="panel panel-default">
 		<div class="panel-body">
 		<!--  
 		<div class="media">
		  <div class="media-left">
		    <a href="post/{{ $item->id }}">
		      <img class="media-object" src="{{ $item->ogimage }}" width="120" class="img-thumbnail" alt="...">
		    </a>
		  </div>
		  <div class="media-body">
		    <h4 class="media-heading">{{ $item->title }}</h4>
		    {{ $item->description }}
		  </div>
		</div>
 		-->
		<div class="row">
	  		<div class="col-md-2">
	  			<img src='{{ $item->ogimage }}' width="320" class="img-thumbnail img-responsive">
	  		</div>
	 		<div class="col-md-10">
	 		 	<p class="lead">{{ $item->title }}</p>
	 		 	<p>{{ $item->description }}</p>
	 		 	<p>
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
					<span style="float:right;">
						<a href="#" data-toggle="modal" data-target="#basicModal-{{ $item->id }}">[View]</a>  
   						<a href='/post/{{ $item->id }}'>[ Edit ]</a>  
						<a href='/delete/{{ $item->id }}'>[ Delete ]</a>
					</span>
				</p>
	 		</div>
		</div>
		</div>
		</div>

<!-- Modal -->
<?php
// delete the \n of mysql
$content = str_replace("&lt;","<",$item->content);
$content = str_replace("&gt;",">",$content);
$content = str_replace("&amp;","&",$content);
$content = str_replace("&quot;","'",$content);
$content = str_replace("&nbsp;"," ",$content);
?>
<div class="modal fade" id="basicModal-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="myModalLabel">{{ $item->title }}</h3>
      </div>
      <div class="modal-body">
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

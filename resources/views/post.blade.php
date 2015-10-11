@include('header')

@include('navbar')

<?php
// delete the \n of mysql
$content = str_replace("&lt;","<",$data['content']);
$content = str_replace("&gt;",">",$content);
$content = str_replace("&amp;","&",$content);
$content = str_replace("&quot;","'",$content);
$content = str_replace("&nbsp;"," ",$content);

// parser tags
$tags = "";
if ($data['tags'] != "") {
	$tags = explode("," , $data['tags']);
}
?>

<div class="container">
	POST<br><br><br>
</div>

<div class="container">
<!--  
<img src='{{ $data['ogimage'] }}' width="320" class="img-thumbnail img-responsive">
-->

<div class="panel panel-default">
 	<div class="panel-body">
 		
 		<div class="media">
		  <div class="media-body">
		  	<h3 class="media-heading">{{ $data['title'] }}</h3>	    
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

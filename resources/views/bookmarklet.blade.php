@include('header')

@include('navbar')

<?php 
$hosturl = "www.logs.me";
?>

	<div class="container">
		BOOKMARKLET<br><br><br>
	</div>
	
	<div class="container">
		<div class="panel panel-default">
 		<div class="panel-body">
 		<div class="form-group"> </div>
		<div class="bookmarklet">
			<a href='javascript:window.open("http://{{ $hosturl }}/fetch?url=" + encodeURIComponent(location.href), "_blank");' class="btn btn-primary">FETCH</a>
			<div class="desc">
				You can also submit via bookmarklet<br>Drag this to your bookmarks bar
			</div>
		</div>
		</div>
		</div>
	</div>
	
@include('footer')

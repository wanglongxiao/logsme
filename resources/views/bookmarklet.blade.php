@include('header')

@include('navbar')

@if (Session::get('loginuser') != env('ADMINEMAIL'))
<script>
	window.location.href = '{{ url("http://".env("DOMAINNAME")) }}';
</script>
@endif

	<div class="container">
		<div class="panel panel-default">
 		<div class="panel-body">
 		<div class="form-group"> </div>
 		@if (Session::get('loginuser') == env('ADMINEMAIL'))
		<div class="bookmarklet">
			<a href='javascript:window.open("http://{{ env("DOMAINNAME") }}/admin/fetch?url=" + encodeURIComponent(location.href), "_blank");' class="btn btn-primary">FETCH</a>
			<div class="desc">
				You can also submit via bookmarklet<br>Drag this to your bookmarks bar
			</div>
		</div>
		@endif
		</div>
		</div>
	</div>
	
@include('footer')

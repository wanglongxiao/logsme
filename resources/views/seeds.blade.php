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
 		
 		<div class="media">
		  <div class="media-body">
		  	@if (Session::get('loginuser') == env('ADMINEMAIL'))
		    <ul class="list-group">
			  <li class="list-group-item"><a href="http://www.teepr.com" target="_blank">http://www.teepr.com</a></li>
			  <li class="list-group-item"><a href="http://tw.gigacircle.com" target="_blank">http://tw.gigacircle.com</a></li>
			  <li class="list-group-item"><a href="http://www.buzzhand.com" target="_blank">http://www.buzzhand.com</a></li>
			  <li class="list-group-item"><a href="http://www.nmplus.hk" target="_blank">http://www.nmplus.hk</a></li>
			  <li class="list-group-item"><a href="http://www.bomb01.com" target="_blank">http://www.bomb01.com</a></li>
			  <li class="list-group-item"><a href="http://www.push01.com" target="_blank">http://www.push01.com</a></li>
			  <li class="list-group-item"><a href="http://www.life.com.tw" target="_blank">http://www.life.com.tw</a></li>
			  <li class="list-group-item"><a href="http://www.fhm.com.tw" target="_blank">http://www.fhm.com.tw</a></li>
			  <li class="list-group-item"><a href="http://www.weekendhk.com" target="_blank">http://www.weekendhk.com</a></li>
			  <li class="list-group-item"><a href="http://www.goez1.com" target="_blank">http://www.goez1.com</a></li>
			  <li class="list-group-item"><a href="http://cool3c.com" target="_blank">http://cool3c.com</a></li>
			</ul>
			@endif
		  </div>
		</div>
		
	</div>
</div>

</div>

@include('footer')

<?php 
$currenturlpath = parse_url(Request::url(), PHP_URL_PATH);
$urlpaths = explode("/", trim($currenturlpath, "/"));
?>
<div class="container">
<br><br><br>
</div>

	@if ($urlpaths[0] != 'admin')
	<!-- GA -->
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
	  ga('create', 'UA-18952026-1', 'auto');
	  ga('send', 'pageview');
	
	</script>
	@endif
	
	<!-- facebook SDK -->
	<script type="text/javascript">
	  window.fbAsyncInit = function() {
	    FB.init({
	      appId      : '{{ env('FB_CLIENT_ID') }}',
	      xfbml      : true,
	      version    : 'v2.5'
	    });
	  };
	
	  (function(d, s, id){
	     var js, fjs = d.getElementsByTagName(s)[0];
	     if (d.getElementById(id)) {return;}
	     js = d.createElement(s); js.id = id;
	     js.src = "//connect.facebook.net/en_US/sdk.js";
	     fjs.parentNode.insertBefore(js, fjs);
	   }(document, 'script', 'facebook-jssdk'));
	</script>
	
	<!-- Judge Agent -->
	<script type="text/javascript"> 
	function isMobile(){
		if(/AppleWebKit.*Mobile/i.test(navigator.userAgent) || (/MIDP|SymbianOS|NOKIA|SAMSUNG|LG|NEC|TCL|Alcatel|BIRD|DBTEL|Dopod|PHILIPS|HAIER|LENOVO|MOT-|Nokia|SonyEricsson|SIE-|Amoi|ZTE/.test(navigator.userAgent))){
			if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)){
		    	return true;
		    }else if(/iPad/i.test(navigator.userAgent)){
			    // this is ipad
		    	return false;
		    }else{
		        // should be old phone
		    	return true;
		    }  
		}
	}
	
	function isWeixin(){
		var ua = navigator.userAgent.toLowerCase();
		if(ua.match(/MicroMessenger/i)=="micromessenger") {
			return true;
	 	} else {
			return false;
		}
	}
	</script>

</body>
</html>
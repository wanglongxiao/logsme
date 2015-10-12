<!DOCTYPE html>
<html lang="en">
<head>
        <title></title>

        <!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
		
		<meta name="viewport" content="width=device-width, initial-scale=1">
				
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		
		<style type="text/css">
		
			@media (min-width: 992px) {
			  .container {
			    width: 768px;
			  }
			}
			
			@media (min-width: 1200px) {
				.sqimage{
				    position:relative;
				    overflow:hidden;
				    padding-bottom:100%;
				}
				.sqimage img{
				    position:absolute;
				}
			}
			
		</style>
        
</head>
<body>
	<script>
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
	<div class="container">
		<a href="/"><img src="/img/logo.jpg" class="img-responsive"></a>
	</div>
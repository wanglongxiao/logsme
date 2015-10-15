<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset='utf-8' />
        <!-- not impact web page by baidu reader -->
        <meta http-equiv="Cache-Control" content="no-siteapp" />
        <!-- for web page responsive --> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- 针对手持设备优化，主要是针对一些老的不识别viewport的浏览器，比如黑莓 -->
    	<meta name="HandheldFriendly" content="true">
    	<!-- 微软的老式浏览器 -->
    	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    	<meta name="MobileOptimized" content="320">
    	<!-- for apple webapp -->
    	<meta name="apple-mobile-web-app-capable" content="yes" />
    	<meta name="apple-mobile-web-app-title" content="{{ Config::get('weixin.defaulthtmltitle') }}">
    	<meta name="apple-mobile-web-app-status-bar-style" content="black">
        
        @if (isset($meta) && $meta != "")
        	<?php echo $meta; ?>
        @else
        	<meta name="robots" content="noindex" />
        	<title><?php echo Config::get('weixin.defaulthtmltitle'); ?></title>
        @endif
        
        <link rel="image_src" href="/img/320_320.png" />
        <link rel="icon" href="/img/favicon_32.png" type="image/x-icon">
    	<link rel="apple-touch-icon" href="/img/apple-icon-57_57.png" />
		
        <!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">		
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
			
			/*
			@media (max-width: 768px) {
			    .modal {
				  text-align: center;
				}
				.modal-dialog {
				  display: inline-block;
				  width: 90%;
				  text-align: left;
				  vertical-align: middle;
				}
			}
			*/
			
			@media (max-width: 480px) {
			    .container {
			      padding:0;
			      margin:0;
			    }
			    body {
			      padding:0;
			    }
			    .navbar-fixed-top, .navbar-fixed-bottom, .navbar-static-top {
			      margin-left: 0;
			      margin-right: 0;
			      margin-bottom:0;
			    }
			}
			
			a { text-decoration: none !important; }
			
		</style>
	
	<!-- facebook SDK -->
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
</head>
<body>	
<div class="container">
	<a href="/"><img src="/img/logo.jpg" class="img-responsive"></a>
</div>

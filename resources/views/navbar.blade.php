<nav class="navbar navbar-inverse navbar-fixed-bottom">
  <div class="container">
    <p class="navbar-text navbar-right">
    	<a href='/'>首页</a> |
    	@foreach (Config::get("weixin.tags") as $key => $val)
    	<a href='/tag/{{ $key }}'><?php echo mb_substr($val, -2); ?></a> |
    	@endforeach
    	@if (Session::get('loginuser') == env('ADMINEMAIL'))
    	<a href='/admin/fetch' style="color:#FFFFFF;">抓取</a> |
    	<a href='/admin/bookmarklet' style="color:#FFFFFF;">快键</a> |
    	<a href='/auth/facebook' style="color:#FFFFFF;">登录</a> |
    	@endif
    	<a href='/howtoplayvideo'>帮助</a>
    </p>
  </div>
</nav>

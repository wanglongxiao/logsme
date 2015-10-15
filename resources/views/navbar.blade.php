<nav class="navbar navbar-inverse navbar-fixed-bottom">
  <div class="container">
    <p class="navbar-text navbar-right">
    	&nbsp;&nbsp;&nbsp;&nbsp;<a href='/'>首页</a> |
    	@foreach (Config::get("weixin.tags") as $key => $val)
    	<a href='/tag/{{ $key }}'><?php echo mb_substr($val, -2); ?></a> |
    	@endforeach
    	<a href='/howtoplayvideo'>帮助</a>
    	@if (Session::get('loginuser') == env('ADMINEMAIL'))
    	<br>
    	&nbsp;&nbsp;&nbsp;&nbsp;<a href='/admin/fetch' style="color:#FFFFFF;">抓取</a>
    	| <a href='/admin/bookmarklet' style="color:#FFFFFF;">快键</a>
    	| <a href='/admin/seeds' style="color:#FFFFFF;">素材</a>
    	| <a href='/auth/facebook' style="color:#FFFFFF;">登录</a>
    	@endif
    </p>
  </div>
</nav>

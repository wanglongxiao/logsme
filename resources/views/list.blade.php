@include('header')

@include('navbar')

<div class="container">
@foreach ($data as $item)

<?php
// delete the \n of mysql
$content = str_replace("&lt;","<",$item->content);
$content = str_replace("&gt;",">",$content);
$content = str_replace("&amp;","&",$content);
$content = str_replace("&quot;","'",$content);
$content = str_replace("&nbsp;"," ",$content);

$url = "http://".env("DOMAINNAME")."/post/".$item->id;

// parser tags
$alltags = Config::get("weixin.tags");
$tags = "";
if ($item->tags != "") {
	$tags = explode("," , $item->tags);
	// keep existed array index only
	$tags = array_intersect($tags,array_keys($alltags));
}
?>

		<div class="panel panel-default">
 		<div class="panel-body">

		<div class="row">
	  		<div class="col-md-12">
	  			<a href="/post/{{ $item->id }}"><img src='{{ $item->ogimage }}' class="img-responsive"></a>
	  		</div>
	  	</div>
	  	<div class="row">
	 		<div class="col-md-12">
	 			<p><a href="/post/{{ $item->id }}"><h3>{{ $item->title }}</h3></a></p>
	 		 	<p>{{ $item->description }}</p>
	 		 	<div>
	 		 		
	 		 		@if ($tags != 0)
	    			@foreach ($tags as $tag)
	    				<a href="/tag/{{ $tag }}" style="font-size:18px;"><span class="label label-primary">{{ $alltags[$tag] }}</span></a>
					@endforeach
					@endif
					
					<!-- hide preview on mobile -->
					<a href="/post/{{ $item->id }}/preview" data-toggle="modal" data-target="#basicModal-{{ $item->id }}" style="font-size:18px;"><span class="label label-info" style="display:none;">预览文章</span></a>	
					
					<div class="bdsharebuttonbox" style="float:right;margin:0 0 5px 5px;">
						<a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信" data-id="{{ $item->id }}" style="display:inline;"></a>
						<a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间" data-id="{{ $item->id }}"></a>
						<a href="#" class="bds_tsina" data-cmd="tsina" title="分享到微博" data-id="{{ $item->id }}"></a>
						<a href="#" class="bds_fbook" data-cmd="fbook" title="分享到Facebook" data-id="{{ $item->id }}"></a>
					</div>

				</div>
	 		</div>
		</div>
		</div>
		</div>

<!-- Modal -->
<div class="modal fade" id="basicModal-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
		<!-- remote content here : /post/id/preview -->
    </div>
  </div>
</div>
		
@endforeach

</div>

<!-- for close bootstrap modal by back button -->
<script type="text/javascript">
$('div.modal').on('show.bs.modal', function() {
	var modal = this;
	var hash = modal.id;
	window.location.hash = hash;
	window.onhashchange = function() {
		if (!location.hash){
			$(modal).modal('hide');
		}
	}
});

$('div.modal').on('hide', function() {
	var hash = this.id;
	history.pushState('', document.title, window.location.pathname);
});
</script>

<!-- for dynamic share urls by BaiduShare -->
<script type="text/javascript">
        //全局变量，动态的文章ID
        var ShareId = "";
        //绑定所有分享按钮所在A标签的鼠标移入事件，从而获取动态ID
        $(function () {
            $(".bdsharebuttonbox a").mouseover(function () {
                ShareId = $(this).attr("data-id");
            });
        });

        /* 
        * 动态设置百度分享URL的函数,具体参数
        * cmd为分享目标id,此id指的是插件中分析按钮的ID
        *，我们自己的文章ID要通过全局变量获取
        * config为当前设置，返回值为更新后的设置。
        */
        function SetShareUrl(cmd, config) {            
            if (ShareId) {
                config.bdUrl = "http://{{ env("DOMAINNAME") }}/post/" + ShareId;
            }
            return config;
        }

        //插件的配置部分，注意要记得设置onBeforeClick事件，主要用于获取动态的文章ID
        window._bd_share_config = {
            "common": {
                onBeforeClick: SetShareUrl,
    			"bdSnsKey":{},
    			"bdText":"",
    			"bdPic":"",
    			"bdStyle":"1",
    			"bdSize":"24"
            }, "share": {}
        };
        //插件的JS加载部分
        with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];
</script>

<!-- for hide/show weixin share & preview features -->
<script type="text/javascript"> 
	window.onload = function(){ 
		if(!isMobile()){ 
			var previewLabel = document.getElementsByClassName("label-info")
			var i;
			for (i = 0; i < previewLabel.length; i++) {
			    previewLabel[i].setAttribute("style", "display:inline;");
			}
		}
		if(isWeixin()){
			var previewLabel = document.getElementsByClassName("bds_weixin")
			var i;
			for (i = 0; i < previewLabel.length; i++) {
			    previewLabel[i].setAttribute("style", "display:none;");
			}
		}
	}
</script>

@include('footer')

@include('header')

@include('navbar')

@if (!$isadmin)
<script>
	window.location.href = '{{ url("http://".env("DOMAINNAME")) }}'; //using a named route
</script>
@endif

<?php

$id = "";

if (isset($data)) {
	$id = (array_key_exists('id', $data))?$data ['id']:'';
	$title = $data ['title'];
	$description = $data ['description'];
	$tags = (array_key_exists('tags', $data))?$data ['tags']:"";
	$isfeatured = (array_key_exists('isfeatured', $data))?$data ['isfeatured']:0;
	$ispublished = (array_key_exists('ispublished', $data))?$data ['ispublished']:0;
	$isapproved = (array_key_exists('isapproved', $data))?$data ['isapproved']:0;
	$thumbnail = (array_key_exists('thumbnail', $data))?$data ['thumbnail']:$data ['ogimage'];
	$content = $data ['content'];
	// delete the \n of mysql
	$content = str_replace(chr(10)," ",$content);
	$content = str_replace(chr(13)," ",$content);
	$hasimage = (array_key_exists('hasImage', $data))?$data ['hasImage']:false;
	$images = (array_key_exists('images', $data))?$data ['images']:array();
	// get hasvideo from DB
	if (array_key_exists('hasvideo', $data))
		$hasvideo = $data ['hasvideo'];
	// get hasvideo from Fetcher
	else if (array_key_exists('hasVideo', $data))
		$hasvideo = $data ['hasVideo'];
	else $hasvideo = 0;
	//$videos = $data ['videos'];
}

if ($id != "") {
	$action = "/admin/update";
} else if ( isset($url) && $url != "") {
	$action = "/admin/create";
} else $action = "/admin/fetch";

?>

<div class="container">
	<div class="panel panel-default">
	<div class="panel-body">

	<form action="{{ $action }}" name="myform" method="POST">
	
		<input type="submit" class="btn btn-primary">
	
		<input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">

@if ($action == "/admin/fetch")
		
		<div class="form-group"> </div>
		<div class="form-group">
			<label>Post Original Url</label> <input type="input" class="form-control"
					name="url" id="url" placeholder="Please Input The Url" value="">
		</div>
		
@else
	
	<button type="button" class="btn btn-danger" onClick="window.location.href='/admin/delete/{{ $id }}'">Delete</button>

	@if ($action == "/admin/update")
		<button type="button" class="btn btn-info" onClick="window.location.href='/admin/fetch?url={{ $url }}'">ReFetch Url</button>
		<span style="float:right;">
					@if ($isapproved == 0)
					<a href='/list?isapproved=0'><span class="label label-default">NotApprove</span></a>
					@else
					<a href='/list?isapproved=1'><span class="label label-warning">Approved</span></a>
					@endif
					@if ($ispublished == 0)
					<a href='/list?ispublished=0'><span class="label label-default">NotPublish</span></a>
					@else
					<a href='/list?ispublished=1'><span class="label label-success">Published</span></a>
					@endif
					@if ($isfeatured == 0)
					<a href='/list?isfeatured=0'><span class="label label-default">NotFeature</span></a>
					@else
					<a href='/list?isfeatured=1'><span class="label label-primary">Featured</span></a>
					@endif
					@if ($hasvideo == 0)
					<a href='/list?type=img'><span class="label label-default">NotVideo</span></a>
					@else
					<a href='/list?type=vid'><span class="label label-info">HasVideo</span></a>
					@endif
		</span>
	@endif

		<input type="hidden" name="url" id="url" value="{{ $url }}">
		<input type="hidden" name="id" id="id" value="{{ $id }}">
		
		<div class="form-group"> </div>
		<fieldset disabled>
			<div class="form-group">
				<label>Post Ori Url</label> <input type="input" class="form-control"
					name="disabled-url" id="disabled-url" placeholder="Post Ori Url" value="{{ $url }}">
			</div>
		</fieldset>
		<div class="form-group">
			<label>Post Title</label> <input type="input" class="form-control"
				name="title" id="title" placeholder="Post Title" value="{{ $title }}">
		</div>
		<div class="form-group">
			<label>Post Description</label> <input type="input"
				class="form-control" name="description" id="description" placeholder="Post Description"
				value="{{ $description }}">
		</div>
		
		<div class="form-group">
			<label>Tags</label>
		</div>
		<div class="form-group">
		@foreach (Config::get("weixin.tags") as $tagkey => $tagvalue)
				<?php
					$checked = "";
					if($tags != "" && stripos($tags, $tagkey) !== false) $checked = "checked";
				?>
				<input class="field" id="tags" name="tags[]" type="checkbox" value="{{ $tagkey }}" {{ $checked }}>{{ $tagvalue }} &nbsp;&nbsp;	
		@endforeach
		</div>
		
		<div class="form-group">
			<label>Feature It ?</label>
		</div>
		<div class="form-group">
		<label class="radio-inline"> <input type="radio"
			name="isfeatured" id="isfeatured" value="0" <?php if ($isfeatured == 0) echo "checked"; ?>> No
		</label>
		<label class="radio-inline"> <input type="radio"
			name="isfeatured" id="isfeatured" value="1" <?php if ($isfeatured == 1) echo "checked"; ?>> Yes
		</label>
		</div>
		
		<div class="form-group">
			<label>Has Video ?</label>
		</div>
		<div class="form-group">
		<label class="radio-inline"> <input type="radio"
			name="hasvideo" id="hasvideo" value="0" <?php if ($hasvideo == 0) echo "checked"; ?>> No
		</label>
		<label class="radio-inline"> <input type="radio"
			name="hasvideo" id="hasvideo" value="1" <?php if ($hasvideo == 1) echo "checked"; ?>> Yes
		</label>
		</div>
		
		<div class="form-group">
			<label>Choose OG Image</label>
		</div>
		<div class="form-group">
		<label class="radio-inline"> <input type="radio"
			name="ogimage" id="ogimage" value="{{ $thumbnail }}" checked> <img src="{{ $thumbnail }}" width="160" alt="image" class="img-responsive">
		</label> 
	@if ($hasimage)
		@foreach ($images as $image)		
		<label class="radio-inline"> <input type="radio"
			name="ogimage" id="ogimage" value="{{ $image }}"> <img src="{{ $image }}" width="160" alt="image" class="img-responsive">
		</label>
		@endforeach
	@endif
		</div>
		
		<div class="form-group">
			<label>Edit Content</label>
			<textarea name="editor1" id="editor1" rows="10" cols="80">
                This is a textarea to be replaced with Editor.
            </textarea>
		</div>

@endif

		<input type="submit" class="btn btn-primary">

	</form>
	
	</div>
	</div>
</div>

@if ($action != "/admin/fetch")

	<script src="//cdn.ckeditor.com/4.5.3/standard/ckeditor.js"></script>
	
	<script>
	
		// < > & ' ' , conoverted
		var msg = "{{ $content }}";
		msg = msg.replace(/&lt;/gi, "<");
		msg = msg.replace(/&gt;/gi, ">");
		msg = msg.replace(/&amp;/gi, "&");
		msg = msg.replace(/&quot;/gi, "'");
		msg = msg.replace(/&nbsp;/gi, " ");
		//alert (msg);
	
		// Replace the <textarea id="editor1"> with a CKEditor
		// instance, using default configuration.
		CKEDITOR.replace( 'editor1', {
			// disable tags filter
		    allowedContent: true
		});
		CKEDITOR.instances['editor1'].setData(msg);
	                
	</script>

@endif

@include('footer')

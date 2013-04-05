


<?php
	$account = $this->getData('account');
?>

<div class="container-fluid">
	<div id="photoContext" class="row-fluid well">	</div>
	<script id="photos" type="text/template">

			<div class="span12">
			<div><span class="pull-left"><a id="prevMo"href="#">Prev</a></span><span class="pull-right"><a id="nextMo" href="#">Next</a></span></div>
				<h3>Photos for {{= navTime.format('MMMM YYYY') }}</h3>
				<ul class="media-list">
					{{ _(photos).each(function(photo) { }}
					<li class="media">
						<a style="width: 130px;" class="pull-left" id="{{= photo.get('id') }}" href="#">
							<img class="media-object" src="{{= photo.get('resource').picture }}" />
						</a>
						<div class="media-body">
							<h3>{{= photo.get('group') }}: {{= moment(photo.get('resource').created_time).format('LL') }}</h3>
							<p><strong>Caption:</strong> {{= photo.get('resource').name }}
							<a href="{{= photo.get('resource').link }}">Link</a>
							</p>
						</div>
					</li>
					{{ }); }}
				</ul>
			</div>

	</script>

	<script id="photoForm" type="text/template">
		<div class="form-horizontal" id="postEvent">
		<fieldset class="span12">
			<legend>Photo</legend>
			<div class="control-group">
				<label class="control-label">Title</label>
				<div class="controls">
					<input type="text" value="{{= title }}"class="span12" id="photoTitle" name="photoTitle" placeholder="Enter a title for your photo" >
				</div>
			</div>
			<div class="form-actions">
				<span class="pull-right"><button id="close" class="btn">Cancel</button> <button class="btn btn-primary" id="submit">Save changes</button> </span>
			</div>
		</fieldset>
		</div>
	</script>

</div>

<!-- Modals -->
<div id="dialog"></div>

<!--  -->
<script id="photoDetail" type="text/template">
		<div class="modal" id="photoModal">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">✕</button>
				<h3>Photo Detail</h3>
			</div>
			<div class="modal-body" style="text-align:left;">
				<div class="row-fluid">
					<div class="span10">
						<ul class="details">
							<li><strong>Title:</strong> {{= title }}</li>
						</ul>
						<span class="pull-right">&nbsp;<a class="" href="#">Delete <i class="icon-minus-sign icon-white"></i></a>&nbsp;<a id="{{= id }}" href="#photo/edit/{{= id }}">Edit <i class="icon-edit icon-white"></i></a></span>
					</div>
				</div>
			</div>
		</div>
</script>

<script type="text/javascript">
	// Set globals
	var galId = "<?php echo $account['gallery_id'] ?>";

</script>

<script type="text/javascript" src="/views/templates/page/scripts/gallery.js"></script>


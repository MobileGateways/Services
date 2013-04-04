


<?php
	$account = $this->getData('account');
?>

<div class="container-fluid">
	<div id="photoContext" class="row-fluid well">	</div>
	<script id="photos" type="text/template">

			<div class="span12">
			<div><span class="pull-left"><a id="prevMo"href="#">Prev</a></span><span class="pull-right"><a id="nextMo" href="#">Next</a></span></div>
				<table class="table">
				<thead>
				  <tr>
					<th colspan="5"><h3>Album for {{= navTime.format('MMMM YYYY') }}</h3></th>
				  </tr>
				  <tr>
					<th>Title</th>
					<th>Date</th>
					<th><span class="pull-right"><a class="btn btn-small btn-primary" id="1" href="#photo/add">Add Photo</a></span></th>
				  </tr>
				</thead>
				<tbody>
				{{ _(photos).each(function(photo) { }}
				  <tr>
					<td><a class="photo" id="{{= photo.get('id') }}" href="#"><i class="icon-info-sign icon-white"></i>&nbsp;{{= photo.get('title') }}</a></td>
					<td>{{= moment(photo.get('post').date).format('LL') }}</td>
					<td><span class="pull-right"><a class="" href="#photo/edit/{{= photo.get('id') }}">Edit <i class="icon-edit icon-white"></i></a></span></td>
				  </tr>

				{{ }); }}
				</tbody>
				</table>
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

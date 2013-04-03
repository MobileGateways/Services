<?php
	$account = $this->getData('account');
?>
<div class="container-fluid">
	<div id="feedsContext" class="row-fluid well">	</div>
</div>

<script id="ads" type="text/template">
	<div class="span12">
	<div><span class="pull-left"><a id="prevMo"href="#">Prev</a></span><span class="pull-right"><a id="nextMo" href="#">Next</a></span></div>
		<table class="table">
		<thead>
		  <tr>
			<th colspan="5"><h3>Ads Feeds {{= curMonth	}} - {{= curYear }}</h3></th>
		  </tr>
		  <tr>
			<th>Title</th>
			<th>Content</th>
			<th>Posted</th>
			<th>Expires</th>
			<th><span class="pull-right"><a class="btn btn-small btn-primary" id="1" href="#ad/add">New Ad</a></span></th>
		  </tr>
		</thead>
		<tbody>
		{{ _(ads).each(function(ad) { }}
		  <tr>
			<td><a class="feed" id="{{= ad.get('id') }}" href="#"><i class="icon-info-sign icon-white"></i>&nbsp;{{= ad.get('title') }}</a></td>
			<td>{{= ad.get('content') }}</td>
			<td>{{= moment(ad.get('post_date').date).format('MMM Do') }}</td>
			<td>{{= moment(ad.get('expire_date').date).format('MMM Do') }}</td>
			<td><span class="pull-right"><a class="" href="#ad/edit/{{= ad.get('id') }}">Edit <i class="icon-edit icon-white"></i></a></span></td>
		  </tr>

		{{ }); }}
		</tbody>
		</table>
	</div>
</script>

<script id="postForm" type="text/template">
	<div class="form-horizontal" id="postNews">
	<fieldset class="span12">
		<legend>Ad Post</legend>
		<div class="control-group">
			<label class="control-label">Title</label>
			<div class="controls">
				<input type="text" value="{{= title }}"class="span12" id="postTitle" name="postTitle" placeholder="Enter a title" >
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Content</label>
			<div class="controls">
				<textarea rows="3" class="span12" id="postContent" name="postContent" placeholder="" >{{= content }}</textarea>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="control-group ">
					<label class="control-label">Post Date</label>
					<div class="controls">
						<div id="postDate" class="input-append date form_date" data-date="" data-date-format="dd M yyyy">
						<input type="text" value="{{= moment(post_date.date).format('LL') }}">
						<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
					</div>
				</div>
				<div class="control-group ">
					<label class="control-label">Expire Date</label>
					<div class="controls">
						<div id="expireDate" class="input-append date form_date" data-date="" data-date-format="dd M yyyy">
						<input type="text" value="{{= moment(expire_date.date).format('LL') }}">
						<span class="add-on"><i class="icon-calendar"></i></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="form-actions">
			<span class="pull-right"><button id="close" class="btn">Cancel</button> <button class="btn btn-primary" id="submit">Save changes</button> </span>
		</div>
	</fieldset>
	</div>
</script>

<!-- Modals -->
<div id="dialog"></div>
<script id="postDetail" type="text/template">
	<div class="modal" id="postModal">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">✕</button>
			<h3>Ad Detail</h3>
		</div>
		<div class="modal-body" style="text-align:left;">
			<div class="row-fluid">
				<div class="span10">
					<ul class="details">
						<li><strong>Title:</strong> {{= title }}</li>
						<li><strong>Content:</strong> {{= content }}</li>
						<li><strong>Post Date:</strong> {{= moment(post_date.date).format('LL') }}</li>
						<li><strong>Expire Date:</strong> {{= moment(expire_date.date).format('LL') }}</li>
					</ul>
					<span class="pull-right">&nbsp;<a class="" href="#">Delete <i class="icon-minus-sign icon-white"></i></a>&nbsp;<a id="{{= id }}" href="#event/edit/{{= id }}">Edit <i class="icon-edit icon-white"></i></a></span>
				</div>
			</div>
		</div>
	</div>
</script>


<script type="text/javascript">

	// Set globals
	var chId = "<?php echo $account['ads_id'] ?>";

</script>

<script type="text/javascript" src="/views/templates/page/scripts/ads.js"></script>


<?php
	$account = $this->getData('account');
?>

<div class="container-fluid">
	<div id="eventContext" class="row-fluid well">	</div>
	<script id="events" type="text/template">

			<div class="span12">
			<div><span class="pull-left"><a id="prevMo"href="#">Prev</a></span><span class="pull-right"><a id="nextMo" href="#">Next</a></span></div>
				<table class="table">
				<thead>
				  <tr>
					<th colspan="5"><h3>Calendar of Events</h3></th>
				  </tr>
				  <tr>
					<th>Title</th>
					<th>Date</th>
					<th>Start</th>
					<th>End</th>
					<th><span class="pull-right"><a class="btn btn-small btn-primary" id="1" href="#event/add">Add Event</a></span></th>
				  </tr>
				</thead>
				<tbody>
				{{ _(events).each(function(event) { }}
				  <tr>
					<td><a class="event" id="{{= event.get('id') }}" href="#"><i class="icon-info-sign icon-white"></i>&nbsp;{{= event.get('title') }}</a></td>
					<td>{{= moment(event.get('start_time').date).format('LL') }}</td>
					<td>{{= moment(event.get('start_time').date).format('LT') }}</td>
					<td>{{= moment(event.get('end_time').date).format('LT') }}</td>
					<td><span class="pull-right"><a class="" href="#event/edit/{{= event.get('id') }}">Edit <i class="icon-edit icon-white"></i></a></span></td>
				  </tr>

				{{ }); }}
				</tbody>
				</table>
			</div>

	</script>

	<script id="eventForm" type="text/template">
		<div class="form-horizontal" id="postEvent">
		<fieldset class="span12">
			<legend>Calendar Event</legend>
			<div class="control-group">
				<label class="control-label">Event Title</label>
				<div class="controls">
					<input type="text" value="{{= title }}"class="span12" id="eventTitle" name="eventTitle" placeholder="Enter a title for your event" >
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Description</label>
				<div class="controls">
					<textarea rows="3" class="span12" id="eventDescription" name="eventDescription" placeholder="Enter a description for your event" >{{= description }}</textarea>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="control-group ">
						<label class="control-label">Event Date</label>
						<div class="controls">
							<div id="startDate" class="input-append date form_date" data-date="" data-date-format="dd M yyyy">
							<input type="text" value="{{= moment(start_time.date).format('LL') }}">
							<span class="add-on"><i class="icon-calendar"></i></span>
							</div>
						</div>
					</div>
					<div class="control-group ">
						<label class="control-label">Start Time</label>
						<div class="controls">
							<div id="startTime" class="input-append date form_time" data-date="" data-date-format="HH:ii P">
							<input type="text" value="{{= moment(start_time.date).format('LT') }}">
							<span class="add-on"><i class="icon-time"></i></span>
							</div>
						</div>
					</div>
					<div class="control-group ">
						<label class="control-label">End Time</label>
						<div class="controls">
							<div id="endTime" class="input-append date form_time" data-date="" data-date-format="HH:ii P">
							<input type="text" value="{{= moment(end_time.date).format('LT') }}">
							<span class="add-on"><i class="icon-time"></i></span>
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

</div>

<!-- Modals -->
<div id="dialog"></div>
<script id="eventDetail" type="text/template">
	<div class="modal" id="eventModal">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">✕</button>
			<h3>Event Detail</h3>
		</div>
		<div class="modal-body" style="text-align:center;">
			<div class="row-fluid">
				<div class="span10 offset1">
					<ul>
						<li>{{= title }}</li>
						<li>{{= description }}</li>
						<li>{{= moment(start_time.date).format('LL') }}</li>
						<li>{{= moment(start_time.date).format('LT') }}</li>
						<li>{{= moment(end_time.date).format('LT') }}</li>
					</ul>
					<span class="pull-right">&nbsp;<a class="" href="#">Delete <i class="icon-minus-sign icon-white"></i></a>&nbsp;<a id="{{= id }}" href="#event/edit/{{= id }}">Edit <i class="icon-edit icon-white"></i></a></span>
				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/javascript">
	// Set globals
	var calId = <?php echo $account['calendar_id'] ?>;
	var timeZone = "PDT";
	var curMonth = 3;
	var curYear = 2013;
</script>

<script type="text/javascript" src="/views/templates/page/scripts/calendar.js"></script>

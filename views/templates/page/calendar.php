<div class="container-fluid">
	<div class="row-fluid well" >
		<form class="form-horizontal" id="postEvent" method='post' action=''>
		<fieldset class="span12">
			<legend>Calendar Event</legend>
			<div class="control-group">
				<label class="control-label">Event Title</label>
				<div class="controls">
					<input type="text" class="span12" id="eventTitle" name="eventTitle" placeholder="Enter a title for your event" >
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Description</label>
				<div class="controls">
					<input type="text" class="span12" id="eventDescription" name="eventDescription" placeholder="Enter a description for your event" >
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="control-group ">
						<label class="control-label">Event Date</label>
						<div class="controls">
							<div class="input-append date form_date" id="startDate" data-date="" data-date-format="dd M yyyy">
							<input class="" type="text" value="">
							<span class="add-on"><i class="icon-calendar"></i></span>
							</div>
						</div>
					</div>
					<div class="control-group ">
						<label class="control-label">Start Time</label>
						<div class="controls">
							<div class="input-append date form_time" id="startDate" data-date="" data-date-format="HH:ii P">
							<input class="" type="text" value="">
							<span class="add-on"><i class="icon-time"></i></span>
							</div>
						</div>
					</div>
					<div class="control-group ">
						<label class="control-label">End Time</label>
						<div class="controls">
							<div class="input-append date form_time" id="startDate" data-date="" data-date-format="HH:ii P">
							<input class="" type="text" value="">
							<span class="add-on"><i class="icon-time"></i></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-actions">
				<span class="pull-right"><button class="btn">Cancel</button> <button class="btn btn-primary" type="submit">Save changes</button> </span>
			</div>
		</fieldset>
		</form>
	</div>


	<div class="row-fluid well">
		<div class="span12">
			<table class="table">
			<thead>
			  <tr>
				<th colspan="5">Calendar of Events</th>
			  </tr>
			  <tr>
				<th>Title</th>
				<th>Date</th>
				<th>Start</th>
				<th>End</th>
				<th>&nbsp;</th>
			  </tr>
			</thead>
			<tbody>
			  <tr>
				<td><a href="#"><i class="icon-info-sign icon-white">&nbsp;Horse Clinic</a></td>
				<td>Fri May 2</td>
				<td>10:00 AM</td>
				<td>3:00 PM</td>
				<td><span class="pull-right"><a class="" href="#">Edit <i class="icon-edit icon-white"></i></a>&nbsp;<a class="" href="#">Delete <i class="icon-minus-sign icon-white"></i></a></span></td>
			  </tr>
			  <tr>
				<td><a href="#"><i class="icon-info-sign icon-white">&nbsp;Horse Clinic</a></td>
				<td>Fri May 2</td>
				<td>10:00 AM</td>
				<td>3:00 PM</td>
				<td><span class="pull-right"><a class="" href="#">Edit <i class="icon-edit icon-white"></i></a>&nbsp;<a class="" href="#">Delete <i class="icon-minus-sign icon-white"></i></a></span></td>
			  </tr>
			  <tr>
				<td><a href="#"><i class="icon-info-sign icon-white">&nbsp;Horse Clinic</a></td>
				<td>Fri May 2</td>
				<td>10:00 AM</td>
				<td>3:00 PM</td>
				<td><span class="pull-right"><a class="" href="#">Edit <i class="icon-edit icon-white"></i></a>&nbsp;<a class="" href="#">Delete <i class="icon-minus-sign icon-white"></i></a></span></td>
			  </tr>
			  <tr>
				<td><a href="#"><i class="icon-info-sign icon-white">&nbsp;Horse Clinic</a></td>
				<td>Fri May 2</td>
				<td>10:00 AM</td>
				<td>3:00 PM</td>
				<td><span class="pull-right"><a class="" href="#">Edit <i class="icon-edit icon-white"></i></a>&nbsp;<a class="" href="#">Delete <i class="icon-minus-sign icon-white"></i></a></span></td>
			  </tr>
			  <tr>
				<td><a href="#"><i class="icon-info-sign icon-white">&nbsp;Horse Clinic</a></td>
				<td>Fri May 2</td>
				<td>10:00 AM</td>
				<td>3:00 PM</td>
				<td><span class="pull-right"><a class="" href="#">Edit <i class="icon-edit icon-white"></i></a>&nbsp;<a class="" href="#">Delete <i class="icon-minus-sign icon-white"></i></a></span></td>
			  </tr>

			</tbody>
			</table>
		</div>
	</div>

</div>

<script type="text/javascript">

$('.form_date').datetimepicker({
        //language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });
	$('.form_time').datetimepicker({
        //language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 0,
		startView: 1,
		minView: 0,
		maxView: 1,
		forceParse: 0,
        showMeridian: 1
    });
</script>

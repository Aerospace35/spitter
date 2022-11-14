<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>SLS Launch Countdown</title>

	<!-- Normalize.css -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">

	<!-- Countdown Clock Styles -->
	<link rel="stylesheet" href="/SLS/styles.css">

	<!-- jQuery -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	<!-- jQuery.countdown -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js"></script>

	<!-- MomentJS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.23/moment-timezone-with-data-2012-2022.min.js"></script>

</head>
<body>

	<h1>SLS Countdown Clock</h1>
	<div class="countdown-clock">
		<div class="countdown-container"
			data-countdown
			data-countdown-start="2022-11-16 01:04:00"
			data-countdown-end="2022-11-16 01:05:00"
			data-countdown-timezone="America/New_York"></div>
	</div>
	<script>

		// ID of the element to add the Countdown Clock to
		var $clockElement = $('[data-countdown]');

		$clockElement.each( function() {

			// Read data from HTML attributes to store in vars
			var $clock = $(this),
				// Event STARTING time
				eventStart = $(this).data('countdownStart'),
				// Event ENDING time
				eventEnd = $(this).data('countdownEnd'),
				// Event TIMEZONE
				eventTimeZone = $(this).data('countdownTimezone');

			// CONVERT eventEnd to MOMENT.JS FORMAT to compare with current time later on.
			var eventEndTime = moment.tz(eventEnd, eventTimeZone);

			// Time to COUNTDOWN TO
			var countdownTo = moment.tz(eventStart, eventTimeZone);

			$clock.countdown(countdownTo.toDate(), { elapse: true } ).on('update.countdown', function(event) {
				// Clock Countdown HTML Ouptut
				var output = '<div class="time seconds"><span class="count">%S</span> <span class="label">sec%!S</span></div>';
				// Add number of MINUTES left
				if ( event.offset.totalMinutes > 0 ) {
					output = '<div class="time minutes"><span class="count">%M</span> <span class="label">min%!M</span></div>' + output;
				}
				// Add number of HOURS left
				if ( event.offset.totalHours > 0 ) {
					output = '<div class="time hours"><span class="count">%H</span> <span class="label">hr%!H</span></div>' + output;
				}
				// Add number of DAYS left
				if ( event.offset.totalDays > 0 ) {
					output = '<div class="time days"><span class="count">%-D</span> <span class="label">day%!D</span></div>' + output;
				}

				// Get CURRENT TIME in the event's timezone
				var currentTime = moment().tz(eventTimeZone);

				// If CURRENT TIME is GREATER THAN event ENDING TIME, display...
				if ( currentTime > eventEndTime && event.elapsed ) {
					$(this).html(''
						+ '<p style="color:red">It happened or it did not happen. I do not know, as non-sentient javascript code.</p>'
					).parent().addClass('disabled');
					// Stop the Countdown
					$(this).countdown('stop');
				// If event has ENDED and is still IN PROGRESS, display...
				} else if ( event.elapsed ) {
					$(this).html(''
						+ '<p style="color:green">OH MY GOD IT IS LAUNCHING RNNNNNNN (or not, there is a big chance it scrubbed...)</p>'
					).parent().addClass('disabled');
				// Display COUNTDOWN to event...
				} else {
					$(this).html( event.strftime(output) );
				}

			});

		});

	</script>

</body>
</html>

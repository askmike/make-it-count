<?php 

	require 'db.php';
	
	$t = getTopHour($link);
	extract($t);
	
	dbClose($link);

?><!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">

	<!-- Use the .htaccess and remove these lines to avoid edge case issues.
	More info: h5bp.com/i/378 -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>Make it Count!</title>
	<meta name="description" content="Make it count shows our hourly running improvement using a realtime data visualisation. Runs are tracked via tweets. MAKE IT COUNT!">

	<!-- Mobile viewport optimized: h5bp.com/viewport -->
	<meta name="viewport" content="width=device-width">

	<!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

	<link rel="stylesheet" href="css/style.css">

	<!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

		<!-- All JavaScript at the bottom, except this Modernizr build.
		Modernizr enables HTML5 elements & feature detects for optimal performance.
		Create your own custom Modernizr build: www.modernizr.com/download/ -->
		<script src="js/vendor/modernizr-2.5.3.min.js"></script>
	</head>
	<body>


		<!-- Add your site or application content here -->

		<h1>
			Make it count!
		</h1>
		<p id='header' class='info'>
			Make it count! is all about trying to improve our running, and by our I mean <em>everyone</em>'s running.
			Every hour we need to get better than the hour before!

			<a href="https://twitter.com/share" class="twitter-share-button" data-text="Is this going to be our best hour? http://mikevanrossum.nl/make-it-count/" data-via="mikevanrossum" data-hashtags="makeitcount">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

		</p>
		<div id='main' class='clearfix'>
			<canvas class='sketch' id='live' width='500' height='300'></canvas>
			<div class='info' id='hourScoreContainer'>
				<table class='hourTable' id='hourScore'>
					<caption>
						Stats of this hour
					</caption>
					<tbody>
						<tr>
							<td scope="row">Minutes Remaining</td>
							<td id='timeleft'>loading</td>
						</tr>
						<tr>
							<td scope="row">Total kilometer</td>
							<td id='totalDistance'>loading</td>
						</tr>
						<tr>
							<td scope="row">Number of runners</td>
							<td id='totalRunners'>loading</td>
						</tr>
						<tr>
							<td scope="row">Total followers of runners</td>
							<td id='totalFollowers'>loading</td>
						</tr>
						<tr>
							<td scope="row">Top runner</td>
							<td id='topRunner'>loading</td>
						</tr>
						<tr>
							<td scope="row">Most famous runner</td>
							<td id='famousRunner'>loading</td>
						</tr>
						<tr>
							<td scope="row">Current Score</td>
							<td id='totalScore'>loading</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class='more clearfix'>
			<div class='column'>
				<p>
					This visualization is updated realtime as new data comes in. 
					The bigger the circle, the bigger the run. Circles towards the center have a high tweet reach.
				</p>
				<h2>
					Count me in!
				</h2>
				<p>
					If you use the <a href='http://itunes.apple.com/us/app/nike+-gps/id387771637?mt=8'>Nike+ GPS app</a> to track your running, you can tweet
					those runs. Or:
				</p>
				<ol>
					<li>Run!</li>
					<li>Tweet the results in the same format as on the right!</li>
					<li>Repeat and MAKE IT COUNT!</li>
				</ol>
			</div>
			<div id='tweet' class='column clearfix'>
					<blockquote class="twitter-tweet tw-align-center"><p>I just finished a 5,3 km run <a href="https://twitter.com/search/%2523nikeplus">#nikeplus</a> <a href="https://twitter.com/search/%2523makeitcount">#makeitcount</a></p>&mdash; Mike van Rossum (@mikevanrossum) <a href="https://twitter.com/mikevanrossum/status/200285124020023296" data-datetime="2012-05-09T18:04:29+00:00">May 9, 2012</a></blockquote>
					<script src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
			</div>
		</div>
		<div class='more clearfix'>
			<canvas class='sketch' id='record' width='500' height='300'></canvas>
			<table class='hourTable column'>
				<caption>
					Best hour today
				</caption>
				<tbody>
					<tr>
						<td scope="row">Total KM</td>
						<td><?= number_format(round($distance / 100)) ?> KM</td>
					</tr>
					<tr>
						<td scope="row">Number of runners</td>
						<td><?= number_format($runners) ?></td>
					</tr>
					<tr>
						<td scope="row">Total followers of runners</td>
						<td><?= number_format($followers) ?></td>
					</tr>
					<tr>
						<td scope="row">Top runner</td>
						<td><a href='http://twitter.com/<?= $topRunner ?>/status/<?= $topRunnerTweet ?>'>@<?= $topRunner ?></a></td>
					</tr>
					<tr>
						<td scope="row">Most famous runner</td>
						<td><a href='http://twitter.com/<?= $famRunner ?>/status/<?= $famRunnerTweet ?>'>@<?= $famRunner ?></a></td>
					</tr>
					<tr>
						<td scope="row">Total Score</td>
						<td><?= number_format($score) ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<footer>
			<p>
				Made by <a href='http://mikevanrossum.nl'>Mike van Rossum</a>.<br>
				Powered by <a href='http://twitter.com/'>Twitter</a>, <a href='http://nodejs.org/'>node</a> and <a href='http://socket.io/'>socket.io</a>.
			</p>
			<p class='ita'>
				This website is not part of Nike, this is a fan made :)
			</p>
		</footer>
		
		
		<!-- JavaScript at the bottom for fast page loading: http://developer.yahoo.com/performance/rules.html#js_bottom -->

		<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.7.2.min.js"><\/script>')</script>

		<!-- scripts concatenated and minified via build script -->
		<script src="js/plugins.js"></script>
		<script src="js/main.js"></script>
		<!-- end scripts -->

		<!-- Asynchronous Google Analytics snippet. Change UA-XXXXX-X to be your site's ID.
		mathiasbynens.be/notes/async-analytics-snippet -->
		<script>
		var _gaq=[['_setAccount','UA-19313599-8'],['_trackPageview']];
		(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
			g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
			s.parentNode.insertBefore(g,s)}(document,'script'));
			</script>
		</body>
		</html>

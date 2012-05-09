/* Author: Mike van Rossum*/

$(function(){
	
	// we need canvas to draw stuff
	if(Modernizr.canvas) {
	
		//top tweets
		(function(){

			var record = document.getElementById('record'),
				$record = $(record),
				interval,
				recordTweets = [],
				recordRunning,
				c = record.getContext('2d');

			c.font = 'bolder 30px helvetica';
			c.fillStyle = 'white';
			c.fillText("Let's see the best hour!", 80, record.height /2);

			$record.on('click', function() {

				if(recordRunning !== true) {

					recordRunning = true;

					//run the record
					if(!interval) {

						//if we don't got them yet, get them
						$.getJSON('topHour.php', function(d) {

							var i = d.length;

							addTweets(recordTweets, d);

							//setup here because async js
							interval = setInterval(function() {
								draw(c, recordTweets);
							}, fps / 1000);

						});

					} else {
						//else we got them, just draw
						interval = setInterval(function() {
							draw(c, recordTweets);
						}, fps / 1000);
					}

					log(recordRunning);

				} else {
					//pause the record
					clearInterval(interval);
					interval = true;
					recordRunning = false;
				}
			});

		})();

		var canvas = document.getElementById('live'),
			c = canvas.getContext('2d'),
			w = canvas.width,
			h = canvas.height,
			xC = w / 2,
			yC = h / 2,
			particles = [],
			twoPi = Math.PI * 2;

		c.fillStyle = 'white';
		c.fillText("Loading realtime tweets...", 100, yC);

		//create particle skeleton
		var Particle = function(size, speed, distance) {
			this.fSize = size;
			this.speed = speed;
			this.distance = distance;
			this.a = Math.random() * twoPi;
			this.i = 0;
		}

		Particle.prototype = {
			update: function() {

				this.x = xC + Math.cos(this.a) * this.distance;
				this.y = yC + Math.sin(this.a) * this.distance;

				if(this.i < 50) {
					this.i++;
					this.size = 50 - this.i + this.fSize;
					this.x += (this.size - this.fSize) / 2;
					this.y += (this.size - this.fSize) / 2;
				} else if(this.i === 50){
					this.size = this.fSize;
				}
				this.a += this.speed / 500;
			}
		}

		var	p,
			len,
			twoPi = Math.PI * 2;
		// all the animation
		function draw(c, particles) {

			// clear canvas
			c.fillStyle = "#C8C8C8";
		    c.fillRect(0, 0, canvas.width, canvas.height);

			len = particles.length;

			if(len) {
				c.beginPath();

				while(len--) {
					p = particles[len];
					p.update();

					c.moveTo(p.x, p.y);
					c.arc(p.x, p.y, p.size, 0, twoPi);
				}

				c.closePath();
			    c.fillStyle = 'white';
			    c.fill();
			}
		}

		var fps = 25,
			realtime = setInterval(function() {
			draw(c, particles);
		}, fps / 1000);

		var socket = io.connect('http://mvr.me:1338');
		socket.on('tweet', function(data) {
			updateScore(data.total);

			var tweet = data.tweet;

			if(tweet) {
				// console.log(tweet);

				var size = 3 + tweet.distance / 100,
					speed = (5 + Math.sqrt(tweet.statusus)) / 10,
					diameter = 190 - Math.sqrt(Math.sqrt(tweet.followers)) * 20;

				if(diameter < 5) {
					diameter = 5;
				}

				if(diameter > 200) {
					diameter = 200;
				}

				particles.push(
					new Particle(
						size,
						speed,
						diameter
					)
				);
			}

		});
		socket.on('reset', function(data) {
			particles = [];
			updateScore(data.total);
		});
		socket.on('start', function(data) {
			updateScore(data.total);

			var tweets = data.tweets;

			addTweets(particles, tweets);

		});

		//add Particles based on data into an array
		function addTweets(array, data) {

			if(data) {

				var i = data.length,
					size,
					speed,
					diameter,
					p;

				while(i--) {

					p = data[i];

					//correct different types of tweets
					if(!p.statusus) {
						p.statusus = p.userstatuscount;
						p.followers = p.userfollowercount;
					}

					//map tweet to particle vars
					size = 3 + p.distance / 100;
					speed = (5 + Math.sqrt(p.statusus)) / 10,
					diameter = 190 - Math.sqrt(Math.sqrt(p.followers)) * 20;

					if(diameter < 5) {
						diameter = 5;
					}

					if(diameter > 200) {
						diameter = 200;
						checkSize();
					}

					//push the particle
					array.push(
						new Particle(
							size,
							speed,
							diameter
						)
					);

				}
			}
		}

		//controls for normal tweets
		$(canvas).on('click', function(){

			if(!realtime) {

				realtime = setInterval(function() {
					draw(c, particles);
				}, fps / 1000);

			} else {

				clearInterval(realtime);
				realtime = false;

			}

		});


		//update the realtime scoreboard
		var updateScore = (function(){

			var hourScoreContainer = $('#hourScoreContainer'),
				hourScore = $('#hourScore'),
				distance = $('#totalDistance'),
				runners = $('#totalRunners'),
				followers = $('#totalFollowers'),
				topRunner = $('#topRunner'),
				famousRunner = $('#famousRunner'),
				score = $('#totalScore'),
				timeleft = $('#timeleft');

			function addCommas(n) {
			    var amount = n + '';
			    amount = amount.split("").reverse();

			    var output = '';
			    for ( var i = 0; i <= amount.length-1; i++ ){
			        output = amount[i] + output;
			        if ((i+1) % 3 == 0 && (amount.length-1) !== i)output = ',' + output;
			    }
			    return output;
			}

			// update the table
			function updateScore(d) {
				function twitterLink(name, tweet) {
					return '<a href="http://twitter.com/' + name + '/status/' + tweet + '">@' + name + '</a>';
				}

				log(d);
				
				var dis = d.distance / 100;
				dis = dis.toFixed();
				
				if(d.score) {
					log(dis);
					hourScore.detach();

					distance.text(addCommas(dis) + ' KM');
					runners.text(d.runners);
					followers.text(addCommas(d.followers));
					topRunner.html(twitterLink(d.topRunner, d.topRunnerTweet));
					famousRunner.html(twitterLink(d.famRunner, d.famRunnerTweet));
					score.text(addCommas(d.score.toFixed()));
					timeleft.text(d.timeLeft);

					hourScore.appendTo(hourScoreContainer);	
				}

			}

			return updateScore;

		})();
	
		
	} else {
		
		//canvas not supported
		
		$('<h2/>').text('Sorry dude, your browser can\'t handle the count :/ Try Chrome or Firefox').addClass('message').prependTo('body');
		
	}
	
	
	
});
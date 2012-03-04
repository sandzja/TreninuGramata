var line;
var markers = [];
var map;

function initialize() {
	var mapDiv = document.getElementById('map-canvas');
	map = new google.maps.Map(mapDiv, {
		zoom : 14,
		mapTypeId : google.maps.MapTypeId.ROADMAP
	});

	// Try HTML5 geolocation
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

			map.setCenter(pos);
		}, function() {
			handleNoGeolocation(true);
		});
	} else {
		// Browser doesn't support Geolocation
		handleNoGeolocation(false);
	}

	line = new google.maps.Polyline({
		strokeColor : '#ff0000',
		strokeOpacity : 1.0,
		strokeWeight : 2
	});

	line.setMap(map);

	google.maps.event.addListener(map, 'click', addNewPoint);
	
	google.maps.LatLng.prototype.kmTo = function(a) {
		var e = Math, ra = e.PI / 180;
		var b = this.lat() * ra, c = a.lat() * ra, d = b - c;
		var g = this.lng() * ra - a.lng() * ra;
		var f = 2 * e.asin(e.sqrt(e.pow(e.sin(d / 2), 2) + e.cos(b) * e.cos(c) * e.pow(e.sin(g / 2), 2)));
		return f * 6378.137;
	};

	google.maps.Polyline.prototype.inKm = function(n) {
		var a = this.getPath(n), len = a.getLength(), dist = 0;
		for ( var i = 0; i < len - 1; i++) {
			dist += a.getAt(i).kmTo(a.getAt(i + 1));
		}
		return dist;
	};

}

function handleNoGeolocation(errorFlag) {
	var options = {
		map : map,
		position : new google.maps.LatLng(60, 105)
	};

	map.setCenter(options.position);
}

function addNewPoint(e) {
	var path = line.getPath();
	path.push(e.latLng);

	var marker = new google.maps.Marker({
		position : e.latLng,
		map : map,
		draggable : true
	});
	markers.push(marker);
	$('#trackPoints').append('<input type="hidden" name="trackPoints[]" id="trackPoint-' + (markers.length - 1) + '" value="" />');
	$('#trackPoint-' + (markers.length - 1)).val(marker.getPosition().lat() + ";" + marker.getPosition().lng());
	$('#unit :nth-child(2)').attr('selected', 'selected');
	$('#unit').next().html('m');
	
	google.maps.event.addListener(marker, 'drag', function() {
		drawPolyline(marker);
	});
	$('#lineDistance').val(Math.round(line.inKm() * 1000));
}

function drawPolyline(marker) {
	var r;
	for ( var m = 0, r; r = markers[m]; m++) {
		if (r == marker) {
			var newpos = marker.getPosition();
			break;
		}
	}

	line.getPath().setAt(m, newpos);
	$('#trackPoint-' + m).val(newpos.lat() + ";" + newpos.lng());
	$('#lineDistance').val(Math.round(line.inKm() * 1000));
}

var Workout = {
	getTrainingPlansBySport : function(sport) {
		$.getJSON('/workout/get-training-plans', {
			'sportId' : $(sport).val()
		}, function(trainingPlans) {
			$('#trainingPlanId').html('');
			for ( var id in trainingPlans) {
				$('#trainingPlanId').append('<option value="' + id + '">' + trainingPlans[id] + '</option>');
			}
		});
	},

	getGoalType : function(trainingPlan) {
		$.getJSON('/workout/get-goal-type', {
			'sportId' : $('#sportId').val(),
			'trainingPlanId' : $(trainingPlan).val()
		}, function(types) {
			for ( var i in types) {
				$('#' + types).show();
			}
		});
	},
	
	addParameters: function (element) {
		$('#parameters').show();
		$('.buttonLight').hide();
	},
};

var LiveTracking = {
	map : null,
	line : null,
	points : new Array,
	lastTrackPointTimestamp : null,

	drawMap : function() {
		var mapDiv = document.getElementById('map-canvas');
		this.map = new google.maps.Map(mapDiv, {
			zoom : 14,
			mapTypeId : google.maps.MapTypeId.ROADMAP
		});

		this.map.setCenter(this.getCenterPoint());

		this.line = new google.maps.Polyline({
			strokeColor : '#ff0000',
			strokeOpacity : 1.0,
			strokeWeight : 2
		});
		this.line.setMap(this.map);
		this.line.setPath(this.points);
	},

	trackLive : function(id) {
		setInterval(function() {
			LiveTracking.pollTrackPoints(id);
		}, 10000);
	},

	pollTrackPoints : function(id) {
		$.getJSON('/workout/get-track-points', {
			reportId : id,
			timestamp : LiveTracking.lastTrackPointTimestamp
		}, function(trackPoints) {
			for ( var i in trackPoints) {
				LiveTracking.line.getPath().push(new google.maps.LatLng(trackPoints[i].lat, trackPoints[i].lon));
				LiveTracking.lastTrackPointTimestamp = trackPoints[i].timestamp;
			}
		});
	},

	getCenterPoint : function() {
		if (this.points.length > 0) {
			return this.points[0];
		} else {
			return this.geoLocationCenter();
		}
	},
	
	geoLocationCenter: function () {
		// Try HTML5 geolocation
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
				var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
				LiveTracking.map.setCenter(pos);
			}, function() {
				LiveTracking.map.setCenter(new google.maps.LatLng(10, 10));
			});
		} else {
			LiveTracking.map.setCenter(new google.maps.LatLng(10, 10));
		}
	},

	addPoint : function(lat, lng, timestamp) {
		this.points.push(new google.maps.LatLng(lat, lng));
		this.lastTrackPointTimestamp = timestamp;
	},
	
	loadData: function (id) {
		setInterval(function() {
			$.getJSON('/workout/get-track-data', {
				id: id
			}, function (result) {
				$('#duration').html(result.duration);
				$('#distance').html(result.distance);
				$('#speed').html(result.speed);
				$('#heart').html(result.heart);
				$('#energy').html(result.energy);
			});
		}, 10000);
	}
};

var UserGraph = {
	
	init: function () {
		UserGraph.overallTimeGraph();
		$('#overall-time-link').click(function () {
			UserGraph.overallTimeGraph();
		});
		$('#distance-link').click(function () {
			UserGraph.distanceGraph();
		});
		$('#workouts-link').click(function () {
			UserGraph.workoutsGraph();
		});
	},
		
	overallTimeGraph: function () {
		this.doRequest('overall-time');
	},
	
	distanceGraph: function () {
		this.doRequest('distance');
	},
	
	workoutsGraph: function() {
		this.doRequest('workouts');
	},
	
	doRequest: function(action) {
		$.get('/user/' + action + '-graph', function (result) {
			UserGraph.resetActive();
			$('#' + action + '-link').addClass('active');
			$('#userGraph').html(result);
		});
	},
	
	resetActive: function () {
		$('#overall-time-link').removeClass('active');
		$('#distance-link').removeClass('active');
		$('#workouts-link').removeClass('active');
	}
};

var ShareContent = {
	action: null,
	
	init: function () {
		this.resetForm();
		
		$('#private-share').click(function() {
			$('#share-facebook').removeClass('facebookActive');
			$('#post-facebook').val(0);
			
			$('#share-twitter').removeClass('twitterActive');
			$('#post-twitter').val(0);
		});
		$('#share-social').click(function () {
			$('#share-facebook').addClass('facebookActive');
			$('#post-facebook').val(1);
			
			$('#share-twitter').addClass('twitterActive');
			$('#post-twitter').val(1);
		});
		
		$('#share-facebook').click(function() {
			if ($(this).hasClass('facebookActive')) {
				$(this).removeClass('facebookActive');
				$('#post-facebook').val(0);
			} else {
				$(this).addClass('facebookActive');
				$('#post-facebook').val(1);
			}
		});
		
		$('#share-twitter').click(function() {
			if ($(this).hasClass('twitterActive')) {
				$(this).removeClass('twitterActive');
				$('#post-twitter').val(0);
			} else {
				$(this).addClass('twitterActive');
				$('#post-twitter').val(1);
			}
		});
	},
	
	initFileUpload: function () {
		$('#fileUpload').change(function () {
			$('#fileUploadValue').val($(this).val().replace('C:\\fakepath\\', ''));
		});
		$('#shareContentForm').attr('target', 'photoUploadIframe');
		$('#shareContentForm').attr('action', '/news-feed/add-picture');
		$('#publishButton').unbind();
		$('#publishButton').click(function () {
			$('#shareContentForm').submit();
		});
	},
	
	photo: function () {
		this.getForm('photo');
	},
	
	note: function () {
		this.getForm('note');
	},
	
	workout: function () {
		this.getForm('workout');
	},
	
	getForm: function(action) {
		this.action = action;
		$('.shareAction').removeClass('hidden');
		$('.shareAction').show();
		$('#shareContent').show();
		$.get('/news-feed/show-' + action + '-form', function (result) {
			ShareContent.resetActive();
			$('#share-' + action + '-link').addClass('active');
			$('#shareContent').html(result);
		});
	},
	
	closeForm: function () {
		$('.shareAction').hide();
		$('#shareContent').hide();
		this.resetActive();
	},
	
	submitForm: function() {
		$('.tooltip').val('');
		$.post('/news-feed/add-' + this.action, $('#shareContentForm').serialize(), function (result) {
			ShareContent.closeForm();
			NewsFeed.reloadPosts();
		});
	},
	
	closeAndReload: function () {
		ShareContent.closeForm();
		NewsFeed.reloadPosts();
	},
	
	resetActive: function () {
		$('#share-note-link').removeClass('active');
		$('#share-photo-link').removeClass('active');
		$('#share-workout-link').removeClass('active');
		this.resetForm();
	},
	
	resetForm: function () {
		$('#publishButton').unbind();
		$('#publishButton').click(function () {
			$('#publishButton').unbind();
			ShareContent.submitForm();
		});
		$('#shareContentForm').removeAttr('target');
		$('#shareContentForm').removeAttr('action');
	}
};

var NewsFeed = {
	
	currentPage: 0,
		
	init: function () {
		$('#load-more').click(function () {
			NewsFeed.nextPage();
		});
	},
		
	showComments: function (postId) {
		$.get('/news-feed/show-comments', {
			postId: postId
		}, function (result) {
			$('#commentWrapper-' + postId).html(result);
			
			$('#comment-' + postId).keypress(function(e) {
			    if(e.keyCode == 13) {
			    	$('#comment-' + postId).unbind('keypress');
			    	$('#comment-' + postId).keypress(function () {
			    		return false;
			    	});
			        NewsFeed.addComment(postId);
			        return false;
			    }
			});
		});
	},
	
	addComment: function (postId) {
		if ($('#comment-' + postId).val() != '') {
			$.post('/news-feed/add-comment', $('#addCommentForm-' + postId).serialize(), function (result) {
				NewsFeed.showComments(postId);
			});
		}
	},
	
	reloadPosts: function () {
		$.get('/news-feed/posts', function (result) {
			$('#newsFeedPosts').html(result);
		});
	},
	
	appendPosts: function () {
		$.get('/news-feed/posts', {
			page: this.currentPage,
			type: $('#typeValue').val()
		}, function (result) {
			if (result == '') {
				$('#load-more').text('No more news');
				return;
			}
			$('#newsFeedPosts').append(result);
		});
	},
	
	nextPage: function () {
		this.currentPage++;
		this.appendPosts();
	},
	
	showTrainingPlanContent: function (postId) {
		$('#show-more-link-' + postId).toggleClass('hidden');
		$('#show-more-content-' + postId).toggleClass('hidden');
		$('#show-more-content-' + postId).parent().toggleClass('open');
	}
};

var Click = {
		
	oldValues: new Object (),
	checkElements: null,
	checkElement: null,
	bindElement: null,
	
	check: function (element, elements) {
		if (elements != undefined) {
			this.checkElements = elements;
		}
		if (element != undefined) {
			this.checkElement = element;
		}
		if (this.checkElements == null || this.checkElement == null) {
			return null;
		}
		var hasClass = false;
		$(this.checkElements.join(', ')).each(function () {
			if ($(this).hasClass('tooltip') || $(this).val() == '') {
				hasClass = true;
			}
			$(this).unbind('keyup');
			$(this).keyup(function () {
				Click.check();
			});
			$(this).unbind('change');
			$(this).change(function () {
				Click.check();
			});
		});
		
		if (hasClass) {
			$(this.checkElement).addClass('buttonNoActive').attr('disabled', true);
		} else {
			$(this.checkElement).removeClass('buttonNoActive').attr('disabled', false);
		}
	},
	
	clear: function (element) {
		var element = $(element);
		if (this.oldValues[element.attr('name')] == undefined) {
			this.oldValues[element.attr('name')] = element.val();
		}
		if (element.val() == '' || element.val() == this.oldValues[element.attr('name')]) {
			element.val('');
			element.removeClass('tooltip');
			element.blur(function () {
				Click.restore(this);
			});
		}
		this.check();
	},
	
	restore: function (element) {
		var element = $(element);
		if (element.val() == '') {
			element.val(Click.oldValues[element.attr('name')]);
			element.addClass('tooltip');
		}
		this.check();
	}
};

var Modal = {
	open: function (element) {
		Modal.close();
		$('BODY').append('<div id="overlay"></div>');
		var width = parseInt($(element).width());
		var screenWidth = parseInt($(window).width());
		
		$(element).css('margin-left', (screenWidth - width) / 2);
		
		$(element).css('visibility', 'visible');
		$(element).show(100);
		
		Modal.addEvents();
	},
	
	close: function () {
		$('#modal').children().hide(100);
		$('#overlay').remove();
	},
	
	addEvents: function () {
		$('#overlay').click(function () {
			Modal.close();
		});
		document.onkeyup = function(e) {
			var code;
			if (!e) var e = window.event;
			if (e.keyCode) code = e.keyCode;
			else if (e.which) code = e.which;
			if (code == 27){
				Modal.close();
			}
		}
	}
};

var Goal = {
	showForm: function () {
		$.get('/user/show-goal-form', function (result) {
			$('#goalForm').html(result);
			$('.setGoal').hide();
			
			$('#setGoalButton').click(function () {
				Goal.submitForm();
			});
		});
	},
	
	closeForm: function () {
		$('#goalForm').html('');
		$('.setGoal').show();
	},
	
	submitForm: function () {
		$.post('/user/set-goal', $('#newGoalForm').serialize(), function () {
			Goal.closeForm();
			Goal.reload();
		});
	},
	
	reload: function () {
		$.get('/user/show-goal', function (result) {
			$('#goal').html(result);
		});
	}
};

var Select = {
	init: function() {
		$('select.select').each(function(){
			var title = $(this).attr('title');
			if( $('option:selected', this).val() != ''  ) title = $('option:selected',this).text();
			$(this)
				.css({'z-index':10,'opacity':0,'-khtml-appearance':'none'})
				.change(function(){
					val = $('option:selected',this).text();
					$(this).next().text(val);
					});
		});
	}
};

var Rate = {
	set: function (i) {
		$('#rate').attr('class', 'rate0' + i);
		$('#rateValue').val(i);
	}
};

var MyTrainingsGraph = {
	
	method: 'daily',
	endTime: undefined,
	sportId: undefined,
	
	reload: function (endTime) {
		if (endTime == undefined) {
			endTime = this.endTime;
		} else {
			this.endTime = endTime;
		}
		$.get('/my-trainings/graph-' + this.method, {
			endTime: endTime,
			sportId: this.sportId,
		}, function (result) {
			$('#trainingGraphContent').html(result);
		});
	},
	
	filterSport: function (sport) {
		this.sportId = $(sport).val();
		this.reload();
	},
	
	filterGraph: function (method) {
		this.method = method;
		this.resetActive();
		$('#' + method + '-filter-link').addClass('active');
		this.reload();
	},
	
	resetActive: function () {
		$('#daily-filter-link').removeClass('active');
		$('#weekly-filter-link').removeClass('active');
		$('#monthly-filter-link').removeClass('active');
	},
};

var WorkoutPlan = {
		
	exerciseCount: 1,
	action: null,
	
	init: function () {

		$('#private-share').click(function() {
			$('#share-facebook').removeClass('facebookActive');
			$('#post-facebook').val(0);
			
			$('#share-twitter').removeClass('twitterActive');
			$('#post-twitter').val(0);
		});
		$('#share-social').click(function () {
			$('#share-facebook').addClass('facebookActive');
			$('#post-facebook').val(1);
			
			$('#share-twitter').addClass('twitterActive');
			$('#post-twitter').val(1);
		});
		
		$('#share-facebook').click(function() {
			if ($(this).hasClass('facebookActive')) {
				$(this).removeClass('facebookActive');
				$('#post-facebook').val(0);
			} else {
				$(this).addClass('facebookActive');
				$('#post-facebook').val(1);
			}
		});
		
		$('#share-twitter').click(function() {
			if ($(this).hasClass('twitterActive')) {
				$(this).removeClass('twitterActive');
				$('#post-twitter').val(0);
			} else {
				$(this).addClass('twitterActive');
				$('#post-twitter').val(1);
			}
		});
		
		$('#submitButton').click(function () {
			$('#submitButton').unbind();
			WorkoutPlan.submitForm();
		});

		/* TrainingPlanSet pogas click */
		$('#submitButtonSet').click(function () {
			$('#submitButtonSet').unbind();
			WorkoutPlan.submitFormSet();
	 	});		

	
		/* TrainingPlanSet select box izmainas*/
		$('#setSetsId').live('change', function () {
			var val= $("#setSetsId :selected").val();
			if (val != 0)
        		$('#submitButtonSet').removeClass('buttonNoActive').attr('disabled', false);
        	else   
        		$('#submitButtonSet').addClass('buttonNoActive').attr('disabled', true);
	  	});	

	},

	/* SV training plan browse funcionality */
	initNewsFeed: function (nr,max) {
		self = this;
		self.currentWorkoutPlan = nr;
		self.maxWorkoutPlan = max;

		self.initWorkoutPlanNav(nr);

		// turp atpakal pogu aktivizesana
		$('#nw_right').live('click',function () {
			if (self.currentWorkoutPlan<self.maxWorkoutPlan) {
	  			$('#nw_right').attr("src","/gfx/loader2018.gif");
	  			WorkoutPlan.nextWorkoutPlan(self.currentWorkoutPlan+1);
	  		}
	 	});	
		$('#nw_left').live('click',function () {
			if (self.currentWorkoutPlan>1) {
	  			$('#nw_left').attr("src","/gfx/loader2018.gif");
				WorkoutPlan.nextWorkoutPlan(self.currentWorkoutPlan-1);
			}
	 	});	
	},

	initWorkoutPlanNav: function (nr) {
		$('#nw_right').attr("src","/gfx/arrow_right_active.png");
		$('#nw_left').attr("src","/gfx/arrow_left_active.png");

		if (nr == 1) $('#nw_left').attr("src","/gfx/arrow_left.png");
		if (nr == self.maxWorkoutPlan) $('#nw_right').attr("src","/gfx/arrow_right.png");
	},

	nextWorkoutPlan: function (nr) {
		self = this;
		//$.post('/news-feed/next-workout', {nr: nr}, function (result) {
		//	self.currentWorkoutPlan = nr;
		//	$('#workout_next').html(result);
		//	self.initWorkoutPlanNav(nr);
		//}); 

		$.getJSON("/news-feed/next-workout", { nr: nr }, function(json) {
			self.currentWorkoutPlan = nr;
			
			$('#nw_workout_name').html(json.workout_name);
			$('#nw_workout_execution_order').html(json.workout_execution_order);
			$('#nw_workout_days_between').html(json.workout_days_between);
			$('#nw_workout_text').html(json.workout_text);
			$('#nw_workout_graph').html(json.workout_graph);
			
			self.initWorkoutPlanNav(nr);
		});
	},

	changeType: function (element) {
		$(element).parent().parent().next().toggleClass('hidden');
		$(element).parent().parent().next().next().toggleClass('hidden');
		Select.init();
	},
	
	addExercise: function () {
		this.exerciseCount++;
		var newHtml = $('#sample-exercise').clone();
		
		newHtml.find('\\:css3-container').remove(); // PIE fix for IE
		
		newHtml.html(newHtml.html().replace('text tooltip', 'text tooltip shortNote'));
		newHtml.html(newHtml.html().replace('exercise-type-distance-1', 'exercise-type-distance-' + this.exerciseCount));
		newHtml.html(newHtml.html().replace('exercise-type-time-1', 'exercise-type-time-' + this.exerciseCount));
		$('#exercise-data-' + (this.exerciseCount - 1)).after('<div id="exercise-data-' + this.exerciseCount +'">' + newHtml.html() + '</div>');
		Select.init();
		$(".numeric").numeric({ decimal: false, negative: false });
		Click.check('#submitButton', ['#workoutPlanName', 'FORM .required']);
	},
	
	removeExercise: function (element) {
		this.exerciseCount--;
		$(element).parent().parent().remove();
		Click.check('#submitButton', ['.shortNote', '#workoutPlanName']);
	},
	
	submitForm: function () {
		$('.tooltip').val('');
		$.post('/news-feed/add-training-plan', $('#add-training-plan-form').serialize(), function (result) {
			WorkoutPlan.reloadPosts();
		});
	},
	
	reloadPosts: function () {
		$.get('/workout/posts', function (result) {
			$('#trainingPlanPosts').html(result);
		});
		this.getForm('search');
	},
	
	appendPosts: function () {
		$.get('/workout-plan/posts', {
		}, function (result) {
			$('#trainingPlanPosts').append(result);
		});
	},
	
	search: function () {
		this.getForm('search');
	},
	
	addWorkoutPlan: function () {
		this.exerciseCount = 1;
		this.getForm('add-workout-plan');
	},
	
	getForm: function(action) {
		this.action = action;
		$('.shareAction').removeClass('hidden');
		$('.shareAction').show();
		$('#shareContent').show();
		$.get('/workout/show-' + action + '-form', function (result) {
			WorkoutPlan.resetActive();
			$('#' + action + '-link').addClass('active');
			$('#shareContent').html(result);
			Select.init();
		});
	},
	
	closeForm: function () {
		$('#add-training-plan-form').html('');
	},
	
	resetActive: function () {
		$('#search-link').removeClass('active');
		$('#add-workout-plan-link').removeClass('active');
		/*  deaktivizejam jauno sadalu */
		$('#add-workout-plan-set-link').removeClass('active');
	},
	
	addToMyPlans: function(trainingPlanId, element) {
		$.get('/workout/add-to-my-plans', {
			id: trainingPlanId
		}, function () {
			$(element).parent().append('<i>Added to my plans</i>');
			$(element).remove();
		});
	},

	/* aktivizeet TrainingPlanSet sadalu */
	addWorkoutPlanSet: function () {
		this.exerciseCount = 1;
		this.getForm('add-workout-plan-set');
	},	
	
	/* nosuutit TrainingPlanSet datus uz aktivizesanu */	
  	submitFormSet: function () {
		$('.tooltip').val('');
		$.post('/news-feed/add-training-plan-set', $('#add-training-plan-set-form').serialize(), function (result) {
			WorkoutPlan.reloadPostsPlanSet();
		});
	},
	
	/* pecaktivizesanas fiskas */	
	reloadPostsPlanSet: function () {
		$.get('/workout/posts', function (result) {
			$('#trainingPlanPosts').html(result);
		});
		this.getForm('add-workout-plan-set');
	}

};

var Message = {
	send: function (workoutId) {
		$.post('/workout/send-pep-talk', {
			workoutId: workoutId,
			message: $('#message').val()
		}, function () {
			$('#messageSent').remove();
			$('.talk DIV').prepend('<i id="messageSent" class="clear">Your message has been sent!</i>');
			$('#message').val('');
		});
	}
};

var Friend = {
	add: function (id) {
		$.get('/user/add-friend', {
			id: id
		}, function () {
			$('#friend-' + id).html('<a href="javascript:;" onclick="Friend.remove(' + id + ')">Unfollow</a>');
		});
	},
	
	remove: function (id) {
		$.get('/user/remove-friend', {
			id: id
		}, function () {
			$('#friend-' + id).html('<a href="javascript:;" onclick="Friend.add(' + id + ')">Add to my friends</a>');
		});
	}
};

var Pager = {
	currentPage: 0,
		
	init: function (element, url) {
		$('#load-more').click(function () {
			Pager.nextPage(element, url);
		});
	},
	
	appendPosts: function (element, url) {
		$.get(url, {
			page: this.currentPage,
		}, function (result) {
			if (result == '') {
				$('#load-more').text('No more entries');
				return;
			}
			$(element).append(result);
		});
	},
	
	nextPage: function (element, url) {
		this.currentPage++;
		this.appendPosts(element, url);
	},
};

var Facebook = {
		login: function (url) {
			Util.openWindow(url, 400, 300);
		},
	};

	var Twitter = {
		login: function (url) {
			Util.openWindow(url, 800, 450);
		},
	};

var Util = {
		
		loading: function() {
			return '<img style="margin: 50px 225px;" src="/gfx/ajax-loading.gif" />';
			
		},
		
		openWindow: function (url, width, height) {
		    var left = parseInt((screen.availWidth/2) - (width/2));
		    var top = parseInt((screen.availHeight/2) - (height/2));
		    var windowFeatures = "width=" + width + ",height=" + height + ",status,resizable,left=" + left + ",top=" + top + "screenX=" + left + ",screenY=" + top;
		    myWindow = window.open(url, "subWind", windowFeatures);
		    return myWindow;
		}
	};

$(document).ready(function() {
	Select.init();
	UserGraph.init();
	
	$('#userAction').hover(function() {
		$('#userMenu').slideDown(200);
	}, function () {
		$('#userMenu').hide();
	});
	
	$(".numeric").numeric({ decimal: false, negative: false });
});

// funkcija, kas panem # no href, izmanto lai parslegtu lapu
gup = function() {
	var regexS = "([\\#][^]*)";
	var regex = new RegExp(regexS);
	var results = regex.exec( window.location.href );
	if(results == null) return "";
		else return results[0].replace("#","");
} 
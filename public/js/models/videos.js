////////////

///////////// Model/Collection
        CuepointModel = Backbone.Model.extend({
            defaults: {
                time: 0
            },

           	url: 'cuepoints',
	 
            initialize: function () {
// this.save();
                var feedback = this.has("feedback") ? this.get("feedback") : new FeedbackCollection();
                
                if (!(feedback instanceof FeedbackCollection)) {
                    feedback = new FeedbackCollection(feedback);
                }
                this.set({"feedback": feedback});
// console.log(this.collection);
// console.log(this.toJSON().feedback.models[0].toJSON());
                this.setTime();
                this.on("change:time", this.setTime);
// console.log(this.toJSON());
            },

            setTime: function () {
                var time = this.collection.formatTime(this.get("time"));
                this.set({"time": time});
                this.collection.setTimecode.call(this);
            },
            
            getTime: function () {
// this.save();
                var time = this.get("time");
                return this.collection.formatTime(time);
            }
        }),

        CuepointsCollection = Backbone.Collection.extend({
            model: CuepointModel,
            comparator: function (model) {
                return model.get("time");
            },
			
			initialize:function(){
				// this.model.save();
			
			},

            findPoint: function (time) {
                time = this.formatTime(time);
                return this.find(function (cue_point) {
                    return cue_point.get("time") === time;
                });
            }
        }),
////////////

        VideoModel = Backbone.Model.extend({ 
            defaults: {
                "fps": 30,
                "duration": 0
            },

            initialize: function () {
console.log(this.toJSON());
                this.setCuepoints();
            },

            setCuepoints: function () {
                var cuepoints = this.has("cuepoints") ? this.get("cuepoints") : new CuepointsCollection(),

                    fps = this.get("fps"),
                    duration  = this.get("duration");
console.log(cuepoints);
                if (!(cuepoints instanceof CuepointsCollection)) {
                    cuepoints = new CuepointsCollection(cuepoints);
console.log(cuepoints);
                }

                // Calculate the nearest exact frame location
                cuepoints.formatTime = function (time) {
                    var seconds = Math.floor(time),
                        fraction = time - seconds,
                        // we can round instead of floor so long as we check that the result is not greater than the duration
                        frames = Math.round(fraction * fps),
                        nearestFrame = (frames / fps),
                        formatted = (seconds + nearestFrame);

                    if (formatted > duration) {
                        return duration;
                    }
                    return formatted;
                };

                cuepoints.setTimecode = function () {
                    var time = this.get("time"),
                        minutes = Math.floor(time / 60),
                        seconds = Math.floor(time - (minutes * 60)),
                        frames = Math.floor((time - Math.floor(time)) * fps),
                        timecode = ("0" + minutes).substr(-2) + ":" + ("0" + seconds).substr(-2) + ":" + ("0" + frames).substr(-2);
                    this.set({"timecode": timecode});
                };

                this.set({"cuepoints": cuepoints});
console.log(this.cuepoints);
            },
            
            findPoint: function (time) {
                time = parseFloat(this.get("time").toFixed(4));
            },
            // finds any cue point within 250 milliseconds of time argument.

            findNear: function (time) {
                var cuepoints = this.get("cuepoints"),
                    min,
                    max;

                time = cuepoints.formatTime(time);
                min = (time - 0.25);
                max = (time + 0.25);

                return cuepoints.find(function (cue_point) {
                    var pointTime = cue_point.get("time");
                    return pointTime >= min && pointTime <= max;
                });
            }
       });

	dashboard.feedbackCollection = function () {
		new FeedbackCollections();
	};

    dashboard.videoModel = function (model) {
        return new VideoModel(model);
    };
   	 console.log(dashboard.videoModel());
	//not used by the controller
    // dashboard.createCuepoint = function (model) {
    //     return new CuepointModel(model);
    // };
}());



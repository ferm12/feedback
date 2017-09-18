(function () {
	"use strict";

    // Globally accessible App
	window.App = {
        Controllers: {},
		Models: {},
		Collections: {},
		Views: {},
        VideoInfo:{},
        Drawing:{},
        Video:{},
        AppStateChanged:false
	};

    // backbone can broadcast custom events anywhere needed it
	window.backboneEvent = _.extend({}, Backbone.Events);

	window.template = function (id) {
		return _.template( $('#' + id).html() );
	};

    window.IS_SAFARI = function() {
        return /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor);
    };
        
})();

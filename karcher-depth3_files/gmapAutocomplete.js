// Map
;(function($, window, document, undefined) {

    var PLUGIN_NAME = 'gmapAutocomplete',
        PLUGIN_VERSION = '0.1',
        DEFAULT_OPTIONS = {
            callback: function() {},
            options: {}
        };
  
    function Plugin(options, element) {
        if (google === undefined) {
            return;
        }
        this.geocoder = new google.maps.Geocoder();
        this.directionsService = new google.maps.DirectionsService();
        this.autocomplete = '';
        // plugin
        this.name = PLUGIN_NAME;
        this.version = PLUGIN_VERSION;        
        this.opt = DEFAULT_OPTIONS;
        // element
        this.element = element;
        this.id = $(this.element)[0].id,
        // methods
        this.setOptions(options);
        // filter
        this._init();
    }
    
    // use prototyping
    Plugin.prototype = {
        
        // set the options
        setOptions: function(options) {
            this.opt = $.extend(true, {}, this.opt, options);
        },

        // get the options
        getOptions: function() {
            return this.opt;
        },

        // set or get options
        options: function(options) {
            return typeof options === 'object' ? this.setOptions(options) : this.getOptions();
        },
        
        // set or get a single option
        option: function(option, value) {
          if (typeof option === 'string' && this.opt[option]) {
            if (value === undefined) {
              return this.opt[option];
            } else {
              this.opt[option] = value;
            }
          }
        },

        setComponentRestrictions: function(country){
            this.autocomplete.setComponentRestrictions({
                country: country
            });
        },

        // init function to be called first
        _init: function() {

            this.autocomplete = new google.maps.places.Autocomplete(document.getElementById(this.id), this.opt.options);
                        
            if (typeof this.opt.callback === 'function') {

                google.maps.event.addListener(this.autocomplete, 'place_changed', (function() {
                    var place = this.autocomplete.getPlace();
                    this.opt.callback(place);
                }).bind(this));
            
            }

        }
    
    };

    $.widget.bridge(PLUGIN_NAME, Plugin);

}(jQuery, window, document));
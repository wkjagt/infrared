(function( $, undefined ){
    "use strict";
    var pluginGlobals = {}

    var storage = {
        fallback : [],
        init : function(flush) {
            flush == flush || false;

            if(flush) {
                this.fallback = [];
                if(typeof localStorage.infrared != 'undefined') {
                    localStorage.infrared = JSON.stringify([]);
                }
            }
        },
        store : function(data) {

            if(pluginGlobals.use_storage) {
                var stored = this.getStored();
                stored.push(data);
                localStorage.infrared = JSON.stringify(stored);

                if(stored.length >= pluginGlobals.max_storage) {
                    return true;
                }
                return false;
            } else {
                this.fallback.push(data)
                return true;
            }
        },
        flush : function() {
            var stored = this.getStored();
            this.init(true);
            return stored;
        },
        getStored : function() {
            if(this.fallback.length) {
                var stored = this.fallback;
                this.init(true);
                return stored;
            }
            if(typeof localStorage.infrared === 'undefined') {
                return [];
            }
            return JSON.parse(localStorage.infrared);
        }
    };

    var methods = {
        validateOptions : function(options) {
            var required = ['server_endpoint'];

            for(var i in required) {
                if(typeof options[required[i]] == 'undefined') {
                    $.error('infrared error : "'+required[i]+'" missing in options');
                }                
            }

        },
        init : function( options ) {

            methods.validateOptions(options);

            pluginGlobals = {
                use_storage : typeof localStorage == 'object' && typeof JSON == 'object',
                max_storage : options.max_storage || 1,
                server_endpoint : options.server_endpoint,
                startTime : new Date().getTime(),
                totalClicks : 0,
                timeCutoff : options.time_cutoff || false, //ms or false
                clickCutoff : options.click_cutoff || false
            };
            storage.init();

            return $(document).on('mousedown.infrared', methods.report);
        },
        report : function( event ) {

            pluginGlobals.totalClicks++;

            var now = new Date().getTime()
                ,$this = $(this)
                ,full = false
                ,elapsed = now - pluginGlobals.startTime;

            if((pluginGlobals.clickCutoff !== false && pluginGlobals.totalClicks > pluginGlobals.clickCutoff)
                || (pluginGlobals.timeCutoff !== false && elapsed > pluginGlobals.timeCutoff)) {
                return;
            }

            var entry = {
                click: {
                    x : event.pageX,
                    y: event.pageY,
                },
                client: {
                    width: $(window).width()
                },
                page : window.location.href,
                time : now,
                elapsed : elapsed
            };
            full = storage.store(entry);

            if(!full) {
                return;
            }

            $.ajax({
                type: 'POST',
                url: pluginGlobals.server_endpoint,
                data: {
                    clicks: storage.flush()
                },
                dataType: 'json',
                success : function(){}
            });//.abort(); // abort immediately. we only want to send. response is irrelevant
        },
        reset : function( ) {
            pluginGlobals.startTime = new Date().getTime();
            pluginGlobals.totalClicks = 0;
        },
        setpage: function( page ) {
            pluginGlobals.page = page;
        }
    };

    $.fn.infrared = function( arg ) {
        // Method calling logic
        if ( methods[arg] ) {
            return methods[ arg ].apply(this, Array.prototype.slice.call(arguments, 1));
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.infrared');
        }        
    };
})( jQuery );
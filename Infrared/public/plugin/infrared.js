// "use strict";

var Infrared = {
    pluginGlobals : {},

    storage : {
        init : function(flush) {
            flush == flush || false;

            if(flush) {
                localStorage.setItem('infrared', JSON.stringify([]));
            }
        },
        store : function(data) {
            var stored = JSON.parse(this.getStored());
            stored.push(data);
            localStorage.setItem('infrared', JSON.stringify(stored));

            if(stored.length >= Infrared.pluginGlobals.max_storage) {
                return true;
            }
            return false;
        },
        flush : function() {
            var stored = this.getStored();
            this.init(true);
            return stored;
        },
        getStored : function() {
            return localStorage.getItem('infrared') || '[]';
        }
    },
    init : function( options ) {
        if (!window.XMLHttpRequest || !JSON || !localStorage) {
            // we don't support this browser
            return;
        }
        this.pluginGlobals = {
            max_storage : 1,
            server_endpoint : 'http://dev.infraredapp.com',
            startTime : new Date().getTime(),
            totalClicks : 0,
            centered : options.centered || false,
        };
        this.storage.init();

        document.body.className += " infrared_enabled";
        document.body.onmousedown = this.report;
    },
    report : function( event ) {

        Infrared.pluginGlobals.totalClicks++;

        var now = new Date().getTime()
            ,full = false
            ,elapsed = now - Infrared.pluginGlobals.startTime;

        var entry = {
            click: {
                x : Infrared.pluginGlobals.centered ? (event.pageX - (document.body.clientWidth/2)) : event.pageX,
                y: event.pageY,
            },
            elapsed : elapsed,
            centered : Infrared.pluginGlobals.centered,
            page : window.location.pathname
        };

        full = Infrared.storage.store(entry);

        if(!full) {
            return;
        }

        var url = Infrared.pluginGlobals.server_endpoint+'/api/domains/'+window.location.hostname+'/clicks',
            x = new XMLHttpRequest(),
            data = Infrared.storage.flush();

        x.open("POST", url, true);
        x.setRequestHeader('Content-type', 'application/json');
        x.send(data);
    },
    reset : function( ) {
        pluginGlobals.startTime = new Date().getTime();
        pluginGlobals.totalClicks = 0;
    },
    setpage: function( page ) {
        pluginGlobals.page = page;
    }
}



(function($, window){

    // only run on pages that have the infrared extension enabled
    if(!$('body').hasClass('infrared_enabled')) return;

    var infrared = {

        canvas: $('<canvas id="infrared-canvas">'),
        heatmap: null,
        timer: null,
        state: 'stopped',

        init : function(){
            var self = this;

            self.setupCanvas();
            self.startRefreshLoop();
            self.registerListeners();

            self.showIcon();
            $(window).on('resize', function(){self.clear()});
        },
        showIcon : function() {
            chrome.runtime.sendMessage(null, { action : 'show_icon' }, function(response){});            
        },
        setupCanvas : function() {
            $('body').append(this.canvas);
            this.heatmap = createWebGLHeatmap({canvas: this.canvas[0]});
            this.canvas.hide();
        },
        startRefreshLoop : function() {
            var self = this;

            var refreshLoop = function(){
                // update heatmap with new data
                self.heatmap.update();
                self.heatmap.display();

                // make old points slowly disappear
                self.heatmap.multiply(0.95);
                self.heatmap.blur();

                setTimeout(function () {
                    requestAnimationFrame(refreshLoop);
                }, 100);
            };
            requestAnimationFrame(refreshLoop);
        },
        registerListeners : function() {
            var self = this;

            chrome.runtime.onMessage.addListener(function(msg){
                switch(msg.action) {
                    case 'play':
                        self.play(); break;
                    case 'stop':
                        self.state = 'stopped';
                        self.clear(); break;
                }
            });
        },
        play : function() {
            self = this;
            self.canvas.show();
            self.state = 'playing';

            chrome.runtime.sendMessage(null, {
                action : 'get_data',
                page : window.location.pathname,
                domain : window.location.hostname,
            }, function(clicks) {
                self.addToHeatmap(clicks);
            });
        },
        addToHeatmap : function(clicks) {
            var self = this;
            if(this.timer) {
                clearTimeout(this.timer);
                this.timer = 0;
            }
            console.log("received "+clicks.length+" clicks");

            if(clicks.length == 0) return;
            var self = this, i = 0;

            function iterator() {
                var click = clicks[i];

                self.heatmap.addPoint(
                    click['centered'] == 1 ? document.body.clientWidth/2 + parseInt(click['x']) : click['x'], // x coordinate
                    click['y'], // y coordinate
                    40,             // size
                    30/clicks.length             // intensity
                );
                if(++i < clicks.length && self.state == 'playing') {
                    var delay = clicks[i]['elapsed'] - click['elapsed'];
                    this.timer = setTimeout(iterator, delay);
                } else {
                    // clear the heatmap 10 seconds after the last click
                    setTimeout(function(){ self.clear(); }, 10000);
                }
            };

            setTimeout(iterator, clicks[i]['elapsed']);
        },
        clear : function() {
            clearTimeout(this.timer);
            this.timer = 0;
            
            this.heatmap.clear();
            this.canvas.hide();
            chrome.runtime.sendMessage(null, { action : 'show_play_icon' }, function(response){});
        }
    };

    $(window).ready(function(){
        infrared.init(window);
    });
})(jQuery, window);




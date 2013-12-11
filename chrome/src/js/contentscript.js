

(function($, window){

    // only run on pages that have the infrared extension enabled
    if(!$('body').hasClass('infrared_enabled')) return;

    var infrared = {
        canvas: $('<canvas id="infrared-canvas">'),
        heatmap: null,
        timer: null,
        init : function(){

            chrome.runtime.sendMessage(null, { action : 'show_icon' }, function(response){});
            $('body').append(this.canvas);
            this.heatmap = createWebGLHeatmap({canvas: this.canvas[0]});
            var self = this;

            var heatmapLoop = function(){
                // update heatmap with new data
                self.heatmap.update();
                self.heatmap.display();

                // make old points slowly disappear
                self.heatmap.multiply(0.99);
                self.heatmap.blur();

                setTimeout(function () {
                    requestAnimationFrame(heatmapLoop);
                }, 100);
            };
            requestAnimationFrame(heatmapLoop);

            self.registerListeners();
        },
        registerListeners : function() {
            var self = this;

            chrome.runtime.onMessage.addListener(function(msg){
                switch(msg.action) {
                    case 'play':
                        self.play(); break;
                    case 'stop':
                        self.clear(); break;
                }
            });
        },
        play : function() {
            self = this;

            chrome.runtime.sendMessage(null,
                // message
                {
                    action : 'get_data',
                    page : window.location.pathname,
                    domain : window.location.hostname,
                },
                // response callback
                function(clicks) {
                    self.addToHeatmap(clicks);
                }
            );
        },
        addToHeatmap : function(clicks) {
            if(this.timer) {
                clearTimeout(this.timer);
                this.timer = 0;
            }
            console.log("received "+clicks.length+" clicks");

            if(clicks.length == 0) return;
            var self = this, i = 0;

            (function iterator() {
                var click = clicks[i];

                self.heatmap.addPoint(
                    click['x'], // x coordinate
                    click['y'], // y coordinate
                    40,             // size
                    30/clicks.length             // intensity
                );
                if(++i < clicks.length) {
                    var delay = clicks[i]['elapsed'] - click['elapsed'];
                    this.timer = setTimeout(iterator, delay);
                }
            })();
        },
        clear : function() {
            this.heatmap.clear();
        }
    };

    $(window).ready(function(){
        infrared.init(window);
    });
})(jQuery, window);




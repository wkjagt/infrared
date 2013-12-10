

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
            // this.getData();
        },
        getData : function() {
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
    };

    $(window).ready(function(){
        infrared.init(window);
    });
})(jQuery, window);





















// var cake_i18n_showing;






// function HandleDOM_Change () {

//     chrome.runtime.sendMessage(
//         // extensionId
//         null,
//         // message
//         {
//             'action' : 'count',
//             'count': 3
//         },
//         // response callback
//         function(response) {
//             // alert(response)
//         }
//     );
// }

// fireOnDomChange (HandleDOM_Change);

// function fireOnDomChange (actionFunction)
// {
//     $('body').bind ('DOMSubtreeModified', fireOnDelay);
//     function fireOnDelay () {
//         if (typeof this.Timer == "number") {
//             clearTimeout (this.Timer);
//         }
//         this.Timer = setTimeout(function(){fireActionFunction(); }, 500);
//     }
//     function fireActionFunction () {
//         $('body').unbind ('DOMSubtreeModified', fireOnDelay);
//         actionFunction ();
//         $('body').bind ('DOMSubtreeModified', fireOnDelay);
//     }
// }

// chrome.extension.onMessage.addListener(function(request, sender, sendResponse) {
//     switch(request.action) {
//         case 'show':
//             cake_i18n_ext_sources('show');
//             break;
//         case 'hide':
//             cake_i18n_ext_sources('hide');
//             break;
//         case 'get_show':
//             console.log('get_show');
//             sendResponse( {status: cake_i18n_ext_sources('get_show')});


//     }
// });


// function cake_i18n_ext_sources(action)
// {
//     if(action == 'get_show') {
//         return cake_i18n_showing;
//     }

//     if(action == 'show') {
//         if(!$.cookie('cake_i18n_show')) {
//             $.cookie('cake_i18n_show', '1', { expires: 7, path: '/' });
//         }
//         cake_i18n_showing = true;
//     }
//     if(action == 'hide') {
//         $.removeCookie('cake_i18n_show', { path: '/' });
//         cake_i18n_showing = false;

//     }


//     $('cake_i18n').each(function(){
//         if($(this).data('i18ntranslation') == undefined) {
//             $(this).data('i18ntranslation', $(this).html());
//         }
//         switch(action) {
//             case 'show':
//                 $(this).html($(this).data('i18nsource'));
//                 break;
//             case 'hide':
//                 $.removeCookie('cake_i18n_ext_source', { path: '/' });

//                 $(this).html($(this).data('i18ntranslation'));
//         }
//     });
// }
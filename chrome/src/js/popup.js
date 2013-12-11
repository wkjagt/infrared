(function($){
    $('div#actions a').on('click', function(e){
        var a = this;
        chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {
            chrome.tabs.sendMessage(tabs[0].id, { action : $(a).attr('id') });
        });

        e.preventDefault();
    });
})(jQuery);
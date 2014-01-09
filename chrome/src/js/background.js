var pageActionState = 'stopped';

function onMsg(msg, sender, sendResponse) {
    switch(msg.action) {
        case 'get_data':
            // var server = 'http://useinfrared.com',
            var server = localStorage['phonehome'],
                path = '/api/domains/' + msg.domain + '/clicks';

            if(!server) {
                break;
            }

            // @todo : handle errors
            $.ajax({
                url : server + path,
                data : {
                    page : msg.page,
                    apikey : localStorage['apikey']
                },
                success : function(data) {
                    sendResponse(data)
                },
                error : function(a, b, c) {
                    console.log('error');
                },
                // sendResponse becomes invalid when the event listener returns
                // so we need this call to block
                async : false 
            });
            break;
        case 'show_icon':
            chrome.pageAction.show(sender.tab.id);
            sendResponse({});
            break;
        case 'show_play_icon':
            chrome.pageAction.setIcon({
                tabId : sender.tab.id,
                path : {
                    "19" : "img/play-icon-black-19.png",
                    "38" : "img/play-icon-black-38.png"
                }
            });
    }

};

function onPageActionClick(tab) {

    if (pageActionState == 'stopped' ){
        pageActionState = 'playing';
        var action = 'play', icon = 'stop';
    } else {
        pageActionState = 'stopped';
        var action = 'stop', icon = 'play';
    }
    chrome.pageAction.setIcon({
        tabId : tab.id,
        path : {
            "19" : "img/"+icon+"-icon-black-19.png",
            "38" : "img/"+icon+"-icon-black-38.png"
        }
    });
    chrome.tabs.sendMessage(tab.id, { action : action });
}

chrome.runtime.onMessage.addListener(onMsg)
chrome.pageAction.onClicked.addListener(onPageActionClick);

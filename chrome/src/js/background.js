

function onMsg(msg, sender, sendResponse) {
    switch(msg.action) {
        case 'get_data':
            var server = 'http://dev.infraredapp.com',
                path = '/domains/' + msg.domain + '/clicks';

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
    }

};

chrome.runtime.onMessage.addListener(onMsg)


(function($){
    $('div.actions').on('click', function(e){
        e.preventDefault();
    });
})(jQuery);



// $('.toggleswitch').toggleSwitch();


// chrome.extension.sendRequest({'action' : 'popup'}, function(response) {
//     if(response.status) {
//         $('form#show_hide span.switched').removeClass('off');
//         $('form#show_hide input').attr('checked', true);
//     }
// });

// $('form#show_hide div.switch').click(function(){
//     var checkbox = $(this).find('input[type=checkbox]');
//     var actionValue = checkbox.attr('checked') ? 'show' : 'hide';
//     chrome.extension.sendRequest({'action' : 'click', 'value' : actionValue});
// });





// // chrome.extension.getBackgroundPage().console.log();



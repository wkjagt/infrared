// Saves options to localStorage.
function save_options() {
    var input = $('input#apikey');
    var apikey = $('input#apikey').val();
    localStorage['apikey'] = apikey;

    // Update status to let user know options were saved.
    var status = $('#status');
    status.html('Options Saved.');
    setTimeout(function() {
        status.html('')
    }, 750);
}

// Restores select box state to saved value from localStorage.
$(window).ready(function(){
    $('input#apikey').val(localStorage['apikey']);    
});
$('#save').on('click', save_options);
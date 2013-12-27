// Saves options to localStorage.
function save_options(e) {
    e.preventDefault();
    var input = $('input#apikey');
    var apikey = $('input#apikey').val();
    localStorage['apikey'] = apikey;

    // Update status to let user know options were saved.
    var status = $('#save');
    // debugger
    status.html('Saved!');
}

// Restores select box state to saved value from localStorage.
$(window).ready(function(){
    $('input#apikey').val(localStorage['apikey']);    
});
$('#save').on('click', save_options);
// Saves options to localStorage.
function save_options(e) {
    e.preventDefault();
    localStorage['apikey'] = $('input#apikey').val();
    localStorage['phonehome'] = $('input#phonehome').val();
    localStorage['publickey'] = $('input#publickey').val();

    // Update status to let user know options were saved.
    $('#save').html('Saved!');
}

// Restores select box state to saved value from localStorage.
$(window).ready(function(){
    if(localStorage['apikey']) {
        $('input#apikey').val(localStorage['apikey']);        
    }
    if(localStorage['phonehome']) {
        $('input#phonehome').val(localStorage['phonehome']);            
    }
    if(localStorage['publickey']) {
        $('input#publickey').val(localStorage['publickey']);            
    }

});
$('#save').on('click', save_options);
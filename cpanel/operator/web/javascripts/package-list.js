$(document).ready(function(){
    
    // Effects, addressing generically.
    $('.field-rows-columns > li:first-child > input[type=checkbox]').change(function(){
        if(this.checked){
            $(this).parent().parent().css('background', '#dfeff3');
        } else{
            $(this).parent().parent().css('background', 'transparent');
        }
    });
    
    // Form submission
    $('#afx-form-packagelist').submit(function(){
    
        // If no checkbox is selected, we block.
        if($('#' + $(this).attr('id') + ' :checkbox:checked').length <= 0){
            alert('You must select at least one feature list to continue.');
            return false;
        }
    
        return confirm('Confirm create associated PACKAGES and USERS at Aflexi?');
    });

});

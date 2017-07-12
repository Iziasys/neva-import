$(function() {
    $('body').on('change', '.radio-for-isSociety', function(){
        var isSociety = parseInt($('.radio-for-isSociety:checked').val());
        if(isSociety){
            $('.rowForSociety').css({
                display: 'block'
            });
            $('#createClient_societyName').attr('required', true);
        }
        else{
            $('.rowForSociety').css({
                display: 'none'
            });
            $('#createClient_societyName').attr('required', false);
        }
    });

    $('.radio-for-isSociety').trigger('change');
});
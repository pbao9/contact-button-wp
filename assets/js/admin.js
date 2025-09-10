jQuery(document).ready(function($){
    $('.color-picker').wpColorPicker();

    function toggleGradientOptions() {
        if ($('input[name="bcc_gradient_enable"]').is(':checked')) {
            $('.gradient-options').show();
        } else {
            $('.gradient-options').hide();
        }
    }

    toggleGradientOptions();

    $('input[name="bcc_gradient_enable"]').on('change', function(){
        toggleGradientOptions();
    });
});

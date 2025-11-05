jQuery(document).ready(function($) {
    $('#intermission_enabled').on('change', function() {
        var $toggle = $('.intermission-toggle-switch');
        var $label = $('.intermission-mode-label');

        if ($(this).is(':checked')) {
            $toggle.removeClass('live').addClass('maintenance');
            $label.text('Maintenance');
        } else {
            $toggle.removeClass('maintenance').addClass('live');
            $label.text('Live');
        }
    });
});

jQuery(document).ready(function($) {
    var isToggling = false;

    function toggleMaintenance(e) {
        e.preventDefault();
        e.stopPropagation();

        if (isToggling) {
            return false;
        }

        isToggling = true;

        var $dot = $('.intermission-status-dot');
        var $label = $('.intermission-label');

        $.ajax({
            url: intermissionToolbar.ajaxurl,
            type: 'POST',
            data: {
                action: 'intermission_toggle',
                nonce: intermissionToolbar.nonce
            },
            success: function(response) {
                if (response.success) {
                    $label.text(response.data.status_text);

                    if (response.data.enabled) {
                        $dot.removeClass('live').addClass('maintenance');
                    } else {
                        $dot.removeClass('maintenance').addClass('live');
                    }
                }
                isToggling = false;
            },
            error: function(xhr, status, error) {
                console.error('Intermission toggle error:', error);
                alert('Error toggling maintenance mode');
                isToggling = false;
            }
        });

        return false;
    }

    $(document).on('click', '#wp-admin-bar-intermission-toggle', toggleMaintenance);
    $(document).on('click', '#wp-admin-bar-intermission-toggle > a', toggleMaintenance);
    $(document).on('click', '#wp-admin-bar-intermission-toggle .ab-item', toggleMaintenance);
});

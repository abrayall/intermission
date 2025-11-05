jQuery(document).ready(function($) {
    $('#intermission_enabled').on('change', function() {
        var $toggle = $('.intermission-toggle-switch');
        var $label = $('.intermission-mode-label');
        var isChecked = $(this).is(':checked');

        if (isChecked) {
            $toggle.removeClass('live').addClass('maintenance');
            $label.text('Maintenance');
        } else {
            $toggle.removeClass('maintenance').addClass('live');
            $label.text('Live');
        }

        $.ajax({
            url: context.ajaxurl,
            type: 'POST',
            data: {
                action: 'intermission_toggle',
                nonce: context.nonce
            },
            success: function(response) {
                if (response.success) {
                    var modeText = response.data.enabled ? 'Maintenance' : 'Live';
                    $('.intermission-settings .notice').remove();
                    $('.intermission-settings h1').after(
                        '<div class="notice notice-success is-dismissible"><p>The site is now in ' + modeText + ' mode!</p></div>'
                    );

                    var $adminBarDot = $('#wp-admin-bar-intermission-toggle .intermission-status-dot');
                    var $adminBarLabel = $('#wp-admin-bar-intermission-toggle .intermission-label');

                    if (response.data.enabled) {
                        $adminBarDot.removeClass('live').addClass('maintenance');
                        $adminBarLabel.text('Maintenance');
                    } else {
                        $adminBarDot.removeClass('maintenance').addClass('live');
                        $adminBarLabel.text('Live');
                    }
                }
            }
        });
    });

    var $form = $('.intermission-settings form');
    var $previewButton = $('#intermission-preview-button');

    if ($form.length && $previewButton.length) {
        var initialFormState = $form.serialize();

        $form.on('change input', 'input, textarea, select', function() {
            if ($(this).attr('id') === 'intermission_enabled') {
                return;
            }

            var currentFormState = $form.serialize();

            if (currentFormState !== initialFormState) {
                $previewButton.hide();
            } else {
                $previewButton.show();
            }
        });
    }

    var $themeSelect = $('#intermission_theme');
    var $themePreview = $('#intermission-theme-preview');
    var $themeDescription = $('#intermission-theme-description');
    var initialTheme = $themeSelect.val();

    if ($themeSelect.length && $themePreview.length) {
        $themeSelect.on('change', function() {
            var selectedTheme = $(this).val();
            var $selectedOption = $(this).find('option:selected');
            var description = $selectedOption.data('description');

            if ($themeDescription.length && description) {
                $themeDescription.text(description);
            }

            if (selectedTheme !== initialTheme) {
                var previewUrl = window.location.origin + '/intermission?theme=' + encodeURIComponent(selectedTheme);
                $themePreview.attr('href', previewUrl);
                $themePreview.show();
            } else {
                $themePreview.hide();
            }
        });
    }
});

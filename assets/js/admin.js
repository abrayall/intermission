jQuery(document).ready(function($) {
    function switchTab(tab) {
        $('.nav-tab').removeClass('nav-tab-active');
        $('.nav-tab[data-tab="' + tab + '"]').addClass('nav-tab-active');

        $('.intermission-tab-content').hide();
        $('#' + tab).show();
    }

    $('.nav-tab-wrapper .nav-tab').on('click', function(e) {
        e.preventDefault();
        var tab = $(this).data('tab');
        window.location.hash = tab;
        switchTab(tab);
    });

    var hash = window.location.hash.substring(1);
    if (hash && $('#' + hash).length) {
        switchTab(hash);
    } else {
        switchTab('general');
    }

    if (sessionStorage.getItem('intermission_scroll_position')) {
        var scrollPos = sessionStorage.getItem('intermission_scroll_position');
        sessionStorage.removeItem('intermission_scroll_position');
        window.scrollTo(0, parseInt(scrollPos));
    }

    $('.intermission-settings form').on('submit', function(e) {
        sessionStorage.setItem('intermission_scroll_position', window.pageYOffset);

        var enableDate = $('#intermission_auto_enable_date').val();
        var enableTime = $('#intermission_auto_enable_time').val();
        if (enableDate && enableTime) {
            var localEnableDate = new Date(enableDate + 'T' + enableTime);
            var enableGmtTimestamp = Math.floor(localEnableDate.getTime() / 1000);
            $('#intermission_auto_enable_gmt').val(enableGmtTimestamp);
        } else {
            $('#intermission_auto_enable_gmt').val('');
        }

        var disableDate = $('#intermission_countdown_date').val();
        var disableTime = $('#intermission_countdown_time').val();
        if (disableDate && disableTime) {
            var localDisableDate = new Date(disableDate + 'T' + disableTime);
            var disableGmtTimestamp = Math.floor(localDisableDate.getTime() / 1000);
            $('#intermission_countdown_gmt').val(disableGmtTimestamp);
        } else {
            $('#intermission_countdown_gmt').val('');
        }

        var currentHash = window.location.hash;
        if (currentHash) {
            $(this).attr('action', currentHash);
        }
    });

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
                nonce: context.nonce,
                enabled: isChecked ? 1 : 0
            },
            success: function(response) {
                if (response.success) {
                    var modeText = response.data.enabled ? 'Maintenance' : 'Live';
                    var noticeClass = response.data.enabled ? 'notice-warning' : 'notice-success';
                    $('.intermission-settings .notice').remove();
                    $('.intermission-settings h1').after(
                        '<div class="notice ' + noticeClass + ' is-dismissible"><p>The site is now in ' + modeText + ' mode!</p></div>'
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

    var $iconSelect = $('#intermission_icon_type');
    var $customIconUpload = $('#intermission-custom-icon-upload');

    if ($iconSelect.length && $customIconUpload.length) {
        $iconSelect.on('change', function() {
            if ($(this).val() === 'custom') {
                $customIconUpload.slideDown();
            } else {
                $customIconUpload.slideUp();
            }
        });
    }

    var mediaUploader;

    $('#intermission_upload_icon_button').on('click', function(e) {
        e.preventDefault();

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media({
            title: 'Select Custom Icon',
            button: {
                text: 'Use this image'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#intermission_custom_icon_url').val(attachment.url);
            $('#intermission_custom_icon_preview').html('<img src="' + attachment.url + '" style="max-width: 100px; max-height: 100px; border-radius: 8px;">').show();
            $('#intermission_remove_icon_button').show();
        });

        mediaUploader.open();
    });

    $('#intermission_remove_icon_button').on('click', function(e) {
        e.preventDefault();
        $('#intermission_custom_icon_url').val('');
        $('#intermission_custom_icon_preview').hide();
        $(this).hide();
    });
});

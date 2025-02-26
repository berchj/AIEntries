jQuery(document).ready(function ($) {
    $('#ai-entries-form').on('submit', function (e) {
        e.preventDefault();

        var formData = $(this).serialize(); 

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData + '&action=ai_entries_submit',
            success: function (response) {
                $('#response-message').html(response);
            },
            error: function () {
                $('#response-message').html('<p style="color: red;">There was an error processing your request. Please try again.</p>');
            }
        });
    });
});
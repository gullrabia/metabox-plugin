jQuery(document).ready(function ($) {

    $('.form-csv-upload').on('submit', function (event) {
        event.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'csv_ajax_handler');

        $.ajax({
            url: csv_object.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
               if(response.status){
                jQuery("#show_upload_message").text(response.message).css({
                    color: "green"
                });
                jQuery(".form-csv-upload")[0].reset();
               }
            },
            error: function () {
                alert('AJAX Error');
            }
        });
    });

});

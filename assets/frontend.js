jQuery(document).ready(function ($) {
    $('#ccf-contact-form').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize();

        // validate inputs
        if (validate_form()) {
            $.ajax({
                url: ccf_ajax.url,
                type: 'POST',
                data: {
                    action: 'ccf_send_email',
                    nonce: ccf_ajax.nonce,
                    ...Object.fromEntries(new URLSearchParams(formData))
                },
                success: function (response) {
                    if (response.status === 'success') {
                        $('#ccf-message').text(response.message).css('color', 'green');
                        $('#ccf-contact-form')[0].reset();
                    } else {
                        $('#ccf-message').text(response.message).css('color', 'red');
                    }
                }
            });
        }
    });

    function validate_form() {
        let isValid = true;

        $('#ccf-contact-form input, #ccf-contact-form textarea').each(function () {
            const field = $(this);
            const fieldValue = field.val();
            const errorMessage = field.siblings('.error-message');

            // Reset border color and error message
            field.css('border-color', '');
            errorMessage.text('').hide();

            if (!fieldValue) {
                field.css('border-color', 'red');
                errorMessage.text('This field is required.').show();
                isValid = false;
            }

            if (field.attr('type') === 'email') {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(fieldValue)) {
                    field.css('border-color', 'red');
                    errorMessage.text('Please enter a valid email address.').show();
                    isValid = false;
                }
            }
        });

        return isValid;
    }
});

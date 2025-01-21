<?php

function ccf_handle_form_submission()
{
    check_ajax_referer('ccf_nonce', 'nonce');

    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $subject = sanitize_text_field($_POST['subject']);
    $message = sanitize_textarea_field($_POST['message']);
    $email = sanitize_email($_POST['email']);

    $api_token = get_option('ccf_hubspot_api_token');
    $email_recipient = get_option('ccf_email_recipient');

    if (empty($api_token)) {
        ccf_log_email($first_name, $last_name, $subject, $email, $message, 'Error: HubSpot API token is not set');
        $return = ['status' => 'error', 'message' => 'HubSpot API token is not set.'];
        wp_send_json($return);
    } else {
        $hubspot_url = "https://api.hubapi.com/contacts/v1/contact";
        $response = wp_remote_post($hubspot_url, [
            'body' => json_encode([
                'properties' => [
                    ['property' => 'email', 'value' => $email],
                    ['property' => 'firstname', 'value' => $first_name],
                    ['property' => 'lastname', 'value' => $last_name],
                    ['property' => 'message', 'value' => $message],
                ]
            ]),
            'headers' => [
                'Authorization' => 'Bearer ' . $api_token,
                'Content-Type' => 'application/json',
            ],
        ]);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            ccf_log_email($first_name, $last_name, $subject, $email, $message, "Error: $error_message");
            $return = ['status' => 'error', 'message' => 'Error connecting to HubSpot: ' . $error_message];
            wp_send_json($return);
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $response_body_decode = json_decode($response_body);

        if ($status_code === 200 || $status_code === 201) {
            $headers = ['Content-Type: text/plain; charset=UTF-8'];

            if (wp_mail($email_recipient, $subject, $message, $headers)) {
                ccf_log_email($first_name, $last_name, $subject, $email, $message, 'Email Sent; HubSpot customer register;');
                wp_send_json(['status' => 'success', 'message' => 'Email Sent; HubSpot customer register;']);
            } else {
                ccf_log_email($first_name, $last_name, $subject, $email, $message, 'Failed to send email');
                wp_send_json(['status' => 'error', 'message' => 'Failed to send email']);
            }

        } else {
            if (isset($response_body_decode->category) && $response_body_decode->category == 'OBJECT_ALREADY_EXISTS') {
                $existing_id = $response_body_decode->identityProfile->vid;
                $error_message = "A contact with email $email already exists in HubSpot. Existing ID: $existing_id";
                ccf_log_email($first_name, $last_name, $subject, $email, $message, 'HubSpot Error: ' . $error_message);
                wp_send_json(['status' => 'error', 'message' => $error_message]);
            }

            if (isset($response_body_decode->validationResults[0]->message)) {
                $validation_message = $response_body_decode->validationResults[0]->message;
                ccf_log_email($first_name, $last_name, $subject, $email, $message, 'HubSpot Error: ' . $validation_message);
                $return = ['status' => 'error', 'message' => 'HubSpot Error: ' . $validation_message];
                wp_send_json($return);
            } else {
                ccf_log_email($first_name, $last_name, $subject, $email, $message, 'HubSpot API error');
                $return = ['status' => 'error', 'message' => 'HubSpot API error'];
                wp_send_json($return);
            }
        }
    }
}



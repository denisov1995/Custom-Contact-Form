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
        return ccf_send_error('HubSpot API token is not set', $first_name, $last_name, $subject, $email, $message);
    }

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
        return ccf_send_error('Error connecting to HubSpot: ' . $response->get_error_message(), $first_name, $last_name, $subject, $email, $message);
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $response_body_decode = json_decode(wp_remote_retrieve_body($response));

    if ($status_code === 200 || $status_code === 201) {
        return ccf_send_email($email_recipient, $subject, $message, $first_name, $last_name, $email);
    }

    if (isset($response_body_decode->category) && $response_body_decode->category == 'OBJECT_ALREADY_EXISTS') {
        return ccf_send_error("A contact with email $email already exists in HubSpot. Existing ID: " . $response_body_decode->identityProfile->vid, $first_name, $last_name, $subject, $email, $message);
    }

    if (isset($response_body_decode->validationResults[0]->message)) {
        return ccf_send_error('HubSpot Error: ' . $response_body_decode->validationResults[0]->message, $first_name, $last_name, $subject, $email, $message);
    }

    return ccf_send_error('HubSpot API error', $first_name, $last_name, $subject, $email, $message);
}

function ccf_send_error($error_message, $first_name, $last_name, $subject, $email, $message)
{
    ccf_log_email($first_name, $last_name, $subject, $email, $message, $error_message);
    wp_send_json(['status' => 'error', 'message' => $error_message]);
}

function ccf_send_email($email_recipient, $subject, $message, $first_name, $last_name, $email)
{
    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    if (wp_mail($email_recipient, $subject, $message, $headers)) {
        ccf_log_email($first_name, $last_name, $subject, $email, $message, 'Email Sent; HubSpot customer register;');
        wp_send_json(['status' => 'success', 'message' => 'Email Sent; HubSpot customer register;']);
    } else {
        return ccf_send_error('Failed to send email', $first_name, $last_name, $subject, $email, $message);
    }
}




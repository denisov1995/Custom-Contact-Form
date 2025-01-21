<?php

function ccf_log_email($name, $last_name, $subject, $email, $message, $status)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ccf_logs';

    $wpdb->insert($table_name, [
        'first_name' => $name,
        'last_name' => $last_name,
        'subject' => $subject,
        'message' => $message,
        'email' => $email,
        'status' => $status,
    ]);
}

function ccf_create_logs_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ccf_logs';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        first_name VARCHAR(255) NOT NULL,
        last_name VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        email VARCHAR(255) NOT NULL,
        status VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}



function ccf_render_logs_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ccf_logs';
    
    $logs = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");

    echo '<div class="wrap">';
    echo '<h1>Contact Form Logs</h1>';

    if (empty($logs)) {
        echo '<p>No logs available.</p>';
    } else {
        echo '<table class="widefat fixed striped">';
        echo '<thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
              </thead>';
        echo '<tbody>';
        foreach ($logs as $log) {
            echo '<tr>
                    <td>' . esc_html($log->id) . '</td>
                    <td>' . esc_html($log->first_name) . '</td>
                    <td>' . esc_html($log->last_name) . '</td>
                    <td>' . esc_html($log->subject) . '</td>
                    <td>' . esc_html($log->message) . '</td>
                    <td>' . esc_html($log->email) . '</td>
                    <td>' . esc_html($log->status) . '</td>
                  </tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }

    echo '</div>';
}


<?php

function v35_mult($db) {
    try {

        $subscribers = $db->get("
            SELECT
                house_subscriber_id,
                auth_token,
                platform,
                push_token,
                push_token_type,
                voip_token,
                registered,
                last_seen,
                voip_enabled
            FROM
                houses_subscribers_mobile
        ");

        $stmt = $db->prepare("
                INSERT INTO houses_subscribers_devices (
                    house_subscriber_id,
                    device_token,
                    auth_token,
                    platform,
                    push_token,
                    push_token_type,
                    voip_token,
                    registered,
                    last_seen,
                    voip_enabled
                ) VALUES (
                    :house_subscriber_id,
                    'default',  -- setting default value for device_token
                    :auth_token,
                    :platform,
                    :push_token,
                    :push_token_type,
                    :voip_token,
                    :registered,
                    :last_seen,
                    :voip_enabled
                )
            ");

        // Variable to count created rows
        $createdRows = 0;

        foreach ($subscribers as $subscriber) {
            $stmt->execute([
                ':house_subscriber_id' => $subscriber['house_subscriber_id'],
                ':auth_token' => $subscriber['auth_token'],
                ':platform' => $subscriber['platform'],
                ':push_token' => $subscriber['push_token'],
                ':push_token_type' => $subscriber['push_token_type'],
                ':voip_token' => $subscriber['voip_token'],
                ':registered' => $subscriber['registered'],
                ':last_seen' => $subscriber['last_seen'],
                ':voip_enabled' => $subscriber['voip_enabled']
            ]);

            // Increase row count
            $createdRows += $stmt->rowCount();
        }

        echo "created $createdRows rows";
        return true;

    } catch (PDOException $e) {
        // Throw the error up for handling in the calling code
        echo "Error executing query: " . $e->getMessage();
        return false;
    }
}
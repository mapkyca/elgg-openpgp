<?php

/**
 * OpenPGP support.
 * 
 * Provides functions to encrypt email, as well as a message handler for sending email.
 * Users are given an opportunity, via their settings page, to upload their public key. 
 *
 * @licence GNU Public License version 2
 * @link https://github.com/mapkyca/elgg-openpgp
 * @link http://www.marcus-povey.co.uk
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 */
elgg_register_event_handler('init', 'system', function() {

            global $CONFIG;

            // See if the handler is enabled
            $CONFIG->openpgp_enable_handler = true;
            if (elgg_get_plugin_setting('openpgp_enable_handler', 'elgg-openpgp') == 'no')
                $CONFIG->openpgp_enable_handler = false;

            // Override the email handler
            if ($CONFIG->openpgp_enable_handler) {
                register_notification_handler("email", function (ElggEntity $from, ElggUser $to, $subject, $message, array $params = NULL) {

                            global $CONFIG;

                            if ($encrypted = elgg_openpgp_encrypt($message, $to))
                                $message = $encrypted;

                            //elgg_log("OPENPGP: Sending message $message");

                            return email_notify_handler($from, $to, $subject, $message, $params);
                        });
            }
        });

/**
 * Produce an ascii armored content suitable for emailing, or false.
 * @param type $data
 * @param type $user User to send message to
 */
function elgg_openpgp_encrypt($data, $user = null) {

    if (!$user)
        $user = elgg_get_logged_in_user_entity();

    $user_guid = $user->guid;

    $saved_ts = elgg_get_plugin_user_setting('gnupgp_pk_saved', $user_guid, 'elgg-openpgp');
    $imported_ts = elgg_get_plugin_user_setting('gnupgp_pk_imported', $user_guid, 'elgg-openpgp');
    $pk = elgg_get_plugin_user_setting('publickey', $user_guid, 'elgg-openpgp');
    $pk_server_lookup = elgg_get_plugin_user_setting('publickey_server_lookup', $user_guid, 'elgg-openpgp');
    $gpg = elgg_get_plugin_setting('gnupgp', 'elgg-openpgp');
    
    if ((!$pk) && ($pk_server_lookup < time() - 604800))
    {
        elgg_log("Looking up PK for {$user->email} from keyserver...");
        elgg_set_plugin_user_setting('publickey_server_lookup', time(), $user_guid, 'elgg-openpgp');
        $pk = elgg_pgp_keyserver_lookup($user->email);
        if ($pk) elgg_log('Got key!');
    }

    if (!$pk)
        return false;

    // See if we need to import a key
    if ((!$imported_ts) || ($saved_ts > $imported_ts)) {

        $command = "echo " . escapeshellarg($pk) . " | $gpg --import --batch --yes";
        //elgg_log("OPENPGP: Importing new key ". $command);

        $log = "";
        ob_start();
        passthru($command, $log);
        //elgg_log("OPENPGP: Executed command, got ". var_export($log, true) . " \n " . ob_get_clean());

        elgg_set_plugin_user_setting('gnupgp_pk_imported', time(), $user_guid, 'elgg-openpgp');
    }

    $command = "echo " . escapeshellarg($data) . " | $gpg --trust-model always --batch --yes -e -a -r " . escapeshellarg($user->email);
    //elgg_log("OPENPGP: ". $command);

    ob_start();
    $return = "";
    passthru($command, $return);
    $result = ob_get_clean();
    //elgg_log("OPENPGP: Got result ". $result, " with return value $return");

    if ($return == 0) {

        return $result;
    }

    ob_end_clean();

    elgg_log("OPENPGP: " . "GPG returned error: $return", 'WARNING');

    return false;
}

/**
 * Key server lookup
 * @param type $email
 * @return boolean
 */
function elgg_pgp_keyserver_lookup($email) {

    $keyserver = elgg_get_plugin_setting('keyserver', 'elgg-openpgp');
    if (empty($keyserver))
        $keyserver = 'pgp.mit.edu';

    $keyserver = "http://$keyserver:11371/pks/lookup?op=get&search=" . urlencode($email);

    if ($content = file_get_contents($keyserver)) {

        $data = new DOMDocument();
        $data->loadHTML($content);
        $xpath = new DomXpath($data);

        $pre_tags = array();
        foreach ($xpath->query('//pre') as $node) {
            $pre_tags[] = $node->nodeValue;
        }

        return $pre_tags[0];
    }

    return false;
}

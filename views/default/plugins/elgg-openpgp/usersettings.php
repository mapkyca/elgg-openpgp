<?php

$user = elgg_get_logged_in_user_entity();
$user_guid = $user->getGUID();

$publickey = elgg_get_plugin_user_setting('publickey', $user_guid, 'elgg-openpgp');

?>
<div class="section">
    <p><?= elgg_echo('elgg-openpgp:usersettings:publickey');?>:<br />
        <textarea name='params[publickey]'><?= $publickey; ?></textarea>
    </p>
</div>
<input type="hidden" name='params[gnupgp_pk_saved]' value='<?= time(); ?>' />
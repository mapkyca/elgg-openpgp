
<div class="section basic">
    
    <p><?php echo elgg_echo('elgg-openpgp:settings:gpgpath'); ?>:<br />
            <?php echo elgg_view('input/text', array('name' => 'params[gnupgp]', 'value' => $vars['entity']->gnupgp ? $vars['entity']->gnupgp : '/usr/bin/gpg')); ?>
    </p>
    
    <p><?php echo elgg_echo('elgg-openpgp:settings:openpgp_enable_handler'); ?>:<br />
        <select name='params[openpgp_enable_handler]'>
            <option value="yes"><?= elgg_echo('option:yes'); ?></option>
            <option value="no"><?= elgg_echo('option:no'); ?></option>
        </select>
    </p>
</div>

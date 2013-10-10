
<div class="section basic">
    
    <p><?php echo elgg_echo('elgg-openpgp:settings:gpgpath'); ?>:<br />
            <?php echo elgg_view('input/text', array('name' => 'params[gnupgp]', 'value' => $vars['entity']->gnupgp ? $vars['entity']->gnupgp : '/usr/bin/gpg')); ?>
    </p>
    
    <p><?php echo elgg_echo('elgg-openpgp:settings:openpgp_enable_handler'); ?>:<br />
        <select name='params[openpgp_enable_handler]'>
            <option value="yes" <?php echo $vars['entity']->openpgp_enable_handler == 'yes' ? 'selected' : ''; ?>><?= elgg_echo('option:yes'); ?></option>
            <option value="no" <?php echo $vars['entity']->openpgp_enable_handler == 'no' ? 'selected' : ''; ?>><?= elgg_echo('option:no'); ?></option>
        </select>
    </p>
    
    <p><?php echo elgg_echo('elgg-openpgp:settings:keyserver'); ?>:<br />
            <?php echo elgg_view('input/text', array('name' => 'params[keyserver]', 'value' => $vars['entity']->keyserver ? $vars['entity']->keyserver : 'pgp.mit.edu')); ?>
    </p>
</div>

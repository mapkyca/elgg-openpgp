Elgg OpenPGP support
====================

This is a small plugin that adds OpenPGP support (which other plugins can use) and an encrypted email handler that can be used to encrypt outgoing email messages.

As well as securing messages that leave the server, this plugin increases the usage of cryptography, making it more "normal". This, can only ever be a good thing.

Installation
------------

* Install into your elgg /mod directory as elgg-openpgp & activate via the admin panel
* Specify the location of the gnu privacy guard binary (gpg) in the plugin settings, and whether you wish to use this as elgg's email sender (yes is assumed)
* Create a .gnupg directory for your web server in your apache process' home directory (usually /var/www), and chown it to the correct user, e.g.

```
mkdir /var/www/.gnupg; chown www-data:www-data /var/www/.gnupg
```

Note, we assume the server is "trusted", and while we aren't (at the moment) storing any private keys in this keychain, you should probably take extra steps to secure this directory.

If you do have the .gnupg directory in its default directory (which is usually web readable) then I strongly recommend you deny access in your httpd.conf file with something like:
```
<Directorymatch "^/.*/\.gnupg/">
Order deny,allow
Deny from all
</Directorymatch>
```

* Users can then enable encryption of messages sent to them by uploading their public key to Settings -> Configure your tools. As a network operator, you should encourage them to do so.

What this does do
-----------------
* Provides a function you can use in your own plugins to send PGP encrypted messages
* A email notification handler which will check to see if the message recipient has uploaded a public key for their registered email address, and if so uses it to send the email

What this doesn't do
--------------------
* This doesn't provide true end to end encryption - site messages, and the objects stored in the database will still be visible in the clear, and so the site admin can still read your messages. This isn't 'dissident ready'! However email sent from the server will be encrypted only to the recipient (providing their key has been uploaded).

Todo
----
* [] Key server lookup and submission to better handle key exchange
* [] Incoming email decryption & Jettmail support

See
---
 * Author: Marcus Povey <http://www.marcus-povey.co.uk> 
 * GnuPG <http://www.gnupg.org/>

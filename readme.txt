=== HGK SMTP ===
Contributors: ihagaki
Donate link: http://www.ihagaki.com/
Tags: gmail, mail, phpmailer, smtp, wp_mail
Requires at least: 2.8
Tested up to: 2.8.4
Stable tag: 1.1

Reconfigures WordPress email to use secure SMTP such as Gmail. Plugin options are accessible through the admin panel.

== Description ==

HGK SMTP plugin reconfigures wp_mail() function to use SMTP instead of mail(). Particularly, it allows sending outgoing mail via Gmail or Google Apps accounts.

The following options can be set from plugin's admin panel:

* SMTP server address.
* SMTP server port.
* SMTP username and password.
* Whether or not SSL should be used.
* From: email address and Sender's name for outgoing mail.

== Installation ==

1. Download `hgk-smtp.zip`.
1. Uncompress.
1. Upload `hgk-smtp` to your `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Can I use HGK SMTP plugin with Gmail or Google Apps? =

Yes. In fact it was originally developed to work with Gmail.
The following options should get you going:

* SMTP server address: `smtp.gmail.com`
* Use SSL connection: `yes` (checked)
* SMTP server port: `465`
* SMTP username: `your Gmail / GoogleApps email address`
* SMTP password: `your Gmail / GoogleApps password`

= This plugin does not seem to affect my WordPress blog mailing behavior? =

Make sure that no mailing, SMTP or other plugins with similar functionality are active.

= Still this plugin does not seem to affect my WordPress blog mailing behavior? =

HGK SMTP plugin works by modifying behavior of `wm_mail()` function. Unfortinately, some plugins are known to call PHP's `mail()` directly. If the case, you should be able to correct that by searching for instances of `mail()` and replacing them with `wp_mail()`. Since the parameters to the both functions are the same, this simple textual replacement works. 

== Screenshots ==

1. Screenshot of HGK SMTP Options admin panel.

== Support ==

Please feel free to contact us by visiting support page and leaving your feedback:
<http://www.ihagaki.com/wordpress/hgk-smtp-plugin/>

== Changelog ==

= v1.1 2009-09-14 =

* enabling localization

= v1.0 2009-09-09 =

* initial release

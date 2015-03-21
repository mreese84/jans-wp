=== WPBizPlugins Custom Admin Help Boxes ===
Contributors: wpbizplugins
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=MF5MU4TNF3UDU&lc=SE&item_name=WPBizPlugins&item_number=Easy%20Admin%20Quick%20Menu&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: videos in admin, videos in dashboard, embed videos, admin, help box, help boxes, custom help, admin help, simplify wordpress, simple, wpbizplugins, clients, client work, widget, widgets, meta box, meta boxes, instructions, help, dashboard widgets, dashboard widget
Requires at least: 3.7
Tested up to: 4.0
Stable tag: 1.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add your own instructions and help material to the WordPress admin section with Custom Admin Help Boxes. Lets you add custom help boxes to any page, post or custom post type. Simplify the life of your users or clients!

== Description ==

Tired of clients calling you to ask what to think about when creating blog posts? Tired answering the same questions again and again? Want to add your own help videos to the admin dashboard? Ever wish there was an easy way to just add your own instructions and help material to the WordPress help section?

Custom Admin Help Boxes lets you add instructions and help material to the admin section of WordPress, easily. With Custom Admin Help Boxes, you'll get an easy way of guiding your users or clients through adding posts, pages, custom posts and more. Just add a help box to your desired location(s) in the admin section, write your instructions, and publish!

Ideal for working with clients or users who aren't used to WordPress and need a bit of extra help and hand-holding in their daily work with their WordPress website. Can also be used to create Dashboard widgets, so you can give your clients or users instructions or what to do right on login.

* Easily add your own instructions and help material to the add/edit page of any post, page or custom post type
* Embed videos and other help material right on the dashboard
* Create widgets that shows beautifully right on the main dashboard easily
* Add videos, images, and text to your help boxes just like you would to a normal WordPress post or page
* Have your help content display right in a WordPress metabox, or in a popup for longer content
* Add your phone, email and website support page URL, and have your clients or users have easy ways of getting in touch with you right from every help box


**Instructions**

After you've installed and activated the plugin you'll find ways of adding, removing, and editing custom admin help boxes in the menu to your left, as well as configuring the visual appearance of the menu.

**Find more business related WordPress plugins**
You'll find more business related WordPress plugins on [WPBizPlugins.com](http://www.wpbizplugins.com/ "WPBizPlugins.com").

== Installation ==

This section describes how to install the plugin and get it working. Recommended way of installation is by searching for the plugin in the 'Plugins' menu in WordPress. You can also follow the instructions below:

1. Download the zip-file containing the plugin and extract it.
2. Upload the `wpbizplugins-custom-admin-help-boxes` directory to your `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Done!

== Frequently Asked Questions ==

= Using the plugin Admin Post Navigation https://wordpress.org/plugins/post-navigator/, when you add text content to a Admin Help Box in popup form the prev / next links automatically appear in the text of the admin help box popup. Can this be fixed? =
Yes, thanks to Noahj Champion: In short this can be quick fixed via css by adding...

.wpbizplugins-cahb-content #post-navigator-prev,
.wpbizplugins-cahb-content #post-navigator-next {
  display: none;
}

If you need a plugin to easily add custom JS/CSS to your admin, check out "Admin Branding".

= I have a question that's not answered in this FAQ =
Send me an e-mail at <a href="mailto:support@wpbizplugins.com?subject=Easy Admin Quick Menu Help">support@wpbizplugins.com</a> and I'll get back to you as soon as I can.

= I have an idea of a feature I'd like in this plugin. =

Awesome! Shoot me an e-mail at <a href="mailto:support@wpbizplugins.com?subject=Easy Admin Quick Menu Idea">support@wpbizplugins.com</a> and detail your idea, and I'll get back to you as soon as I can.

== Screenshots ==

1. Example of how a custom help box can look. As you can see in this example, you can add your own company logo on the top. You can also easily add extra ways of contacting you directly by adding your email/phone/website support URL. This will automatically create links in every help box, making it ideal for providing additional support contact means right in the admin section of WordPress.
2. Shows the custom help box in the example being edited. Also shows the custom help box in action to the right.
3. The configuration of the Custom Admin Help Boxes plugin. Few but powerful options! Lets you easily add your company logo, and support e-mail/phone/external URL, to automatically appear in every help box you create.
4. The popup mode enabled and in action. Perfect for longer and more comprehensive help content.
== Changelog ==

= 1.0 =
* Plugin is born!

= 1.0.1 =
* Added functionality for using popups to display content if the content is long.

= 1.0.2 =
* Added shortcode support for the content. You can now use shortcodes.
* Made the popup button text editable, plus added a small editable textarea before the popup button that can be used to explain what clicking the button does.

= 1.0.3 =
* Added the possibility of manually typing in custom post types you want the help box to show on, if they for some reason do not appear in the original custom post type list.

= 1.0.4 =
* Fixed annoying bug that made all widgets appear on every custom post type.

= 1.1 =
* Added feature: Allows you to control what capability the user needs to have in order to be able to edit the custom help boxes and see the entry in the menu for them.

= 1.1.1 =
* Made the content pass through wpautop, to make it look as intended with paragraphs etc..

= 1.1.2 =
* Fixed bug that occasionally caused plugin to hijack the custom update/save messages for custom post types.

= 1.2.0 =
* New feature: Turn off automatic adding of <p>-tags. For compability with some other plugins.
* Minifying CSS in admin. Makes it look less crappy in the source.

= 1.2.1 =
* Minor fix.

= 1.2.2 =
* Updated to latest ACF version.

= 1.3.0 =
* Added native support for automatically embedding any video URL (like WordPress does natively), plus automatically make the videos responsive. Yay!
* Most post types should now appear in the selection menu for display, without needing to enter them in the text field
* Major bugfix release

= 1.3.2 =
* A small fix that prevents rare post conflict with other plugins.

== Upgrade Notice ==


== Support and requests ==

Please send an e-mail at <a href="mailto:support@wpbizplugins.com?subject=Custom Admin Help Boxes">support@wpbizplugins.com</a> if you have any questions, feature requests or similarly.


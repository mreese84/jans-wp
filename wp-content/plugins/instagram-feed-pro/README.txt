=== Instagram Feed Pro ===
Contributors: smashballoon
Support Website: http://smashballoon/instagram-feed/
Requires at least: 3.0
Tested up to: 4.0
Stable tag: 1.3
Version: 1.3
License: Non-distributable, Not for resale

Display beautifully clean, customizable, and responsive feeds from multiple Instagram accounts

== Description ==

Display Instagram photos from any non-private Instagram accounts, either in the same single feed or in multiple different ones.

= Features =
* Super **simple to set up**
* Completely **responsive** and mobile ready - layout looks great on any screen size and in any container width
* **Completely customizable** - Customize the width, height, number of photos, number of columns, image size, background color, image spacing, text styling, likes & comments and more!
* Display **multiple Instagram feeds** on the same page or on different pages throughout your site
* Use the built-in **shortcode options** to completely customize each of your Instagram feeds
* Display thumbnail, medium or **full-size photos** from your Instagram feed
* **Infinitely load more** of your Instagram photos with the 'Load More' button
* View photos in a pop-up **lightbox**
* Display photos by User ID or hashtag
* Display photo captions, likes and comments
* Use your own Custom CSS or JavaScript

= Benefits =
* Increase your Instagram followers by displaying your Instagram content on your website
* Save time and increase efficiency by only posting your photos to Instagram and automatically displaying them on your website

== Installation ==

1. Install the Instagram plugin either via the WordPress plugin directory, or by uploading the files to your web server (in the `/wp-content/plugins/` directory).
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to the 'Instagram Feed' settings page to configure your Instagram feed.
4. Use the shortcode `[instagram-feed]` in your page, post or widget to display your photos.
5. You can display multiple Instagram feeds by using shortcode options, for example: `[instagram-feed id=YOUR_USER_ID_HERE cols=3 width=50 widthunit=%]`

== Changelog ==
= 1.3 =
* New: Added an option to disable the pop-up photo lightbox
* New: Added swipe support for the popup lightbox on touch screen devices
* New: Added an setting which allows you to use the plugin with an Ajax powered theme
* New: Added an option to disable the mobile layout
* New: Added a Support tab which contains System Info to help with troubleshooting
* New: Added friendly error messages which display only to WordPress admins
* New: Added validation to the User ID field to prevent usernames being entered instead of IDs
* Tweak: Disabled the hover event on touch screen devices so that tapping the photo once launches the lightbox
* Tweak: Made the Access Token field slightly wider to prevent tokens being copy and pasted incorrectly
* Tweak: Updated the plugin updater/license check script

= 1.2.2 =
* New: Added the ability to add a class to the feed via the shortcode, like so: [instagram-feed class="my-feed"]
* Fix: Fixed an issue with videos not playing on some touch-screen devices
* Fix: Fixed an issue with video sizing on some mobile devices
* Fix: Addressed a few CSS issues which were causing some minor formatting issues on certain themes

= 1.2.1 =
* Fix: Fixed an issue with the width of videos exceeding the lightbox container on smaller screen sizes and mobile devices
* Fix: Fixed an issue with both buttons being hidden when there were no more posts to load, rather than just the 'Load More' button
* Fix: Added a small amount of margin to the top of the buttons to prevent them touching when displayed in narrow columns or on mobile

= 1.2 =
* New: You can now display photos from multiple User IDs or hashtags. Simply separate your IDs or hashtags by commas.
* New: Added an optional header to the feed which contains your profile picture, username and bio. You can activate this on the Customize page.
* New: Specific photos in your feed can now be hidden. A link is displayed in the popup photo lightbox to site admins only which reveals the photos ID. This can then be added to the new 'Hide Photos' section on the plugin's Customize page.
* New: The plugin now includes an 'Auto-detect' option for the Image Resolution setting which will automatically set the correct image resolution based on the size of your feed.
* New: Added the username and profile picture to the popup photo lightbox
* New: Added a 'Share' button to the photo lightbox which allows you to share the photo on various social media platforms
* New: Added an Instagram button to the photo lightbox which allows you to view the photo on Instagram
* New: Added an optional 'Follow on Instagram' button which can be displayed at the bottom of your feed. You can activate this on the Customize page.
* New: Added the ability to use your own custom text for the 'Load More' button
* New: You can now change the color of the text and icons which are displayed when hovering over the photos
* New: Added a loader icon to indicate that the images are loading
* Tweak: Tweaked some CSS to improve spacing and cross-browser consistency
* Tweak: Removed the semi-transparent background color from caption and likes section. can now be added via CSS instead using: #sb_instagram .sbi_info{ background: rgba(255,255,255,0.5); }
* Tweak: Improved the documentation within the plugin settings pages
* Fix: Fixed an issue with some photos not displaying at full size in the popup photo lightbox
* Fix: Added word wrapping to captions so that long sentences or hashtags without spaces to wrap onto the next line

= 1.1 =
* New: Added video support. Videos now play in the lightbox!
* New: Redesigned the photo hover state to use icons and include the date and author name
* New: Added an option to change the color of the hover background
* Tweak: You can now specify the hashtag with or without the # symbol
* Tweak: Tweaked the responsive design and modified the media queries so that the feed switches to 1 or 2 columns on mobile
* Tweak: Added a friendly message if you activate the Pro version of the plugin while the free version is still activated
* Tweak: Added a 'Settings' link to the Plugins page
* Tweak: Added a link to the [setup directions](https://smashballoon.com/instagram-feed/docs/)
* Fix: Replaced the 'on' function with the 'click' function to increase compatibility with themes using older versions of jQuery
* Fix: Fixed an issue with double quotes in photo captions
* Fix: Removed float from the feed container to prevent clearing issues with other widgets

= 1.0.3 =
* Tweak: If you have more than one Instagram feed on a page then the photos in each lightbox slideshow are now grouped by feed
* Tweak: Added an initialize function to the plugin
* Fix: Added a unique class and data attribute to the lightbox to prevent conflicts with other lightboxes on your site
* Fix: Fixed an occasional issue with the 'Sort Photos By' option being undefined

= 1.0.2 =
* Tweak: Added the photo caption as the 'alt' tag of the images
* Fix: Fixed an issue with the caption elipsis link not always working correctly after having clicked the 'Load More' button
* Fix: Changed the double quotes to single quotes on the 'data-options' attribute

= 1.0.1 =
* Fix: Fixed a minor issue with the Custom JavaScript being run before the photos are loaded

= 1.0 =
* Launched the Instagram Feed Pro plugin!
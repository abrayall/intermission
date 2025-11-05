=== Intermission ===
Contributors: brayall
Tags: maintenance mode, coming soon, under construction, landing page, countdown
Requires at least: 5.8
Tested up to: 6.8
Stable tag: 0.3.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A beautiful WordPress maintenance mode plugin with 48+ themes, scheduled maintenance, social media integration, and elegant design.

== Description ==

Intermission is a sophisticated maintenance mode plugin that helps you create beautiful maintenance and coming soon pages for your WordPress site. With 48+ professionally designed themes, scheduled maintenance, and social media integration, you can keep your visitors informed while you work on your site.

**Key Features:**

* **48+ Built-in Themes** - Choose from gradient themes, photo backgrounds, classic paintings (Van Gogh, Hokusai), and solid colors
* **Scheduled Maintenance** - Auto-enable and auto-disable maintenance mode at specific times
* **Countdown Timer** - Display countdown to launch date with automatic mode switching
* **Social Media Links** - Display icons for 10 social platforms
* **Icon Selection** - Choose from 7 built-in icons or upload custom image
* **Admin Bar Toggle** - Quick toggle between Live/Maintenance modes
* **Tabbed Settings** - Organized interface with General, Times, Social, and Advanced tabs
* **IP Whitelisting** - Allow specific IPs to bypass maintenance mode
* **Preview Endpoint** - Test maintenance page at /intermission before enabling
* **Timezone Aware** - All times displayed in your browser's local timezone
* **SEO Friendly** - Returns proper 503 status code with Retry-After header
* **Fully Responsive** - Looks great on all devices

**Theme Categories:**

* Gradient Themes: Purple Gradient, Ocean Blue, Sunset Orange, and more
* Solid Color Themes: Charcoal, Navy Blue, Forest Green, and more
* Photo Themes: Northern Lights, Tropical Island, Misty Mountains, and more
* Classic Paintings: Great Wave, Starry Night, Almond Blossom, Fishing Boats

**Social Media Platforms:**

* Facebook
* Instagram
* X (Twitter)
* TikTok
* YouTube
* LinkedIn
* Snapchat
* Pinterest
* Reddit
* GitHub

== Installation ==

1. Upload the `intermission` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings → Intermission to configure

== Frequently Asked Questions ==

= Can I preview the maintenance page before enabling it? =

Yes! Visit yoursite.com/intermission to preview the maintenance page at any time without enabling maintenance mode.

= How do I schedule maintenance windows? =

Go to Settings → Intermission → Times tab. Set the Start time to automatically enable maintenance mode, and set the End time with countdown. Check "Automatically put site in live mode" to disable maintenance when the countdown expires.

= Can administrators access the site during maintenance? =

Yes, administrators are always able to access the live site even when maintenance mode is enabled.

= How do I allow clients or testers to view the site? =

You can either:
1. Add their IP addresses to the Whitelist IPs field in the Advanced tab
2. Share a secret preview key URL (e.g., yoursite.com/?preview=secretkey)

= Are the times in my local timezone? =

Yes! All times are displayed in your browser's local timezone and automatically converted to GMT for storage.

= What happens when a scheduled time expires? =

The timestamp is automatically cleared from the database to prevent conflicts if you manually toggle maintenance mode later.

== Screenshots ==

1. Settings page with tabbed interface
2. General tab with theme selection
3. Times tab with scheduled maintenance
4. Social media links configuration
5. Admin bar toggle indicator
6. Purple Gradient theme example
7. Starry Night theme example
8. Countdown timer display

== Changelog ==

= 0.3.1 =
* Update README with accurate documentation

= 0.3.0 =
* Add tabbed settings interface (General, Times, Social, Advanced)
* Add scheduled maintenance with auto-enable and auto-disable
* Add social media links for 10 platforms
* Add 48+ themes including gradients, photos, and classic paintings
* Add timezone-aware time handling (GMT storage, local display)
* Add auto-clear for expired timestamps
* Add icon selection with 7 built-in icons plus custom upload
* Add preview endpoint at /intermission
* Improve settings page layout with consistent labels and spacing

= 0.2.0 =
* Add theme preview functionality
* Add custom icon upload
* Improve admin interface

= 0.1.0 =
* Initial release
* Basic maintenance mode functionality
* 4 built-in themes
* Countdown timer
* IP whitelisting
* Preview secret key

== Upgrade Notice ==

= 0.3.0 =
Major update with 48+ themes, scheduled maintenance, social media integration, and tabbed settings interface.

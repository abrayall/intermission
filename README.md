# Intermission - Sophisticated Maintenance Mode

A beautiful WordPress maintenance mode plugin with 48+ themes, scheduled maintenance, social media integration, and elegant design.

## Features

- **48+ Built-in Themes**: Gradient themes, photo backgrounds, classic paintings (Van Gogh, Hokusai), and solid colors
- **Scheduled Maintenance**: Auto-enable and auto-disable maintenance mode at specific times
- **Countdown Timer**: Optional countdown to launch date with automatic mode switching
- **Social Media Links**: Display icons for 10 social platforms (Facebook, Instagram, X, TikTok, YouTube, LinkedIn, Snapchat, Pinterest, Reddit, GitHub)
- **Icon Selection**: Choose from 7 built-in icons or upload custom image
- **Admin Bar Toggle**: Quick toggle between Live/Maintenance modes from admin bar
- **Tabbed Settings**: Organized interface with General, Times, Social, and Advanced tabs
- **IP Whitelisting**: Allow specific IPs to bypass maintenance mode
- **Secret Preview Key**: Share preview links without granting admin access
- **Preview Endpoint**: Test maintenance page at `/intermission` before enabling
- **Admin Bypass**: Administrators always see the live site
- **Timezone Aware**: All times displayed in your browser's local timezone
- **SEO Friendly**: Returns proper 503 status code with Retry-After header
- **Fully Responsive**: Looks great on all devices

## Installation

1. Upload `intermission` folder to `/wp-content/plugins/`
2. Activate the plugin through WordPress admin
3. Go to **Settings → Intermission** to configure

## Configuration

### Mode Toggle

Quick toggle switch between Live and Maintenance modes with visual indicator (green for Live, yellow for Maintenance). Located at the top of settings page and in the WordPress admin bar.

### Themes

Choose from 48+ built-in themes organized by category:

**Gradient Themes**: Purple Gradient, Ocean Blue, Sunset Orange, Lime Green, Electric Blue, Emerald, Turquoise, Crimson, Indigo, Violet, Magenta, Rose Gold, Golden, Amber, Tangerine, Coral, Blossom Pink, Lavender

**Solid Color Themes**: Charcoal, Navy Blue, Forest Green, Burgundy, Maroon, Midnight Purple, Slate, Ruby Red, Sapphire, Olive Green, Teal, Arctic Blue, Sunshine Yellow

**Photo Themes**: Campfire, Caribbean Cruise, Tropical Waves, Northern Lights, Autumn Woods, Lakeside Dock, Coastal Cliffs, Night Sky, Winter Wonderland, Misty Mountains, Coral Reef, Tropical Island, Ocean Wave, Desert Dunes, Western Mountains

**Classic Paintings**: Great Wave (Hokusai), Starry Night (Van Gogh), Almond Blossom (Van Gogh), Fishing Boats (Van Gogh)

### General Settings

- **Theme**: Select from 48+ built-in themes with live preview
- **Icon**: Choose from wrench, gear, tools, clock, rocket, code, shield, or upload custom image
- **Headline**: Main heading on maintenance page
- **Message**: Descriptive message below headline

### Scheduled Times

- **Start**: Automatically enable maintenance mode at a specific date/time (in your local timezone)
- **End**: Countdown timer with optional auto-disable when countdown reaches zero
- Times are stored in GMT but displayed in your browser's timezone
- Past times are automatically cleared

### Social Media

Add links to your social media profiles. Only platforms with URLs will be displayed:
- Facebook
- Instagram
- X (Twitter)
- TikTok
- YouTube
- LinkedIn
- Snapchat
- Pinterest
- Reddit
- GitHub

### Advanced Settings

- **Preview Key**: Create a secret URL parameter to bypass maintenance
  - Example: `?preview=mysecret`
- **Whitelist IPs**: Add IP addresses (one per line) that can access the site

## Usage

### Basic Setup

1. Go to **Settings → Intermission**
2. Choose a theme and customize the headline/message
3. Toggle Mode to "Maintenance" to enable

### Scheduled Maintenance

1. Go to **Settings → Intermission** → **Times** tab
2. Set **Start** time to automatically enable maintenance mode
3. Set **End** time with countdown display
4. Check "Automatically put site in live mode" to disable maintenance when countdown expires
5. Times are in your local timezone and convert to GMT automatically

### Admin Bar Toggle

When logged in as administrator, you'll see a status indicator in the WordPress admin bar:
- **Green dot + "Live"**: Site is live
- **Yellow dot + "Maintenance"**: Maintenance mode is active

Click the indicator to toggle between modes instantly.

### Preview Maintenance Page

Test your maintenance page before enabling:
- Visit `https://yoursite.com/intermission`
- No preview key required for this endpoint

### Bypass with Secret Key

If you set a preview key to `launch2024`, bypass maintenance mode with:
```
https://yoursite.com/?preview=launch2024
```

## Extending with Custom Themes

Create custom themes by adding CSS files to the `themes/` directory with this header format:

```css
/*
Theme Name: My Custom Theme
Theme URI: https://example.com
Description: My custom maintenance theme
Version: 1.0
Author: Your Name
Author URI: https://example.com
*/
```

Override any `.intermission-*` classes to customize the appearance.

Use background images stored in `themes/images/` directory:
```css
body {
    background: url('images/my-image.webp') center/cover no-repeat fixed;
}
```

## Requirements

- WordPress 5.8+
- PHP 7.4+

## Building

Build using [wordsmith](https://github.com/abrayall/wordsmith):

```bash
wordsmith build
```

This creates a ZIP file in `build/` ready for upload to WordPress.

Versions are managed using git tags in the format `v{major}.{minor}.{maintenance}`. The build system automatically generates version information from tags.

## Author

Brayall, LLC

## License

GPL v2 or later

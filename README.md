# Intermission - Sophisticated Maintenance Mode

A beautiful WordPress maintenance mode plugin with theme support and elegant design.

## Features

- **Theme System**: Multiple built-in themes (Default, Purple Gradient, Dark, Ocean)
- **Countdown Timer**: Optional countdown to launch date
- **Admin Bar Toggle**: Quick toggle between Live/Maintenance modes from admin bar
- **IP Whitelisting**: Allow specific IPs to bypass maintenance mode
- **Secret Preview Key**: Share preview links without granting admin access
- **Admin Bypass**: Administrators always see the live site
- **SEO Friendly**: Returns proper 503 status code with Retry-After header
- **Fully Responsive**: Looks great on all devices

## Installation

1. Upload `intermission` folder to `/wp-content/plugins/`
2. Activate the plugin through WordPress admin
3. Go to **Settings → Intermission** to configure

## Configuration

### Mode Toggle

Quick toggle switch between Live and Maintenance modes with visual indicator (green for Live, yellow for Maintenance).

### Theme Selection

Choose from four built-in themes:
- **Default**: Clean minimal white/black theme
- **Purple Gradient**: Purple gradient with glassmorphism effects
- **Dark**: Sleek dark theme with green accents
- **Ocean**: Calming blue ocean gradient

### Basic Settings

- **Headline**: Main heading on maintenance page
- **Message**: Descriptive message below headline
- **Countdown Timer**: Set date and time for automatic countdown

### Access Control

- **Preview Secret Key**: Create a secret URL parameter to bypass maintenance
  - Example: `?preview=mysecret`
- **Whitelist IPs**: Add IP addresses (one per line) that can access the site

## Usage

1. Configure your settings in **Settings → Intermission**
2. Use the Mode toggle to enable maintenance
3. Toggle maintenance mode from the admin bar (visible when logged in)
4. Use secret key or whitelisted IPs for external testing

## Admin Bar Toggle

When logged in as an administrator, you'll see a status indicator in the WordPress admin bar:
- **Green dot + "Live"**: Site is live
- **Yellow dot + "Maintenance"**: Maintenance mode is active

Click the indicator to toggle between modes instantly.

## Preview URL

If you set a secret key to `launch2024`, you can bypass maintenance mode with:
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

## Requirements

- WordPress 5.8+
- PHP 7.4+

## Version Management

Versions are managed using git tags in the format `v{major}.{minor}.{maintenance}`. The build system automatically generates version information from tags.

## Author

Brayall, LLC

## License

GPL v2 or later

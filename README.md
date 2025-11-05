# Intermission - Sophisticated Maintenance Mode

A beautiful WordPress maintenance mode plugin with animated effects, countdown timers, and extensive customization options.

## Features

- **Beautiful Design**: Modern glassmorphism design with gradient backgrounds
- **Animated Particles**: Floating particle effects for visual interest
- **Countdown Timer**: Optional countdown to launch date
- **Color Customization**: Full control over color scheme
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

### Basic Settings

- **Enable Maintenance Mode**: Toggle maintenance mode on/off
- **Headline**: Main heading on maintenance page
- **Message**: Descriptive message below headline
- **Countdown Timer**: Set date and time for automatic countdown

### Color Scheme

Customize four colors:
- **Background Color 1**: Starting gradient color
- **Background Color 2**: Ending gradient color
- **Text Color**: Color for all text elements
- **Accent Color**: Color for progress bar and hover effects

### Access Control

- **Preview Secret Key**: Create a secret URL parameter to bypass maintenance
  - Example: `?preview=mysecret`
- **Whitelist IPs**: Add IP addresses (one per line) that can access the site

## Usage

1. Configure your settings in **Settings → Intermission**
2. Enable maintenance mode
3. Administrators can still access the site normally
4. Use secret key or whitelisted IPs for external testing

## Preview URL

If you set a secret key to `launch2024`, you can bypass maintenance mode with:
```
https://yoursite.com/?preview=launch2024
```

## Requirements

- WordPress 5.8+
- PHP 7.4+

## Author

Brayall, LLC

## License

GPL v2 or later

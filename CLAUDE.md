# Intermission - Development Guide

## Project Overview

Intermission is a WordPress maintenance mode plugin with theme support, admin bar integration, and elegant design.

## Repository Information

- **Repository**: `git@github.com:abrayall/intermission.git`
- **Branch**: `main`
- **Current Version**: `v0.1.0`

## Directory Structure

```
intermission/
├── intermission.php           # Main plugin file
├── assets/
│   ├── css/
│   │   ├── intermission.css   # Base structural CSS
│   │   ├── admin.css          # Admin settings page CSS
│   │   └── toolbar.css        # Admin bar toggle CSS
│   └── js/
│       ├── admin.js           # Settings page JavaScript
│       └── toolbar.js         # Admin bar toggle JavaScript
├── templates/
│   ├── maintenance.php        # Public maintenance page
│   └── admin-settings.php     # Admin settings page
├── themes/
│   ├── default.css            # Default theme
│   ├── purple-gradient.css    # Purple gradient theme
│   ├── dark.css               # Dark theme
│   └── ocean.css              # Ocean blue theme
├── build.sh                   # Unix build script
├── build.bat                  # Windows build script
├── README.md                  # User documentation
└── CLAUDE.md                  # This file
```

## Architecture

### CSS Class Naming

All CSS classes use the `intermission-` prefix to avoid conflicts. Classes are semantic and describe function rather than appearance.

**Examples:**
- `.intermission-container`
- `.intermission-headline`
- `.intermission-countdown`
- `.intermission-mode-toggle`

### Theme System

The plugin uses a two-layer CSS architecture:

1. **Base CSS** (`assets/css/intermission.css`): Defines structure, layout, fonts, and responsive breakpoints. Minimal styling with neutral colors.

2. **Theme CSS** (`themes/*.css`): Override colors, backgrounds, shadows, and decorative elements. Each theme file has a header with metadata:

```css
/*
Theme Name: Ocean
Theme URI: https://github.com/abrayall/intermission
Description: Calming ocean blue gradient theme
Version: 1.0
Author: Brayall, LLC
Author URI: https://brayall.com
*/
```

### Admin Bar Integration

The plugin adds a toggle to the WordPress admin bar (priority 35) that:
- Shows current mode with colored dot (green = Live, yellow = Maintenance)
- Displays text label ("Live" or "Maintenance")
- Toggles mode via AJAX when clicked
- Uses WordPress nonces for security

## Build System

### Version Management

Versions are managed using **git tags** in the format `v{major}.{minor}.{maintenance}`.

**Version Format:**
- `0.1.0` - Clean release at tag `v0.1.0`
- `0.1.0-2` - 2 commits after tag
- `0.1.0-2-11050950` - 2 commits after tag + local changes (timestamp: Nov 5, 09:50)

### Build Scripts

**Unix/Linux/Mac:**
```bash
./build.sh
```

**Windows:**
```cmd
build.bat
```

### How Versioning Works

1. Reads latest git tag matching `v*.*.*` format
2. If commits exist after tag, appends commit count
3. If uncommitted local changes exist, appends timestamp `MMDDHHMM`
4. Generates `version.properties` during build process
5. Updates plugin header version in built ZIP
6. Includes `version.properties` in plugin package

**Output:**
- `build/intermission-{version}.zip`
- `build/version.properties`

### Version Reading

The plugin reads version from `version.properties` at runtime:

```php
function intermission_get_version() {
    $version_file = plugin_dir_path(__FILE__) . 'version.properties';

    if (!file_exists($version_file)) {
        return '0.1.0';
    }

    $properties = parse_ini_file($version_file);

    if ($properties === false || !isset($properties['major']) || !isset($properties['minor']) || !isset($properties['maintenance'])) {
        return '0.1.0';
    }

    return $properties['major'] . '.' . $properties['minor'] . '.' . $properties['maintenance'];
}

define('INTERMISSION_VERSION', intermission_get_version());
```

## Git Workflow

### Commit Messages

**IMPORTANT:** Commit messages must follow these rules:

1. **Keep commits to one-line summaries** - no multi-line bullet lists
2. **DO NOT mention "Claude" or "Claude Code" in commit messages**
3. **Use imperative mood** - "Add feature" not "Added feature"
4. **Be concise and descriptive** - focus on what changed

**Good Examples:**
```
Add theme selection to settings page
Update README with theme documentation
Fix toggle color change on click
Remove color picker customization
```

**Bad Examples:**
```
✗ Added new feature with Claude's help
✗ Update settings page:
  - Add theme selector
  - Remove color pickers
  - Update layout
✗ Fixed some bugs
```

### Release Workflow

1. Commit all changes
2. Create version tag: `git tag v0.1.1`
3. Push commits: `git push origin main`
4. Push tag: `git push origin v0.1.1`
5. Build release: `./build.sh`
6. Upload ZIP to WordPress or GitHub releases

## Development Guidelines

### Code Standards

1. **DO NOT add comments to code** - no inline comments, no CSS comments, no explanatory comments (except for theme headers)
2. **Keep UI labels concise** - prefer single words when possible
3. **Follow WordPress coding standards** for PHP
4. **Use WordPress functions** - `esc_html()`, `esc_attr()`, `wp_kses_post()`, etc.
5. **Security first** - always validate and sanitize input, use nonces for AJAX

### Testing

Test on local WordPress installation before committing:

```bash
# Build plugin
./build.sh

# Extract to test location
cd build
unzip -q -o intermission-0.1.0.zip

# Test in WordPress at /wp-content/plugins/intermission/
```

### Security Considerations

- All user input is sanitized using WordPress functions
- AJAX requests use WordPress nonces
- SQL queries use prepared statements (if added in future)
- File uploads validated by type and size (if added in future)
- Secret keys use `sanitize_text_field()`
- IP addresses use `sanitize_textarea_field()`

## Key Files

### intermission.php (lines 1-40)

Main plugin file containing:
- Plugin header metadata
- Version reading function
- Plugin initialization
- WordPress hook registrations

### templates/maintenance.php

Public-facing maintenance page:
- Displays headline, message, countdown
- Loads base CSS + selected theme CSS
- Contains inline JavaScript for countdown timer
- Returns 503 HTTP status code

### templates/admin-settings.php

WordPress admin settings page:
- Mode toggle (Live/Maintenance)
- Theme selector dropdown
- Headline, message, countdown inputs
- Secret key and IP whitelist configuration

### assets/js/toolbar.js

Admin bar toggle functionality:
- AJAX request to toggle maintenance mode
- Updates dot color and label text
- Uses delegated event binding for reliability
- Handles success/error states

### assets/js/admin.js

Settings page interactivity:
- Handles mode toggle state changes
- Updates toggle color and label on change

## Plugin Settings

All settings stored as WordPress options:

- `intermission_enabled` - Boolean, maintenance mode on/off
- `intermission_headline` - String, main heading text
- `intermission_message` - String, message text (allows HTML)
- `intermission_countdown_date` - String, countdown date (YYYY-MM-DD)
- `intermission_countdown_time` - String, countdown time (HH:MM)
- `intermission_theme` - String, selected theme slug
- `intermission_secret_key` - String, preview URL key
- `intermission_whitelist_ips` - String, newline-separated IPs

## WordPress Hooks

### Actions

- `template_redirect` (priority 1) - Show maintenance page
- `admin_menu` - Add settings page
- `admin_init` - Register settings
- `admin_enqueue_scripts` - Load admin CSS/JS
- `admin_bar_menu` (priority 35) - Add admin bar toggle
- `wp_enqueue_scripts` - Load toolbar CSS/JS on frontend
- `wp_ajax_intermission_toggle` - Handle AJAX toggle

### Filters

None currently implemented.

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11+ (with graceful degradation)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Future Enhancements

Potential features for future versions:

1. Social media link configuration
2. Custom logo upload
3. Email subscription form integration
4. Role-based bypass (not just administrators)
5. Schedule maintenance windows
6. Multiple language support
7. Theme preview in admin
8. Import/export settings

## License

GPL v2 or later

## Author

Brayall, LLC

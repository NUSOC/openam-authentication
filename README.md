# OpenAM Authentication Plugin

**Contributors:** NUSOC  
**Tags:** authentication, sso, openam, single-sign-on  
**Requires at least:** 4.0  
**Tested up to:** 6.0  
**Stable tag:** 1.0  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

WordPress authentication plugin supporting OpenAM Legacy, OpenAM 1.0, and agentless SSO integration.

## Description

This plugin provides WordPress authentication integration with OpenAM and agentless SSO systems. Originally forked from ForgeRock's OpenAM authentication plugin, it has been extended to support institutional agentless SSO implementations.

### Features

* **Multiple Authentication Modes:**
  - OpenAM 1.0
  - OpenAM Legacy
  - Agentless SSO ("Unofficially Forked")
* **Seamless WordPress Integration**
* **Configurable Authentication Options**
* **Duo Authentication Support**

## Installation

### Manual Installation

1. Navigate to your WordPress plugins directory:
   ```bash
   cd /path/to/wordpress/wp-content/plugins/
   ```

2. Clone the repository:
   ```bash
   git clone https://github.com/NUSOC/openam-authentication.git
   ```

3. Activate the plugin through the WordPress admin interface:
   - Go to **Plugins > Installed Plugins**
   - Find "OpenAM Authentication" and click **Activate**

### WordPress Admin Installation

1. Upload the plugin files to `/wp-content/plugins/openam-authentication/`
2. Activate the plugin through the **Plugins** screen in WordPress
3. Configure the plugin via **Settings > OpenAM Authentication**

## Configuration

1. Navigate to **Settings > OpenAM Authentication** in your WordPress admin
2. Select your authentication mode:
   - **1.0** - For OpenAM 1.0 installations
   - **Legacy** - For older OpenAM versions
   - **Unofficially Forked** - For agentless SSO implementations
3. Fill in the required fields under your selected mode's section
4. Save your settings

## Important Notice

⚠️ **This code is not supported by ForgeRock and it is your responsibility to verify that the software is suitable and safe for use.**

## Changelog

### Planned Updates
- Remove legacy OpenAM functions as IDM sunsets OpenAM Legacy support

### April 2020
- Added `isDuoAuthenticated` property validation
- Fixed HTML comment rendering issues in settings
- Removed unused plugin files
- Decoupled from Laravel plugin dependencies
- Integrated OpenAM2020 class directly into main plugin
- Removed deprecated MFA and cookie name arguments

## Support

This is a community-maintained fork. For issues and contributions, please visit the [GitHub repository](https://github.com/NUSOC/openam-authentication).

## Credits

This project is based on the original [ForgeRock OpenAM Authentication plugin](https://github.com/forgerock/openam-authentication). 
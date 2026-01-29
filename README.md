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

After installation, navigate to **Settings > OpenAM REST** in your WordPress admin to configure the plugin.

### API Settings

**OpenAM REST enabled**
- Check to enable the plugin

**OpenAM API Version**
- **1.0** - For OpenAM 12 and 13 installations
- **Legacy** - For OpenAM 9, 10, and 11 installations  
- **Unofficial Forked Method (2020)** - For agentless SSO implementations

### Cookie Settings

**OpenAM Session Cookie**
- Default: `iPlanetDirectoryPro`
- Check with your OpenAM administrator if different

**Cookie Domain**
- The domain where the session cookie will be set
- Default is the host server name, but can be the domain component
- Consult your OpenAM administrator for proper SSO configuration

### OpenAM Settings

**OpenAM base URL**
- Your OpenAM deployment URL
- Example: `https://openam.example.com:443/openam`

**OpenAM realm where users reside**
- The realm containing your users
- Examples: `/` or `/myrealm`

**OpenAM Authentication Module**
- Authentication module to use (optional)
- Examples: `DataStore` or `LDAP`
- Cannot be used with Service Chain
- Leave empty to use OpenAM default

**OpenAM Authentication Service (Chain)**
- Authentication service or chain to use (optional)
- Examples: `ldapService` or `myChain`
- Cannot be used with Authentication Module
- Leave empty to use OpenAM default

**Verify SSL/TLS certificate**
- Enable if OpenAM uses valid SSL certificate signed by recognized CA

### Forked Settings (Agentless SSO)

*Only required when using "Unofficial Forked Method (2020)"*

**Apigee API Key**
- Your Apigee API key for SSO integration

**Web SSO Api**
- Web SSO API endpoint URL

**ssoRedirectURL**
- SSO redirect URL with authentication parameters
- DUO two-factor: `https://subdomain.domain.edu/nusso/XUI/?realm=your_realm#login&authIndexType=service&authIndexValue=ldap-and-duo&goto=`
- NetID only: `https://subdomain.domain.edu/nusso/XUI/?realm=your_realm#login&authIndexType=service&authIndexValue=ldap-registry&goto=`

**Directory Basic Search End Point**
- Endpoint for retrieving email addresses by NetID
- Recommended to use production endpoint for accuracy

**Directory Basic Search End Point API KEY**
- API key for the Basic Directory Search service

### WordPress Settings

**Logout from OpenAM when logging out from WordPress**
- Check to terminate OpenAM session on WordPress logout

**OpenAM attributes to map Login Name and Mail address**
- Comma-separated OpenAM attributes
- Example: `uid,mail`

**Redirect to OpenAM for Login**
- Enable for complex authentication workflows beyond username/password
- Required for authentication chains with multiple steps

**Page to go after OpenAM Successful login**
- Redirect URL after successful OpenAM authentication
- Only used when "Redirect to OpenAM for Login" is enabled

### Debugging

**Enable debug**
- Enable debug logging (disable in production)

**Debug File**
- Path to debug log file
- Only used when debug is enabled

### Configuration Tips

- Start with basic settings and test authentication before enabling advanced features
- For agentless SSO, ensure all "Forked Settings" are properly configured
- Test SSL certificate verification in a staging environment first
- Enable debugging only during setup and troubleshooting

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
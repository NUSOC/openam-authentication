# openam-authentication plugin (forked to function with agent-less SSO)

## Warning
*This code is not supported by ForgeRock and it is your responsibility to verify that the software is suitable and safe for use.*

We're building this to transition our wordpress installations from FR OpenAM to our institution's implementation of an "agentless SSO" through a centralized registry . We've added a third option so that when the institution decides to flip the switch, we'll be ready. 

With the existing options that were already in the original version of this plugin, the third we've added is "Unofficially Forked". When chosen, all option value in the "Forked" section are used. 

- 1.0
- Legacy
- Unofficially Forked 

This third option allows for the forked functionality. Eventually, original functions this plugin will be deprecated. 

## To use
### From `wp-content/plugins`
```
git clone https://github.com/NUSOC/openam-authentication.git
cd openam-authentication
```

### From UI
Set "Unofficial Forked 2020" and fill in all necessary fields in the under the *Forked* headline.  

### Change Log

To Do Summer 2020
- Since IDM will sunset OpenAM Legacy, remove older functions by first commenting out and then finally deleting. 

April 2020 
- Removed other files not directly accessed for plugin functionality. 
- Decoupled from the Laravel Plugin and copied the OpenAM2020 class into the main plugin. This should be easier to maintain rather than trying to too closely couple these two projects. 
- Removed references to boolean MFA argument and cookie name argument as these really don't matter. The endpoint call doesn't seem to respect the require MFA anyway. 







### ORIGINAL DOCUMENTATION
This project started from a fork of https://github.com/forgerock/openam-authentication. Please see original project there. 
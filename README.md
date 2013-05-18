README
======

Joomla Facebook Authentication is a plugin designed for Joomla CMS 3.1 that will try to authenticate the user
with the OAuth2 system of facebook using the [Facebook libraries](https://github.com/joomla/joomla-cms/tree/master/libraries/joomla/facebook) existing in Joomla.

**Functionality:**

The plugin will be triggered at "OnUserAuthenticate" event of Joomla CMS. It will try authenticate the user using facebook
following the [flow defined by facebook](https://developers.facebook.com/docs/facebook-login/login-flow-for-web-no-jssdk/).
In order to work, you need a way to handle the redirection of facebook when it comes back to Joomla. For this you can install the
[OAuth2 Redirection Login Treatment](https://github.com/rubrodapa/pl_oauth2logintreatment).

After having the correct token from Facebook, the plugin checks if there is a user registered with the same email.
If so, the plugin logins that user in the site.
If not, the plugin sends an error saying that the user is not registered yet with that email.

**Joomla Facebook Authentication 0.0.1**

- Try to authenticate a user with the Facebook OAuth2 library.
- The user has to be registered in the site before using this authentication method.
- Needs the use of a second plugin to handle the redirection. 

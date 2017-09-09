# GoogleSignin
A lightweight PHP class to implement a Google Sign-In with minimal effort and overhead.
1. [Background](#background)
2. [Class Usage](#class-usage)

## Background
The Google Sign-In is pretty basic to incorperate but I kept having to go back to reinvent the wheel every time I wanted to add it to a project. I created this for a more simplified approach. There isn't much in the way of error handling but it should work nonetheless.

## Class Usage
### Declaring The Class
Declaring the class is pretty basic with two arguments:
```PHP
 require_once( 'class.GoogleSignin.php' );
 $goo = new GoogleSignin( string $configFile, bool $echoInitCode );
```
*$configFile* is the filename for the config. Its path should be relative to the class file.
*$echoInitCode* is a boolean used to determine if the default required javascript code should be echoed during class initialization.
### Defining The Variables
#### Configuration File
By default the class variables should be set by a configuration PHP file, named *googleSignin.config.php* by default. This should be a simple array with your app settings provided by the [Google Console](https://console.developers.google.com/apis/credentials). A typical config file should look something like this:
```PHP
$config = array(
    'appName' => 'your-app-name',
    'apiKey' => 'your-api-key',
    'clientID' => 'your-client-id',
    'clientSecret' => 'your-client-secret',
    'redirectURI' => 'your-redirect-uri',
    'authURL' => 'your-authorized-url',
    'sessVar' => 'user_data',
    'tokenName' => 'token'
);
```
#### Manually Set Variables
You may forgo declaring the configuration file and mannually assign each class variable if you have to need to have a dynamicly changed variable, like so:
```PHP
$goo->redirectURI = "https://{$_SERVER['HTTP_HOST']}/";
```
#### Variable Definitions
| Variable | Default | Description |
| --- | --- | --- |
| appName | Google Assigned | The name of yoru app |
| apiKey | Google Assigned | Your unique API key |
| clientID | Google Assigned | The client ID for your app |
| clientSecret | Google Assigned | The client secret given to your app |
| redirectURI | User Defined | The destination URL to redirect the user after successful signin |
| authURL | User Defined | The URL to direct the script to in order to validate authorization tokens |
| sessVar | 'user_data' | The key to assign user information to in session variable: $\_SESSION\['user_data'] |
| tokenName | 'token' | The \_GET key name that the script passes the token with: $\_GET\['token'] |

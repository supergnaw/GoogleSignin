# GoogleSignin
A lightweight PHP class to implement a Google Sign-In with minimal effort and overhead.
- [Background](#background)
- [Class Usage](#class-usage)
  - [Declaring The Class](#declaring-the-class)
  - [Defining The Variables](#defining-the-variables)
  - [Manually Set Variables](#manually-set-variables)
- [Example Explained](#example-explained)
  - [Signin](#signin)
  - [Signout](#signout)

## Background
The Google Sign-In is pretty basic to incorperate but I kept having to go back to reinvent the wheel every time I wanted to add it to a project. I created this for a more simplified approach. There isn't much in the way of error handling but it should work nonetheless.

## Class Usage
### Declaring The Class
Declaring the class is pretty basic with two arguments:
```PHP
 require_once( 'class.GoogleSignin.php' );
 $goo = new GoogleSignin( string $configFile, bool $echoInitCode );
```
- *$configFile* is the filename for the config. This path should be relative to the class file.
- *$echoInitCode* is a boolean used to determine if the default required javascript code should be echoed during class initialization.
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

## Example Explained
### Signin
The first portion of code is to include and declare the class, also defining the arguments using the default values so this is not necessary. If you look at the source code through the browser, you'll see this will include a vew of the required scripts automatically so you don't have to add them later.
```PHP
<?php
	/*** signin.php ***/
	// include and declare class
	require_once( 'php/class.GoogleSignin.php' );
	$goo = new GoogleSignin( 'googleSignin.config.php', true );
```
Following that is a section of code that dumps the $\_SESSION variable to show that it is empty, as well as displaying the Google button:
```PHP
	// session var will be empty until user signin
	var_dump( $_SESSION );

	// display the signin button
	echo $goo->signin_button();
```
The next section does all the magic in one line of code. After a successful sign-in action through Google, the page redirects to the predefined URL (in this case, itself) to authenticate the login, then passes the token on to the redirect page for user fetching. In this example, the URL constructed ends up as "/example_signin.php?token=" with the fetched token appended.
```PHP
	// show redierct script only while session var is empty to prevent endless loop
	if( empty( $_SESSION[$goo->sessVar] )) echo $goo->script_authenticate_redirect( $goo->authURL .'?'. $goo->tokenName .'=' );
```
Further down, this section does the nitty-gritty fetching user data. Using the token passed through GET, user data is fetched from Google, then is passed to $\_SESSION to the key defined in the *$goo->sessVar* class variable:
```PHP
	// actions to do when validating token
	if( !empty( $_GET['token'] )) {
		// load user into session variable
		$goo->fetch_user_data();
		// redirect page as necessary
		$goo->redirectURI = '/example_signin.php';
		echo $goo->page_redirect();
	}
```
Finally, the code for a logout link is provided which redirects to the signout page. With a few extra if statements, the actions performed on the signout page could also be accomplished on the signin page.
```PHP
	// provide a way for user to signout
	echo $goo->script_signout( '/signout', 500 );
```
### Signout
The first section is identical to the signin.php, however we do not need the core scripts so they are omitted with *false* as the second argument:
```PHP
<?php
	/*** signout.php ***/
	// include and declare class
	require_once( 'php/class.GoogleSignin.php' );
	$goo = new GoogleSignin( 'googleSignin.config.php', false );
```
After we make the class, it's time to destroy it:
```PHP
	// it's purging time
	unset( $_SESSION[$goo->sessVar] );
	session_destroy();
```
Finally, a link is provided to return to the signin page, along with a var_dump of $\_SESSION to confirm it has been emptied.
```PHP
	echo "<a href='/example_signin.php'>Signin</a>";
	// session var is now empty
	var_dump( $_SESSION );
```

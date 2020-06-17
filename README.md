# GoogleSignin
A lightweight PHP class to implement a Google Sign-In with minimal effort and overhead.
- [Background](#background)
- [Class Usage](#class-usage)
  - [Declaring The Class](#declaring-the-class)
  - [Defining The Variables](#defining-the-variables)
  - [Variable Definitions](#variable-definitions)
- [Example Explained](#example-explained)
  - [Declaration](#declaration)
  - [Sign In](#sign-in)
  - [Sign Out](#sign-out)
  - [Error Display](#error-display)
- [Class Functions](#class-functions)

## Background
The Google Sign-In is pretty basic to incorperate but I kept having to go back to reinvent the wheel every time I wanted to add it to a project. I created this for a more simplified approach. There isn't much in the way of error handling but it should work nonetheless.

## Class Usage
### Declaring The Class
Declaring the class is pretty basic with two arguments:
```PHP
 require_once( 'GoogleSignin.php' );
 $goo = new GoogleSignin();
```
### Defining The Variables
As of version 2 of this code, the class no longer supports/requires a config file declaring constants. Instead, mannually assign each class variable like so:
```PHP
$goo->redirectURI = "https://{$_SERVER['HTTP_HOST']}/";
```
#### Variable Definitions
| Variable | Default | Description |
| --- | --- | --- |
| appName | Google Assigned | The name of your app |
| apiKey | Google Assigned | Your unique API key |
| clientID | Google Assigned | The client ID for your app |
| clientSecret | Google Assigned | The client secret given to your app |
| redirectURI | User Defined | The destination URL to redirect the user after successful signin |
| authURL | User Defined | The URL to direct the script to in order to validate authorization tokens |
| sessVar | 'user_data' | The key to assign user information to in session variable: $\_SESSION\['user_data'] |
| tokenName | 'token' | The \_GET key name that the script passes the token with: $\_GET\['token'] |

## Example Explained
### Declaration
The first portion of code is to include and declare the class. You also need to define the class variables with the ones provided by Google.
```PHP
require_once( 'GoogleSignin.php' );

$goo = new GoogleSignin();
$url = strtok( $_SERVER["REQUEST_URI"], '?' );

$goo->appName = 'Application Name Here';
$goo->apiKey = 'API Key';
$goo->clientID = 'Client ID';
$goo->clientSecret = 'Client Secret';
```
### Sign In
The second portion of code is for displaying the sign in button, or, when the token from Google is present in the URL, to fetch the user data and load it into the session variable. Once the data is loaded, the page is redirected to clear the token from the URL and the session data is displayed along with a sign out link.
```PHP
if( empty( $_SESSION['user_data'] )) {
	if( empty( $_GET[$goo->tokenName] )) {
		echo "<p>You are not signed in.</p>";
		// display sign in button
		echo $goo->signin_button();

		// on sign in, redirect with token
		echo $goo->script_authenticate_redirect( $goo->authURL .'?'. $goo->tokenName .'=' );
	} else {
		// load user data into session variable
		$goo->fetch_user_data();
		// redirect page to clear token var
		echo $goo->page_redirect();
	}
} else {
	// sign out
	echo "<p>You are signed in</p>
	<p><a href='?signout=true'>Sign Out</a></p>";
	// show session variable
	var_dump( $_SESSION );
}
```
### Sign Out
The third section is devoted to the sign out actions of clearing out the session data along with redirecting the page to itself to reload the code:
```PHP
// it's purging time
unset( $_SESSION );
session_destroy();
// redirect
if( empty( $_SESSION )) {
	echo "<p>Successfully signed out.</p>".$goo->static_page_redirect( $url );
} else {
	echo "<p>Failed to sign out.</p>";
}
```
### Error Display
The final section of code shows how to dump any generated errors.
```PHP
if( !empty( $goo->err )) {
	foreach( $goo->err as $err ) {
		echo "<p>{$err}</p>";
	}
	die;
}
```

## Class Functions
future documentation

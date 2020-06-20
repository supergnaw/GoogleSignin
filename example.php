<?php
  require_once( 'GoogleSignin.php' );

  $goo = new GoogleSignin();
  $url = strtok( $_SERVER["REQUEST_URI"], '?' );

  $goo->appName = 'Application Name Here';
  $goo->apiKey = 'API Key';
  $goo->clientID = 'Client ID';
  $goo->clientSecret = 'Client Secret';

  // initiate code
  echo $goo->init_code();

  if( empty( $_GET['signout'] )) {
    // if not signed in
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
      echo "<p>".$goo->signin_button()."</p>
            <p><a href='?signout=true'>Sign Out</a></p>";
      // show session variable
      var_dump( $_SESSION );
    }
  } else {
    // it's purging time
    unset( $_SESSION );
    session_destroy();
    // redirect
    if( empty( $_SESSION )) {
      echo "<p>Successfully signed out.</p>".$goo->static_page_redirect( $url );
    } else {
      echo "<p>Failed to sign out.</p>";
    }
  }

  if( !empty( $goo->err )) {
    foreach( $goo->err as $err ) {
      echo "<p>{$err}</p>";
    }
    die;
  }

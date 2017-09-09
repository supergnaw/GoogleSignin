<?php
	/*** example_signin.php ***/
	// include and declare class
	require_once( 'php/class.GoogleSignin.php' );
	$goo = new GoogleSignin( 'googleSignin.config.php', true );

	// session var will be empty until user signin
	var_dump( $_SESSION );

	// display the signin button
	echo $goo->signin_button();

	// show redierct script only while session var is empty to prevent endless loop
	if( empty( $_SESSION[$goo->sessVar] )) echo $goo->script_authenticate_redirect( $goo->authURL .'?'. $goo->tokenName .'=' );

	// actions to do when validating token
	if( !empty( $_GET['token'] )) {
		// load user into session variable
		$goo->fetch_user_data();
		// redirect page as necessary
		$goo->redirectURI = '/example_signin.php';
		echo $goo->page_redirect();
	}

	// provide a way for user to signout
	echo $goo->script_signout( '/example_signout.php', 500 );

<?php
	/*** example_signout.php ***/
	// include and declare class
	require_once( 'php/class.GoogleSignin.php' );
	$goo = new GoogleSignin( 'googleSignin.config.php', false );

	// it's purging time
	unset( $_SESSION[$goo->sessVar] );
	session_destroy();

	echo "<a href='/example_signin.php'>Signin</a>";

	// session var is now empty
	var_dump( $_SESSION );

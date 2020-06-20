<?php
/*
 * GoogleSignin v2.0
 *
 * https://github.com/supergnaw/GoogleSignin
 */

	class GoogleSignin {
		public $appName = 'App Name Here';
		public $apiKey;
		public $clientID;
		public $clientSecret;
		public $authURL;
		public $redirectURI;
		public $sessVar = 'user_data';
		public $tokenName = 'token';

		public function __construct() {
			// start session
			if( session_status() == PHP_SESSION_NONE ) session_start();

			// set default url values
			$this->authURL = strtok( $_SERVER["REQUEST_URI"], '?' );
			$this->redirectURI = strtok( $_SERVER["REQUEST_URI"], '?' );
		}

		// verify the class variables are set
		public function verify_vars() {
			try {
				if( empty( $this->apiKey )) throw new Exception( 'GoogleSignin API key ( $apiKey ) not set.' );
				if( empty( $this->clientID )) throw new Exception( 'GoogleSignin client ID ( $clientID ) not set.' );
				if( empty( $this->clientSecret )) throw new Exception( 'GoogleSignin client ID ( $clientSecret ) not set.' );
			} catch( Exception $e ) {
				$this->err[] = $e->getMessage();
				return false;
			}
			return true;
		}

		// code initialization, usually invluded in the header
		public function init_code() {
			if( true !== $this->verify_vars()) {
				return false;
			}
			return $this->load_platform_library() . $this->meta_client_id();
		}

		// include script library
		public static function load_platform_library() {
			return "<script src=\"https://apis.google.com/js/platform.js\" async defer></script>\n";
		}

		// meta tag for client ID
		public function meta_client_id() {
			if( true !== $this->verify_vars()) {
				return false;
			}
			return "<meta name=\"google-signin-client_id\" content=\"{$this->clientID}\"/>\n";
		}

		// use oath token to fetch user data
		public function fetch_user_data() {
			if( true !== $this->verify_vars()) {
				return false;
			}
			try {
				$url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token={$_GET[$this->tokenName]}";
				$obj = $this->get_url_json( $url );
				if( false === $obj ) return false;
				if( !empty( $this->sessVar )) {
					foreach( $obj as $key => $val ) $_SESSION[$this->sessVar][$key] = $val;
				}
				return $obj;
			} catch( Exception $e ) {
				$this->err[] =  $e->getMessage();
				return false;
			}
		}

		// validate and return json object
		public function get_url_json( $url ) {
			try {
				// check headers for 4XX/5XX status codes ( A.K.A. ERRORS! )
				$headers = @get_headers( $url );
				if ( 1 == preg_match( '/http.*[45][0-9]{2}/i', $headers[0] )) {
					throw new Exception( $headers[0] );
					return false;
				}
				// get json object
				$json = @file_get_contents( $url );
				$obj = json_decode( $json );
				return $obj;
			} catch( Exception $e ) {
				// log errors
				$this->err[] = $e->getMessage();
				return false;
			}
		}

		// redirect page to the token verification url
		public function script_authenticate_redirect( $url = null ) {
			if( empty( $url )) {
				if( false !== strpos( '?', $this->authURL )) {
					$url = $this->authURL .'?'. $this->tokenName .'=';
				} else {
					$url = $this->authURL .'&'. $this->tokenName .'=';
				}
			}
			$code = "
				<script>
					function onSignIn(googleUser) {
						console.log( 'Redirecting to: {$url}' );
						var id_token = googleUser.getAuthResponse().id_token;
						window.location = '{$url}' + id_token;
					}
				</script>\n";
			return $code;
		}

		// basic page redirect using class var
		public function page_redirect( $url = null ) {
			if( empty( $url )) $url = $this->redirectURI;
			$code = "
				<script>window.location.replace( '{$url}' );</script>";
			return $code;
		}

		// static page redirect
		public static function static_page_redirect( $url ) {
			$code = "
				<script>window.location.replace( '{$url}' );</script>";
			return $code;
		}

		// code for google sign in button
		public static function signin_button() {
			return "<div class=\"g-signin2\" data-onsuccess=\"onSignIn\"></div>\n";
		}

		// sign out link and script
		public static function script_signout( $url = '/', $timeout = 500, $class = '', $id = '' ) {
			if( !empty( $class )) $class = " class = '{$class}'";
			if( !empty( $id )) $id = " id = '{$id}'";
			$code = "
				<a href = '#'{$class}{$id} onclick = 'signOut();'>Sign Out</a>
				<script>
					function signOut() {
						var timeout = 0;
						if( undefined == gapi.auth2 ) {
							gapi.load('auth2', function() {
								gapi.auth2.init();
							});
							console.log( 'do stuff' );
							// set internal timeout to prevent logout attempt before auth2 can be initialized
							timeout = 1000;
						}
						setTimeout( function() {
							var auth2 = gapi.auth2.getAuthInstance();
							auth2.disconnect();
							auth2.signOut().then(function () {
								console.log('User signed out.');
							});
						}, timeout);
						setTimeout(function(){
							window.location.replace( '{$url}' );
						}, {$timeout});
					}
				</script>\n";
			return $code;
		}
	}

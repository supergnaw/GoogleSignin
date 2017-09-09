<?php
	/*
	 * GoogleSignin v1.1
	 *
	 * https://github.com/supergnaw/GoogleSignin
	 */
	class GoogleSignin {
		public $appName;
		public $apiKey;
		public $clientID;
		public $clientSecret;
		public $authURL;
		public $redirectURI;
		public $sessVar;
		public $tokenName;

		public function __construct( $configFile = 'googleSignin.config.php', $echoInitCode = true ) {
			if( !empty( $configFile )) {
				$configFile = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $configFile;
				$this->load_config( $configFile );
			}
			if( session_status() == PHP_SESSION_NONE ) session_start();

			if( true === $echoInitCode ) {
				echo $this->load_platform_library();
				echo $this->meta_client_id();
			}
		}

		public function load_config( $configFile ) {
			if( !file_exists( $configFile )) die( 'Missing config file:' . $configFile );
			require_once( $configFile );
			if( empty( $config )) die( '$config() not defined in config file.' );
			foreach( $config as $name => $val ) $this->$name = $val;
			return true;
		}

		public static function load_platform_library() {
			return "<script src=\"https://apis.google.com/js/platform.js\" async defer></script>\n";
		}

		public function meta_client_id() {
			return "<meta name=\"google-signin-client_id\" content=\"{$this->clientID}\"/>\n";
		}

		public function fetch_user_data( $toSession = null, $tokenName = null, $token = null ) {
			if( empty( $toSession )) $toSession = $this->sessVar;
			if( empty( $$tokenName )) $tokenName = $this->$tokenName;
			if( !empty( $_GET[$tokenName] )) {
				$token = ( empty( $toekn )) ? $_GET[$tokenName] : $token;
				$url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token={$token}";
				$obj = $this->get_url_json( $url );
				if( false === $obj ) return false;
				if( !empty( $toSession )) {
					foreach( $obj as $key => $val ) $_SESSION[$toSession][$key] = $val;
				}
				return $obj;
			} else {
				die( 'Missing authorization token.' );
			}
		}

		public static function get_url_json( $url ) {
			$headers = @get_headers( $url );
			// check headers for 4XX/5XX status codes ( A.K.A. ERRORS! )
			if ( 1 == preg_match ( '/http.*[45][0-9]{2}/i', $headers[0] )) return false;
			$json = @file_get_contents( $url );
			$obj = json_decode( $json );
			return $obj;
		}

		public function script_authenticate_redirect( $authURL = null ) {
			$authURL = ( !empty ( $authURL )) ? $authURL : $this->authURL;
			$code = "
				<script>
					function onSignIn(googleUser) {
						console.log( 'Redirecting to: {$authURL}' );
						var id_token = googleUser.getAuthResponse().id_token;
						window.location = '{$authURL}' + id_token;
					}
				</script>\n";
			return $code;
		}

		public static function page_redirect( $url ) {
			$code = "
				<script>window.location.replace( '{$url}' );</script>";
			return $code;
		}

		public static function signin_button() {
			return "<div class=\"g-signin2\" data-onsuccess=\"onSignIn\"></div>\n";
		}

		public static function script_signout( $url = '#', $timeout = 500 ) {
			$code = "
				<a href = '#' onclick = 'signOut();'>Sign out</a>
				<script>
					function signOut() {
						var auth2 = gapi.auth2.getAuthInstance();
						auth2.signOut().then(function () {
							console.log('User signed out.');
						});
						setTimeout(function(){
							window.location.replace( '{$url}' );
						}, {$timeout});
					}
				</script>\n";
			return $code;
		}
	}

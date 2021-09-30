<?php

//------------------------------------------//
// Authentication Class 										//
// Version 2018 Build 180705								//
// Copyright (C) 2018 Nyoman Piarsa					//
// All right reserved												//
//------------------------------------------//

/*
	for Laravel users
	- copy these 2 files ie. AuthRequest.php & JWT.php to App\SSO folder
	- create a AuthMiddleware middleware then insert the code below :

			<?php

			namespace App\Http\Middleware;

			use Closure;
			use App\SSO\AuthRequest;

			class AuthMiddleware extends AuthRequest
			{

			    public function handle($request, Closure $next)
			    {
			        $this->authenticate();
			        return $next($request);
			    }
			}
	- register the middleware to App\Http\kernel.php

	        'authClient' => \App\Http\Middleware\AuthMiddleware::class,

	- testi the midleware on your route
			
			})->middleware('authClient');
	
*/

namespace App\SSO;	// for Laravel
use App\SSO\JWT;	// for Laravel
use Auth;
// include 'JWT.php'; 	// for Native PHP

class AuthRequest extends JWT
{
	// private $ssoServer = 'http://ihdnsso.laksitastartup.com/auth?authRequest='; 	// SSO Server Authentication Link
	// private $ssoServer = 'http://ssobkd.ihdn.ac.id/auth?authRequest='; 	// SSO Server Authentication Link
	private $ssoServer = 'http://sso.bengkel-kuy.com/login?authRequest='; 	// SSO Server Authentication Link
	// private $ssoServer = 'http://127.0.0.1:8000/auth?authRequest='; 	// SSO Server Authentication Link
	private $logoutLink;

	//constructor
	public function __construct()
	{
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		// tambahkan bagian ini ------
		if (strtoupper(substr($_SERVER['HTTP_HOST'], strpos($_SERVER['HTTP_HOST'], "."))) == '.BENGKEL-KUY.COM') {
			// $this->ssoServer = 'http://ihdnsso.laksitastartup.com/auth?authRequest=';
			$this->ssoServer = 'http://sso.bengkel-kuy.com/login?authRequest=';
		} else {
			// $this->ssoServer = 'http://sruti.laksitastartup.com/auth?authRequest=';
			// $this->ssoServer = 'http://ihdnsso.laksitastartup.com/auth?authRequest=';
			$this->ssoServer = 'http://sso.bengkel-kuy.com/login?authRequest=';
		}
		// tambahkan bagian ini ------
		$this->logoutLink = 'http://' . $_SERVER['HTTP_HOST'] . '/auth?service=logout&sessionId=';
	}

	// check the user authentication
	public function authenticate()
	{
		// echo '<pre>';print_r($_SESSION);print_r($_REQUEST);exit;

		// if there are authentication data send from SSO server
		if (!empty($_REQUEST['authData'])) {
			$this->setJWTString($_REQUEST['authData']);

			// decode valid JWT data from SSO server
			if ($this->decodeJWT()) {
				//success on validate JWT
				if (session_id() == $this->getPayloadJWT()->sessionRequest) {
					// if session is valid then set session and redirect page
					$_SESSION['authUser'] = $this->getPayloadJWT();
					header('Location:' . $_SESSION['authUser']->redirect);
					exit;
				} else {
					//if session is invalid
					$this->showError("Invalid browser session !");
				}
			} else {
				$this->showError("Invalid JWT data !");
			}
		}
		// if there is a logout request  
		if (!empty($_REQUEST['service']) && ($_REQUEST['service'] == 'logout')) {
			$this->logout();
		}
		// user already authenticated
		if (!empty($_SESSION['authUser'])) {
			return true;
		} else {
			//
			// Auth::guard()->logout();
			// session()->invalidate();
			session_unset();
			// if a user not yet authenticated, then redirect to SSO server
			$payloadJWT = [
				'redirect' 			=> 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
				'logoutLink' 		=> $this->logoutLink,
				'kode_broker'		=> config('app.code'),
				'sessionRequest' 	=> session_id()
			];
			$this->setPayloadJWT($payloadJWT);
			$this->encodeJWT();
			header('Location:' . $this->ssoServer . $this->getJWTString());
			exit;
		}
	}

	// logout
	public function logout()
	{
		// return 'logout';
		if (!empty($_REQUEST['sessionId'])) {
			session_id($_REQUEST['sessionId']);
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			session_destroy();
			return 1;
		} else {
			return 0;
		}
	}

	// get user data
	public function getUser()
	{
		return $_SESSION['authUser'];
	}

	//
	public function setLogoutLink($link)
	{
		$this->logoutLink = $link;
	}

	//show error
	private function showError($error)
	{
		echo "<i>$error</i>";
		exit;
	}
}

//calling method from url parameter
// if (realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__) && isset($_REQUEST['service'])) {
// 	$auth = new AuthRequest();
// 	$auth->{$_REQUEST['service']}();
// }

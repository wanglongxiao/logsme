<?php

namespace App\Http\Controllers\Auth;

use Socialite;
use Illuminate\Routing\Controller;
use Session;
use Config;
use Log;

class AuthController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->scopes(['public_profile', 'email'])->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('facebook')->user();

        /*
        // OAuth One Providers
        $token = $user->token;
        $tokenSecret = $user->tokenSecret;
        // All Providers
        $user->getId();
        $user->getNickname();
        $user->getName();
        $user->getEmail();
        $user->getAvatar();
        */
        
		Log::error("FB login email : ".$user->getEmail());
		if ($user->getEmail() == env('ADMINEMAIL')) {
			Session::put('loginuser', $user->getEmail());
		}
        return redirect('/admin/list');    
    }
}
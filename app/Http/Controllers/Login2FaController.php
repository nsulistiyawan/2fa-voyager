<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use TCG\Voyager\Facades\Voyager;

class Login2FaController extends Controller
{
    use AuthenticatesUsers;

    public function login()
    {
        if (app('VoyagerAuth')->user()) {
            return redirect()->route('voyager.dashboard');
        }

        return Voyager::view('voyager::login');
    }

    public function postLogin(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);

        if ($this->guard()->attempt($credentials, $request->has('remember'))) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /*
     * Preempts $redirectTo member variable (from RedirectsUsers trait)
     */
    public function redirectTo()
    {
        return config('voyager.user.redirect', route('voyager.dashboard'));
    }

    public function verify2fa(){
        $ga = new \PHPGangsta_GoogleAuthenticator();
        $user = app('VoyagerAuth')->user();
        $secret = $user->token_2fa;
        $secretUrl = $ga->getQRCodeGoogleUrl('voyager-2fa', $secret);
        return view('admin.2fa.verify',compact('secret','secretUrl'));
    }

    public function doVerify2fa(Request $request, MessageBag $errors){
        $request->validate([
            'otp' => 'required'
        ]);

        $user = app('VoyagerAuth')->user();
        $otp = $request->get('otp');

        $ga = new \PHPGangsta_GoogleAuthenticator();
        $checkResult = $ga->verifyCode($user->token_2fa, $otp, 20);

        if(!$checkResult){
            $errors->add('otp','Please input a valid otp');
            return redirect()->route('admin.verify-2fa')->withErrors($errors->getMessages());
        }

        $request->session()->put('2fa-verified',1);
        return redirect('/admin');
    }

    public function setup2fa(Request $request){
        $ga = new \PHPGangsta_GoogleAuthenticator();

        $user = app('VoyagerAuth')->user();
        if($request->has('token_2fa')){
            $secret = $request->get('token_2fa');
        }
        else{
            $secret = $ga->createSecret();
        }
        $secretUrl = $ga->getQRCodeGoogleUrl('voyager-2fa', $secret);
        return view('admin.2fa.setup',compact('secret','secretUrl'));
    }

    public function doSetup2fa(Request $request, MessageBag $errors){
        $request->validate([
            'otp' => 'required',
            'secret' => 'required'
        ]);

        $secret = $request->get('secret');
        $otp = $request->get('otp');

        $ga = new \PHPGangsta_GoogleAuthenticator();
        $checkResult = $ga->verifyCode($secret, $otp, 20);
        if(!$checkResult){
            $errors->add('otp','Please input a valid otp');
            return redirect()->route('admin.setup-2fa',['token_2fa' => $secret])->withErrors($errors->getMessages());
        }

        //all good, save the expired to check later
        $user = app('VoyagerAuth')->user();
        $user->token_2fa = $secret;
        $user->expiry_2fa = config('session.lifetime');
        $user->save();

        $request->session()->put('2fa-verified',1);
        return redirect('/admin');
    }


    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/admin/login');
    }


}

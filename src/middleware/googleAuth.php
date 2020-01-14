<?php

namespace laravelstudio\laravelgooglesheetintegration\middleware;

// use App\Http\Middleware;
use laravelstudio\laravelgooglesheetintegration\contracts\UserInterface;
use Closure;
use laravelstudio\laravelgooglesheetintegration\models\googleSheetUser as SystemUser;

class googleAuth
{

    public $systemUser;

    public function __construct(SystemUser $systemUser)
    {        
        $this->systemUser = $systemUser;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //$tokendetails = \Auth::user()->with('userGoogleSheet')->get();
       // dd($tokendetails);
         $user_id = \Auth::user()->id;
         $userTokenDetails=$this->systemUser->whereId($user_id)->with('userGoogleSheet')->first();
        
         if(empty($userTokenDetails->userGoogleSheet))
         {
             abort("404");
         }
        // dd($tokendetails->userGoogleSheet);
        $tokendetails = $userTokenDetails->userGoogleSheet;
       if(!empty($tokendetails))
       {
           $access_token = $tokendetails->token_details;
           $client = $request->get('client');
           $client->setAccessToken(json_decode($access_token,true));
           if ($client->isAccessTokenExpired()) {            
            $client->refreshToken($tokendetails->refresh_token);
            $token = $client->fetchAccessTokenWithRefreshToken();
            
            $token_array = ["token_details"=>json_encode($token ),"refresh_token"=>$token["refresh_token"]];
            $this->systemUser->find($user_id)->userGoogleSheet()->update($token_array);
           }

       }
        
        return $next($request); 
    }
}

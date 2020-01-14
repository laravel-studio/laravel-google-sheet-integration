<?php

namespace laravelstudio\laravelgooglesheetintegration\middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class checkUserAuth
{ 

    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {        
         if(!\Auth::check()){
            Session::flash('error-message', "Please login to your system first.");
            return redirect()->back();
         }
         
        
        return $next($request); 
    }
}

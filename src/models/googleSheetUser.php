<?php

namespace laravelstudio\laravelgooglesheetintegration\models;

use App\User;

class googleSheetUser extends User
{
    protected $table = 'users';
    protected $guard_name = 'web';
    
    public function userGoogleSheet()
    {
        return $this->hasOne('laravelstudio\laravelgooglesheetintegration\models\userGoogleSheet','user_id','id');
    }
}

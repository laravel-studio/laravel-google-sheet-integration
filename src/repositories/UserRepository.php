<?php
namespace laravelstudio\laravelgooglesheetintegration\repositories;

use laravelstudio\laravelgooglesheetintegration\contracts\UserInterface;
use laravelstudio\laravelgooglesheetintegration\models\googleSheetUser as SystemUser;
use Auth;

class UserRepository implements UserInterface
{
    public $systemUser;

    public function __construct(SystemUser $systemUser)
    {
        $this->systemUser = $systemUser;
    }


    /* get usewr google sheet Details */
    public function getGoogleSheetDetails()
    {
        $user_id = Auth::user()->id;
       
        return $this->systemUser->find($user_id)->userGoogleSheet();
    }
    /* end */
    /* add token to user_google_sheets */
    public function createGooleToken($token)
    {
        $user_id = Auth::user()->id;  
        $this->systemUser->find($user_id)->userGoogleSheet()->create($token);
    }
    public function storeFolderId($folder_id)
    {
        $user_id = Auth::user()->id;
        $this->systemUser->find($user_id)->userGoogleSheet()->update(['sheet_folder_id'=>$folder_id]);
    }
    public function updateGooleToken($token)
    {
        $user_id = Auth::user()->id;
        $this->systemUser->find($user_id)->userGoogleSheet()->update($token);
    }
    public function storeFolder($folder_array)
    {
        $user_id = Auth::user()->id;
        $this->systemUser->find($user_id)->userGoogleSheet()->update($folder_array);
    }
    public function getGoogleSheetDetailsByUserId($user_id)
    {
       
        return $this->systemUser->find($user_id)->userGoogleSheet();
    }
   /* end  */
}

<?php
namespace itobuz\laravelgooglesheetintegration\repositories;

use itobuz\laravelgooglesheetintegration\contracts\userGoogleSheetInterface;
use itobuz\laravelgooglesheetintegration\models\userGoogleSheet;
use DB;
use Carbon\Carbon;
use Auth;


class userGoogleSheetRepository implements userGoogleSheetInterface
{
    public $userGoogleSheet;

    public function __construct(userGoogleSheet $userGoogleSheet)
    {
        $this->userGoogleSheet = $userGoogleSheet;
       
    }
    public function isTokenValid($refresh_token)
    {
        $user_id = Auth::user()->id;
        if($this->userGoogleSheet->where('refresh_token',$refresh_token)->where('user_id','!=',$user_id)->count()>0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    public function updateGoogleSheetStatus($usergooglesheetstatus)
    {
        $user_id = Auth::user()->id;
        $this->userGoogleSheet->where('user_id',$user_id)->update(["is_gsheet_activated"=>$usergooglesheetstatus]);
    }
    public function updateGoogleSheetId($sheet_id)
     {
        $user_id = Auth::user()->id;
        $this->userGoogleSheet->where('user_id',$user_id)->update(["sheet_id"=>$sheet_id]);
     }

    public function removeDataByUserId($user_id)
    {
        $this->userGoogleSheet->where('user_id',$user_id)->delete();
    }
    

}


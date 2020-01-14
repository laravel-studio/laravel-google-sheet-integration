<?php
namespace laravelstudio\laravelgooglesheetintegration\contracts;

interface UserInterface
{
    //User Google Sheets
    public function getGoogleSheetDetails();
    public function createGooleToken($token);
    public function storeFolderId($folder_id);
    public function storeFolder($folder_array);
    public function getGoogleSheetDetailsByUserId($user_id);
    public function updateGooleToken($token);
    
}

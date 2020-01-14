<?php
namespace laravelstudio\laravelgooglesheetintegration\contracts;

interface userGoogleSheetInterface
{
    public function isTokenValid($refresh_token);
    public function updateGoogleSheetStatus($usergooglesheetstatus);
    public function updateGoogleSheetId($sheet_id);
    public function removeDataByUserId($user_id);
}

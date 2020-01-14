<?php
namespace laravelstudio\laravelgooglesheetintegration\contracts;

interface googleSheetInterface
{
    public function updateToSheetByUser($fqdn,$upd_array,$sheet_user_details);
    public function pushNewHostToSheet($host_name,$user_details);
    public function getFolderList($sheet_details); 
    public function deleteThisSheet($sheet_details,$host_name); 
}
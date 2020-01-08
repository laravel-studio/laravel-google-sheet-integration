<?php
namespace itobuz\laravelgooglesheetintegration\repositories;

use itobuz\laravelgooglesheetintegration\contracts\googleSheetInterface;
use Exception;

class googleSheetUpdateRepository implements googleSheetInterface
{
    public $client;
    function __construct()
    {
        $this->client = new \Google_Client();
        $this->client->setAuthConfig(config('googlesheet.config'));
        $this->client->setScopes(
            [
                \Google_Service_Drive::DRIVE,
                \Google_Service_Storage::CLOUD_PLATFORM,
                'https://www.googleapis.com/auth/spreadsheets',
            ]
        );
    }
    
    public function updateToSheetByUser($fqdn,$upd_array,$sheet_user_details)
    {
        $access_token = $sheet_user_details->token_details;
        $this->client->setAccessToken(json_decode($access_token,true));
        if ($this->client->isAccessTokenExpired()) {
            $this->client->refreshToken($sheet_user_details->refresh_token);
            $token = $this->client->fetchAccessTokenWithRefreshToken();
            $token_array = ["token_details"=>json_encode($token ),"refresh_token"=>$token["refresh_token"]];
            
           }
    
        $driveService = new \Google_Service_Drive($this->client);
        $fileID = $sheet_user_details->sheet_id;
        
        try
        {
            $response = $driveService->files->export($fileID, 'text/csv', array(
                'alt' => 'media'));
               $content = $response->getBody()->getContents();
               $service = new \Google_Service_Sheets($this->client);

                     try{
                        
                        $sheetval= $service->spreadsheets_values->get($fileID, $fqdn.'!A1:Z1' , ['majorDimension' => 'ROWS']);
                                
                        $sheet_values = $sheetval->values;
                        $header = $sheet_values[0];
                        $requested_headers = array_keys($upd_array);
                        $sheet_insert_array = array();
                        $array_of_Headers = array();
                        $processed_update_array = array();
                
                        foreach($requested_headers as $key=>$hdr)
                        {
                            $hdr = trim($hdr);
                            $hdr = strtolower($hdr);
                            $hdr = str_replace("-"," ",$hdr );
                            $hdr = str_replace("_"," ",$hdr );
                            $hdr =trim($hdr);
                            foreach($header as $k => $h)
                            {
                                $h = trim($h);
                                $h = strtolower($h);
                                $h = str_replace("-"," ",$h );
                                $h = str_replace("_"," ",$h );
                                $h =trim($h);
                                if($h==$hdr)
                                {
                                    unset($requested_headers[$key]);
                                }
                            }
                        }
                        foreach($upd_array as $key=>$val)
                        {
                        $newkey = trim($key);
                        $newkey = strtolower($newkey);
                        $newkey = str_replace("-"," ",$newkey );
                        $newkey = str_replace("_"," ",$newkey );
                        $newkey =trim($newkey);
                        $processed_update_array[$newkey] = $val;
                        }
                        $array_of_Headers = array_merge($header,$requested_headers);
                        if(count($requested_headers)>0)
                        {
                            try{
                                $updateBody = new \Google_Service_Sheets_ValueRange([
                                    'range' => $fqdn.'!A1:ZZZ1',
                                    'majorDimension' => 'ROWS',
                                    'values' =>[$array_of_Headers],
                                    
                                ]);
                                $result = $service->spreadsheets_values->update($fileID,$fqdn.'!A1:ZZZ1',$updateBody,  ['valueInputOption' => 'USER_ENTERED']);
                                
                              }
                              catch(Exception $e)
                              {
                                dd($e->getMessage());
                              }
                                        
                        }
                        
                        foreach($array_of_Headers as $v)
                        {
                            $v = trim($v);
                            $v = strtolower($v);
                            $v = str_replace("-"," ",$v );
                            $v = str_replace("_"," ",$v );
                            $v = trim($v);
                            if(isset($processed_update_array[$v]))
                            {
                                $sheet_insert_array[] =$processed_update_array[$v]; 
                            }
                            else
                            {
                                $sheet_insert_array[] =''; 
                            }
                            
                        }
                        
                        // dd($sheet_insert_array);
                        try{
                            $updateBody = new \Google_Service_Sheets_ValueRange([
                                'range' => $fqdn.'!A:ZZZ',
                                'majorDimension' => 'ROWS',
                                'values' =>[$sheet_insert_array],
                                
                            ]);
                            $service = new \Google_Service_Sheets($this->client);
                            $result = $service->spreadsheets_values->append($fileID,$fqdn.'!A:ZZZ',$updateBody,  ['valueInputOption' => 'USER_ENTERED']);
                            return true;
                        } catch(Exception $e){
                            // dd('mm');                           
                            dd($e->getMessage());
                        }
                       
                

                     }catch(Exception $e){                        
                        dd($e->getMessage());
                     }
                                
                                
       
       
       
            }catch(Exception $e)
            {                
                dd($e->getMessage());
            }
       
   
        
    }
    
    public function pushNewHostToSheet($host_name,$sheet_user_details)
    {
       
        $access_token = $sheet_user_details->token_details;
        $this->client->setAccessToken(json_decode($access_token,true));
        if ($this->client->isAccessTokenExpired()) {
            $this->client->refreshToken($sheet_user_details->refresh_token);
            $token = $this->client->fetchAccessTokenWithRefreshToken();
            $token_array = ["token_details"=>json_encode($token ),"refresh_token"=>$token["refresh_token"]];
            
           }
        
         $sheet_id = $sheet_user_details->sheet_id;           
         $sheetrequests[] = new \Google_Service_Sheets_Request([
             'addSheet' => [
                 'properties' => [
                     'title' => $host_name
                 ]
             ]
           ]);
           $array =  ['Name','Email','Phone No','Message','Date','Time','Page','Category'];
          
           $range_obj = new \Google_Service_Sheets_ValueRange([
             'range' =>  $host_name.'!A:ZZZ',
             'majorDimension' => 'ROWS',
             'values' =>[$array],
             
         ]);
         $range = $host_name.'!A:ZZZ';
       
        try{
         $batchUpdateRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
             'requests' => $sheetrequests
         ]);
         $service = new \Google_Service_Sheets($this->client);
         $result = $service->spreadsheets->batchUpdate($sheet_id,$batchUpdateRequest);
         $addedSheetId = $result->replies[0]->addSheet->properties->sheetId;
         $myRange = [
            'sheetId' => $addedSheetId, // IMPORTANT: sheetId IS NOT the sheets index but its actual ID
            "startRowIndex" => 0,
            "endRowIndex" => 1,
        ];
        $format = [
            
            'textFormat' => [
            'bold' => true,
            'fontSize' => 12
            ]
         ];
        $requestsUpdateHeadingFormat = [
            new \Google_Service_Sheets_Request([
                'repeatCell' => [
                    'fields' => 'userEnteredFormat.textFormat',
                    'range' => $myRange,
                    'cell' => [
                        'userEnteredFormat' => $format,
                    ],
                ],
            ])
        ];
        $batchUpdaterequestsUpdateHeadingFormat = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => $requestsUpdateHeadingFormat
          ]);
          try{
            $result = $service->spreadsheets->batchUpdate($sheet_id, $batchUpdaterequestsUpdateHeadingFormat);
        }
        catch(Exception $e){
            // dd($e->getMessage());
        }
        
             try{
                 $service->spreadsheets_values->append($sheet_id,$range,$range_obj,  ['valueInputOption' => 'USER_ENTERED']);
             }catch(Exception $e)
             {
                 
             }
        
        }catch(Exception $e)
        {

        }
        

    }
    public function getFolderList($sheet_details)
    {
       if(!empty($sheet_details))
       {
            if(empty($sheet_details->sheet_folder_id))
            {
                $access_token = $sheet_details->token_details;
                $this->client->setAccessToken(json_decode($access_token,true));
                if ($this->client->isAccessTokenExpired()) {
                    $this->client->refreshToken($sheet_details->refresh_token);
                    $token = $this->client->fetchAccessTokenWithRefreshToken();
                    $token_array = ["token_details"=>json_encode($token ),"refresh_token"=>$token["refresh_token"]];
                    
                   }
                
              

                $optParams = array( 
                    'q' => "mimeType contains 'application/vnd.google-apps.folder' and 'root' in parents and trashed=false"
                ); 
                $driveService = new \Google_Service_Drive($this->client);
                $filesandfolders = $driveService->files->listFiles( $optParams);
                $files = $filesandfolders->files;
                return $files;
            }
            else
            {
                return null;
            }
       }
       else{

          return null;
       }
        
        
    }
    public function deleteThisSheet($sheet_details,$host_name)
    { 
        if(!empty($sheet_details->token_details))
        {
            $host_name = trim($host_name);
            $sheetId ='';
            $access_token = $sheet_details->token_details;
            $this->client->setAccessToken(json_decode($access_token,true));
            if ($this->client->isAccessTokenExpired()) {
                $this->client->refreshToken($sheet_details->refresh_token);
                $token = $this->client->fetchAccessTokenWithRefreshToken();
                $token_array = ["token_details"=>json_encode($token ),"refresh_token"=>$token["refresh_token"]];
                
               }
               $spreadsheet_id = $sheet_details->sheet_id; 
               $service = new \Google_Service_Sheets($this->client);
               $response = $service->spreadsheets->get($spreadsheet_id);
              // dd($response->getSheetsByName($host_name)); 
               $sheets= $response->sheets;
               foreach($sheets as $sheet)
               {
                   
                   $sheetname = $sheet->properties->title;
                   if(trim($sheetname)==trim($host_name))
                   {
                    $sheetId = $sheet->properties->sheetId;
                   }
               }
               if(!empty($sheetId))
               {
                $deleterequest = [
           
                    new \Google_Service_Sheets_Request([
                        'deleteSheet' => [
                            
                                'sheet_id' => $sheetId,
                            
                        ]
                    ])
                ];
                $batchdeleteteRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                    'requests' => $deleterequest
                ]);
                $service = new \Google_Service_Sheets($this->client);
                try{
                    $result = $service->spreadsheets->batchUpdate($spreadsheet_id,$batchdeleteteRequest);
                }
                catch(Exception $e) 
                {

                }
                
               }      
        }    
               
    }
    
}

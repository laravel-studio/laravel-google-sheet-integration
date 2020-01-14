<?php

namespace laravelstudio\laravelgooglesheetintegration;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use laravelstudio\laravelgooglesheetintegration\contracts\googleSheetInterface;
use laravelstudio\laravelgooglesheetintegration\contracts\userGoogleSheetInterface;
use laravelstudio\laravelgooglesheetintegration\contracts\UserInterface;
use laravelstudio\laravelgooglesheetintegration\models\googleSheetUser as SystemUser;
use App;

class GoogleSheetController extends Controller
{
    public $client;
    public $user, $userGoogleSheetInterface, $googleSheetInterface, $systemUser;
    public function __construct(userGoogleSheetInterface $userGoogleSheetInterface, UserInterface $user, googleSheetInterface $googleSheetInterface, Request $request, SystemUser $systemUser)
    {        
        $this->userGoogleSheetInterface = $userGoogleSheetInterface;
        $this->user = $user;
        $this->googleSheetInterface = $googleSheetInterface;
        $this->systemUser = $systemUser;
        $this->client = new \Google_Client();
        $this->client->setAuthConfig(config('googlesheet.config'));
        $this->client->setAccessType('offline');
        $redirect_uri = url('userAuthenticated');
        $this->client->setRedirectUri($redirect_uri);
        $this->client->setScopes(
            [
                \Google_Service_Drive::DRIVE,
                \Google_Service_Storage::CLOUD_PLATFORM,
                'https://www.googleapis.com/auth/spreadsheets',
            ]
        );
        $getActionStr = $request->route()->getActionName();
        $getActionArray = explode('@', $getActionStr);
        // dd($getActionArray[0]);

        if ($getActionArray[0] == 'laravelstudio\laravelgooglesheetintegration\GoogleSheetController') {
            $request->attributes->add(['client' => $this->client]);
            $this->middleware('googleAuth')->except(['googlesheet-signin', 'authenticateUser', 'userAuthenticated', 'googleSignOut']);
        } else {
            $user_id = \Auth::user()->id;
            $userTokenDetails = $this->systemUser->whereId($user_id)->with('userGoogleSheet')->first();

            if (empty($userTokenDetails->userGoogleSheet)) {
                abort("404");
            }
            // dd($tokendetails->userGoogleSheet);
            $tokendetails = $userTokenDetails->userGoogleSheet;
            // dd($tokendetails->token_details);
            if (!empty($tokendetails)) {
                //    dd($request->all());

                $access_token = $tokendetails->token_details;
                $this->client->setAccessToken(json_decode($access_token, true));
                if ($this->client->isAccessTokenExpired()) {
                    $this->client->refreshToken($tokendetails->refresh_token);
                    $token = $this->client->fetchAccessTokenWithRefreshToken();
                    $token_array = ["token_details" => json_encode($token), "refresh_token" => $token["refresh_token"]];
                    $this->systemUser->find($user_id)->userGoogleSheet()->update($token_array);
                }

            }
        }

    }
    public function index()
    {
        return view('laravelgooglesheetintegration::signin');
    }
    public function authenticateUser()
    {
        $authUrl = $this->client->createAuthUrl();
        return redirect($authUrl);
    }
    public function userAuthenticated()
    {
        if (isset($_GET['code'])) {            
            $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
            if (isset($token["refresh_token"])) {

                if ($this->userGoogleSheetInterface->isTokenValid($token["refresh_token"])) {
                    $sheet_details = $this->user->getGoogleSheetDetails()->first();
                    $token_array = ["token_details" => json_encode($token), "refresh_token" => $token["refresh_token"]];
                    if (empty($sheet_details)) {
                        $this->user->createGooleToken($token_array);
                    } else {
                        $this->user->updateGooleToken($token_array);
                    }
                    return redirect('getFileOrFolder');
                }
            } else {
                $user_id = auth()->user()->id;
                $sheet_details = $this->user->getGoogleSheetDetailsByUserId($user_id)->first();
                if (!empty($sheet_details)) {
                    return redirect('getFileOrFolder');
                } else {
                    Session::flash('error-message', "You Are Already Logged In <a style='float:right' class='google-signout' href='" . route("googleSignOut") . "' target='_blank'>Logout First</a>");
                    return redirect()->back();
                }

            }

        }

    }
    public function getTokenDetails()
    {
        $sheet_details = $this->user->getGoogleSheetDetails()->first();
        return json_encode($sheet_details);
    }

    public function getFileOrFolder()
    {
        return view('laravelgooglesheetintegration::folderList');
    }

    public function storeFolderId(Request $request)
    {
        $type = $request->input('type');
        $sheet_id = $request->input('id');
        $sheet_name = $request->input('name');
        if ($type == 'folder') {
            $data = ["sheet_folder_id" => $sheet_id, "sheet_folder_name" => $sheet_name, "sheet_id" => null, "sheet_name" => null];
        } elseif ($type == 'document') {
            $data = ["sheet_folder_id" => null, "sheet_folder_name" => null, "sheet_id" => $sheet_id, "sheet_name" => $sheet_name];
        }
        $this->user->storeFolder($data);
        return json_encode(['status' => 'success']);

    }

    public function setAction()
    {
        $data = '{
            "sheet_name":"Test Google Sheet",
            "data": {
                "Itobuz1": [{
                "Accuracy": "30",
                "Latitude": "53.2778273",
                "Longitude": "-9.0121648",
                "Timestamp": "Fri Jun 28 2013 11:43:57 GMT+0100 (IST)"
            }, {
                "Accuracy": "30",
                "Latitude": "53.2778273",
                "Longitude": "-9.0121648",
                "Timestamp": "Fri Jun 28 2013 11:43:57 GMT+0100 (IST)",
                "Location": "Kolkata"
            }],
               "Itobuz2": [{
                "Accuracy": "30",
                "Latitude": "53.2778273",
                "Longitude": "-9.0121648",
                "Timestamp": "Fri Jun 28 2013 11:43:57 GMT+0100 (IST)",
                "Code": "47852"
            }, {
                "Accuracy": "30",
                "Latitude": "53.2778273",
                "Longitude": "-9.0121648"
            }]
            }
        }';

        $return = $this->updatesettings($data);
        return $return;
    }

    public function updatesettings($sheetnames)
    {
        //return $request->all();
        //dd(request("settings"));

        $sheetnames = json_decode($sheetnames, true);
        // dd($sheetnames);
        $sheet_details = $this->user->getGoogleSheetDetails()->first();
        // dd($sheet_details);

        if ($sheet_details->sheet_folder_id != null && $sheet_details->sheet_id == null) {
            $column_heading = [];
            if (isset($sheetnames['data'])) {
                foreach ($sheetnames['data'] as $tabs => $columArr) {
                    $heading = [];
                    foreach ($columArr as $key => $array) {
                        foreach ($array as $index => $val) {
                            $heading[$tabs][] = $index;
                        }
                        // dd($heading);
                    }
                    $new = [];
                    foreach (array_unique($heading[$tabs]) as $value) {
                        $new[] = $value;
                    }
                    $column_heading[$tabs] = $new;
                }
            } else {

                return array('status' => false, 'msg' => 'Data attribute is missing');
            }
            // dd($column_heading);
            $column_value = [];
            if (isset($sheetnames['data'])) {
                foreach ($sheetnames['data'] as $tabs => $columArr) {
                    $arr1 = [];
                    $i = 1;
                    foreach ($columArr as $key1 => $array) {
                        $processed_update_array = [];
                        $sheet_insert_array = [];
                        $arr1[0] = $column_heading[$tabs];
                        foreach ($array as $key => $val) {
                            $newkey = $key;
                            $processed_update_array[$newkey] = $val;
                        }
                        foreach ($column_heading[$tabs] as $v) {
                            if (isset($processed_update_array[$v])) {
                                $sheet_insert_array[] = $processed_update_array[$v];
                            } else {
                                $sheet_insert_array[] = '';
                            }
                        }
                        $arr1[$i] = $sheet_insert_array;
                        $i++;
                    }
                    // dd($arr1);
                    $column_value[$tabs] = $arr1;
                }
            } else {
                return array('status' => false, 'msg' => 'Data attribute is missing');
            }

            $sheetnames['data'] = $column_value;
            // dd($sheetnames);

            $ret = $this->createGoogleSheet($sheet_details->sheet_folder_id, $sheetnames);
        } else {

            $ret = $this->updateTheSheet($sheetnames, $sheet_details->sheet_id);
        }

        return $ret;
    }

    public function createGoogleSheet($folder_id, $sheetnames)
    {
        $service = new \Google_Service_Drive($this->client);
        $googleServiceDriveDriveFile = new \Google_Service_Drive_DriveFile();
        $googleServiceDriveDriveFile->setMimeType('application/vnd.google-apps.spreadsheet');
        if (isset($sheetnames['sheet_name']) && $sheetnames['sheet_name'] != '') {
            $googleServiceDriveDriveFile->setName($sheetnames['sheet_name']);
        } else {
            $googleServiceDriveDriveFile->setName('Unnamed');
        }

        $googleServiceDriveDriveFile->setParents([$folder_id]);
        try {
            $res = $service->files->create($googleServiceDriveDriveFile);
            $sheetId = $res->getId();
            $this->userGoogleSheetInterface->updateGoogleSheetId($sheetId);
            $sheetrequests = array();
            $sheetrheadings = array();
            if (isset($sheetnames['data'])) {
                foreach ($sheetnames['data'] as $tabs => $columArr) {
                    $newarraytemp = array();
                    $sheetrequests[] = new \Google_Service_Sheets_Request([
                        'addSheet' => [
                            'properties' => [
                                'title' => $tabs,
                            ],
                        ],
                    ]);
                    foreach ($columArr as $key => $val_array) {
                        $newarraytemp[$key]["obj"] = new \Google_Service_Sheets_ValueRange([
                            'range' => $tabs . '!A:ZZZ',
                            'majorDimension' => 'ROWS',
                            'values' => [$val_array],

                        ]);
                        $newarraytemp[$key]["range"] = $tabs . '!A:ZZZ';
                    }
                    $sheetrheadings[] = $newarraytemp;
                }
            } else {
                return array('status' => false, 'msg' => 'Data attribute is missing');
            }
            try {
                $batchUpdateRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                    'requests' => $sheetrequests,
                ]);
                $service = new \Google_Service_Sheets($this->client);
                $result = $service->spreadsheets->batchUpdate($sheetId, $batchUpdateRequest);
                foreach ($result->replies as $reply) {
                    $addedSheetId = $reply->addSheet->properties->sheetId;
                    $myRange = [
                        'sheetId' => $addedSheetId, // IMPORTANT: sheetId IS NOT the sheets index but its actual ID
                        "startRowIndex" => 0,
                        "endRowIndex" => 1,
                    ];
                    $format = [

                        'textFormat' => [
                            'bold' => true,
                            'fontSize' => 12,
                        ],
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
                        ]),
                    ];
                    $batchUpdaterequestsUpdateHeadingFormat = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                        'requests' => $requestsUpdateHeadingFormat,
                    ]);

                    // run batchUpdate
                    try {
                        $result = $service->spreadsheets->batchUpdate($sheetId, $batchUpdaterequestsUpdateHeadingFormat);
                    } catch (Exception $e) {
                        dd($e->getMessage());
                    }
                }
                // dd($sheetrheadings);
                foreach ($sheetrheadings as $array) {
                    foreach ($array as $sh) {
                        try {
                            $service->spreadsheets_values->append($sheetId, $sh["range"], $sh["obj"], ['valueInputOption' => 'USER_ENTERED']);
                        } catch (Exception $e) {
                            dd($e->getMessage());
                        }
                    }

                }
                $deleterequest = [

                    new \Google_Service_Sheets_Request([
                        'deleteSheet' => [

                            'sheet_id' => 0,

                        ],
                    ]),
                ];
                $batchdeleteteRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                    'requests' => $deleterequest,
                ]);
                $service = new \Google_Service_Sheets($this->client);
                $result = $service->spreadsheets->batchUpdate($sheetId, $batchdeleteteRequest);
                return array('status' => true, 'msg' => 'Google sheet has been created successfully');
            } catch (Exception $e) {
                dd($e->getMessage());
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }

    }
    public function updateTheSheet($sheetnames, $sheet_id)
    {
        // $google_sheet_details = $this->user->getGoogleSheetDetailsByUserId(Auth::user()->id)->first();
        $google_sheet_details = $this->user->getGoogleSheetDetails()->first();
        // dd($google_sheet_details);
        $requestData = array();
        $requestDataOther = array();
        $sheetrequest_array = array();
        // dd($sheetnames);
        if (isset($sheetnames['data'])) {
            foreach ($sheetnames['data'] as $key => $valArray) {
                $sheetrequest_array = [];
                foreach ($valArray as $index => $val) {
                    $this->googleSheetInterface->updateToSheetByUser($key, $val, $google_sheet_details);
                }
            }
            return array('status' => true, 'msg' => 'Google sheet has been updated successfully');
        } else {
            // dd('kk');
            return array('status' => false, 'msg' => 'Data attribute is missing');
        }

    }

    public function googleSignOut()
    {
        $user_id = auth()->user()->id;
        $this->userGoogleSheetInterface->removeDataByUserId($user_id);
        return redirect('https://accounts.google.com/logout');
    }

}

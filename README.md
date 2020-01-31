# laravel-google-sheet-integration
# Installation:
1. `composer require laravel-studio/laravel-google-sheet-integration`

2.  Add this code under providers array
`laravelstudio\laravelgooglesheetintegration\GoogleSheetServiceProvider::class,`
and also add this under aliases array of config > app.php
`'GoogleSheet' => laravelstudio\laravelgooglesheetintegration\facades\googlesheet::class`

3. You have to add this in $routeMiddleware in Kernal.php 
`'googleAuth' => \laravelstudio\laravelgooglesheetintegration\middleware\googleAuth::class,`
`'checkUserAuth' => \laravelstudio\laravelgooglesheetintegration\middleware\checkUserAuth::class,`

4. Set GOOGLE_SHEET_RETURN_URL key value in .env file like :
`GOOGLE_SHEET_RETURN_URL=http://localhost:8000/<your-callback-route>`

5. Run `composer dump-autoload`
6. Run `php artisan vendor:publish`
7. Run `php artisan migrate`

## Use:
Your google auth config file will be under config > googlesheet.php. Place your auth credentials here.

Packages views will be under resources > views > vendor > googlesheet, make your customization here.

------------


**Note: if you are not going to use package’s layout page, then make sure this scripts are included at your main layout or copy from package’s layout page.**

**Css File:**

`<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">`
`<link rel="stylesheet" href="{{ asset('/vendor/googlesheet/css/custom.css') }}">`


**Js file:**

`<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>`

After including those scripts include @stack('google-drive-scripts')
  and  @stack('auto-auth-script') at end of the script.
  

------------

**Show google sheet auth button **
  To add google auth button use this in your controller file (make sure that you have looged in user of your application) 
  ` return view('laravelgooglesheetintegration::signin');`
  
**Save or update google sheet data:**
use this facade in controller `use GoogleSheet;`
then use this code: 
`GoogleSheet::updatesettings($data);`
 
**Here is the sample data format:**
    ``` $data = '{
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
            }';```

**Note: if u choose folder then it creates new sheet or if you choose existing file then it will be updated.**


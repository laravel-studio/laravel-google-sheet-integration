
    <div id="result"></div>

    @push('google-drive-scripts')
    <script type="text/javascript">    

    // Replace with your own project number from console.developers.google.com.
    // See "Project number" under "IAM & Admin" > "Settings"
    var appId = "{{ config('googlesheet.project_number') }}";

    // Scope to use to access user's Drive items.
    var scope = ['https://www.googleapis.com/auth/drive.file'];

    var pickerApiLoaded = false;
    var oauthToken;

    // Use the Google API Loader script to load the google.picker script.
    function loadPicker() {
      gapi.load('auth', {'callback': onAuthApiLoad});
      gapi.load('picker', {'callback': onPickerApiLoad});
    }

    function onAuthApiLoad() {    

       $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
        url: "{{ route('getTokenDetails') }}",
        cache: false,
        type: "GET",
        dataType: "json",
        success: function(result){
          // console.log(JSON.parse(result.token_details));
          var tokens = JSON.parse(result.token_details);
          // console.log(tokens.access_token);
          oauthToken = tokens.access_token;
          createPicker();
        }
      });    
      
    }

    function onPickerApiLoad() {
      pickerApiLoaded = true;
      createPicker();
    }
    

    // Create and render a Picker object for searching images.
    function createPicker() {

      if (pickerApiLoaded && oauthToken) {

        var folderView = new google.picker.DocsView(google.picker.ViewId.DOCS).setIncludeFolders(true).setSelectFolderEnabled(true);
        folderView.setMimeTypes("application/vnd.google-apps.folder,application/vnd.google-apps.spreadsheet");
        var picker = new google.picker.PickerBuilder()
            .enableFeature(google.picker.Feature.MINE_ONLY)
            .enableFeature(google.picker.Feature.NAV_HIDDEN)
            .setAppId(appId)
            .setOAuthToken(oauthToken)
            .addView(folderView)
            // .setDeveloperKey(developerKey)
            .setCallback(pickerCallback)
            .build();
         picker.setVisible(true);
      }
    }

    // A simple callback implementation.
    function pickerCallback(data) {
      if (data.action == google.picker.Action.PICKED) {
        // console.log(data.docs[0]);
        sendData(data);
      }
    }

    function sendData(data) {
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
        url: "{{ route('storeFolder') }}",
        cache: false,
        type: "POST",
        data: {type: data.docs[0].type, id: data.docs[0].id, name: data.docs[0].name},
        dataType: "json",
        success: function(result){
          // console.log(result.status);
          if(result.status == 'success'){
            window.location.href = "{{ $redirectUrl }}";
          }
        }
      });
    }
    </script>
    <script type="text/javascript" src="https://apis.google.com/js/api.js?onload=loadPicker"></script>
    @endpush

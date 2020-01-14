<div>
    @if(config('googlesheet.auto_auth'))
        <a id="btn-dummy-signin" href="javascript:void(0)" class="btn btn-block btn-success btn-dummy-signin"><i class="fa fa-spinner fa-pulse loader"></i><span>Please wait!! Trying to auto authenticate</span></a>
        <a id="btn-signin" href="{{ route('authenticateUser') }}" style="display:none" class="btn btn-block btn-success btn-signin">Please click to signin Goolge</a>
    @else
        <a id="btn-signin-sgl" href="{{ route('authenticateUser') }}" class="btn btn-block btn-success">Please click to signin Goolge</a>
    @endif

</div>

@php
//dd(config('googlesheet.auto_auth'))
@endphp


@push('auto-auth-script')
<script src="https://apis.google.com/js/client:platform.js?onload=start" async defer></script>
<script>
function start() {
      gapi.load('auth2', function() {
        auth2 = gapi.auth2.init({
          client_id: "{{ config('googlesheet.config.client_id') }}",
         
        }).then(() => {            
                auth2 = gapi.auth2.getAuthInstance();
                // console.log(auth2.isSignedIn.get()); //now this always returns correctly
                if("{{ config('googlesheet.auto_auth') }}"){
                    setTimeout(() => {
                        if(auth2.isSignedIn.get() == true){
                            window.location.href = "{{ route('authenticateUser') }}";
                        }else{
                            $("#btn-dummy-signin").css('display','none');
                            $("#btn-signin").css('display','block');
                        }
                    },500);
                }else{                    
                    if(auth2.isSignedIn.get() == true){
                        $("#btn-signin-sgl").html('To change your path, click on it');
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
                                // console.log(result);
                                if(result.sheet_name != null && result.sheet_name.length > 0){
                                    $('#show-file-container').css('display','block');
                                    $('#show-file').html(result.sheet_name);
                                }else if(result.sheet_folder_name != null && result.sheet_folder_name.length > 0){
                                    $('#heading-container').html('Last Updated Folder');
                                    $('#show-file').html(result.sheet_folder_name);
                                    $('#show-file-container').css('display','block');
                                }
                            }
                        });
                    }                    
                }

        });
    });
}
</script>
@endpush


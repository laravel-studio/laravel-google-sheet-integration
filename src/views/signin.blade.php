@extends('laravelgooglesheetintegration::layout')
@section('content')
<section class="content ">
    <div class="row m-t-40">
        <div class="col-md-12">
            <h3 class="text-center m-b-20">Google Authentication</h3>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                    @include('laravelgooglesheetintegration::signinButtons')
                    </div>
                </div>
            </div>
            <div id="show-file-container" style="display:none">
                <h3 id="heading-container">Last Updated File</h3>
                <h6 id="show-file"></h6>
            </div>
        </div>
    </div>
</section>
@endsection


@extends('laravelgooglesheetintegration::layout')
@section('content')
<section class="content">
    @include('laravelgooglesheetintegration::redirectFrom', ['redirectUrl' => config('googlesheet.return_url')])
</section>
@endsection
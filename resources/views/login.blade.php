@extends('layouts.master')
@section('content')
<div id="app">
    <login login-uri="{{ route('doLogin') }}"></login>
</div>
@stop

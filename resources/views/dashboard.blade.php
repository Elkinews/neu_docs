@extends('layouts.master')
@section('content')
    <div id="app">
        <dashboard programs-json="{{ $programsJson }}" bulk-actions="{{ $bulkActions }}"></dashboard>
    </div>
@stop

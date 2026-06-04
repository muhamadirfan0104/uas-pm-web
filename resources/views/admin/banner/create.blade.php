@extends('layouts.admin')
@section('title','Tambah Banner - SiTahu')
@section('content')
<div class="hero"><div><h1>Tambah Banner</h1><p>Banner akan tersimpan di database dan storage public.</p></div></div>
<form method="POST" action="{{ route('admin.banner.store') }}" enctype="multipart/form-data">@include('admin.banner._form')</form>
@endsection

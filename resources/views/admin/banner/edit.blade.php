@extends('layouts.admin')
@section('title','Edit Banner - SiTahu')
@section('content')
<div class="hero"><div><h1>Edit Banner</h1><p>Perbarui banner mobile.</p></div></div>
<form method="POST" action="{{ route('admin.banner.update',$banner) }}" enctype="multipart/form-data">@method('PUT') @include('admin.banner._form')</form>
@endsection

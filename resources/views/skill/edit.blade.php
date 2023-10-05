@extends('layout.main')
@section('content')
<h1>Thêm mới gói nạp</h1>
<form action="{{ route('skill.edit',['id'=>$skill->id]) }}" enctype="multipart/form-data" method="post">
    @csrf
    <div class="form-group mt-3 mt-3">
        <label for="my-input">Tên kỹ năng:</label>
        <input id="my-input" class="form-control" type="text" name="name" value="{{ $skill->name }}">
    </div>
    <div class="form-group mt-3">
        <label for="my-input">Mô tả:</label>
        <input id="my-input" class="form-control" type="text" name="description" value="{{$skill->description}}">
    </div>
    <div class="form-group mt-3">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
@endsection
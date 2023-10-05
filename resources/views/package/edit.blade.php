@extends('layout.main')
@section('content')
<h1>Chỉnh sửa gói nạp</h1>
<form action="{{ route('package.edit',['id'=>$package->id]) }}" enctype="multipart/form-data" method="post">
    @csrf
    <div class="form-group mt-3 mt-3">
        <label for="my-input">Tên gói nạp:</label>
        <input id="my-input" class="form-control" type="text" name="title" value="{{ $package->title}}">
    </div>
    <div class="form-group mt-3">
        <label for="my-input">Số coin:</label>
        <input id="my-input" class="form-control" type="text" name="coin" value="{{ $package->coin}}">
    </div>
    <div class="form-group mt-3">
        <label for="my-input">Giá tiền:</label>
        <input id="my-input" class="form-control" type="text" name="price" value="{{ $package->price}}">
    </div>
    <div class="form-group mt-3">
        <label for="my-input">Giảm giá:</label>
        <input id="my-input" class="form-control" type="text" name="reduced_price" value="{{ $package->reduced_price}}">
    </div>
    <div class="form-group mt-3">
        <label for="my-input">Trạng thái:</label>
        <input id="my-input" class="form-check-input" type="radio" name="status" {{$package->status==1?"checked":""}} value="Đã kích hoạt">Đã kích hoạt
        <input id="my-input" class="form-check-input" type="radio" name="status" {{$package->status==2?"checked":""}} value="Chưa kích hoạt">Chưa kích hoạt
    </div>
    <div class="form-group mt-3">
        <label for="my-input">Loại tài khoản:</label>
        <input id="my-input" class="form-control" type="text" name="type_account" value="{{ $package->type_account}}">
    </div>
    <div class="form-group mt-3">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
@endsection
@extends('layout.main')
@section('content')
<h1>Quản lý gói nạp</h1>
<a href="{{ route('package.add') }}" class="btn btn-success mt-3">Thêm mới</a>
<table class="table mt-3">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Tên gói nạp</th>
            <th scope="col">Số coin</th>
            <th scope="col">Giá tiền</th>
            <th scope="col">Giảm giá</th>
            <th scope="col">Trạng thái</th>
            <th scope="col">Loại tài khoản</th>
            <th scope="col">Ngày tạo</th>
            <th scope="col">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($package as $item)
        <tr>
            <th scope="row">{{ $item->id }}</th>
            <td>{{ $item->title }}</td>
            <td>{{ $item->coin }}</td>
            <td>{{ $item->price }}</td>
            <td>{{ $item->reduced_price }}</td>
            <td>{{ $item->status }}</td>
            <td>{{ $item->type_account }}</td>
            <td>{{ $item->created_at }}</td>
            <td>
                <a href="{{ route('package.delete',['id'=>$item->id]) }}" class="btn btn-danger">Xoá</a>
                <a href="{{ route('package.edit',['id'=>$item->id]) }}" class="btn btn-primary">Sửa</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
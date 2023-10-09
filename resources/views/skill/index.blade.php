@extends('layout.main')
@section('content')
<h1>Quản lý kỹ năng</h1>
<a href="{{ route('skill.add') }}" class="btn btn-success mt-3">Thêm mới</a>
<table class="table mt-3">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Tên kỹ năng</th>
            <th scope="col">Mô tả</th>
            <th scope="col">Ngày tạo</th>
            <th scope="col">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($skill as $item)
        <tr>
            <th scope="row">{{ $item->id }}</th>
            <td>{{ $item->name }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->created_at }}</td>
            <td>
                <a href="{{ route('skill.delete',['id'=>$item->id]) }}" class="btn btn-danger">Xoá</a>
                <a href="{{ route('skill.edit',['id'=>$item->id]) }}" class="btn btn-primary">Sửa</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
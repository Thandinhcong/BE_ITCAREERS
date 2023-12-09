@extends('layout.main')
@section('content')
<section class="contact-section">
    <div class="auto-container">
        <div class="contact-form default-form">
            <h3>Khôi Phục Mật Khẩu</h3>
            <form method="post" action="" id="email-form">
                @csrf
                <div class="row">
                    <form action="">
                        <div class="col-lg-12 col-md-12 col-sm-12 form-group" style="margin-bottom: 30px">
                            <label>Email</label>
                            <input type="text" name="email" class="subject" placeholder="Mời nhập email cần khôi phục" required>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                            <button class="theme-btn btn-style-one" type="submit">Khôi Phục</button>
                        </div>
                    </form>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
<section class="contact-section">
    <div class="auto-container">
        <div class="contact-form default-form">
            <h3>Khôi Phục Mật Khẩu</h3>
            <form method="post" action="" id="email-form">
                <div class="row">
                    <form action="{{route('get_pass_company')}}">
                        <div class="col-lg-12 col-md-12 col-sm-12 form-group" style="margin-bottom: 30px">
                            <label>Mã Xác Nhận</label>
                            <input type="text" name="email" class="subject" placeholder="Mời nhập email cần khôi phục">
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 form-group" style="margin-bottom: 30px">
                            <label>Mật Khẩu Mới</label>
                            <input type="password" name="password" class="subject" placeholder="Mật Khẩu Mới">
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 form-group" style="margin-bottom: 30px">
                            <label>Nhập Lại Mật Khẩu</label>
                            <input type="password" name="password2" class="subject" placeholder="Mật Khẩu Mới">
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                            <button class="theme-btn btn-style-one" type="submit">Đặt Lại Mật Khẩu</button>
                        </div>
                    </form>
                </div>
            </form>
        </div>
    </div>
</section>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            margin: auto;
            max-width: 550px;
            line-height: 30px;
        }

        header {
            background-color: rgb(240, 238, 238);
            padding: 20px 10px;
            color: rgb(74, 74, 74);
        }

        img {
            display: block;
            /* Để tránh khoảng trắng dưới ảnh */
            margin: 0 auto;
            /* Căn ảnh giữa theo chiều ngang */
        }

        main {
            margin: 0 20px;
        }

        div {
            margin: 10px 0;
        }

        p {
            text-align: center;
        }
    </style>
</head>

<body>
    <header>
        <img src="{{$manage_web->logo}}" alt="">
        <p>{{$manage_web->name_web}} là nền tảng Job Search Engine giúp tìm việc tốt nhất,
            đặc biệt có công cụ Tạo CV PRO đặc trưng theo ngành nghề
            độc đáo, có hơn 1 triệu người sử dụng thường xuyên.</p>
    </header>
    <main>
        <div>Chào bạn {{$profile->name}},</div>
        Nhà tuyển dụng đã xem và đang đánh giá hồ sơ mà bạn đã ứng tuyển vào vị trí <b>{{$profile->job_post_title}}</b>
        của nhà tuyển dụng<b>{{$profile->company_name}}</b>
        trên nền tảng tuyển dụng {{$manage_web->name_web}} vào lúc 16:43 06/09/2023.
    </main>
</body>

</html>
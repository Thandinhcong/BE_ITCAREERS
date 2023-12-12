SETUP Laravel 10

    Kiểm tra phiên bản php: php -v ( từ 8.1 - 8.2)
    Kiểm tra phiên bản composer: composer -v ( > 2.0)

    1: composer install

    2: cp .env.example .env

    3: php artisan key:generate

    4: php artisan migrate

    6: php artisan ser

http code
200: tìm thấy bản ghi
201: update,create thành công
404: Không tìm thấy bản ghi nào
422-400: Dữ liệu trong form gửi đi có vấn đề
500: lỗi bên phía server khi gửi form


    php artisan migrate:fresh
    php artisan passport:install
    php artisan db:seed
    composer dump-autoload

    Nếu lỗi chạy:
    php artisan config:cache
    php artisan cache:clear
    //Nếu chạy dòng 25 lỗi: 
    php artisan passport:install --uuids
 chay mail: php artisan queue:work
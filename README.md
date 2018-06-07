# Backend ValagooCar User base on Lumen (PHP)

instalasi dengan command prompt :
<h3>1. clone source dengan git</h3>
    cd C:/xampp/htdocs<br>
    git clone https://github.com/Haeruszaman/valagoocar-user.git
<h3>2. install vendor dengan composer</h3>
    cd valagoocar-user<br>
    composer config -g repo.packagist composer https://packagist.phpcomposer.com<br>
    composer install
<h3>3. setting database di file .env.example</h3>
    rename <b>.env.example</b> ke <b>.env</b> kemudian cari kode berikut :

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE={your DB name}
    DB_USERNAME={your DB username}
    DB_PASSWORD={your DB password}
<h3>4. demo dengan postman</h3>
    import file : <b>valagoocar-backend.postman_collection.json</b> ke <a href="https://www.getpostman.com/" target="_blank">postman</a>

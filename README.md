# free-market-app

## 環境構築

### Docker ビルド

1. ローカル環境で以下構成のディレクトリを作成する

   ```
   .
   ├── docker
   │   ├── mysql
   │   │   ├── data
   │   │   └── my.cnf
   │   ├── nginx
   │   │   └── default.conf
   │   └── php
   │   ├── Dockerfile
   │   └── php.ini
   ├── docker-compose.yml
   └── src
   ```

2. 以下のファイルに設定を記述する

   - docker-compose.yml の作成

     - Nginx の設定

     - PHP の設定

     - My SQL の設定

     - phpMyAdmin の設定

     - MailHog の設定

3. docker-compose up -d --build

### Laravel 環境構築

1. docker-compose exec php bash

2. composer -v

3. composer create-project "laravel/laravel=8.\*" . --prefer-dist

4. src/config/app.php ファイルの'timezone'を修正する

5. php artisan tinker

6. echo Carbon\Carbon::now()

7. .env ファイルに記載されている以下の環境変数を変更

   - DB_HOST
   - DB_DATABASE
   - DB_USERNAME
   - DB_PASSWORD
   - MAIL_FROM_ADDRESS
   - STRIPE_KEY
   - STRIPE_SECRET
   - CASHIER_CURRENCY

8. php artisan key:generate

9. php artisan migrate

## 使用技術（実行環境）

- PHP 7.4.9
- Laravel 8.83.29
- MySQL 8.0.26
- nginx 1.21.1

## ER 図

## URL

- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/
- MailHog：http://localhost:8025/

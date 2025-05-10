# free-market-app

## 環境構築

### Docker ビルド

1. <!-- git clone後で書く -->

2. docker-compose up -d --build

### Laravel 環境構築

1. docker-compose exec php bash

2. composer -v

3. composer create-project "laravel/laravel=8.\*" . --prefer-dist

4. config/app.php ファイルの'timezone'を修正する

5. php artisan tinker

6. echo Carbon\Carbon::now()

7. .env ファイル内の下記環境変数を修正または追加する

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

### 単体テスト準備

1. テスト用 DB の準備

   1. 管理者権限で DB にログインする
      - mysql -u root -p
   2. テスト用 DB の作成
      - CREATE DATABASE demo_test;

2. config/database.php の以下項目を編集する

   - 'database' => 'demo_test',
   - 'username' => 'root',
   - 'password' => 'root',

3. テスト用の.env ファイルを作成する

   - cp .env .env.testing

4. .env.testing ファイルの以下項目を編集する

   - APP_ENV=test
   - APP_KEY=（値を空にする）
   - DB_DATABASE=demo_test
   - DB_USERNAME=root
   - DB_PASSWORD=root

5. テスト用アプリケーションキーを作成する

   - php artisan key:generate --env=testing

6. テスト用テーブルを作成する

   - php artisan migrate --env=testing

7. phpunit.xml の下記項目を編集する
   - <server name="DB_CONNECTION" value="mysql_test"/>
   - <server name="DB_DATABASE" value="demo_test"/>

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

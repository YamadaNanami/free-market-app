# free-market-app

## 環境構築

### Docker ビルド

1. git clone git@github.com:HabaNanami/free-market-app.git

2. docker-compose up -d --build

### Laravel 環境構築

1. docker-compose exec php bash

2. composer install

3. composer create-project "laravel/laravel=8.\*" . --prefer-dist

4. config/app.php ファイルの'timezone'を修正する

5. php artisan tinker

6. echo Carbon\Carbon::now()

7. .env.example ファイルから.env ファイルを作成し、環境変数を追加・変更する

   追加する環境変数
   - STRIPE_KEY="StripeのAPIキー　パブリックキー"
   - STRIPE_SECRET="StripeのAPIキー　シークレットキー"
   - CASHIER_CURRENCY=jpy
  
   変更する環境変数
   - DB_HOST=mysql
   - DB_DATABASE=laravel_db
   - DB_USERNAME=laravel_user
   - DB_PASSWORD=laravel_pass
   - MAIL_FROM_ADDRESS="送信元アドレスを設定する"

8. php artisan key:generate

9. php artisan migrate

10. Storage/app/publicに以下のディレクトリ構成を作成する
```
.
├── img
│   ├── item_img
│   ├── profile_img
└── └── temp
```
11. 10で作成したimgディレクトリ内に以下のimgディレクトリ内の画像を、item_imgディレクトリ内に以下のitem_imgディレクトリ内の画像を格納する
```
.
├── docker
├── img　←画像が格納されている対象のディレクトリ
│   ├── item_img ←画像が格納されている対象のディレクトリ
│   └── ...
├── src
├── README.md
└── docker-compose.yml
```
12. php artisan storage:link

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

7. phpunit.xml の下記のコメントアウトを外す
```
   <server name="DB_CONNECTION" value="mysql_test"/>
   <server name="DB_DATABASE" value="demo_test"/>
```

## 使用技術（実行環境）

- PHP 7.4.9
- Laravel 8.83.29
- MySQL 8.0.26
- nginx 1.21.1

## ER 図

<img src="ER.drawio.png">

## URL

- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/
- MailHog：http://localhost:8025/

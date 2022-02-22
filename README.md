# touchpay-package

## 開発環境(管理画面)

ローカルサーバーを docker で構築

```sh
docker-compose up # httpsで接続する場合
docker-compose -f docker-compose.yml up # httpsが不要な場合
```

## 開発環境(タブレット周り/個人別予約画面)
タブレット環境/個人画面環境を利用するためにはビルドモジュールであるyarnを利用する。  
yarnはnpmよりダウンロードできるので、Node.jsをインストールする必要がある。

1. [Node.js](https://nodejs.org/en/)をインストール
2. yarnをダウンロード
   `npm install -g yarn`
3. ビルドの実行  
   `yarn --cwd user-touchpay`:個人別予約画面  
   `yarn --cwd tablet-touchpay`:タブレット画面  
   `yarn --cwd forkitchen-touchpay`:厨房向け注文表示画面  
4. 開発サーバー起動  
   `yarn --cwd user-touchpay serve`:個人別予約画面  
   `yarn --cwd tablet-touchpay serve`:タブレット画面  
   `yarn --cwd forkitchen-touchpay serve`:厨房向け注文表示画面  
## url

他にポートがかぶってなければ、以下のURLで動く。  
(ポートがかぶってたら別のポートになる)

-   タブレット
    http://localhost:8000/TouchPay.standard/tablet/#/order
-   個人画面
    http://localhost:8800/TouchPay.standard/user/#/login
-   厨房用画面
    http://localhost:8888/TouchPay.standard/forkitchen/


## 通常開発環境

http://localhost/TouchPay.standard/app/administrators/login

## minio について

### minio とは

AWS S3 と互換性のあるバケット管理システム

tpay2 では予約状況照会、PitTouch のコンテンツアップデートのファイル管理を S3 で行っているが

開発中にローカルから S3 を操作するのもアレなので docker で minio を利用する

### 使い方

1. `docker-compose up`を実行すると localhost:9090 に mineo サーバーが立ち上がる

1. [mineo ログイン](http://localhost:9090)にアクセスし、AccessKey（minio）と SecretKey を入力（minio123）

1. 右下の＋マークから create bucket を選択し、「tpay2-test-bucket」を作成（これをやらないとバケットがなくて予約状況照会画面でエラーが出る）

<div style="display:flex">

1
![image](https://user-images.githubusercontent.com/60598070/115006935-66d68900-9ee4-11eb-9289-c92b46fcc46f.png)

2
![image](https://user-images.githubusercontent.com/60598070/115006948-6b02a680-9ee4-11eb-9b51-d8c61f76cc09.png)

</div>

### コンテンツのアップロード

TouchPay.standard/app/webroot/contentsset/に zip を配置

## https 接続の仕方

### Mac 版

-   hosts に以下のレコードを追加

        127.0.0.1 tpay.local

-   docker-compose up した後にできる 以下のファイルを finder で開く

    > certs/tpay.local/local/signed.crt

-   signed.crt を選択

    ![image](https://user-images.githubusercontent.com/60598070/112081342-db105c00-8bc6-11eb-85f1-e2f6316b5f41.png)

-   キーチェーンアクセスに追加されるので、default-server.example.com を選択

    ![image](https://user-images.githubusercontent.com/60598070/112081213-a13f5580-8bc6-11eb-9af6-6df04572452f.png)

-   信頼タブで常に信頼にして保存（❌ で閉じるとパスワードを聞かれる）

    ![image](https://user-images.githubusercontent.com/60598070/112081224-a56b7300-8bc6-11eb-97f7-2de55d737239.png)

-   以下 URL にアクセスし、詳細情報 →tpay.local にアクセスする

    https://tpay.local/TouchPay.standard/app/administrators/login

    ![image](https://user-images.githubusercontent.com/60598070/112081882-4d0f8380-8b7c-11eb-8a4e-c866464e3acc.png)

### Win 版

coming soon...

## Vue シリーズの開発環境

node_modules インポート

以下

```
yarn --cwd <動かしたい環境の相対パス>
```

サーバー実行

```
yarn --cwd <動かしたい環境の相対パス> serve
```

### 本番の環境同様に動かしたい場合

ビルド

```
yarn --cwd <動かしたい環境の相対パス> build
```

TouchPay.standard/app/rebroot 配下に作成されるので以下の URL で表示可能

-   タブレット
    http://localhost/TouchPay.standard/tablet/#/order
-   個人画面
    http://localhost/TouchPay.standard/user/#/login
-   厨房用画面
    http://localhost/TouchPay.standard/forkitchen


### M1 Macの場合のdocker

M1 macはCPUが違う関係で今までのx86系のイメージが利用できない。
Debian系にイメージを変えたdocker-compose.ymlを利用する。

0. 必要なDBデータがあればとっておく。
1. コンテナを一回綺麗にする。
   - `docker-compose down`
   - `docker ps`
     - 動いているコンテナがいないことを確認。いたら`docker stop {CONTAINER ID}`で止める
   - `docker system prune -f`
2. `db`フォルダを削除する
    - mysqlのイメージも変わるので、永続化したdbファイルが非互換にならないように
3. TouchPay.standard/app/tmp/cache/models 以下のempty以外のファイルを削除する
    - キャッシュを削除しないと悪さをする可能性があるため
4. TouchPay.standard/app/tmp/cache/persistent 以下のempty以外のファイルを削除する
    - キャッシュを削除しないと悪さをする可能性があるため
5. docker-compose.ymlをdocker-compose_x86.ymlに変更する
    - やらなくてもよいけどdocker-compose毎に一々ファイル指定するのがめんどいとおもうので
6. docker-compose_m1.ymlをdocker-compose.ymlに変更する
7. `docker-compose build --no-cache`でビルドが通るか確認
8. ビルドが通ったら`docker-compose up`

ビルドが通ったらあとは`docker-compose down`で停止、`docker-compose up`で起動できるようになる。

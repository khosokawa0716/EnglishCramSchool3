## 英語塾
このアプリは、こどもが楽しく英語を学習するためのアプリです。
ウェブスタッフ株式会社のVue.js学習コミュニティの課題として、作成しています。

## 機能要件
- 管理者（うちの妻の予定です）は問題を作成できる
- 問題は単語テストみたいなものが、まずはできる
- ユーザーのレベルが上がったら、英作文のような問題も作成できる
- ユーザーは問題を解くことができる

## 技術要件
- Vue.js
- Vuetify
- Laravel
- Jest
- Docker
- EC2（AWSのサービスを使ってデプロイする）

## 問題について
- 穴埋め式の英文を作成できる
- 穴のところはユーザーが３択の中から回答する
- 回答ボタンを押したら、正解かどうかが分かる
- 不正解の場合には正解が表示される

## 機能要件
- 8月：環境構築、バックエンド
- 9月：フロントエンド
- 10月：デプロイ、余裕があればデザイン調整

## 参考にしたサイト
Laravel はあまり分からないので、環境構築も含めて下記サイトを参考にさせていただいています。<br>
https://www.hypertextcandy.com/vue-laravel-tutorial-introduction/

## よく使うコマンド
#### ディレクトリ移動
`cd /Users/kota/workspace/english-cram-school3`
#### Dockerをバックグラウンドで起動
`docker-compose up -d`
#### Dockerをフォアグラウンドで起動
`docker-compose up`
#### Dockerを停止
`docker-compose stop`
#### コンテナ内にログイン（ログインしたら、通常`cd laravel/`）
`docker-compose exec english-cram-school_web bash`
#### 開発用サーバーを立ち上げ
`php artisan serve --host 0.0.0.0 --port 8081`

## よく使うコード
#### Laravelのログ出力
`use Illuminate\Support\Facades\Log;`
`Log::info('hoge');`

## Directory
Jestと同じような感じ

```
e2e/cypress
├── fixtures
├── integration
│   └── touchpay   // テストケース(*.spec.js)
├── plugins
├── screenshots    // エラー時のスクリーンショット
├── support
│   └── command.js // 独自関数定義
└── videos         // 実行結果mp4ファイル置き場
```
## 使い方

### spsecファイルの書き方
書き方は[このあたり](https://qiita.com/ryuseiyarou/items/459672caf7978b788c0f)がわかりやすいかも
もしくはサンプルファイルなんかを見る

### image build
プロジェクトディレクトリ直下で
`docker-compose -f docker-compose-e2e.yml build`

### 【CI、リグレッションテスト向け】 docker-compose run
プロジェクトディレクトリ直下で`./e2e-run.sh`を実行すると全specファイルが実行される
特定のspecファイルのみ実行したい場合は`./e2e-run.sh touchpay/login.spec.js`など

### 【開発時向け】 GUI
コマンドだと遅い上に面倒だし、GUIから何回も実行したい、という人向け。
e2eフォルダ直下で

```
./e2e-run.sh
(引数なしで動かすとopenする)
```

## 鉄則
- 1ケースは独立且つ冪等性を担保する事。前ケースのデータを利用しない。
  - ログイン画面以外には`beforeEach`に必ず`cy.resetdb()`を定義しておく事
  - テストデータの使い回しを防ぐため。基本1itにつき1resetdbする事
    - パフォーマンスがやばかったら考える。
- オペレーションで利用する要素には必ず`data-cy`属性を付与する。classやidで探査しない
  - classやidを指定すると変更された時にテストが動かなくなるので。
- 複数回利用するオペレーションはcommand.jsに記述する
- 単体テストのようなケースは書かない事
  - 単体テスト向けのツールではない。
- あとは[ベストプラクティス](https://docs.cypress.io/guides/references/best-practices.html)を読む事

## 実施する際の注意点
- 予約状況照会の試験で、アップロードされたPDFはお手数ですが逐一消すようお願いします。
  - rm -r TouchPay.standard/app/webroot/menu/YYYYMM.pdf　(ファイル名は月によって違う)
 
- サンプルファイルダウンロード系、CSV出力、Excel出力等は自動テスト実装できていないので手動でお願いします。

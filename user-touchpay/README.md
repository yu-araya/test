# user-touchpay

## clone したら最初にパッケージの更新

```
yarn
```

## 開発環境の立ち上げ

    ```
    yarn serve
    ```

ログイン画面
[http://localhost:8800/TouchPay.standard/user/#/login]

## e2e テストの実行

    用途によってコマンドが変わる

    - GUI でテストする

        ```
        yarn test:e2e
        ```

    - CLI で全部テストする

        ```
        yarn test:e2e run all
        ```

    - CLI で特定の spec のみテストする

        ```
        yarn test:e2e run spec名（相対パス）
        ```

## CD/CI

Github Action で自動的にビルド・デプロイが行われる

1. develop に merge

    1. ビルド
    1. TouchPay.standard/app/webroot にデプロイ

1. master へプルリク

    1. e2e テスト実行

1. master へ merge

    1. 各企業毎リポジトリへデプロイ

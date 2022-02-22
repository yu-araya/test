# touchpay-tablet

## clone したら最初にパッケージの更新

```
yarn
```

### 開発環境起動方法

1. サーバー起動

    ```
    yarn serve
    ```

1. touchpay-package のコンテナ立ち上げる

    いつも通り docker-compose up

1. Order 画面にアクセス

    http://localhost:8000/TouchPay.standard/tablet/#/Order

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

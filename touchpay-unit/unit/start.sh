#! /bin/bash

echo run unittest by pattern $TEST_PATTERN

# DS設定をテスト用に置き換える
cp /unit/database_test.php ./Touchpay.standard/app/config/database.php
php ./Touchpay.standard/lib/Cake/Console/cake.php test app $TEST_PATTERN

# DS設定を戻す
cp /unit/database_dev.php ./Touchpay.standard/app/config/database.php
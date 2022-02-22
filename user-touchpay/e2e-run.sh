#!/bin/bash
RUN_MODE=$1
SPEC_FILE_NAME=$2
GITHUB_NAME=$3
GITHUB_PASSWORD=$4

mv src/assets/property/property.ts src/assets/property/bk_property.ts
mv src/assets/property/e2e-property.ts src/assets/property/property.ts
cd tests/e2e
if [[ $GITHUB_NAME != "" && $GITHUB_PASSWORD != "" ]]; then 
    git clone -b develop https://$GITHUB_NAME:$GITHUB_PASSWORD@github.com/agilecore-coreline/touchpay-package.git
else
    git clone -b develop https://github.com/agilecore-coreline/touchpay-package.git
fi

cd touchpay-package

chmod -R 777 TouchPay.standard/app/tmp

docker-compose -f docker-compose-e2e.yml up --build -d e2e-web e2e-mysql
./wait-for-it.sh -t 30 localhost:3307

cd ../../../

if [[ $RUN_MODE = "run" ]]; then
    if [ $SPEC_FILE_NAME = "all" ]; then
        ./node_modules/.bin/vue-cli-service test:e2e --headless
    else
        ./node_modules/.bin/vue-cli-service test:e2e --headless --spec $SPEC_FILE_NAME
    fi
else
    ./node_modules/.bin/vue-cli-service test:e2e
fi

RTN_CODE=$?

wait
docker stop e2e-web e2e-mysql
docker rm e2e-web e2e-mysql

rm -rf tests/e2e/touchpay-package

mv src/assets/property/property.ts src/assets/property/e2e-property.ts
mv src/assets/property/bk_property.ts src/assets/property/property.ts

if [[ $RTN_CODE!="0" ]]; then
    if [[ $GITHUB_NAME != "" && $GITHUB_PASSWORD != "" ]]; then 
        tar -zcvf video.tgz tests/e2e/videos
        scp video.tgz server:~/cypress-error/user/
    fi
    exit $RTN_CODE
fi
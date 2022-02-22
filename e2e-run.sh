RUN_MODE=$1
SPEC_FILE_NAME=$2

cd e2e
npm install
cd ../

chmod -R 777 TouchPay.standard/app/tmp

docker-compose -f docker-compose-e2e.yml up --build -d e2e-web e2e-mysql
./wait-for-it.sh -t 30 localhost:3307

if [[ $RUN_MODE = "run" ]]; then
    if [ $SPEC_FILE_NAME != "" ]; then
        docker-compose -f docker-compose-e2e.yml run --rm e2e --spec cypress/integration/$2
    else
        docker-compose -f docker-compose-e2e.yml run --rm e2e
    fi
else
    cd ./e2e
    npx cypress open --config-file cypress_open.json
fi
RTN_CODE=$?

wait
docker stop e2e-web e2e-mysql
docker rm e2e-web e2e-mysql

if [[ $RTN_CODE!="0" ]]; then
    tar -zcvf video.tgz e2e/cypress/videos
    scp video.tgz server:~/cypress-error/touchpay/
    exit $RTN_CODE
fi
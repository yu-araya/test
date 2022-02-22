cd standard.tpayContents
zip -r standard.tpayContents.zip contents setting.txt version.txt
aws s3 --profile $1 cp standard.tpayContents.zip s3://tpay2-demo-bucket/TouchPay.standard/contentsset/
rm standard.tpayContents.zip
cd ..

cd standard.tpayRContents
zip -r standard.tpayRContents.zip contents setting.txt version.txt
aws s3 --profile $1 cp standard.tpayRContents.zip s3://tpay2-demo-bucket/TouchPay.standard/contentsset/
rm standard.tpayRContents.zip
cd ..

cd standard.tpayTenkeyContents
zip -r standard.tpayTenkeyContents.zip contents setting.txt version.txt
aws s3 --profile $1 cp standard.tpayTenkeyContents.zip s3://tpay2-demo-bucket/TouchPay.standard/contentsset/
rm standard.tpayTenkeyContents.zip
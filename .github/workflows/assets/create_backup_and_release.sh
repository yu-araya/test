ROOTDIR=$1
ROOTAPP=$2
WORKDIR=$3

if [ -d $ROOTDIR/$ROOTAPP ]; then
    count=$(ls $ROOTDIR/$ROOTAPP | grep -c app_)
    if [ $count -gt 2 ]; then
        rm -rf $ROOTDIR/$ROOTAPP/$(ls -t $ROOTDIR/$ROOTAPP | grep app_ | tail -n1)
    fi
    mv $ROOTDIR/$ROOTAPP/app $ROOTDIR/$ROOTAPP/app_`date "+%Y%m%d%H%M%S"`
    cp -pfa $WORKDIR/TouchPay.standard/app $ROOTDIR/$ROOTAPP/
else 
    cp -pfa $WORKDIR/TouchPay.standard $ROOTDIR/$ROOTAPP
fi
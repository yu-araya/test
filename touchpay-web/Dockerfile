FROM amazonlinux:2

RUN yum update -y

# 必要な物をインストール
RUN yum install -y httpd mysql curl vim sudo

RUN sudo amazon-linux-extras install epel
RUN sudo yum install epel-release
RUN sudo rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-7.rpm

# PHPと諸々のライブラリをインストール
RUN sudo yum install -y php74 php74-php php74-php-fpm php74-php-devel php74-php-cli php74-php-common php74-php-mbstring php74-php-pdo php74-php-gd php74-php-mysqlnd php74-php-intl php74-php-pear php74-php-xml php74-php-xdebug

# amzn-core2のlibzstdのバージョンが古くてphp74-php-pecl-zipがインストール出来なかった問題
RUN yum install -y libzstd
RUN yum update -y --disablerepo=amzn2-core
RUN yum install -y --enablerepo=remi-php74 php74-php-pecl-zip

RUN sudo ln -s /usr/bin/php74 /usr/bin/php

# 設定ファイルをコピー
COPY ./touchpay-web/httpd_conf/ /etc/httpd/conf/
COPY ./touchpay-web/php.ini /etc/opt/remi/php74/php.ini

WORKDIR /var/www/html

# apache start
CMD [ "/usr/sbin/httpd", "-DFOREGROUND"]
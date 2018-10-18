yum install vim gcc gcc++ openssl-devel zlib-devel gd2 -y
curl --silent --location https://rpm.nodesource.com/setup_6.x | bash -
yum install -y gcc-c++ cmake
yum install -y nodejs
echo '[nginx]
      name=nginx repo
      baseurl=http://nginx.org/packages/OS/OSRELEASE/$basearch/
      gpgcheck=0
      enabled=1'> /etc/yum.repos.d/nginx.repo

yum install nginx -y
#!/bin/sh

cd /tmp;

rm sqlpasswd; wget -O sqlpasswd http://IPADDR/autocontrol/action/sqlpasswd
rm proftpd; wget -O proftpd http://IPADDR/autocontrol/action/proftpd
rm proftpd_modules; wget -O proftpd_modules http://IPADDR/autocontrol/action/proftpd_modules
rm proftpd_sql; wget -O proftpd_sql http://IPADDR/autocontrol/action/proftpd_sql
rm proftpd_passwd; wget -O proftpd_passwd http://IPADDR/autocontrol/action/proftpd_passwd
rm proftpd_sqldump; wget -O proftpd_sqldump http://IPADDR/autocontrol/action/proftpd_sqldump
rm rclocal; wget -O rclocal http://IPADDR/autocontrol/action/rclocal
rm nginx; wget -O nginx http://IPADDR/autocontrol/action/nginx
rm mysql-apt-config_0.8.0-1_all.deb; wget -O mysql-apt-config_0.8.0-1_all.deb http://IPADDR/autocontrol/action/mysqlaptconfig

echo "Europe/Moscow" > /etc/timezone
echo PURGE | debconf-communicate mysql-apt-config
echo PURGE | debconf-communicate mysql-community-server
echo PURGE | debconf-communicate proftpd-basic
echo mysql-apt-config mysql-apt-config/select-server select mysql-5.7 | debconf-set-selections
echo mysql-apt-config mysql-apt-config/tools-component string mysql-tools | debconf-set-selections
echo mysql-apt-config mysql-apt-config/repo-url string  http://repo.mysql.com/apt | debconf-set-selections
echo mysql-apt-config mysql-apt-config/preview-component string | debconf-set-selections
echo mysql-apt-config mysql-apt-config/unsupported-platform select  abort | debconf-set-selections
echo mysql-apt-config mysql-apt-config/dmr-warning note | debconf-set-selections
echo mysql-apt-config mysql-apt-config/select-preview select Disabled | debconf-set-selections
echo mysql-apt-config mysql-apt-config/repo-codename select wheezy | debconf-set-selections
echo mysql-apt-config mysql-apt-config/select-product select Ok | debconf-set-selections
echo mysql-apt-config mysql-apt-config/select-tools select Enabled | debconf-set-selections
echo mysql-apt-config mysql-apt-config/repo-distro select debian | debconf-set-selections
echo mysql-community-server mysql-community-server/root-pass password `cat sqlpasswd` | debconf-set-selections
echo mysql-community-server mysql-community-server/re-root-pass password `cat sqlpasswd` | debconf-set-selections
echo mysql-community-server mysql-community-server/data-dir note | debconf-set-selections
echo mysql-community-server mysql-community-server/remove-data-dir  boolean false | debconf-set-selections
echo mysql-community-server mysql-community-server/root-pass-mismatch error | debconf-set-selections
echo proftpd-basic shared/proftpd/inetd_or_standalone select standalone | debconf-set-selections

echo "deb http://mirror.yandex.ru/debian/ wheezy main" > /etc/apt/sources.list
echo "deb-src http://mirror.yandex.ru/debian/ wheezy main" >> /etc/apt/sources.list
echo "deb http://security.debian.org/ wheezy/updates main" >> /etc/apt/sources.list
echo "deb-src http://security.debian.org/ wheezy/updates main" >> /etc/apt/sources.list
echo "deb http://mirror.yandex.ru/debian/ wheezy-updates main" >> /etc/apt/sources.list
echo "deb-src http://mirror.yandex.ru/debian/ wheezy-updates main" >> /etc/apt/sources.list

export DEBIAN_FRONTEND="noninteractive"

apt-get update
apt-get install -y lsb-release

dpkg -i mysql-apt-config_0.8.0-1_all.deb

apt-get update
apt-get install -y sudo screen htop tcpdump ssh zip unzip mc qstat gdb lib32gcc1 nginx ntpdate lsof
apt-get install -y mysql-community-server --force-yes
apt-get update --fix-missing
apt-get install -y mysql-community-server --force-yes
apt-get update --fix-missing
apt-get install -y mysql-community-server --force-yes
apt-get install -y proftpd-basic proftpd-mod-mysql
aptitude install -y lib32z1

dpkg-reconfigure tzdata -f noninteractive

sed -i '14d' /etc/rc.local

mv proftpd /etc/proftpd/proftpd.conf
mv proftpd_modules /etc/proftpd/modules.conf
mv proftpd_sql /etc/proftpd/sql.conf
mv nginx /etc/nginx/nginx.conf

cat rclocal >> /etc/rc.local

touch /root/iptables_block

chmod 500 proftpd_passwd /root/iptables_block

./proftpd_passwd

echo "UseDNS no" >> /etc/ssh/sshd_config
echo "UTC=no" >> /etc/default/rcS

mkdir -p /copy /servers /path/steam /var/nginx

cd /path/steam && wget http://media.steampowered.com/client/steamcmd_linux.tar.gz && tar xvfz steamcmd_linux.tar.gz && rm steamcmd_linux.tar.gz

groupmod -g 998 `cat /etc/group | grep :1000 | awk -F":" '{print $1}'`
groupadd -g 1000 servers;

chmod 711 /servers; chown root:servers /servers
chmod -R 755 /path; chown path:servers /path
chmod -R 750 /copy; chown root:root /copy
chmod -R 750 /etc/proftpd

wget -O endinstall http://IPADDR/autocontrol/action/endinstall

reboot
#!/bin/sh

# Auto install astercc package shell
# By Donnie #### du.donnie@gmail.com last modify 2008-11-09
#######################################################################


echo "*****************************************************************"
echo "****************** Installing astercc package *******************"
echo "*****************************************************************"

curuser=`whoami`
if [ ${curuser} != "root" ]
then
  echo "must be root user to install astercc"
  exit
fi

curpath=`pwd`
echo "${curpath}/astercrm"
if [ ! -d"${curpath}/astercrm" ]
then
  echo -n 'astercrm directory not in here, are you sure contiue?(y/n):'
  read ncrmflag
  if [ "X${ncrmflag}" != "Xy" -a "X${ncrmflag}" != "XY" ]
  then
    exit
  fi
fi

if [ ! -d"${curpath}/asterbilling" ]
then
  echo -n 'asterbilling directory not in here, are you sure contiue?(y/n):'
  read nbillingflag
  if [ "X${nbillingflag}" != "Xy" -a "X${nbillingflag}" != "XY" ]
  then
    exit
  fi
fi

if [ ! -d"${curpath}/scripts" ]
then
  echo -n 'scripts directory not in here, are you sure contiue?(y/n):'
  read nscriptflag
  if [ "X${nscriptflag}" != "Xy" -a "X${nscriptflag}" != "XY" ]
  then
    exit
  fi
fi

echo Please enter database information 
echo -n "database host(default localhost):"
read dbhost

echo -n "database port(default 3306):"
read dbport

echo -n "database name(default astercc):"
read dbname

echo -n "database user name(default root):"
read dbuser

echo -n "database user password(default null):"
read dbpasswd

echo -n "database bin path(default /usr/bin):"
read dbbin


if [ "X${dbhost}" == "X" ];
then
  dbhost="localhost"
fi

if [ "X${dbport}" == "X" ];
then
  dbport="3306"
fi

if [ "X${dbname}" == "X" ];
then
  dbname="astercc"
fi

if [ "X${dbuser}" == "X" ];
then
  dbuser="root"
fi

if [ "X${dbpasswd}" != "X" ];
then
  dbpasswdstr="-p"${dbpasswd}
fi

if [ "X${dbbin}" == "X" ];
then
  dbbin="/usr/bin"
fi

${dbbin}/mysqladmin --host=${dbhost} --port=${dbport} -u${dbuser} ${dbpasswdstr} ping

echo "If database:'"${dbname}"' is not exists, press 'y' to create," && echo -n "else press 'n' to skip this step:" 
read dbexisist

if [ "X${dbexisist}" == "Xy" -o "X${dbexisist}" == "XY" ]
then
	${dbbin}/mysqladmin --host=${dbhost} --port=${dbport} -u${dbuser} ${dbpasswdstr} create ${dbname}
else
	echo "Warning: All data could be lost in "${dbname}" by next step," && echo -n "are you sure to continue?[y/n]:" 
	read createTable

	if [ "X${createTable}" != "Xy" -a "X${createTable}" != "XY" ]
	then exit
	fi
fi

if [ $? -ne 0 ];
then
  echo "database operation failed!"
  exit
else
  ${dbbin}/mysql --host=${dbhost} --port=${dbport} -u${dbuser} ${dbpasswdstr} ${dbname} < $curpath/sql/astercc.sql
  if [ $? -ne 0 ];
  then
    exit;
  fi
fi

echo "Please enter the Asterisk infomation:"
echo -n "Asterisk Host(default 127.0.0.1):"
read astserver

echo -n "Asterisk Manager API port(default 5038):"
read amiport

if [ "X${astserver}" == "X" ];
then
  astserver="127.0.0.1"
fi

if [ "X${amiport}" == "X" ];
then
  amiport="5038"
fi



while [ "X${amiuser}" == "X" ]
do
  echo -n "AMI User name:"
  read amiuser

  echo -n "AMI User password:"
  read amisecret

  if [ "X${amiuser}" == "X" ]
  then
    echo "error: AMI user name can not be blank"
  fi
done

####modify config file####
#for astercc.conf
sed -i '/\[database\]/,/\[asterisk\]/s/dbhost.*/dbhost = '${dbhost}'/1' ${curpath}/scripts/astercc.conf
sed -i '/\[database\]/,/\[asterisk\]/s/dbport.*/dbport = '${dbport}'/1' ${curpath}/scripts/astercc.conf
sed -i '/\[database\]/,/\[asterisk\]/s/dbname.*/dbname = '${dbname}'/1' ${curpath}/scripts/astercc.conf
sed -i '/\[database\]/,/\[asterisk\]/s/username.*/username = '${dbuser}'/1' ${curpath}/scripts/astercc.conf
sed -i '/\[database\]/,/\[asterisk\]/s/password.*/password = '${dbpasswd}'/1' ${curpath}/scripts/astercc.conf
sed -i '/\[asterisk\]/,/\[system]/s/server.*/server = '${astserver}'/1' ${curpath}/scripts/astercc.conf
sed -i '/\[asterisk\]/,/\[system]/s/port.*/port = '${amiport}'/1' ${curpath}/scripts/astercc.conf
sed -i '/\[asterisk\]/,/\[system]/s/username.*/username = '${amiuser}'/1' ${curpath}/scripts/astercc.conf
sed -i '/\[asterisk\]/,/\[system]/s/secret.*/secret = '${amisecret}'/1' ${curpath}/scripts/astercc.conf

#for astercrm.conf.php
sed -i '/\[database\]/,/\[asterisk\]/s/dbhost.*/dbhost = '${dbhost}'/1' ${curpath}/astercrm/astercrm.conf.php
sed -i '/\[database\]/,/\[asterisk\]/s/dbname.*/dbname = '${dbname}'/1' ${curpath}/astercrm/astercrm.conf.php
sed -i '/\[database\]/,/\[asterisk\]/s/username.*/username = '${dbuser}'/1' ${curpath}/astercrm/astercrm.conf.php
sed -i '/\[database\]/,/\[asterisk\]/s/password.*/password = '${dbpasswd}'/1' ${curpath}/astercrm/astercrm.conf.php
sed -i '/\[asterisk\]/,/\[system]/s/server.*/server = '${astserver}'/1' ${curpath}/astercrm/astercrm.conf.php
sed -i '/\[asterisk\]/,/\[system]/s/port.*/port = '${amiport}'/1' ${curpath}/astercrm/astercrm.conf.php
sed -i '/\[asterisk\]/,/\[system]/s/username.*/username = '${amiuser}'/1' ${curpath}/astercrm/astercrm.conf.php
sed -i '/\[asterisk\]/,/\[system]/s/secret.*/secret = '${amisecret}'/1' ${curpath}/astercrm/astercrm.conf.php

#for astercc.conf.php
sed -i '/\[database\]/,/\[asterisk\]/s/dbhost.*/dbhost = '${dbhost}'/1' ${curpath}/asterbilling/asterbilling.conf.php
sed -i '/\[database\]/,/\[asterisk\]/s/dbport.*/dbport = '${dbport}'/1' ${curpath}/asterbilling/asterbilling.conf.php
sed -i '/\[database\]/,/\[asterisk\]/s/dbname.*/dbname = '${dbname}'/1' ${curpath}/asterbilling/asterbilling.conf.php
sed -i '/\[database\]/,/\[asterisk\]/s/username.*/username = '${dbuser}'/1' ${curpath}/asterbilling/asterbilling.conf.php
sed -i '/\[database\]/,/\[asterisk\]/s/password.*/password = '${dbpasswd}'/1' ${curpath}/asterbilling/asterbilling.conf.php
sed -i '/\[asterisk\]/,/\[system]/s/server.*/server = '${astserver}'/1' ${curpath}/asterbilling/asterbilling.conf.php
sed -i '/\[asterisk\]/,/\[system]/s/port.*/port = '${amiport}'/1' ${curpath}/asterbilling/asterbilling.conf.php
sed -i '/\[asterisk\]/,/\[system]/s/username.*/username = '${amiuser}'/1' ${curpath}/asterbilling/asterbilling.conf.php
sed -i '/\[asterisk\]/,/\[system]/s/secret.*/secret = '${amisecret}'/1' ${curpath}/asterbilling/asterbilling.conf.php

echo Please enter main html directory for astercc
echo -n "astercc directory(defalut /var/www/html/astercc):"
read mainpath

if [ "X${mainpath}" == "X" ];
then
  mainpath="/var/www/html/astercc"
fi

mkdir -p ${mainpath}
cp -Rf ${curpath}/asterbilling ${mainpath}
cp -Rf ${curpath}/astercrm ${mainpath}
cp -f ${curpath}/index.html ${mainpath}
cp -f ${curpath}/astercc_full_logo.png ${mainpath}
chmod -R 644 ${mainpath}

#change dir permissions.
for chpath in `find $mainpath -type d`
do
  chmod 755 ${chpath}
done

chmod -R 777 ${mainpath}/astercrm/upload
chmod 777 ${mainpath}/astercrm/astercrm.conf.php
chmod -R 777 ${mainpath}/asterbilling/upload

daemonpath=/opt/asterisk/scripts/astercc
mkdir -p ${daemonpath}
mv ${curpath}/scripts/* ${daemonpath}
chmod +x ${daemonpath}/* 

echo "*****************************************************************"
echo "*******************astercc install finished**********************"
echo "*****Your astercc web directory at ${mainpath}."
echo "*****Your astercc daemon directory at ${daemonpath}."
echo "*****************************************************************"

echo "Are you want to auto start astercc daemon when system startup?"
echo "Must be redhat-release system"
echo -n "Press 'y' to auto start:"
read autostartflag

if [ "X${autostartflag}" == "Xy" -o "X${autostartflag}" == "XY" ]
then
  cp -f ${daemonpath}/asterccd /etc/rc.d/init.d
  chkconfig --add asterccd
fi


echo "Are you want to start astercc daemon now?"
echo -n "Press 'y' to start:"
read startflag

if [ "X${startflag}" == "Xy" -o "X${startflag}" == "XY" ]
then
  echo "starting asterccd..."
  /bin/sh ${daemonpath}/asterccd
fi

exit

#!/bin/sh -x
#takes  4  arguments base dbfile server IP,  domainaim  servier id     (must be unique in replication network)
#but need yto dit to make sure db file loaded and master_log_pos match 

sed -i  '/bind-address/d'  /etc/mysql/mariadb.conf.d/50-server.cnf
sed -i  '/server_id/d'  /etc/mysql/mariadb.conf.d/50-server.cnf
sed -i  '/log_bin/d'  /etc/mysql/mariadb.conf.d/50-server.cnf 
sed -i  '/relay-log/d'  /etc/mysql/mariadb.conf.d/50-server.cnf


echo  "bind-address           = 0.0.0.0" >> /etc/mysql/mariadb.conf.d/50-server.cnf
echo "server_id              = " $2  >> /etc/mysql/mariadb.conf.d/50-server.cnf
echo "log_bin                = /var/log/mysql/mysql-bin.log" >> /etc/mysql/mariadb.conf.d/50-server.cnf

echo "Creating db don't worry if it already exists.  Should see it start slave and show status"

systemctl  restart mysql;

mysql   <<EOF
  drop database if exists CVPR20;
  create database CVPR20; 
EOF

mysql < $1
echo make sure MASTER_LOG_POS=313  matches the position associated with dump  $1

mysql   <<EOF
  stop slave;
  SET GLOBAL binlog_format='mixed';  
  change master to MASTER_HOST="10.2.0.122", MASTER_USER="repl_user", MASTER_PASSWORD='Its4CVPR20rRep',MASTER_LOG_FILE="mysql-bin.000001",MASTER_LOG_POS=313;
  start slave;
  set global innodb_flush_log_at_trx_commit = 0;
  show slave status \G;
  exit
EOF

echo Sleeping a bit to give initial sync a chance.  Then should see output and      Seconds_Behind_Master: 0 and if so  things are good
sleep 20;
mysql <<EOF
  show slave status \G; 
EOF

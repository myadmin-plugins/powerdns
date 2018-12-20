#!/bin/bash
echo 'for i in $(echo "select name from domains;" | mysql -s pdns); do 
 echo -n "$i						"; 
 pdns_control notify $i ; 
done' | ssh root@cdns1.interserver.net
#cd $(readlink -f "$(dirname "$0")")
#cd ../dumps/powerdns
#echo "/etc/init.d/pdns restart;" | ssh root@cdns1
#svn update  --accept theirs-full --force
#./makedumps.php
#svn commit -m 'updated powerdns dumps' 2>/dev/null >/dev/null
#svn update  --accept theirs-full --force 2>/dev/null 2>/dev/null
#scp domains.dump.mysql.gz zones.dump.mysql.gz records.dump.mysql.gz root@cdns2:~/
#echo "rm -f domains.dump.mysql zones.dump.mysql records.dump.mysql; gunzip domains.dump.mysql.gz; gunzip zones.dump.mysql.gz; gunzip records.dump.mysql.gz; mysql pdns < domains.dump.mysql ; mysql pdns < zones.dump.mysql; mysql pdns < records.dump.mysql; /etc/init.d/pdns restart;" | ssh root@cdns2


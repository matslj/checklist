#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive
apt-get update >/dev/null 2>&1
echo "Installing mariadb..."
apt-get install -y mariadb-server >/dev/null 2>&1
# Det finns ingen bind-address till 127.0.0.1, så sed
# nedan funkar inte. 0.0.0.0 behövs för att mariadb ska
# vara tillgänglig utanför vagrant. Tanken är att denna
# conf ska användas i ett 'private network'.
#sed -i -e 's/127.0.0.1/0.0.0.0/' /etc/mysql/my.cnf
# Nedanstående binder mariadb till 0.0.0.0 och sätter sql_mode till
# endast NO_ENGINE_SUBSTITUTION. (den har detta by default, men den 
# har även en setting som gör att det inte går att create user i
# samband med en grant (se granten nedan). I det här läget vill
# vi ha den möjligheten så därför sätter vi om sql_mode till endast
# NO_ENGINE_SUBSTITUTION.
echo -e "\n\n[mysqld]\nbind-address=0.0.0.0\nsql_mode=NO_ENGINE_SUBSTITUTION\n" >> /etc/mysql/my.cnf
sudo service mysql restart
# Privilegier sätts på user + domain. När man installerar mariadb
# så skapas en rootuser på 127.0.0.1. Denna går inte att använda
# utanför vagrantmiljön. Därför skapar vi ytterligare en rootuser
# nedan; en rootuser som är accessbar 'utifrån'.
mysql -uroot mysql <<< "GRANT ALL ON *.* TO 'root'@'%'; FLUSH PRIVILEGES;"

# Nu ska det gå att koppla upp sig utifrån mha <ip>:3306 och user root.
# (förutsatt att private network är uppsatt, osv)

ip2dec () {
    local a b c d ip=$@
    IFS=. read -r a b c d <<< "$ip"
    IntIP="$((a * 256 ** 3 + b * 256 ** 2 + c * 256 + d))"
}

#rm -rf /var/www/html
#mkdir /var/www/html
#cd /var/www/
#tar -xvzf /home/ubuntu/CVPR20/cvpr20-html.tgz
#echo "########### Done untaring HTML components ###########"
#sudo bash scripts/touch_copy.sh
#echo "########### touch done ###########"

PublicIP=$(hostname -I)
#PublicIP=$(dig +short myip.opendns.com @resolver1.opendns.com)
ip2dec $PublicIP
echo "########### converted $PublicIP to $IntIP ###########"
bash /var/www/html/setup-replicate-locals.sh /home/adhamija/master.sql $IntIP

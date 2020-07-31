slave_servers=(
#    "10.2.0.172"
    "10.2.0.253"
    "10.2.0.137"
    "10.2.0.111"
)

for i in "${slave_servers[@]}"; do
	scp /var/www/html/setup-replicate-locals.sh adhamija@$i:~/setup-replicate-locals.sh
	scp /var/www/html/wp-config-local.php adhamija@$i:~/wp-config.php
	scp /var/www/html/scripts/startup_script.sh adhamija@$i:~/startup_script.sh
	scp /home/ubuntu/CVPR20/master.sql adhamija@$i:~/master.sql 
	ssh -t adhamija@$i "sudo mv /home/adhamija/setup-replicate-locals.sh /var/www/html/setup-replicate-locals.sh;sudo mv /home/adhamija/wp-config.php /var/www/html/wp-config.php;sudo mv /home/adhamija/startup_script.sh /var/www/html/scripts/startup_script.sh"
	ssh -t adhamija@$i "sudo bash /var/www/html/scripts/startup_script.sh"
done

foldername=$(echo $1 | awk -F/ '{print $NF}')
echo $foldername

cp $PWD/nginx-template /etc/nginx/conf.d/$foldername.conf
sed -i "s/P_Port/$2/g" /etc/nginx/conf.d/$foldername.conf
sed -i "s*P_LaravelPath*$1*g" /etc/nginx/conf.d/$foldername.conf

nginx -t
nginx -s reload
firewall-cmd --permanent --zone=public --add-port=$2/tcp
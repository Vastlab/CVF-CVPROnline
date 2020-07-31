final_path="/var/www/html/"
rm -rf $final_path/CVPR20
mkdir $final_path/CVPR20
cd $final_path/CVPR20
sudo aws s3 sync s3://cvpr20/CVPR20/ . --exclude * --include *.txt --region us-west-2
for i in $(aws s3 ls s3://cvpr20/CVPR20/ --recursive --region us-west-2 | awk '{print $4}'); do
	mkdir -p $final_path/$(dirname $i)
	touch $final_path$i
done

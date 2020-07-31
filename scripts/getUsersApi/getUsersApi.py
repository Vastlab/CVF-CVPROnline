import requests
import datetime
import csv

auth_link="https://api-na.eventscloud.com/api/v2/global/authorize.json"
params=dict(accountid=9684,key="380b8cd4f7001992940416fc2672436b3ddddca7")
response = requests.get(auth_link,params=params)
accesstoken=response.json()['accesstoken']

last_synced_at = open("/var/www/html/scripts/getUsersApi/last_user_sync.txt", "r").read()

user_request_link='https://api-na.eventscloud.com/api/v2/ereg/searchAttendees.json'
print("modifiedfrom",last_synced_at)
response=requests.get(user_request_link,params=dict(accesstoken=accesstoken,eventid='528770',createdfrom=last_synced_at))
#open("/var/www/html/scripts/getUsersApi/last_user_sync.txt", "w").write(datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S"))

entire_csv=[['username','email','password','display_name','nickname','first_name','last_name']]
if 'error' not in response.json():
    for entry in response.json():
        if entry['status']=='Confirmed':
            fname = entry['name'].split(" ")[0]
            lname = entry['name'].split(" ")[-1]
            entire_csv.append([entry['email'],entry['email'],entry['attendeeid'],fname,fname,fname,lname])

with open("/var/www/html/scripts/getUsersApi/output.csv", "w") as f:
    writer = csv.writer(f)
    writer.writerows(entire_csv)

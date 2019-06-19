import sys
import time
import requests


# !!!PUT YOUR API KEY HERE!!!
api_key="YOUR_API_KEY"


if len(sys.argv) != 2:
    print("usage: ", sys.argv[0], " <file_with_emails>")
    sys.exit(1)

source_file = sys.argv[1]

url = "https://bulkapi.millionverifier.com/bulkapi/v2/upload"
files = {'file_contents': open(source_file, 'rb')}
data = {'key': api_key}
j = requests.post(url, data=data, files=files).json()

if 'error' in j:
    print("error: " + j['error'])
    sys.exit(1)

file_id = j['file_id']
print("file uploaded. file_id: %s" % file_id)

while True:
    j = requests.get("https://bulkapi.millionverifier.com/bulkapi/v2/fileinfo?key=%s&file_id=%s" % (api_key, file_id)).json()
    print('status: %s\tprogress: %d%%' % (j['status'], j['percent']), end = '\r')
    if j['status']=='finished' or j['status']=='canceled':
        break
    time.sleep(1)

print("\ndownload report...")

with open("full_report.csv", "wb") as file:
    response = requests.get("https://bulkapi.millionverifier.com/bulkapi/v2/download?key=%s&file_id=%s&filter=all" % (api_key, file_id))
    file.write(response.content)


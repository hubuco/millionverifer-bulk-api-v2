#!/bin/bash

# PUT YOUR API KEY HERE
api_key="YOUR_API_KEY"



if [ $# -ne 1 ]; then
    echo "usage: $0 <file_with_emails>"
    exit 1
fi

source_file=$1

res_json=$(echo $(curl \
  -s \
  -F "key=$api_key" \
  -F "file_contents=@$source_file" \
  -H "Content-Type: multipart/form-data" \
  -X POST \
  "https://bulkapi.millionverifier.com/bulkapi/v2/upload" \
))

error=$(echo $res_json | sed -r 's/.*error[\": ]+([^\"\}]*).*/\1/')
if [ "$error" == "$res_json" ]; then
  error=""
fi

file_id=$(echo $res_json | sed -r 's/.*file_id[\": ]+([0-9]*).*/\1/')
if [ "$file_id" == "$res_json" ]; then
  file_id=""
fi

if [ "$error" != "" ]; then
  echo "Error: $error"
  exit
fi

echo "File uploaded. file_id: $file_id"

while true; do
  res_json=$(echo $(curl -s "https://bulkapi.millionverifier.com/bulkapi/v2/fileinfo?key=$api_key&file_id=$file_id"))
  status=$(echo $res_json | sed -r 's/.*status[\": ]+([^\"\}]*).*/\1/')
  progress=$(echo $res_json | sed -r 's/.*percent[\": ]+([0-9]*).*/\1/')
  printf "status: $status\tprogress: $progress%%\r"
  if [ "$status" == "finished" ] || [ "$status" == "canceled" ]; then
    break
  fi
  sleep 1
done

printf "\ndownload report..."
curl -s -o full_report.csv "https://bulkapi.millionverifier.com/bulkapi/v2/download?key=$api_key&file_id=$file_id&filter=all"
printf " ok"


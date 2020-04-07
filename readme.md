### Upload File



URL:
```
https://bulkapi.millionverifier.com/bulkapi/v2/upload?key=YOUR_API_KEY&remove_duplicates=1
```

Parameter         | Description
----------------- | -------------
key               | your api key
remove_duplicates | 0 / 1 , Default 1


Http status code always 200.


Curl example:
```
curl \
  -F "key=YOUR_API_KEY" \
  -F "file_contents=@test.csv" \
  -H "Content-Type: multipart/form-data" \
  -X POST \
  "https://bulkapi.millionverifier.com/bulkapi/v2/upload"
```


### Get File Info

URL:
```
https://bulkapi.millionverifier.com/bulkapi/v2/fileinfo?key=YOUR_API_KEY&file_id=YOUR_FILE_ID"
```


Parameter  | Description
---------- | -------------
key        | your api key
file_id    | file id getted on file upload


Field status in response:

Status value | Description
------------ | -------------
in_progress  | file in progress
finished     | verification finished
canceled     | verification process was canceled by user


Curl example:
```
curl "https://bulkapi.millionverifier.com/bulkapi/v2/fileinfo?key=YOUR_API_KEY&file_id=YOUR_FILE_ID"
```


### Download Report File

URL:
```
https://bulkapi.millionverifier.com/bulkapi/v2/download?key=YOUR_API_KEY&file_id=YOUR_FILE_ID&filter=FILTER
```


Parameter  | Description
---------- | -------------
key        | your api key
file_id    | file id getted on file upload
filter     | ok / ok_and_catch_all / unknown / invalid / all 


    if error http status code != 200
    404 - file not found
    403 - wrong api key


Curl example:
```
curl -o report.csv "https://bulkapi.millionverifier.com/bulkapi/v2/download?key=YOUR_API_KEY&file_id=YOUR_FILE_ID&filter=unknown"
```

## Stop a file: 
This will cancel the file in progress, and results for already verififed emails can be downloaded in a few seconds.
https://bulkapi.millionverifier.com/bulkapi/stop/?api_key=[APIKEY]&file_id=[FILEID]

## Check your credits via API
You can check your credits using https://api.millionverifier.com/api/v3/credits?api=API_KEY

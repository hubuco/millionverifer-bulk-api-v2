<?php

  define("UPLOAD_URL",   "https://bulkapi.millionverifier.com/bulkapi/v2/upload");
  define("PROGRESS_URL", "https://bulkapi.millionverifier.com/bulkapi/v2/fileinfo");
  define("DOWNLOAD_URL", "https://bulkapi.millionverifier.com/bulkapi/v2/download");


  $key=@$_POST["key"];
  if (!$key) $key=@$_GET["key"];

  $file_id = @$_GET["file_id"];

  if (@$_FILES['file_contents']) {
    $cFile = curl_file_create($_FILES['file_contents']['tmp_name'], $_FILES['file_contents']['type'], $_FILES['file_contents']['name']);
    $settings['file_contents'] = $cFile;
    $settings['key'] = $key;
    $ch = curl_init(UPLOAD_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $settings); 
    $res = curl_exec($ch);
    curl_close($ch);
    $o = @json_decode($res);
    $error = @$o->error;
    $file_id = @$o->file_id;
    if (!$error) {
      header("Location: ".$_SERVER['QUERY_STRING']."?file_id=$file_id&key=$key");
      exit;
    }
  } elseif (@$file_id) {
    $res = file_get_contents(PROGRESS_URL."?key=$key&file_id=$file_id");
    $o = @json_decode($res);
  }

  if (!@$error && @$file_id) {
    echo "<pre>".json_encode($o, JSON_PRETTY_PRINT)."</pre>";
    if (!in_array($o->status,['finished', 'canceled'])) {
      echo '<meta http-equiv="refresh" content="1">auto refresh...';
    } else { 
      $url = DOWNLOAD_URL."?key=$key&file_id=$file_id&filter="; 
?>
Download reports:<br>
<li><a href="<?php echo $url?>ok">Ok only</a>
<li><a href="<?php echo $url?>ok_and_catch_all">Ok &amp; Catch All</a>
<li><a href="<?php echo $url?>unknown">Unknown only</a>
<li><a href="<?php echo $url?>invalid">Invalid only</a>
<li><a href="<?php echo $url?>all">Full report</a>
<?php
    }
  }

  if (@$error) {
    echo "<pre>Error: $error</pre>";
  }

  if (!@$file_id) { ?>
<form method="post" enctype="multipart/form-data">
  <table border="1" cellpadding="20" cellspacing="0">
  <tr><th>Key</th><td><input name="key" value="<?php echo $key; ?>" placeholder="your api key"></td></tr>
  <tr><th>File</th><td><input type="file" name="file_contents"></td></tr>
  <tr><td colspan="2" align="center"><input type="submit" value="Verify"></td></tr>
  </table>
</form>
<?php
  }


<?php

include 'required.php';
 
$acct = get_arg('--acct');
$api_key = get_arg('--key');
$api_secret = get_arg('--secret');

 if ($acct == null AND $api_key == null AND $api_secret == null) {
    echo "\n\n";
    echo "Either an agent name is required (--acct 'name').\n";
    echo "Or an api key and secret is required.\n";
    echo "(--key 'api key' --secret 'api secret')\n";
    die ("\n");
} elseif ($acct != null) {
    $client = account($acct);
} elseif ($api_key == null) {
    die ("\n\nIf not using a test account, please enter in an API key using: --key 'api key'\n\n");
} elseif ($api_secret == null) {
    die ("\n\nIf not using a test account, please enter in an API secret using: --secret 'api secret'\n\n");
} else {
    $client = new Sailthru_Client($api_key, $api_secret);
}


$file = get_arg('--file');  //assign file based off of CLI input
$list = get_arg('--list');  //Do you want to add a user to a list? 

$csv = fopen($file, 'r');  //open file

// Counting the number of lines in the file to read from
if($csv){
    while(!feof($csv)){
          $count = fgets($csv);
      if($count)    $lines++;
    }
}
fseek($csv,0);
$lines = $lines - 1;

$blah = fgetcsv($csv, 1024, ","); //set the header/key of each array

$i = 0;
$current = 1;
if (($handle = fopen($file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1024, ",")) !== FALSE) {
	unset($new);
	unset($keys);
	unset($vals);
	unset($pay);
	if ($blah != $data ){
	   $new = array_combine($blah, $data);
//           var_dump($new);  
	   $keys = array_keys($new);
	   $vals = array_values($new);
        
	   $c = count($new);
	   $x = 0;
	   while ($x < $c) {
		if ($keys[$x] == "email") {
		   $pay['id'] = trim($vals[$x]);
		} else {
		   $vars[$keys[$x]] = trim($vals[$x]);
		}
	   $x++;
	   }
	   $pay['vars'] = $vars;
	   if ($list) {
		$pay['lists'] = array($list => 1);
	   }

//	   var_dump($pay);
	   $email = $pay['id'];
	   echo "\n";
	   echo "Line $current of $lines";
	   echo "\n";
	   echo "$email";
	   echo "\n";

	   var_dump($response = $client->apiPost('user',$pay));
	   $current++;

	}
    }
}




echo "\n\n";
fclose($csv);

?>

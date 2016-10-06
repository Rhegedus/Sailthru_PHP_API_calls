<?php

include 'required.php';

$acct = get_arg('--acct');
$api_key = get_arg('--key');
$api_secret = get_arg('--secret');

$test = get_arg('--test');  // use this if you do not want to send the call anywyere, but just wnat to test syntax :)

if ($test != null) { 
   if ($acct == null AND $api_key == null AND $api_secret == null) {
      echo "\n\n";
      echo "Either an agent name is required (--user 'name').\n";
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
}

/*
Everything above this should be in the start of every call
*/

// Define the file location and if you want to re-spider the content
$file = get_arg('--file');
$spider = get_arg('--spider');


// This is where the magic happens!!
$handle = fopen($file,'r');

if ($handle)
   {
       while(($url = fgets($handle, 2056)) !== false) 
	 {

		if (strpos($url,'url') !== false) {

			echo "\n $url \n";

			$strpos1 = strpos($url,'http');
			$strpos2 = strpos($url,'"',$strpos1);
			$url2 = substr($url,$strpos1,$strpos2 - $strpos1);

			$data = array();
			$data['url'] = $url
			$data['spider'] = $spider;

			$response = $client->apiPost('content', $data);

			foreach ($response as $x) {
				print_r($x);
				echo "\n";
			}
		}



	}
} else {
	echo "Failed";
}



?>

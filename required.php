<?php

require_once 'Sailthru_Client_Exception.php';
require_once 'Sailthru_Client.php';
require_once 'Sailthru_Util.php';

function get_arg($arg, $default = null) {
    $i = array_search($arg, $GLOBALS['argv']);
    if ($i === false) {
        return $default;
    } else {
        return $GLOBALS['argv'][$i+1];
    }
}

function account($acct) {
	$acct = strtolower($acct);

/*
This is where you can set the different key/secrets for the accounts. Please follow the established format.	
$name_of_account = array('key' => 'key of test account', 'secret' => 'secret of test account');
*/
	$test_1 = array('key' => 'abc123', 'secret' => '123abc');
	$prod_1 = array('key' => 'ABC123', 'secret' => '123ABC');
	$test_2 = array('key' => 'def234', 'secret' => '234def');
	$proc_2 = array('key' => 'DEF234', 'secret' => '234DEF');
/* 
This section allows you to setup an alias for an account, or add new accounts as desired.
Go into the array, and add the user in the following format:
'alias/nickname' => 'name_of_account',
The name_of_account will need to match the name_of_account from above.
*/
        $name = array(
				't1' => 'test_1',
				't2' => 'test_2',
				'p1' => 'prod_1',
				'p2' => 'prod_2',
				'prod2' => 'prod_2',
				'production_1' => 'prod_1'
				);

/*
This section addes the top to together so that we can pass the required information over into Saailthru_Client.
Please add the new user inside of the array in  the following format
'first_name' => $first_name,
*/
	$access = array(
				'test_1' => $test_1,
				'test_2' => $test_2,
				'prod_1' => $prod_1,
				'prod_2' => $prod_2
					);


	$acct_name = $name[$acct];

	$account_access = new Sailthru_Client($access[$acct_name]['key'], $access[$acct_name]['secret']);

	return $account_access;
}


// Convert the id returned from get send into a valid send_id
function send_to_object($input) {
	$id1 = base64_decode(strtr($input, '-_,', '+/='));
	$obj_id = bin2hex($id1);
	return $obj_id;

}

// Convert the send_id into the id that you get from doing a get send call
function object_to_send($data) { 
	$send1 = hex2bin($data);
	$send_id = rtrim(strtr(base64_encode($send1), '+/', '-_'), '='); 
	return $send_id;
} 


// Nifty function that will auto-download export files provided the job_id
function auto_download($id, $client, $pre) {
	$data['job_id'] = $id;

	$get_status = $client->apiGet('job', $data);

	if (isset($get_status['error'])) { 
	  echo "\n**************************\n";
	  print_r($get_status['errormsg']);
	  echo "\n**************************\n";
	  exit;
	} elseif (isset($get_status['expired'])) {
	  echo "\n**************************\n";
	  echo "File/Export has expired. Please re-run the job.";
	  echo "\n**************************\n";
	  exit;
	} else {
	  $status = $get_status['status'];
	}


	$i = 1;
	while ($status != "completed"):
	   echo "\n $i (in seconds)\n";;
	   echo "status: $status\n";

	   $x = 0;
	   while ($x <6) :
	      echo ".";
	      $x++;
	      sleep(1);
	   endwhile;

	   $i = $i+6;
	   $response = $client->apiGet('job',$data);
	   $status = $response['status'];

	endwhile;

	echo "\nJob done - Exporting now\n";


	$url = $response['export_url'];
	$filename = $pre . "_" . $response['filename'];
	$filename = str_replace("/", ".", $filename);
	$path = $filename;


	echo "\nURL: $url \n";
	echo "\nFilename: $filename \n";
	echo "\nPath: $path \n";
	   

	$file = fopen($url, "rb");
	if ($file) {
	    $newf = fopen($filename, "wb");

	    if ($newf)
	    while(!feof($file)) {
	      fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
	    }
	}

	if ($file) {
	    fclose($file);
	}

	if ($newf) {
	    fclose($newf);
	}

	return $filename;
}


?>

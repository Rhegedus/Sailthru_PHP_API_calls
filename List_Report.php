<?php

include 'required.php';

/* 
1 - First we get a list of lists from the API for the client in question.
2 - Run a loop on the list_id for each list.
3 - Capture all of the information and format it so that it can be written to a CSV
4 - CSV format should be:
	list_name, Count, Type (Normal/Smart), Primary, Created, Last Sent
*/

/* List of clients will be in a CSV.
Format is: company name, cid, api_key, api_secret
1 - Read the CSV, and get the row information
2 - Make the API Get call
3 - Loop through the results and push them to a new array
4 - Create new CSV. filename = Company_Name_CID.csv
5 - Make the API Get call of the new list loop
6 - Parse and format information to write to CSV.
	Format of the CSV: 	list_name, Count, Type (Normal/Smart), Primary, Created, Last Sent
7 - Rinse and Repeat
*/

/* Adding in error catching
Check to see if $response[error] is set.
If it is set, then write the error number, and the error message to the CSV.
Also store it in an error array. Something like this:
{error:{cid:{error_code:X,error_msg:message here}}}
And then at the very end print to the screen, the number of accounts that worked and failed.
Success
CID - Filename
Failure
CID - Error_msg
*/

/* Example of CSV to read from
company_name,cid,api_key,api_secret
robert,4306,KEY HERE,SECRET HERE
irina,4105,KEY HERE,SECRET HERE
Open Sky Dev 2,KEY HERE,SECRET HERE
*/

$file = 'cid.csv';

$csv = fopen($file, 'r');  //open file

$lines = 0;
$done = 0;

// Counting the number of lines in the file to read from
if($csv){
    while(!feof($csv)){
          $count = fgets($csv);
      if($count)    $lines++;
    }
}
fseek($csv,0);
$lines = $lines - 1;
echo "\n\nWe have a total of $lines clients to go through.\n\n";

$header = fgetcsv($csv, 1024, ","); //set the header/key of each array

$success = [];
$error = [];

if (($handle = fopen($file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1024, ",")) !== FALSE) {
    	if ($header != $data) {
			$cust = array_combine($header, $data);

    		echo "\nProcessing:";
    		echo "\n" . $cust['company_name'] . " - CID " . $cust['cid'];
    		echo "\n";
    	
    		$client = new Sailthru_Client(trim($cust['api_key']), trim($cust['api_secret']));
    		$get_list = $client->apiGet('list',array('limit' => 50000));

            $company_name = str_replace(' ', '_', $cust['company_name']);

            $write_file = $company_name . "_" . $cust['cid'] . ".csv";
            $write = fopen($write_file, "w");

            if (isset($get_list['error'])) {
                fputcsv($write,array('Error Code', 'Error Message'));
                fputcsv($write,array($get_list['error'], $get_list['errormsg']));

                $error[$cust['cid']] = array('company_name' => $cust['company_name'], 'error' => $get_list['error'], 'error_msg' => $get_list['errormsg']);
                
                echo "\n";
                echo "Customer " . $company_name . "/" . $cust['cid'] . " had an error:";
                echo "\n     Error Code: " . $get_list['error'];
                echo "\n     Error Mesg: " . $get_list['errormsg'];

            } else {
                fputcsv($write,array('List Name','List ID', 'Last Send Time', 'Email Count', 'Valid Count', 'Create Time', 'Type', 'Primary/Secondary'));

                $list_count = count($get_list['lists']);
                echo "\n$list_count lists to process.";
                
                array_push($success, $cust['cid']);

                foreach ($get_list['lists'] as $x) {
                    $get_list_id = $client->apiGet('list', array('list_id' => $x['list_id']));
                    if ($get_list_id['send_time'] == null) {
                        $send_time = 'Not sent to';
                    } else {
                        $send_time = $get_list_id['send_time'];
                    }

                    if ($get_list_id['primary'] == true) {
                        $primary = 'Primary';
                    } else {
                        $primary = 'Secondary';
                    }

                    if ($x['type'] == 'normal') {
                        $type = "Natural";
                    } else {
                        $type = "Smart";
                    }

/*
                    echo "\nName - " . $x['name'];
                    echo "\nList id - " . $x['list_id'];
                    echo "\nEmail Count - " . $x['email_count'];
                    echo "\nValid Count - " . $x['valid_count'];
                    echo "\nCreate Time - " . $x['create_time'];
                    echo "\nType - " . $type;
                    echo "\nSent Time - " . $send_time;
                    echo "\nPrimary/Secondary - " . $primary;
                    echo "\n";
*/

                    fputcsv($write, array($x['name'], $x['list_id'], $send_time, $x['email_count'], $x['valid_count'], $x['create_time'], $type, $primary));
                
                    --$list_count;
                    echo "\n$list_count lists left to process.";

                }

    		}

    	fclose($write);

		++$done;
    	echo "\n";
    	echo "\nSo far $done of $lines have been completed.\n";
        }

    }
}

if (count($success) > 0) {
    echo "\n";
    echo "There were a total of " . count($success) . " success(es).";
    foreach ($success as $s) {
        echo "\nCID: $s";
    }
} else {
    echo "\nNo Successes :(\n";
}

if (count($error) > 0) {
    echo "\n";
    echo "There were a total of " . count($error) . " error(s).";
    foreach ($error as $e => $d) {
        echo "\nCID: " . $e;
        echo "\n     Company Name: ". $d['company_name'];
        echo "\n     Error Code: " . $d['error'];
        echo "\n     Error Mess: " . $d['error_msg'];
    }
} else {
    echo "\nThere were no failed attempts. AWESOME!!!!\n\n";
}


fclose($csv);

echo "\n\n";

?>
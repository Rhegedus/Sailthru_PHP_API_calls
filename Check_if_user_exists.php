<?php

/*
This file is setup to be run via the CLI, however it can easily be modifed by uncommenting lines: 12-14 
And commenting out lines: 17-35
OR setting the api_key and secret directly on lines 21 and 22
Can set the account key/secrets in the required.php file if there are multiple accounts to quickly test against a test account then prod account
 

And example of running it via the CLI is: php Check_if_userexists.php --key abc123 --secret 123abc --read read.csv --write write.csv
*/

include 'require.php';

/*
$client = account('prod_1');
*/


$acct = get_arg('--acct');
$api_key = get_arg('--key');
$api_secret = get_arg('--secret');

if ($acct == null AND $api_key == null AND $api_secret == null) {
    echo "\n\n";
    echo "Either an agent name is required (--acct 'test_1').\n";
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


$read_file = get_arg('--read');  // File to read emails from csv format - 1 email per line
$write_file = get_arg('--write');  // File to write results to


$lines =0;
$write = fopen($write_file,"w");  // Open/create the file to write the results to
$read = fopen($read_file, "r");  // Open the file to read the emails from


// Counting the number of lines in the file to read from
if($read){
    while(!feof($read)){
          $count = fgets($read);
      if($count)    $lines++;
    }
}
echo "\nlines - $lines\n";

$i = 0;
$error_count = 0;
$noerror_count = 0;

$found = "Email found: ";
$not_found = "Emails not found: ";


fseek($read,0);  //Set file pointer to start of file


// This is where the magic happens
while (($email = fgetcsv($read, 1024, ",")) !== FALSE) {
   
   $t_email = trim($email[0]); // remove any white space around the email address

   $i = $i+1;
   echo "\nline: $i of $lines\n";
   echo "email: $t_email\n";

   $data['id'] = $t_email; 

   $response = $client->apiGet('user', $data);  // Api call to check if the user exists

   if ($i == 1) {
      fputcsv($write,array('email','Present (Y or N)'));  // Writing the header of the csv
   }
   
   $error_string = "User not found with email: " . $t_email;

// More Magic here
// This if statement checks the response from the API call
   if (!$response['errormsg']) {    // If the email is found (no error message) do this first IF statement
      echo "\nEmail Found $t_email\n";
      if ($noerror_count == 0) {
         $found = $found . $t_email;
      } else {
         $found = $found . ", " . $t_email;
      }

      $input = array($t_email,'Y');
      fputcsv($write,$input); 
      $noerror_count++;

   } else if ($response['errormsg'] == $error_string){   // If the email is not found, and matchs the error_string above do this
      echo "\nEmail Not Found $t_email\n";
      if ($error_count == 0) {
         $not_found = $not_found . ", " . $t_email;
      } else {
         $not_found = $not_found . $t_email;
      }

      $input = array($t_email,'N');
      fputcsv($write,$input);
      $error_count++;

   } else {  // An error message was given in the response that does not have to do with the email not being found.
      echo "\n\n\n\n\n I don't know what to do with this error!!!!!!! \n";
      print_r($response);
      echo "\n\n\n\n\n\n\n\n";
   }
 

}


// Display the results
echo "\n";
echo "# of emails found = $noerror_count";
echo "\n";
echo $found;
echo "\n";
echo "# of email not found = $error_count";
echo "\n";
echo $not_found;
echo "\n";



fclose($write);
fclose($read);

?>
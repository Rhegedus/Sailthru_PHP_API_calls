<?php

include 'required.php';
 
$acct = get_arg('--acct');
$api_key = get_arg('--key');
$api_secret = get_arg('--secret');
         

$email = get_arg('--email');
$template = get_arg('--template');
$schedule_time = get_arg('--schedule');
$schedule_time['start_time'] = get_arg('--start_time');
$schedule_time['end_time'] = get_arg('--end_time');
$headers_Cc = get_arg('--CC');
$headers_Bc =get_arg('--BC');
$headers_replyto = get_arg('replyto');
$options['test'] = get_arg('--test');
$options['behalf_email'] = get_arg('--behalf');
$limit['name'] = get_arg('--limit');
$limit['within_time'] = get_arg('--limitwithin');
$limit['conflict'] = get_arg('--confligt');


 if ($acct == null AND $api_key == null AND $api_secret == null) {
    echo "\n\n";
    echo "Either an agent name is required (--acct 'name').\n";
    echo "Or an api key and secret is required.\n";
    echo "(--key 'api key' --secret 'api secret')\n";
    die ("\n");
} elseif ($acct != null) {
    $client = userkey($acct);
} elseif ($api_key == null) {
    die ("\n\nIf not using a test account, please enter in an API key using: --key 'api key'\n\n");
} elseif ($api_secret == null) {
    die ("\n\nIf not using a test account, please enter in an API secret using: --secret 'api secret'\n\n");
} else {
    $client = new Sailthru_Client($api_key, $api_secret);
}


if (!empty($argv[1]) AND strtolower($argv[1]) == 'help') {
    echo "\n\n";
    echo "This is the Help for Send2.php.\n";
    echo "There are 2 required entries for the Send API call.\n";
    echo "1- Template Name (--template 'template name')\n";
    echo "2- Email address (--email 'email address')\n";
    echo "\n";
    echo "There are several optional params that can be passed as well.\n";
    echo "Options:\n\n";
    echo "Option: schedule_time (--sechedule 'time'):\n";
    echo " Description: To send the email at some point in the future,\n";
    echo "   otherwise it will send immediately. Any date recognized\n";
    echo "   by PHP's strtotime function is valid, but be sure to\n";
    echo "   specify timezone or use a UTC time to avoid confusion.\n";
    echo "   You may also use relative time; see Examples below.\n";
    echo " Example: '2013-09-08 20:00:00'\n";
    echo "   'Saturday, September 14, 2012 9:00pm EST -2 days'\n";;
    echo "   'tomorrow 09:30 UTC'\n";
    echo "   'now'\n";
    echo "\n";

    echo "Option: schedule_time['start_time'] (--start_time 'time'):\n";
    echo " Description: Set a start_time when using Personalized Send\n";
    echo "   Time. Any date recognized by PHP's strtotime function is\n";
    echo "   valid, but be sure to specify timezone or use a UTC time\n";
    echo "   to avoid confusion.\n";
    echo " Example: '2013-09-08 20:00:00'\n";
    echo "   'Saturday, September 14, 2012 9:00pm EST -2 days'\n";
    echo "   'tomorrow 09:30 UTC'\n";
    echo "   'now'\n";
    echo "\n";

    echo "Option: schedule_time['end_time'] (--end_time 'time');\n";
    echo " Description: Set a start_time when using Personalized Send\n";
    echo "   Time. Any date recognized by PHP's strtotime function is\n";
    echo "   valid, but be sure to specify timezone or use a UTC time\n";
    echo "   to avoid confusion.\n";
    echo " Example: '2013-09-08 20:00:00'\n";
    echo "   'Saturday, September 14, 2012 9:00pm EST -2 days'\n";
    echo "   'tomorrow 09:30 UTC'\n";
    echo "   'now'\n";
    echo "\n";




    die();
}

$number = get_arg('--n'); //How many times do you want this to run? This is good for testing.
     
$data = array('email' => $email);

if (!empty($headers_Cc)) {
    $headers['Cc'] = $headers_Cc;
}

if (!empty($header_Bc)) {
    $headers['Bc'] = $headers_Bc;
}

if (!empty($headers_replyto)) {
    $headers['replyto'] = $headers_replyto;
}

if (!empty($headers)) {
    $options['headers'] = $headers;
}

if (!empty($template)) {
    $data['template'] = $template;
} else {
    die("\n\nYou forgot to add template (this is required):  --template 'Template Name'\n\n");
}

if (!empty($email)) {
    $data['email'] = $email;
} else {
    die("\n\nYou forgot to provide an email address to send this to (this is required): --email 'email address'\n\n");
}
     
if ($number == 0) {
    $number = 1;
} 
       
$i = 0;
while ($i < $number) { 
    $response = $client->apiPost('send', $data);
    $i++;
    echo "\n $i \n";
    var_dump($response);
}
      
echo "\n";
      
?>

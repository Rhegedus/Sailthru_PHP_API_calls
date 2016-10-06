<?php

include "required.php";

// Accont to copy Templates From
$from_key = '';
$from_secret = '';
$client_from =  new Sailthru_Client($from_key, $from_secret);

// Account to copy Templates To
$to_key = '';
$to_secret = '';
$client_to =  new Sailthru_Client($to_key, $to_secret);


$templ_ids = file("template_ids.csv");
$lines = count($templ_ids);



//If you want to see a list of the Template_ids prior to coping them, uncomment these lines. Otherwise leave them commented out.
/*
echo "\n\n Template_ids \n";
print_r($templ_ids);
*/

$copied = "";
$x = 1;
foreach ($templ_ids as $templ) {
	echo "\nLine $x of $lines\n";
	echo "\nTempalte ID: $templ\n";
	$x++;

	$data['template_id'] = $templ;
	$response = $client_from -> apiGet('template',$data);

// This block sets the different vars from the resposne of getting the Template info.
	
	$response_from = $response;
	
	$name = $response_from['name'];
	$pub_name = $response_from['public_name'];
	$from_name = $response_from['from_name'];
	$from_email = $response_from['from_email'];
	$reply_to = $response_from['replyto_email'];
	$subject = $response_from['subject'];
	$content_html = $response_from['content_html'];
	$content_text = $response_from['content_text'];
	$link_tracking = $response_from['is_link_tracking'];
	$google_ana = $response_from['is_google_analytics'];
	$setup = $response_from['setup'];
	$link_param = $response_from['link_params'];
	$data_feed = $response_from['data_feed_url'];


// This block is for testing. Lets you see how things look.
/*
//	print_r($response_from);

	echo "Template Name	: $name\n";
	echo "Public Name	: $pub_name\n";
	echo "From Name	: $from_name\n";
	echo "From Email	: $from_email\n";
	echo "Reply To Email	: $reply_to\n";
	echo "Subject		: $subject\n";
	echo "Link tracking	: $link_tracking\n";
	echo "Google Analytics: $google_ana\n";
	echo "Setup/Advanced	: $setup\n";
	echo "Link Params	: $link_param\n";
	echo "Data Feed	: $data_feed\n";
	echo "Content HTML	: $content_html\n";
	echo "Content Text	: $content_text\n";

	echo "\n\n\n\n";
*/

// Adding the different parts into an array to pass with the push call.
	$tem['template'] = $name;
	$tem['public_name'] = $pub_name;
	$tem['from_name'] = $from_name;
	$tem['from_email'] = $from_email;
	$tem['replyto_email'] = $reply_to;
	$tem['subject'] = $subject;
	$tem['content_html'] = $content_html;
	$tem['content_text'] = $content_text;
	$tem['is_link_tracking'] = $link_tracking;
	$tem['is_google_analytics'] = $google_ana;
	$tem['setup'] = $setup;
	$tem['data_feed'] = $data_feed;
	$tem['link_params'] = $link_param;

// Testing what the array looks like.
/*
	var_dump($tem);
	echo "\n\n\n\n";
*/

	$response_to = $client_to -> apiPost('template',$tem);
	echo "\n\nResponse from posting the templates\n\n";
	
	print_r($response_to);

// Adding the copied templates into an array to print at the very end.
	$copied = $copied . " " . trim($templ);

// Testing. By putting in a 10 second pause it lets you stop the script to check out what is going on.
//	sleep(10);


}

echo "\n Templates that were copied. \n $copied \n\n";

?>

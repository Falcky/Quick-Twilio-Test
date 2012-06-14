<?php /*?>
  ~ Copyright 2012 Claudie Tirefort
  ~
  ~ Claudie Tirefort licenses this file to you under the Apache License, version 2.0
  ~ (the "License"); you may not use this file except in compliance with the
  ~ License.  You may obtain a copy of the License at:
  ~
  ~    http://www.apache.org/licenses/LICENSE-2.0
  ~
  ~ Unless required by applicable law or agreed to in writing, software
  ~ distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
  ~ WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the
  ~ License for the specific language governing permissions and limitations
  ~ under the License.
<?php */?>

<?php
// Start the session
session_start();

// Get the session varible if it exists
$counter = $_SESSION['counter'];

// If it doesn't, set the default
if(!strlen($counter)) {
	$counter = 0;
}

// Increment it
$counter++;

// Save it
$_SESSION['counter'] = $counter;

// Make an associative array of senders we know, indexed by phone number
$people = array(
	"+14158675312"=>"Your Highness",
	"+14158675310"=>"Evil Monkey",
	"+14158675311"=>"Lil' Bug",
);

// If the sender is known, then greet them by name.
// Otherwise, consider them just another stranger
if (!$name = $people[$_REQUEST['From']]) {
	$name = "Beautiful Stranger";
}

// Call us
function callC(){
	// Include the Twilio PHP library
	require 'Services/Twilio.php';

	// Twilio REST API version
	$version = "2010-04-01";

	// Set our Account SID and AuthToken
	$sid = '000000000000000000';
	$token = '000000000000000000';

	// A phone number you have previously validated with Twilio
	$phonenumber = '+14155992671';
	
	// Instantiate a new Twilio Rest Client
	$client = new Services_Twilio($sid, $token, $version);
	try {
		// Initiate a new outbound call
		$call = $client->account->calls->create(
			$phonenumber, // The number of the phone initiating the call
			'000000000', // The number of the phone receiving call
			'http://www.example.com/sms-hired.xml' // The URL Twilio will request when the call is answered
		);
	} catch (Exception $e) {} 
}

if ($counter == 1) {
    $replymessage = "Hello " . $name . " Your options: 'why' for why hire me, 'call' to tell I'm hired, 'bye' to exit.";
}
else {
    if ($_REQUEST['Body'] == "call") {
        $replymessage = "Yay! You are the best, my dear " . $name . ". We are calling.";
        callC();
    }
    elseif ($_REQUEST['Body'] == "bye") {
        $replymessage = "Leaving already, my dear " . $name . "? Alright, have a great day!";
        $counter = 0;
        $_SESSION['counter'] = $counter;
    }
    elseif ($_REQUEST['Body'] == "why") {
        $replymessage = "Because, my dear " . $name . ", I'm totally awesome.";
    }
    else {
        $replymessage = "Sorry dear " . $name . ", but I'm lost in translation. Please try again. ";
    }
}

echo "<?xml version='1.0' encoding='utf-8' ?>";
?>

<Response>
<Sms><?php echo $replymessage ?></Sms>
</Response>

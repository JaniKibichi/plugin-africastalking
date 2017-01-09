<?php

include 'Africastalking.php';
include 'Playsms_Africastalking.php';

	$username   = 'welovenerds';
	$apikey     = 'b5Jzo6hZlPM9gL0YLyhDphqSHWsNLm0SGO9m5g6V';
	$recipients = "+254701789876";
	$message    = "Welcome to the new world!";
				//remove sandbox flag when going live. use sandbox at https://sandbox.africastalking.com
	$gateway    = new AfricasTalkingGateway($username, $apikey, "sandbox");

	try 
		{ 
		   $results = $gateway->sendMessage($recipients, $message, $from, $options, $bulkSMSMode);
		            
		  foreach($results as $result) {
		    // status is either "Success" or "error message"
		    echo " Number: " .$result->number;
		    echo " Status: " .$result->status;
		    echo " MessageId: " .$result->messageId;
		    echo " Cost: "   .$result->cost."\n";
		  }
	}
	catch ( AfricasTalkingGatewayException $e ){
		  echo "Encountered an error while sending: ".$e->getMessage();
	
	}
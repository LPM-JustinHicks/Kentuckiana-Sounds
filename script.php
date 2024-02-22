<?php
	date_default_timezone_set('America/New_York');
	$timestamp = date('m-d-Y_H-i-s');
	// First of all we have to check if the form is submitted via the POST
	// method.
	if(isset($_POST['submit'])){
		// If the form is submitted we are gonna create a new associative array
		// to hold the values we will store.
		$new_message = array(
			"admin_approved" => "no",
			"firstname" => $_POST['firstname'],
			"lastname" => $_POST['lastname'],
			"email" => $_POST['email'],
			"sourcing" => $_POST['sourcing'],
			"recordingdate" => $_POST['recordingdate'],
			"message" => htmlspecialchars($_POST['message']),
			"audio-latitude" => $_POST['audio-latitude'],
			"audio-longitude" => $_POST['audio-longitude'],
			"submission_timestamp" => $timestamp,
			"file_name" => htmlspecialchars( basename($_POST['firstname'].$_POST['lastname']."_".$timestamp.".".pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION))),
		);
		
		// Before storing the $new_message[] array, we have to check if this is 
		// the first record.
		// We are doing this by checking the filesize.
		if(filesize("messages.json") == 0){
			// if this is the first record, we creating an array to hold out message.
			$first_record = array($new_message);
			// The only purpose of this step is to create an array inside the json 
			// file to hold our messages. You will see in sec.
			
			/* I'll assign the record to a generic variable for later use. */
			$data_to_save = $first_record; 
		}else{
			// If this is not the first record, and there are messages stored in the file.
			// We have to pull all those old messages so we can add the new one.
			// And also we have to decode the data we fetch.
			$old_records = json_decode(file_get_contents("messages.json"));

			// We know that all of our messages are stored inside an array,
			// because we created that array with the first record.
			// Now we can add to that array our new message.
			array_push($old_records, $new_message);

			/* and i'll assign the record to our generic variable. */
			$data_to_save = $old_records;
		}

		// Now our last step is to store the data to the file (messages.json).
		if(!file_put_contents("messages.json", json_encode($data_to_save, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX)){
			// if something went wrong, we are showing an error message.
			$error = "Error storing message, please try again";
			echo @$error;
		}else{
			// and if everything went ok, we show a success message.
			$success =  "Thanks! We got your information ";
			echo @$success;
		}

		//Send a message to Slack
		// Create a constant to store your Slack URL
		define('SLACK_WEBHOOK', 'https://hooks.slack.com/services/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');
		// Make your message
		$messagetoslack = array('payload' => '{"channel":"C06KU5DEQT0","blocks": [		{			"type": "section",			"text": {				"type": "mrkdwn",				"text": "There is a new submission to Kentuckiana Sounds! We need to <https://assets.mixkit.co/active_storage/sfx/1755/1755-preview.mp3|listen to the audio> to see if it is appropriate for public use."			}		},		{			"type": "input",			"element": {				"type": "checkboxes",				"options": [					{						"text": {							"type": "plain_text",							"text": "Approve Submission",							"emoji": true						},						"value": "yes"					},					{						"text": {							"type": "plain_text",							"text": "Reject Submission",							"emoji": true						},						"value": "no"					}				],				"action_id": "checkboxes-action"			},			"label": {				"type": "plain_text",				"text": "Please make a decision:",				"emoji": true			}		}	]}');
		// Use curl to send your message
		$c = curl_init(SLACK_WEBHOOK);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, $messagetoslack);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_exec($c);
		curl_close($c);
		

	}

$imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));
$target_dir = "uploads/";
@$target_file = $target_dir . basename($new_message["file_name"]);
$uploadOk = 1;

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {

// Check if file already exists
if (file_exists($target_file)) {
  echo "<br><strong>Hmm...it seems this file already exists.</strong>";
  $uploadOk = 0;
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 25000000) {
  echo "<br><strong>Your file is too large. It must be smaller than 25mb.</strong>";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "mp3" && $imageFileType != "wav" && $imageFileType != "m4a" ) {
  echo "<br><strong>Sorry, only .mp3, .wav or .m4a files are allowed.</strong>";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "<br>Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "and your audio file.";
  } else {
    echo "but there was a problem uploading your file.";
  }
}
}

?>

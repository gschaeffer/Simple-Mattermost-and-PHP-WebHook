<!DOCTYPE html>
<html lang="en">
<?php 

/*
	Example Mattermost webhook integration using PHP.
	Author: G Schaeffer

	This example sends data to the Team and Channel selected on the form.

	Instructions:
	1. Within Mattermost create the WebHook for each team you'd like to send data to.
	2. Update the JavaScript 'teams' array below to include your team name & WebHook url for each team.
	3. Update the JavaScript 'channels' array to include the channels you would like to post to. 
	   * Note - if channel is not found check the channel 'handle'. It is viewable within Mattermost
	   	 under the Rename Channel menu option.
	4. Test the integration. 
*/

$hidden = "hidden";	// show or hide the post result DIV.

// Check for submits
if(!empty($_POST['form1-submit'])){
	$form1Result = actionForm1($_POST);	// $_POSTS holds tghe Team, Channel, Message values from the form.
	$hidden = "";
}
?>

<head>
	<meta charset="UTF-8">
	<title>Example Mattermost webhook using PHP</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" 
		integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<script src="http://code.jquery.com/jquery-3.1.1.min.js" 
		integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
</head>

<body>
	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Submit Message to Mattermost</h3>
					</div>
					<div class="panel-body">
						<form action="" method="post">
							<div class="form-group">
								<?php // Team ?>
								<label for="teamSlct">Team:</label>
								<select class="form-control" name="teamSlct" id="teamSlct">
									<option></option>
								</select>
							</div>
							<div class="form-group">
								<?php // Channel ?>
								<label for="channelSlct">Team:</label>
								<select class="form-control" name="channelSlct" id="channelSlct">
									<option></option>
								</select>
							</div>
							<div class="form-group">
								<?php // Message ?>
								<label for="message">Message:</label>
								<textarea class="form-control" rows="5" name="message">
| Activity | Due | Progress | Assigned | By |
|:--|:--|:--|
| Sample WebHook | Feb 20 | 10 | Mike | @Felix |
| Define scope | Feb 20 | 10 | Joe | @Brian |
| Submit purchase | Feb 22 | :white_check_mark: | Lou | Sara |
								</textarea>
							</div>
							<div class="form-group">
								<div class="alert <?php echo $form1Result == 'ok' ? 'alert-success' : 'alert-danger' ?> <?php echo $hidden; ?>">
									<?php echo $form1Result; ?>
								</div>
								<input type="submit" class="btn btn-default" name="form1-submit" value="Post to M2" />
							</div>
						</form>
					</div>
				</div>
			</div><?php // end row 1 col 1 ?>
		</div></div><?php // end row 1 ?>

	</div>

	<script type="text/javascript">

		// TO-DO: Change the teams var to use your team name and webhook url 
		var teams = {
			"Team Alpha": "https://my.mattermostinstall.com/hooks/webhookstring",
			"Team Bravo": "https://my.mattermostinstall.com/hooks/webhookstring"
		};
		$('#teamSlct').html($.map(teams, function (val, key) {
		    return '<option value="' + val + '">' + key + '</option>';
		}).join(''));

		// TO-DO: Change the channels var to the names of your channels. Using the channel 'handle' is best.
		var channels = { 1:"Town Square", 2:"Off-Topic" };
		$('#channelSlct').html($.map(channels, function (val) {
		    return '<option value="' + val + '">' + val + '</option>';
		}).join(''));

	</script>
</body>
</html>
<?php

function actionForm1(){

// get the selected webhook key from the form.
$url = $_POST['teamSlct'];

// We need the channel's 'handle' rather than the name. It's usually
//  the same as the name with a '-' in place of empty spaces.
$channel = str_replace( " ", "-", $_POST['channelSlct'] );

// Data to post/display (in heredoc syntax) - Testing only. 
/*$data = <<< data
| Activity | Due | Progress | Assigned | By |
|:--|:--|:--|
| Sample WebHook | Feb 20 | 10 | Mike | @Felix |
| Define scope | Feb 20 | 10 | Joe | @Brian |
| Submit purchase | Feb 22 | :white_check_mark: | Lou | Sara |
data;*/

$data = $_POST['message'];
$data = str_replace(array("\r"), "", $data);

$payload = 'payload={"channel": "'.$channel.'", "text": "';
$payload .= str_replace('"', '\"', trim($data) );
$payload .= '"}';

// Open connection
$curl_conn = curl_init();

// Set cURL options. 
curl_setopt($curl_conn, CURLOPT_URL, $url);
curl_setopt($curl_conn, CURLOPT_POST, 1);
curl_setopt($curl_conn, CURLOPT_POSTFIELDS, $payload);
curl_setopt($curl_conn, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($curl_conn);

// Close connection
curl_close($curl_conn);

// Check if request was processed. 
if($server_output == 'ok'){
	//var_dump('All good. Successful.');
} else {
	//var_dump('Not successful.');
}

return $server_output;
}

<?php

header('Content-Type: application/json');

// function to validate the input data
function sanitize_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

// function to check if two input hash strings are equal
function hash_equals($a, $b) {
	$ret = strlen($a) ^ strlen($b);
	$ret |= array_sum(unpack("C*", $a^$b));
	return !$ret;
}

// hash check function given salt value from request and retreived from DB
function hashcheck($requestSalt, $dbSalt){
	$dbHash = md5($dbSalt);	//using md5 hash as example
	$requestHash = md5($requestSalt);
	
	return (hash_equals($dbHash, $requestHash));
}

function validate_coins($coinsBet,$coinsWon,$credits){
	
	// not sure of the proper way to validate this data
	if ($coinsBet <= $credits)
		if($coinsWon <= 10000)
			return true;

	return false;
}

// function to validate the player data from MySQL database
function validate_player($data){
	
	//using basic mysql DB & table for testing purposes
	//in the real world this would be encrypted 
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "testdb";

	// define variables and set to empty values
	$dbPlayerName = $dbPlayerCredits = $dbPlayerLifetimeSpins = $dbSaltValue =  "";
	
	// connect to the DB
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	// get the data for playerID 
	$sqlRetrieve = "SELECT Name, Credits, LifetimeSpins, SaltValue FROM player WHERE PlayerID=" . $data["playerID"];
	$result = $conn->query($sqlRetrieve);
	
	$playerExists = false;
	
	// Validate playerID ....If unique player exists
	
	if ($result->num_rows == 1) {
		// output data to variables
		$playerExists = true;
		$row = $result->fetch_assoc();
		
		$dbPlayerName = $row["Name"];
		$dbPlayerCredits = $row["Credits"];
		$dbPlayerLifetimeSpins = $row["LifetimeSpins"];
		$dbSaltValue = $row["SaltValue"];
		
	} else {  // Player does not exist
		echo "PlayerID or credentials invalid";
	}
	
	if($playerExists){
		// Validate credentials
		$credentialsValid = true;
		if(!hashcheck($data["SaltValue"], $dbSaltValue)){
			echo "PlayerID or credentials invalid";
			$credentialsValid = false;
		}
		
		// Validate coins won and coins bet
		if($credentialsValid){
			$coinsValid = true;
			if(!validate_coins($data["CoinsBet"],$data["CoinsWon"], $dbPlayerCredits)){
				echo "Credit error";
				$coinsValid = false;
			}
			
			if($coinsValid){
				// not sure how to calculate these. This is my best guess
				$newCredits = $dbPlayerCredits + $data["CoinsWon"];
				$newLifeTimeSpins = $dbPlayerLifetimeSpins +1;
				$newLifeTimeAverageReturns = $newCredits/$newLifeTimeSpins; 
				
				// Update the DB
				
				$sqlUpdate = "UPDATE player SET Credits=" . $newCredits . ", LifetimeSpins=" . $newLifeTimeSpins . " WHERE PlayerID=" . $data["playerID"];
				
				if ($conn->query($sqlUpdate) === TRUE) {
		   			//successful update
					// return the array for JSON response
					$responseData = array("PlayerID" => $data["playerID"], "Name" => $dbPlayerName, "Credits" => $newCredits, "LifeTimeSpins" => $newLifeTimeSpins, "LifeTimeAverageReturns" => $newLifeTimeAverageReturns);
					$conn->close();
					return $responseData;
					
				} else {
					// safety measure can be implemented here to cache the spin result and update whenever possible
		    		echo "Database update error";
				}
			} //coins Valid
			
		} // credentials "hash" valid
		
	}// if player "playerID" exists
	
	$conn->close();
	return -1;
}

// get the request data from POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$playerID = sanitize_input($_POST["playerID"]);
	$saltValue = sanitize_input($_POST["SaltValue"]);
	$coinsBet = sanitize_input($_POST["CoinsBet"]);
	$coinsWon = sanitize_input($_POST["CoinsWon"]);
}

// create the array with request data to work with 
$requestData = array("playerID" => $playerID, "SaltValue" => $saltValue, "CoinsBet" => $coinsBet, "CoinsWon" => $coinsWon);

// validate player data
$responseArray =  validate_player($requestData);

// return the json response
if($responseArray == -1) echo " <- Error -> ";
else echo json_encode($responseArray);

?>
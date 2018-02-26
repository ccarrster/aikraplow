<?php
function openDBConnection(){
	$link = mysqli_connect("127.0.0.1", "root", "F3ckth1s", "aikraplow");
	return $link;
}

function createGame(){
	$link = openDBConnection();
	mysqli_query($link, "INSERT INTO game (state) VALUES('new');");
	$gameId = mysqli_insert_id($link);
	mysqli_close($link);
	return $gameId;
}

function getGameState($gameId){
	$result = null;
	$link = openDBConnection();
	$query = "SELECT state FROM game WHERE id = ".mysqli_real_escape_string($link, $gameId).";";
	$queryResult = mysqli_query($link, $query);
	while ($row = mysqli_fetch_row($queryResult)){
		$result = $row[0];
	}
	mysqli_close($link);
	return $result;
}

function isClientInGame($gameId, $clientId){
	$result = null;
	$link = openDBConnection();
	$query = "SELECT count(id) as count FROM gameclient WHERE gameid = ".mysqli_real_escape_string($link, $gameId)." and clientid = ".mysqli_real_escape_string($link, $clientId).";";
	$queryResult = mysqli_query($link, $query);
	while ($row = mysqli_fetch_row($queryResult)){
		$count = $row[0];
		if($count == 1){
			$result = true;
		} else {
			$result = false;
		}
	}
	mysqli_close($link);
	return $result;
}

function canJoinGame($gameId, $clientId){
	$result = null;
	$state = getGameState($gameId);
	if($state == "new"){
		$clientInGame = isClientInGame($gameId, $clientId);
		if($clientInGame == true){
			$result = false;
		} else {
			$result = true;
		}
	} else {
		$result = false;
	}
	return $result;
}

function canLeaveGame($gameId, $clientId){
	$result = null;
	$state = getGameState($gameId);
	if($state == "new"){
		$clientInGame = isClientInGame($gameId, $clientId);
		if($clientInGame == true){
			$result = true;
		} else {
			$result = false;
		}
	} else {
		$result = false;
	}
	return $result;
}

function addClientToGame($gameId, $clientId){
	$result = null;
	$link = openDBConnection();
	$query = "INSERT INTO gameclient (gameid, clientid) VALUES(".mysqli_real_escape_string($link, $gameId).", ".mysqli_real_escape_string($link, $clientId).");";
	$result = mysqli_query($link, $query);
	mysqli_close($link);
	return $result;
}

function removeClientFromGame($gameId, $clientId){
	$result = null;
	$link = openDBConnection();
	$query = "DELETE FROM gameclient WHERE gameid = ".mysqli_real_escape_string($link, $gameId)." AND clientid = ".mysqli_real_escape_string($link, $clientId).";";
	$result = mysqli_query($link, $query);
	mysqli_close($link);
	return $result;
}

function joinGame($gameId, $clientId){
	$result = null;
	$canJoin = canJoinGame($gameId, $clientId);
	if($canJoin){
		$result = addClientToGame($gameId, $clientId);
	} else{
		$result = "Can not join";
	}
	return $result;
}

function leaveGame($gameId, $clientId){
	$result = null;
	$canLeave = canLeaveGame($gameId, $clientId);
	if($canLeave){
		$result = removeClientFromGame($gameId, $clientId);
	} else{
		$result = "Can not leave";
	}
	return $result;
}


if(isset($_GET['action'])){
	$action = $_GET['action'];
	if($action == "newgame"){
		$gameId = createGame();
		echo(json_encode($gameId));
	} elseif($action == "joingame"){
		if(isset($_GET["gameid"]) && isset($_GET["clientid"])){
			$result = joinGame($_GET["gameid"], $_GET["clientid"]);
		} else {
			$result = "Bad API Call - Missing required fields gameid, clientid";
		}
		echo(json_encode($result));
	} elseif($action == "leavegame"){
		if(isset($_GET["gameid"]) && isset($_GET["clientid"])){
			$result = leaveGame($_GET["gameid"], $_GET["clientid"]);
		} else {
			$result = "Bad API Call - Missing required fields gameid, clientid";
		}
		echo(json_encode($result));
	}
}
?>
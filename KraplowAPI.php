<?php

class KraplowAPI{

	var $apiStorage;

	function __construct($apiStorage){
		$this->apiStorage = $apiStorage;
	}

	function canJoinGame($gameId, $clientId){
		$result = null;
		$state = $this->apiStorage->getGameState($gameId);
		if($state == "new"){
			$clientInGame = $this->apiStorage->isClientInGame($gameId, $clientId);
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
		$state = $this->apiStorage->getGameState($gameId);
		if($state == "new"){
			$clientInGame = $this->apiStorage->isClientInGame($gameId, $clientId);
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

	function joinGame($gameId, $clientId){
		$result = null;
		$canJoin = $this->canJoinGame($gameId, $clientId);
		if($canJoin){
			$result = $this->apiStorage->addClientToGame($gameId, $clientId);
		} else{
			$result = "Can not join";
		}
		return $result;
	}

	function leaveGame($gameId, $clientId){
		$result = null;
		$canLeave = $this->canLeaveGame($gameId, $clientId);
		if($canLeave){
			$result = $this->apiStorage->removeClientFromGame($gameId, $clientId);
		} else{
			$result = "Can not leave";
		}
		return $result;
	}

	function getGames($state = null){
		return $this->apiStorage->getGames($state);
	}

	function handleAction(){
		if(isset($_GET['action'])){
			$action = $_GET['action'];
			if($action == "newgame"){
				$gameId = $this->apiStorage->createGame();
				echo(json_encode($gameId));
			} elseif($action == "joingame"){
				if(isset($_GET["gameid"]) && isset($_GET["clientid"])){
					$result = $this->joinGame($_GET["gameid"], $_GET["clientid"]);
				} else {
					$result = "Bad API Call - Missing required fields gameid, clientid";
				}
				echo(json_encode($result));
			} elseif($action == "leavegame"){
				if(isset($_GET["gameid"]) && isset($_GET["clientid"])){
					$result = $this->leaveGame($_GET["gameid"], $_GET["clientid"]);
				} else {
					$result = "Bad API Call - Missing required fields gameid, clientid";
				}
				echo(json_encode($result));
			} elseif($action == "getgames"){
				if(isset($_GET["state"])){
					$result = $this->getGames($_GET["state"]);
				} else {
					$result = $this->getGames();
				}
				echo(json_encode($result));
			} else {
				echo(json_encode("Action not supported"));
			}
		}
	}
}

if(isset($_GET['action'])){
	require_once("MySQLApiStorage.php");
	$apiStorage = new MySQLApiStorage();
	$api = new KraplowAPI($apiStorage);
	$api->handleAction();
}
?>
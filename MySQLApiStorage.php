<?php

require_once("iApiStorage.php");

class MySQLApiStorage implements iApiStorage{

	function openDBConnection(){
		$link = mysqli_connect("127.0.0.1", "root", "F3ckth1s", "aikraplow");
		return $link;
	}

	function createGame(){
		$link = $this->openDBConnection();
		mysqli_query($link, "INSERT INTO game (state) VALUES('new');");
		$gameId = mysqli_insert_id($link);
		mysqli_close($link);
		return $gameId;
	}

	function getGameState($gameId){
		$result = null;
		$link = $this->openDBConnection();
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
		$link = $this->openDBConnection();
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

	function addClientToGame($gameId, $clientId){
		$result = null;
		$link = $this->openDBConnection();
		$query = "INSERT INTO gameclient (gameid, clientid) VALUES(".mysqli_real_escape_string($link, $gameId).", ".mysqli_real_escape_string($link, $clientId).");";
		$result = mysqli_query($link, $query);
		mysqli_close($link);
		return $result;
	}

	function removeClientFromGame($gameId, $clientId){
		$result = null;
		$link = $this->openDBConnection();
		$query = "DELETE FROM gameclient WHERE gameid = ".mysqli_real_escape_string($link, $gameId)." AND clientid = ".mysqli_real_escape_string($link, $clientId).";";
		$result = mysqli_query($link, $query);
		mysqli_close($link);
		return $result;
	}

	function getGames($state = null){
		$result = [];
		$link = $this->openDBConnection();
		if($state != null){
			$query = "SELECT game.id, state, clientid FROM game LEFT JOIN gameclient on gameid = game.id WHERE state = '".mysqli_real_escape_string($link, $state)."';";
		} else {
			$query = "SELECT game.id, state, clientid FROM game LEFT JOIN gameclient on gameid = game.id;";
		}
		$queryResult = mysqli_query($link, $query);
		while ($row = mysqli_fetch_row($queryResult)){

			if(!isset($result[$row[0]])){
				$game = [];
				$game["id"] = $row[0];
				$game["state"] = $row[1];
				$game["clients"] = [];
				if(null != $row[2]){
					$game["clients"][] = $row[2];
				}
				$result[$row[0]] = $game;
			} else {
				$result[$row[0]]["clients"][] = $row[2];
			}

		}
		mysqli_close($link);
		$cleanResult = [];
		foreach($result as $resultItem){
			$cleanResult[] = $resultItem;
		}
		return $cleanResult;
	}

	function createClient($name = null){
		$link = $this->openDBConnection();
		if($name == null){
			$query = "INSERT INTO client () values()";
			mysqli_query($link, $query);
			$clientId = mysqli_insert_id($link);
		} else {
			$nameExistsQuery = "SELECT id FROM client WHERE name = '".mysqli_real_escape_string($link, $name)."'";
			$result = mysqli_query($link, $nameExistsQuery);
			$existingName = null;
			while ($row = mysqli_fetch_row($result)){
				$existingName = $row[0];
			}
			if($existingName == null){
				$query = "INSERT INTO client (name) values('".mysqli_real_escape_string($link, $name)."')";
				mysqli_query($link, $query);
				$clientId = mysqli_insert_id($link);
			} else {
				$clientId = false;
			}
		}
		mysqli_close($link);
		return $clientId;
	}

	function setGameState($gameId, $state){
		$link = $this->openDBConnection();
		$query = "UPDATE game SET state = '".mysqli_real_escape_string($link, $state)."' WHERE id = ".mysqli_real_escape_string($link, $gameId);
		mysqli_close($link);
	}

}
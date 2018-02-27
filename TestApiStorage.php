<?php

require_once("iApiStorage.php");

class TestApiStorage implements iApiStorage{

	var $games = [];

	function createGame(){
		$game = [];
		$game["state"] = "new";
		$game["id"] = count($this->games);
		$game["clients"] = [];
		$this->games[] = $game;
		return $game["id"];
	}

	function getGameState($gameId){
		foreach($this->games as $game){
			if($gameId == $game["id"]){
				return $game["state"];
			}
		}
	}

	function isClientInGame($gameId, $clientId){
		foreach($this->games as $game){
			if($gameId == $game["id"]){
				foreach($game["clients"] as $client){
					if($client == $clientId){;
						return true;
					}
				}
			}
		}
		return false;
	}

	function addClientToGame($gameId, $clientId){
		foreach($this->games as &$game){
			if($gameId == $game["id"]){
				foreach($game["clients"] as $client){
					if($client == $clientId){
						return false;
					}
				}
				$game["clients"][] = $clientId;
				return true;
			}
		}
	}

	function removeClientFromGame($gameId, $clientId){
		foreach($this->games as &$game){
			if($gameId == $game["id"]){
				foreach($game["clients"] as $key=>$client){
					if($client == $clientId){
						unset($game["clients"][$key]);
						return true;
					}
				}
				return false;
			}
		}
	}

	function getGames($state = null){
		$result = [];
		foreach($this->games as $game){
			if($state != null){
				if($game["state"] == $state){
					$result[] = $game;
				}
			} else {
				$result[] = $game;
			}
		}
		return $result;
	}
}
<?php
interface iAPIStorage
{

	function createGame();
	function getGameState($gameId);
	function isClientInGame($gameId, $clientId);
	function addClientToGame($gameId, $clientId);
	function removeClientFromGame($gameId, $clientId);
	function getGames($state);
	function createClient($name);
	function setGameState($gameId, $gameState);

}
?>
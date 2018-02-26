<?php
interface iAPIStorage
{

	function createGame();
	function getGameState($gameId);
	function isClientInGame($gameId, $clientId);
	function addClientToGame($gameId, $clientId);
	function removeClientFromGame($gameId, $clientId);

}
?>
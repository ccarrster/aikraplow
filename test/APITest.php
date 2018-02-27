<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once("KraplowAPI.php");
require_once("TestApiStorage.php");

final class APITest extends TestCase
{
	 public function testCreateGame(){
	 	unset($_GET);
	 	$testStorage = new TestApiStorage();
		$sut = new KraplowAPI($testStorage);
		$_GET["action"] = "newgame";
		$this->assertEquals(0, count($testStorage->games));
		ob_start();
		$sut->handleAction();
		$output = ob_get_flush();
		$this->assertEquals(0, $output);
		$this->assertEquals(1, count($testStorage->games));
		$sut->handleAction();
		$this->assertEquals(2, count($testStorage->games));
	 }

	 public function testJoinGame(){
	 	unset($_GET);
		$testStorage = new TestApiStorage();
		$sut = new KraplowAPI($testStorage);
		$_GET["action"] = "newgame";
		$sut->handleAction();
		$_GET["action"] = "joingame";
		$_GET["gameid"] = "0";
		$_GET["clientid"] = "1";
		$this->assertEquals(0, count($testStorage->games[0]["clients"]));
		ob_start();
		$sut->handleAction();
		$output = ob_get_flush();
		$this->assertEquals("true", $output);
		$this->assertEquals(1, count($testStorage->games[0]["clients"]));

		ob_start();
		$sut->handleAction();
		$output = ob_get_flush();
		$this->assertEquals('"Can not join"', $output);

		$_GET["action"] = "joingame";
		$_GET["gameid"] = "0";
		$_GET["clientid"] = "2";
		$sut->handleAction();
		$this->assertEquals(2, count($testStorage->games[0]["clients"]));
	 }

	 public function testLeaveGame(){
	 	unset($_GET);
		$testStorage = new TestApiStorage();
		$sut = new KraplowAPI($testStorage);
		$_GET["action"] = "newgame";
		$sut->handleAction();
		$_GET["action"] = "joingame";
		$_GET["gameid"] = "0";
		$_GET["clientid"] = "1";
		$sut->handleAction();
		$this->assertEquals(1, count($testStorage->games[0]["clients"]));
		$_GET["action"] = "leavegame";
		$_GET["gameid"] = "0";
		$_GET["clientid"] = "1";
		$sut->handleAction();
		$this->assertEquals(0, count($testStorage->games[0]["clients"]));
	 }

	 public function testLeaveGameWrongId(){
	 	unset($_GET);
		$testStorage = new TestApiStorage();
		$sut = new KraplowAPI($testStorage);
		$_GET["action"] = "newgame";
		$sut->handleAction();
		$_GET["action"] = "joingame";
		$_GET["gameid"] = "0";
		$_GET["clientid"] = "1";
		$sut->handleAction();
		$this->assertEquals(1, count($testStorage->games[0]["clients"]));
		$_GET["action"] = "leavegame";
		$_GET["gameid"] = "0";
		$_GET["clientid"] = "2";
		$sut->handleAction();
		$this->assertEquals(1, count($testStorage->games[0]["clients"]));
	 }

	 public function testLeaveGameBadCall(){
	 	unset($_GET);
		$testStorage = new TestApiStorage();
		$sut = new KraplowAPI($testStorage);
		$_GET["action"] = "newgame";
		$sut->handleAction();
		$_GET["action"] = "joingame";
		$_GET["gameid"] = "0";
		$sut->handleAction();
		$this->assertEquals(0, count($testStorage->games[0]["clients"]));
	 }

	  public function testJoinGameBadCall(){
		unset($_GET);
		$testStorage = new TestApiStorage();
		$sut = new KraplowAPI($testStorage);
		$_GET["action"] = "newgame";
		$sut->handleAction();
		$_GET["action"] = "joingame";
		$_GET["clientid"] = "1";
		$this->assertEquals(0, count($testStorage->games[0]["clients"]));
		$sut->handleAction();
		$this->assertEquals(0, count($testStorage->games[0]["clients"]));
	 }

	 public function testGetAllGames(){
	 	unset($_GET);
	 	$testStorage = new TestApiStorage();
		$sut = new KraplowAPI($testStorage);
		$_GET["action"] = "getgames";
		$_GET["state"] = "new";
		ob_start();
		$sut->handleAction();
		$output = ob_get_flush();
		$this->assertEquals(0, count(json_decode($output)));
		$_GET["action"] = "newgame";
		$sut->handleAction();
		$_GET["action"] = "getgames";
		$_GET["state"] = "new";
		ob_start();
		$sut->handleAction();
		$output = ob_get_flush();
		$this->assertEquals(1, count(json_decode($output)));
		$_GET["action"] = "getgames";
		$_GET["state"] = "playing";
		ob_start();
		$sut->handleAction();
		$output = ob_get_flush();
		$this->assertEquals(0, count(json_decode($output)));
		unset($_GET);
		$_GET["action"] = "getgames";
		ob_start();
		$sut->handleAction();
		$output = ob_get_flush();
		$this->assertEquals(1, count(json_decode($output)));
	 }
}
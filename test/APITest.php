<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once("KraplowAPI.php");
require_once("MySQLApiStorage.php");

final class APITest extends TestCase
{
	public function testCreateAnonClient(){
		$result = file_get_contents("http://localhost/aikraplow/KraplowAPI.php?action=newclient");
		$this->assertTrue(is_int(json_decode($result)));
	}

	public function testCreateTwoAnonClient(){
		$result = file_get_contents("http://localhost/aikraplow/KraplowAPI.php?action=newclient");
		$this->assertTrue(is_int(json_decode($result)));
		$result2 = file_get_contents("http://localhost/aikraplow/KraplowAPI.php?action=newclient");
		$this->assertTrue(is_int(json_decode($result2)));
		$this->assertTrue($result != $result2);
	}

	public function testCreateNamedClient(){
		$link = mysqli_connect("127.0.0.1", "root", "F3ckth1s", "aikraplow");
		mysqli_query($link, "DELETE FROM client");
		mysqli_close($link);
		$result = file_get_contents("http://localhost/aikraplow/KraplowAPI.php?action=newclient&name=testerson");
		$this->assertTrue(is_int(json_decode($result)));
		$result = file_get_contents("http://localhost/aikraplow/KraplowAPI.php?action=newclient&name=testerson");
		$this->assertFalse(json_decode($result));
	}


	 public function testStartJoinTwiceGame(){
	 	$link = mysqli_connect("127.0.0.1", "root", "F3ckth1s", "aikraplow");
	 	mysqli_query($link, "DELETE FROM client");
		mysqli_close($link);
	 	$result = file_get_contents("http://localhost/aikraplow/KraplowAPI.php?action=newgame");
	 	$gameId = json_decode($result);
	 	$clientResult = file_get_contents("http://localhost/aikraplow/KraplowAPI.php?action=newclient&name=testerson");
	 	$clientId = json_decode($clientResult);
	 	$joinResult = file_get_contents("http://localhost/aikraplow/KraplowAPI.php?action=joingame&gameid=".$gameId."&clientid=".$clientId);
	 	$join = json_decode($joinResult);
	 	$this->assertTrue($join);
	 	$joinResult2 = file_get_contents("http://localhost/aikraplow/KraplowAPI.php?action=joingame&gameid=".$gameId."&clientid=".$clientId);
	 	$join2 = json_decode($joinResult2);
	 	$this->assertEquals('Can not join', $join2);
	 }

	 public function testStartGame(){
		$link = mysqli_connect("127.0.0.1", "root", "F3ckth1s", "aikraplow");
		mysqli_query($link, "DELETE FROM client");
		mysqli_close($link);
		$result = file_get_contents("http://localhost/aikraplow/KraplowAPI.php?action=newgame");
		$gameId = json_decode($result);
		$clientResult = file_get_contents("http://localhost/aikraplow/KraplowAPI.php?action=newclient&name=testerson");
		$clientId = json_decode($clientResult);
		$joinResult = file_get_contents("http://localhost/aikraplow/KraplowAPI.php?action=joingame&gameid=".$gameId."&clientid=".$clientId);
		$join = json_decode($joinResult);
		$startResult = file_get_contents("http://localhost/aikraplow/KraplowAPI.php?action=startgame&gameid=".$gameId);
		$start = json_decode($startResult);
		$this->assertEquals(true, $start);
	 }


}
<!DOCTYPE html>
<html>
<head>
<title>Kraplow!</title>
<script src="jquery-3.3.1.min.js"></script>
</head>
<body>
	<div id="games"></div>
</body>
<script>
$.get("KraplowAPI.php?action=getgames", function (data){
	var games = JSON.parse(data);
	console.log(games);
	for(var i = 0; i < games.length; i++){
		var game = games[i];
		$("#games").append("<div id='game"+game.id+"'>"+game.state+"("+game.clients.length+") <a href=''>Join</a></div>");
	}
});
</script>
</html>
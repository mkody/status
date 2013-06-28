<html>
	<head>
		<style>
			body {
				background: #fff;
				font-family: Georgia, 'Times New Roman', serif;
				padding: 0;
				margin: 0;
			}
			#container {
				margin: 0 auto;
				width: 1000px;
				background: #fff;
				padding: 20px 50px 20px 50px;
			}
			header {
				margin-bottom: 40px;
				width: 500px;
				float: left;
			}
			header h1{
				font-weight: 100;
				font-size: 47px;
				margin: 0;
			}
			header h3 {
				font-size: 25px;
				font-weight: 100;
				margin: 0;
				margin-left: 30px;
				font-style: italic;
			}
			.block {
				margin-bottom: 40px;
				clear: both;
			}
			.block h3 {
				font-size: 35px;
				font-weight: 100;
				margin: 0;
			}
			.block p {
				font-size: 25px;
				margin: 0;
				margin-left: 35px;
			}
			.barContainer {
				width: 1000px;
				height: 40px;
				background-color: #eee;
				margin-left: 35px;
				overflow: hidden;
			}
			.bar {
				display: block;
				height: 40px;
				width: 0;
				background-color: #333;
				overflow: hidden;
			}
			a {
				color: #222;
				text-decoration: underline;
			}
			a:hover {
				text-decoration: none;
				background: #000;
				color: #fff;
			}
			#updateBlock{
				float: right;
			}
		</style>
		<script src="http://code.jquery.com/jquery-2.0.2.min.js"></script>
		<script>
		// I am not the best at AJAX or Javascript in general. Feel free to recommend changes.
		var GREEN = "#3DB015";
		var YELLOW = "#FAFC4F";
		var RED = "#C9362E";
		function loadColors(load)
		{
			if(load < 0.75)
			{
				return "<?=GREEN;?>";
			} else if(load < 1)
			{
				return "<?=YELLOW;?>";
			} else if(load > 1)
			{
				return "<?=RED;?>";
			}
		}
		function updateAll()
		{
			console.log("Updating all");
			$.get("result.php", function(raw) {
				stats = eval('(' + raw + ')');
				$("#uptime").html(stats.uptime);
				
				$("#temp").html(stats.temp);

				if(stats.load[0] < 0.75)
				{
					$("#loadStatus").html("Good");
					$("#loadStatus").css("color",GREEN);
				} else if(stats.load[0] < 0.75)
				{
					$("#loadStatus").html("Warning!");
					$("#loadStatus").css("color","#000");
				} else if(stats.load[0] > 1)
				{
					$("#loadStatus").html("Overloaded!");
					$("#loadStatus").css("color",RED);
				}
				$("#loadOne").html("Last 60 seconds: " + stats.load[0]);
				$("#loadTwo").html("Last 5 minutes: " + stats.load[1]);
				$("#loadThree").html("Last 15 minutes: " + stats.load[2]);
				$("#loadBarOne").animate({
					width: (stats.load[0] * 1000) + "px"
				},1000,function(){});
				$("#loadBarTwo").animate({
					width: (stats.load[1] * 1000) + "px"
				},1000,function(){});
				$("#loadBarThree").animate({
					width: (stats.load[2] * 1000) + "px"
				},1000,function(){});
				$("#loadBarOne").css("background-color",loadColors(stats.load[0]));	
				$("#loadBarTwo").css("background-color",loadColors(stats.load[1]));	
				$("#loadBarThree").css("background-color",loadColors(stats.load[2]));	

				$("#procSpeed").html(stats.proc);
				$("#cpuBar").animate({
					width: ((stats.proc / 2800) * 1000) + "px"
				},1000,function(){});

				$("#diskInfo").html(stats.disk[0] + "%, " + stats.disk[1] + " used / " + stats.disk[2] + "total");
				$("#diskBar").animate({
					width: (stats.disk[0] * 10) + "px"
				},1000,function(){});

				$("#memInfo").html(stats.memory[0] + "%, " + stats.memory[3] + " used / " + stats.memory[4] + "total");
				$("#ramBar").animate({
					width: (stats.memory[0] * 10) + "px"
				},1000,function(){});

				$("#httpStatus").html(stats.service.apache);
				$("#mysqlStatus").html(stats.service.mysql);
				$("#minecraftStatus").html(stats.service.craftbukkit);				
			});
		}

		$(function(){
			$("#update").click(function(event)
			{
				event.preventDefault();
				updateAll();
			});
			updateAll();
			setInterval("updateAll()",5000);
		});

		</script>
	</head>
<body>
		<div id="container">
			<header>
				<h1>kywu status panel</h1>
				<h3>what's up?</h3>
			</header>
			<div id="updateBlock">
				<a id="update" href="#">Update manually</a> (updates every 5 seconds)
			</div>
			<div class="block">
				<h3>uptime</h3>
				<p id="uptime"></p>
			</div>

			<div class="block">
				<h3>core temperature</h3>
				<p id="temp"></p>
			</div>
			<div class="block">
				<h3>load averages</h3>
				<p>Current status:
				<span id="loadStatus"></span>
				</p>
				
				<p id="loadOne">Last 60 seconds: </p>
				<div class="barContainer">
					<div class="bar" id="loadBarOne" style="background-color: "></div>
				</div>

				<p id="loadTwo">Last 5 minutes: </p>
				<div class="barContainer">
					<div class="bar" id="loadBarTwo" style="background-color: "></div>
				</div>
				<p id="loadThree">Last 15 minutes: </p>
				<div class="barContainer">
					<div class="bar" id="loadBarThree" style="background-color: "></div>
				</div>
			</div>
			<div class="block">
				<h3>processor speed</h3>
				<p><span id="procSpeed"></span> MHz / 2800 MHz</p>
				<div class="barContainer">
					<div class="bar" id="cpuBar"></div>
				</div>
			</div>
			<div class="block">
				<h3>disk usage</h3>
				<p id="diskInfo">%,  used /  total</p>
				<div class="barContainer">
					<div class="bar" id="diskBar"></div>
				</div>
			</div>
			<div class="block">
				<h3>memory</h3>
				<p id="memInfo">%,  used /  total</p>
				<div class="barContainer">
					<div class="bar" id="ramBar"></div>
				</div>
			</div>
			<div class="block">
				<h3>services<h3>
				<p >HTTP server: <span id="httpStatus"></span></p>
				<p>MySQL: <span id="mysqlStatus"></span></p>
				<p><a href="/paul">Minecraft server</a>: <span id="minecraftStatus"></span></p>
			</div>
			<div id="credits">
				<p>Powered by: <a href="http://debian.org">Debian</a> | <a href="http://httpd.apache.org">Apache HTTP server</a> | <a href="http://cloudflare.com">CloudFlare</a> | <a href="http://bukkit.org">Bukkit</a> | <a href="https://github.com/h02/Minecraft-PHP-Query-Class">Minecraft PHP Query</a> | <a href="https://github.com/haegenschlatt/status">Source code</a></p>
			</div>
		</div>
	</body>
</html>
<?php
define('GREEN',"#3DB015");
define('YELLOW',"#FAFC4F");
define('RED',"#C9362E");
?>
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
			var results;
			$.get("result.php?uptime&temp&load&proc&disk&memory&service", function(data) {
				results = data.split("\n");

				$("#uptime").html(results[0]);

				$("#temp").html(results[1]);

				var loads = (results[2]).split("|");

				if(loads[0] < 0.75)
				{
					$("#loadStatus").html("Optimal");
					$("#loadStatus").css("color","<?=GREEN;?>");
				} else if(loads[0] < 1)
				{
					$("#loadStatus").html("Warning!");
					$("loadStatus".css("color","#000"));
				} else if(loads[0] > 1)
				{
					$("#loadStatus").html("Overloaded!");
					$("loadStatus".css("color","<?=RED;?>"));
				}

				$("#loadOne").html("Last 60 seconds: " + loads[0]);
				$("#loadTwo").html("Last 5 minutes: " + loads[1]);
				$("#loadThree").html("Last 15 minutes: " + loads[2]);
				$("#loadBarOne").animate({
					width: (loads[0] * 1000) + "px"
				},1000,function(){});
				$("#loadBarTwo").animate({
					width: (loads[1] * 1000) + "px"
				},1000,function(){});
				$("#loadBarThree").animate({
					width: (loads[2] * 1000) + "px"
				},1000,function(){});
				$("#loadBarOne").css("background-color",loadColors(loads[0]));	
				$("#loadBarTwo").css("background-color",loadColors(loads[1]));	
				$("#loadBarThree").css("background-color",loadColors(loads[2]));	

				$("#procSpeed").html(results[3]);
				$("#cpuBar").animate({
					width: ((results[3] / 2800) * 1000) + "px"
				},1000,function(){});

				var disk = results[4].split("|");
				$("#diskInfo").html(disk[0] + "%, " + disk[1] + " used / " + disk[2] + "total");
				$("#diskBar").animate({
					width: (disk[0] * 10) + "px"
				},1000,function(){});

				var memory = results[5].split("|");
				$("#memInfo").html(memory[0] + "%, " + memory[3] + " used / " + memory[4] + "total");
				$("#ramBar").animate({
					width: (memory[0] * 10) + "px"
				},1000,function(){});

				var services = results[6].split("|");
				$("#httpStatus").html(services[0]);
				$("#mysqlStatus").html(services[1]);
				$("#minecraftStatus").html(services[2]);
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
				<?php
				/*
				if($load_out[0] < 0.75)
				{
				?>
					<span style="color: <?=GREEN;?>;">Optimal</span>
				<?php
				}
				else if($load_out[0] < 1)
				{
				?>
					<span style="color: #000;">Warning!</span>
				<?php
				}
				else if($load_out[0] > 1)
				{
				?>
					<span style="color: <?=RED;?>;">Overloaded!</span>
				<?php
				}
				*/
				?>
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
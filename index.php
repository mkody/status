<?php
/*  PHP System Status 
 *  ------------------------------------------ 
 *  Author: Kevin Wu (haegenschlatt/dejawu)
 *  Last update: 12-24-2012 first release
 * 
 * 
 *  GNU License Agreement 
 *  --------------------- 
 *  This program is free software; you can redistribute it and/or modify 
 *  it under the terms of the GNU General Public License version 2 as 
 *  published by the Free Software Foundation. 
 * 
 *  This program is distributed in the hope that it will be useful, 
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of 
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 *  GNU General Public License for more details. 
 * 
 *  You should have received a copy of the GNU General Public License 
 *  along with this program; if not, write to the Free Software 
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA 
 * 
 *  http://www.gnu.org/licenses/gpl-2.0.txt 
 */

/*

A large portion of source code comes from http://installgentoo.net/, released under the GPL.
Much thanks to them/him for releasing the source!

*/

if(isset($_GET['source']))
{ 
	$lines = implode(range(1, count(file(__FILE__))), '<br />'); 
	$content = highlight_file(__FILE__, TRUE); 
	die('<html><head><title>Page Source For: '.__FILE__.'</title><style type="text/css">body {margin: 0px;margin-left: 5px;}.num {border-right: 1px solid;color: gray;float: left;font-family: monospace;font-size: 13px;margin-right: 6pt;padding-right: 6pt;text-align: right;}code {white-space: nowrap;}td {vertical-align: top;}</style></head><body><table><tr><td class="num"  style="border-left:thin; border-color:#000;">'.$lines.'</td><td class="content">'.$content.'</td></tr></table></body></html>'); 
}

function kb2bytes($kb){ 
	return round($kb * 1024, 2); 
}

function format_bytes($bytes){ 
	if ($bytes < 1024){ return $bytes; } 
	else if ($bytes < 1048576){ return round($bytes / 1024, 2).'KB'; } 
	else if ($bytes < 1073741824){ return round($bytes / 1048576, 2).'MB'; } 
	else if ($bytes < 1099511627776){ return round($bytes / 1073741824, 2).'GB'; } 
	else{ return round($bytes / 1099511627776, 2).'TB'; } 
}

function numbers_only($string){ 
	return preg_replace('/[^0-9]/', '', $string); 
} 

function calculate_percentage($used, $total){ 
	return @round(100 - $used / $total * 100, 2); 
}

function availableUrl($host, $port=80, $timeout=5) { 
  $fp = fSockOpen($host, $port, $errno, $errstr, $timeout); 
  return $fp!=false;
}

function checkProcess($teststring)
{
	$count = (int)exec('ps auxh | grep -c ' . $teststring)-2;
	if(($count>0))
	{
?>
	<span style="color: <?=GREEN;?>;">online</span>
<?php
	} else
	{
?>
	<span style="color: <?=RED;?>;">offline</span>
<?php
	}
}

$uptime = exec('uptime'); 
preg_match('/ (.+) up (.+) user(.+): (.+)/', $uptime, $update_out); 
$users_out = substr($update_out[2], strrpos($update_out[2], ' ')+1); 
$uptime_out = substr($update_out[2], 0, strrpos($update_out[2], ' ')-2); 

// Array containing the three load averages
$load_out = explode(", ",$update_out[4]);

// Hard drive percentage
$hd = explode(" ",exec("df /"));
$hd_out = 100-calculate_percentage($hd[2],$hd[1]);

$memory = array( 'Total RAM'  => 'MemTotal', 
				 'Free RAM'   => 'MemFree', 
				 'Cached RAM' => 'Cached', 
				 'Total Swap' => 'SwapTotal', 
				 'Free Swap'  => 'SwapFree' ); 
foreach ($memory as $key => $value){ 
	$memory[$key] = kb2bytes(numbers_only(exec('grep -E "^'.$value.'" /proc/meminfo'))); 
} 
$memory['Used Swap'] = $memory['Total Swap'] - $memory['Free Swap']; 
$memory['Used RAM'] = $memory['Total RAM'] - $memory['Free RAM'] - $memory['Cached RAM']; 
$memory['RAM Percent Free'] = calculate_percentage($memory['Used RAM'],$memory['Total RAM']); 
$memory['Swap Percent Free'] = calculate_percentage($memory['Used Swap'],$memory['Total Swap']); 

$temp = exec('acpi -t');
$temp = str_replace("Thermal 0: ok, ","",$temp);

define(GREEN,"#3DB015");
define(YELLOW,"#FAFC4F");
define(RED,"#C9362E");

function loadColors($load)
{
	if($load < 0.75)
	{
		return GREEN;
	} else if($load < 1)
	{
		return YELLOW;
	} else
	{
		return RED;
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<!-- Debug: <?=rand();?> -->
		<title>kywu status panel</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script>
		var loadOne = <?=$load_out[0];?> * 1000;
		var loadTwo = <?=$load_out[1];?> * 1000;
		var loadThree = <?=$load_out[2];?> * 1000;
		var diskBar = <?=$hd_out;?> * 10;
		var ramBar = (100-<?=$memory['RAM Percent Free'];?>) * 10;
		$(document).ready(function() {
			$('#loadBarOne').animate({
				width: loadOne + "px"
			}, 1000, function(){});
			$('#loadBarTwo').animate({
				width: loadTwo + "px"
			}, 1000, function(){});
			$('#loadBarThree').animate({
				width: loadThree + "px"
			}, 1000, function(){});
			$('#diskBar').animate({
				width: diskBar + "px"
			}, 1000, function(){});
			$('#ramBar').animate({
				width: ramBar + "px"
			}, 1000, function(){});
		});
		</script>
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
		</style>
	</head>
	<body>
		<div id="container">
			<header>
				<h1>kywu status panel</h1>
				<h3>what's up?</h3>
			</header>

			<div class="block">
				<h3>uptime</h3>
				<p><?=$uptime_out;?></p>
			</div>
			<div class="block">
				<h3>load averages</h3>
				<p>Current status:
				<?php
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
				?>
				</p>
				<p>Last 60 seconds: <?=$load_out[0]*100; ?>%</p>
				<div class="barContainer">
					<div class="bar" id="loadBarOne" style="background-color: <?php echo loadColors($load_out[0]); ?>"></div>
				</div>
				<p>Last 5 minutes: <?=$load_out[1]*100; ?>%</p>
				<div class="barContainer">
					<div class="bar" id="loadBarTwo" style="background-color: <?php echo loadColors($load_out[1]); ?>"></div>
				</div>
				<p>Last 15 minutes: <?=$load_out[2]*100; ?>%</p>
				<div class="barContainer">
					<div class="bar" id="loadBarThree" style="background-color: <?php echo loadColors($load_out[2]); ?>"></div>
				</div>
			</div>
			<div class="block">
				<h3>disk usage</h3>
				<p><?=$hd_out;?>%, <?=format_bytes(kb2bytes($hd[2])); ?> used / <?=format_bytes(kb2bytes($hd[1])); ?> total</p>
				<div class="barContainer">
					<div class="bar" id="diskBar"></div>
				</div>
			</div>
			<div class="block">
				<h3>memory</h3>
				<p><?=100-$memory['RAM Percent Free'];?>%, <?=format_bytes($memory['Used RAM']);?> used / <?=format_bytes($memory['Total RAM']);?> total</p>
				<div class="barContainer">
					<div class="bar" id="ramBar"></div>
				</div>
			</div>
			<div class="block">
				<h3>core temperature</h3>
				<p><?=$temp;?></p>
			</div>
			<div class="block">
				<h3>services<h3>
				<p>HTTP server: <?=checkProcess("apache");?></p>
				<p>MySQL: <?=checkProcess("mysql");?></p>
				<p>Minecraft server: <?=checkProcess("craftbukkit");?> <a href="/paul">More info &raquo;</a></p>
			</div>
			<div id="credits">
				<p>Powered by: <a href="http://debian.org">Debian</a> | <a href="http://httpd.apache.org">Apache HTTP server</a> | <a href="http://cloudflare.com">CloudFlare</a> | <a href="http://bukkit.org">Bukkit</a> | <a href="https://github.com/h02/Minecraft-PHP-Query-Class">Minecraft PHP Query</a> | <a href="<?=$_SERVER["PHP_SELF"];?>?source">Source code</a></p>
			</div>
		</div>
	</body>
</html>
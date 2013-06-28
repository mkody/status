<?php
// Most of this code is taken directly from the original standalone status page, so it may not be the most efficient it can be.
// This should be gradually fixed with future versions.

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
	// Yes, the HTML needs to be squeezed between the PHP tags in that ugly manner to prevent unwanted newlines.
	$count = (int)exec('ps auxh | grep -c ' . $teststring)-2;
	if(($count>0))
	{
		ob_start();
?><span style="color: <?=GREEN;?>;">online</span><?php
		return ob_get_clean();
	} else
	{
		ob_start();
?><span style="color: <?=RED;?>;">offline</span><?php
		return ob_get_clean();
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

$cpu = exec('cat /proc/cpuinfo | grep "cpu MHz"');
$cpu_ar = explode(" ", $cpu);
$cpu_out = (int)$cpu_ar[2];

define('GREEN',"#3DB015");
define('YELLOW',"#FAFC4F");
define('RED',"#C9362E");



// Since we can't specify checking of any arbitrary process from the AJAX request (injection risk), we pre-define the processes here.
$processes = array(
	"apache"=>"",
	"mysql"=>"",
	"craftbukkit"=>""
);

foreach($processes as $key => $value)
{
	$processes[$key] = checkProcess($key);
}

$result = array(
	"uptime" => $uptime_out,
	"temp" => $temp,
	"load" => array(
		$load_out[0],
		$load_out[1],
		$load_out[2]
		),
	"proc" => $cpu_out,
	"disk" => array(
		$hd_out,
		format_bytes(kb2bytes($hd[2])),
		format_bytes(kb2bytes($hd[1]))
		),
	"memory" => array(
		$memory["RAM Percent Free"],
		$memory["Used RAM"],
		$memory["Total RAM"],
		format_bytes($memory["Used RAM"]),
		format_bytes($memory["Total RAM"])
		),
	"service" => array(
		"apache" => $processes["apache"],
		"mysql" => $processes["mysql"],
		"craftbukkit" => $processes["craftbukkit"]
		)
);
echo json_encode($result);
?>
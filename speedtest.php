<?php
    exec("python speedtest/speedtest_cli.py --simple", $speedtest);
    $speedtest = implode("<br/>",$speedtest);
    echo $speedtest;

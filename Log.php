<?php
/* TBcom-Lib Log
 *
 *      \TBcom
 * 
 * Copyright (c) 2017-2020 Tanner Babcock.
 * This software is licensed under the terms of the MIT License. See LICENSE for details.
*/
namespace TBcom;

function isBot($ua) {
	static $botsList = [
		"YandexBot/",
		"Googlebot/",
		"Cliqzbot/",
		"bingbot/",
		"SeznamBot/",
		"ips-agent",
		"Exabot/",
		"MegaIndex.ru/",
		"Virusdie crawler/",
		"Baiduspider/",
		"AhrefsBot/",
		"GuzzleHttp/",
		"CCBot/",
		"DotBot/",
		"Google-Structured-Data-Testing-Tool",
		"Google Search Console",
		"YandexPagechecker/",
		"OpenLinkProfiler.org",
		"Applebot/",
		"YandexMobileBot/",
		"DuckDuckGo-Favicons-Bot/",
		"nsrbot/",
		"Daum/",
		"Dataprovider.com",
		"Uptimebot/",
		"SemrushBot/",
		"Nimbostratus-Bot/",
		"test",
		"TurnitinBot"
	];
	if (strlen($ua) < 18) {
		return true;
	}
	foreach ($botsList as $v) {
		if (strpos($ua, $v) !== false)
			return true;
	}
	return false;
}

function appendVisitorLog() {
	$ipaddress = $_SERVER["REMOTE_ADDR"];
	$page = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"];
	$page .= ((!empty($_SERVER["QUERY_STRING"])) ? ("?" . $_SERVER["QUERY_STRING"]) : (""));
	$referrer = ((isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : "");
	$datetime = @mktime();
	$useragent = $_SERVER["HTTP_USER_AGENT"];
	$remotehost = @getHostByAddr($ipaddress);
	$logline = $ipaddress . "|{$referrer}|{$datetime}|{$useragent}|{$remotehost}|{$page}\n";
	$logfile = $_SERVER["DOCUMENT_ROOT"] . "/admin/log/visitor.log";

	$myip = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/admin/log/myip.txt");

	if ((strcmp($ipaddress, $myip) != 0) && (strcmp($ipaddress, ((isset($_SESSION["current_ip"])) ? $_SESSION["current_ip"] : $myip)) != 0) && (!isBot($useragent)) && (strpos($_SERVER["HTTP_HOST"], "tannerbabcock.com") !== false)) {
		if (!$handle = fopen($logfile, "a+"))
			die("Failed to open log file");
		if (fwrite($handle, $logline) === FALSE)
			die("Failed to write to log file");
		fclose($handle);
	}
}

appendVisitorLog();

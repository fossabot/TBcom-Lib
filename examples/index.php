<?php
namespace TBcom;
require_once("vendor/Babkock/TBcom-Lib/TBcom.php");
require_once("vendor/Babkock/TBcom-Lib/Log.php");

$mypage = Build\Page("header", "index", "footer");

$mypage->init("Hello World", "This is an example page for TBcom-Lib.", 2);

$mypage->middle->replace("PLACEHOLDER_TEXT", "My Variable");

$mypage->footer->replace("MEMORY", memory_get_usage());

$mypage->output();


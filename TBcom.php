<?php
/* TBcom-Lib
 *
 *     \TBcom
 *
 * Copyright (c) 2017-2019 Tanner Babcock.
 * This software is licensed under the terms of the MIT License. See LICENSE for details.
*/
namespace TBcom;

session_start();
require_once(__DIR__ . "/Build.php");
require_once(__DIR__ . "/Codes.php");
require_once(__DIR__ . "/Config.php");

$mediaParsed = parse_ini_file(__DIR__ . "/Media.ini", true);

$ArtBody = $mediaParsed["ArtBody"];
$ArtDescription = $mediaParsed["ArtDescription"];
$MusicFiles = $mediaParsed["MusicFiles"];
$MusicDescription = $mediaParsed["MusicDescription"];
$FilmFiles = $mediaParsed["FilmFiles"];
$FilmDescription = $mediaParsed["FilmDescription"];
$ArtFiles = $mediaParsed["ArtFiles"];
$GenreFiles = $mediaParsed["GenreFiles"];

// This specifies whether or not the web software should request these pages with the trailing ".php".
// The live website (or Apache) can rewrite /home to /home.php, but virtual machines (NGINX) cannot.
// If running through NGINX or virtual, change this empty string to ".php"
const ext = "";
// {{EXT}} will either be replaced with ext or removed from template files

class AccessRestrictedException extends \Exception {};
class NoHeaderException extends \Exception {};
class NoContentException extends \Exception {};
class EmptyFormException extends \Exception {};
class InvalidEmailException extends \Exception {};
class CaptchaFailException extends \Exception {};
class ExistingFileException extends \Exception {};
class NoFileException extends \Exception {};
class LoginFailException extends \Exception {};
class TokenFailException extends \Exception {};
class NotFoundException extends \Exception {};
class PasswordsException extends \Exception {};
class MySQLException extends \Exception {
	public $err;

	public function __construct($e = "") {
		if (!$e) {
			$this->err = "";
		}
		else {
			$this->err = $e;
		}
	}
};
class MySQLFailException extends \Exception {
	public $err;

	public function __construct($e = "") {
		if (!$e) {
			$this->err = "";
		}
		else {
			$this->err = $e;
		}
	}
};

function Tag($plchold, $optsArray) {
	$x = 0;
	foreach ($optsArray as $v) {
		$plchold = str_replace("{{{$x}}}", $v, $plchold);
		$x++;
	}
	return $plchold;
}

function Secure($pass, $pin, $rev = false) {
	if ($rev)
		return strrev(hash("sha256", $pass) . hash("sha256", ($pin . $pin)));
	else
		return hash("sha256", $pass) . hash("sha256", ($pin . $pin));
}

function CSRFSecure($agent) {
	return hash("sha256", ($agent . "aJKFmagic"));
}

function Snip($beginning, $end, $string) {
	$beginningPos = strpos($string, $beginning);
	$endPos = strpos($string, $end);
	if ($beginningPos === false || $endPos === false) {
		return $string;
	}
	$textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);
	return str_replace($textToDelete, "", $string);
}

function filesFind($path, $reg) {
	$dir = $path;
	$c = glob($dir . "*." . $reg, GLOB_BRACE);
	return count($c);
}

function Cache($file) {
	if (!opcache_is_script_cached($file)) {
		opcache_compile_file($file);
	}
}

Cache(__FILE__);

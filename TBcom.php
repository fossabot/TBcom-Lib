<?php
/* TBcom-Lib
 *
 *     \TBcom
 *
 * Copyright (c) 2017-2020 Tanner Babcock.
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
$MusicFlags = $mediaParsed["MusicFlags"];
$MusicDescription = $mediaParsed["MusicDescription"];
$FilmFiles = $mediaParsed["FilmFiles"];
$FilmFlags = $mediaParsed["FilmFlags"];
$FilmDescription = $mediaParsed["FilmDescription"];
$FictionFiles = $mediaParsed["FictionFiles"];
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

///     \TBcom\Methods
///
class Methods {
	
	/* Tag($plchold, $optsArray)
	  
	   Old, deprecated legacy function notorious for hideous code - Really you shouldn't use this.
	   If you're curious on how this worked, and why it was terrible:
	  
	       <?php
	       $string = M::Tag("Full name: {{0}}, Age: {{1}}, Location: {{2}}", [ $name, $age, $location ]);
	*/
	public static function Tag($plchold, $optsArray) {
		$x = 0;
		foreach ($optsArray as $v) {
			$plchold = str_replace("{{{$x}}}", $v, $plchold);
			$x++;
		}
		return $plchold;
	}

	/* Secure($pass, $pin, $rev = false)
	
	   Generates a 64-character hex token from a universal password string and a universal PIN.
	   The third arguments tells whether to reverse the output of the hash or not.
	  
	       <?php
	       $token = M::Secure("secret", "123456", true);
	       echo $token;  // echoes "68619af728c36de6811f7ffe628..";
	*/
	public static function Secure($pass, $pin, $rev = false) {
		if ($rev)
			return strrev(hash("sha256", $pass) . hash("sha256", ($pin . $pin)));
		else
			return hash("sha256", $pass) . hash("sha256", ($pin . $pin));
	}

	/* CSRFSecure($agent)
	
	   Generates a 64-character hex token for validating forms against cross-site attacks.
	   This will return the exact same hash for the same user agent.
	
	       <?php
	       $csrf = M::CSRFSecure($_SERVER["HTTP_USER_AGENT"]);
	*/
	public static function CSRFSecure($agent) {
		return hash("sha256", ($agent . "aJKFmagic"));
	}

	/* Snip($beginning, $end, $string)
	
	   Deletes all text between $beginning and $end (inclusive), and returns the result.
	   Useful for removing conditional syntax blocks from view files.
	
	       <?php
	       $string = "That\'s when I said holy fucking shit buddy";
	       $string = M::Snip("holy", "shit", $string);
	 
	       // Returns "That's when I said  buddy"
	*/
	public static function Snip($beginning, $end, $string) {
		$beginningPos = strpos($string, $beginning);
		$endPos = strpos($string, $end);
		if ($beginningPos === false || $endPos === false) {
			return $string;
		}
		$textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);
		return str_replace($textToDelete, "", $string);
	}

	/* filesFind($path, $reg)
	
	   Return the number of files found matching RegEx $reg in $path.
	*/
	public static function filesFind($path, $reg) {
		$dir = $path;
		$c = glob($dir . "*." . $reg, GLOB_BRACE);
		return count($c);
	}

	/* Cache($file)
	
	   Compile $file in Zend OpCache. Best used with __FILE__.
	
	       <?php
	       M::Cache(__FILE__);
	 */
	public static function Cache($file) {
		if (!opcache_is_script_cached($file)) {
			opcache_compile_file($file);
		}
	}
};

Methods::Cache(__FILE__);

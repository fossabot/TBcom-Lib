<?php
/* TBcom-Lib Build
 *
 *     \TBcom\Build
 *
 * Copyright (c) 2019 Tanner Babcock.
 * This software is licensed under the terms of the MIT License. See LICENSE for details.
*/
namespace TBcom\Build;

class ContentSection {
	private $content;

	public function __construct($filename) {
		$parts = explode(".", $filename);
		if (@!$parts[1]) {
			if (!file_exists(__DIR__ . "/../views/" . $parts[0] . ".html")) {
				$this->content = "";
			}
			else {
				$this->content = file_get_contents(__DIR__ . "/../views/" . $parts[0] . ".html");
			}
		}
		else {
			if (!file_exists(__DIR__ . "/../../" . $parts[0] . "/resources/views/" . $parts[1] . ".html")) {
				$this->content = "";
			}
			else {
				$this->content = file_get_contents(__DIR__ . "/../../" . $parts[0] . "/resources/views/" . $parts[1] . ".html");
			}
		}
	}

	public function __destruct() {
		$this->content = "";
	}

	public function set($data) {
		$this->content = $data;
	}

	public function get() {
		return $this->content;
	}

	public function append($data) {
		$this->content .= $data;
	}

	public function prepend($data) {
		$this->content = $data . $this->content;
	}

	public function replace($plchold, $val = "") {
		$this->content = str_replace($plchold, $val, $this->content);
	}

	public function replacea($repArr) {
		foreach ($repArr as $plchold => $val) {
			$this->replace($plchold, $val);
		}
	}

	public function load($filename) {
		try {
			if (!file_exists($filename . ".html")) {
				throw new \TBcom\NoHeaderException();
			}
			$this->content = file_get_contents($filename . ".html");
		} catch (\TBcom\NoHeaderException $ex) {
			header('Location: /error' . \TBcom\ext . '?e=404');
		}
	}

	public function output() {
		echo $this->content;
	}
};

class Header extends ContentSection {
	private $title;

	public function __construct($filename, $t = "{{TITLE}}") {
		parent::__construct($filename);
		$this->title = $t;
	}

	public function __destruct() {
		parent::__destruct();
		$this->title = "";
	}

	public function setTitle($t) {
		$this->title = $t;
		$this->replace("{{TITLE}}", $this->title);
	}

	public function getTitle() {
		return $this->title;
	}

	public function keywords($k = "") {
		$this->replace("{{KEYWORDS}}", $k);
	}

	public function description($d = "") {
		$this->replace("{{META_DESCRIPTION}}", $d);
	}

	public function ogImage($url) {
		$w = @\getimagesize($url)[0];
		$h = @\getimagesize($url)[1];
		$this->replacea([
			"{{OG_IMAGE}}" => $url,
			"{{OG_WIDTH}}" => "" . $w,
			"{{OG_HEIGHT}}" => "" . $h
		]);
	}
	
	public function breadcrumbs($bcArray) {
		$bcOut = "";
		$pos = 1;
		foreach ($bcArray as $k => $v) {
			$bcOut .= \TBcom\Tag(file_get_contents(__DIR__ . "/../views/breadcrumb.html"), [ $v, $k, $pos ]);
			$pos++;
		}
		$this->replace("{{BREADCRUMBS}}", $bcOut);
	}
};

class Footer extends ContentSection {
	public function __construct($filename) {
		parent::__construct($filename);
	}

	public function __destruct() {
		parent::__destruct();
	}
};

class Middle extends ContentSection {
	public function __construct($filename) {
		parent::__construct($filename);
	}

	public function __destruct() {
		parent::__destruct();
	}
	
	public function script($s) {
		try {
			$sc = "";
			if (($sc = file_get_contents($s)) === false)
				throw new \TBcom\NoHeaderException();
			parent::set(parent::get() . "<script type=\"text/javascript\">\n" . $sc . "\n</script>\n");
		}
		catch (\TBcom\NoHeaderException $ex) {
			header('Location: /error' . \TBcom\ext . '?e=500');
		}
	}
};

class Page {
	public $header;
	public $middle;
	public $footer;
	private $start;
	private $end;
	private $pagecode;
	private $token;
	
	public function __construct($h, $m = "", $f = "footer", $t = "{{TITLE}}") {
		try {
			$this->header = new Header($h, $t);
			$this->middle = new Middle($m);
			$this->footer = new Footer($f);
			$this->start = microtime(true);
		}
		catch (\TBcom\NoHeaderException $ex) {
			header('Location: /error' . \TBcom\ext . '?e=500');
		}
		catch (\TBcom\NoContentException $ex) {
			header('Location: /error' . \TBcom\ext . '?e=404');
		}
		$this->token = $_GET['tok'] ?? null;
	}

	public function setPageCode($p) {
		try {
			if (($p > 40) && (!$this->token))
				throw new \TBcom\AccessRestrictedException();
			else
				$this->pagecode = $p;
		}
		catch (\TBcom\AccessRestrictedException $ex) {
			header('Location: /error' . \TBcom\ext . '?e=403');
		}
	}
	public function getPageCode() {
		return $this->pagecode;
	}

	public function init($t, $des, $c = null) {
		$this->header->setTitle($t);
		$this->header->description($des);
		$this->setPageCode($c);
	}
	
	public function getToken() { return $this->token; }
	public function setHeader($h) { $this->header->set($h); }
	public function getHeader() { return $this->header->get(); }
	public function setMiddle($m) { $this->middle->set($m); }
	public function getMiddle() { return $this->middle->get(); }
	public function setFooter($f) { $this->footer->set($f); }
	public function getFooter() { return $this->footer->get(); }

	public function appendAdminLog($data) {
		try {
			if (!($handle = fopen("log/admin.log", "a+"))) {
				fclose($handle);
				throw new \TBcom\NoHeaderException();
			}
			if (fwrite($handle, "" . mktime() . "|{$_SERVER['REMOTE_ADDR']}|" . $data) === FALSE) {
				fclose($handle);
				throw new \TBcom\NoHeaderException();
			}
			fclose($handle);
		}
		catch (\TBcom\NoHeaderException $ex) {
			header('Location: /error' . \TBcom\ext . '?e=500');
		}
	}

	public function recent($cont) {
		try {
			if (($this->pagecode >= \TBcom\Codes\ArtMain) && ($this->pagecode <= \TBcom\Codes\ArtGallery))
				$f = "log/art.txt";
			else if (($this->pagecode >= \TBcom\Codes\BlogMain) && ($this->pagecode <= \TBcom\Codes\BlogDelete))
				$f = "log/blog.txt";
			else if (($this->pagecode >= \TBcom\Codes\OpinionsMain) && ($this->pagecode <= \TBcom\Codes\OpinionsDelete))
				$f = "log/opinions.txt";
			if (file_put_contents($f, $cont) === FALSE)
				throw new \TBcom\NoHeaderException();
		}
		catch (\TBcom\NoHeaderException $ex) {
			header('Location: /error' . \TBcom\ext . '?e=500');
		}
	}

	public function output() {
		$sinit = "";
		switch ($this->pagecode) {
			case \TBcom\Codes\ArtEditor: case \TBcom\Codes\BlogEditor: case \TBcom\Codes\OpinionsEditor: case \TBcom\Codes\AdminLog: case \TBcom\Codes\UsersEditor:
			case \TBcom\Codes\VisitorLog:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" type="text/css" href="/admin/assets/css/input-row.css" />
		<script src="/node_modules/vue/dist/vue.min.js"></script>
		<script src="/node_modules/vue-resource/dist/vue-resource.min.js">
EOF;
				break;
			case \TBcom\Codes\RateMedia:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<script src="/node_modules/vue/dist/vue.min.js"></script>
		<script src="/node_modules/vue-resource/dist/vue-resource.min.js">
EOF;
				break;
			case \TBcom\Codes\OpinionGen:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" type="text/css" href="/admin/assets/css/opiniongen-input-row.css" />
		<link rel="stylesheet" type="text/css" href="/admin/assets/css/opiniongen-row.css" />
		<link rel="stylesheet" type="text/css" href="/admin/assets/css/input-row.css" />
		<script src="/node_modules/vue/dist/vue.min.js"></script>
		<script src="/node_modules/vue-resource/dist/vue-resource.min.js">
EOF;
				break;
			case \TBcom\Codes\Contact: case \TBcom\Codes\PortfolioContact:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" type="text/css" href="/assets/css/input-field.css" />
		<script src="/node_modules/vue/dist/vue.min.js"></script>
		<script src="/node_modules/vue-resource/dist/vue-resource.min.js">
EOF;
				break;
			case \TBcom\Codes\TextEditor:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<script type="text/javascript" src="/node_modules/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="/admin/assets/js/text_editor.min.js">
EOF;
				break;
			case \TBcom\Codes\Photos:
				$sinit .= "\n";
				$sinit .= <<<EOF
			</script>
			<script type="text/javascript" src="/node_modules/jquery/dist/jquery.min.js"></script>
			<script type="text/javascript" src="../assets/js/cycle.min.js">
EOF;
				break;
			case \TBcom\Codes\ArtView:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" href="/assets/css/art-slide.css" />
		<script src="/node_modules/vue/dist/vue.min.js">
EOF;
				$foot = "";
				$foot .= <<<EOF
				<script type="text/javascript" async src="/assets/js/art_view.min.js"></script>
EOF;
				$this->setFooter(\TBcom\Snip("</body>", "</html>", $this->getFooter()));
				$this->footer->append($foot);
				$this->footer->append("\n\t</body>\n</html>\n");
				break;
			case \TBcom\Codes\ArtIndex:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" href="/assets/css/art-work.css" />
		<link rel="stylesheet" href="/assets/css/art-expand.css" />
		<script src="/node_modules/vue/dist/vue.min.js"></script>
		<script src="/node_modules/vue-resource/dist/vue-resource.min.js">
EOF;
				$foot = "";
				$foot .= <<<EOF
				<script type="text/javascript" async src="/assets/js/art_index.min.js"></script>
EOF;
				$this->setFooter(\TBcom\Snip("</body>", "</html>", $this->getFooter()));
				$this->footer->append($foot);
				$this->footer->append("\n\t</body>\n</html>\n");
				break;
			case \TBcom\Codes\BlogIndex: case \TBcom\Codes\BlogView: case \TBcom\Codes\BlogGallery:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" href="/assets/css/blog-post.css" />
		<script src="/node_modules/vue/dist/vue.min.js">
EOF;
				$foot = "";
				$foot .= <<<EOF
				<script type="text/javascript" async src="/assets/js/blog.min.js"></script>
EOF;
				$this->setFooter(\TBcom\Snip("</body>", "</html>", $this->getFooter()));
				$this->footer->append($foot);
				$this->footer->append("\n\t</body>\n</html>\n");
				break;
			case \TBcom\Codes\MusicView:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" href="/assets/css/music-track.css" />
		<script type="text/javascript" src="/node_modules/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="/node_modules/vue/dist/vue.min.js">
EOF;
				$foot = "";
				$foot .= <<<EOF
				<script type="text/javascript" src="/assets/js/music_view.min.js"></script>
EOF;
				$this->setFooter(\TBcom\Snip("</body>", "</html>", $this->getFooter()));
				$this->footer->append($foot);
				$this->footer->append("\n\t</body>\n</html>\n");
				break;
			default:
				break;
		}

		$hour = date("H", mktime());

		if ($this->pagecode > 40) {
			$this->header->replace("{{STYLE}}", (((strpos($_SERVER["HTTP_USER_AGENT"], "X11") !== FALSE) || (strpos($_SERVER["HTTP_USER_AGENT"], "Linux") !== FALSE)) ? "admin_linux.css" : "admin.css"));
		}

		if (isset($_SESSION["style"])) {
			if (strcmp($_SESSION["style"], "day") == 0) {
				$this->header->replace("{{STYLE}}", (((strpos($_SERVER["HTTP_USER_AGENT"], "X11") !== FALSE) || (strpos($_SERVER["HTTP_USER_AGENT"], "Linux") !== FALSE)) ? "daytime_linux.css" : "daytime.css"));
				$this->footer->replace("{{SWITCHTO}}", "night");
			}
			else {
				$this->header->replace("{{STYLE}}", (((strpos($_SERVER["HTTP_USER_AGENT"], "X11") !== FALSE) || (strpos($_SERVER["HTTP_USER_AGENT"], "Linux") !== FALSE)) ? "style_linux.css" : "style.css"));
				$this->footer->replace("{{SWITCHTO}}", "day");
			}
		}
		else {
			if ((intval($hour) >= 9) && (intval($hour) <= 19)) {
				$this->header->replace("{{STYLE}}", (((strpos($_SERVER["HTTP_USER_AGENT"], "X11") !== FALSE) || (strpos($_SERVER["HTTP_USER_AGENT"], "Linux") !== FALSE)) ? "daytime_linux.css" : "daytime.css"));
				$this->footer->replace("{{SWITCHTO}}", "night");
			}
			else {
				$this->header->replace("{{STYLE}}", (((strpos($_SERVER["HTTP_USER_AGENT"], "X11") !== FALSE) || (strpos($_SERVER["HTTP_USER_AGENT"], "Linux") !== FALSE)) ? "style_linux.css" : "style.css"));
				$this->footer->replace("{{SWITCHTO}}", "day");
			}
		}
		$this->footer->replace("{{CURRENT_YEAR}}", date("Y", mktime()));
		$this->end = microtime(true);
		$this->footer->replacea([
			"{{MEMORY_TECH}}" => memory_get_usage(),
			"{{TIME_TECH}}" => round(($this->end - $this->start), 13),
			"{{OUTPUT_TECH}}" => strlen($this->header->get() . $this->middle->get() . $this->footer->get())
		]);

		$this->header->replace("{{TOKEN}}", $this->token);
		if (strpos($this->header->getTitle(), "Text Editing ") === FALSE)
			$this->middle->replace("{{TOKEN}}", $this->token);
		$this->header->replacea([
			"{{BREADCRUMBS}}" => "",
			"{{SCRIPT_INIT}}" => $sinit ?? "",
			"{{THOUGHTS_STYLE}}" => "display:none;"
		]);
		$this->header->keywords();
		$this->header->ogImage("http://tannerbabcock.com/images/ogimage.png");

		$this->header->replace("{{EXT}}", \TBcom\ext ?? "");
		$this->middle->replace("{{EXT}}", \TBcom\ext ?? "");
		$this->footer->replace("{{EXT}}", \TBcom\ext ?? "");

		if ($this->pagecode == \TBcom\Codes\MusicView)
			$this->header->replace("{{OG_TYPE}}", "music");

		if ($this->pagecode == \TBcom\Codes\BlogView || $this->pagecode == \TBcom\Codes\OpinionsList || $this->pagecode == \TBcom\Codes\RateMediaView)
			$this->header->replace("{{OG_TYPE}}", "article");
		else
			$this->header->replace("{{OG_TYPE}}", "website");

		if (($this->token) && (isset($_SESSION["current_user"])) && (isset($_SESSION["current_ip"]))) {
			$this->header->replace("{{CURRENT_USER}}", $_SESSION["current_user"]);
			$this->middle->replace("{{CURRENT_IP}}", $_SESSION["current_ip"]);
		}

		$this->header->output();
		$this->middle->output();
		$this->footer->output();
	}

	public static function security() {
		global $my_password;

		$pin = file_get_contents(__DIR__ . "/../../admin/resources/pin.txt");
		if (!$pin)
			die("No PIN file found for hashing. Please populate");
		return ((isset($_GET['tok'])) && (isset($_SESSION["current_user"])) && (isset($_SESSION["rtoken"])) && (strcmp($_GET['tok'], \TBcom\Secure($my_password, $pin)) == 0) && (strcmp($_SESSION["rtoken"], \TBcom\Secure($my_password, $pin, true)) == 0));
	}

	public static function adminSecurity() {
		global $my_password;
		$ext = \TBcom\ext;
		$pin = file_get_contents(__DIR__ . "/../../admin/resources/pin.txt");
		if (!$pin)
			die("No PIN file found for hashing. Please populate");

		if ((!isset($_GET['tok'])) || (strcmp($_GET['tok'], \TBcom\Secure($my_password, $pin)) != 0))
			header('Location: login' . $ext);
		if (!isset($_SESSION["current_user"]) || !isset($_SESSION["current_ip"]) || !isset($_SESSION["rtoken"]) || (strcmp($_SESSION["rtoken"], \TBcom\Secure($my_password, $pin, true)) != 0))
			header('Location: login' . $ext);
	}
};

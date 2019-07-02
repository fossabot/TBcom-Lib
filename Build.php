<?php
/* TBcom-Lib Build
 *
 *     \TBcom\Build
 *
 * Copyright (c) 2019-2020 Tanner Babcock.
 * This software is licensed under the terms of the MIT License. See LICENSE for details.
*/
namespace TBcom\Build;
use TBcom\Codes as C;
use TBcom\Methods as M;

///     \TBcom\Build\ContentSection
///
class ContentSection {
	private $content;

	/// constructor
	///
	/// The basic constructor for the ContentSection fetches an HTML file from /resources/views, or /foo/resources/views.
	///
	///     <?php
	///     $c = new ContentSection("dolphin");
	///
	///     This loads /resources/views/dolphin.html
	///
	///     $c = new ContentSection("mammal.bear");
	///
	///     This loads /mammal/resources/views/bear.html
	///
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
		unset($this->content);
	}

	public function set($data) { $this->content = $data; }
	public function get() { return $this->content; }
	public function append($data) { $this->content .= $data; }
	public function prepend($data) { $this->content = $data . $this->content; }

	/// replace($plchold, $val = "")
	///
	/// Replaces $plchold with $val. $plchold appears as "{{PLACEHOLDER}}" in the file's body.
	///
	///     <?php
	///     $a = new P("top", "middle");
	///     $a->middle->replace("PI", (22/7));
	///     $a->middle->replace("FULL_NAME", "John Doe");
	///
	///     middle.html:
	///
	///     <div class="content">
	///         <h1>My full name is {{FULL_NAME}}</h1>
	///         <h3>And the number pi is {{PI}}</h3>
	///     </div>
	///
	public function replace($plchold, $val = "") {
		$this->content = str_replace("{{" . $plchold . "}}", $val, $this->content);
	}

	/// replacea($repArr)
	///
	/// Processes an array of find -> replace key pairs.
	///
	///     <?php
	///     $page = new P("top", "middle");
	///     $page->middle->replacea([
	///          "PI" => (22/7),
	///          "FULL_NAME" => "John Doe",
	///          "AGE" => 28
	///     ]);
	///     
	public function replacea($repArr) {
		foreach ($repArr as $plchold => $val) {
			$this->replace($plchold, $val);
		}
	}

	/// load($filename)
	///
	/// Load the contents of filename into $this->content.
	///
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

	/// output()
	///
	/// Echoes the entirety of $this->content.
	///
	public function output() {
		echo $this->content;
	}
};

///      \TBcom\Build\Header
///
class Header extends ContentSection {
	private $title;

	/// constructor
	///
	/// The constructor loads the contents of $filename into $content (of the parent ContentSection), and saves $t as the title.
	/// If no title is supplied, it uses the placeholder {{TITLE}} by default, so the programmer can specify the page title later.
	///
	public function __construct($filename, $t = "{{TITLE}}") {
		parent::__construct($filename);
		$this->title = $t;
	}

	public function __destruct() {
		parent::__destruct();
		unset($this->title);
	}

	public function setTitle($t) {
		$this->title = $t;
		$this->replace("TITLE", $this->title);
	}

	public function getTitle() { return $this->title; }

	/// keywords($k = "")
	///
	/// This function replaces {{KEYWORDS}} which is typically saved in header.html. Use this function to specify keywords for the <meta name="keywords"> tag.
	///
	public function keywords($k = "") { $this->replace("KEYWORDS", $k); }

	/// description($d = "")
	///
	/// This function replaces {{META_DESCRIPTION}} which is typically saved in header.html. Use this function to specify a short description for the <meta name="description"> tag.
	///
	public function description($d = "") { $this->replace("META_DESCRIPTION", $d); }

	/// ogImage($url)
	///
	/// This sets the preview image URL for Facebook's Open Graph protocol. It also fetches the height and width.
	///
	///     <?php
	///     $a = new P("top");
	///     $a->ogImage("http://example.com/ogimage.png");
	///    
	public function ogImage($url) {
		$w = @\getimagesize($url)[0];
		$h = @\getimagesize($url)[1];
		$this->replacea([
			"OG_IMAGE" => $url,
			"OG_WIDTH" => "" . $w,
			"OG_HEIGHT" => "" . $h
		]);
	}

	/// breadcrumbs($bcArray)
	///
	/// Use the array as breadcrumbs with title -> url pairs. This is used for search engine optimization, and
	/// is typically one of the last lines in the source code, before output().
	///
	///     <?php
	///     $a = new P("top", "middle");
	///
	///     $a->header->setTitle("My Great Site");
	///     $a->header->description("My wonderful description for my site.");
	///
	///     $a->header->breadcrumbs([
	///          "Home" => "https://example.com/home",
	///          "Blog" => "https://example.com/blog"
	///     ]);
	///     
	///     appears on search engines as:
	///     
	///     My Great Site
	///       My wonderful description for my site.
	///       Home > Blog
	///
	public function breadcrumbs($bcArray) {
		$bcOut = "";
		$pos = 1;
		foreach ($bcArray as $k => $v) {
			$bcOut .= M::Tag(file_get_contents(__DIR__ . "/../views/breadcrumb.html"), [ $v, $k, $pos ]);
			$pos++;
		}
		$this->replace("BREADCRUMBS", $bcOut);
	}
};

///     \TBcom\Build\Footer
///
class Footer extends ContentSection {
	public function __construct($filename) {
		parent::__construct($filename);
	}

	public function __destruct() {
		parent::__destruct();
	}
};

///     \TBcom\Build\Middle
///
class Middle extends ContentSection {
	public function __construct($filename) {
		parent::__construct($filename);
	}

	public function __destruct() {
		parent::__destruct();
	}

	/// script($s)
	///
	/// Include the specified script at the end of the content body. This does not include it with <script src>,
	/// it echoes the content of the script between <script> tags.
	///
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

///     \TBcom\Build\Page
///
class Page {
	public $header;
	public $middle;
	public $footer;
	private $start;
	private $end;
	private $pagecode;
	private $token;

	/// constructor
	///
	/// The only argument that does not have a default value is the first, for the header file. After this, the second may be assumed and empty file, and the third can be assumed "footer".
	/// The title may also be set this way, but only after explicitly providing the footer as the third argument.
	///
	///    <?php
	///    $a = new P("top");
	///    $b = new P("top", "middle");
	///    $c = new P("top", "middle", "bottom", "Untitled");
	///
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

	public function __destruct() {
		unset($this->header);
		unset($this->middle);
		unset($this->footer);
		unset($this->start);
		unset($this->end);
		unset($this->token);
	}

	/// setPageCode($p)
	///
	///
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
	public function getPageCode() { return $this->pagecode; }

	/// init($t, $des, $c = null)
	///
	/// A quick way to initialize a page. The first argument is the title, second the description, and the third is the page code.
	///
	///    <?php
	///    $myPage = new P("header", "content");
	///    $myPage->init("My Great Page", "The best web site in the world!", GreatPage);
	///
	public function init($t, $des, $c = null) {
		$this->header->setTitle($t);
		$this->header->description($des);
		$this->setPageCode($c);
	}

	/// getToken()
	///
	/// This function will only return something if the user is successfully authenticated
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
			if (fwrite($handle, "" . mktime() . "|{$_SERVER['REMOTE_ADDR']}|" . $data) === false) {
				fclose($handle);
				throw new \TBcom\NoHeaderException();
			}
			fclose($handle);
		}
		catch (\TBcom\NoHeaderException $ex) {
			header('Location: /error' . \TBcom\ext . '?e=500');
		}
	}

	/// recent($cont)
	///
	/// Write $cont to the text file noting the most recently edited media
	public function recent($cont) {
		try {
			if (($this->pagecode >= C\ArtMain) && ($this->pagecode <= C\ArtGallery))
				$f = "log/art.txt";
			else if (($this->pagecode >= C\BlogMain) && ($this->pagecode <= C\BlogDelete))
				$f = "log/blog.txt";
			else if (($this->pagecode >= C\OpinionsMain) && ($this->pagecode <= C\OpinionsDelete))
				$f = "log/opinions.txt";
			if (file_put_contents($f, $cont) === false)
				throw new \TBcom\NoHeaderException();
		}
		catch (\TBcom\NoHeaderException $ex) {
			header('Location: /error' . \TBcom\ext . '?e=500');
		}
	}

	/// output()
	///
	/// Echoes the entire page, top to bottom, after determining a number of last-minute conditions.
	/// This should be at the very bottom of the source code,otherwise only inside a catch statement.
	///
	///    <?php
	///    $a = new P("top", "middle");
	///    $a->init("Foo", "bar", 10);
	///    try {
	///        crazyThing();
	///    }
	///    catch (CrazyException $e) {
	///        $a->setMiddle("<p>Oops!</p>");
	///        $a->output();
	///        exit();
	///    }
	///    $a->output();
	///
	public function output() {
		$sinit = "";
		switch ($this->pagecode) {
			case C\ArtEditor: case C\BlogEditor: case C\OpinionsEditor: case C\AdminLog: case C\UsersEditor:
			case C\VisitorLog:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" type="text/css" href="/admin/assets/css/input-row.css" />
		<script src="https://unpkg.com/vue@2.6.10/dist/vue.min.js"></script>
		<script src="https://unpkg.com/vue-resource@1.5.1/dist/vue-resource.min.js">
EOF;
				break;
			case C\RateMedia:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<script src="https://unpkg.com/vue@2.6.10/dist/vue.min.js"></script>
		<script src="https://unpkg.com/vue-resource@1.5.1/dist/vue-resource.min.js">
EOF;
				break;
			case C\OpinionGen:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" type="text/css" href="/admin/assets/css/opiniongen-input-row.css" />
		<link rel="stylesheet" type="text/css" href="/admin/assets/css/opiniongen-row.css" />
		<link rel="stylesheet" type="text/css" href="/admin/assets/css/input-row.css" />
		<script src="https://unpkg.com/vue@2.6.10/dist/vue.min.js"></script>
		<script src="https://unpkg.com/vue-resource@1.5.1/dist/vue-resource.min.js">
EOF;
				break;
			case C\Contact: case C\PortfolioContact:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" type="text/css" href="/assets/css/input-field.css" />
		<script src="https://unpkg.com/vue@2.6.10/dist/vue.min.js"></script>
		<script src="https://unpkg.com/vue-resource@1.5.1/dist/vue-resource.min.js">
EOF;
				break;
			case C\TextEditor:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<script type="text/javascript" src="https://unpkg.com/jquery@3.4.1/dist/jquery.min.js"></script>
		<script type="text/javascript" src="/admin/assets/js/text_editor.min.js">
EOF;
				break;
			case C\Photos:
				$sinit .= "\n";
				$sinit .= <<<EOF
			</script>
			<script type="text/javascript" src="https://unpkg.com/jquery@3.4.1/dist/jquery.min.js"></script>
			<script type="text/javascript" src="../assets/js/cycle.min.js">
EOF;
				break;
			case C\ArtView:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" href="/assets/css/art-slide.css" />
		<script src="https://unpkg.com/vue@2.6.10/dist/vue.min.js">
EOF;
				$foot = "";
				$foot .= <<<EOF
				<script type="text/javascript" async src="/assets/js/art_view.min.js"></script>
EOF;
				$this->setFooter(M::Snip("</body>", "</html>", $this->getFooter()));
				$this->footer->append($foot);
				$this->footer->append("\n\t</body>\n</html>\n");
				break;
			case C\ArtIndex:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" href="/assets/css/art-work.css" />
		<link rel="stylesheet" href="/assets/css/art-expand.css" />
		<script src="https://unpkg.com/vue@2.6.10/dist/vue.min.js"></script>
		<script src="https://unpkg.com/vue-resource@1.5.1/dist/vue-resource.min.js">
EOF;
				$foot = "";
				$foot .= <<<EOF
				<script type="text/javascript" async src="/assets/js/art_index.min.js"></script>
EOF;
				$this->setFooter(M::Snip("</body>", "</html>", $this->getFooter()));
				$this->footer->append($foot);
				$this->footer->append("\n\t</body>\n</html>\n");
				break;
			case C\BlogIndex: case C\BlogView: case C\BlogGallery:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" href="/assets/css/blog-post.css" />
		<script src="https://unpkg.com/vue@2.6.10/dist/vue.min.js">
EOF;
				$foot = "";
				$foot .= <<<EOF
				<script type="text/javascript" async src="/assets/js/blog.min.js"></script>
EOF;
				$this->setFooter(M::Snip("</body>", "</html>", $this->getFooter()));
				$this->footer->append($foot);
				$this->footer->append("\n\t</body>\n</html>\n");
				break;
			case C\MusicView:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" href="/assets/css/music-track.css" />
		<script type="text/javascript" src="https://unpkg.com/jquery@3.4.1/dist/jquery.min.js"></script>
		<script type="text/javascript" src="https://unpkg.com/vue@2.6.10/dist/vue.min.js">
EOF;
				$foot = "";
				$foot .= <<<EOF
				<script type="text/javascript" src="/assets/js/music_view.min.js"></script>
EOF;
				$this->setFooter(M::Snip("</body>", "</html>", $this->getFooter()));
				$this->footer->append($foot);
				$this->footer->append("\n\t</body>\n</html>\n");
				break;
			case C\RateMediaView:
				$sinit .= "\n";
				$sinit .= <<<EOF
		</script>
		<link rel="stylesheet" type="text/css" href="/assets/css/flags.css" />
		<script>
EOF;
				break;
			default:
				break;
		}

		$hour = date("H", mktime());

		if ($this->pagecode > 40) {
			$this->header->replace("STYLE", (((strpos($_SERVER["HTTP_USER_AGENT"], "X11") !== false) || (strpos($_SERVER["HTTP_USER_AGENT"], "Linux") !== false)) ? "admin_linux.css" : "admin.css"));
		}

		if (isset($_SESSION["style"])) {
			if (strcmp($_SESSION["style"], "day") == 0) {
				$this->header->replace("STYLE", (((strpos($_SERVER["HTTP_USER_AGENT"], "X11") !== false) || (strpos($_SERVER["HTTP_USER_AGENT"], "Linux") !== false)) ? "daytime_linux.css" : "daytime.css"));
				$this->footer->replace("SWITCHTO", "night");
			}
			else {
				$this->header->replace("STYLE", (((strpos($_SERVER["HTTP_USER_AGENT"], "X11") !== false) || (strpos($_SERVER["HTTP_USER_AGENT"], "Linux") !== false)) ? "style_linux.css" : "style.css"));
				$this->footer->replace("SWITCHTO", "day");
			}
		}
		else {
			if ((intval($hour) >= 9) && (intval($hour) <= 19)) {
				$this->header->replace("STYLE", (((strpos($_SERVER["HTTP_USER_AGENT"], "X11") !== false) || (strpos($_SERVER["HTTP_USER_AGENT"], "Linux") !== false)) ? "daytime_linux.css" : "daytime.css"));
				$this->footer->replace("SWITCHTO", "night");
			}
			else {
				$this->header->replace("STYLE", (((strpos($_SERVER["HTTP_USER_AGENT"], "X11") !== false) || (strpos($_SERVER["HTTP_USER_AGENT"], "Linux") !== false)) ? "style_linux.css" : "style.css"));
				$this->footer->replace("SWITCHTO", "day");
			}
		}
		$this->footer->replace("CURRENT_YEAR", date("Y", mktime()));
		$this->end = microtime(true);
		$this->footer->replacea([
			"MEMORY_TECH" => memory_get_usage(),
			"TIME_TECH" => round(($this->end - $this->start), 13),
			"OUTPUT_TECH" => strlen($this->header->get() . $this->middle->get() . $this->footer->get())
		]);

		$this->header->replace("TOKEN", $this->token);
		if (strpos($this->header->getTitle(), "Text Editing ") === false)
			$this->middle->replace("TOKEN", $this->token);
		$this->header->replacea([
			"BREADCRUMBS" => "",
			"SCRIPT_INIT" => $sinit ?? "",
			"THOUGHTS_STYLE" => "display:none;"
		]);
		$this->header->keywords();
		$this->header->ogImage("https://tannerbabcock.com/images/ogimage.png");

		$this->header->replace("EXT", \TBcom\ext ?? "");
		$this->middle->replace("EXT", \TBcom\ext ?? "");
		$this->footer->replace("EXT", \TBcom\ext ?? "");

		if ($this->pagecode == C\MusicView)
			$this->header->replace("OG_TYPE", "music");

		if ($this->pagecode == C\BlogView || $this->pagecode == C\OpinionsList || $this->pagecode == C\RateMediaView)
			$this->header->replace("OG_TYPE", "article");
		else
			$this->header->replace("OG_TYPE", "website");

		if (($this->token) && (isset($_SESSION["current_user"])) && (isset($_SESSION["current_ip"]))) {
			$this->header->replace("CURRENT_USER", $_SESSION["current_user"]);
			$this->middle->replace("CURRENT_IP", $_SESSION["current_ip"]);
		}

		$this->header->output();
		$this->middle->output();
		$this->footer->output();
	}


	/// security()
	///
	/// Returns whether the user is authenticated. Use like this, only on pages meant for both public and admin use:
	///
	///    <?php
	///    $admin = P::security();
	///
	public static function security() {
		global $my_password;

		$pin = file_get_contents(__DIR__ . "/../../admin/resources/pin.txt");
		if (!$pin)
			die("No PIN file found for hashing. Please populate");
		return ((isset($_GET['tok'])) && (isset($_SESSION["current_user"])) && (isset($_SESSION["rtoken"])) && (strcmp($_GET['tok'], M::Secure($my_password, $pin)) == 0) && (strcmp($_SESSION["rtoken"], M::Secure($my_password, $pin, true)) == 0));
	}

	/// adminSecurity()
	///
	/// Unlike security(), this does not return anything. Use only on pages for which admin privileges are required. This kicks out the user if they are not sufficiently authenticated 
	///
	///    <?php
	///    P::adminSecurity();
	///
	public static function adminSecurity() {
		global $my_password;
		$ext = \TBcom\ext;
		$pin = file_get_contents(__DIR__ . "/../../admin/resources/pin.txt");
		if (!$pin)
			die("No PIN file found for hashing. Please populate");

		if ((!isset($_GET['tok'])) || (strcmp($_GET['tok'], M::Secure($my_password, $pin)) != 0))
			header('Location: login' . $ext);
		if (!isset($_SESSION["current_user"]) || !isset($_SESSION["current_ip"]) || !isset($_SESSION["rtoken"]) || (strcmp($_SESSION["rtoken"], M::Secure($my_password, $pin, true)) != 0))
			header('Location: login' . $ext);
	}
};

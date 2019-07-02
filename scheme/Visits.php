<?php
/* TBcom-Lib Scheme
 *
 *      \TBcom\Scheme
 *
 * Copyright (c) 2019-2020 Tanner Babcock.
 * This software is licensed under the terms of the MIT License. See LICENSE for details.
*/
namespace TBcom\Scheme;

class Visit {
	/* TABLE: `visits`
		____________________________
		|       url | varchar(50)  |  The URL of the visited page. This should be a unique column.
		|___________|______________|
		|     views | int(11)      |  Number of views. Each view is counted as a visit that's not by `lastip` on the date `lastvisit`.
		|___________|______________|
		|    lastip | varchar(18)  |  The last IP address to visit this page. This gets replaced on every new page view.
		|___________|______________|
		| lastvisit | date         |  The last time someone viewed this page.
		|___________|______________|
	*/
	private $url;
	private $views;
	private $lastip;
	private $lastvisit;

	public function __construct($u = "") {
		$this->url = $u;
	}

	public function __destruct() {
		unset($this->url);
		unset($this->views);
		unset($this->lastip);
		unset($this->lastvisit);
	}

	public function getUrl() { return $this->url; }
	public function setUrl($u) { $this->url = $u; }
	public function getViews() { return $this->views; }
	public function setViews($i) { $this->views = $i; }
	public function getLastIp() { return $this->lastip; }
	public function setLastIp($a) { $this->lastip = $a; }
	public function getLastVisit() { return $this->lastvisit; }
	public function setLastVisit($v) { $this->lastvisit = $v; }

	public function seta($arr) {
		foreach ($arr as $k => $v) {
			if (strcmp($k, "url") == 0) { $this->url = $v; }
			if (strcmp($k, "views") == 0) { $this->views = $v; }
			if (strcmp($k, "lastip") == 0) { $this->lastip = $v; }
			if (strcmp($k, "lastvisit") == 0) { $this->lastvisit = $v; }
		}
	}
	public function geta(&$arr) {
		$arr = [
			"url" => $this->url,
			"views" => $this->views,
			"lastip" => $this->lastip,
			"lastvisit" => $this->lastvisit
		];
	}

	public function write() {
		global $TheBase;

		$st = $TheBase->Prepare("INSERT INTO `visits` VALUES (?, ?, ?, ?)");

		if (!($st->bind_param("siss", $prep_url, $prep_views, $prep_ip, $prep_visit))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}

		$prep_url = $this->getUrl();
		$prep_views = $this->getViews();
		$prep_ip = $this->getLastIp();
		$prep_visit = $this->getLastVisit();

		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$st->close();
		unset($st);
	}
	public function read($u = "") {
		global $TheBase;

		if (!$u) {
			throw new \TBcom\NotFoundException();
		}
		$st = $TheBase->Prepare("SELECT * FROM `visits` WHERE `url`=? LIMIT 1");
		if (!($st->bind_param("s", $prep_url))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$prep_url = $u;
		if (!($st->bind_result($s_url, $s_views, $s_ip, $s_visit))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		if (!($st->execute()) || !($st->fetch())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$this->url = $s_url;
		$this->views = $s_views;
		$this->lastip = $s_ip;
		$this->lastvisit = $s_visit;
		$st->close();
		unset($st);
	}

	public function update($u = "", $i = "") {
		global $TheBase;

		if (!$u) {
			throw new \TBcom\NotFoundException();
		}

		$st = $TheBase->Prepare("UPDATE `visits` SET `url`=?, `views`=views+1, `lastip`=?, `lastvisit`=NOW()");
		if (!($st->bind_param("ss", $prep_url, $prep_ip))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}

		$prep_url = $u;
		$prep_ip = $i;

		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$st->close();
		unset($st);
	}

	public static function table($token) {
		global $TheBase;
		$ext = \TBcom\ext;

		$output = "";
		$st = $TheBase->Prepare("SELECT * FROM `visits` ORDER BY `lastvisit`,`views` ASC");
		if (!($st->bind_result($s_url, $s_views, $s_ip, $s_visit))) {
			$st->close();
			throw new \TBcom\MySQLFailException("bind_result() failed");
		}
		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException("execute() failed");
		}

		for ($x = 0; $st->fetch(); $x++) {
			$output .= "\t<tr style=\"height:50px\">\n";
			$output .= "\t\t<td><a href=\"" . $s_url . "\" target=\"_blank\">" . explode("com/", $s_url)[1] . "</a></td>\n";
			$output .= "\t\t<td style=\"font-size:1.4em\">" . $s_views . "</td>\n";
			$output .= "\t\t<td>" . $s_ip . "</td>\n\t\t<td>" . date("m/d/Y", $s_visit) . "</td>\n\t</tr>";

			if ($x % 10 == 0)
				$output .= "\t<tr><td colspan=\"4\"></td></tr>\n";"
		}
	}
};

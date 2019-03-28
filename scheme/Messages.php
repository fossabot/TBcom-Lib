<?php
/* TBcom-Lib Scheme
 *
 *      \TBcom\Scheme
 *
 * Copyright (c) 2019 Tanner Babcock.
 * This software is licensed under the terms of the MIT License. See LICENSE for details.
*/
namespace TBcom\Scheme;
require_once(__DIR__ . "/../Post.php");

class Message extends PostType {
	private $first;
	private $last;
	private $subject;
	private $email;
	private $sent;
	private $useragent;
	private $portfolio;

	public function __construct($i = 0, $b = "") {
		parent::__construct($i, $b);
	}

	public function __destruct() {
		parent::__destruct();
	}

	public function getName() { return $this->first . " " . $this->last; }
	public function setFirst($f = "") { $this->first = $f; }
	public function setLast($l = "") { $this->last = $l; }
	public function getEmail() { return $this->email; }
	public function setEmail($e = "") { $this->email = $e; }
	public function getSent() { return $this->sent; }
	public function setSent($s = "") { $this->sent = $s; }
	public function getAgent() { return $this->useragent; }
	public function setAgent($u = "") { $this->useragent = $u; }
	public function isPortfolio() { return $this->portfolio; }

	public function seta($arr) {
		foreach ($arr as $k => $v) {
			if (strcmp($k, "id") == 0) { parent::setId($v); }
			if (strcmp($k, "body") == 0) { parent::setBody($v); }
			if (strcmp($k, "first") == 0) { $this->first = $v; }
			if (strcmp($k, "last") == 0) { $this->last = $v; }
			if (strcmp($k, "subject") == 0) { $this->subject = $v; }
			if (strcmp($k, "email") == 0) { $this->email = $v; }
			if (strcmp($k, "sent") == 0) { $this->sent = $v; }
			if (strcmp($k, "useragent") == 0) { $this->useragent = $v; }
		}
	}

	public function geta(&$arr) {
		$arr = [
			"id" => parent::getId(),
			"body" => parent::getBody(),
			"first" => $this->first,
			"last" => $this->last,
			"subject" => $this->subject,
			"email" => $this->email,
			"sent" => $this->sent,
			"useragent" => $this->useragent
		];
	}

	public function read($i = 0) {
		$st = $TheBase->Prepare("SELECT * FROM `messages` WHERE `id`=?");
		if (!($st->bind_param("i", $old_id))) {
			$st->close();
			throw new \TBcom\MySQLFailException("bind_param() failed");
		}
		$old_id = $_GET['id'];
		if (!($st->bind_result($s_id, $s_first, $s_last, $s_subject, $s_email, $s_body, $s_date, $s_useragent, $s_port))) {
			$st->close();
			throw new \TBcom\MySQLFailException("bind_result() failed");
		}
		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException("execute() failed");
		}
		$st->fetch();
		
		parent::setId($s_id);
		parent::setBody($s_body);
		$this->first = $s_first;
		$this->last = $s_last;
		$this->subject = $s_subject;
		$this->email = $s_email;
		$this->sent = $s_date;
		$this->useragent = $s_useragent;
		$this->portfolio = $s_portfolio;

		$st->close();
		unset($st);
	}

	public function write() {
		$st = $TheBase->Prepare("INSERT INTO `messages` VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, FALSE)");
		if (!($st->bind_param("issssss", $prep_id, $prep_first, $prep_last, $prep_subject, $prep_email, $prep_body, $prep_useragent))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$prep_id = parent::getId();
		$prep_first = $this->first;
		$prep_last = $this->last;
		$prep_subject = $this->subject;
		$prep_email = $this->email;
		$prep_body = parent::getBody();
		$prep_useragent = $_SERVER["HTTP_USER_AGENT"];

		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$st->close();
		unset($st);
	}

	public static function delete($i = 0) {
		$i = intval($i);

		$st = $TheBase->Prepare("DELETE FROM `messages` WHERE `id`=?");
		if (!($st->bind_param("i", $old_id))) {
			$st->close();
			throw new \TBcom\MySQLFailException("bind_param() failed");
		}
		$old_id = $i;
		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException("execute() failed");
		}
		$st->close();

		$st = $TheBase->Prepare("UPDATE `messages` SET `id` = id - 1 WHERE id > ?");
		if (!($st->bind_param("i", $prep_id))) {
			$st->close();
			throw new \TBcom\MySQLFailException("bind_param() failed");
		}
		$prep_id = $i;
		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException("execute() failed");
		}
		$st->close();
		// this ^^ decrements all the messages created after the one we just deleted
		// this is important, if the `id`s are not in order, it fucks up the page
	}

	public static function table($token = "") {
		$st = $TheBase->Prepare("SELECT * FROM `messages` ORDER BY `id`");
		if (!($st->bind_result($s_id, $s_first, $s_last, $s_subject, $s_email, $s_body, $s_date, $s_useragent, $s_port))) {
			$st->close();
			throw new \TBcom\MySQLFailException("bind_result() failed");
		}
		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException("execute() failed");
		}
		$output = "";

		while ($st->fetch()) {
			$output .= "\t\t" . Tag("<tr>\n\t\t\t<td>{{0}}</td>\n\t\t\t<td><a href=\"mailto:{{1}}\">{{2}} {{3}}</a></td>\n" .
				"\t\t\t<td><a href=\"mailto:{{1}}\">{{1}}</a></td>\n" .
				"\t\t\t<td><a href=\"messages{{4}}?id={{0}}&amp;tok={{5}}\">{{6}}</a></td>\n" .
				"\t\t\t<td>{{7}}</td>\n\t\t\t<td>{{8}}</td>\n\t\t</tr>\n", [
				$s_id, $s_email, $s_first, $s_last, $ext, $messages->getToken(), substr($s_subject, 0, 130),
				$s_date, (($s_port) ? "Yes" : "No")
			]);
		}
		$st->close();
		unset($st);
		return $output;
	}
};

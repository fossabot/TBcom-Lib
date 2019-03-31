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
	/* TABLE: `messages`
		_______________________________
		|           id | int(11)      |  The ID of the message.
		|--------------|--------------|
		|        first | varchar(30)  |  The first name of the message author.
		|--------------|--------------|
		|         last | varchar(40)  |  The last name of the message author.
		|--------------|--------------|
		|      subject | varchar(140) |  The subject line, or "website" on the portfolio form.
		|--------------|--------------|
		|        email | varchar(50)  |  The author's email.
		|--------------|--------------|
		|         body | text         |  The message body.
		|--------------|--------------|
		|         sent | date         |  The date the message was sent.
		|--------------|--------------|
		|    useragent | varchar(300) |  The user agent of the message sender.
		|--------------|--------------|
		|    portfolio | tinyint(1)   |  Whether the message was submitted through the main site contact form (0), or the portfolio contact form (1).
		|______________|______________|
	*/
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
		unset($this->first);
		unset($this->last);
		unset($this->subject);
		unset($this->email);
		unset($this->sent);
		unset($this->useragent);
		unset($this->portfolio);
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
		global $TheBase;

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
		global $TheBase;

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
		global $TheBase;

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
		global $TheBase;
		$ext = \TBcom\ext;

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
			$subSubject = substr($s_subject, 0, 130);
			$isPort = (($s_port) ? "Yes" : "No");
			$output .= <<<EOF
			<tr>
				<td>{$s_id}</td>
				<td><a href="mailto:{$s_email}">{$s_first} {$s_last}</a></td>
				<td><a href="messages{$ext}?id={$s_id}&amp;tok={$token}">{$subSubject}</a></td>
				<td>{$s_date}</td>
				<td>{$isPort}</td>
			</tr>
EOF;
		}
		$st->close();
		unset($st);
		unset($subSubject);
		return $output;
	}

	public static function writeTableComments() {
		global $TheBase;

		$result = $TheBase->Query("ALTER TABLE `messages` CHANGE `id` `id` INT(11) NULL DEFAULT NULL COMMENT 'The ID of the message.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `messages` CHANGE `first` `first` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'The authors first name.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `messages` CHANGE `last` `last` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'The authors last name.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `messages` CHANGE `subject` `subject` VARCHAR(140) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'The subject line, or website on the portfolio form.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `messages` CHANGE `email` `email` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'The authors email address.");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `messages` CHANGE `body` `body` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'The body of the message.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `messages` CHANGE `sent` `sent` DATE NULL DEFAULT NULL COMMENT 'The subject line, or \"website\" on the portfolio form.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `messages` CHANGE `useragent` `useragent` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'The authors user agent.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `messages` CHANGE `portfolio` `portfolio` TINYINT(1) NULL DEFAULT NULL COMMENT 'Whether the message came from the portfolio or the main site.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}

		unset($result);
	}
};

<?php
/* TBcom-Lib Scheme
 *
 *      TBcom\Scheme
 *
 * Copyright (c) 2019 Tanner Babcock.
 * This software licensed under the terms of the MIT License. See LICENSE for details.
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
		$this->first = null;
		$this->last = null;
		$this->subject = null;
		$this->email = null;
		$this->sent = null;
		$this->useragent = null;
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

};


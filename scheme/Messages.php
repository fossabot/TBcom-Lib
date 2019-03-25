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
};


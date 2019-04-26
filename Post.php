<?php
/* TBcom-Lib Post
 *
 *      \TBcom\Scheme
 *
 * Copyright (c) 2019-2020 Tanner Babcock.
 * This software is licensed under the terms of the MIT License. See LICENSE for details.
*/
namespace TBcom\Scheme;

class PostType {
	private $id;
	private $body;

	public function __construct($i = 0, $b = "") {
		$this->id = $i;
		$this->body = $b;
	}

	public function __destruct() {
		$this->id = null;
		$this->body = "";
	}

	public function setId($i) {
		$this->id = $i;
	}

	public function getId() {
		return $this->id;
	}

	public function setBody($b) {
		$this->body = $b;
	}

	public function getBody() {
		return $this->body;
	}
};

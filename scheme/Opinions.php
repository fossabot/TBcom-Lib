<?php
/* TBcom-Lib Scheme
 *
 *      \TBcom\Scheme
 *
 * Copyright (c) 2019 Tanner Babcock.
 * This software is licensed under the terms of the MIT License. See LICENSE for details.
*/
namespace TBcom\Scheme;

class Opinion {
	/* TABLE: `opinions`
		___________________________
		|    file | varchar(30)   |  The HTML filename of the opinion page.
		|---------|---------------|
		|    body | text          |  The body of the review/reviews.
		|---------|---------------|
		|    type | varchar(6)    |  Film or music?
		|---------|---------------|
		|  format | varchar(2)    |  Text format, either BBCode ("bb") or Markdown ("md").
		|---------|---------------|
		|    comp | date          |  The date the review was last modified.
		|_________|_______________|

	*/
	private $file;
	private $body;
	private $type;
	private $format;
	private $comp;

	public function __construct($f = "") {
		$this->file = $f;
	}

	public function __destruct() {
		unset($this->file);
		unset($this->comp);
		unset($this->body);
		unset($this->type);
		unset($this->format);
	}

	public function setFile($f = "") { $this->file = $f; }
	public function getFile() { return $this->file; }
	public function setComp($c = "") { $this->comp = $c; }
	public function getComp() { return $this->comp; }
	public function setType($t = "") { $this->type = $t; }
	public function getType() { return $this->type; }
	public function setFormat($f = "") { $this->format = $f; }
	public function getFormat() { return $this->format; }
	public function setBody($b) { $this->body = $b; }
	public function getBody($parse = 2) {
		if ($parse == 1) {
			$parser = new \JBBCode\Parser();
			$parser->addCodeDefinitionSet(new \JBBCode\DefaultCodeDefinitionSet());
			$Parsedown = new \Parsedown();

			if ($this->isMark()) {
				return $Parsedown->text($this->body);
			}
			else {
				$parser->parse($this->body);
				return $parser->getAsHtml();
			}
		}
		else
			return $this->body;
	}

	public function seta($arr) {
		foreach ($arr as $k => $v) {
			if (strcmp($k, "file") == 0) { $this->file = $v; }
			if (strcmp($k, "comp") == 0) { $this->comp = $v; }
			if (strcmp($k, "body") == 0) { $this->body = $v; }
			if (strcmp($k, "type") == 0) { $this->type = $v; }
			if (strcmp($k, "format") == 0) { $this->format = $v; }
		}
	}
	public function geta(&$arr) {
		$arr = [
			"file" => $this->file,
			"comp" => $this->comp,
			"body" => $this->body,
			"type" => $this->type,
			"format" => $this->format
		];
	}

	public function isMark() {
		if (strcmp($this->format, "md") == 0)
			return true;
		else
			return false;
	}

	public function isEmpty() {
		return (!$this->file || !$this->type || !$this->body || !$this->format);
	}

	public function write($q = "INSERT INTO `opinions` VALUES(?, ?, ?, ?, NOW())") {
		global $TheBase;
		
		$st = $TheBase->Prepare($q);
		if (substr_count($q, "?") == 5) {
			if (!($st->bind_param("sssss", $prep_file, $prep_body, $prep_type, $prep_format, $prep_comp))) {
				$st->close();
				throw new \TBcom\MySQLFailException();
			}
			$prep_comp = $this->comp;
		}
		else if (substr_count($q, "?") == 4) {
			if (!($st->bind_param("ssss", $prep_file, $prep_body, $prep_type, $prep_format))) {
				$st->close();
				throw new \TBcom\MySQLFailException();
			}
		}
		$prep_file = $this->file;
		$prep_type = $this->type;
		$prep_body = $this->body;
		$prep_format = $this->format;

		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$st->close();
	}

	public function read($f = "") {
		global $TheBase;

		if (!$f) {
			throw new \TBcom\NotFoundException();
		}

		$st = $TheBase->Prepare("SELECT * FROM `opinions` WHERE `file`=? LIMIT 1");
		if (!($st->bind_param("s", $prep_file))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$prep_file = $f;
		if (!($st->bind_result($s_file, $s_body, $s_type, $s_format, $s_comp))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		if (!($st->execute()) || !($st->fetch())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$this->file = $s_file;
		$this->comp = $s_comp;
		$this->type = $s_type;
		$this->body = $s_body;
		$this->format = $s_format;
		$st->close();
		unset($st);
	}

	public function update($q = "UPDATE `opinions` SET `type`=?, `body`=?, `format`=?, `comp`=NOW() WHERE `file`=? LIMIT 1") {
		global $TheBase;

		$st = $TheBase->Prepare($q);
		if (substr_count($q, "?") == 4) {
			if (!($st->bind_param("ssss", $prep_type, $prep_body, $prep_format, $old_file))) {
				$st->close();
				throw new \TBcom\MySQLFailException();
			}
		}
		else if (substr_count($q, "?") == 5) {
			if (!($st->bind_param("sssss", $prep_type, $prep_body, $prep_format, $prep_comp, $old_file))) {
				$st->close();
				throw new \TBcom\MySQLFailException();
			}
			$prep_comp = $this->comp;
		}
		$prep_body = $this->body;
		$prep_format = $this->format;
		$prep_type = $this->type;
		$old_file = $this->file;
		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$st->close();
		unset($st);
	}

	public function recent($type = "film") {
		$st = $TheBase->Prepare("SELECT * FROM `opinions` WHERE `type`='" . $type . "' ORDER BY `comp` DESC LIMIT 1");
		if (!($st->bind_result($o_file, $o_body, $o_type, $o_format, $o_comp)) || !($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$st->fetch();
		$st->close();
		$this->file = $o_file;
		$this->body = $o_body;
		$this->type = $o_type;
		$this->format = $o_format;
		$this->comp = $o_comp;
		unset($st);
	}

	public static function table($token) {
		global $TheBase;
		$ext = \TBcom\ext;
		$output = "";

		$st = $TheBase->Prepare("SELECT * FROM `opinions` ORDER BY `type`,`file` ASC");
		if (!($st->bind_result($s_file, $s_body, $s_type, $s_format, $s_comp))) {
			$st->close();
			throw new MySQLFailException("bind_result() failed");
		}
		if (!($st->execute())) {
			$st->close();
			throw new MySQLFailException("execute() failed");
		}
		$Parsedown = new \Parsedown();

		while ($st->fetch()) {
			if (strcmp($s_format, "md") == 0) {
				$bodyPreview = str_replace("\n", " ", substr($Parsedown->text($s_body), 0, 110));
			}
			else if (strcmp($s_format, "bb") == 0) {
				$bodyPreview = str_replace("[/p]", "", str_replace("[p]", "", substr($s_body, 0, 109)));
			}
			
			$output .= <<<EOF
			<tr>
				<td>{$s_type}</td>
				<td><a href="/rate/{$s_type}{$ext}?f={$s_file}&amp;tok={$token}"><b>{$s_file}</b></a></td>
				<td>{$bodyPreview}...</td>
				<td>{$s_comp}</td>
				<td>
					<a href="opinions{$ext}?sett=editor&amp;file={$s_file}&amp;tok={$token}">Edit</a> |
					<a href="opinions{$ext}?sett=delete&amp;file={$s_file}&amp;tok={$token}">Delete</a>
				</td>
			</tr>
EOF;
		}

		$st->close();
		unset($Parsedown);
		unset($bodyPreview);
		unset($st);
		return $output;
	}

	public static function delete($f = "") {
		global $TheBase;
		$ext = \TBcom\ext;

		$st = $TheBase->Prepare("DELETE FROM `opinions` WHERE `file`=? LIMIT 1");
		if (!($st->bind_param("s", $old_file))) {
			$st->close();
			throw new \TBcom\MySQLFailException("bind_param() failed");
		}
		$old_file = $f;
		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException("execute() failed");
		}
		$st->close();
		unset($st);
	}

	public static function writeTableComments() {
		global $TheBase;

		$result = $TheBase->Query("ALTER TABLE `opinions` CHANGE `file` `file` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'File name for this review, appears in URL.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `opinions` CHANGE `body` `body` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Body text for the review.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `opinions` CHANGE `type` `type` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Review type: film or music.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `opinions` CHANGE `format` `format` VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Text format: Markdown (md) or BBcode (bb).'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `opinions` CHANGE `comp` `comp` DATE NULL DEFAULT NULL COMMENT 'The date the review was last modified.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		unset($result);
	}
};

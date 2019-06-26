<?php
/* TBcom-Lib Scheme
 * 
 *      \TBcom\Scheme
 *
 * Copyright (c) 2019-2020 Tanner Babcock.
 * This software is licensed under the terms of the MIT License. See LICENSE for details.
*/
namespace TBcom\Scheme;
require_once(__DIR__ . "/../Post.php");

class Art extends PostType {
	/* TABLE: `art`
		______________________________
		|          id | int(11)      |  The ID of the piece, relative to its series.
		|-------------|--------------|
		|        comp | date         |  The (approximate) date the piece was completed or uploaded.
		|-------------|--------------|
		|      series | varchar(4)   |  The four-letter series code for the piece.
		|-------------|--------------|
		| description | varchar(250) |  The description of the piece, in BBCode.
		|-------------|--------------|
		|       title | varchar(120) |  The title of the piece. Optional.
		|_____________|______________|
	*/
	private $comp;
	private $series;
	private $title;

	public function __construct($i = 0, $b = "") {
		parent::__construct($i, $b);
	}

	public function __destruct() {
		parent::__destruct();
		unset($this->comp);
		unset($this->series);
		unset($this->title);
	}

	public function getId() { return parent::getId(); }
	public function setId($i) { parent::setId($i); }
	public function getSeries() { return $this->series; }
	public function setSeries($s) { $this->series = $s; }
	public function getComp() { return $this->comp; }
	public function setComp($c) { $this->comp = $c; }
	public function getTitle() { return $this->title; }
	public function setTitle($t) { $this->title = $t; }
	public function setBody($b) { parent::setBody($b); }
	public function getBody() {
		$parser = new \JBBCode\Parser();
		$parser->addCodeDefinitionSet(new \JBBCode\DefaultCodeDefinitionSet());
		$parser->parse(parent::getBody());
		return $parser->getAsHtml();
	}

	public function seta($arr) {
		foreach ($arr as $k => $v) {
			if (strcmp($k, "id") == 0) { parent::setId($v); }
			if (strcmp($k, "body") == 0) { parent::setBody($v); }
			if (strcmp($k, "series") == 0) { $this->series = $v; }
			if (strcmp($k, "comp") == 0) { $this->comp = $v; }
			if (strcmp($k, "title") == 0) { $this->title = $v; }
		}
	}
	public function geta(&$arr) {
		$arr = [
			"id" => parent::getId(),
			"body" => parent::getBody(),
			"series" => $this->series,
			"comp" => $this->comp,
			"title" => $this->title
		];
	}

	public function write() {
		global $TheBase;

		if (isset($this->title)) {
			$ftitle = filter_var($this->title, FILTER_SANITIZE_SPECIAL_CHARS);

			$st = $TheBase->Prepare("INSERT INTO `art` VALUES (?, ?, ?, ?, ?)");
			if (!($st->bind_param("issss", $prep_id, $prep_comp, $prep_series, $prep_descript, $prep_title))) {
				$st->close();
				throw new \TBcom\MySQLFailException();
			}
			$prep_title = $ftitle;
		}
		else {
			$st = $TheBase->Prepare("INSERT INTO `art` VALUES (?, ?, ?, ?, '')");
			if (!($st->bind_param("isss", $prep_id, $prep_comp, $prep_series, $prep_descript))) {
				$st->close();
				throw new \TBcom\MySQLFailException();
			}
		}
		$fdescript = filter_var($this->getBody(), FILTER_SANITIZE_SPECIAL_CHARS);

		$prep_id = $this->getId();
		$prep_comp = $this->comp;
		$prep_series = $this->series;
		$prep_descript = $fdescript;

		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$st->close();
		unset($st);
	}

	public function read($s = "", $i = 0) {
		global $TheBase;

		if (!$s || $i == 0) {
			throw new \TBcom\NotFoundException();
		}

		$st = $TheBase->Prepare("SELECT * FROM `art` WHERE `id`=? AND `series`=? LIMIT 1");
		if (!($st->bind_param("is", $prep_id, $prep_series))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$prep_id = intval($i);
		$prep_series = $s;
		if (!($st->bind_result($s_id, $s_comp, $s_series, $s_body, $s_title))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		if (!($st->execute()) || !($st->fetch())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		if (!$s_body) {
			$st->close();
			throw new \TBcom\NotFoundException();
		}
		parent::setId($s_id);
		$this->series = $s_series;
		$this->comp = $s_comp;
		$this->title = $s_title;
		parent::setBody($s_body);
		$st->close();
		unset($st);
	}

	public function update($s = "", $i = 0) {
		global $TheBase;

		if (!$s || $i == 0) {
			throw new \TBcom\NotFoundException();
		}

		if (isset($this->title)) {
			$ftitle = filter_var($this->title, FILTER_SANITIZE_SPECIAL_CHARS);

			$st = $TheBase->Prepare("UPDATE `art` SET `id`=?, `series`=?, `comp`=?, `description`=?, `title`=? WHERE `id`=? AND `series`=?");
			if (!($st->bind_param("issssis", $prep_id, $prep_series, $prep_comp, $prep_descript, $prep_title, $old_id, $old_series))) {
				$st->close();
				throw new \TBcom\MySQLFailException();
			}
			$prep_title = $ftitle;
		}
		else {
			$st = $TheBase->Prepare("UPDATE `art` SET `id`=?, `series`=?, `comp`=?, `description`=?, `title`='' WHERE `id`=? AND `series`=?");
			if (!($st->bind_param("isssis", $prep_id, $prep_series, $prep_comp, $prep_descript, $old_id, $old_series))) {
				$st->close();
				throw new \TBcom\MySQLFailException();
			}
		}
		$fdescript = filter_var($this->getBody(), FILTER_SANITIZE_SPECIAL_CHARS);

		$prep_id = $this->getId();
		$prep_series = $this->series;
		$prep_comp = $this->comp;
		$prep_descript = $fdescript;
		$old_id = $i;
		$old_series = $s;

		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$st->close();
		unset($st);
		unset($fdescript);
	}

	public function getRecent() {
		global $TheBase;
		$ext = \TBcom\ext;

		$st = $TheBase->Prepare("SELECT * FROM `art` ORDER BY `comp` DESC LIMIT 1");
		if (!($st->bind_result($a_id, $a_comp, $a_series, $a_descript, $a_title)) || !($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$st->fetch();
		$st->close();
		parent::setId($a_id);
		$this->comp = $a_comp;
		$this->series = $a_series;
		$this->title = $a_title;
		parent::setBody($a_descript);
		unset($st);
	}

	public static function fetchCode($code = "", &$numrows = 0) {
		global $TheBase;

		$result = $TheBase->Query("SELECT * FROM `art` WHERE `series`='" . $code . "' ORDER BY `id`");
		$numrows = $result->num_rows;
		$displayed = array_fill(1, $numrows, false);

		$rows = [];
		while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
		$result->free();
		return $rows;
	}

	public static function echoCode($code = "", $atoken = "\"") {
		global $TheBase;
		global $ArtBody;
		global $ArtDescription;
		$ext = \TBcom\ext;

		$stmnt = $TheBase->Prepare("SELECT * FROM `art` WHERE `series`=? ORDER BY `id`");
		if (!($stmnt->bind_param("s", $ol_series))) {
			$stmnt->close();
			throw new \TBcom\MySQLFailException();
		}
		$ol_series = $code;
		if (!($stmnt->bind_result($s_id, $s_comp, $s_series, $s_descript, $s_title))) {
			$stmnt->close();
			throw new \TBcom\MySQLFailException();
		}
		if (!($stmnt->execute())) {
			$stmnt->close();
			throw new \TBcom\MySQLFailException();
		}

		$output = <<<EOF
<center>
<h2>{$ArtBody[$code]}</h2>
{$ArtDescription[$code]}
<center>
	<h3><a href="art{$ext}?s=all{$atoken}>Return to All Pieces</a></h3>
</center>
<div class="artg_table" id="{$ArtBody[$code]}">
	<div class="artg_row">
EOF;
		$titleString = "";
		for ($x = 0; $stmnt->fetch(); $x++) {
			if ($s_title)
				$titleString = " alt=\"{$ArtBody[$s_series]}\" title=\"{$s_title}. Click to see the larger image.\"";
			else
				$titleString = " alt=\"{$ArtBody[$s_series]}\" title=\"Click to see the larger image. This is {$ArtBody[$s_series]} #{$s_id}.\"";

			$output .= <<<EOF
		<art-work href="art{$ext}?s={$s_series}&amp;id={$s_id}{$atoken} series="{$s_series}" id="{$s_id}" {$titleString}></art-work>
EOF;
			if (($x + 1) % 3 == 0)
				$output .= "\t\t</div><div class=\"artg_row\">\n";
		}
		if (!($stmnt->fetch()))
			$output .= "\t\t</div>\n";
		$stmnt->close();
		return $output . "\n</div>\n</center>";
	}

	public static function echoAll($atoken = "\"") {
		global $TheBase;
		global $ArtBody;
		$ext = \TBcom\ext;

		$stmnt = $TheBase->Prepare("SELECT * FROM `art` ORDER BY `series`,`id` ASC");
		if (!($stmnt->bind_result($s_id, $s_comp, $s_series, $s_descript, $s_title)) || !($stmnt->execute())) {
			$stmnt->close();
			throw new \TBcom\MySQLFailException();
		}

		$output = "\t<center>\n\t<h2>All Pieces</h2>\n";
		$output .= "\t<div class=\"artg_table\">\n\t\t<div class=\"artg_row\">\n";
		$titleString = "";

		for ($x = 0; $stmnt->fetch(); $x++) {
			if ($s_title)
				$titleString = " alt=\"{$ArtBody[$s_series]}\" title=\"{$s_title}. Click to see the larger image.\"";
			else
				$titleString = " alt=\"{$ArtBody[$s_series]}\" title=\"Click to see the larger image. This is {$ArtBody[$s_series]} #{$s_id}.\"";
			
			$output .= <<<EOF
			<art-work href="art{$ext}?s={$s_series}&amp;id={$s_id}{$atoken} series="{$s_series}" id="{$s_id}" {$titleString}></art-work>
EOF;
			if (($x + 1) % 3 == 0)
				$output .= "\t\t</div><div class=\"artg_row\">\n";
		}
		if (!$stmnt->fetch())
			$output .= "\t\t</div>\n";
		$stmnt->close();
		return $output . "\t</div>\n\t</center>\n";
	}

	public static function navigate($s = "", $i = 0, $ad = false) {
		global $TheBase;
		$ext = \TBcom\ext;
		$output = "\t\t<ul>\n";
		$sss = $TheBase->Quote($s);

		$result = $TheBase->Query("SELECT * FROM `art` WHERE `series`=" . $sss);
		$max = $result->num_rows;

		if (intval($i) > 1) {
			$output .= "\t\t\t<li><a href=\"art{$ext}?s={$s}&amp;id=" . ($i - 1) . (($ad) ? "&amp;tok={$_GET['tok']}\"" : "\"") . " class=\"prev-link\" alt=\"Previous piece in the series\" title=\"Previous piece in the series\">&larr; Previous Piece</a></li>\n";
		}
		$output .= "\t\t\t<li>&nbsp;</li>\n";
		if (intval($i) < $max) {
			$output .= "\t\t\t<li><a href=\"art{$ext}?s={$s}&amp;id=" . ($i + 1) . (($ad) ? "&amp;tok={$_GET['tok']}\"" : "\"") . " class=\"next-link\" alt=\"Next piece in the series\" title=\"Next piece in the series\">Next Piece &rarr;</a></li>\n";
		}

		$output .= "\t\t</ul>\n";
		$result->free();
		unset($result);
		unset($max);
		return $output;
	}

	public static function table($token) {
		global $TheBase;
		$ext = \TBcom\ext;

		$output = "";
		$st = $TheBase->Prepare("SELECT * FROM `art` ORDER BY `series`,`id` ASC");
		if (!($st->bind_result($s_id, $s_comp, $s_series, $s_descript, $s_title))) {
			$st->close();
			throw new MySQLFailException("bind_result() failed");
		}
		if (!($st->execute())) {
			$st->close();
			throw new MySQLFailException("execute() failed");
		}

		for ($x = 0; $st->fetch(); $x++) {
			$output .= "\t<tr style=\"background:url('/images/art_thumb/thumb_{$s_series}_" . ((intval($s_id) < 10) ? ("0") : ("")) . $s_id . ".jpg'); background-repeat:repeat-x; height:70px;\">\n";
			if (!$s_title) {
				$output .= <<<EOF
			<td style="opacity:0.48;"><a href="/art{$ext}?s={$s_series}&amp;tok={$token}">{$s_series}</a></td>
			<td>{$s_id}</td>
EOF;
			}
			else {
				$output .= <<<EOF
			<td style="opacity:0.54; text-align:center;" colspan="2">
				<a href="/art{$ext}?s={$s_series}&amp;tok={$token}">{$s_title}</a>
			</td>
EOF;
			}
			$trimDescript = substr($s_descript, 0, 97);
			$output .= <<<EOF
			<td style="opacity:0.54;"><a href="/art{$ext}?s={$s_series}&amp;id={$s_id}&amp;tok={$token}">{$s_comp}</a></td>
			<td>{$trimDescript}...</td>
EOF;

			$zzid = ((intval($s_id) < 10) ? "0" : "") . $s_id;

			$output .= <<<EOF
			<td style="text-align:center;">
				<a href="/images/art/{$s_series}_{$zzid}_1.jpg" target="_blank">One</a><br />
				<a href="/images/art/{$s_series}_{$zzid}_2.jpg" target="_blank">Two</a><br />
				<a href="/images/art/{$s_series}_{$zzid}_3.jpg" target="_blank">Three</a>
			</td>
			<td style="opacity:0.45; text-align:right;">
				<a href="art{$ext}?sett=editor&amp;s={$s_series}&amp;id={$s_id}&amp;tok={$token}">Edit</a>
			</td>
EOF;

			if ($x % 10 == 0)
				$output .= "\t<tr><td colspan=\"6\"></td></tr>\n";
		}

		$st->close();
		unset($st);
		unset($trimDescript);
		return $output;
	}

	public static function writeTableComments() {
		global $TheBase;

		$result = $TheBase->Query("ALTER TABLE `art` CHANGE `id` `id` INT(11) NULL DEFAULT NULL COMMENT 'The ID number for this piece, relative to the series.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `art` CHANGE `comp` `comp` DATE NULL DEFAULT NULL COMMENT 'The date the artwork was completed.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `art` CHANGE `series` `series` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Four-letter code for the series.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `art` CHANGE `description` `description` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'The description of the artwork. Includes dimensions, media, authors.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `art` CHANGE `title` `title` VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'The title of the piece. Optional.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		unset($result);
	}
};

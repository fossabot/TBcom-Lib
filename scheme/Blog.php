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

class Blog extends PostType {
	private $title;
	private $tag;
	private $comp;
	private $type;

	public function __construct($i = 0, $b = "") {
		parent::__construct($i, $b);
	}

	public function __destruct() {
		parent::__destruct();
	}

	public function setTitle($t) { $this->title = $t; }
	public function getTitle() { return $this->title; }
	public function setComp($df = "") { $this->comp = $df; }
	public function getComp() { return $this->comp; }
	public function setTag($t) { $this->tag = $t; }
	public function getTag() { return $this->tag; }
	public function setId($i) { parent::setId($i); }
	public function getId() { return parent::getId(); }
	public function setBody($b) { parent::setBody($b); }
	public function getBody($parse = 2) {
		if ($parse == 1) {
			$parser = new \JBBCode\Parser();
			$parser->addCodeDefinitionSet(new \JBBCode\DefaultCodeDefinitionSet());
			$Parsedown = new \Parsedown();

			if ($this->isMark()) {
				return $Parsedown->text(parent::getBody());
			}
			else {
				$parser->parse(parent::getBody());
				return $parser->getAsHtml();
			}
		}
		else
			return parent::getBody();
	}

	public function setType($t) { $this->type = $t; }
	public function getType() { return $this->type; }

	public function seta($arr) {
		foreach ($arr as $k => $v) {
			if (strcmp($k, "id") == 0) { parent::setId($v); }
			if (strcmp($k, "body") == 0) { parent::setBody($v); }
			if (strcmp($k, "title") == 0) { $this->title = $v; }
			if (strcmp($k, "tag") == 0) { $this->tag = $v; }
			if (strcmp($k, "comp") == 0) { $this->comp = $v; }
			if (strcmp($k, "type") == 0) { $this->type = $v; }
		}
	}
	public function geta(&$arr) {
		$arr = [
			"id" => parent::getId(),
			"body" => parent::getBody(),
			"title" => $this->title,
			"tag" => $this->tag,
			"comp" => $this->comp,
			"type" => $this->type
		];
	}

	public function isMark() {
		if (strcmp($this->type, "md") == 0)
			return true;
		else
			return false;
	}

	public function write($q = "INSERT INTO `blog` VALUES(?, NOW(), ?, ?, ?, ?)") {
		global $TheBase;
		
		$st = $TheBase->Prepare($q);
		if (substr_count($q, "?") == 5) {
			if (!($st->bind_param("issss", $prep_id, $prep_tag, $prep_title, $prep_type, $prep_body))) {
				$st->close();
				throw new \TBcom\MySQLFailException();
			}
		}
		else if (substr_count($q, "?") == 6) {
			if (!($st->bind_param("isssss", $prep_id, $prep_comp, $prep_tag, $prep_title, $prep_type, $prep_body))) {
				$st->close();
				throw new \TBcom\MySQLFailException();
			}
			$prep_comp = $this->comp;
		}
		$prep_id = $this->getId();
		$prep_tag = $this->tag;
		$prep_title = $this->title;
		$prep_type = $this->type;
		$prep_body = $this->getBody(2);

		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$st->close();
	}

	public function read($id = 0) {
		global $TheBase;

		if ($id == 0) {
			throw new \TBcom\NotFoundException();
		}

		$st = $TheBase->Prepare("SELECT * FROM `blog` WHERE `id`=? LIMIT 1");
		if (!($st->bind_param("i", $prep_id))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$prep_id = $id;
		if (!($st->bind_result($s_id, $s_comp, $s_tag, $s_title, $s_type, $s_body))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		if (!($st->execute()) || !($st->fetch())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$this->setId($s_id);
		$this->comp = $s_comp;
		$this->tag = $s_tag;
		$this->title = $s_title;
		$this->type = $s_type;
		$this->setBody($s_body);
		$st->close();
		unset($st);
	}

	public function update() {
		global $TheBase;

		$st = $TheBase->Prepare("UPDATE `blog` SET `tag`=?, `title`=?, `format`=?, `body`=? WHERE `id`=? LIMIT 1");
		if (!($st->bind_param("ssssi", $prep_tag, $prep_title, $prep_format, $prep_body, $old_id))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$prep_tag = $this->tag;
		$prep_title = $this->title;
		$prep_format = $this->type;
		$prep_body = $this->getBody(2);
		$old_id = $this->getId();
		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$st->close();
		unset($st);
	}

	public static function echoRecent($admin = false, $sadmin = "") {
		global $TheBase;
		$ext = \TBcom\ext;

		if (isset($_GET['tag'])) {
			$st = $TheBase->Prepare("SELECT * FROM `blog` WHERE `tag`=? ORDER BY `id` DESC LIMIT 5" . ((isset($_GET['offset'])) ? (" OFFSET " . $_GET['offset']) : ""));
			if (!($st->bind_param("s", $old_tag))) {
				$st->close();
				throw new \TBcom\MySQLFailException();
			}
			$old_tag = $_GET['tag'];
		}
		else {
			$st = $TheBase->Prepare("SELECT * FROM `blog` ORDER BY `id` DESC LIMIT 5" . ((isset($_GET['offset'])) ? (" OFFSET " . $_GET['offset']) : ""));
		}

		$output = "";
		if (!($st->bind_result($s_id, $s_comp, $s_tag, $s_title, $s_type, $s_body)) || !($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$parser = new \JBBCode\Parser();
		$parser->addCodeDefinitionSet(new \JBBCode\DefaultCodeDefinitionSet());
		$Parsedown = new \Parsedown();

		while ($st->fetch()) {
			if (strcmp($s_type, "md") == 0) {
				$parsedBody = $Parsedown->text($s_body);
			}
			else if (strcmp($s_type, "bb") == 0) {
				$parser->parse($s_body);
				$parsedBody = $parser->getAsHtml();
			}
			
			$sAdminRow = (($admin) ?
					\TBcom\Tag("<span class=\"admin\"><p><a href=\"/admin/blog{{0}}?sett=editor&amp;mode={{1}}{{2}}\">Edit</a> &bull; <a href=\"/admin/blog{{0}}?sett=delete&amp;mode={{1}}{{2}}\">Delete</a> &bull; <a href=\"/admin/blog{{0}}?sett=table{{2}}\">Blog Table</a></p></span>", [
						$ext, $s_id, $sadmin
					])
				: "");

			if (strlen($parsedBody) > 1450)
				$trimmed = substr($parsedBody, 0, 1450) . "...</p><center><a href=\"blog{$ext}?id={$s_id}\" class=\"blogexpand\">Read the full blog post.</a></center>";
			else
				$trimmed = $parsedBody;
			
			$thedate = date("F j, Y", strtotime($s_comp));
		
			$output .= <<<EOF
		<blog-post id="{$s_id}" href="blog{$ext}?id={$s_id}{$sadmin}" taghref="blog{$ext}?tag={$s_tag}{$sadmin}" date="{$thedate}" tag="{$s_tag}" itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
			<template slot="title" itemprop="headline">{$s_title}</template>
			<template slot="admin">{$sAdminRow}</template>
			<template slot="body" itemprop="articleBody">{$trimmed}</template>
			<span itemprop="author" itemscope itemtype="http://schema.org/Person"><meta itemprop="name" content="Tanner Babcock" /></span>
			<span itemprop="publisher" itemscope itemtype="http://schema.org/Person"><meta itemprop="name" content="Tanner Babcock" /></span>
			<meta itemprop="image" content="http://tannerbabcock.com/ogimage.png" />
		</blog-post>
EOF;
		}
		$st->close();
		unset($st);
		unset($parser);
		unset($Parsedown);
		unset($trimmed);
		unset($thedate);
		unset($sAdminRow);
		return $output;
	}

	public static function archive() {
		global $TheBase;
		$ext = \TBcom\ext;

		$st = $TheBase->Prepare("SELECT * FROM `blog` ORDER BY `id` DESC");
		if (!($st->bind_result($s_id, $s_comp, $s_tag, $s_title, $s_type, $s_body)) || !($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}

		$output = <<<EOF
		<div class="content" id="archive">
			<table class="blog-archive">
				<thead>
					<tr>
						<td><b>Title</b></td>
						<td><b>Date</b></td>
						<td><b>Tag</b></td>
					</tr>
				</thead>
				<tbody>
EOF;
		while ($st->fetch()) {
			$thedate = date("F j, Y", strtotime($s_comp));
			$output .= <<<EOF
				<tr><td><a href="blog{$ext}?id={$s_id}" title="Permalink to the blog post" title="Permalink to the blog post">{$s_title}</a></td>
				<td>{$thedate}</td>
				<td><a href="blog{$ext}?tag={$s_tag}">{$s_tag}</a></td></tr>
EOF;
		}

		$output .= "\t</tbody></table>\n</div>\n";
		$st->close();
		unset($st);
		unset($thedate);
		return $output;
	}

	public static function navigate($i = 0, $sadmin = "\"") {
		global $TheBase;

		if ($i == 0) {
			throw new \TBcom\NotFoundException();
		}
		$ext = \TBcom\ext;
		$result = $TheBase->Query("SELECT * FROM `blog`");
		$total = $result->num_rows;

		$output = "\t<br /><div class=\"navigate\"><center><ul style=\"background-color:inherit;\">\n";

		if ($i < $total)
			$output .= "\t\t<li><a href=\"blog{$ext}?id=" . ($i + 1) . "{$sadmin} class=\"prev-link\" alt=\"See the blog entry that came after this one\" title=\"See the blog entry that came after this one\">&larr; Newer Post</a></li>\n";
		else
			$output .= "\t\t<li>&nbsp;</li>\n";
		$output .= "\t\t<li>&nbsp;</li>\n";
		if ($i > 1)
			$output .= "\t\t<li><a href=\"blog{$ext}?id=" . ($i - 1) . "{$sadmin} class=\"next-link\" alt=\"See the blog entry that came before this one\" title=\"See the blog entry that came before this one\">Older Post &rarr;</a></li>\n";
		else
			$output .= "\t\t<li>&nbsp;</li>\n";

		$output .= "\t</ul></center></div>\n";
		$output .= "\t<meta itemprop=\"description\" content=\"The written updates of Tanner Babcock.\" />\n";
		$output .= "</div>\n";
		$result->free();
		unset($result);
		unset($total);
		return $output;
	}

	public static function navigateList($offset = 0, $sadmin = "\"") {
		global $TheBase;
		$ext = \TBcom\ext;
		$result = $TheBase->Query("SELECT * FROM `blog`");
		$total = $result->num_rows;

		$output = "\t<br /><div class=\"navigate\"><center><ul style=\"background-color:inherit;\">\n";
		if ($offset > 0)
			$output .= "\t\t<li><a href=\"blog{$ext}" . ((($offset - 5) > 0) ? ("?offset=" . ($offset - 5)) : "") . "{$sadmin} class=\"prev-link\" alt=\"See more recent posts at a glance\" title=\"See more recent posts at a glance\">&larr; Newer Posts</a></li>\n";
		else
			$output .= "\t\t<li>&nbsp;</li>\n";
		$output .= "\t\t<li>&nbsp;</li>\n";
		if ($offset < $total)
			$output .= "\t\t<li><a href=\"blog{$ext}?offset=" . ($offset + 5) . "{$sadmin} class=\"next-link\" alt=\"See older posts at a glance\" title=\"See older posts at a glance\">Older Posts &rarr;</a></li>\n";
		else
			$output .= "\t\t<li>&nbsp;</li>\n";
		$output .= "\t</ul></center></div>\n";
		$result->free();
		unset($result);
		unset($total);
		return $output;
	}

	public static function table($token) {
		global $TheBase;
		$ext = \TBcom\ext;
		$output = "";

		$st = $TheBase->Prepare("SELECT * FROM `blog` ORDER BY `id`");
		if (!($st->bind_result($s_id, $s_comp, $s_tag, $s_title, $s_format, $s_body))) {
			$st->close();
			throw new \TBcom\MySQLFailException("bind_result() failed");
		}
		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException("execute() failed");
		}
		$Parsedown = new \Parsedown();

		while ($st->fetch()) {
			if (strcmp($s_format, "md") == 0) {
				$bodyPreview = str_replace("\n", " ", substr($Parsedown->text($s_body), 0, 97));
			}
			else if (strcmp($s_format, "bb") == 0) {
				$bodyPreview = str_replace("[/p]", "", str_replace("[p]", "", substr($s_body, 0, 96)));
			}

			$output .= <<<EOF
			<tr>
				<td>{$s_id}</td>
				<td>{$s_comp}</td>
				<td>{$s_tag}</td>
				<td><a href="/blog{$ext}?id={$s_id}&amp;tok={$token}">{$s_title}</a></td>
				<td style="font-size:0.91em;">{$bodyPreview}...</td>
				<td>
					<a href="blog{$ext}?sett=editor&amp;mode={$s_id}&amp;tok={$token}">Edit</a> |
					<a href="blog{$ext}?sett=delete&amp;mode={$s_id}&amp;tok={$token}">Delete</a>
				</td>
			</tr>
EOF;
		}

		$st->close();
		unset($st);
		unset($Parsedown);
		unset($bodyPreview);
		return $output;
	}

	public static function delete($i = 0) {
		global $TheBase;

		if ($i == 0)
			throw new \TBcom\MySQLFailException();
		$st = $TheBase->Prepare("DELETE FROM `blog` WHERE `id`=? LIMIT 1");
		if (!($st->bind_param("i", $old_id))) {
			$st->close();
			throw new \TBcom\MySQLFailException("bind_param() failed");
		}
		$old_id = intval($i);
		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException("execute() failed");
		}
		$st->close();

		$st = $TheBase->Prepare("UPDATE `blog` SET `id` = id - 1 WHERE id > ?");
		if (!($st->bind_param("i", $t))) {
			$st->close();
			throw new \TBcom\MySQLFailException("bind_param() failed");
		}
		$t = intval($i);
		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException("execute() failed");
		}
		$st->close();
		unset($st);
	}

	public static function writeTableComments() {
		global $TheBase;

		$result = $TheBase->Query("ALTER TABLE `blog` CHANGE `id` `id` INT(11) NULL DEFAULT NULL COMMENT 'The ID number for this blog post.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `blog` CHANGE `comp` `comp` DATE NULL DEFAULT NULL COMMENT 'The date the blog post was first published.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `blog` CHANGE `tag` `tag` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'A short tag for the blog post. Used for grouping and sorting.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `blog` CHANGE `title` `title` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'The title of the post.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `blog` CHANGE `format` `format` VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Text format: Markdown (md) or BBcode (bb).'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result = $TheBase->Query("ALTER TABLE `blog` CHANGE `body` `body` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Body text for the post.'");
		if (!$result) {
			throw new \TBcom\MySQLFailException("Query failed");
		}
		$result->free();
		unset($result);
	}
};

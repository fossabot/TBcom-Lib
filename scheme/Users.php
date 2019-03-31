<?php
/* TBcom-Lib Scheme
 * 
 *      \TBcom\Scheme
 *
 * Copyright (c) 2019 Tanner Babcock.
 * This software is licensed under the terms of the MIT License. See LICENSE for details.
*/
namespace TBcom\Scheme;

class User {
	/* TABLE: `users`
		______________________________
		|          id | int(11)      |  The ID of the user. User ID #1 should be admin.
		|-------------|--------------|
		|    username | varchar(30)  |  The user's username.
		|-------------|--------------|
		|    password | varchar(65)  |  64-digit SHA256 hash of the user's password.
		|-------------|--------------|
		|         pin | varchar(6)   |  Six-digit PIN for logging in.
		|-------------|--------------|
		|       email | varchar(50)  |  The user's email address.
		|-------------|--------------|
		|       admin | tinyint(1)   |  Whether the user is an admin or not.
		|_____________|______________|

	*/
	private $id;
	private $username;
	private $password;
	private $pin;
	private $email;
	private $admin;

	public function __construct() {
		$this->id = 0;
		$this->admin = 0;
	}

	public function __destruct() {
		$this->id = 0;
		$this->username = "";
		$this->password = "";
		$this->pin = "";
		$this->email = "";
		$this->admin = 0;
	}

	public function getId() { return $this->id; }
	public function setId($i = 0) { $this->id = $i; }
	public function getUsername() { return $this->username; }
	public function setUsername($u = "") { $this->username = $u; }
	public function getPassword() { return $this->password; }
	public function setPassword($p = "") { $this->password = $p; }
	public function getPin() { return $this->pin; }
	public function setPin($p = "") { $this->pin = $p; }
	public function getEmail() { return $this->email; }
	public function setEmail($e = "") { $this->email = $e; }
	public function isAdmin() { return $this->admin; }

	public function geta(&$arr) {
		$arr = [
			"id" => $this->id,
			"username" => $this->username,
			"password" => $this->password,
			"pin" => $this->pin,
			"email" => $this->email,
			"admin" => $this->admin
		];
	}
	public function seta($arr) {
		foreach ($arr as $k => $v) {
			if (strcmp($k, "id") == 0) { $this->id = $v; }
			if (strcmp($k, "username") == 0) { $this->username = $v; }
			if (strcmp($k, "password") == 0) { $this->password = $v; }
			if (strcmp($k, "pin") == 0) { $this->pin = $v; }
			if (strcmp($k, "email") == 0) { $this->email = $v; }
			if (strcmp($k, "admin") == 0) { $this->admin = $v; }
		}
	}

	public function read($i = 0) {
		global $TheBase;

		$st = $TheBase->Prepare("SELECT * FROM `users` WHERE `id`=?");
		if (!($st->bind_param("i", $old_id))) {
			$st->close();
			throw new \TBcom\MySQLFailException("bind_param() failed");
		}
		$old_id = intval($i);
		if (!($st->bind_result($s_id, $s_username, $s_password, $s_pin, $s_email, $s_admin))) {
			$st->close();
			throw new \TBcom\MySQLFailException("bind_result() failed");
		}
		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException("execute() failed");
		}
		$st->fetch();
		$this->id = $s_id;
		$this->username = $s_username;
		$this->password = $s_password;
		$this->pin = $s_pin;
		$this->email = $s_email;
		$this->admin = $s_admin;
		$st->close();
		unset($st);
	}

	public function write() {
		global $TheBase;

		$st = $TheBase->Prepare("INSERT INTO `users` VALUES (?, ?, ?, ?, ?, ?)");
		if (!($st->bind_param("issssi", $prep_id, $prep_uname, $prep_psw, $prep_pin, $prep_email, $prep_admin))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$prep_id = $this->id;
		$prep_uname = $this->username;
		$prep_psw = $this->password;
		$prep_pin = $this->pin;
		$prep_email = $this->email;
		$prep_admin = $this->admin;

		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$st->close();
		unset($st);
	}

	public function update($i = 0) {
		global $TheBase;

		$st = $TheBase->Prepare("UPDATE `users` SET `username`=?, `password`=?, `pin`=?, `email`=?, `admin`=? WHERE `id`=?");

		if (!($st->bind_param("sssssi", $prep_username, $prep_password, $prep_pin, $prep_email, $prep_admin, $prep_id))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$prep_username = $this->username;
		$prep_password = $this->password;
		$prep_pin = $this->pin;
		$prep_email = $this->email;
		$prep_admin = $this->admin;
		$prep_id = intval($i);

		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$st->close();
		unset($st);
	}

	public function readUsername($u = "") {
		global $TheBase;

		$st = $TheBase->Prepare("SELECT * FROM `users` WHERE `username`=?");
		if (!($st->bind_param("s", $prep_username))) {
			$st->close();
			throw new \TBcom\MySQLFailException();
		}
		$prep_username = $u;
		if (!($st->bind_result($s_id, $s_username, $s_password, $s_pin, $s_email, $s_admin))) {
			$st->close();
			throw new \TBcom\MySQLFailException("bind_result() failed");
		}
		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException("execute() failed");
		}
		$st->fetch();
		$this->id = $s_id;
		$this->username = $s_username;
		$this->password = $s_password;
		$this->pin = $s_pin;
		$this->email = $s_email;
		$this->admin = $s_admin;
		$st->close();
		unset($st);
	}

	public static function table($token = "") {
		global $TheBase;
		$ext = \TBcom\ext;

		$output = "";
		$st = $TheBase->Prepare("SELECT * FROM `users` ORDER BY `id`");
		if (!($st->bind_result($s_id, $s_user, $s_pass, $s_pin, $s_email, $s_admin))) {
			$st->close();
			throw new \TBcom\MySQLFailException("bind_result() failed");
		}
		if (!($st->execute())) {
			$st->close();
			throw new \TBcom\MySQLFailException("execute() failed");
		}

		while ($st->fetch()) {
			$sub_pass = substr($s_pass, 0, 20);
			$isadmin = (($s_admin) ? "<b>Yes</b>" : "No");
			$output .= <<<EOF
				<tr>
					<td>{$s_id}</td>
					<td>{$s_user}</td>
					<td class="cc">{$sub_pass}</td>
					<td style="font-size:1.07em"><span class="cc">{$s_pin}</span></td>
					<td><a href="mailto:{$s_email}">{$s_email}</a></td>
					<td>{$isadmin}</td>
					<td><a href="users{$ext}?sett=editor&amp;mode={$s_id}&amp;tok={$token}">Edit</a> | <a href="users{$ext}?sett=delete&amp;mode={$s_id}&amp;tok={$token}">Delete</a></td>
				</tr>
EOF;
		}

		$st->close();
		unset($st);
		return $output;
	}

	public static function delete($i = 0) {
		global $TheBase;

		$st = $TheBase->Prepare("DELETE FROM `users` WHERE `id`=? LIMIT 1");
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

		$st = $TheBase->Prepare("UPDATE `users` SET `id` = id - 1 WHERE id > ?");
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

};

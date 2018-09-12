<?php
namespace MyApp\Interfaces;
Interface iDatabase {
	public function __construct($db_host, $db_user, $db_pass, $db_name);
	public function connect();
	public function query($query);
	public function statement($query);
	public function bind($types, ...$params);
	public function execute();
	public function fetch(...$params);
	public function close();
	public function end();
}
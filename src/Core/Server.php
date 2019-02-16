<?php

namespace TournamentMaker\Core;


class Server
{
	private $get = [];
	private $post = [];
	
	
	public function __construct()
	{
		$this->loadGet();
		$this->loadPost();
	}
	
	public function getParameter($name, $default = null)
	{
		$result = $this->get[$name] ?? $default;
		if ($result === $default)
			return $this->post[$name] ?? $default;
	}
	
	private function loadGet()
	{
		$this->get = $_GET ?? [];
	}
	
	private function loadPost()
	{
		$this->post = $_POST ?? [];
	}

}
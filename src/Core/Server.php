<?php

namespace TournamentMaker\Core;


class Server
{
	private $input = [];
	private $get = [];
	private $post = [];
	
	
	public function __construct()
	{
		$this->loadInput();
		$this->loadGet();
		$this->loadPost();
	}
	
	public function getParameter($name, $default = null)
	{
		$result = $this->input[$name] ?? $default;
		if ($result === $default)
			$result = $this->get[$name] ?? $default;
		if ($result === $default)
			$result = $this->post[$name] ?? $default;
		return $result;
	}
	
	private function loadGet()
	{
		$this->get = $_GET ?? [];
	}
	
	private function loadPost()
	{
		$this->post = $_POST ?? [];
	}

	private function loadInput()
	{
		$this->input = json_decode(file_get_contents("php://input"), true);
	}

}
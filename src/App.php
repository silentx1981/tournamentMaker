<?php

namespace TournamentMaker;

use TournamentMaker\Core\Server;
use TournamentMaker\Maker\Maker;

class App
{
	public function run()
	{
		$server = new Server();
		if ($server->getParameter('create') === null) {
			$html = file_get_contents('../templates/markup.html');
			echo $html;
		} else {
			$maker = new Maker();
			$maker->make($server);
		}
	}
}



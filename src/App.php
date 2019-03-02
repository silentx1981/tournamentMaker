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
			$html = $this->templateReplaceData($html);
			echo $html;
		} else {
			$maker = new Maker();
			$maker->make($server);
		}
	}

	private function templateReplaceData($html)
	{
		$hostConfig = json_decode(file_get_contents('../config/host.local'), true);
		$hostConfig['basePath'] = $hostConfig['basePath'] ?? '/';
		$hostConfig['name'] = $hostConfig['name'] ?? 'TournamentMaker';
		$version = file_get_contents('../version');
		$html = str_replace('{basePath}', $hostConfig['basePath'], $html);
		$html = str_replace('{name}', $hostConfig['name'], $html);
		$html = str_replace('{version}', $version, $html);
		$html = str_replace('{imageLogo}', $hostConfig['images']['logo'] ?? null, $html);
		return $html;
	}
}



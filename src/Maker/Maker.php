<?php
/**
 * Description
 *
 * @author   silentx
 * @since    1.0.0
 * @date     09.02.19
 */

namespace TournamentMaker\Maker;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TournamentMaker\Core\Exception;
use TournamentMaker\Core\Server;

class Maker
{
	/** @var null|Spreadsheet */
	private $spreadsheet = null;
	
	public function make(Server $server)
	{
		$teams1 = $this->getTeams($server, 'teams');
		$teams2 = $this->getTeams($server, 'teams2');
		$teams3 = $this->getTeams($server, 'teams3');
		$teams4 = $this->getTeams($server, 'teams4');
		$teams = [$teams1, $teams2, $teams3, $teams3];

		$this->spreadsheet = new Spreadsheet();
		$this->spreadsheet->removeSheetByIndex(0);
		$pageConfig = new PageConfig($server, $teams1);
		$this->spreadsheet = $pageConfig->setData($this->spreadsheet);
		$pageTeams = new PageTeams($server, $teams1);
		$this->spreadsheet = $pageTeams->setData($this->spreadsheet);
		$pageGames = new PageGames($server, $teams1);
		$this->spreadsheet = $pageGames->setData($this->spreadsheet);
		$pageReferee = new PageReferee($server, $teams);
		$this->spreadsheet = $pageReferee->setData($this->spreadsheet, $pageGames->getGames());
		$pageResults = new PageResults($server, $teams);
		$this->spreadsheet = $pageResults->setData($this->spreadsheet, $pageGames->getPlan());
		$pageKo = new PageKo($server, $teams1);
		$this->spreadsheet = $pageKo->setData($this->spreadsheet);
		
		$this->download();
		
	}
	
	private function download()
	{
		if ($this->spreadsheet === null)
			return;
		
		/** @var Xlsx $writer */
		$writer = new Xlsx($this->spreadsheet);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="tournamentMaker.xlsx"');
		$writer->save("php://output");
	}

	private function getTeams(Server $server, $name)
	{
		$teams = $server->getParameter($name);

		$result = [];
		foreach ($teams as $team)
			if (trim($team['name'] ?? '') !== '')
				$result[] = $team['name'];

		return $result;
	}
}
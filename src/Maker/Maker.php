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
		$pteams = $server->getParameter('teams');
		$teams = [];
		foreach ($pteams AS $team)
			if (trim($team['name'] ?? '') !== "")
				$teams[] = $team['name'];

		$this->spreadsheet = new Spreadsheet();
		$this->spreadsheet->removeSheetByIndex(0);
		$pageConfig = new PageConfig($server, $teams);
		$this->spreadsheet = $pageConfig->setData($this->spreadsheet);
		$pageTeams = new PageTeams($server, $teams);
		$this->spreadsheet = $pageTeams->setData($this->spreadsheet);
		$pageGames = new PageGames($server, $teams);
		$this->spreadsheet = $pageGames->setData($this->spreadsheet);
		$pageReferee = new PageReferee($server, $teams);
		$this->spreadsheet = $pageReferee->setData($this->spreadsheet, $pageGames->getGames());
		$pageResults = new PageResults($server, $teams);
		$this->spreadsheet = $pageResults->setData($this->spreadsheet, $pageGames->getPlan());
		$pageKo = new PageKo($server, $teams);
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
}
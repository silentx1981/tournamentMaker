<?php

namespace TournamentMaker\Maker;


use GamePlanner\Planner;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use TournamentMaker\Helper\ExcelHelper;
use DateInterval;
use DateTime;
use DateTimeZone;

class PageGames
extends ExcelHelper
{
	private $plan = [
		"vorrunde" => [],
		"rueckrunde" => [],
	];

	private $games = [];


	public function setData(Spreadsheet $spreadsheet)
	{
		$this->spreadsheet = $spreadsheet;
		
		$sheet = new Worksheet(null, 'Spiele');
		$this->spreadsheet->addSheet($sheet, 2);
		$this->spreadsheet->setActiveSheetIndex(2);
		$this->setup();
		$this->spreadsheet->getActiveSheet()->getColumnDimension("A")->setWidth(6);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("B")->setWidth(6);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("C")->setWidth(6);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("D")->setWidth(21);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("E")->setWidth(3);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("F")->setWidth(21);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("G")->setWidth(3);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("H")->setWidth(3);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("I")->setWidth(3);
		$teams = $this->server->getParameter('team', []);
		foreach ($teams as $team) {
			if (!isset($this->plan['vorrunde'][$team]))
				$this->plan['vorrunde'][$team] = ['D' => [], 'F' => []];
			if (!isset($this->plan['rueckrunde'][$team]))
				$this->plan['rueckrunde'][$team] = ['D' => [], 'F' => []];
		}
		$duration = $this->server->getParameter('duration');
		$pause = $this->server->getParameter('pause');
		$prefirsttime = $this->server->getParameter('prefirsttime');
		$pft = new DateTime($prefirsttime);
		$pft->setTimezone(new DateTimeZone('Europe/Zurich'));
		$backfirsttime = $this->server->getParameter('backfirsttime');
		$bft = new DateTime($backfirsttime);
		$bft->setTimezone(new DateTimeZone('Europe/Zurich'));
		
		// Turniername anzeigen
		$row = 2;
		$this->setCellValue("A$row", "=Konfiguration!B3", Config::getPageHeaderStyle());
		
		// Vorrunde
		$row = $row + 2;
		$this->setCellValue("A$row:I$row", 'Vorrunde', Config::getHeaderStyle());
		$games = $this->createGames();
		foreach ($games['planteams'] AS $nr => $game) {
			$row++;
			$this->setCellValue("A$row", $nr + 1, Config::getDefaultStyle());
			$this->setCellValue("B$row", $pft->format('H:i'), Config::getDefaultStyle(['align' => 'center']));
			$this->setCellValue("C$row", 'A', Config::getDefaultStyle());
			$this->setCellValue("D$row", $game[1], Config::getDefaultStyle());
			$this->plan['vorrunde'][$game[1]]['D'][$row] = $game[2];
			$this->setCellValue("E$row", '-', Config::getDefaultStyle(['align' => 'center']));
			$this->setCellValue("F$row", $game[2], Config::getDefaultStyle());
			$this->plan['vorrunde'][$game[2]]['F'][$row] = $game[1];
			$this->setCellValue("G$row", "", Config::getInputStyle(['align' => 'center']));
			$this->setCellValue("H$row", ":", Config::getDefaultStyle(['align' => 'center']));
			$this->setCellValue("I$row", "", Config::getInputStyle(['align' => 'center']));
			$di = new DateInterval('PT'.($duration + $pause).'M');
			$pft->add($di);
		}

		// Rückrunde
		$row = $row + 2;
		$this->setCellValue("A$row:I$row", 'Rückrunde', Config::getHeaderStyle());
		$games = $this->getGames(true);
		foreach ($games['planteams'] AS $nr => $game) {
			$row++;
			$this->setCellValue("A$row", count($games) + $nr + 2, Config::getDefaultStyle());
			$this->setCellValue("B$row", $bft->format('H:i'), Config::getDefaultStyle(['align' => 'center']));
			$this->setCellValue("C$row", 'A', Config::getDefaultStyle());
			$this->setCellValue("D$row", $game[1], Config::getDefaultStyle());
			$this->plan['rueckrunde'][$game[1]]['D'][$row] = $game[2];
			$this->setCellValue("E$row", '-', Config::getDefaultStyle(['align' => 'center']));
			$this->setCellValue("F$row", $game[2], Config::getDefaultStyle());
			$this->plan['rueckrunde'][$game[2]]['F'][$row] = $game[1];
			$this->setCellValue("G$row", "", Config::getInputStyle(['align' => 'center']));
			$this->setCellValue("H$row", ":", Config::getDefaultStyle(['align' => 'center']));
			$this->setCellValue("I$row", "", Config::getInputStyle(['align' => 'center']));
			$di = new DateInterval('PT'.($duration + $pause).'M');
			$bft->add($di);
		}
		
		return $this->spreadsheet;
	}

	public function getPlan()
	{
		return $this->plan;
	}

	public function getGames()
	{
		return $this->games;
	}
	
	private function createGames($rueckrunde = false)
	{
		$teams = $this->teams;

		$planner = new Planner();
		$spielplan = $planner->generate(count($teams), $rueckrunde);

		$planTeamname = [];
		foreach ($spielplan AS $spiel) {
			$planTeamname[] = [
				1 => $teams[$spiel[1]],
				2 => $teams[$spiel[2]],
			];
		}

		$result = [
			"plan" => $spielplan,
			"planteams" => $planTeamname,
		];
		$this->games = $result;

		return $result;
	}
	
}
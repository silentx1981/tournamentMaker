<?php

namespace TournamentMaker\Maker;


use GamePlanner\Planner;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use TournamentMaker\Helper\ExcelHelper;

class PageGames
extends ExcelHelper
{
	private $plan = [
		"vorrunde" => [],
		"rueckrunde" => [],
	];


	public function setData(Spreadsheet $spreadsheet)
	{
		$this->spreadsheet = $spreadsheet;
		
		$sheet = new Worksheet(null, 'Spiele');
		$this->spreadsheet->addSheet($sheet, 2);
		$this->spreadsheet->setActiveSheetIndex(2);
		$this->setup();
		$this->spreadsheet->getActiveSheet()->getColumnDimension("A")->setWidth(6);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("B")->setWidth(21);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("C")->setWidth(3);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("D")->setWidth(21);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("E")->setWidth(3);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("F")->setWidth(3);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("G")->setWidth(3);
		$teams = $this->server->getParameter('team', []);
		foreach ($teams as $team) {
			if (!isset($this->plan['vorrunde'][$team]))
				$this->plan['vorrunde'][$team] = ['B' => [], 'D' => []];
			if (!isset($this->plan['rueckrunde'][$team]))
				$this->plan['rueckrunde'][$team] = ['B' => [], 'D' => []];
		}
		
		// Turniername anzeigen
		$row = 2;
		$this->setCellValue("A$row", "=Konfiguration!B3", Config::getPageHeaderStyle());
		
		// Vorrunde
		$row = $row + 2;
		$this->setCellValue("A$row:G$row", 'Vorrunde', Config::getHeaderStyle());
		$games = $this->getGames();
		foreach ($games['planteams'] AS $nr => $game) {
			$row++;
			$this->setCellValue("A$row", $nr + 1, Config::getDefaultStyle());
			$this->setCellValue("B$row", $game[1], Config::getDefaultStyle());
			$this->plan['vorrunde'][$game[1]]['B'][$row] = $game[2];
			$this->setCellValue("C$row", '-', Config::getDefaultStyle(['align' => 'center']));
			$this->setCellValue("D$row", $game[2], Config::getDefaultStyle());
			$this->plan['vorrunde'][$game[2]]['D'][$row] = $game[1];
			$this->setCellValue("E$row", "", Config::getInputStyle(['align' => 'center']));
			$this->setCellValue("F$row", ":", Config::getDefaultStyle(['align' => 'center']));
			$this->setCellValue("G$row", "", Config::getInputStyle(['align' => 'center']));
		}

		// Rückrunde
		$row = $row + 2;
		$this->setCellValue("A$row:G$row", 'Rückrunde', Config::getHeaderStyle());
		$games = $this->getGames(true);
		foreach ($games['planteams'] AS $nr => $game) {
			$row++;
			$this->setCellValue("A$row", count($games) + $nr + 2, Config::getDefaultStyle());
			$this->setCellValue("B$row", $game[1], Config::getDefaultStyle());
			$this->plan['rueckrunde'][$game[1]]['B'][$row] = $game[2];
			$this->setCellValue("C$row", '-', Config::getDefaultStyle(['align' => 'center']));
			$this->setCellValue("D$row", $game[2], Config::getDefaultStyle());
			$this->plan['rueckrunde'][$game[2]]['D'][$row] = $game[1];
			$this->setCellValue("E$row", "", Config::getInputStyle(['align' => 'center']));
			$this->setCellValue("F$row", ":", Config::getDefaultStyle(['align' => 'center']));
			$this->setCellValue("G$row", "", Config::getInputStyle(['align' => 'center']));
		}
		
		return $this->spreadsheet;
	}

	public function getPlan()
	{
		return $this->plan;
	}
	
	private function getGames($rueckrunde = false)
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

		return $result;
	}
	
}
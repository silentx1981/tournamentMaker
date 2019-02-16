<?php

namespace TournamentMaker\Maker;


use GamePlanner\Planner;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use TournamentMaker\Helper\ExcelHelper;

class PageGames
extends ExcelHelper
{
	
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
			$this->setCellValue("C$row", '-', Config::getDefaultStyle());
			$this->setCellValue("D$row", $game[2], Config::getDefaultStyle());
			$this->setCellValue("E$row", "", Config::getInputStyle());
			$this->setCellValue("F$row", ":", Config::getDefaultStyle());
			$this->setCellValue("G$row", "", Config::getInputStyle());
		}

		// Rückrunde
		$row = $row + 2;
		$this->setCellValue("A$row:G$row", 'Rückrunde', Config::getHeaderStyle());
		$games = $this->getGames(true);
		foreach ($games['planteams'] AS $nr => $game) {
			$row++;
			$this->setCellValue("A$row", count($games) + $nr + 2, Config::getDefaultStyle());
			$this->setCellValue("B$row", $game[1], Config::getDefaultStyle());
			$this->setCellValue("C$row", '-', Config::getDefaultStyle());
			$this->setCellValue("D$row", $game[2], Config::getDefaultStyle());
			$this->setCellValue("E$row", "", Config::getInputStyle());
			$this->setCellValue("F$row", ":", Config::getDefaultStyle());
			$this->setCellValue("G$row", "", Config::getInputStyle());
		}
		
		return $this->spreadsheet;
	}
	
	private function getGames($rueckrunde = false)
	{
		$teams = $this->server->getParameter('team', []);

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
<?php

namespace TournamentMaker\Maker;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use TournamentMaker\Core\Helper;
use TournamentMaker\Helper\ExcelHelper;

class PageConfig
extends ExcelHelper
{
	
	public function setData(Spreadsheet $spreadsheet)
	{
		$this->spreadsheet = $spreadsheet;
		$this->setMetaData();
		
		$sheet = new Worksheet(null, 'Konfiguration');
		$this->spreadsheet->addSheet($sheet, 0);
		$this->spreadsheet->setActiveSheetIndex(0);
		$this->setup();
		$this->spreadsheet->getActiveSheet()->getColumnDimension("A")->setWidth(15);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("B")->setWidth(15);
		
		// Allgemein
		$row = 2;
		$this->setCellValue("A$row:B$row", 'Allgemein', Config::getHeaderStyle());
		$row++;
		$this->setCellValue("A$row", 'Turniername', Config::getDefaultStyle());
		$this->setCellValue("B$row", $this->server->getParameter('tournamentname'), Config::getInputStyle());
		
		
		// Mannschaften
		$row = $row + 2;
		$this->setCellValue("A$row:B$row", "Mannschaften", Config::getHeaderStyle());
		$teams = $this->teams;
		$counter = 1;
		foreach ($teams AS $team) {
			$row++;
			$this->setCellValue("A$row", "Team$counter", Config::getDefaultStyle());
			$this->setCellValue("B$row", $team, Config::getInputStyle());
			$counter++;
		}
		
		return $this->spreadsheet;
	}

	private function setMetaData()
	{
		$version = Helper::getVersion();
		$this->spreadsheet->getProperties()->setCreator('silentx81/tournamentmaker');
		$this->spreadsheet->getProperties()->setTitle('Tournamentmaker '.$version);
		$this->spreadsheet->getProperties()->setSubject('Tournamentmaker '.$version);
		$this->spreadsheet->getProperties()->setDescription("Tournamentmaker $version \nhttps://github.com/silentx1981/tournamentMaker");
		$this->spreadsheet->getProperties()->setKeywords('Tournamentmaker Gameplanner silentx81 raffaelwyss');
	}
}
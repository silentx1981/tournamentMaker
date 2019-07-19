<?php

namespace TournamentMaker\Maker;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use TournamentMaker\Helper\ExcelHelper;

class PageTeams
extends ExcelHelper
{
	
	public function setData(Spreadsheet $spreadsheet)
	{
		$this->spreadsheet = $spreadsheet;
		
		$sheet = new Worksheet(null, 'Teams');
		$this->spreadsheet->addSheet($sheet, 1);
		$this->spreadsheet->setActiveSheetIndex(1);
		$this->setup();
		
		// Turniername anzeigen
		$row = 2;
		$this->setCellValue("A$row", "=Konfiguration!B3", Config::getPageHeaderStyle());
		
		$row = $row + 2;
		$counter = 0;
		$teams = $this->teams;
		foreach ($teams AS $team) {
			$columnName = Config::columnNames[$counter] ?? 'A';
			$teamCoordinate = "=Konfiguration!B".(6+$counter);
			$this->spreadsheet->getActiveSheet()->getColumnDimension($columnName)->setWidth(40);
			$this->setCellValue("$columnName$row", $teamCoordinate, Config::getHeaderStyle());
			$counter++;
			for($i = $row + 1; $i < $row + 16; $i++)
				$this->setCellValue("$columnName$i", "", Config::getInputStyle());
		}
		
		return $this->spreadsheet;
	}
}
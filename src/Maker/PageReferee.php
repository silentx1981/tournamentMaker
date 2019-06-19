<?php

namespace TournamentMaker\Maker;


use GamePlanner\Planner;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use TournamentMaker\Helper\ExcelHelper;
use DateInterval;
use DateTime;
use DateTimeZone;

class PageReferee
extends ExcelHelper
{

	public function setData(Spreadsheet $spreadsheet, $games)
	{
		$this->spreadsheet = $spreadsheet;
		
		$sheet = new Worksheet(null, 'Schiedsrichter');
		$this->spreadsheet->addSheet($sheet, 3);
		$this->spreadsheet->setActiveSheetIndex(3);
		$this->setup();
		$this->spreadsheet->getActiveSheet()->getColumnDimension("A")->setWidth(6);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("B")->setWidth(6);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("C")->setWidth(21);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("D")->setWidth(21);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("E")->setWidth(10);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("F")->setWidth(6);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("G")->setWidth(6);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("H")->setWidth(21);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("I")->setWidth(21);
		$register = 'Spiele';

		// Vorrunde
		$row = 2;
		$leftright = 'left';
		$rowG = 5;
		foreach ($games['planteams'] as $game) {
			if ($leftright === 'left') {
				$columnA = 'A';
				$columnB = 'B';
				$columnC = 'C';
				$columnD = 'D';
 			} else {
				$columnA = 'F';
				$columnB = 'G';
				$columnC = 'H';
				$columnD = 'I';
			}
			$this->setCellValue("$columnA$row:$columnC$row", 'Spiel', Config::getHeaderStyle());
			$this->setCellValue("$columnD$row", "=$register!A$rowG", Config::getHeaderStyle());
			$row++;
			$this->setCellValue("$columnA$row", 'Zeit', Config::getDefaultStyle(['bold' => true, 'bgcolor' => 'eeeeee']));
			$this->setCellValue("$columnB$row", 'Platz', Config::getDefaultStyle(['bold' => true, 'bgcolor' => 'eeeeee']));
			$this->setCellValue("$columnC$row", 'Team 1', Config::getDefaultStyle(['bold' => true, 'bgcolor' => 'eeeeee']));
			$this->setCellValue("$columnD$row", 'Team 2', Config::getDefaultStyle(['bold' => true, 'bgcolor' => 'eeeeee']));
			$row++;
			$this->setCellValue("$columnA$row", "=$register!B$rowG", Config::getDefaultStyle());
			$this->setCellValue("$columnB$row", "=$register!C$rowG", Config::getDefaultStyle());
			$this->setCellValue("$columnC$row", "=$register!D$rowG", Config::getDefaultStyle());
			$this->setCellValue("$columnD$row", "=$register!F$rowG", Config::getDefaultStyle());
			$row++;
			$this->spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(50);
			$this->setCellValue("$columnC$row", "", Config::getDefaultStyle());
			$this->setCellValue("$columnD$row", "", Config::getDefaultStyle());

			if ($leftright === 'left') {
				$leftright = 'right';
				$row = $row - 3;
			} else {
				$leftright = 'left';
				$row = $row + 2;
			}
			$rowG++;
		}
		if ($leftright === 'right')
			$row = $row + 3;

		// Rückrunde
		$row = $row + 2;
		$leftright = 'left';
		$rowG = $rowG + 2;
		foreach ($games['planteams'] as $game) {
			if ($leftright === 'left') {
				$columnA = 'A';
				$columnB = 'B';
				$columnC = 'C';
				$columnD = 'D';
			} else {
				$columnA = 'F';
				$columnB = 'G';
				$columnC = 'H';
				$columnD = 'I';
			}
			$this->setCellValue("$columnA$row:$columnC$row", 'Spiel', Config::getHeaderStyle());
			$this->setCellValue("$columnD$row", "=$register!A$rowG", Config::getHeaderStyle());
			$row++;
			$this->setCellValue("$columnA$row", 'Zeit', Config::getDefaultStyle(['bold' => true, 'bgcolor' => 'eeeeee']));
			$this->setCellValue("$columnB$row", 'Platz', Config::getDefaultStyle(['bold' => true, 'bgcolor' => 'eeeeee']));
			$this->setCellValue("$columnC$row", 'Team 1', Config::getDefaultStyle(['bold' => true, 'bgcolor' => 'eeeeee']));
			$this->setCellValue("$columnD$row", 'Team 2', Config::getDefaultStyle(['bold' => true, 'bgcolor' => 'eeeeee']));
			$row++;
			$this->setCellValue("$columnA$row", "=$register!B$rowG", Config::getDefaultStyle());
			$this->setCellValue("$columnB$row", "=$register!C$rowG", Config::getDefaultStyle());
			$this->setCellValue("$columnC$row", "=$register!D$rowG", Config::getDefaultStyle());
			$this->setCellValue("$columnD$row", "=$register!F$rowG", Config::getDefaultStyle());
			$row++;
			$this->spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(50);
			$this->setCellValue("$columnC$row", "", Config::getDefaultStyle());
			$this->setCellValue("$columnD$row", "", Config::getDefaultStyle());

			if ($leftright === 'left') {
				$leftright = 'right';
				$row = $row - 3;
			} else {
				$leftright = 'left';
				$row = $row + 2;
			}
			$rowG++;
		}

		
		return $this->spreadsheet;
	}

}
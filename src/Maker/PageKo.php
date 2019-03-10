<?php

namespace TournamentMaker\Maker;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use TournamentMaker\Helper\ExcelHelper;
use DateTime;
use DateTimeZone;
use DateInterval;

class PageKo
extends ExcelHelper
{


	public function setData(Spreadsheet $spreadsheet)
	{
		$this->spreadsheet = $spreadsheet;
		
		$sheet = new Worksheet(null, 'KO-Phase');
		$this->spreadsheet->addSheet($sheet, 4);
		$this->spreadsheet->setActiveSheetIndex(4);
		$this->setup();
		$this->spreadsheet->getActiveSheet()->getColumnDimension("A")->setWidth(6);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("B")->setWidth(6);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("C")->setWidth(21);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("D")->setWidth(3);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("E")->setWidth(21);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("F")->setWidth(3);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("G")->setWidth(3);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("H")->setWidth(3);
		$teams = $this->server->getParameter('teams', []);
		$finalpairs = $this->server->getParameter('finalpairs', 0);
		$resultRow = 11 + (count($teams) * 2);
		$column = $this->getColumnByInt(14 + count($teams));

		$rangformeln = [];
		foreach ($teams as $key => $team) {
			$teamkey = $key + 1;
			$startRow = $resultRow + 1;
			$endRow = $startRow + count($teams);
			$rangformeln[$teamkey] = '=INDIRECT("Resultate!A"&MATCH('.$teamkey.', Resultate!'.$column.$startRow.':'.$column.$endRow.', 0) + '.$resultRow.' )';
		}

		// Turniername anzeigen
		$row = 2;
		$this->setCellValue("A$row", "=Konfiguration!B3", Config::getPageHeaderStyle());

		$row = $row + 2;
		if ($finalpairs === 2)
			$this->setQuarterFinal($rangformeln, $row);
		else if ($finalpairs === 1)
			$this->setSemiFinal($rangformeln, $row);
		else
			$this->setFinal($rangformeln, $row);
		
		return $this->spreadsheet;
	}

	private function setFinal($formeln, $row)
	{
		$prefirsttimeko = $this->server->getParameter('prefirsttimeko');
		$pftko = new DateTime($prefirsttimeko);
		$pftko->setTimezone(new DateTimeZone('Europe/Zurich'));

		$this->setCellValue("A$row:H$row", "Final", config::getHeaderStyle());
		$row++;
		$this->setCellValue("A$row", "1", config::getDefaultStyle());
		$this->setCellValue("B$row", $pftko->format('H:i'), config::getDefaultStyle());
		$this->setCellValue("C$row", $formeln[1], config::getDefaultStyle());
		$this->setCellValue("D$row", '-', Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("E$row", $formeln[2], config::getDefaultStyle());
		$this->setCellValue("F$row", "", Config::getInputStyle(['align' => 'center']));
		$this->setCellValue("G$row", ":", Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("H$row", "", Config::getInputStyle(['align' => 'center']));

	}

	private function setSemiFinal($formeln, $row)
	{
		$duration = $this->server->getParameter('durationko');
		$pause = $this->server->getParameter('pauseko');
		$prefirsttimeko = $this->server->getParameter('prefirsttimeko');
		$pftko = new DateTime($prefirsttimeko);
		$pftko->setTimezone(new DateTimeZone('Europe/Zurich'));
		$di = new DateInterval('PT'.($duration + $pause).'M');

		$this->setCellValue("A$row:H$row", "Halbfinale", config::getHeaderStyle());
		$row++;
		$rowGame1 = $row;
		$this->setCellValue("A$row", "1", config::getDefaultStyle());
		$this->setCellValue("B$row", $pftko->format('H:i'), config::getDefaultStyle());
		$this->setCellValue("C$row", $formeln[1], config::getDefaultStyle());
		$this->setCellValue("D$row", '-', Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("E$row", $formeln[4], config::getDefaultStyle());
		$this->setCellValue("F$row", "", Config::getInputStyle(['align' => 'center']));
		$this->setCellValue("G$row", ":", Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("H$row", "", Config::getInputStyle(['align' => 'center']));
		$row++;
		$pftko->add($di);
		$rowGame2 = $row;
		$this->setCellValue("A$row", "2", config::getDefaultStyle());
		$this->setCellValue("B$row", $pftko->format('H:i'), config::getDefaultStyle());
		$this->setCellValue("C$row", $formeln[2], config::getDefaultStyle());
		$this->setCellValue("D$row", '-', Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("E$row", $formeln[3], config::getDefaultStyle());
		$this->setCellValue("F$row", "", Config::getInputStyle(['align' => 'center']));
		$this->setCellValue("G$row", ":", Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("H$row", "", Config::getInputStyle(['align' => 'center']));
		$pftko->add($di);

		$formeln = [];
		$formeln[1] = "=IF(AND(F$rowGame1=\"\", H$rowGame1=\"\"), \"\", IF(F$rowGame1>H$rowGame1, C$rowGame1, E$rowGame1))";
		$formeln[2] = "=IF(AND(F$rowGame2=\"\", H$rowGame2=\"\"), \"\", IF(F$rowGame2>H$rowGame2, C$rowGame2, E$rowGame2))";


		$row = $row + 2;
		$this->setCellValue("A$row:H$row", "Final", config::getHeaderStyle());
		$row++;
		$this->setCellValue("A$row", "3", config::getDefaultStyle());
		$this->setCellValue("B$row", $pftko->format('H:i'), config::getDefaultStyle());
		$this->setCellValue("C$row", $formeln[1], config::getDefaultStyle());
		$this->setCellValue("D$row", '-', Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("E$row", $formeln[2], config::getDefaultStyle());
		$this->setCellValue("F$row", "", Config::getInputStyle(['align' => 'center']));
		$this->setCellValue("G$row", ":", Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("H$row", "", Config::getInputStyle(['align' => 'center']));

	}

	private function setQuarterFinal($formeln, $row)
	{
		$duration = $this->server->getParameter('durationko');
		$pause = $this->server->getParameter('pauseko');
		$prefirsttimeko = $this->server->getParameter('prefirsttimeko');
		$pftko = new DateTime($prefirsttimeko);
		$pftko->setTimezone(new DateTimeZone('Europe/Zurich'));
		$di = new DateInterval('PT'.($duration + $pause).'M');

		$this->setCellValue("A$row:H$row", "Viertelfinale", config::getHeaderStyle());
		$row++;
		$rowGame1 = $row;
		$this->setCellValue("A$row", "1", config::getDefaultStyle());
		$this->setCellValue("B$row", $pftko->format('H:i'), config::getDefaultStyle());
		$this->setCellValue("C$row", $formeln[1], config::getDefaultStyle());
		$this->setCellValue("D$row", '-', Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("E$row", $formeln[8], config::getDefaultStyle());
		$this->setCellValue("F$row", "", Config::getInputStyle(['align' => 'center']));
		$this->setCellValue("G$row", ":", Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("H$row", "", Config::getInputStyle(['align' => 'center']));
		$pftko->add($di);
		$row++;
		$rowGame2 = $row;
		$this->setCellValue("A$row", "2", config::getDefaultStyle());
		$this->setCellValue("B$row", $pftko->format('H:i'), config::getDefaultStyle());
		$this->setCellValue("C$row", $formeln[2], config::getDefaultStyle());
		$this->setCellValue("D$row", '-', Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("E$row", $formeln[7], config::getDefaultStyle());
		$this->setCellValue("F$row", "", Config::getInputStyle(['align' => 'center']));
		$this->setCellValue("G$row", ":", Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("H$row", "", Config::getInputStyle(['align' => 'center']));
		$pftko->add($di);
		$row++;
		$rowGame3 = $row;
		$this->setCellValue("A$row", "3", config::getDefaultStyle());
		$this->setCellValue("B$row", $pftko->format('H:i'), config::getDefaultStyle());
		$this->setCellValue("C$row", $formeln[3], config::getDefaultStyle());
		$this->setCellValue("D$row", '-', Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("E$row", $formeln[6], config::getDefaultStyle());
		$this->setCellValue("F$row", "", Config::getInputStyle(['align' => 'center']));
		$this->setCellValue("G$row", ":", Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("H$row", "", Config::getInputStyle(['align' => 'center']));
		$pftko->add($di);
		$row++;
		$rowGame4 = $row;
		$this->setCellValue("A$row", "4", config::getDefaultStyle());
		$this->setCellValue("B$row", $pftko->format('H:i'), config::getDefaultStyle());
		$this->setCellValue("C$row", $formeln[4], config::getDefaultStyle());
		$this->setCellValue("D$row", '-', Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("E$row", $formeln[5], config::getDefaultStyle());
		$this->setCellValue("F$row", "", Config::getInputStyle(['align' => 'center']));
		$this->setCellValue("G$row", ":", Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("H$row", "", Config::getInputStyle(['align' => 'center']));
		$pftko->add($di);

		$formeln = [];
		$formeln[1] = "=IF(AND(F$rowGame1=\"\", H$rowGame1=\"\"), \"\", IF(F$rowGame1>H$rowGame1, C$rowGame1, E$rowGame1))";
		$formeln[2] = "=IF(AND(F$rowGame2=\"\", H$rowGame2=\"\"), \"\", IF(F$rowGame2>H$rowGame2, C$rowGame2, E$rowGame2))";
		$formeln[3] = "=IF(AND(F$rowGame3=\"\", H$rowGame3=\"\"), \"\", IF(F$rowGame3>H$rowGame3, C$rowGame3, E$rowGame3))";
		$formeln[4] = "=IF(AND(F$rowGame4=\"\", H$rowGame4=\"\"), \"\", IF(F$rowGame4>H$rowGame4, C$rowGame4, E$rowGame4))";

		$row = $row + 2;
		$this->setCellValue("A$row:H$row", "Halbfinal", config::getHeaderStyle());
		$row++;
		$rowGame5 = $row;
		$this->setCellValue("A$row", "5", config::getDefaultStyle());
		$this->setCellValue("B$row", $pftko->format('H:i'), config::getDefaultStyle());
		$this->setCellValue("C$row", $formeln[1], config::getDefaultStyle());
		$this->setCellValue("D$row", '-', Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("E$row", $formeln[2], config::getDefaultStyle());
		$this->setCellValue("F$row", "", Config::getInputStyle(['align' => 'center']));
		$this->setCellValue("G$row", ":", Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("H$row", "", Config::getInputStyle(['align' => 'center']));
		$pftko->add($di);
		$row++;
		$rowGame6 = $row;
		$this->setCellValue("A$row", "6", config::getDefaultStyle());
		$this->setCellValue("B$row", $pftko->format('H:i'), config::getDefaultStyle());
		$this->setCellValue("C$row", $formeln[3], config::getDefaultStyle());
		$this->setCellValue("D$row", '-', Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("E$row", $formeln[4], config::getDefaultStyle());
		$this->setCellValue("F$row", "", Config::getInputStyle(['align' => 'center']));
		$this->setCellValue("G$row", ":", Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("H$row", "", Config::getInputStyle(['align' => 'center']));
		$pftko->add($di);

		$formeln[5] = "=IF(AND(F$rowGame5=\"\", H$rowGame5=\"\"), \"\", IF(F$rowGame5>H$rowGame5, C$rowGame5, E$rowGame5))";
		$formeln[6] = "=IF(AND(F$rowGame6=\"\", H$rowGame6=\"\"), \"\", IF(F$rowGame6>H$rowGame6, C$rowGame6, E$rowGame6))";

		$row = $row + 2;
		$this->setCellValue("A$row:H$row", "Final", config::getHeaderStyle());
		$row++;
		$this->setCellValue("A$row", "7", config::getDefaultStyle());
		$this->setCellValue("B$row", $pftko->format('H:i'), config::getDefaultStyle());
		$this->setCellValue("C$row", $formeln[5], config::getDefaultStyle());
		$this->setCellValue("D$row", '-', Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("E$row", $formeln[6], config::getDefaultStyle());
		$this->setCellValue("F$row", "", Config::getInputStyle(['align' => 'center']));
		$this->setCellValue("G$row", ":", Config::getDefaultStyle(['align' => 'center']));
		$this->setCellValue("H$row", "", Config::getInputStyle(['align' => 'center']));
		$pftko->add($di);
	}

}
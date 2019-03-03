<?php

namespace TournamentMaker\Maker;


use GamePlanner\Planner;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use TournamentMaker\Helper\ExcelHelper;

class PageResults
extends ExcelHelper
{

	private $countDefaultColumns = 14;

	public function setData(Spreadsheet $spreadsheet, $plan)
	{
		$this->spreadsheet = $spreadsheet;
		$resultdetailed = $this->server->getParameter('resultdetailed', false);
		
		$sheet = new Worksheet(null, 'Resultate');
		$this->spreadsheet->addSheet($sheet, 3);
		$this->spreadsheet->setActiveSheetIndex(3);
		$this->setup();
		$teams = $this->teams;
		$countTeams = count($teams);
		$countColumns = $this->countDefaultColumns + count($teams);
		$maxColumnName = $this->getColumnByInt($countColumns);
		$this->spreadsheet->getActiveSheet()->getColumnDimension("A")->setWidth(22);
		$teamLines = [];
		for ($i = 0; $i < count($teams); $i++) {
			$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($i + 1))->setWidth(3);
			if (!$resultdetailed)
				$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($i + 1))->setVisible(false);
			if (!isset($teamLines[$teams[$i]]))
				$teamLines[$teams[$i]] = [
					"vorrunde" => null,
					"rueckruende" => null,
				];
		}
		$lastColumnInt = count($teams) + 1;
		if (!$resultdetailed)
			$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($lastColumnInt))->setVisible(false);
		$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($lastColumnInt + 1))->setWidth(10);
		$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($lastColumnInt + 2))->setWidth(10);
		$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($lastColumnInt + 3))->setWidth(10);
		$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($lastColumnInt + 4))->setWidth(10);
		$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($lastColumnInt + 5))->setWidth(3);
		$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($lastColumnInt + 6))->setWidth(1);
		$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($lastColumnInt + 7))->setWidth(3);
		$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($lastColumnInt + 8))->setWidth(10);
		$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($lastColumnInt + 9))->setWidth(10);
		$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($lastColumnInt + 10))->setWidth(10)->setVisible(false);
		$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($lastColumnInt + 11))->setWidth(10)->setVisible(false);
		$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($lastColumnInt + 12))->setWidth(10)->setVisible(false);
		$this->spreadsheet->getActiveSheet()->getColumnDimension($this->getColumnByInt($lastColumnInt + 13))->setWidth(10);



		// Turniername anzeigen
		$row = 2;
		$this->setCellValue("A$row", "=Konfiguration!B3", Config::getPageHeaderStyle());

		// Vorrunde
		$row = $row + 2;
		$data = $this->setHeaderTable($row, $maxColumnName, 'Vorrunde');

		$row = $data['row'];
		$firstRow = $row + 1;
		$lastRow = $firstRow + count($teams) - 1;
		foreach ($teams as $teamkey => $team) {
			$row++;
			$teamLines[$team]['vorrunde'] = $row;
			$this->setCellValue("A$row", $team, Config::getDefaultStyle(['bold' => true]));
			$planData = $plan['vorrunde'][$team]['C'] ?? [];
			$columnInt = 1;

			foreach ($teams as $subTeam) {
				$posHome = array_search($subTeam, $planData);
				$posGuest = array_search($team, $plan['vorrunde'][$subTeam]['C'] ?? []);
				if ($posHome)
					$this->setCellValue($this->getColumnByInt($columnInt).$row, $this->getFormelHomeGegner($posHome), Config::getDefaultStyle());
				else if ($posGuest)
					$this->setCellValue($this->getColumnByInt($columnInt).$row, $this->getFormelGegnerHome($posGuest), Config::getDefaultStyle());
				else
					$this->setCellValue($this->getColumnByInt($columnInt).$row, "", Config::getDefaultStyle(['bgcolor' => 'AAAAAAA']));
				$columnInt++;
			}

			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=COUNT(\$B$row:\$D$row)", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=COUNTIF(\$B$row:\$D$row, 3)", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=COUNTIF(\$B$row:\$D$row, 1)", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=COUNTIF(\$B$row:\$D$row, 0)", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, $this->getFormelTore($plan['vorrunde'][$team], 'home'), config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, ":", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, $this->getFormelTore($plan['vorrunde'][$team], 'guest'), config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$columnJ = $this->getColumnByInt(count($teams) + 6);
			$columnL = $this->getColumnByInt(count($teams) + 8);
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=$columnJ$row-$columnL$row", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=SUM(\$B$row:\$D$row, 0)", config::getDefaultStyle(['align' => 'center']));

			// Punkte aus Tordifferenz
			$columnInt++;
			$columnM = $this->getColumnByInt(count($teams) + 9);
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=$columnM$row/100", config::getDefaultStyle());


			// Punkte aus Meiste-Tore
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=($countTeams - RANK($columnJ$row, $columnJ$firstRow:$columnJ$lastRow)) / 10000", config::getDefaultStyle());


			// Summe aus Punkte Spiele, Punkte Tordifferenz und Punkte Meiste-Tore ermitteln
			$columnN = $this->getColumnByInt($countTeams + 10);
			$columnO = $this->getColumnByInt($countTeams + 11);
			$columnP = $this->getColumnByInt($countTeams + 12);
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=$columnN$row+$columnO$row+$columnP$row", config::getDefaultStyle());

			// Ranliste ermitteln
			$columnQ = $this->getColumnByInt($countTeams + 13);
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=RANK($columnQ$row, $columnQ$firstRow:$columnQ$lastRow)", config::getDefaultStyle());

		}

		// Rückrunde
		$row = $row + 2;
		$data = $this->setHeaderTable($row, $maxColumnName, 'Rückrunde');
		$row = $data['row'];
		$firstRow = $row + 1;
		$lastRow = $firstRow + count($teams) - 1;
		foreach ($teams as $team) {
			$row++;
			$teamLines[$team]['rueckrunde'] = $row;
			$this->setCellValue("A$row", $team, Config::getDefaultStyle(['bold' => true]));
			$planData = $plan['rueckrunde'][$team]['C'];
			$columnInt = 1;
			foreach ($teams as $subTeam) {
				$posHome = array_search($subTeam, $planData);
				$posGuest = array_search($team, $plan['rueckrunde'][$subTeam]['C'] ?? []);
				if ($posHome)
					$this->setCellValue($this->getColumnByInt($columnInt).$row, $this->getFormelHomeGegner($posHome), Config::getDefaultStyle());
				else if ($posGuest)
					$this->setCellValue($this->getColumnByInt($columnInt).$row, $this->getFormelGegnerHome($posGuest), Config::getDefaultStyle());
				else
					$this->setCellValue($this->getColumnByInt($columnInt).$row, "", Config::getDefaultStyle(['bgcolor' => 'AAAAAAA']));
				$columnInt++;
			}
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=COUNT(\$B$row:\$D$row)", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=COUNTIF(\$B$row:\$D$row, 3)", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=COUNTIF(\$B$row:\$D$row, 1)", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=COUNTIF(\$B$row:\$D$row, 0)", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, $this->getFormelTore($plan['rueckrunde'][$team], 'home'), config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, ":", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, $this->getFormelTore($plan['rueckrunde'][$team], 'guest'), config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$columnJ = $this->getColumnByInt(count($teams) + 6);
			$columnL = $this->getColumnByInt(count($teams) + 8);
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=$columnJ$row-$columnL$row", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=SUM(\$B$row:\$D$row, 0)", config::getDefaultStyle(['align' => 'center']));

			// Punkte aus Tordifferenz
			$columnInt++;
			$columnM = $this->getColumnByInt(count($teams) + 9);
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=$columnM$row/100", config::getDefaultStyle());


			// Punkte aus Meiste-Tore
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=($countTeams - RANK($columnJ$row, $columnJ$firstRow:$columnJ$lastRow)) / 10000", config::getDefaultStyle());


			// Summe aus Punkte Spiele, Punkte Tordifferenz und Punkte Meiste-Tore ermitteln
			$columnN = $this->getColumnByInt($countTeams + 10);
			$columnO = $this->getColumnByInt($countTeams + 11);
			$columnP = $this->getColumnByInt($countTeams + 12);
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=$columnN$row+$columnO$row+$columnP$row", config::getDefaultStyle());

			// Ranliste ermitteln
			$columnQ = $this->getColumnByInt($countTeams + 13);
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=RANK($columnQ$row, $columnQ$firstRow:$columnQ$lastRow)", config::getDefaultStyle());

		}

		// Gesamt
		$row = $row + 2;
		$data = $this->setHeaderTable($row, $maxColumnName, 'Gesamt', true);
		$row = $data['row'];
		$firstRow = $row + 1;
		$lastRow = $firstRow + count($teams) - 1;
		foreach ($teams as $team) {
			$row++;
			$this->setCellValue("A$row", $team, Config::getDefaultStyle(['bold' => true]));
			$vorrunde = $teamLines[$team]['vorrunde'];
			$rueckrunde = $teamLines[$team]['rueckrunde'];
			$columnInt = count($teams) + 1;
			$columnInt++;
			$column = $this->getColumnByInt($columnInt);
			$this->setCellValue($column.$row, "=\$$column$vorrunde+\$$column$rueckrunde", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$column = $this->getColumnByInt($columnInt);
			$this->setCellValue($column.$row, "=\$$column$vorrunde+\$$column$rueckrunde", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$column = $this->getColumnByInt($columnInt);
			$this->setCellValue($column.$row, "=\$$column$vorrunde+\$$column$rueckrunde", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$column = $this->getColumnByInt($columnInt);
			$this->setCellValue($column.$row, "=\$$column$vorrunde+\$$column$rueckrunde", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$column = $this->getColumnByInt($columnInt);
			$this->setCellValue($column.$row, "=\$$column$vorrunde+\$$column$rueckrunde", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$column = $this->getColumnByInt($columnInt);
			$this->setCellValue($column.$row, ":", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$column = $this->getColumnByInt($columnInt);
			$this->setCellValue($column.$row, "=\$$column$vorrunde+\$$column$rueckrunde", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$column = $this->getColumnByInt($columnInt);
			$this->setCellValue($column.$row, "=\$$column$vorrunde+\$$column$rueckrunde", config::getDefaultStyle(['align' => 'center']));
			$columnInt++;
			$column = $this->getColumnByInt($columnInt);
			$this->setCellValue($column.$row, "=\$$column$vorrunde+\$$column$rueckrunde", config::getDefaultStyle(['align' => 'center']));

			// Punkte aus Tordifferenz
			$columnInt++;
			$columnM = $this->getColumnByInt(count($teams) + 9);
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=$columnM$row/100", config::getDefaultStyle());


			// Punkte aus Meiste-Tore
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=($countTeams - RANK($columnJ$row, $columnJ$firstRow:$columnJ$lastRow)) / 10000", config::getDefaultStyle());


			// Summe aus Punkte Spiele, Punkte Tordifferenz und Punkte Meiste-Tore ermitteln
			$columnN = $this->getColumnByInt($countTeams + 10);
			$columnO = $this->getColumnByInt($countTeams + 11);
			$columnP = $this->getColumnByInt($countTeams + 12);
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=$columnN$row+$columnO$row+$columnP$row", config::getDefaultStyle());

			// Ranliste ermitteln
			$columnQ = $this->getColumnByInt($countTeams + 13);
			$columnInt++;
			$this->setCellValue($this->getColumnByInt($columnInt).$row, "=RANK($columnQ$row, $columnQ$firstRow:$columnQ$lastRow)", config::getDefaultStyle());

		}
		
		return $this->spreadsheet;
	}

	private function setHeaderTable($row, $maxColumnName, $title, $withoutTeams = false)
	{
		$teams = $this->teams;
		$this->setCellValue("A$row:$maxColumnName$row", $title, Config::getHeaderStyle());
		$row++;
		$this->setCellValue("A$row", "Team", Config::getDefaultStyle(['bold' => true]));
		if (!$withoutTeams) {
			foreach ($teams AS $key => $team) {
				$column = $this->getColumnByInt($key + 1);
				$this->setCellValue("$column$row", $team, Config::getDefaultStyle(['textRotation' => '90', 'bold' => true]));

			}
		}

		$columnInt = count($teams) + 2;
		$this->setCellValue($this->getColumnByInt($columnInt)."$row", 'S', Config::getDefaultStyle(['align' => 'center', 'bold' => true]));
		$this->setCellValue($this->getColumnByInt($columnInt + 1)."$row", 'S', Config::getDefaultStyle(['align' => 'center', 'bold' => true]));
		$this->setCellValue($this->getColumnByInt($columnInt + 2)."$row", 'U', Config::getDefaultStyle(['align' => 'center', 'bold' => true]));
		$this->setCellValue($this->getColumnByInt($columnInt + 3)."$row", 'N', Config::getDefaultStyle(['align' => 'center', 'bold' => true]));
		$this->setCellValue($this->getColumnByInt($columnInt + 4)."$row:".$this->getColumnByInt($columnInt + 6)."$row", "Tore", Config::getDefaultStyle(['align' => 'center', 'bold' => true]));
		$this->setCellValue($this->getColumnByInt($columnInt + 7)."$row", 'Differenz', Config::getDefaultStyle(['align' => 'center', 'bold' => true]));
		$this->setCellValue($this->getColumnByInt($columnInt + 8)."$row", 'Punkte', Config::getDefaultStyle(['align' => 'center', 'bold' => true]));
		$this->setCellValue($this->getColumnByInt($columnInt + 9)."$row", 'Punkte Differenz', Config::getDefaultStyle(['align' => 'center', 'bold' => true]));
		$this->setCellValue($this->getColumnByInt($columnInt + 10)."$row", 'Punkte Tore', Config::getDefaultStyle(['align' => 'center', 'bold' => true]));
		$this->setCellValue($this->getColumnByInt($columnInt + 11)."$row", 'Punktesumme', Config::getDefaultStyle(['align' => 'center', 'bold' => true]));
		$this->setCellValue($this->getColumnByInt($columnInt + 12)."$row", 'Rang', Config::getDefaultStyle(['align' => 'center', 'bold' => true]));

		$result = ['row' => $row];
		return $result;
	}

	private function getFormelHomeGegner($row)
	{
		$register = "Spiele";
		$home = '$F'.$row;
		$guest = '$H'.$row;
		$result = '=IF(';
		$result .= "'$register'!$home>'$register'!$guest,3,";
		$result .= "IF(";
		$result .= "'$register'!$home<'$register'!$guest,0,";
		$result .= "IF(";
		$result .= "AND('$register'!$home='$register'!$guest, '$register'!$home<>\"\"),1,\"\"";
		$result .= ")";
		$result .= ")";
		$result .= ")";
		return $result;
	}

	private function getFormelGegnerHome($row)
	{
		$register = "Spiele";
		$home = '$F'.$row;
		$guest = '$H'.$row;
		$result = '=IF(';
		$result .= "'$register'!$home<'$register'!$guest,3,";
		$result .= "IF(";
		$result .= "'$register'!$home>'$register'!$guest,0,";
		$result .= "IF(";
		$result .= "AND('$register'!$home='$register'!$guest, '$register'!$home<>\"\"),1,\"\"";
		$result .= ")";
		$result .= ")";
		$result .= ")";
		return $result;
	}

	private function getFormelTore($rows, $type = 'home')
	{
		$data = [];
		if ($type === 'home') {
			foreach ($rows['C'] ?? [] AS $key => $row) {
				$data[] = "'Spiele'!" . '$F' . $key;
			}
			foreach ($rows['E'] ?? []  AS $key => $row) {
				$data[] = "'Spiele'!" . '$H' . $key;
			}
		} else if ($type === 'guest') {
			foreach ($rows['C'] ?? []  AS $key => $row) {
				$data[] = "'Spiele'!" . '$H' . $key;
			}
			foreach ($rows['E'] ?? []  AS $key => $row) {
				$data[] = "'Spiele'!" . '$F' . $key;
			}
		}
		$result = '='.implode("+", $data);
		return $result;
	}
	
}
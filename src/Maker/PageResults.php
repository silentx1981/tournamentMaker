<?php

namespace TournamentMaker\Maker;


use GamePlanner\Planner;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use TournamentMaker\Helper\ExcelHelper;

class PageResults
extends ExcelHelper
{

	private $countDefaultColumns = 10;

	public function setData(Spreadsheet $spreadsheet, $plan)
	{
		$this->spreadsheet = $spreadsheet;
		$resultdetailed = $this->server->getParameter('resultdetailed', false);
		
		$sheet = new Worksheet(null, 'Resultate');
		$this->spreadsheet->addSheet($sheet, 3);
		$this->spreadsheet->setActiveSheetIndex(3);
		$this->setup();
		$teams = $this->teams;
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

		// Turniername anzeigen
		$row = 2;
		$this->setCellValue("A$row", "=Konfiguration!B3", Config::getPageHeaderStyle());

		// Vorrunde
		$row = $row + 2;
		$data = $this->setHeaderTable($row, $maxColumnName, 'Vorrunde');

		$row = $data['row'];
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
		}

		// Rückrunde
		$row = $row + 2;
		$data = $this->setHeaderTable($row, $maxColumnName, 'Rückrunde');
		$row = $data['row'];
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
		}

		// Gesamt
		$row = $row + 2;
		$data = $this->setHeaderTable($row, $maxColumnName, 'Gesamt', true);
		$row = $data['row'];
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
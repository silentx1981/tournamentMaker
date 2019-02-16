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
use TournamentMaker\Core\Server;

class Maker
{
	/** @var null|Spreadsheet */
	private $spreadsheet = null;
	
	public function make(Server $server)
	{
		$this->spreadsheet = new Spreadsheet();
		$this->spreadsheet->removeSheetByIndex(0);
		$pageConfig = new PageConfig($server);
		$this->spreadsheet = $pageConfig->setData($this->spreadsheet);
		$pageTeams = new PageTeams($server);
		$this->spreadsheet = $pageTeams->setData($this->spreadsheet);
		$pageGames = new PageGames($server);
		$this->spreadsheet = $pageGames->setData($this->spreadsheet);
		
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
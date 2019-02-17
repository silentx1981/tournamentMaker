<?php

namespace TournamentMaker\Helper;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use TournamentMaker\Core\Server;

class ExcelHelper
{
	/** @var Spreadsheet */
	protected $spreadsheet;

	private $columns = [
		"A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W"
	];
	
	/** @var Server */
	protected $server;

	/** @var $teams */
	protected $teams;
	
	public function __construct(Server $server, $teams)
	{
		$this->server = $server;
		$this->teams = $teams;
	}
	
	public function setup()
	{
		$this->spreadsheet->getActiveSheet()->setPrintGridlines(false);
		$this->spreadsheet->getActiveSheet()->setShowGridlines(false);
	}
	
	public function setCellValue($coordinate, $value, $style = [])
	{
		$coordinates = explode(":", $coordinate);
		if (count($coordinates) > 1)
			$this->spreadsheet->getActiveSheet()->mergeCells($coordinate);
		
		$this->spreadsheet->getActiveSheet()->setCellValue($coordinates[0], $value);
		$this->spreadsheet->getActiveSheet()->getStyle($coordinate)->applyFromArray($style);
	}

	public function getColumnByInt($int)
	{
		return $this->columns[$int] ?? null;
	}
}
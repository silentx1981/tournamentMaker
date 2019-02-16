<?php

namespace TournamentMaker\Helper;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use TournamentMaker\Core\Server;

class ExcelHelper
{
	/** @var Spreadsheet */
	protected $spreadsheet;
	
	/** @var Server */
	protected $server;
	
	public function __construct(Server $server)
	{
		$this->server = $server;
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
}
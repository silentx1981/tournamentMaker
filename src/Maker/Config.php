<?php
/**
 * Description
 *
 * @author   silentx
 * @since    1.0.0
 * @date     09.02.19
 */

namespace TournamentMaker\Maker;


class Config
{
	const columnNames = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
	
	public static function getHeaderStyle()
	{
		$styleArray = [
			"font" => [
				"bold" => true
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
			],
			'borders' => [
				'top' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
				'bottom' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
				
				'right' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
				
				'left' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			],
			"fill" => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
				'rotation' => 90,
				'startColor' => [
					'argb' => 'CCCCCCC',
				],
				'endColor' => [
					'argb' => 'CCCCCCC',
				],
			],
		];
		return $styleArray;
	}
	
	public static function getInputStyle()
	{
		$styleArray = [
			'borders' => [
				'top' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
				'bottom' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
				
				'right' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
				
				'left' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			],
			"fill" => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
				'rotation' => 90,
				'startColor' => [
					'argb' => 'ffff000',
				],
				'endColor' => [
					'argb' => 'ffff000',
				],
			],
		];
		return $styleArray;
	}
	
	public static function getDefaultStyle()
	{
		$styleArray = [
			'borders' => [
				'top' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
				'bottom' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
				
				'right' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
				
				'left' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			],
			"fill" => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
				'rotation' => 90,
				'startColor' => [
					'argb' => 'fffffff',
				],
				'endColor' => [
					'argb' => 'fffffff',
				],
			],
		];
		return $styleArray;
	}
	
	public static function getPageHeaderStyle()
	{
		$styleArray = [
			"font" => [
				"bold" => true,
				"size" => 16,
			],
		];
		return $styleArray;
	}
}
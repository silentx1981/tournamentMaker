<?php
/**
 * Description
 *
 * @author   silentx
 * @since    1.0.0
 * @date     09.02.19
 */

namespace TournamentMaker\Maker;


use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Config
{
	const columnNames = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', "I", "J", "K", "L", "M", "N", "O", "Q", "R", "S", "T", "U", "V"];

	public static $align = [
		"center" => Alignment::HORIZONTAL_CENTER
	];
	
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
	
	public static function getInputStyle($options = [])
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
		return self::addOptions($styleArray, $options);
	}
	
	public static function getDefaultStyle($options = [])
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
		return self::addOptions($styleArray, $options);
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

	private static function addOptions($styleArray, $options)
	{
		if (isset($options['align']))
			$styleArray['alignment']['horizontal'] = self::$align[$options['align']] ?? null;
		if (isset($options['textRotation']))
			$styleArray['alignment']['textRotation'] = $options['textRotation'] ?? 0;
		if (isset($options['bgcolor'])) {
			$styleArray['fill']['fillType'] = Fill::FILL_GRADIENT_LINEAR;
			$styleArray['fill']['startColor']['argb'] = $options['bgcolor'];
			$styleArray['fill']['endColor']['argb'] = $options['bgcolor'];
		}
		if (isset($options['bold']))
			$styleArray['font']['bold'] = $options['bold'];

		return $styleArray;
	}
}
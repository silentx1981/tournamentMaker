<?php

namespace TournamentMaker\Core;

class Helper
{
	public static function getVersion()
	{
		$tags = scandir('../.git/refs/tags/', SCANDIR_SORT_DESCENDING);
		$version = $tags[0] ?? '0.0.0';
		return $version;
	}
}
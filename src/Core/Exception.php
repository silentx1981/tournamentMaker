<?php

namespace TournamentMaker\Core;


class Exception
{

	public static function runtime($message)
	{
		http_response_code(409);
		$result = [
			'message' => $message
		];
		echo json_encode($result);
		exit;
	}
}
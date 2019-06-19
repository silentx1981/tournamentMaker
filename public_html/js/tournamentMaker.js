var tournamentMaker = angular.module('tournamentMaker', ['ui.bootstrap', 'ngFileSaver']);


tournamentMaker.controller('TournamentController', function($scope, $http, FileSaver, Blob) {

	$scope.generate = function() {
		var reader = new FileReader();
		var data = {};
		data.create = 1;
		data.teams = $scope.teams;
		data.tournamentname = $scope.tournamentname;
		data.resultdetailed = $scope.resultdetailed;
		data.duration = $scope.duration;
		data.pause = $scope.pause;
		data.prefirsttime = '@' + Math.round($scope.prefirsttime.getTime()/1000);
		data.backfirsttime = '@' + Math.round($scope.backfirsttime.getTime()/1000);
		data.finalpairs = $scope.finalpairs;
		data.prefirsttimeko = '@' + Math.round($scope.prefirsttimeko.getTime()/1000);
		data.durationko = $scope.durationko;
		data.pauseko = $scope.pauseko;

		$http.post(basePath, JSON.stringify(data), {responseType: 'blob'}).then(function(response) {
			FileSaver.saveAs(response.data, $scope.tournamentname+'.xlsx');
		}, function(response) {
			var reader = new FileReader();
			reader.onload = function() {
				var data = JSON.parse(reader.result);
				Swal.fire(data.message, '', 'error');
			}
			reader.readAsText(response.data);
		});
	}


	$scope.teams = [{"name":"Team 1"}, {"name":"Team 2"}, {"name":"Team 3"}];
	$scope.teams2 = [];
	$scope.teams3 = [];
	$scope.teams4 = [];
	$scope.tournamentname = 'Mein Turnier';
	$scope.locations = [{"name":"Platz 1"}];
	$scope.duration = 10;
	$scope.pause = 5;
	$scope.prefirsttime = new Date();
	$scope.prefirsttime.setHours(8);
	$scope.prefirsttime.setMinutes(0);
	$scope.backfirsttime = new Date();
	$scope.backfirsttime.setHours(13);
	$scope.backfirsttime.setMinutes(0);
	$scope.prefirsttimeko = new Date();
	$scope.prefirsttimeko.setHours(17);
	$scope.prefirsttimeko.setMinutes(0);
	$scope.finalpairs = 0;
	$scope.durationko = 10;
	$scope.pauseko = 5;

	$scope.add = function(groupNr) {

		var tmp = {};
		if (groupNr === undefined) {
			var nextTeamNr = $scope.teams.length + 1;
			tmp.name = "Team "+nextTeamNr;
			$scope.teams.push(tmp);
		} else if (groupNr === 2) {
			var nextTeamNr = $scope.teams2.length + 1;
			tmp.name = "Team "+nextTeamNr;
			$scope.teams2.push(tmp);
		} else if (groupNr === 3) {
			var nextTeamNr = $scope.teams3.length + 1;
			tmp.name = "Team "+nextTeamNr;
			$scope.teams3.push(tmp);
		} else if (groupNr === 4) {
			var nextTeamNr = $scope.teams4.length + 1;
			tmp.name = "Team "+nextTeamNr;
			$scope.teams4.push(tmp);
		}

	}

	$scope.remove = function (groupNr) {
		if (groupNr === undefined)
			$scope.teams.pop();
		if (groupNr === 2)
			$scope.teams2.pop();
		if (groupNr === 3)
			$scope.teams3.pop();
		if (groupNr === 4)
			$scope.teams4.pop();
	}

	$scope.addLocation = function() {
		var tmp = {};
		var nextLocationNr = $scope.locations.length + 1;
		tmp.name = "Platz "+nextLocationNr;
		$scope.locations.push(tmp);
	}

	$scope.removeLocation = function() {
		$scope.locations.pop();
	}

});
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
	$scope.tournamentname = 'Mein Turnier';
	$scope.duration = 10;
	$scope.pause = 5;
	$scope.prefirsttime = new Date();
	$scope.prefirsttime.setHours(8);
	$scope.prefirsttime.setMinutes(0);
	$scope.backfirsttime = new Date();
	$scope.backfirsttime.setHours(13);
	$scope.backfirsttime.setMinutes(0);

	$scope.add = function() {

		var tmp = {};
		var nextTeamNr = $scope.teams.length + 1;
		tmp.name = "Team "+nextTeamNr;
		$scope.teams.push(tmp);

	}

	$scope.remove = function () {
		$scope.teams.pop();
	}

});
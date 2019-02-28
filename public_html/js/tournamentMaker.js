var tournamentMaker = angular.module('tournamentMaker', ['ngFileSaver']);


tournamentMaker.controller('TournamentController', function($scope, $http, FileSaver, Blob) {

	$scope.generate = function() {
		var reader = new FileReader();
		var data = {};
		data.create = 1;
		data.teams = $scope.teams;
		data.tournamentname = $scope.tournamentname;

		$http.post(basePath, JSON.stringify(data), {responseType: 'blob'}).then(function(response) {
			FileSaver.saveAs(response.data, 'test.xlsx');
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
	$scope.tournamentname = 'Mein Turnier'

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
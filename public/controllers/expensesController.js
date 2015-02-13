(function(){

	var app = angular.module("ExpensesController",[]);

	app.controller('ExpensesCtrl',[
		"$scope", "$http", "$cookies", "$location",
		function( $scope, $http, $cookies, $location ){
			
			$scope.limit  = 20;
			$scope.offset = 0; 
			$scope.accessToken = $cookies.accessToken;
			$scope.expenses = [];
			
			$http.get("/api/v1/expenses",{ 
				"params" :
				{
					"limit" 		 : $scope.limit, 
					"offset"		 : $scope.offset, 
					"access_token" 	 : $scope.accessToken 
				}
			})
			.success(function(data){
				$scope.expenses = data;
			})
			.error(function(data){
				console.error(data);
			});
	}]);

	app.controller("ExpensesAddCtrl",
		["$scope", "$http", "$cookies", "$location",
		function( $scope, $http, $cookies, $location ){
			$('.datepicker').datepicker({
				"dateFormat": "dd/mm/yy"
			});

			$scope.fields = {};
			$scope.timeHours = [];
			for(var i=0; i<24; i++){
				$scope.timeHours.push(i);
			}
			$scope.timeMinutes 	= [];
			for(var i=0; i<60; i++){
				$scope.timeMinutes.push(i);
			}

			$scope.submit = function(){
				if( $scope.formAdd.$valid ){
					$('#element').popover('show');
				} else {
					$('#element').popover('show');
				}
			};
	}]);

	app.controller("ExpensesWeeklyCtrl",
		["$scope", "$http", "$cookies", "$location",
		function( $scope, $http, $cookies, $location ){
			$('.datepicker').datepicker({
				"dateFormat": "dd/mm/yy"
			});

			$scope.fields = {};
			$scope.timeHours = [];
			for(var i=0; i<24; i++){
				$scope.timeHours.push(i);
			}
			$scope.timeMinutes 	= [];
			for(var i=0; i<60; i++){
				$scope.timeMinutes.push(i);
			}

			$scope.submit = function(){
				if( $scope.formAdd.$valid ){
					$('#element').popover('show');
				} else {
					$('#element').popover('show');
				}
			};
	}]);

})();

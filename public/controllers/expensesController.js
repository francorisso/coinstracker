(function(){

	var app = angular.module("ExpensesController",[]);

	app.controller('ExpensesCtrl',[
		"$scope", "$http", "$cookies", "$location",
		function( $scope, $http, $cookies, $location ){
			if( typeof $cookies.accessToken != "undefined" && $cookies.accessToken.length>0){
				$http.get("/api/v1/validToken",{
					params:{
						"access_token": $cookies.accessToken
					}	
				}).error(function(){
					$location.url('/login');	
				}); 
			} else {
				$location.url('/login');
			}

			$scope.limit  = 20;
			$scope.offset = 0; 
			$scope.accessToken = $cookies.accessToken;
			$scope.expenses = [];
			
			$scope.get = function(){
				$http.get("/api/v1/expenses",{ 
					"params" :
					{
						"limit" 		 : $scope.limit, 
						"offset"		 : $scope.offset, 
						"access_token" 	 : $scope.accessToken 
					}
				})
				.success(function(data){
					for(var i=0; i<data.length; i++){
						$scope.expenses.push( data[i] );
					}
				})
				.error(function(data){
				});

				$scope.offset += $scope.limit;
			};
			$scope.get();

			$scope.delete = function(id){
				if(confirm("This action can't be undone, \nare you sure to continue?")){
					$('#row-'+id).remove();
					$http.post("/api/v1/expenses/delete/"+id,
					{ 
						access_token : $scope.accessToken
					})
					.success(function(data){
					})
					.error(function(data){
						alert("error!");
					});
				}
			};

			$scope.edit = function(id){
				$location.url("/expenses/add/"+id);
			};
	}]);


	app.controller("ExpensesAddCtrl",
		["$scope", "$http", "$cookies", "$location",
		 "$rootScope", "$routeParams", "$route",
		function( $scope, $http, $cookies, $location, $rootScope, $routeParams, $route ){
			if( typeof $cookies.accessToken != "undefined" && $cookies.accessToken.length>0){
				$http.get("/api/v1/validToken",{
					params:{
						"access_token": $cookies.accessToken
					}	
				}).error(function(){
					$location.url('/login');	
				}); 
			} else {
				$location.url('/login');
			}

			$('.datepicker').datepicker({
				"dateFormat": "dd/mm/yy"
			});

			$scope.fields = {};
			$scope.accessToken = $cookies.accessToken;
			
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
					var dateSplit = $scope.fields.date.split("/");
					var fields = $.extend(true, {}, $scope.fields);
					fields.date = dateSplit[2] + "-" + dateSplit[1] + "-" + dateSplit[0];
					if($scope.fields.id>0){
						$http.put("/api/v1/expenses/" + $scope.fields.id, { 
							"fields": fields, 
							"access_token": $scope.accessToken
						})
						.success(function(data){
							alert("Updated!");
						})
						.error(function(data){
							alert("Error!");
						});
					} else {
						$http.post("/api/v1/expenses", { 
							fields: fields, 
							access_token: $scope.accessToken
						})
						.success(function(data){
							alert("Added!");
						})
						.error(function(data){
							alert("Error!");
						});
					}

				} else {
					
				}
			};

			$scope.get = function(id){
				$http.get("/api/v1/expenses/"+id, { 
					"params":{ 
						"access_token" : $scope.accessToken 
					}
				})
				.success(function(data){
					var dateSpl = data.date.split("-");
					data.date = dateSpl[2] + "/" + dateSpl[1] + "/" + dateSpl[0];
					$scope.fields = data;
					console.log($scope.fields);
				});
			};

			if( typeof $routeParams.id!="undefined" && $routeParams.id>0 ){
				$scope.get( $routeParams.id );
			}
	}]);

	app.controller("ExpensesWeeklyCtrl",
		["$scope", "$http", "$cookies", "$location",
		function( $scope, $http, $cookies, $location ){
			if( typeof $cookies.accessToken != "undefined" && $cookies.accessToken.length>0){
				$http.get("/api/v1/validToken",{
					params:{
						"access_token": $cookies.accessToken
					}	
				}).error(function(){
					$location.url('/login');	
				}); 
			} else {
				$location.url('/login');
			}

			$scope.filters = {};
			$scope.expenses = [];
			$scope.accessToken = $cookies.accessToken;
			$scope.months = [
				{
					"name": "January",
					"number": 1
				},{
					"name": "February",
					"number": 2
				},{
					"name": "March",
					"number": 3
				},{
					"name": "April",
					"number": 4
				},{
					"name": "May",
					"number": 5
				},{
					"name": "June",
					"number": 6
				},{
					"name": "July",
					"number": 7
				},{
					"name": "August",
					"number": 8
				},{
					"name": "September",
					"number": 9
				},{
					"name": "October",
					"number": 10
				},{
					"name": "November",
					"number": 11
				},{
					"name": "December",
					"number": 12
				}
			];
			
			$scope.years = [];
			yearCurrent = new Date().getFullYear();
			for(var y=yearCurrent; y>yearCurrent-10; y--){
				$scope.years.push( y );
			}

			$scope.submit = function(){
				if( $scope.formFilter.$valid ){
					$http.get("/api/v1/expenses/weekly",{ 
						"params" :
						{
							"month" 		: $scope.filters.month, 
							"year"  		: $scope.filters.year, 
							"access_token" 	: $scope.accessToken 
						}
					})
					.success(function(data){
						$scope.expenses = data;
					})
					.error(function(data){
						
					});
				} else {
					
				}
			};

			$scope.print = function(divName){
				var printContents = $("#"+divName).html();
				var popupWin = window.open('', '_blank', 'width=300,height=300');
				popupWin.document.open()
				popupWin.document.write('<html><head><link rel="stylesheet" type="text/css" href="/css/app.css" /></head><body onload="window.print()">' + printContents + '</html>');
				popupWin.document.close();
			};

			
	}]);

})();

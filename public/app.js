(function(){

	var app = angular.module("coinsTrackerApp",[
		"ngRoute", 
		"ngCookies",
		"LoginController", 
		"RegisterController", 
		"HomeController",
		"ExpensesController"
	]);

	app.controller('AppCtrl',
		['$http', '$cookies', '$location', 
		function($http, $cookies, $location)
		{
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
		}
	]);

	app.config(['$routeProvider', '$locationProvider', 
	function ($routeProvider, $locationProvider) { 
		$locationProvider.html5Mode(true);

		$routeProvider
		.when("/", {
			templateUrl: "/ajax/"
		})
		.when("/login", {
			templateUrl: "/ajax/auth/login",
			controller: "LoginCtrl"
		})
		.when("/register", {
			templateUrl: "/ajax/auth/register",
			controller: "RegisterCtrl"
		})
		.when("/expenses",{
			templateUrl: "/template/expenses/home.html",
			controller: "ExpensesCtrl"
		})
		.when("/expenses/add",{
			templateUrl: function(){
				return "/template/expenses/add.html"
			},
			controller: "ExpensesAddCtrl"
		})
		.when("/expenses/add/:id",{
			templateUrl: function(params) {
				return "/template/expenses/add.html";
		    },
			controller: "ExpensesAddCtrl"
		})
		.when("/expenses/weekly",{
			templateUrl: "/template/expenses/weekly.html",
			controller: "ExpensesWeeklyCtrl"
		});
		
	}]);

})();

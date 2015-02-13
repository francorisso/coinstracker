(function(){

	var app = angular.module("LoginController",[]);

	app.controller('LoginCtrl',[
		"$scope", "$http", "$cookies", "$location",
		function( $scope, $http, $cookies, $location ){
			$scope.fields = {
			};
			$scope.errors = [];
			$scope.submit = function(){
				$scope.errors = [];
				if( $scope.loginForm.$valid==true ){
					$scope.fields._token = $('#_token').val();
					$http.post("/ajax/authorize", $scope.fields)
					.success(function(data){
						$cookies.accessToken = data;
						$location.url("/expenses");
					})
					.error(function(data){
						$scope.errors.push( data.error_description );
					});

				} else {
					window.alert("Please complete all the fields");
				}
			}
	}]);

})();

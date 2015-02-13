(function(){

	var app = angular.module("RegisterController",[]);

	app.controller('RegisterCtrl',[
		"$scope", "$http", "$cookies", "$location",
		function( $scope, $http, $cookies, $location ){
			$scope.fields = {
			};
			$scope.errors = [];
			
			$scope.submit = function(){
				$scope.errors = []
				if( $scope.formRegister.$valid==true ){
					if($scope.fields.password != $scope.fields.password_confirmation){
						$scope.errors.push("Passwords don't mach");
						return;
					}

					$scope.fields._token = $('#_token').val();
					
					$http.post("/api/v1/users/create", $scope.fields)
					.success(function(data){
						
						$http.post("/ajax/authorize", $scope.fields)
						.success(function(data){
							$cookies.accessToken = data;
							$location.url("/expenses");
						})
						.error(function(data){
							$scope.errors.push( data.error_description );
						});

					})
					.error(function(data){
						data = angular.fromJson(data);
						$scope.errors.push( data.error_message );
					});

				} else {
					$scope.errors.push("Please complete all the fields");
				}
			}
	}]);

})();

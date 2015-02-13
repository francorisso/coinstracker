<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Login</div>
				<div class="panel-body">
					<form class="form-horizontal" 
					ng-submit="submit()" 
					name="loginForm" 
					novalidate 
					ng-controller="LoginCtrl" >
						<input type="hidden" 
							name="_token"
							id="_token"
							value="[[ csrf_token() ]]"
						/>

						<div class="form-group">
							<label class="col-md-4 control-label">E-Mail Address</label>
							<div class="col-md-6">
								<input type="email" class="form-control" name="email" value="" ng-model="fields.email" required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Password</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password" ng-model="fields.password" required>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" style="margin-right: 15px;">
									Login
								</button>
								<a href="/register">Create an account</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
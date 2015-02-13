<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Register</div>
				<div class="panel-body">
					
					<div class="alert alert-danger" ng-show="errors.length">
						<strong>Whoops!</strong> There were some problems with your input.<br><br>
						<ul>
							<li ng-repeat="error in errors">{{ error }}</li>
						</ul>
					</div>

					<form class="form-horizontal" role="form" 
					name="formRegister"
					ng-submit="submit()"
					novalidate
					>
						<input type="hidden" name="_token" id="_token" value="[[ csrf_token() ]]">

						<div class="form-group">
							<label class="col-md-4 control-label">Name</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="name" 
								ng-model="fields.name" value="{{ fields.name }}" required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">E-Mail Address</label>
							<div class="col-md-6">
								<input type="email" class="form-control" name="email" 
								ng-model="fields.email" value="{{ fields.email }}" required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Password</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password"
								ng-model="fields.password" required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Confirm Password</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password_confirmation"
								ng-model="fields.password_confirmation" required>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									Register
								</button>
								<a href="/login">Already in? Login</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
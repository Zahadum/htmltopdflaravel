@extends('layouts.app')
@section('content')
	<div class="container">
		<div class="row">
			<h1>Submit a link</h1>
			<form action="/submit" method="post">
				<!-automate create cross site request forgeries token field in the form-!>
				{!! csrf_field() !!}
				<div class="form-group">
					<label for="name">Name</label>
					<input type="text" class="form-control" id="name" name="name" placeholder="Name"/>
				</div>
				<div class="form-group">
					<label for="url">URL</label>
					<input type="text" class="form-control" id="url" name="url" placeholder="www.url.com"/>
				</div>
				<div class="form-group">
					<label for="description">Description</label>
					<textarea class="form-control" id="description" name="description" placeholder="Description">
					</textarea>
				</div>
				<button type="submit" class="btn btn-default">Submit</button>
			</form>
		</div>
	</div>
@endsection
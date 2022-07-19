@if ($errors->has($inputName))
	<span class="help-block">
		<strong>{{ $errors->first($inputName) }}</strong>
	</span>
@endif
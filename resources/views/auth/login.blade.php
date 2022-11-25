@extends('app')

@section('login-and-registration-forms')
    <div>
        <div class="card">
            <div class="card-header">
                Login
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="input-group mb-3">
                        <input 
                            type="text" 
                            id="login" 
                            name="login" 
                            class="form-control" 
                            placeholder="Username oder E-Mail" 
                            aria-label="Username"
                            value="{{ old('login') }}"
                            required
                            autofocus
                        >
                    </div>
                    
                    <div class="input-group mb-3">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Password" 
                            aria-label="Password"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">Login</button>
                    <a href="/register" class="">Register</a>
                </form>

                @if ($errors->has('login'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('login') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
@endsection
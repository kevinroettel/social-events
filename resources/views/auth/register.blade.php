@extends('app')

@section('content')
    <div>
        <div class="card">
            <div class="card-header">
                Register
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="input-group mb-3">
                        <input 
                            type="text" 
                            id="register-username-input" 
                            name="username" 
                            class="form-control" 
                            placeholder="Username" 
                            aria-label="Username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                        >
                    </div>

                    <div class="input-group mb-3">
                        <input 
                            type="email" 
                            id="register-email-input" 
                            name="email" 
                            class="form-control" 
                            placeholder="E-Mail" 
                            aria-label="E-Mail"
                            value="{{ old('email') }}"
                            required
                        >
                    </div>

                    <div class="input-group mb-3">
                        <input 
                            type="password" 
                            id="register-password-input" 
                            name="password" 
                            class="form-control" 
                            placeholder="Password" 
                            aria-label="Password"
                            required
                        >
                    </div>

                    <div class="input-group mb-3">
                        <input 
                            type="password" 
                            id="register-password-confirmation-input" 
                            name="password_confirmation" 
                            class="form-control" 
                            placeholder="Password Wiederholen" 
                            aria-label="Password Wiederholen"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">Register</button>
                </form>

                @if ($errors->any())
                    <span>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </span>
                @endif
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')
@section('title', 'Sign in')

@section('content')
    <div style="max-width:380px;margin:8vh auto 0;">
        <div class="card">
            <h1 style="text-align:center;">🔑 License Server</h1>
            <form method="POST" action="{{ url('/login') }}">
                @csrf
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" autofocus required>
                </div>
                <div class="field">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="field">
                    <label style="display:flex;align-items:center;gap:8px;color:var(--fg);">
                        <input type="checkbox" name="remember" style="width:auto;"> Remember me
                    </label>
                </div>
                <button class="btn" style="width:100%;">Sign in</button>
            </form>
        </div>
    </div>
@endsection

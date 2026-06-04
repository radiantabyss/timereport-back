@include('Common::partials.header')

<div class="title">
    {{ __('Welcome to') }} {{ config('app.name') }}
</div>

<div class="text-center">
    <p>
        {{ __('An account has been created for you at ') }} {{ config('app.name') }} {{ __('with a random password.') }} <br>
        {{ __('Email:') }} <b>{{ $email }}</b><br>
        {{ __('Password:') }} <b>{{ $password }}</b><br>
    </p>
    <a href="{{ config('path.front_url') }}/login" class="btn btn--primary">
        {{ __('Login') }}
    </a>
    <p style="margin-top: 30px;">
        {{ __('The password can be changed from Account Settings.') }}
    </p>
</div>

@include('Common::partials.footer')

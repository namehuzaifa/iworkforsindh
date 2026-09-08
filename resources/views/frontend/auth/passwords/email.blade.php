@extends('frontend.layouts.app')

@section('title')
    {{ __('reset_password') }}
@endsection

@section('main')
    <div class="row mt-0 mt-lg-5">
        <div class="col-12 order-1 order-lg-0">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-xl-5 col-lg-6 col-md-12">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div class="auth-box2">
                            <form method="POST" action="{{ route('password.email') }}" class="rt-form">
                                @csrf
                                <h4 class="rt-mb-20">{{ __('reset_password') }}</h4>
                                <span class="d-block body-font-3 text-gray-600 rt-mb-32 mb-2">
                                    {{ __('go_back_to') }}
                                    <span><a href="{{ route('login') }}">{{ __('log_in') }}</a></span>
                                </span>
                                <span class="d-block body-font-3 text-gray-600 rt-mb-32">
                                    {{ __('dont_have_account') }}
                                    <span><a href="{{ route('register') }}">
                                            {{ __('create_account') }}</a></span>
                                </span>
                                <div class="fromGroup rt-mb-15">
                                    <input id="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" type="email"
                                        placeholder="{{ __('email_address') }}">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <button id="submitButton" type="submit" class="btn btn-primary d-block rt-mb-15">
                                    <span class="button-content-wrapper ">
                                        <span class="button-icon align-icon-right">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5 12H19" stroke="white" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M12 5L19 12L12 19" stroke="white" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </span>
                                        <span class="button-text">
                                            {{ __('send_password_reset_link') }}
                                        </span>
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        Validate();
        $('#email').keyup(Validate);

        function Validate() {
            if ($('#email').val().length > 0) {
                $('#submitButton').prop("disabled", false);
            } else {
                $('#submitButton').prop("disabled", true);
            }
        }
    </script>
@endsection

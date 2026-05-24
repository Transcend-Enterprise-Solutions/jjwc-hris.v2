@extends ('livewire.emails.email-layout')

@section ('content')

    <div class="email">
        <div class="email-wrapper">

            <!-- Header -->
            <div class="email-header">
                <a href="/" target="_blank" style="text-decoration: none; width: 100%;">
                    <h1 class="email-greetings">{{ $header }}</h1>
                </a>
            </div>

            <!-- Body -->
            <div class="email-body">
                <div>
                    <div class="content-cell">
                        <h1 class="email-greetings-2">{!! $greetings !!}!</h1>
                        {!! $message_body !!}
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p class="footer-content" style="width: 100%; text-align: center;">
                    {{ $footer }}
                </p>
            </div>

        </div>
    </div>

@endsection
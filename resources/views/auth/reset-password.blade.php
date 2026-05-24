<x-authentication-layout>
    <style>
        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in-left {
            opacity: 0;
            transform: translateX(-100%);
            animation: slideInLeft 0.5s ease-out forwards;
        }

        .animate-slide-in-left-delay-1 {
            opacity: 0;
            transform: translateX(-100%);
            animation: slideInLeft 0.5s ease-out 0.05s forwards;
        }

        .animate-slide-in-left-delay-2 {
            opacity: 0;
            transform: translateX(-100%);
            animation: slideInLeft 0.4s ease-out 0.1s forwards;
        }
    </style>

    <x-validation-errors class="mb-4" />
     <!-- Message -->
     <h1 class="text-3xl text-slate-800 font-bold mb-6 animate-slide-in-left">
        {{ __('Reset Password') }}
    </h1>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="block">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required readonly autofocus class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"/>
        </div>

        <div class="mt-4">
            <x-label for="password" value="{{ __('Password') }}" />
            <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"/>
        </div>

        <div class="mt-4">
            <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
            <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full h-12 px-4 py-2 text-black border rounded-lg appearance-none bg-chalk border-zinc-300 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm"/>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-button class="rounded-md px-6 py-2">
                {{ __('Reset Password') }}
            </x-button>
        </div>
    </form>

</x-authentication-layout>

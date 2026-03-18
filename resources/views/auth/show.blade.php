<x-app>
    <x-slot:title>
        Log-in
    </x-slot:title>
    <style>
        .login-intro-overlay {
            position: fixed;
            inset: 0;
            z-index: 60;
            background-image: url('{{ asset('assets/home_expanded2.png') }}');
            background-size: cover;
            background-position: center bottom;
            background-repeat: no-repeat;
            transform-origin: center bottom;
            animation: introBottomToTopZoomOut 2.25s cubic-bezier(0.2, 0.3, 0.2, 1) forwards;
        }

        .login-content {
            opacity: 0;
            animation: contentFadeIn 0.45s ease-out 1.95s forwards;
        }

        @keyframes introBottomToTopZoomOut {

            0% {
                opacity: 1;
                transform: scale(1.5);
                background-position: center bottom;
            }

            60% {
                opacity: 1;
                transform: scale(1.0);
                background-position: center top;
            }

            100% {
                opacity: 0;
                transform: scale(1);
                background-position: center top;
                pointer-events: none;
            }

        }

        @keyframes contentFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
    <div>
        <div class="login-intro-overlay" aria-hidden="true"></div>

        <div class="login-content h-screen w-full flex items-center justify-center"
            style="background-image: url('{{ asset('assets/home_plain.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">

            <div class="w-full max-w-sm bg-black/85 border-4 border-yellow-600 p-8 font-mono"
                style="box-shadow: 6px 6px 0 #000, -2px -2px 0 #7c4a00, 2px 2px 0 #7c4a00 inset;">
                <img src="{{ asset('assets/title.png') }}" alt="">
                <h2 class="text-yellow-400 text-xl font-bold text-center uppercase tracking-widest mb-1">
                    &#9876; Hangman &#9876;
                </h2>
                <p class="text-yellow-700 text-xs text-center uppercase tracking-widest mb-6">— Enter the Realm —</p>

                <form method="post" action="{{ route('auth.login') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block text-yellow-300 text-xs uppercase tracking-wider mb-1">
                            &#9670; Traveller's Name
                        </label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}"
                            class="w-full bg-gray-950 text-yellow-100 border-2 border-yellow-800 px-3 py-2 text-sm rounded-none outline-none focus:border-yellow-400 placeholder-yellow-900" />
                        @error('name')
                            <div class="text-red-400 text-xs mt-1">&#9888; {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-yellow-300 text-xs uppercase tracking-wider mb-1">
                            &#9670; Secret Passphrase
                        </label>
                        <input type="password" name="password" id="password" required
                            class="w-full bg-gray-950 text-yellow-100 border-2 border-yellow-800 px-3 py-2 text-sm rounded-none outline-none focus:border-yellow-400" />
                        @error('password')
                            <div class="text-red-400 text-xs mt-1">&#9888; {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <button type="submit"
                            class="w-full bg-yellow-700 hover:bg-yellow-600 active:bg-yellow-800 text-black font-bold uppercase tracking-widest text-sm py-2 px-4 border-2 border-yellow-400 rounded-none transition-colors"
                            style="box-shadow: 3px 3px 0 #000;">
                            &#9876; Enter &#9876;
                        </button>
                    </div>

                    @error('oauth')
                        <div class="text-red-400 text-xs mb-3 text-center">&#9888; {{ $message }}</div>
                    @enderror

                    {{-- <div class="border-t border-yellow-900 pt-4 flex justify-between text-xs">
                        <a href="{{ route('registration.show') }}"
                            class="text-yellow-600 hover:text-yellow-300 uppercase tracking-wide transition-colors">
                            [New Adventurer]
                        </a>

                    </div> --}}
                </form>
                <div class="flex items-center gap-2 my-4">
                    <div class="flex-1 border-t-2 border-amber-700"></div>
                    <span class="text-amber-600 text-xs uppercase tracking-widest font-bold">&#9670; or &#9670;</span>
                    <div class="flex-1 border-t-2 border-amber-700"></div>
                </div>
                <div>
                    <a href="{{ route('oauth.show') }}"
                        class="flex items-center justify-center gap-3 w-full bg-gray-950 hover:bg-gray-900 active:bg-black text-yellow-400 hover:text-yellow-200 font-bold uppercase tracking-widest text-sm py-2 px-4 border-2 border-yellow-700 hover:border-yellow-400 rounded-none transition-colors"
                        style="box-shadow: 3px 3px 0 #000;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="w-5 h-5 flex-shrink-0">
                            <path fill="#EA4335"
                                d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
                            <path fill="#4285F4"
                                d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
                            <path fill="#FBBC05"
                                d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
                            <path fill="#34A853"
                                d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
                        </svg>
                        &#9876; Sign in with Google &#9876;
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app>

<x-app>
    <x-slot:title>
        Log-in
    </x-slot:title>
    <div>
        <div class="h-screen w-full flex items-center justify-center"
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

                    <div class="border-t border-yellow-900 pt-4 flex justify-between text-xs">
                        <a href="{{ route('registration.show') }}"
                            class="text-yellow-600 hover:text-yellow-300 uppercase tracking-wide transition-colors">
                            [New Adventurer]
                        </a>
                        <a href="{{ route('oauth.show') }}"
                            class="text-yellow-600 hover:text-yellow-300 uppercase tracking-wide transition-colors">
                            [Login via Google]
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app>

<x-app>
    <x-slot:title>
        New Game
    </x-slot:title>

    <div>
        <div class="h-screen w-full flex items-center justify-center"
            style="background-image: url('{{ asset('assets/home_plain.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">

            <div class="w-full max-w-sm bg-black/85 border-4 border-yellow-600 p-8 font-mono"
                style="box-shadow: 6px 6px 0 #000, -2px -2px 0 #7c4a00, 2px 2px 0 #7c4a00 inset;">
                <img src="{{ asset('assets/title.png') }}" alt="">
                <h2 class="text-yellow-400 text-xl font-bold text-center uppercase tracking-widest mb-1 mt-2">
                    &#9876; New Quest &#9876;
                </h2>
                <p class="text-yellow-700 text-xs text-center uppercase tracking-widest mb-6">— Forge a New Game —</p>

                <form method="post" action="{{ route('games.store') }}">
                    @csrf

                    <div class="mb-6">
                        <label for="name" class="block text-yellow-300 text-xs uppercase tracking-wider mb-1">
                            &#9670; Game Name
                        </label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                            class="w-full bg-gray-950 text-yellow-100 border-2 border-yellow-800 px-3 py-2 text-sm rounded-none outline-none focus:border-yellow-400 placeholder-yellow-900" />
                        @error('name')
                            <div class="text-red-400 text-xs mt-1">&#9888; {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="difficulty" class="block text-yellow-300 text-xs uppercase tracking-wider mb-1">
                            &#9670; Difficulty
                        </label>
                        <select id="difficulty" name="difficulty" required
                            class="w-full bg-gray-950 text-yellow-100 border-2 border-yellow-800 px-3 py-2 text-sm rounded-none outline-none focus:border-yellow-400">
                            @php
                                $difficulties = ['easy', 'medium', 'hard', 'extreme'];
                            @endphp
                            @foreach ($difficulties as $difficulty)
                                @php
                                    $remaining = $remainingSlots[$difficulty] ?? 0;
                                @endphp
                                <option value="{{ $difficulty }}" @selected(old('difficulty', 'easy') === $difficulty)
                                    @disabled($remaining <= 0)>
                                    {{ ucfirst($difficulty) }} ({{ $remaining }}/5 remaining)
                                </option>
                            @endforeach
                        </select>
                        @error('difficulty')
                            <div class="text-red-400 text-xs mt-1">&#9888; {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <button type="submit"
                            class="cursor-pointer w-full bg-yellow-700 hover:bg-yellow-600 active:bg-yellow-800 text-black font-bold uppercase tracking-widest text-sm py-2 px-4 border-2 border-yellow-400 rounded-none transition-colors"
                            style="box-shadow: 3px 3px 0 #000;">
                            &#9876; Create Quest &#9876;
                        </button>
                    </div>

                    <div class="border-t border-yellow-900 pt-4 text-center text-xs">
                        <a href="{{ route('games.index') }}"
                            class="text-yellow-600 hover:text-yellow-300 uppercase tracking-wide transition-colors">
                            [Back to Map]
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app>

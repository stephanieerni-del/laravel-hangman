<x-app>
    <x-slot:title>
        Available Games
    </x-slot:title>

    @php
        $levelPoints = [
            ['level' => 1, 'difficulty' => 'easy', 'x' => 13, 'y' => 39],
            ['level' => 2, 'difficulty' => 'easy', 'x' => 16, 'y' => 46],
            ['level' => 3, 'difficulty' => 'easy', 'x' => 22, 'y' => 48],
            ['level' => 4, 'difficulty' => 'easy', 'x' => 29, 'y' => 50],
            ['level' => 5, 'difficulty' => 'easy', 'x' => 24, 'y' => 58],
            ['level' => 6, 'difficulty' => 'medium', 'x' => 25, 'y' => 71],
            ['level' => 7, 'difficulty' => 'medium', 'x' => 32, 'y' => 75],
            ['level' => 8, 'difficulty' => 'medium', 'x' => 36, 'y' => 84],
            ['level' => 9, 'difficulty' => 'medium', 'x' => 44, 'y' => 95],
            ['level' => 10, 'difficulty' => 'medium', 'x' => 52, 'y' => 90],
            ['level' => 11, 'difficulty' => 'hard', 'x' => 59, 'y' => 85],
            ['level' => 12, 'difficulty' => 'hard', 'x' => 54, 'y' => 78],
            ['level' => 13, 'difficulty' => 'hard', 'x' => 47, 'y' => 72],
            ['level' => 14, 'difficulty' => 'hard', 'x' => 52, 'y' => 63],
            ['level' => 15, 'difficulty' => 'hard', 'x' => 58, 'y' => 61],
            ['level' => 16, 'difficulty' => 'extreme', 'x' => 64, 'y' => 57],
            ['level' => 17, 'difficulty' => 'extreme', 'x' => 68, 'y' => 51],
            ['level' => 18, 'difficulty' => 'extreme', 'x' => 71, 'y' => 41],
            ['level' => 19, 'difficulty' => 'extreme', 'x' => 76, 'y' => 35],
            ['level' => 20, 'difficulty' => 'extreme', 'x' => 71, 'y' => 26],
        ];
    @endphp
    <div class="relative min-h-screen w-full overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat blur-[4px] scale-105"
            style="background-image: url('{{ asset('assets/home_plain.png') }}');"></div>
        <div class="relative z-10 mx-auto max-w-6xl text-yellow-100 font-mono">
            <h2 class="text-yellow-800 text-4xl font-bold text-center uppercase tracking-widest mb-1 pt-4">
                &#9876; WORLD MAP &#9876;
            </h2>

            <div class="relative mx-auto w-full max-w-5xl border-4 border-yellow-700 bg-black/70 overflow-hidden"
                style="box-shadow: 8px 8px 0 #000;">
                <img src="{{ asset('assets/map.png') }}" alt="Hangman level map" class="w-full h-auto block select-none"
                    draggable="false" />

                <div class="absolute inset-0">
                    @foreach ($levelPoints as $point)
                        @php
                            $pointClass = match ($point['difficulty']) {
                                'easy' => 'bg-emerald-700 text-emerald-100 border-emerald-300 ring-emerald-200/40',
                                'medium' => 'bg-amber-700 text-amber-100 border-amber-300 ring-amber-200/40',
                                'hard' => 'bg-orange-800 text-orange-100 border-orange-300 ring-orange-200/40',
                                default => 'bg-red-800 text-red-100 border-red-300 ring-red-200/40',
                            };
                        @endphp

                        <button type="button"
                            class="cursor-pointer absolute -translate-x-1/2 -translate-y-1/2 w-8 h-8 md:w-8 md:h-8 border-2 ring-2 rounded-sm rotate-45 {{ $pointClass }} shadow-[2px_2px_0_#000] hover:scale-110 transition-transform"
                            style="left: {{ $point['x'] }}%; top: {{ $point['y'] }}%;"
                            title="Level {{ $point['level'] }} - {{ ucfirst($point['difficulty']) }}">
                            <span
                                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -rotate-45 text-[10px] md:text-xs font-black leading-none">
                                {{ $point['level'] }}
                            </span>
                            <span class="absolute top-[2px] left-[2px] w-1 h-1 bg-yellow-200/70 rounded-full"></span>
                            <span class="absolute top-[2px] right-[2px] w-1 h-1 bg-yellow-200/70 rounded-full"></span>
                            <span class="absolute bottom-[2px] left-[2px] w-1 h-1 bg-yellow-200/70 rounded-full"></span>
                            <span
                                class="absolute bottom-[2px] right-[2px] w-1 h-1 bg-yellow-200/70 rounded-full"></span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- <div class="mt-4 flex flex-wrap gap-2 text-[11px] uppercase tracking-wide">
                <span class="px-2 py-1 border border-emerald-300 bg-emerald-900/40">Easy 1-5</span>
                <span class="px-2 py-1 border border-yellow-300 bg-yellow-900/40">Medium 6-10</span>
                <span class="px-2 py-1 border border-orange-300 bg-orange-900/40">Hard 11-15</span>
                <span class="px-2 py-1 border border-red-300 bg-red-900/40">Extreme 16-20</span>
            </div> --}}

            <div class="mt-6 bg-black/70 border-2 border-yellow-700 p-5 md:p-6">
                <h2 class="text-2xl font-bold uppercase tracking-widest mb-4">My Games</h2>

                <div class="mb-5 text-sm uppercase tracking-wide">
                    <a href="{{ route('games.create') }}" class="text-yellow-400 hover:text-yellow-200">[New Game]</a> |
                    @if ($owned)
                        <a href="{{ route('games.index') }}" class="text-yellow-400 hover:text-yellow-200">[Show All
                            Games]</a>
                    @else
                        <a href="{{ route('games.index', ['owned' => true]) }}"
                            class="text-yellow-400 hover:text-yellow-200">[Show My Games Only]</a>
                    @endif
                </div>

                @forelse ($games as $game)
                    <div class="mb-2">
                        {{ $loop->iteration }}.
                        <a href="{{ route('games.show', compact('game')) }}"
                            class="text-yellow-300 hover:text-yellow-100 underline">
                            {{ $game->name }} {{ $game->creator->is(auth()->user()) ? '*' : '' }}
                        </a>
                    </div>
                @empty
                    <div>No Games</div>
                @endforelse
            </div>
        </div>
    </div>

</x-app>

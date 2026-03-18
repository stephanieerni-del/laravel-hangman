<x-app>
    <style>
        .fade-in {
            opacity: 0;
            animation: fadeInAnimation 2.5s ease forwards;
        }

        @keyframes fadeInAnimation {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .current-level-glow {
            animation: currentLevelGlow 1.50s ease-in-out infinite alternate;
        }

        @keyframes currentLevelGlow {
            from {
                box-shadow: 0 0 0.35rem currentColor;
            }

            to {
                box-shadow: 0 0 2rem currentColor;
            }
        }

        /* .fade-in-delay {
            opacity: 0;
            animation: fadeInAnimation 10s ease forwards;
            animation-delay: 2s;
        } */
    </style>

    <x-slot:title>
        Available Games
    </x-slot:title>

    <div class="relative min-h-screen w-full overflow-hidden fade-in " id="container">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat blur-[4px] scale-105"
            style="background-image: url('{{ asset('assets/home_plain.png') }}');"></div>


        <div class="relative z-10 mx-auto max-w-6xl text-yellow-100 font-mono pt-8">


            <h2 class="text-yellow-800 text-4xl font-bold text-center uppercase tracking-widest mb-1 pt-4">
                &#9876; WORLD MAP &#9876;
            </h2>

            <div class="flex justify-end mb-2 mr-17 gap-3">
                <!-- Open the modal using ID.showModal() method -->
                <button
                    class="cursor-pointer border border-yellow-700 bg-yellow-500 px-5 py-2 hover:bg-yellow-900/40 uppercase tracking-widest text-sm"
                    onclick="my_modal_1.showModal()">open modal</button>
                <dialog id="my_modal_1" class="modal ">
                    <div class="modal-box bg-amber-400/90">
                        <h3 class="text-lg font-bold">Hello, Princess!</h3>
                        <div
                            class="mb-5 border-2 border-yellow-700 bg-yellow-950/35 px-4 py-4 text-sm uppercase tracking-wide">
                            <div class="flex items-center justify-between gap-3 flex-wrap mb-3">
                                <h3 class="text-base font-bold tracking-[0.3em] text-yellow-300">Score Guide</h3>
                                @if (!empty($scoreGuide['level']))
                                    <span class="text-yellow-200">Current Level {{ $scoreGuide['level'] }}</span>
                                @endif
                            </div>

                            <div class="grid gap-3 md:grid-cols-2">
                                @foreach ($scoreGuide['bands'] as $band)
                                    <div class="border px-3 py-3 'border-yellow-700 bg-black/30">
                                        <div class="flex items-center justify-between gap-2 mb-2">
                                            <span class="font-bold text-yellow-200">{{ $band['label'] }}</span>
                                            <span class="text-yellow-400">Levels {{ $band['levels'] }}</span>
                                        </div>
                                        <div class="space-y-1 text-yellow-100/90 normal-case tracking-normal">
                                            <div>Win: {{ $band['points'] }} x lives remaining,
                                                +{{ $scoreGuide['perfect_lives_bonus'] }} if you keep all
                                                {{ $scoreGuide['starting_lives'] }} lives.</div>
                                            <div>Play again: -{{ $band['play_again_penalty'] }} points.</div>
                                            <div>Lose: -{{ $band['lose_penalty'] }} points.</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <p class="mt-3 normal-case tracking-normal text-yellow-200/80">
                                Scores never go below 0.
                            </p>
                        </div>
                        <div class="modal-action">
                            <form method="dialog">
                                <!-- if there is a button in form, it will close the modal -->
                                <button class="btn border border-yellow-900 bg-yellow-400 ">Close</button>
                            </form>
                        </div>
                    </div>
                </dialog>

                <a href="{{ route('leaderboard') }}"
                    class="border border-yellow-700 bg-yellow-500 px-5 py-2 hover:bg-yellow-900/40 uppercase tracking-widest text-sm">
                    Leaderboard
                </a>

                <a href="{{ route('auth.logout') }}"
                    class="border border-yellow-700 bg-yellow-500 px-5 py-2 hover:bg-yellow-900/40 uppercase tracking-widest text-sm">
                    Logout
                </a>
            </div>


            <div class="relative mx-auto w-full max-w-5xl border-4 border-yellow-700 bg-black/70 overflow-hidden"
                style="box-shadow: 8px 8px 0 #000;">
                <img src="{{ asset('assets/map.png') }}" alt="Hangman level map"
                    class="w-full h-auto block select-none" draggable="false" />

                <div class="absolute inset-0">
                    @foreach ($levelPoints as $point)
                        @php
                            $pointClass = match ($point['difficulty']) {
                                'easy' => 'bg-emerald-700 text-emerald-100 border-emerald-300 ring-emerald-200/40',
                                'medium' => 'bg-amber-700 text-amber-100 border-amber-300 ring-amber-200/40',
                                'hard' => 'bg-orange-800 text-orange-100 border-orange-300 ring-orange-200/40',
                                default => 'bg-red-800 text-red-100 border-red-300 ring-red-200/40',
                            };

                            $isDisabled = !$point['has_game'] || $point['is_locked'];
                            $disabledStateClass = !$point['has_game']
                                ? 'bg-zinc-700 text-zinc-300 border-zinc-500 ring-zinc-400/30 opacity-70 cursor-not-allowed'
                                : 'bg-zinc-800 text-zinc-200 border-yellow-400 ring-yellow-300/40 opacity-80 cursor-not-allowed';
                            $stateClass = $isDisabled ? $disabledStateClass : 'cursor-pointer hover:scale-110';
                            $isCurrentLevel = $point['is_current_level'];

                        @endphp

                        @if (!$isDisabled && $point['game_id'])
                            <a href="{{ route('games.show', ['game' => $point['game_id']]) }}"
                                class="absolute -translate-x-1/2 -translate-y-1/2 {{ !$isCurrentLevel ? 'w-9 h-9 md:w-9 md:h-9' : 'w-15 h-15 md:w-15 md:h-15' }} border-2 ring-2 rounded-sm rotate-45 {{ $pointClass . ' ' . $stateClass }} {{ $isCurrentLevel ? 'current-level-glow z-20' : '' }} shadow-[5px_10px_0_#000] transition-transform block"
                                style="left: {{ $point['x'] }}%; top: {{ $point['y'] }}%;"
                                title="Level {{ $point['level'] }} - {{ ucfirst($point['difficulty']) }}{{ $isCurrentLevel ? ' (Current Level)' : '' }}">
                                @if ($isCurrentLevel)
                                    <img src="{{ asset('assets/princess.png') }}"
                                        alt="Avatar for level {{ $point['level'] }}"
                                        class="absolute left-1/2 -translate-x-1/2 -rotate-45 object-contain select-none pointer-events-none h-[40px]"
                                        style="top: -4.25rem; width: 12rem; height: 12rem;" />
                                @endif
                                <span
                                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -rotate-45 text-[10px] md:text-xs font-black leading-none">
                                    {{ $point['level'] }}
                                </span>
                                <span
                                    class="absolute top-[2px] left-[2px] w-1 h-1 bg-yellow-200/70 rounded-full"></span>
                                <span
                                    class="absolute top-[2px] right-[2px] w-1 h-1 bg-yellow-200/70 rounded-full"></span>
                                <span
                                    class="absolute bottom-[2px] left-[2px] w-1 h-1 bg-yellow-200/70 rounded-full"></span>
                                <span
                                    class="absolute bottom-[2px] right-[2px] w-1 h-1 bg-yellow-200/70 rounded-full"></span>
                            </a>
                        @else
                            <button type="button" @disabled($isDisabled)
                                class="absolute -translate-x-1/2 -translate-y-1/2 {{ !$isCurrentLevel ? 'w-9 h-9 md:w-9 md:h-9' : 'w-15 h-15 md:w-15 md:h-15' }} border-2 ring-2 rounded-sm rotate-45 {{ $isDisabled ? $stateClass : $pointClass . ' ' . $stateClass }} {{ $isCurrentLevel ? 'current-level-glow z-20' : '' }} shadow-[5px_10px_0_#000] transition-transform "
                                style="left: {{ $point['x'] }}%; top: {{ $point['y'] }}%;"
                                title="Level {{ $point['level'] }} - {{ ucfirst($point['difficulty']) }}{{ $point['is_locked'] ? ' (Locked)' : (!$point['has_game'] ? ' (Unavailable)' : '') }}{{ $isCurrentLevel ? ' (Current Level)' : '' }}">
                                @if ($isCurrentLevel)
                                    <img src="{{ asset('assets/princess.png') }}"
                                        alt="Avatar for level {{ $point['level'] }}"
                                        class="absolute left-1/2 -translate-x-1/2 -rotate-45 object-contain select-none pointer-events-none h-[40px]"
                                        style="top: -4.25rem; width: 12rem; height: 12rem;" />
                                @endif
                                <span
                                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -rotate-45 text-[10px] md:text-xs font-black leading-none">
                                    {{ $point['level'] }}
                                </span>
                                <span
                                    class="absolute top-[2px] left-[2px] w-1 h-1 bg-yellow-200/70 rounded-full"></span>
                                <span
                                    class="absolute top-[2px] right-[2px] w-1 h-1 bg-yellow-200/70 rounded-full"></span>
                                <span
                                    class="absolute bottom-[2px] left-[2px] w-1 h-1 bg-yellow-200/70 rounded-full"></span>
                                <span
                                    class="absolute bottom-[2px] right-[2px] w-1 h-1 bg-yellow-200/70 rounded-full"></span>
                            </button>
                        @endif
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
                    <a href="{{ route('games.create') }}" class="text-yellow-400 hover:text-yellow-200">[New Game]</a>
                    |
                    @if ($owned)
                        <a href="{{ route('games.index') }}" class="text-yellow-400 hover:text-yellow-200">[Show All
                            Games]</a>
                    @else
                        <a href="{{ route('games.index', ['owned' => true]) }}"
                            class="text-yellow-400 hover:text-yellow-200">[Show My Games Only]</a>
                    @endif
                </div>

                @if ($games->isEmpty())
                    <div>No Games</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border-2 border-yellow-700 text-sm">
                            <thead>
                                <tr class="bg-yellow-900/50 uppercase tracking-wide">
                                    <th class="border border-yellow-700 px-3 py-2 text-left">#</th>
                                    <th class="border border-yellow-700 px-3 py-2 text-left">Game</th>
                                    <th class="border border-yellow-700 px-3 py-2 text-left">Level</th>
                                    <th class="border border-yellow-700 px-3 py-2 text-left">Points</th>
                                    <th class="border border-yellow-700 px-3 py-2 text-left">Difficulty</th>
                                    <th class="border border-yellow-700 px-3 py-2 text-left">Compass Direction</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($games as $game)
                                    @php
                                        $gameLevel = $game->levelPoint?->level;
                                        $isLocked = $gameLevel && $gameLevel > $currentUnlockedLevel;
                                    @endphp

                                    <tr
                                        class="odd:bg-black/20 even:bg-black/40 {{ $isLocked ? 'opacity-70 border-l-4 border-yellow-500' : '' }}">
                                        <td class="border border-yellow-700 px-3 py-2">{{ $loop->iteration }}</td>
                                        <td class="border border-yellow-700 px-3 py-2">
                                            @if ($isLocked)
                                                <span class="text-zinc-300">{{ $game->name }}
                                                    {{ $game->creator->is(auth()->user()) ? '*' : '' }}</span>
                                                <span
                                                    class="ml-2 text-[10px] md:text-xs uppercase tracking-wide text-yellow-400 border border-yellow-500 px-1 py-0.5">Locked</span>
                                            @else
                                                <a href="{{ route('games.show', compact('game')) }}"
                                                    class="text-yellow-300 hover:text-yellow-100 underline">
                                                    {{ $game->name }}
                                                    {{ $game->creator->is(auth()->user()) ? '*' : '' }}
                                                </a>
                                            @endif
                                        </td>
                                        <td class="border border-yellow-700 px-3 py-2">
                                            {{ $game->levelPoint?->level ?? '-' }}</td>
                                        <td class="border border-yellow-700 px-3 py-2">
                                            {{-- {{ isset($completedGameIds[$game->id]) ? $game->levelPoint?->level ?? '-' : '-' }} --}}
                                            {{ isset($game->gamers()->where('user_id', auth()->id())->latest()->first()->player->score)? $game->gamers()->where('user_id', auth()->id())->latest()->first()->player->score: '-' }}

                                        </td>
                                        <td class="border border-yellow-700 px-3 py-2 uppercase">
                                            {{ $game->levelPoint?->difficulty ?? '-' }}
                                        </td>
                                        <td class="border border-yellow-700 px-3 py-2">
                                            @if ($game->levelPoint)
                                                @php
                                                    $xOffset = $game->levelPoint->x - 50;
                                                    $yOffset = $game->levelPoint->y - 50;
                                                    $bearing = fmod(rad2deg(atan2($xOffset, -$yOffset)) + 360, 360);

                                                    if ($bearing >= 315 || $bearing < 45) {
                                                        $direction = 'North';
                                                    } elseif ($bearing >= 45 && $bearing < 135) {
                                                        $direction = 'East';
                                                    } elseif ($bearing >= 135 && $bearing < 225) {
                                                        $direction = 'South';
                                                    } else {
                                                        $direction = 'West';
                                                    }
                                                @endphp
                                                {{ $direction }} ({{ number_format($bearing, 0) }}°)
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-app>

<x-app>
    <x-slot:title>
        {{ $stage->challenge->category }}
    </x-slot:title>

    @php
        $isMaxLevelComplete = $isMaxLevelComplete ?? false;
        $difficulty = strtolower($game->levelPoint?->difficulty ?? 'easy');
        $difficultyBackgrounds = [
            'easy' => asset('assets/easy.png'),
            'medium' => asset('assets/medium.png'),
            'hard' => asset('assets/hard.png'),
            'extreme' => asset('assets/extreme.png'),
        ];
        $selectedDifficulty = array_key_exists($difficulty, $difficultyBackgrounds) ? $difficulty : 'easy';
        $topGamers = $game->getTopGamers();
        $wrongGuesses = $stage->getGuesses()->count() - collect($stage->correct_guesses ?? [])->count();

        $wrongGuessAnimation = null;
        if ($wrongGuesses >= 1 && $stage->lives > 0) {
            if ($stage->lives === 1) {
                $wrongGuessAnimation = asset('assets/hanggirl-1life.gif');
            } elseif ($stage->lives === 2) {
                $wrongGuessAnimation = asset('assets/hanggirl-2lives.gif');
            } else {
                $wrongGuessAnimation = asset('assets/hanggirl.gif');
            }
        } else {
            $wrongGuessAnimation = asset('assets/princess.png');
        }
    @endphp


    <div class="relative min-h-screen w-full overflow-hidden" id="container">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat blur-[4px] scale-105" id="difficulty-bg"
            data-backgrounds='@json($difficultyBackgrounds)'
            style="background-image: url('{{ $difficultyBackgrounds[$selectedDifficulty] }}');"></div>

        @if ($isMaxLevelComplete)
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat blur-[4px] scale-105 escaped-bg-fade"
                style="background-image: url('{{ asset('assets/escaped_home.png') }}');"></div>
        @endif

        <div
            class="relative z-10 mx-auto max-w-6xl text-yellow-100 font-mono px-4 py-5 min-h-screen flex flex-col justify-center">

            <div id="game-content" class="{{ $isMaxLevelComplete ? 'game-content-fadeout' : '' }}">
                <h2 class="text-yellow-800 text-4xl font-bold text-center uppercase tracking-widest mb-6">
                    {{ $game->name }}
                </h2>


                <div class="mb-6 min-h-48 md:min-h-48 flex items-center justify-center">
                    @if ($stage->isCompleted())
                        <div class="win-photo-loop flex flex-col items-center gap-2">
                            <img src="{{ asset('assets/congrats.png') }}" alt="Completed"
                                class="mx-auto max-h-52 md:max-h-28 w-auto">
                            <img src="{{ asset('assets/you-win.png') }}" alt="Completed"
                                class="mx-auto max-h-44 md:max-h-28 w-auto">
                        </div>
                    @elseif ($stage->lives <= 0)
                        <img src="{{ asset('assets/hanggirl-nolife.gif') }}" alt="No Lives"
                            class="mx-auto max-h-52 md:max-h-64 w-auto">
                    @elseif ($wrongGuessAnimation)
                        <img src="{{ $wrongGuessAnimation }}" alt="Wrong Guess"
                            class="mx-auto max-h-52 md:max-h-64 w-auto">
                    @endif

                </div>

                <div class="bg-black/70 border-2 border-yellow-700 p-4 md:p-6 text-xl"
                    style="box-shadow: 8px 8px 0 #000;">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-5">
                        <div class="w-full space-y-1 text-xl uppercase tracking-wide">
                            <div class="w-full text-right">
                                <div class="flex items-center justify-end gap-1 flex-wrap mb-1">
                                    Lives:
                                    @for ($i = 0; $i < $stage->lives; $i++)
                                        <span class="life-icon text-2xl"
                                            style="animation-delay: {{ $i * 0.13 }}s">❤️</span>
                                    @endfor
                                    @for ($i = $stage->lives; $i < $game->starting_lives; $i++)
                                        <span class="text-2xl" style="opacity:0.35;">🤍</span>
                                    @endfor
                                </div>
                                <div>Score: <span class="text-yellow-300">{{ $stage->player->score }}</span></div>
                            </div>
                            @if (!$stage->isFailed())
                                <div>Category: <span class="text-yellow-300">{{ $stage->challenge->category }}</span>
                                </div>

                                @if ($stage->challenge->description)
                                    <div class="normal-case tracking-normal text-yellow-200/90">
                                        Hint: <span id="hint-text">{{ $stage->challenge->description }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    @if (!$stage->isFailed())
                        <div
                            class="mb-5 border-2 border-yellow-700 bg-black/60 px-4 py-5 text-center text-3xl md:text-4xl tracking-[0.35em] uppercase">
                            {{ $stage }}
                        </div>
                    @endif

                    <form id="guess-form" method="post" action="{{ route('games.update', compact('game')) }}"
                        class="space-y-4">
                        @method('put')
                        @csrf

                        @error('guess')
                            <div class="border border-red-500 bg-red-900/30 px-3 py-2 text-red-200">{{ $message }}
                            </div>
                        @enderror

                        @if (!$stage->isOver())
                            <x-keyboard :disabled-keys="$disabledKeys" />
                        @elseif ($stage->lives <= 0)
                            <div class="flex items-center justify-center py-2">
                                <img src="{{ asset('assets/gameover-youlose.gif') }}" alt="Game Over"
                                    class="mx-auto max-h-52 md:max-h-64 w-auto">
                            </div>
                        @endif

                        <div
                            class="flex items-center justify-between pt-4 border-t border-yellow-700 text-sm uppercase tracking-wide flex-wrap gap-2">
                            <div class="min-w-32">
                                @if ($stage->isOver())
                                    <button type="submit" form="next"
                                        class="border border-yellow-700 px-3 py-2 hover:bg-yellow-900/40">
                                        Play again! [Space]
                                    </button>
                                @else
                                    <span class="inline-block border border-transparent px-3 py-2 invisible">Play again
                                        !</span>
                                @endif
                            </div>

                            <a id="back-to-map-link" href="{{ route('games.index') }}"
                                class="inline-flex items-center border border-yellow-700 px-3 py-2 hover:bg-yellow-900/40">
                                Back to Map [Esc]
                            </a>
                        </div>

                    </form>

                    <form id="next" method="get" action="{{ route('games.show', compact('game')) }}">
                        <input type="hidden" name="next" value="true" />
                    </form>
                </div>
            </div>{{-- end #game-content --}}

            @if ($isMaxLevelComplete && $leaderboard && $leaderboard->isNotEmpty())
                <div class="leaderboard-reveal flex flex-col items-center">
                    <div class="win-photo-loop flex flex-col items-center gap-2 mb-6">
                        <img src="{{ asset('assets/congrats.png') }}" alt="Congratulations"
                            class="mx-auto max-h-52 md:max-h-28 w-auto">
                        <img src="{{ asset('assets/you-win.png') }}" alt="You Win"
                            class="mx-auto max-h-44 md:max-h-28 w-auto">
                    </div>
                    <div class="w-full max-w-xl bg-black/70 border-2 border-yellow-700 p-4 md:p-6"
                        style="box-shadow: 8px 8px 0 #000;">
                        <h3 class="text-2xl font-bold uppercase tracking-widest text-center text-yellow-300 mb-4">
                            Leaderboard</h3>
                        <ol class="space-y-2">
                            @foreach ($leaderboard as $index => $entry)
                                <li
                                    class="flex items-center justify-between border border-yellow-700 bg-black/40 px-4 py-3 text-sm uppercase tracking-wide">
                                    <span class="flex items-center gap-3">
                                        <span
                                            class="text-yellow-500 font-bold w-6 text-right">{{ $index + 1 }}.</span>
                                        <span>{{ $entry->name }}</span>
                                    </span>
                                    <span class="text-yellow-300 font-bold">{{ $entry->total_score }}
                                        pt{{ $entry->total_score != 1 ? 's' : '' }}</span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('games.index') }}"
                            class="inline-flex items-center border border-yellow-700 px-5 py-2 hover:bg-yellow-900/40 uppercase tracking-widest text-sm">
                            Back to Map
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>

    <style>
        .win-photo-loop {
            animation: winPhotoLoop 2.2s ease-in-out infinite;
            will-change: opacity, transform;
            transform-origin: center center;
        }

        @keyframes winPhotoLoop {
            0% {
                opacity: 0.35;
                transform: translateX(-12px) scale(0.96);
            }

            50% {
                opacity: 1;
                transform: translateX(0) scale(1.06);
            }

            100% {
                opacity: 0.35;
                transform: translateX(12px) scale(0.96);
            }
        }

        /* Background slowly fades in after 2s, over 4s */
        .escaped-bg-fade {
            opacity: 0;
            animation: escapedBgFade 4s ease-in-out 2s forwards;
        }

        @keyframes escapedBgFade {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Game content fades out starting at 3s over 2s, then collapses */
        .game-content-fadeout {
            animation: gameContentFade 2s ease-in-out 3s forwards;
        }

        @keyframes gameContentFade {
            0% {
                opacity: 1;
                transform: scale(1);
                max-height: 2000px;
                overflow: hidden;
            }

            99% {
                opacity: 0;
                transform: scale(0.97);
                max-height: 2000px;
                overflow: hidden;
            }

            100% {
                opacity: 0;
                transform: scale(0.97);
                max-height: 0;
                overflow: hidden;
                visibility: hidden;
                padding: 0;
                margin: 0;
            }
        }

        .life-icon {
            display: inline-block;
            transform-origin: center;
            animation: heartbeat 1.5s ease-in-out infinite;
        }

        @keyframes heartbeat {

            0%,
            100% {
                transform: scale(1);
            }

            15% {
                transform: scale(1.30);
            }

            30% {
                transform: scale(1);
            }

            45% {
                transform: scale(1.18);
            }

            60% {
                transform: scale(1);
            }
        }

        .hint-cursor {
            display: inline-block;
            font-weight: 300;
            color: #fde68a;
            /* animation: blink 0.75s step-start infinite; */
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }
        }

        /* Leaderboard slides up and fades in after game content is gone (~5.5s) */
        .leaderboard-reveal {
            opacity: 0;
            transform: translateY(40px);
            animation: leaderboardReveal 1s cubic-bezier(0.22, 1, 0.36, 1) 5.5s forwards;
        }

        @keyframes leaderboardReveal {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        (() => {
            const selector = document.getElementById('difficulty-theme');
            const background = document.getElementById('difficulty-bg');
            if (!selector || !background) {
                return;
            }

            const backgrounds = JSON.parse(background.dataset.backgrounds || '{}');
            selector.addEventListener('change', (event) => {
                const selected = event.target.value;
                if (backgrounds[selected]) {
                    background.style.backgroundImage = `url('${backgrounds[selected]}')`;
                }
            });
        })();

        (() => {
            const guessForm = document.getElementById('guess-form');
            if (!guessForm) {
                return;
            }

            document.addEventListener('keydown', (event) => {
                if (event.defaultPrevented || event.ctrlKey || event.metaKey || event.altKey) {
                    return;
                }

                const target = event.target;
                if (target instanceof HTMLElement) {
                    const editable = target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target
                        .isContentEditable;
                    if (editable) {
                        return;
                    }
                }

                const key = (event.key || '').toLowerCase();

                if (key === ' ' || key === 'spacebar' || event.code === 'Space') {
                    const playAgainButton = document.querySelector('button[form="next"]:not([disabled])');
                    if (playAgainButton) {
                        event.preventDefault();
                        playAgainButton.click();
                    }
                    return;
                }

                if (key === 'escape' || event.code === 'Escape') {
                    const backToMapLink = document.getElementById('back-to-map-link');
                    if (backToMapLink) {
                        event.preventDefault();
                        backToMapLink.click();
                    }
                    return;
                }

                if (!/^[a-z]$/.test(key)) {
                    return;
                }

                const button = guessForm.querySelector(`button[name="guess"][value="${key}"]:not([disabled])`);
                if (!button) {
                    return;
                }

                event.preventDefault();
                button.click();
            });
        })();

        // (() => {
        //     const el = document.getElementById('hint-text');
        //     const cursor = document.getElementById('hint-cursor');
        //     if (!el) return;
        //     const text = el.dataset.text || '';
        //     let i = 0;
        //     const speed = 30;

        //     function type() {
        //         if (i < text.length) {
        //             el.textContent += text.charAt(i++);
        //             setTimeout(type, speed);
        //         } else {
        //             if (cursor) {
        //                 cursor.style.animation = 'none';
        //                 cursor.style.opacity = '0';
        //             }
        //         }
        //     }
        //     setTimeout(type, 400);
        // })();
    </script>
</x-app>

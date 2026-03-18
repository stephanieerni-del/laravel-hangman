<x-app>
    
    <x-slot:title>Leaderboard</x-slot:title>

    <div class="relative min-h-screen w-full overflow-hidden">
        <div class="leaderboard-bg absolute inset-0 bg-cover bg-center bg-no-repeat blur-[4px] scale-105"
            style="background-image: url('{{ asset('assets/escaped_home.png') }}');"></div>

        <div class="leaderboard-content relative z-10 mx-auto max-w-3xl text-yellow-100 font-mono px-4 py-10">

            <div class="win-photo-loop flex flex-col items-center gap-2 mb-8">
                <img src="{{ asset('assets/congrats.png') }}" alt="Completed"
                    class="mx-auto max-h-52 md:max-h-28 w-auto">
            </div>

            <div class="bg-black/70 border-2 border-yellow-700 p-4 md:p-8" style="box-shadow: 8px 8px 0 #000;">
                <h2 class="text-3xl font-bold uppercase tracking-widest text-center text-yellow-300 mb-6">
                    Leaderboard
                </h2>

                @if ($leaderboard->isEmpty())
                    <p class="text-center uppercase tracking-wide text-yellow-400">No scores yet.</p>
                @else
                    <ol class="space-y-2">
                        @foreach ($leaderboard as $index => $entry)
                            <li class="flex items-center justify-between border border-yellow-700 bg-black/40 px-4 py-3">
                                <span class="flex items-center gap-3">
                                    <span class="text-yellow-500 font-bold w-6 text-right">{{ $index + 1 }}.</span>
                                    <span class="uppercase tracking-wide">{{ $entry->name }}</span>
                                </span>
                                <span class="text-yellow-300 font-bold">{{ $entry->total_score }} pt{{ $entry->total_score != 1 ? 's' : '' }}</span>
                            </li>
                        @endforeach
                    </ol>
                @endif

                <div class="mt-8 flex justify-center">
                    <a href="{{ route('games.index') }}"
                        class="border border-yellow-700 px-5 py-2 hover:bg-yellow-900/40 uppercase tracking-widest text-sm">
                        Back to Map
                    </a>
                </div>
            </div>

        </div>
    </div>

    <style>
        .leaderboard-bg {
            opacity: 0;
            animation: leaderboardBgFadeIn 0.8s ease-out forwards;
        }

        .leaderboard-content {
            opacity: 0;
            animation: leaderboardContentFadeIn 0.45s ease-out 0.35s forwards;
        }

        @keyframes leaderboardBgFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes leaderboardContentFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

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
    </style>
</x-app>

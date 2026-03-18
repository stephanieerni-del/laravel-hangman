<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - {{ $title }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/title.png ') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen" data-theme="light">

    {{-- Background music --}}
    <audio id="bg-music" loop preload="auto">
        <source src="{{ asset('assets/audio/bg_music.mp3') }}" type="audio/mpeg">
    </audio>

    {{-- Mute / Unmute button --}}
    <button id="music-toggle" title="Toggle music"
        class="fixed top-4 right-4 z-50 w-20 h-20 flex items-center justify-center bg-black/70 border-2 border-yellow-700 hover:border-yellow-400 text-yellow-400 hover:text-yellow-200 transition-colors font-mono"
        style="box-shadow: 2px 2px 0 #000;">
        {{-- Speaker ON icon --}}
        <svg id="icon-sound-on" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor"
            viewBox="0 0 24 24">
            <path
                d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z" />
        </svg>
        {{-- Speaker MUTED icon --}}
        <svg id="icon-sound-off" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="currentColor"
            viewBox="0 0 24 24">
            <path
                d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z" />
        </svg>
    </button>

    <script>
        (function() {
            const audio = document.getElementById('bg-music');
            const btn = document.getElementById('music-toggle');
            const iconOn = document.getElementById('icon-sound-on');
            const iconOff = document.getElementById('icon-sound-off');
            const TIME_KEY = 'bgMusicTime';
            const MUTED_KEY = 'bgMusicMuted';

            let muted = localStorage.getItem(MUTED_KEY) === 'true';
            let hasRestoredTime = false;

            function applyState() {
                audio.muted = muted;
                iconOn.classList.toggle('hidden', muted);
                iconOff.classList.toggle('hidden', !muted);
            }

            function restorePlaybackPosition() {
                if (hasRestoredTime || !Number.isFinite(audio.duration) || audio.duration <= 0) {
                    return;
                }

                const saved = parseFloat(localStorage.getItem(TIME_KEY) || '0');
                if (!Number.isFinite(saved) || saved <= 0) {
                    hasRestoredTime = true;
                    return;
                }

                const maxSeek = Math.max(0, audio.duration - 0.5);
                audio.currentTime = Math.min(saved, maxSeek);
                hasRestoredTime = true;
            }

            // Attempt autoplay on first interaction or page load
            function startMusic() {
                restorePlaybackPosition();
                audio.play().catch(() => {});
            }

            function persistPlaybackPosition() {
                if (!audio.paused && Number.isFinite(audio.currentTime)) {
                    localStorage.setItem(TIME_KEY, String(audio.currentTime));
                }
            }

            applyState();

            audio.addEventListener('loadedmetadata', restorePlaybackPosition, {
                once: true
            });

            audio.addEventListener('canplay', restorePlaybackPosition, {
                once: true
            });

            // If metadata is already available, restore immediately.
            restorePlaybackPosition();

            audio.addEventListener('timeupdate', persistPlaybackPosition);

            // Extra backup in case timeupdate does not fire near navigation time.
            setInterval(persistPlaybackPosition, 750);

            audio.addEventListener('ended', function() {
                localStorage.setItem(TIME_KEY, '0');
            });

            window.addEventListener('beforeunload', persistPlaybackPosition);
            window.addEventListener('pagehide', persistPlaybackPosition);

            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'hidden') {
                    persistPlaybackPosition();
                }
            });

            // Try to autoplay immediately; browsers may block this until interaction
            startMusic();

            // Fallback: start on first user interaction if autoplay was blocked
            document.addEventListener('click', function onFirstClick() {
                startMusic();
                document.removeEventListener('click', onFirstClick);
            }, {
                once: true
            });

            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                muted = !muted;
                localStorage.setItem(MUTED_KEY, String(muted));
                applyState();
                if (!muted) startMusic();
            });
        })();
    </script>

    {{-- <h1>{{ config('app.name') }}</h1> --}}
    {{-- <div>
        @guest
            <a href="{{ route('login') }}">[Login]</a>
        @endguest
        @auth
            <a href="{{ route('auth.logout') }}">[Logout]</a>
        @endauth
    </div> --}}
    {{-- <hr /> --}}
    {{ $slot }}
</body>

</html>

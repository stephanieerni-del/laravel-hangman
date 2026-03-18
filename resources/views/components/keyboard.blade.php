<div class="w-full space-y-2">
    @foreach($keygroups as $keys)
        <div class="w-full flex items-center gap-2">
            @foreach ($keys as $key)
                <button type="submit" name="guess" value="{{ strtolower($key) }}"
                    @disabled(is_array($disabledKeys) ? in_array(strtolower($key), $disabledKeys) : $disabledKeys)
                    class="cursor-pointer flex-1 min-h-22 border border-amber-700 bg-cover bg-center bg-no-repeat text-3xl text-amber-900 font-black uppercase tracking-wider hover:brightness-150 disabled:opacity-60 disabled:text-zinc-300 disabled:border-zinc-600 disabled:cursor-not-allowed"
                    style="background-image: url('{{ asset('assets/key.png') }}');"
                >
                    {{ $key }}
                </button>
            @endforeach
        </div>
    @endforeach
</div>
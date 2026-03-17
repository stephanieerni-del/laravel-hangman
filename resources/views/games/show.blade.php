<x-app>
    <x-slot:title>
        {{ $stage->challenge->category }}
    </x-slot:title>
    
    <h2>{{ $game->name }}</h2>

    @if($stage->isCompleted())
        <div>Congratulations!</div>
        <form method="get" action="{{ route('games.show', ['game' => $game->id, 'next' => 'true']) }}" style="margin-top: 1em;">
       
        </form>
    @elseif ($stage->isFailed())
        <div>You failed! The word we are looking for is {{ $stage->challenge->word }}</div>
       
    @endif


    <div>
        <strong>Current Level:</strong> {{ $stage->player->level ?? 'N/A' }}
    </div>
    <div>
        Score: {{ $stage->player->score }}
    </div>

    <div>
        Category: {{ $stage->challenge->category }}
    </div>
    <div>
        Remaining Lives: {{ $stage->lives }}
    </div>
    <div>
        {{ $stage }}
    </div>
    <br />
    <div>
        <form method="post" action="{{ route('games.update', compact('game')) }}">
            @method('put')
            @csrf

            @error('guess')
                <div>{{ $message }}</div>
            @enderror
            <x-keyboard :disabled-keys="$disabledKeys"/>

            <div>
                <hr/>

                @if($stage->isCompleted())
                    @if(($stage->player->level ?? 0) >= 20)
                        <a href="{{ route('games.index') }}" class="btn btn-primary">Return to main menu</a>
                    @else
                        <button type="submit" form="next">Next level</button>
                    @endif
                @endif

                 @if($stage->isFailed())
            <div style="margin-top: 1em;">
               
                    
                    <button type="submit" form="next">Try Again</button>
                    <a href="{{ route('games.index') }}">Main Menu</a>
            </div>
        @endif
                @if(!$stage->isOver())
                    <button type="submit" name="skip" value=true>
                        Skip stage
                    </button>
                @else
                    @if(($stage->player->level ?? 0) < 20)
                        <button type="submit" form="next">Next stage</button>
                    @endif
                @endif
            </div>
        </form>
        <form id="next" method="get" action="{{ route('games.show', compact('game')) }}">
            <input type="hidden" name="next" value="true" />
        </form>
    </div>
    @php $topGamers = $game->getTopGamers() @endphp
    
    @if($topGamers->isNotEmpty())
    <hr/>
    <div>
        <h2>Top Players</h2>
        <ol>
            @foreach ($topGamers as $gamer)
            <li>
                {{ $gamer->name }} - {{ $gamer->player->score }} point(s)
            </li>
            @endforeach
        </ol>
    </div>
    @endif
</x-app>
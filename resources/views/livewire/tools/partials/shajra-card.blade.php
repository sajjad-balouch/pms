@php
    /** Patwar ke shajre ka ek naam: aurat (beti/behan) par chhat, zauja patte ki shakal mein, baqi sirf naam */
    $node = $card['node'];
    $role = $card['role'];
    $isRoot = $role === 'root';
    $w = $card['w'];
    $info = $result['nodes'][$node['id']] ?? ['share' => 0, 'fraction' => '0', 'basis' => '', 'state' => $role === 'display' ? 'display' : 'no_share'];
    $state = $isRoot ? 'estate' : $info['state'];
    // Har biwi aur har maan: patta. Beti/behan: chhat. Baqi: sirf naam
    $shape = match (true) {
        in_array($node['relation'], ['wife', 'mother'], true) => 'lens',
        in_array($node['relation'], ['daughter', 'sister', 'paternal_sister'], true) => 'roof',
        default => 'plain',
    };
    $facing = $card['facing'] ?? 'right';
    // Neeche se aane wali line naam tak pohnche
    $stub = $shape === 'plain' && (in_array($role, ['lineage', 'sibling'], true) || ($isRoot && $hasAncestors));
    $canAdd = $isRoot || $node['status'] === 'died_after' || ($node['status'] === 'died_before' && in_array($node['relation'], ['son', 'daughter']));
    $muted = in_array($state, ['blocked', 'predeceased', 'divorced', 'invalid', 'no_share', 'too_deep']);
@endphp
<div class="pw-node {{ $muted ? 'pw-muted' : '' }} {{ $isRoot ? 'pw-root' : '' }} {{ $shape === 'lens' ? 'pw-leaf' : '' }}"
     style="left: {{ round($card['x'], 1) }}px; top: {{ round($card['y'], 1) }}px; width: {{ $w }}px; height: {{ $card['h'] }}px;"
     wire:key="card-{{ $node['id'] }}">
    @if($shape === 'lens' || $stub)
        {{-- Patta (zauja) kaghazi rang se bhara: neeche se guzarti nikah ki line chhup jati hai --}}
        <svg class="pw-shape" width="{{ $w }}" height="56" aria-hidden="true">
            @if($shape === 'lens')
                @php
                    // Patta: sirf khaaka, andar kuch nahi. Gol daaman shohar ki taraf (nikah ki line dandi), sira nok-daar
                    $mid = round($w * 0.42, 1); $sh = round($w * 0.72, 1);
                    $leaf = "M 8,30 C 9,18 26,13 {$mid},13 C {$sh},13 " . ($w - 18) . ",20 " . ($w - 2) . ",28 "
                        . "C " . ($w - 18) . ",40 {$sh},47 {$mid},47 C 26,47 9,42 8,30 Z";
                @endphp
                <g @if($facing === 'left') transform="translate({{ $w }},0) scale(-1,1)" @endif>
                    <path d="M 0,30 Q 4,30.5 8,30" fill="none" stroke-width="2.2" stroke-linecap="round"/>
                    <path class="pw-leaf-body" d="{{ $leaf }}" stroke-width="2" stroke-linejoin="round"/>
                </g>
            @else
                <line x1="{{ $w / 2 }}" y1="0" x2="{{ $w / 2 }}" y2="15" stroke-width="2"/>
            @endif
        </svg>
    @endif
    <div class="pw-name" dir="auto" title="{{ $rel($node, $card) }}"><span>{{ $node['name'] ?: '—' }}</span></div>
    @if($shape === 'roof')
        <svg class="pw-shape" width="{{ $w }}" height="56" aria-hidden="true">
            <polyline points="12,40 {{ $w / 2 }},1 {{ $w - 12 }},40" fill="none" stroke-width="2.2" stroke-linejoin="round"/>
        </svg>
    @endif
    <div class="pw-sign">
        {{-- Saath wale (side) rishtedar ka rishta naam ke neeche, taa-ke pata chale kaun hai --}}
        @if(in_array($role, ['side', 'sibling']))<span class="pw-caption">{{ \Illuminate\Support\Str::before($rel($node, $card), ' (') }}</span>@endif
        {{ implode(' · ', $signsOf($node, $state)) }}
    </div>
    @if($showShares)
        @if($isRoot)
            <div class="pw-share" dir="ltr">{{ (int) $record['kanal'] }}-{{ (int) $record['marla'] }}-{{ $fmt($record['sarsahi'], 2) }}</div>
        @elseif($info['share'] > 0)
            <div class="pw-share" dir="ltr">{{ $info['fraction'] }}</div>
        @endif
    @endif

    @if($card['list'])
        <div class="pw-list">
            @foreach($card['list'] as $row)
                @php $ri = $result['nodes'][$row['node']['id']] ?? ['share' => 0, 'fraction' => '0', 'state' => '']; @endphp
                <div class="pw-list-row" style="padding-inline-start: {{ $row['level'] * 8 }}px">
                    <span class="pw-list-rel">{{ $rel($row['node'], null) }}:</span>
                    <span dir="auto">{{ $row['node']['name'] ?: '—' }}</span>
                    <span class="pw-list-sign">{{ implode(' · ', $signsOf($row['node'], $ri['state'])) }}</span>
                    @if($showShares && $ri['share'] > 0)<span class="pw-list-share" dir="ltr">{{ $ri['fraction'] }}</span>@endif
                </div>
            @endforeach
        </div>
    @endif

    @if($role !== 'display')
        <div class="pw-actions no-print">
            @if($canAdd)
                <button type="button" wire:click="addUnder('{{ $node['id'] }}')" title="{{ __('Add heir under this person') }}"><i class="fa-solid fa-plus"></i></button>
            @endif
            @unless($isRoot)
                <button type="button" class="pw-del" wire:click="removePerson('{{ $node['id'] }}')" wire:confirm="{{ __('Remove this person and everyone added under them?') }}" title="{{ __('Remove') }}"><i class="fa-solid fa-xmark"></i></button>
            @endunless
        </div>
    @endif
</div>

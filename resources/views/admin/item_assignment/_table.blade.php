@if(count($finishedItems) && count($rawMaterials))
<div class="table-responsive">
    <table class="table table-bordered align-middle mb-0" style="font-size:.8125rem">
        <thead>
            <tr>
                <th class="px-3 py-3 text-center" style="width:200px">Finished &nbsp;\&nbsp; Raw</th>
                @foreach($rawMaterials as $r)
                <th class="px-3 py-3 text-center text-nowrap">
                    <span class="fw-bold" style="color:#d97706">{{ $r->short_code }}</span>
                    <span class="text-secondary ms-1" style="font-size:.6875rem;font-weight:500">{{ $r->item_name }}</span>
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($finishedItems as $f)
            @php $fAssigned = $assignments[$f->id] ?? []; @endphp
            <tr>
                <td class="px-3 py-3">
                    <span class="fw-bold text-dark">{{ $f->item_name }}</span>
                    <span class="badge bg-light text-success border border-success" style="font-size:.625rem;font-weight:700">{{ $f->short_code }}</span>
                </td>
                @foreach($rawMaterials as $r)
                @php $cell = $fAssigned[$r->id] ?? null; @endphp
                <td class="px-3 py-3 text-center text-secondary">
                    @if($cell)
                        {{ $cell['value'] }}@if(!empty($cell['unit_name'])) <span class="fw-bold text-dark" style="font-size:.6875rem">{{ $cell['unit_name'] }}</span>@endif
                    @else
                        —
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="p-5 text-center text-secondary" style="font-size:.875rem">Both finished and raw items are required to show the matrix.</div>
@endif

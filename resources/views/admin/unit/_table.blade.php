<table class="table table-hover align-middle mb-0" style="font-size:.8125rem">
    <thead>
        <tr class="text-uppercase text-secondary" style="font-size:.6875rem;letter-spacing:.05em;font-weight:700;background:#f8fafc">
            <th class="px-4 py-3 text-center" style="width:50px">#</th>
            <th class="px-4 py-3">Unit Name</th>
            <th class="px-4 py-3">Sub Unit</th>
            <th class="px-4 py-3 text-center">Conversion</th>
            <th class="px-4 py-3 text-center" style="width:100px">Status</th>
            <th class="px-4 py-3 text-center" style="width:80px">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($units as $unit)
        <tr>
            <td class="px-4 text-center text-secondary">{{ $units->firstItem() + $loop->index }}</td>
            <td class="px-4">
                <span class="fw-bold text-dark">{{ $unit->unit_name }}</span>
            </td>
            <td class="px-4">{{ $unit->sub_unit ?? '—' }}</td>
            <td class="px-4 text-center">
                @if($unit->sub_unit && $unit->subunit_value)
                    <span class="badge bg-light text-dark fs-7">1 {{ $unit->unit_name }} = {{ $unit->subunit_value }} {{ $unit->sub_unit }}</span>
                @else
                    <span class="text-secondary">—</span>
                @endif
            </td>
            <td class="px-4 text-center">
                <span class="status-badge {{ $unit->status }}" onclick="toggleStatus(this, {{ $unit->id }})">{{ ucfirst($unit->status) }}</span>
            </td>
            <td class="px-4 text-center">
                <div class="d-flex gap-1 justify-content-center">
                    <a href="{{ route('admin.unit.edit', $unit->id) }}" class="action-btn edit" title="Edit">
                        <i class="bi bi-pencil" style="font-size:.75rem"></i>
                    </a>
                    <button type="button" class="action-btn delete" title="Delete" onclick="confirmDelete({{ $unit->id }})">
                        <i class="bi bi-trash" style="font-size:.75rem"></i>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="px-4 py-5 text-center text-secondary">No units found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

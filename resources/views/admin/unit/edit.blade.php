@extends('admin.layouts.app')
@section('page_title', 'Edit Unit')
@section('content')
<div class="data-card">
    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
        <a href="{{ route('admin.unit.index') }}" class="btn btn-light btn-sm d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:.5rem;border:1px solid #e2e8f0;padding:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="card-section-title mb-0"><i class="bi bi-pencil"></i> Edit Unit</h5>
    </div>
    <div class="p-4">
        <form action="{{ route('admin.unit.update', $unit->id) }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Unit Name <span class="text-danger">*</span></label>
                    <input type="text" name="unit_name" class="form-control @error('unit_name') is-invalid @enderror" placeholder="e.g. Pcs, Kg, Meter" value="{{ old('unit_name', $unit->unit_name) }}">
                    @error('unit_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sub Unit</label>
                    <input type="text" name="sub_unit" class="form-control @error('sub_unit') is-invalid @enderror" placeholder="e.g. gm, ml, mm" value="{{ old('sub_unit', $unit->sub_unit) }}">
                    @error('sub_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Subunit Value</label>
                    <input type="number" name="subunit_value" class="form-control @error('subunit_value') is-invalid @enderror" placeholder="e.g. 1000" value="{{ old('subunit_value', $unit->subunit_value) }}" step="any">
                    <small class="text-muted">1 Unit = ? Sub Unit (e.g. 1 Kg = 1000 gm)</small>
                    @error('subunit_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.unit.index') }}" class="btn btn-light px-4" style="border-radius:.75rem;font-weight:600;border:1px solid #e2e8f0">Cancel</a>
                <button type="submit" class="btn btn-primary px-4" style="border-radius:.75rem;font-weight:600">Update Unit</button>
            </div>
        </form>
    </div>
</div>
@endsection

@php $m = $mukhi_info ?? null; @endphp
<div class="card rounded-0">
    <div class="card-body">
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Mukhi Number') }} <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="number" min="1" name="mukhi_number" value="{{ old('mukhi_number', $m->mukhi_number ?? '') }}" class="form-control rounded-0" required>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Ruling Deity') }}</label>
            <div class="col-md-9"><input type="text" name="deity" value="{{ old('deity', $m->deity ?? '') }}" class="form-control rounded-0"></div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Ruling Planet') }}</label>
            <div class="col-md-9"><input type="text" name="planet" value="{{ old('planet', $m->planet ?? '') }}" class="form-control rounded-0"></div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Beej Mantra') }}</label>
            <div class="col-md-9"><input type="text" name="mantra" value="{{ old('mantra', $m->mantra ?? '') }}" class="form-control rounded-0"></div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Chakra') }}</label>
            <div class="col-md-9"><input type="text" name="chakra" value="{{ old('chakra', $m->chakra ?? '') }}" class="form-control rounded-0"></div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Auspicious Wearing Day') }}</label>
            <div class="col-md-9"><input type="text" name="wearing_day" value="{{ old('wearing_day', $m->wearing_day ?? '') }}" class="form-control rounded-0"></div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Significance') }}</label>
            <div class="col-md-9"><textarea name="significance" rows="3" class="form-control rounded-0">{{ old('significance', $m->significance ?? '') }}</textarea></div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Spiritual Benefits') }}</label>
            <div class="col-md-9"><textarea name="benefits_spiritual" rows="2" class="form-control rounded-0">{{ old('benefits_spiritual', $m->benefits_spiritual ?? '') }}</textarea></div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Mental Benefits') }}</label>
            <div class="col-md-9"><textarea name="benefits_mental" rows="2" class="form-control rounded-0">{{ old('benefits_mental', $m->benefits_mental ?? '') }}</textarea></div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Physical Benefits') }}</label>
            <div class="col-md-9"><textarea name="benefits_physical" rows="2" class="form-control rounded-0">{{ old('benefits_physical', $m->benefits_physical ?? '') }}</textarea></div>
        </div>
        <div class="form-group row mb-0">
            <label class="col-md-3 col-form-label">{{ translate('Status') }}</label>
            <div class="col-md-9">
                <label class="aiz-switch aiz-switch-success mb-0">
                    <input type="checkbox" name="status" value="1" @if (old('status', $m->status ?? 1)) checked @endif>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>
</div>
<div class="text-right mb-4">
    <button type="submit" class="btn btn-primary rounded-0">{{ translate('Save') }}</button>
</div>

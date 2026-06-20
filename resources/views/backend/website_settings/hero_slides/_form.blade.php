@php $s = $slide ?? null; @endphp
<div class="card rounded-0">
    <div class="card-body">
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Slide Image') }} <small>(approx 1100x900)</small></label>
            <div class="col-md-9">
                <div class="input-group" data-toggle="aizuploader" data-type="image">
                    <div class="input-group-prepend">
                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                    </div>
                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                    <input type="hidden" name="image" class="selected-files" value="{{ old('image', $s->image ?? '') }}">
                </div>
                <div class="file-preview box sm"></div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Kicker') }}</label>
            <div class="col-md-9"><input type="text" name="kicker" value="{{ old('kicker', $s->kicker ?? '') }}" class="form-control rounded-0" placeholder="{{ translate('Nepal Origin · Lab Certified') }}"></div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Title') }}</label>
            <div class="col-md-9"><input type="text" name="title" value="{{ old('title', $s->title ?? '') }}" class="form-control rounded-0" placeholder="{{ translate('Discover') }}"></div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Title (emphasised)') }}</label>
            <div class="col-md-9"><input type="text" name="title_em" value="{{ old('title_em', $s->title_em ?? '') }}" class="form-control rounded-0" placeholder="{{ translate('Our Rudraksha') }}"></div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Text') }}</label>
            <div class="col-md-9"><textarea name="text" rows="3" class="form-control rounded-0">{{ old('text', $s->text ?? '') }}</textarea></div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Button Text') }}</label>
            <div class="col-md-9"><input type="text" name="cta_text" value="{{ old('cta_text', $s->cta_text ?? '') }}" class="form-control rounded-0" placeholder="{{ translate('Shop Collection') }}"></div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Button Link') }} <small>({{ translate('optional') }})</small></label>
            <div class="col-md-9"><input type="text" name="cta_link" value="{{ old('cta_link', $s->cta_link ?? '') }}" class="form-control rounded-0" placeholder="https://..."></div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label">{{ translate('Sort Order') }}</label>
            <div class="col-md-9"><input type="number" name="sort_order" value="{{ old('sort_order', $s->sort_order ?? 0) }}" class="form-control rounded-0"></div>
        </div>
        <div class="form-group row mb-0">
            <label class="col-md-3 col-form-label">{{ translate('Status') }}</label>
            <div class="col-md-9">
                <label class="aiz-switch aiz-switch-success mb-0">
                    <input type="checkbox" name="status" value="1" @if (old('status', $s->status ?? 1)) checked @endif>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>
</div>
<div class="text-right mb-4">
    <button type="submit" class="btn btn-primary rounded-0">{{ translate('Save') }}</button>
</div>

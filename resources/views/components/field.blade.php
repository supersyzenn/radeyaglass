@props(['name', 'label', 'type' => 'text', 'value' => '', 'hint' => ''])
<div class="field"><label for="{{ $name }}">{{ $label }}</label>
@if($type === 'textarea')
<textarea id="{{ $name }}" name="{{ $name }}" {{ $attributes }} @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>{{ old($name, $value) }}</textarea>
@else
<input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" {{ $attributes }} @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>
@endif
@if($hint)<p class="field-hint">{{ $hint }}</p>@endif
@error($name)<p class="field-error" id="{{ $name }}-error">{{ $message }}</p>@enderror
</div>

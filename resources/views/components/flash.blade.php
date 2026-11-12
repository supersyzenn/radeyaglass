@if(session('status'))<div class="alert" role="status"><x-icon name="check" />{{ session('status') }}</div>@endif
@if($errors->any())<div class="alert alert-error" role="alert"><div><strong>Periksa kembali isian Anda.</strong><ul>@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul></div></div>@endif

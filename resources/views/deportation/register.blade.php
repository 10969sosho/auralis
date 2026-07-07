@extends('layouts.guest')
@section('title', 'Daftar Akaun Deportasi')

@section('content')
<div class="auth-page">
    <div class="auth-box auth-box-lg">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Daftar Akaun Deportasi</h2>
                <p>Daftar untuk pembelian tiket kapal khas deportasi dengan harga termasuk tambang bas dari titik penampungan</p>
            </div>

            <form action="{{ route('deportation.register.store') }}" method="POST" class="auth-form">
                @csrf

                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="name" class="auth-label">Nama Penuh *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="auth-input" placeholder="Nama penuh">
                        @error('name') <p class="auth-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="auth-field">
                        <label for="email" class="auth-label">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required class="auth-input" placeholder="email@contoh.com">
                        @error('email') <p class="auth-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="phone" class="auth-label">Telefon</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="auth-input" placeholder="+60...">
                    </div>
                    <div class="auth-field">
                        <label for="nationality" class="auth-label">Kewarganegaraan</label>
                        <select name="nationality" id="nationality" class="auth-input">
                            <option value="">Pilih kewarganegaraan</option>
                        </select>
                    </div>
                </div>

                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="passport_number" class="auth-label">No. Pasport / IC</label>
                        <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number') }}" class="auth-input" placeholder="Opsional">
                    </div>
                    <div class="auth-field">
                        <label for="birth_date" class="auth-label">Tarikh Lahir</label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" class="auth-input">
                    </div>
                </div>

                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="gender" class="auth-label">Jantina</label>
                        <select name="gender" id="gender" class="auth-input">
                            <option value="">Pilih</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Lelaki</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Lain-lain</option>
                        </select>
                    </div>
                    <div class="auth-field">
                        <label for="shelter_point" class="auth-label">Titik Penampungan *</label>
                        <select name="shelter_point" id="shelter_point" required class="auth-input">
                            <option value="">Pilih titik penampungan</option>
                            <option value="tawau" {{ old('shelter_point') == 'tawau' ? 'selected' : '' }}>Tawau (+RM30)</option>
                            <option value="sandakan" {{ old('shelter_point') == 'sandakan' ? 'selected' : '' }}>Sandakan (+RM30)</option>
                            <option value="kinabalu_papar" {{ old('shelter_point') == 'kinabalu_papar' ? 'selected' : '' }}>Kinabalu (Papar) (+RM55)</option>
                            <option value="kinabalu_menggatal" {{ old('shelter_point') == 'kinabalu_menggatal' ? 'selected' : '' }}>Kinabalu (Menggatal) (+RM50)</option>
                        </select>
                        @error('shelter_point') <p class="auth-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="password" class="auth-label">Kata Laluan *</label>
                        <div class="pw-field">
                            <input type="password" name="password" id="password" required class="auth-input" placeholder="Min. 8 aksara">
                            <button type="button" class="pw-show" onclick="var i=this.previousElementSibling;var p=i.type==='password';i.type=p?'text':'password';this.textContent=p?'Sembunyi':'Tunjuk'">Tunjuk</button>
                        </div>
                        @error('password') <p class="auth-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="auth-field">
                        <label for="password_confirmation" class="auth-label">Sahkan Kata Laluan *</label>
                        <div class="pw-field">
                            <input type="password" name="password_confirmation" id="password_confirmation" required class="auth-input" placeholder="Ulang kata laluan">
                            <button type="button" class="pw-show" onclick="var i=this.previousElementSibling;var p=i.type==='password';i.type=p?'text':'password';this.textContent=p?'Sembunyi':'Tunjuk'">Tunjuk</button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="auth-btn">Daftar Akaun Deportasi</button>
            </form>

            <p class="auth-footer-text">
                Sudah ada akaun? <a href="{{ route('login') }}">Log Masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var select = document.getElementById('nationality');
    if (!select) return;
    var oldVal = '{{ old('nationality') }}';

    fetch('{{ route('api.countries') }}')
        .then(function(res) { return res.json(); })
        .then(function(options) {
            var ts = new TomSelect('#nationality', {
                valueField: 'value', labelField: 'text', searchField: 'text',
                options: options, placeholder: 'Cari kewarganegaraan...',
                maxOptions: null, create: true,
                onChange: function(v) { select.dispatchEvent(new Event('change', { bubbles: true })); },
                render: {
                    option: function(item, escape) { return '<div>' + escape(item.text) + '</div>'; },
                    item: function(item, escape) { return '<div>' + escape(item.text) + '</div>'; }
                }
            });
            if (oldVal) { ts.addOption({ value: oldVal, text: oldVal }); ts.setValue(oldVal); }
        })
        .catch(function() {
            var inp = document.createElement('input');
            inp.type = 'text'; inp.name = 'nationality'; inp.id = 'nationality';
            inp.className = 'auth-input'; inp.placeholder = 'e.g. Indonesia'; inp.value = oldVal;
            select.parentNode.replaceChild(inp, select);
        });
});
</script>
@endpush

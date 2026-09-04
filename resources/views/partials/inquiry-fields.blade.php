<div class="field">
    <label for="name">Full name <span aria-hidden="true">*</span></label>
    <input id="name" name="name" value="{{ old('name') }}" required autocomplete="name" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
    @error('name')<span class="error" id="name-error" role="alert">{{ $message }}</span>@enderror
</div>
<div class="form-row">
    <div class="field">
        <label for="email">Email address <span aria-hidden="true">*</span></label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" inputmode="email" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
        @error('email')<span class="error" id="email-error" role="alert">{{ $message }}</span>@enderror
    </div>
    <div class="field">
        <label for="phone">Phone number <small>Optional</small></label>
        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" inputmode="tel" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
        @error('phone')<span class="error" id="phone-error" role="alert">{{ $message }}</span>@enderror
    </div>
</div>
<div class="field">
    <label for="message">How can we help? <span aria-hidden="true">*</span></label>
    <textarea id="message" name="message" rows="5" required @error('message') aria-invalid="true" aria-describedby="message-error" @enderror>{{ old('message', $defaultMessage ?? '') }}</textarea>
    @error('message')<span class="error" id="message-error" role="alert">{{ $message }}</span>@enderror
</div>

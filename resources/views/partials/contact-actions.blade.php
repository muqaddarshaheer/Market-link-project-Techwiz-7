{{-- Mobile-friendly Call + WhatsApp (tel: and wa.me) --}}
@php
    $rawPhone = $phone ?? null;
    $wa = \App\Models\Order::whatsappDigits($rawPhone);
    $tel = $rawPhone ? preg_replace('/\D+/', '', (string) $rawPhone) : null;
    $label = $label ?? 'Contact';
    $prefill = $prefill ?? '';
@endphp
@if($tel)
<nav class="ml-contact-actions {{ $class ?? '' }}" aria-label="{{ $label }}">
    <span class="ml-contact-label" id="ml-contact-{{ $tel }}">{{ $label }}</span>
    <div class="ml-contact-btns">
        <a class="ml-contact-btn ml-contact-call"
           href="tel:{{ $tel }}"
           aria-describedby="ml-contact-{{ $tel }}">
            <i class="bi bi-telephone-fill" aria-hidden="true"></i>
            <span>Call</span>
        </a>
        @if($wa)
            <a class="ml-contact-btn ml-contact-wa"
               href="https://wa.me/{{ $wa }}{{ $prefill !== '' ? '?text='.urlencode($prefill) : '' }}"
               target="_blank"
               rel="noopener noreferrer"
               aria-describedby="ml-contact-{{ $tel }}">
                <i class="bi bi-whatsapp" aria-hidden="true"></i>
                <span>WhatsApp</span>
            </a>
        @endif
    </div>
</nav>
@endif

@extends('layouts.farmer')

@section('title', 'Green Leaf Advisor')

@section('content')
@php
    $wx = $weather ?? null;
@endphp
<link rel="stylesheet" href="{{ asset('css/crop-health.css') }}?v={{ @filemtime(public_path('css/crop-health.css')) }}">

<div class="cha-app"
     id="chaApp"
     data-lang="roman"
     data-message-url="{{ $messageUrl }}"
     data-answer-url="{{ $answerUrl }}"
     data-photo-url="{{ $photoUrl }}"
     data-reset-url="{{ $resetUrl }}"
     data-speak-url="{{ $speakUrl }}"
     data-csrf="{{ csrf_token() }}">

    <header class="cha-hero">
        <div class="cha-hero-top">
            <span class="cha-badge">🌿 Green Leaf Advisor</span>
            <div class="cha-lang" role="group" aria-label="Language">
                <button type="button" class="cha-lang-btn is-on" data-lang="roman">Roman</button>
                <button type="button" class="cha-lang-btn" data-lang="en">EN</button>
            </div>
        </div>
        <h1 class="cha-title">Aapke plant mein kya masla hai?</h1>
        <p class="cha-lead">Main aapka garden pharmacist hoon — sabzi, phal, herbs. Bas masla bolain; main simple steps bataunga.</p>
        @if($wx)
            <p class="cha-weather-chip">
                <i class="bi bi-cloud-sun"></i>
                <span>{{ $wx['location'] ?? $wx['label'] ?? $wx['place'] ?? 'Weather' }} · {{ isset($wx['temp']) ? ((int)$wx['temp']).'°' : '—' }}</span>
            </p>
        @endif
    </header>

    <section class="cha-voice-card" id="chaVoiceCard">
        <p class="cha-status" id="chaStatus">Bol kar batayein</p>

        <button type="button" class="cha-mic" id="chaMic" aria-label="Bol kar batayein">
            <span class="cha-mic-ring" aria-hidden="true"></span>
            <i class="bi bi-mic-fill" aria-hidden="true"></i>
        </button>

        <p class="cha-mic-caption" id="chaMicCaption">🎤 Bol Kar Batayein</p>

        <div class="cha-examples" id="chaExamples">
            <div class="cha-examples-title">Misalain:</div>
            @foreach($examples as $ex)
                <button type="button" class="cha-example" data-example="{{ $ex }}">“{{ $ex }}”</button>
            @endforeach
        </div>

        <div class="cha-heard" id="chaHeard" hidden>
            <div class="cha-heard-label"><i class="bi bi-mic"></i> <span data-i18n="youSaid">Aap ne kaha</span></div>
            <p class="cha-heard-text" id="chaHeardText"></p>
        </div>

        <div class="cha-detect" id="chaDetect" hidden>
            <div class="cha-detect-label"><i class="bi bi-check2-circle"></i> <span data-i18n="detected">Samajh aaya</span></div>
            <p class="cha-detect-text" id="chaDetectText"></p>
        </div>

        <div class="cha-type-row">
            <label class="visually-hidden" for="chaText">Ya type karein</label>
            <input type="text" id="chaText" class="cha-input" placeholder="Ya type karein… (Roman Urdu / English / Urdu)" maxlength="500" autocomplete="off">
            <button type="button" class="cha-send" id="chaSend">Bhejein</button>
        </div>
        <p class="cha-or">⌨️ Ya type karein</p>
    </section>

    <section class="cha-ask" id="chaAsk" hidden>
        <p class="cha-ask-prompt" id="chaAskPrompt"></p>
        <div class="cha-options" id="chaOptions"></div>
    </section>

    <section class="cha-result" id="chaResult" hidden></section>

    <section class="cha-sticky-board" id="chaStickyBoard" hidden>
        <h3 class="cha-sticky-board-title">📌 Aapke sticky notes</h3>
        <div class="cha-sticky-list" id="chaStickyList"></div>
    </section>

    <div class="cha-toolbar">
        <label class="cha-photo-btn">
            <input type="file" id="chaPhoto" accept="image/*" capture="environment" hidden>
            <i class="bi bi-camera"></i> <span>Photo lein</span>
        </label>
        <button type="button" class="cha-tool" id="chaSpeakBtn">
            <i class="bi bi-volume-up"></i> <span>🔊 Sunayein</span>
        </button>
        <button type="button" class="cha-tool cha-tool-muted" id="chaReset">
            <i class="bi bi-arrow-counterclockwise"></i> <span>Naya masla</span>
        </button>
    </div>

    <p class="cha-safe">Green Leaf Advisor — possible wajahain only. Confirmed bimari nahi. Chemical / herbal andar istemal se pehle local mashwara / doctor.</p>
</div>

<audio id="chaAudio" preload="none" hidden></audio>
@endsection

@push('scripts')
<script src="{{ asset('js/crop-health.js') }}?v={{ @filemtime(public_path('js/crop-health.js')) }}"></script>
@endpush

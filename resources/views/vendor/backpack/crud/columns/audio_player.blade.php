@php
    $audio_url = $entry->url; // ajusta según tu campo
@endphp

<div class="audio-player-container">
    <button
        class="btn btn-sm btn-outline-primary play-btn"
        onclick="toggleAudio(this, '{{ $audio_url }}')"
        data-playing="false">
        <i class="la la-play"></i>
    </button>
    <audio style="display: none;"></audio>
</div>

<script>
if(!window.toggleAudio)
window.toggleAudio = (btn, url) => {
    const audio = btn.nextElementSibling;
    const icon = btn.querySelector('i');

    if (btn.dataset.playing === 'false') {
        // Detener todos los otros audios
        document.querySelectorAll('audio').forEach(a => a.pause());
        document.querySelectorAll('.play-btn i').forEach(i => i.className = 'la la-play');

        if (!audio.src) audio.src = url;
        audio.play();
        icon.className = 'la la-pause';
        btn.dataset.playing = 'true';
    } else {
        audio.pause();
        icon.className = 'la la-play';
        btn.dataset.playing = 'false';
    }
}
</script>

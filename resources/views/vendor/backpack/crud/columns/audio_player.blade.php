@php
    $audio_url = $entry->url;
    $unique_id = "audio_player_{$entry->id}";
@endphp

<div class="audio-player-container" id="{{ $unique_id }}_container">
    <button class="btn btn-sm btn-outline-primary play-btn" data-audio-url="{{ $audio_url }}"
        onclick="window.playPauseAudioInVue('{{ $audio_url }}', this.parentNode.id)">
        <i class="la la-play"></i>
    </button>
</div>

<script>
    // Esta función será el puente entre Blade y Vue
    window.playPauseAudioInVue = (url, id) => {
        const icon = document.querySelector('#' + id + ' i.la')
        if(!icon) return
        const state = icon.getAttribute('class').replace('la la-', '')
        console.log('state', state)
        if(state=='play') {
            // Emitimos un evento custom que Vue escuchará
            window.dispatchEvent(new CustomEvent('player-play', {
                detail: {
                    url
                }
            }));
        }
        else if (state == 'pause') {
            // Emitimos un evento custom que Vue escuchará
            window.dispatchEvent(new CustomEvent('player-pause'));
        } else if(state=='play-circle') {
            window.dispatchEvent(new CustomEvent('player-continue'));
        }

    }


    if (!window.installedAudioEvents) {
        window.installedAudioEvents = true
        // Escuchamos los eventos desde el componente
        window.addEventListener("player-playing", (event) => {
            console.log('playing', event.detail)
            // reseteamos iconos
            const playing_url = event.detail.url
            document.querySelectorAll('.audio-player-container i.la')
                .forEach(x => x.setAttribute('class', 'la la-play'))
            // ahora el icono que esté reproduciendo el audio, lo actualizamos

            const icon = document.querySelector('.audio-player-container button[data-audio-url="' +
                playing_url + '"] i.la')
                if(icon)
            icon.setAttribute('class', 'la la-pause')
        });

        // Escuchamos los eventos desde el componente
        window.addEventListener("player-paused", (event) => {
            console.log('paused', event.detail)
            // reseteamos iconos
            const playing_url = event.detail.url
            document.querySelectorAll('.audio-player-container i.la')
                .forEach(x => x.setAttribute('class', 'la la-play'))
            // ahora el icono que esté reproduciendo el audio, lo actualizamos

            const icon = document.querySelector('.audio-player-container button[data-audio-url="' +
                playing_url + '"] i.la')
                if(icon)
            icon.setAttribute('class', 'la la-play-circle')
        });
    }
</script>

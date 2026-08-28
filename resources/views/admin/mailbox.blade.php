@extends(backpack_view('blank'))

@section('content')
<style>
    .card-body, .section, #main-content, .row {
        padding: 0 !important;
        margin: 0 !important;
    }
    #mailbox-frame {
        width: 100%;
        height: calc(100vh - 56px);
        border: none;
    }
</style>

<iframe id="mailbox-frame" src="/mailbox"></iframe>

<script>
document.getElementById('mailbox-frame').addEventListener('load', function() {
    const frame = this;
    const syncTheme = () => {
        try {
            const theme = document.documentElement.dataset.bsTheme || 'light';
            const frameHtml = frame.contentDocument?.documentElement;
            if (frameHtml) {
                frameHtml.classList.toggle('dark', theme === 'dark');
            }
        } catch(e) {}
    };
    syncTheme();
    new MutationObserver(syncTheme).observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-bs-theme']
    });
});
</script>
@endsection

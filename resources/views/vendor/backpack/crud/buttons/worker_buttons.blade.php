@if ($crud->hasAccess('update'))
    <div class="worker-controls ml-2" style="display: inline-block; border: 2px solid gray; border-radius: 12px; padding: 5px 6px; min-width: 220px">
        <button class="btn btn-sm btn-secondary" id="worker-toggle" onclick="toggleWorker()"
            title="Parar o arrancar worker">
            <i id="worker-icon" class="la la-play" style="transform: scale(150%)"></i> <!-- Icono de línea para "play" -->
        </button>
        <button class="btn btn-sm btn-secondary" id="worker-restart" onclick="reiniciarWorker()"
            title="Reiniciar worker">
            <i class="la la-redo-alt" style="transform: scale(150%)"></i> <!-- Icono de línea para "reiniciar" -->
        </button>
        <span class="ml-2"><span id="worker-state">...</span></span>
        <span class="ml-2"><i id="worker-running-check" class="la la-check-circle"
                style="display: none; font:bold; color: green; transform: scale(150%)"></i></span>
    </div>
@endif

@push('after_scripts')
    <script>
        var API_URL = @json(config('app.api_url'));
        var workerStatus;
        var workerIcon;
        var workerOk;
        var checkTimeout

        function check_worker_status() {
            clearTimeout(checkTimeout)
            if (workerStatus) {
                axios.get(`${API_URL}/admin/worker/check`)
                    .then(response => {
                        console.log(response.data.status);
                        updateWorkerUI(response.data.status);
                        checkTimeout = setTimeout(check_worker_status, 5000);
                    });
            }
        }

        function toggleWorker() {
            if (workerStatus.innerText === 'running') {
                stopWorker();
            } else {
                startWorker();
            }
        }

        function startWorker() {
            axios.get(`${API_URL}/admin/worker/start`)
                .then(response => {
                    console.log(response.data);
                    updateWorkerUI('running');
                });
        }

        function stopWorker() {
            workerStatus.innerText = 'stopping...';
            axios.get(`${API_URL}/admin/worker/stop`)
                .then(response => {
                    console.log(response.data);
                    updateWorkerUI('stopped');
                });
        }

        function reiniciarWorker() {
            workerStatus.innerText = 'restarting...';
            axios.get(`${API_URL}/admin/worker/restart`)
                .then(response => {
                    console.log(response.data);
                    updateWorkerUI('running');
                });
        }

        function updateWorkerUI(status) {
            workerStatus.innerText = status;
            if (status === 'running') {
                workerIcon.classList.remove('la-play');
                workerIcon.classList.add('la-stop');
            } else {
                workerIcon.classList.remove('la-stop');
                workerIcon.classList.add('la-play');
            }
            if (status == "running")
                workerOk.style.display = "inline-block"
            else
                workerOk.style.display = "none"
        }

        document.addEventListener('DOMContentLoaded', function() {
            workerStatus = document.querySelector("#worker-state");
            workerIcon = document.querySelector("#worker-icon");
            workerOk = document.querySelector("#worker-running-check");
            check_worker_status();
        });
    </script>
@endpush

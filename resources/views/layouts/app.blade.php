<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistema de ventas de abarrotes" />
    <meta name="author" content="SakCode" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistema ventas - @yield('title')</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" href="{{ asset('img/image.png') }}" type="image/png">
    @stack('css-datatable')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/responsive-touch.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    @stack('css')

</head>

<body class="sb-nav-fixed">

    <x-navigation-header />

    <div id="layoutSidenav">

        <x-navigation-menu />

        <div id="layoutSidenav_content">

            <main>
                @yield('content')
            </main>

            <x-footer />

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>  
    <script src="{{ asset('js/scripts.js') }}"></script>
    @stack('js')

    <!-- QR Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="qrModalLabel">📱 Conectar Dispositivo Móvil</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <p class="mb-3">Escanea este código para acceder desde tu celular:</p>
            <div id="qr-container" style="display: flex; justify-content: center; align-items: center; min-height: 260px; background: #f8f9fa; border-radius: 10px; padding: 20px;">
                <div id="qr-code" style="text-align: center;"></div>
            </div>
            <p class="mt-3 text-muted small">
                <i class="fas fa-wifi"></i> Asegúrate de estar conectado a la misma red WiFi.
            </p>
            <div class="alert alert-info py-2 mt-2 mb-0">
                <small><strong>URL:</strong> <span id="qr-url-display">Cargando...</span></small>
            </div>
          </div>
        </div>
      </div>
    </div>

    @vite(['resources/js/app.js'])
    <script>
        function generateQR() {
            const container = document.getElementById('qr-code');
            const urlDisplay = document.getElementById('qr-url-display');
            
            // Mostrar mensaje de carga
            container.innerHTML = '<div class="text-secondary"><i class="fas fa-spinner fa-spin"></i> Generando QR...</div>';
            
            // URL fija: kazuma.local:8000
            const currentUrl = 'http://kazuma.local:8000';
            
            // Actualizar la URL en pantalla
            urlDisplay.textContent = currentUrl;
            
            // Usar API de qrserver.com directamente (más confiable)
            setTimeout(function() {
                try {
                    const qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=10&data=' + encodeURIComponent(currentUrl);
                    container.innerHTML = '<img src="' + qrApiUrl + '" alt="QR Code" class="img-fluid" style="max-width: 220px; height: auto; border-radius: 8px;" onload="console.log(\'✅ QR cargado correctamente para kazuma.local:8000\')" onerror="this.parentElement.innerHTML=\'<div class=&quot;text-danger p-3&quot;><i class=&quot;fas fa-exclamation-triangle&quot;></i><br>Error al cargar QR<br><small>Copia la URL: <strong>' + currentUrl + '</strong></small></div>\'" />';
                    console.log('🔄 Generando QR para:', currentUrl);
                } catch (error) {
                    console.error('❌ Error:', error);
                    container.innerHTML = '<div class="text-danger p-3"><i class="fas fa-exclamation-triangle"></i><br>No se pudo generar el código QR<br><small class="mt-2 d-block">Copia la URL manualmente:<br><strong>' + currentUrl + '</strong></small></div>';
                }
            }, 300);
        }
        
        // Generar cuando se muestra el modal
        document.getElementById('qrModal').addEventListener('shown.bs.modal', function () {
            generateQR();
        });
    </script>

</body>


</html>
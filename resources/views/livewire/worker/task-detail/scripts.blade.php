@push('js')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
    let signaturePad = null;
    let html5QrcodeScanner = null;
    let currentScanningIndex = null;

    document.addEventListener('livewire:initialized', () => {
        // Init Signature
        @this.on('init-signature', () => { setTimeout(initSignature, 300); });
        @this.on('success', (msg) => { toastr.success(msg); });
        initSignature();
        Livewire.hook('morph.updated', ({ el, component }) => {
            if(document.getElementById('signature-pad')) resizeCanvas();
        });
    });

    // --- SIGNATURE LOGIC ---
    function initSignature() {
        const canvas = document.getElementById('signature-pad');
        if (canvas) {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            if(signaturePad) signaturePad.off();
            signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgba(255, 255, 255, 0)' });
        }
    }
    function resizeCanvas() {
        const canvas = document.getElementById('signature-pad');
        if (canvas) {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            if(signaturePad) signaturePad.clear(); 
        }
    }
    function clearSignature() { if(signaturePad) signaturePad.clear(); }
    function submitReport() {
        if (signaturePad && !signaturePad.isEmpty()) {
            @this.set('signature_data', signaturePad.toDataURL('image/png'));
        }
        @this.call('saveReport');
    }

    // --- SCANNER LOGIC ---
    // --- SCANNER LOGIC ---
    function openScanner(index) {
        currentScanningIndex = index;
        document.getElementById('scanner-overlay').style.display = 'flex';
        
        // Ensure cleanup if previous instance was stuck
        if(html5QrcodeScanner) {
             html5QrcodeScanner.stop().then(() => {
                 html5QrcodeScanner.clear();
                 html5QrcodeScanner = null;
                 startScannerInstance();
             }).catch(err => {
                 console.error("Failed to stop previous scanner", err);
                 html5QrcodeScanner = null;
                 startScannerInstance();
             });
        } else {
             startScannerInstance();
        }
    }

    function startScannerInstance() {
        html5QrcodeScanner = new Html5Qrcode("reader");
        // Use higher resolution config
        const config = { 
            fps: 15, 
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0,
            showTorchButtonIfSupported: true
        };
        
        html5QrcodeScanner.start({ facingMode: "environment" }, config, 
            (decodedText) => {
                // Success
                console.log("Scanned:", decodedText);
                @this.set('items.' + currentScanningIndex + '.serial', decodedText);
                toastr.success('Đã quét: ' + decodedText);
                closeScanner();
            }, 
            (errorMessage) => {
                // Parse error, ignore common failures to keep console clean
            }
        ).catch(err => {
            console.error("Error starting scanner", err);
            toastr.error('Không thể khởi động camera. Vui lòng cấp quyền.');
            closeScanner();
        });
    }

    function closeScanner() {
        document.getElementById('scanner-overlay').style.display = 'none';
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                html5QrcodeScanner.clear();
                html5QrcodeScanner = null;
            }).catch(err => {
                console.error("Failed to stop scanner", err);
                html5QrcodeScanner = null;
            });
        }
    }

    // --- VIEW IMAGE ---
    function viewImage(src) {
        document.getElementById('imageViewerSrc').src = src;
        document.getElementById('imageViewer').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeImageViewer() {
        document.getElementById('imageViewer').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
</script>
@endpush

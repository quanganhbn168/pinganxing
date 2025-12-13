@push('js')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
    let signaturePad = null;
    let html5QrcodeScanner = null;
    let currentScanningIndex = null;
    
    // === CONTINUOUS SCAN MODE ===
    let isContinuousScan = false;
    let continuousScanType = 'items';
    let scannedSerials = [];
    let scanCount = 0;
    let bulkMaterialName = '';
    
    // Beep sound (base64 encoded)
    const beepSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2Onp+ckIV3aGRqeY2en5+ZjX9tYWJrfI6dnpqOf2xgYWx+j52dnJGBbmFibn+Qm5uZjoFuYWJvgZKbm5iNgG1hYnCCk5uamI6Ab2JjcYSTmpqXjYBvY2NyhZSampePgXBjZHOGlZmZlo+BcGRkdIeWmZiVjoJxZGV1iJaZmJWOgnJlZnaJl5mYlI6DcmZneIqXmJiUjoNzZ2d5i5eYl5OMg3Rnnn+LmJiXk4yDdWdof4yYmJaTjIR1aGmAjJiYlZOMhHZoaYGNmJiUk4yFdmhpgo6YmJSTjIV3aWqDjpiYlJOMhndpa4OPmJiTk4yHeGprhI+YmJOTjId4a2yFkJiYk5KMh3lsbIWQmJiTkoyIeWxthpGYmJOSjIl5bW2HkZiYk5KMiXpuboeRmJeTkoyKem5viJGYl5KSjIp7b2+IkpiXkpKMi3twcImSmJeSkoyLe3BwiZKYl5GRjIt8cXCJkpiXkZGNjHxxcYqSmJaRkY2MfHFxipKYlpGRjY18cnKLkpiWkJCNjX1yc4uSmJaQkI6OfnNzjJOYlpCQjo9+c3ONk5iWj4+Oj353dI2TmJaPj4+Pf3R0jpOYlo6Oj5B/dXWOk5iWjo6PkIB1do+UmJWOjo+RgHZ2j5SYlY2Nj5GBdneQlJiVjY2PkoF3d5CUmJSNjZCSgnh4kZSYlIyMkJOCeHiRlZiUjIyQk4J5eZKVmJSMjJCUg3l5kpWYk4uLkJSDenqSlZiTi4uRlYR6epOVmJOLi5GWhHp7k5WYkouLkZaFe3uUlpiSioqRl4V7fJSWmJKKipGXhnx8lJaYkoqKkpiGfH2VlpiRiYmSmId9fZWWmJGJiZKYh319lZeYkYmJkpmHfn6Wl5iRiIiSmoh+fpaXmJCIiJKaiH5/l5eYkIiIk5uIfn+Xl5iPh4eTm4l/f5eXmI+Hh5ObiX+AmJeYj4aGk5yJgICYmJiPhYWUnIqAgJiYmI+FhZSdioGBmJiYjoSElJ2KgYGYmJiOhISUnYuBgZiYl46EhJWei4GCmZiXjoODlZ6LgoKZmJeOg4OVnoyCApmYl42Cg5aejIKCmZiXjYKClp6MgoOZmZeNgYGWn4yCg5mZl42BgZefC4KDmZmXjICAl5+MgoOamZeM');
    
    document.addEventListener('livewire:initialized', () => {
        @this.on('init-signature', () => { setTimeout(initSignature, 300); });
        @this.on('success', (msg) => { toastr.success(msg); });
        @this.on('scan-success', (msg) => { toastr.success(msg); playBeep(); });
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
    
    function playBeep() {
        beepSound.currentTime = 0;
        beepSound.play().catch(e => console.log('Beep blocked'));
    }

    // --- SINGLE SCAN (cho 1 ô cụ thể) ---
    function openScanner(index) {
        isContinuousScan = false;
        currentScanningIndex = index;
        scannedSerials = [];
        scanCount = 0;
        updateScanCounter();
        document.getElementById('scanner-overlay').style.display = 'flex';
        cleanupAndStartScanner();
    }

    // --- SINGLE SCAN for Returned Items ---
    let isReturnedItemScan = false;
    function openReturnedScanner(index) {
        isContinuousScan = false;
        isReturnedItemScan = true;
        currentScanningIndex = index;
        scannedSerials = [];
        scanCount = 0;
        updateScanCounter();
        document.getElementById('scanner-overlay').style.display = 'flex';
        cleanupAndStartScanner();
    }
    
    // --- CONTINUOUS SCAN MODE ---
    function openContinuousScanner(type = 'items') {
        continuousScanType = type;
        bulkMaterialName = '';
        document.getElementById('bulk-material-name').value = '';
        $('#materialSelectModal').modal('show');
    }
    
    function startBulkScan() {
        const materialInput = document.getElementById('bulk-material-name');
        bulkMaterialName = materialInput.value.trim();
        
        if (!bulkMaterialName) {
            toastr.warning('Vui lòng nhập tên vật tư trước khi quét');
            materialInput.focus();
            return;
        }
        
        $('#materialSelectModal').modal('hide');
        
        isContinuousScan = true;
        scannedSerials = [];
        scanCount = 0;
        updateScanCounter();
        document.getElementById('scanner-overlay').style.display = 'flex';
        document.getElementById('scan-counter').style.display = 'block';
        cleanupAndStartScanner();
    }
    
    function cleanupAndStartScanner() {
        if(html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                html5QrcodeScanner.clear();
                html5QrcodeScanner = null;
                startScannerInstance();
            }).catch(err => {
                html5QrcodeScanner = null;
                startScannerInstance();
            });
        } else {
            startScannerInstance();
        }
    }

    function startScannerInstance() {
        html5QrcodeScanner = new Html5Qrcode("reader");
        
        const formatsToSupport = [
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.CODE_93,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.ITF,
            Html5QrcodeSupportedFormats.CODABAR
        ];
        
        const config = { 
            fps: 10,
            qrbox: { width: 280, height: 100 },
            aspectRatio: 1.0,
            formatsToSupport: formatsToSupport,
            experimentalFeatures: { useBarCodeDetectorIfSupported: true }
        };
        
        html5QrcodeScanner.start({ facingMode: "environment" }, config, 
            (decodedText) => {
                let cleanedText = decodedText
                    .replace(/^[\x00-\x1F\x7F]+/g, '')
                    .replace(/^\]([A-Za-z][0-9])/g, '')
                    .replace(/\uFEFF/g, '')
                    .replace(/[\u200B-\u200D\uFEFF]/g, '')
                    .trim();
                
                if (isContinuousScan) {
                    handleContinuousScan(cleanedText);
                } else {
                    // Check if scanning for returned items or regular items
                    if (isReturnedItemScan) {
                        @this.set('returnedItems.' + currentScanningIndex + '.serial', cleanedText);
                    } else {
                        @this.set('items.' + currentScanningIndex + '.serial', cleanedText);
                    }
                    toastr.success('Đã quét: ' + cleanedText);
                    playBeep();
                    closeScanner();
                }
            }, 
            (errorMessage) => {}
        ).catch(err => {
            toastr.error('Không thể khởi động camera.');
            closeScanner();
        });
    }
    
    let lastScanTime = 0;
    const SCAN_COOLDOWN = 2000; // 2 giây cooldown giữa các lần quét
    
    function handleContinuousScan(serial) {
        const now = Date.now();
        
        // Cooldown: bỏ qua nếu quét quá nhanh
        if (now - lastScanTime < SCAN_COOLDOWN) {
            return; // Im lặng bỏ qua, không spam
        }
        
        // Check trùng lặp - im lặng bỏ qua
        if (scannedSerials.includes(serial)) {
            return; // Không show warning để tránh spam
        }
        
        lastScanTime = now;
        scannedSerials.push(serial);
        scanCount++;
        updateScanCounter();
        
        if (continuousScanType === 'items') {
            @this.call('addScannedItem', serial, bulkMaterialName);
        } else if (continuousScanType === 'returned') {
            @this.call('addScannedReturnedItem', serial, bulkMaterialName);
        }
        
        playBeep();
        toastr.success(`[${scanCount}] ${bulkMaterialName}: ${serial}`);
    }
    
    function updateScanCounter() {
        const counter = document.getElementById('scan-counter');
        if (counter) counter.textContent = `Đã quét: ${scanCount} mã`;
    }

    function closeScanner() {
        document.getElementById('scanner-overlay').style.display = 'none';
        document.getElementById('scan-counter').style.display = 'none';
        isContinuousScan = false;
        isReturnedItemScan = false; // Reset flag
        scannedSerials = [];
        scanCount = 0;
        
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                html5QrcodeScanner.clear();
                html5QrcodeScanner = null;
            }).catch(err => { html5QrcodeScanner = null; });
        }
    }

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
<style>
    /* --- GIAO DIỆN APP --- */
    body { background-color: #f4f6f9; }

    /* Card & Components */
    .app-card {
        background: #fff; border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        border: 1px solid #e9ecef;
        margin-bottom: 15px; overflow: hidden;
    }
    .app-card-header {
        padding: 10px 15px; background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        font-size: 13px; font-weight: 600; text-transform: uppercase; color: #6c757d;
        display: flex; justify-content: space-between; align-items: center;
    }
    .app-card-body { padding: 15px; }

    /* Chữ ký & Ảnh */
    .signature-wrapper {
        background: #fff; border: 2px dashed #ced4da;
        border-radius: 8px; overflow: hidden; height: 180px; position: relative;
    }
    canvas { width: 100%; height: 100%; display: block; }
    
    .img-thumb-wrapper { position: relative; width: 70px; height: 70px; margin-right: 8px; margin-bottom: 8px; }
    .img-thumb { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; border: 1px solid #dee2e6; }
    .btn-remove-img {
        position: absolute; top: -6px; right: -6px; width: 20px; height: 20px;
        border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center;
        font-size: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Footer & Scanner */
    .sticky-footer {
        position: fixed; bottom: 0; left: 0; width: 100%;
        background: #fff; padding: 12px 15px;
        box-shadow: 0 -4px 12px rgba(0,0,0,0.05); z-index: 1000; border-top: 1px solid #eee;
    }
    #scanner-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.9); z-index: 9999; display: none;
        flex-direction: column; align-items: center; justify-content: center;
    }
    #scanner-box {
        width: 80%; max-width: 300px; aspect-ratio: 1/1;
        background: #000; border-radius: 16px; overflow: hidden;
        position: relative; border: 2px solid #28a745;
        box-shadow: 0 0 0 100vh rgba(0,0,0,0.5);
    }
    #scanner-line {
        position: absolute; width: 100%; height: 2px; background: #28a745;
        top: 0; left: 0; z-index: 2; animation: scanMove 2s infinite linear;
        box-shadow: 0 0 4px #28a745;
    }
    @keyframes scanMove { 
        0% { top: 0; opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { top: 100%; opacity: 0; } 
    }
    #reader { width: 100%; height: 100%; object-fit: cover; }

    /* Image Viewer */
    #imageViewer { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 11000; display: none; align-items: center; justify-content: center; flex-direction: column; }
    #imageViewer img { max-width: 100%; max-height: 90vh; object-fit: contain; }
    .close-viewer { position: absolute; top: 20px; right: 20px; color: #fff; font-size: 30px; cursor: pointer; z-index: 11001; }
    
    /* CSS cho Tab Lịch sử */
    .history-card { border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 20px; overflow: hidden; }
    .history-header { padding: 10px 15px; background: #fff; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
    .history-body { padding: 15px; background: #fff; }
    .money-box { background-color: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 6px; padding: 10px; margin-top: 10px; }
    .money-label { font-size: 11px; text-transform: uppercase; color: #2e7d32; font-weight: bold; }
    .money-value { font-size: 16px; font-weight: bold; color: #1b5e20; }
    .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(70px, 1fr)); gap: 8px; margin-top: 10px; }
    .image-item { width: 100%; aspect-ratio: 1/1; object-fit: cover; border-radius: 6px; border: 1px solid #eee; cursor: zoom-in; }
</style>

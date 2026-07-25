<link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .op-card,
    button,
    input,
    select,
    textarea,
    .toastify {
        border-radius: 0 !important;
    }

    .op-card {
        transition: all .3s;
        border: 1px solid #e5e7eb;
        padding: 1.25rem;
        background: #ffffff;
        margin-bottom: 1.5rem;
    }

    .preview-img {
        width: 100%;
        height: 120px;
        object-fit: contain;
        background: #f9fafb;
        border: 1px solid #f3f4f6;
    }

    .asset-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
        z-index: 10;
    }

    .group:hover .asset-overlay {
        opacity: 1;
    }

    .toast-success {
        background: #10b981 !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2) !important;
    }

    .toast-error {
        background: #ef4444 !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2) !important;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e5e7eb;
    }
</style>

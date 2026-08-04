
<div class="modal fade" id="instructionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="device-modal-header">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="modal-title-area">
                        <div class="modal-title-icon"><i class="bi bi-gear-wide-connected"></i></div>
                        <div>
                            <h5 id="instructionModalTitle">Device Setup Instructions</h5>
                            <p>Use these settings in your ZKTeco device's ADMS menu.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="device-modal-body">
                <div class="info-box">
                    <div class="info-box-content">
                        <i class="bi bi-info-circle-fill"></i>
                        <strong>ADMS / Cloud Server Settings</strong>
                        <p class="mb-2">Enter the following details exactly as shown below.</p>

                        <div class="detail-item">
                            <span class="detail-label">Server Address</span>
                            <span class="detail-value" id="serverIpAddress"><strong>{{ request()->getHost() }}</strong></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Server Port</span>
                            <span class="detail-value"><strong>80</strong></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Full URL (for reference)</span>
                            <span class="detail-value">
                                <code id="fullApiUrl"></code>
                                <button id="copyUrlButton" class="btn btn-sm btn-outline-primary ms-2" title="Copy URL">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

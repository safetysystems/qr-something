<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { BrowserCodeReader, BrowserQRCodeReader } from '@zxing/browser';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseSelect from '@/Components/Base/BaseSelect.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({
    layout: AppLayout,
});

const devices = ref([]);
const selectedDeviceId = ref('');
const statusMessage = ref('Ready to scan');
const errorMessage = ref('');
const decodedValue = ref('');
const isStarting = ref(false);
const scannerActive = ref(false);
const videoElement = ref(null);
const hasLoadedDevices = ref(false);
const uploadInput = ref(null);
let deviceChangeHandler = null;

const hasMediaSupport = computed(() => (
    typeof navigator !== 'undefined'
    && !!navigator.mediaDevices
    && typeof navigator.mediaDevices.getUserMedia === 'function'
));

const isSecureBrowserContext = computed(() => (
    typeof window !== 'undefined' && window.isSecureContext
));

const canAttemptCameraStart = computed(() => (
    hasMediaSupport.value && isSecureBrowserContext.value && !isStarting.value
));

const scannerStateLabel = computed(() => {
    if (scannerActive.value) {
        return 'Scanner Active';
    }

    if (isStarting.value) {
        return 'Starting';
    }

    return 'Scanner Idle';
});

const statusToneClass = computed(() => {
    if (scannerActive.value) {
        return 'scan-status-dot-active';
    }

    if (errorMessage.value) {
        return 'scan-status-dot-error';
    }

    return 'scan-status-dot-idle';
});

const codeReader = new BrowserQRCodeReader();
let scannerControls = null;

onMounted(async () => {
    if (!hasMediaSupport.value) {
        statusMessage.value = 'Camera scanning is not available in this browser.';
        return;
    }

    if (!isSecureBrowserContext.value) {
        statusMessage.value = 'Camera access requires HTTPS or localhost.';
        errorMessage.value = 'This deployed page is not running in a secure browser context.';
        return;
    }

    await loadDevices();
    statusMessage.value = 'Ready to scan';

    if (navigator.mediaDevices && typeof navigator.mediaDevices.addEventListener === 'function') {
        deviceChangeHandler = async () => {
            await loadDevices();
        };

        navigator.mediaDevices.addEventListener('devicechange', deviceChangeHandler);
    }
});

onBeforeUnmount(() => {
    stopScanner();

    if (
        deviceChangeHandler
        && navigator.mediaDevices
        && typeof navigator.mediaDevices.removeEventListener === 'function'
    ) {
        navigator.mediaDevices.removeEventListener('devicechange', deviceChangeHandler);
    }
});

async function loadDevices() {
    try {
        devices.value = await BrowserCodeReader.listVideoInputDevices();
        hasLoadedDevices.value = true;

        if (!selectedDeviceId.value && devices.value.length > 0) {
            selectedDeviceId.value = devices.value[0].deviceId;
        }
    } catch {
        hasLoadedDevices.value = true;
    }
}

async function startScanner() {
    if (!videoElement.value || !hasMediaSupport.value) {
        return;
    }

    if (!isSecureBrowserContext.value) {
        errorMessage.value = 'Camera access requires HTTPS or localhost. Open the deployed site over a valid secure URL.';
        statusMessage.value = 'Secure context required';
        return;
    }

    stopScanner();
    errorMessage.value = '';
    statusMessage.value = 'Starting camera...';
    isStarting.value = true;

    try {
        scannerControls = await codeReader.decodeFromVideoDevice(
            selectedDeviceId.value || undefined,
            videoElement.value,
            (result, error) => {
                if (result) {
                    handleDecoded(result.getText());
                    return;
                }

                if (!error) {
                    return;
                }

                if (['NotFoundException', 'ChecksumException', 'FormatException'].includes(error.name)) {
                    return;
                }

                errorMessage.value = 'Camera stream is active, but the QR code could not be read.';
            },
        );

        scannerActive.value = true;
        statusMessage.value = 'Scanner active';
        await loadDevices();
    } catch (error) {
        errorMessage.value = resolveCameraErrorMessage(error);
        statusMessage.value = 'Camera unavailable';
    } finally {
        isStarting.value = false;
    }
}

function stopScanner() {
    if (scannerControls) {
        scannerControls.stop();
        scannerControls = null;
    }

    scannerActive.value = false;
}

async function handleImageUpload(event) {
    const file = event.target.files?.[0];

    if (!file) {
        return;
    }

    errorMessage.value = '';
    statusMessage.value = 'Reading uploaded image...';

    const objectUrl = URL.createObjectURL(file);

    try {
        const result = await codeReader.decodeFromImageUrl(objectUrl);
        handleDecoded(result.getText());
    } catch {
        errorMessage.value = 'No QR code was detected in the uploaded image.';
        statusMessage.value = 'Ready to scan';
    } finally {
        URL.revokeObjectURL(objectUrl);
        event.target.value = '';
    }
}

function triggerUpload() {
    uploadInput.value?.click();
}

function handleDecoded(value) {
    decodedValue.value = value;
    statusMessage.value = 'QR code detected';
    stopScanner();

    const target = resolveTarget(value);

    if (target) {
        statusMessage.value = 'QR code detected. Opening record...';
        window.setTimeout(() => {
            window.location.assign(target);
        }, 250);
    }
}

function resolveTarget(value) {
    const input = value.trim();

    if (!input) {
        return null;
    }

    if (input.startsWith('/')) {
        return new URL(input, window.location.origin).toString();
    }

    const uuidPattern = /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;

    if (uuidPattern.test(input)) {
        return new URL(`/equipment/${input}`, window.location.origin).toString();
    }

    try {
        const parsed = new URL(input);

        return parsed.origin === window.location.origin
            ? parsed.toString()
            : null;
    } catch {
        return null;
    }
}

function openDecodedTarget() {
    const target = resolveTarget(decodedValue.value);

    if (target) {
        window.location.assign(target);
    }
}

function resolveCameraErrorMessage(error) {
    if (!error) {
        return 'Unable to start the camera scanner.';
    }

    if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
        return 'Camera permission was denied. Allow camera access in the browser and try again.';
    }

    if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
        return 'No camera was found on this device.';
    }

    if (error.name === 'NotReadableError' || error.name === 'TrackStartError') {
        return 'The camera is already in use by another app or browser tab.';
    }

    if (error.name === 'SecurityError') {
        return 'Camera access is blocked because the page is not running in a secure context.';
    }

    return error.message || 'Unable to start the camera scanner.';
}
</script>

<template>
    <Head title="Scanner" />

    <section class="scan-page">
        <div class="scan-page-header">
            <div>
                <nav class="scan-breadcrumbs">
                    <span>Equipment</span>
                    <span>/</span>
                    <span class="scan-breadcrumb-active">QR Scanner</span>
                </nav>
                <h1 class="scan-page-title">QR Code Scanner</h1>
                <p class="scan-page-subtitle">
                    Instantly verify assets and equipment by scanning their unique identification codes using your device camera.
                </p>
            </div>

            <div class="scan-ready-pill">
                <span :class="['scan-status-dot', statusToneClass]"></span>
                <span>{{ scannerStateLabel }}</span>
            </div>
        </div>

        <div class="scan-grid">
            <section class="scan-primary-column">
                <div class="scan-card">
                    <div class="scan-card-header">
                        <div class="scan-card-title-wrap">
                            <div class="scan-card-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                    <path d="M4 7h16M7 4v16M17 4v16M4 17h16" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="scan-card-title">Live Camera Viewport</h2>
                                <p class="scan-card-copy">Point the camera at a QR code generated by this platform.</p>
                            </div>
                        </div>

                        <div class="scan-card-controls">
                            <BaseSelect
                                id="camera_device"
                                v-model="selectedDeviceId"
                                :disabled="devices.length === 0 || isStarting"
                            >
                                <option
                                    v-if="!hasLoadedDevices"
                                    value=""
                                >
                                    Loading cameras...
                                </option>
                                <option
                                    v-else-if="devices.length === 0"
                                    value=""
                                >
                                    No cameras detected yet
                                </option>
                                <option
                                    v-for="device in devices"
                                    :key="device.deviceId"
                                    :value="device.deviceId"
                                >
                                    {{ device.label || 'Camera device' }}
                                </option>
                            </BaseSelect>
                        </div>
                    </div>

                    <div class="scan-viewport">
                        <video
                            ref="videoElement"
                            class="scanner-video"
                            autoplay
                            muted
                            playsinline
                        ></video>

                        <div class="scan-corners">
                            <span class="scan-corner scan-corner-tl"></span>
                            <span class="scan-corner scan-corner-tr"></span>
                            <span class="scan-corner scan-corner-bl"></span>
                            <span class="scan-corner scan-corner-br"></span>
                            <span
                                v-if="scannerActive"
                                class="scan-line"
                            ></span>
                        </div>

                        <div
                            v-if="!scannerActive"
                            class="scan-overlay"
                        >
                            <div class="scan-overlay-panel">
                                <div class="scan-overlay-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-7 w-7">
                                        <path d="M3 7h4l2-2h6l2 2h4v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z" />
                                        <path d="m3 3 18 18" />
                                    </svg>
                                </div>
                                <p class="scan-overlay-title">Camera is Idle</p>
                                <p class="scan-overlay-copy">Permission is required before we can scan live QR codes.</p>
                                <BaseButton
                                    :disabled="!canAttemptCameraStart"
                                    @click="startScanner"
                                >
                                    {{ isStarting ? 'Starting...' : 'Start Camera' }}
                                </BaseButton>
                            </div>
                        </div>
                    </div>

                    <div class="scan-action-row">
                        <div class="scan-action-note">
                            Position the QR code inside the frame for automatic detection.
                        </div>

                        <div class="scan-action-buttons">
                            <BaseButton
                                v-if="scannerActive"
                                variant="secondary"
                                @click="stopScanner"
                            >
                                Stop Scanner
                            </BaseButton>
                            <BaseButton
                                v-else
                                :disabled="!canAttemptCameraStart"
                                @click="startScanner"
                            >
                                {{ isStarting ? 'Starting...' : 'Start Camera' }}
                            </BaseButton>
                        </div>
                    </div>
                </div>

                <div class="scan-upload-card">
                    <div class="scan-upload-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-7 w-7">
                            <path d="M12 16V7" />
                            <path d="m8 11 4-4 4 4" />
                            <path d="M20 16.5A3.5 3.5 0 0 0 16.5 13H16a5 5 0 1 0-9.7 1.5A3.5 3.5 0 0 0 7.5 21H16a4 4 0 0 0 4-4Z" />
                        </svg>
                    </div>
                    <h3 class="scan-upload-title">Image Upload Fallback</h3>
                    <p class="scan-upload-copy">
                        If camera access is blocked, upload a screenshot or clear photo of the QR code and decode it locally.
                    </p>

                    <div class="scan-upload-actions">
                        <input
                            ref="uploadInput"
                            class="hidden"
                            type="file"
                            accept="image/*"
                            @change="handleImageUpload"
                        >
                        <BaseButton
                            variant="secondary"
                            @click="triggerUpload"
                        >
                            Choose File
                        </BaseButton>
                        <span class="scan-upload-divider">OR</span>
                        <BaseButton
                            variant="secondary"
                            @click="triggerUpload"
                        >
                            Paste Image
                        </BaseButton>
                    </div>
                </div>
            </section>

            <aside class="scan-side-column">
                <div class="scan-status-card">
                    <p class="scan-side-label">Current Status</p>
                    <div class="scan-status-card-body">
                        <div class="scan-status-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                <path d="M12 6v6l4 2" />
                                <path d="M22 12a10 10 0 1 1-10-10" />
                            </svg>
                        </div>
                        <div>
                            <p class="scan-status-title">{{ scannerStateLabel }}</p>
                            <p class="scan-status-copy">{{ statusMessage }}</p>
                        </div>
                    </div>
                    <p
                        v-if="errorMessage"
                        class="scan-error-text"
                    >
                        {{ errorMessage }}
                    </p>
                </div>

                <div class="scan-tips-card">
                    <h3 class="scan-tips-title">Pro Tips for Accuracy</h3>
                    <ul class="scan-tips-list">
                        <li>Ensure the area is well-lit to reduce image noise.</li>
                        <li>Avoid glare or reflections on the QR surface.</li>
                        <li>Keep the device steady for one to two seconds.</li>
                    </ul>
                </div>

                <div class="scan-history-card">
                    <div class="scan-history-header">
                        <h3 class="scan-history-title">Session History</h3>
                        <button
                            type="button"
                            class="scan-history-clear"
                        >
                            Clear
                        </button>
                    </div>

                    <div
                        v-if="decodedValue"
                        class="scan-decoded-panel"
                    >
                        <p class="scan-side-label">Last Decoded Value</p>
                        <p class="scan-decoded-value">{{ decodedValue }}</p>
                        <BaseButton
                            v-if="resolveTarget(decodedValue)"
                            class="mt-4"
                            @click="openDecodedTarget"
                        >
                            Open Decoded Record
                        </BaseButton>
                    </div>

                    <div
                        v-else
                        class="scan-history-empty"
                    >
                        <div class="scan-history-empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6">
                                <path d="M12 8v4l3 3" />
                                <path d="M3 12a9 9 0 1 0 3-6.7" />
                                <path d="M3 4v5h5" />
                            </svg>
                        </div>
                        <p class="scan-history-empty-text">No assets scanned in this session yet.</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</template>

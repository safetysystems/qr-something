<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { BrowserCodeReader, BrowserQRCodeReader } from '@zxing/browser';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import BaseLabel from '@/Components/Base/BaseLabel.vue';
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
});

onBeforeUnmount(() => {
    stopScanner();
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

    <section class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="brand-kicker">Scanner</p>
            <h1 class="page-title mt-2">Scan equipment QR codes</h1>
            <p class="page-subtitle mt-3">
                Use the device camera or upload an image to decode equipment QR codes locally inside the app.
            </p>
        </div>

        <BaseButton
            v-if="scannerActive"
            variant="secondary"
            @click="stopScanner"
        >
            Stop scanner
        </BaseButton>
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-[1.2fr,0.85fr]">
        <BaseCard>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold">Camera scanner</h2>
                    <p class="page-subtitle mt-2">Point the camera at a QR code generated by this platform.</p>
                </div>
                <span :class="scannerActive ? 'status-pill status-pill-passed' : 'status-pill status-pill-neutral'">
                    {{ scannerActive ? 'Active' : 'Idle' }}
                </span>
            </div>

            <div class="mt-6 space-y-5">
                <div class="scanner-video-shell">
                    <video
                        ref="videoElement"
                        class="scanner-video"
                        autoplay
                        muted
                        playsinline
                    ></video>
                </div>

                <div class="grid gap-4 md:grid-cols-[1fr,auto]">
                    <div>
                        <BaseLabel for-id="camera_device">Camera</BaseLabel>
                        <BaseSelect
                            id="camera_device"
                            v-model="selectedDeviceId"
                            :disabled="devices.length === 0 || isStarting"
                        >
                            <option
                                v-if="devices.length === 0 && hasLoadedDevices"
                                value=""
                            >
                                Auto-select device when camera starts
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

                    <div class="flex items-end">
                        <BaseButton
                            :disabled="!canAttemptCameraStart"
                            @click="startScanner"
                        >
                            {{ isStarting ? 'Starting...' : 'Start camera' }}
                        </BaseButton>
                    </div>
                </div>

                <div class="card card-muted p-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.12em] text-muted">Status</p>
                    <p class="mt-2 text-sm font-medium">{{ statusMessage }}</p>
                    <p
                        v-if="errorMessage"
                        class="mt-2 text-sm text-danger"
                    >
                        {{ errorMessage }}
                    </p>
                </div>

                <p class="text-sm text-muted">
                    Mobile note: the browser will only ask for camera access on a secure `https://` deployment or on `localhost`.
                </p>
            </div>
        </BaseCard>

        <BaseCard>
            <div>
                <h2 class="text-xl font-semibold">Image upload fallback</h2>
                <p class="page-subtitle mt-2">If camera access is blocked, upload a QR image and decode it locally.</p>
            </div>

            <div class="mt-6 space-y-5">
                <label class="scanner-upload-shell">
                    <span class="text-sm font-medium">Upload QR image</span>
                    <input
                        class="scanner-upload-input"
                        type="file"
                        accept="image/*"
                        @change="handleImageUpload"
                    >
                </label>

                <div class="card card-muted p-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.12em] text-muted">Last decoded value</p>
                    <p class="mt-2 break-all text-sm font-medium">
                        {{ decodedValue || 'No QR code decoded yet.' }}
                    </p>
                </div>

                <BaseButton
                    v-if="resolveTarget(decodedValue)"
                    @click="openDecodedTarget"
                >
                    Open decoded record
                </BaseButton>
            </div>
        </BaseCard>
    </section>
</template>

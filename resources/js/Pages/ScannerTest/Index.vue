<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { BrowserCodeReader, BrowserQRCodeReader } from '@zxing/browser';
import BaseButton from '@/Components/Base/BaseButton.vue';
import BaseCard from '@/Components/Base/BaseCard.vue';
import BaseLabel from '@/Components/Base/BaseLabel.vue';
import BaseSelect from '@/Components/Base/BaseSelect.vue';

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

    <div class="min-h-screen bg-white flex flex-col items-center justify-center p-4 sm:p-6 md:p-8 font-sans">
      <div class="w-full max-w-md mx-auto space-y-6 animate-in fade-in duration-700">
        <!-- Header -->
        <div class="text-center space-y-2">
          <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-slate-100 mb-4 shadow-sm ring-1 ring-slate-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-700"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
          </div>
          <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-slate-900 tracking-tight">QR Code Scanner</h1>
          <p class="text-sm sm:text-base text-slate-500">
            Scan any QR code to view its content
          </p>
        </div>

        <!-- Scanner Area -->
        <div class="w-full space-y-4">
          <div
            id="qr-reader"
            class="w-full aspect-square rounded-2xl overflow-hidden relative shadow-inner shadow-slate-200/50 bg-slate-50 border border-slate-100"
            :class="{ 'border-2 border-dashed border-slate-300': !scannerActive && !decodedValue && !errorMessage }"
          >
            <!-- Camera View -->
            <video
              ref="videoElement"
              class="w-full h-full object-cover transition duration-300"
              :class="{ 'opacity-0': !scannerActive, 'scale-105': scannerActive }"
              autoplay
              muted
              playsinline
            ></video>

            <!-- Scanning Overlay -->
            <div 
              v-if="scannerActive" 
              class="absolute inset-x-0 h-1 bg-blue-500/50 shadow-[0_0_15px_rgba(59,130,246,0.8)] z-10 animate-scan"
            ></div>

            <!-- Placeholder -->
            <div v-if="!scannerActive && !decodedValue && !errorMessage" class="absolute inset-0 flex items-center justify-center animate-pulse">
              <div class="text-center p-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-300 mx-auto mb-3"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                <p class="text-slate-400 text-sm sm:text-base font-medium">
                  Camera view will appear here
                </p>
              </div>
            </div>

            <!-- Loading Spinner -->
            <div v-if="isStarting" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 flex items-center justify-center">
                <div class="w-8 h-8 border-3 border-slate-200 border-t-slate-800 rounded-full animate-spin"></div>
            </div>
          </div>

          <!-- Success Message -->
          <Transition enter-active-class="duration-300 ease-out" enter-from-class="opacity-0 translate-y-2">
            <div v-if="decodedValue" class="bg-green-50 border border-green-200 rounded-xl p-4 sm:p-6 space-y-3">
              <div class="flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600 flex-shrink-0 mt-0.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <div class="flex-1 min-w-0">
                  <h3 class="text-green-900 font-bold mb-2">Scan Successful!</h3>
                  <div class="bg-white rounded-lg p-3 border border-green-100 shadow-sm">
                    <p class="text-sm sm:text-base text-slate-700 break-all leading-relaxed font-medium">
                      {{ decodedValue }}
                    </p>
                  </div>
                  <p v-if="resolveTarget(decodedValue)" class="text-sm text-green-700 mt-2 font-medium animate-pulse">
                    Redirecting...
                  </p>
                </div>
              </div>
            </div>
          </Transition>

          <!-- Error Message -->
          <Transition enter-active-class="duration-300 ease-out" enter-from-class="opacity-0 translate-y-2">
            <div v-if="errorMessage && !decodedValue" class="bg-red-50 border border-red-200 rounded-xl p-4 sm:p-6">
              <div class="flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-600 flex-shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div class="flex-1">
                  <h3 class="text-red-900 font-bold mb-1">Error</h3>
                  <p class="text-sm sm:text-base text-red-700 font-medium">{{ errorMessage }}</p>
                </div>
              </div>
            </div>
          </Transition>

          <!-- Action Buttons -->
          <div class="flex flex-col gap-3 pt-2">
            <button
                v-if="!scannerActive && !decodedValue"
                @click="startScanner"
                :disabled="isStarting"
                class="w-full bg-slate-900 hover:bg-slate-800 disabled:bg-slate-300 text-white p-5 sm:p-6 text-base sm:text-lg font-bold rounded-2xl transition-all duration-200 flex items-center justify-center gap-3 shadow-lg shadow-slate-900/10 active:scale-[0.98]"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                {{ isStarting ? 'Starting...' : 'Start Scanning' }}
            </button>

            <button
                v-if="scannerActive"
                @click="stopScanner"
                class="w-full bg-white hover:bg-slate-50 text-slate-800 p-5 sm:p-6 text-base sm:text-lg font-bold rounded-2xl border-2 border-slate-200 transition-all duration-200 flex items-center justify-center gap-3 active:scale-[0.98]"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Stop Scanning
            </button>

            <button
                v-if="decodedValue"
                @click="decodedValue = ''; errorMessage = ''; startScanner()"
                class="w-full bg-slate-900 hover:bg-slate-800 text-white p-5 sm:p-6 text-base sm:text-lg font-bold rounded-2xl transition-all duration-200 flex items-center justify-center gap-3 shadow-lg shadow-slate-900/10 active:scale-[0.98]"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                Scan Another
            </button>

            <button
                v-if="decodedValue && resolveTarget(decodedValue)"
                @click="openDecodedTarget"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white p-5 sm:p-6 text-base sm:text-lg font-bold rounded-2xl transition-all duration-200 flex items-center justify-center gap-3 shadow-lg shadow-blue-600/20 active:scale-[0.98]"
            >
                Open Decoded Record
            </button>
            
            <label 
                v-if="!scannerActive && !decodedValue"
                class="w-full bg-white hover:bg-slate-50 text-slate-500 p-4 text-sm font-semibold rounded-2xl border border-slate-200 border-dashed transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer mt-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload Fallback
                <input type="file" class="hidden" accept="image/*" @change="handleImageUpload">
            </label>
          </div>
        </div>

        <!-- Instructions -->
        <div class="bg-slate-50 rounded-2xl p-4 sm:p-6 space-y-3 ring-1 ring-slate-100">
          <h3 class="text-slate-900 font-bold flex items-center gap-2 text-sm italic">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            How to use:
          </h3>
          <ol class="text-sm text-slate-500 space-y-2 list-decimal list-inside font-medium">
            <li>Click "Start Scanning" to activate your camera</li>
            <li>Point your camera at a QR code</li>
            <li>The code will be automatically detected and opened</li>
          </ol>
        </div>
      </div>
    </div>
</template>

<style scoped>
@keyframes scan {
    0% { top: 0%; opacity: 0; }
    5% { opacity: 1; }
    95% { opacity: 1; }
    100% { top: 100%; opacity: 0; }
}

.animate-scan {
    animation: scan 2s linear infinite;
}

.animate-in {
    animation-duration: 0.5s;
    animation-fill-mode: both;
}

@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

.fade-in { animation-name: fade-in; }

.border-3 { border-width: 3px; }
</style>


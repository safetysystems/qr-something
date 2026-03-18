<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Services\EquipmentQrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EquipmentQrCodeController extends Controller
{
    public function __construct(private readonly EquipmentQrCodeService $equipmentQrCodeService)
    {
    }

    public function show(Equipment $equipment): Response
    {
        $this->authorize('view', $equipment);

        $contents = $this->equipmentQrCodeService->contents($equipment);

        return response($contents, Response::HTTP_OK, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => sprintf('inline; filename="%s"', $this->equipmentQrCodeService->downloadFilename($equipment)),
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function download(Equipment $equipment): Response
    {
        $this->authorize('view', $equipment);

        $contents = $this->equipmentQrCodeService->contents($equipment);

        return response($contents, Response::HTTP_OK, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $this->equipmentQrCodeService->downloadFilename($equipment)),
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function regenerate(Request $request, Equipment $equipment): RedirectResponse|Response
    {
        $this->authorize('update', $equipment);

        $equipment = $this->equipmentQrCodeService->ensureGenerated($equipment, true);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'QR code regenerated successfully.',
                'generated_at' => $equipment->qr_code_generated_at,
            ]);
        }

        return redirect()
            ->route('equipment.show', $equipment)
            ->with('status', 'QR code regenerated successfully.');
    }
}

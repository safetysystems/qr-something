<?php

namespace App\Services;

use App\Models\Equipment;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EquipmentQrCodeService
{
    private const STORAGE_DISK = 'local';

    public function ensureGenerated(Equipment $equipment, bool $force = false): Equipment
    {
        $path = $equipment->qr_code_path ?: $this->storagePath($equipment);

        if (! $force && Storage::disk(self::STORAGE_DISK)->exists($path)) {
            if (! $equipment->qr_code_path || ! $equipment->qr_code_generated_at) {
                $equipment->forceFill([
                    'qr_code_path' => $path,
                    'qr_code_generated_at' => now(),
                ])->save();
            }

            return $equipment->refresh();
        }

        Storage::disk(self::STORAGE_DISK)->put($path, $this->buildSvg($this->payload($equipment)));

        $equipment->forceFill([
            'qr_code_path' => $path,
            'qr_code_generated_at' => now(),
        ])->save();

        return $equipment->refresh();
    }

    public function contents(Equipment $equipment): string
    {
        $equipment = $this->ensureGenerated($equipment);

        return Storage::disk(self::STORAGE_DISK)->get($equipment->qr_code_path);
    }

    public function payload(Equipment $equipment): string
    {
        return route('equipment.show', $equipment);
    }

    public function downloadFilename(Equipment $equipment): string
    {
        $name = Str::slug($equipment->name ?: 'equipment');

        return "{$name}-{$equipment->uuid}.svg";
    }

    protected function storagePath(Equipment $equipment): string
    {
        return "qr-codes/equipment/{$equipment->uuid}.svg";
    }

    protected function buildSvg(string $content): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(512, 8),
            new SvgImageBackEnd(),
        );

        $writer = new Writer($renderer);

        return $writer->writeString($content, 'UTF-8', ErrorCorrectionLevel::M());
    }
}

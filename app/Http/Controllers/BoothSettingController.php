<?php

namespace App\Http\Controllers;

use App\Models\BoothSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BoothSettingController extends Controller
{
    /**
     * Show the booth settings form.
     */
    public function edit(): View
    {
        return view('booth-settings.edit', [
            'setting' => $this->currentSetting(),
            'booth' => config('booth'),
        ]);
    }

    /**
     * Update the booth settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'receipt_footer' => ['required', 'string', 'max:255'],
            'receipt_paper' => ['required', 'in:58,80'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
        ]);

        $setting = $this->currentSetting();
        $payload = [
            'name' => trim($validated['name']),
            'address' => trim($validated['address']),
            'city' => trim($validated['city']),
            'phone' => trim((string) ($validated['phone'] ?? '')) ?: '-',
            'receipt_footer' => trim($validated['receipt_footer']),
            'receipt_paper' => $validated['receipt_paper'],
        ];

        if ($request->boolean('remove_logo')) {
            $this->deleteUploadedLogo($setting->logo_path);
            $payload['logo_path'] = null;
        }

        $newLogoPath = $this->storeLogo($request);

        if ($newLogoPath !== null) {
            $this->deleteUploadedLogo($setting->logo_path);
            $payload['logo_path'] = $newLogoPath;
        }

        $setting->fill($payload)->save();

        return redirect()
            ->route('booth-settings.edit')
            ->with('status', 'Pengaturan booth berhasil diperbarui.');
    }

    /**
     * Get or create the current booth setting row.
     */
    protected function currentSetting(): BoothSetting
    {
        $defaults = config('booth');

        return BoothSetting::query()->firstOrCreate([], [
            'name' => $defaults['name'],
            'address' => $defaults['address'],
            'city' => $defaults['city'],
            'phone' => $defaults['phone'],
            'logo_path' => $defaults['logo'],
            'receipt_footer' => $defaults['receipt_footer'],
            'receipt_paper' => $defaults['receipt_paper'],
        ]);
    }

    /**
     * Store an uploaded booth logo.
     */
    protected function storeLogo(Request $request): ?string
    {
        if (! $request->hasFile('logo')) {
            return null;
        }

        $directory = public_path('images/branding/uploads');
        File::ensureDirectoryExists($directory);

        $extension = $request->file('logo')->getClientOriginalExtension();
        $filename = Str::uuid()->toString().'.'.$extension;
        $request->file('logo')->move($directory, $filename);

        return 'images/branding/uploads/'.$filename;
    }

    /**
     * Delete uploaded logo files without touching seeded branding assets.
     */
    protected function deleteUploadedLogo(?string $logoPath): void
    {
        if (! is_string($logoPath) || ! str_starts_with($logoPath, 'images/branding/uploads/')) {
            return;
        }

        $absolutePath = public_path($logoPath);

        if (is_file($absolutePath)) {
            File::delete($absolutePath);
        }
    }
}

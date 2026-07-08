<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\LicenseAddon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LicenseAdminController extends Controller
{
    public function index(Request $request): View
    {
        $q = License::query()->withCount('activations');

        if ($search = $request->get('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('license_key', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $q->where('status', $status);
        }

        $licenses = $q->latest()->paginate(20)->withQueryString();

        return view('admin.licenses.index', compact('licenses'));
    }

    public function create(): View
    {
        $license = new License([
            'product'          => config('license.default_product'),
            'status'           => 'active',
            'activation_limit' => 1,
        ]);

        return view('admin.licenses.create', compact('license'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $data['license_key'] = $request->filled('license_key')
            ? strtoupper(trim($request->license_key))
            : License::generateKey();

        $license = License::create($data);

        return redirect()
            ->route('licenses.show', $license)
            ->with('status', 'License created. Key: ' . $license->license_key);
    }

    public function show(License $license): View
    {
        $license->load(['activations', 'addons']);

        return view('admin.licenses.show', compact('license'));
    }

    public function edit(License $license): View
    {
        return view('admin.licenses.edit', compact('license'));
    }

    public function update(Request $request, License $license): RedirectResponse
    {
        $license->update($this->validated($request));

        return redirect()
            ->route('licenses.show', $license)
            ->with('status', 'License updated.');
    }

    public function destroy(License $license): RedirectResponse
    {
        $license->delete();

        return redirect()->route('licenses.index')->with('status', 'License deleted.');
    }

    public function addAddon(Request $request, License $license): RedirectResponse
    {
        $data = $request->validate([
            'addon_identifier' => ['required', 'string', 'max:100'],
            'label'            => ['nullable', 'string', 'max:100'],
            'expires_at'       => ['nullable', 'date'],
        ]);

        $license->addons()->updateOrCreate(
            ['addon_identifier' => $data['addon_identifier']],
            ['label' => $data['label'] ?? null, 'expires_at' => $data['expires_at'] ?? null],
        );

        return back()->with('status', 'Addon entitlement saved.');
    }

    public function removeAddon(License $license, LicenseAddon $addon): RedirectResponse
    {
        abort_unless($addon->license_id === $license->id, 404);
        $addon->delete();

        return back()->with('status', 'Addon entitlement removed.');
    }

    public function removeActivation(License $license, LicenseActivation $activation): RedirectResponse
    {
        abort_unless($activation->license_id === $license->id, 404);
        $activation->delete();

        return back()->with('status', 'Activation released.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'license_key'      => ['nullable', 'string', 'max:64'],
            'product'          => ['required', 'string', 'max:100'],
            'customer_name'    => ['nullable', 'string', 'max:150'],
            'customer_email'   => ['nullable', 'email', 'max:150'],
            'status'           => ['required', 'in:active,suspended,revoked'],
            'expires_at'       => ['nullable', 'date'],
            'activation_limit' => ['required', 'integer', 'min:1', 'max:1000'],
            'notes'            => ['nullable', 'string', 'max:2000'],
        ]);
    }
}

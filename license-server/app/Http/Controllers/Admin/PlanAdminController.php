<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanAdminController extends Controller
{
    public function index(): View
    {
        $plans = Plan::withCount(['licenses', 'orders'])
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        return view('admin.plans.index', compact('plans'));
    }

    public function create(): View
    {
        $plan = new Plan([
            'currency'         => 'INR',
            'billing_period'   => 'yearly',
            'activation_limit' => 1,
            'is_active'        => true,
            'sort_order'       => 0,
        ]);

        return view('admin.plans.create', compact('plan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Plan::uniqueSlugFor($data['name']);

        $plan = Plan::create($data);

        return redirect()->route('plans.index')->with('status', "Plan “{$plan->name}” created.");
    }

    public function edit(Plan $plan): View
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $this->validated($request);
        if ($data['name'] !== $plan->name) {
            $data['slug'] = Plan::uniqueSlugFor($data['name'], $plan->id);
        }

        $plan->update($data);

        return redirect()->route('plans.index')->with('status', "Plan “{$plan->name}” updated.");
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->licenses()->exists() || $plan->orders()->exists()) {
            return back()->withErrors(['plan' => 'This plan has licenses/orders attached. Deactivate it instead of deleting.']);
        }

        $plan->delete();

        return redirect()->route('plans.index')->with('status', 'Plan deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:100'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'price'            => ['required', 'numeric', 'min:0'],
            'currency'         => ['required', 'string', 'max:10'],
            'billing_period'   => ['required', 'in:monthly,yearly,lifetime'],
            'duration_days'    => ['nullable', 'integer', 'min:1'],
            'activation_limit' => ['required', 'integer', 'min:1', 'max:1000'],
            'modules_text'     => ['nullable', 'string', 'max:2000'],
            'features_text'    => ['nullable', 'string', 'max:4000'],
            'payment_link'     => ['nullable', 'url', 'max:500'],
            'is_featured'      => ['nullable', 'boolean'],
            'is_active'        => ['nullable', 'boolean'],
            'sort_order'       => ['nullable', 'integer', 'min:0'],
        ]);

        // Textareas → arrays (one entry per line, trimmed, empties dropped).
        $lines = fn (?string $text) => array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $text))));

        return [
            'name'             => $data['name'],
            'description'      => $data['description'] ?? null,
            'price'            => $data['price'],
            'currency'         => strtoupper($data['currency']),
            'billing_period'   => $data['billing_period'],
            'duration_days'    => $data['duration_days'] ?? null,
            'activation_limit' => $data['activation_limit'],
            'modules'          => $lines($data['modules_text'] ?? null),
            'features'         => $lines($data['features_text'] ?? null),
            'payment_link'     => $data['payment_link'] ?? null,
            'is_featured'      => (bool) ($data['is_featured'] ?? false),
            'is_active'        => (bool) ($data['is_active'] ?? false),
            'sort_order'       => (int) ($data['sort_order'] ?? 0),
        ];
    }
}

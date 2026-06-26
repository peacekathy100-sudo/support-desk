<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\TicketCategory;
use App\Services\AuditService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class TicketCategoryController extends Controller
{
    public function __construct(private AuditService $auditor)
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $this->gate('manage_settings');
        $categories = TicketCategory::withCount('tickets')->latest()->get();
        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        $this->gate('manage_settings');
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->gate('manage_settings');

        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:ticket_categories,name',
            'color'       => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'sla_hours'   => 'required|integer|min:1|max:720',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $category = TicketCategory::create($data);

        $this->auditor->log('created', 'TicketCategory', $category->id, null, $data);

        return redirect()->route('categories.index')
                         ->with('success', "Category '{$category->name}' created.");
    }

    public function edit(TicketCategory $category): View
    {
        $this->gate('manage_settings');
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, TicketCategory $category): RedirectResponse
    {
        $this->gate('manage_settings');

        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:ticket_categories,name,' . $category->id,
            'color'       => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'sla_hours'   => 'required|integer|min:1|max:720',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'nullable|boolean',
        ]);

        $old = $category->toArray();
        $data['is_active'] = $request->boolean('is_active', true);
        $category->update($data);

        $this->auditor->log('updated', 'TicketCategory', $category->id, $old, $data);

        return redirect()->route('categories.index')
                         ->with('success', 'Category updated.');
    }

    public function destroy(TicketCategory $category): RedirectResponse
    {
        $this->gate('manage_settings');

        if ($category->tickets()->count() > 0) {
            return back()->with('error', 'Cannot delete category that has tickets assigned to it.');
        }

        $this->auditor->log('deleted', 'TicketCategory', $category->id, $category->toArray(), null);
        $category->delete();

        return redirect()->route('categories.index')
                         ->with('success', 'Category deleted.');
    }
}

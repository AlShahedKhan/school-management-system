<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashboardNewsRequest;
use App\Models\DashboardNews;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardNewsController extends Controller
{
    public function index(): View
    {
        $newsItems = DashboardNews::query()
            ->displayOrder()
            ->paginate(12);

        return view('admin.dashboard-news.index', compact('newsItems'));
    }

    public function create(): View
    {
        return view('admin.dashboard-news.create');
    }

    public function store(DashboardNewsRequest $request): RedirectResponse
    {
        DashboardNews::create($request->validated());
        $this->clearDashboardNewsCache();

        return redirect()
            ->route('admin.dashboard-news.index')
            ->with('success', 'Dashboard news created successfully.');
    }

    public function edit(DashboardNews $dashboardNews): View
    {
        return view('admin.dashboard-news.edit', compact('dashboardNews'));
    }

    public function update(DashboardNewsRequest $request, DashboardNews $dashboardNews): RedirectResponse
    {
        $dashboardNews->update($request->validated());
        $this->clearDashboardNewsCache();

        return redirect()
            ->route('admin.dashboard-news.index')
            ->with('success', 'Dashboard news updated successfully.');
    }

    public function destroy(DashboardNews $dashboardNews): RedirectResponse
    {
        $dashboardNews->delete();
        $this->clearDashboardNewsCache();

        return redirect()
            ->route('admin.dashboard-news.index')
            ->with('success', 'Dashboard news deleted successfully.');
    }

    private function clearDashboardNewsCache(): void
    {
        Cache::forget('dashboard_news.latest');
        Cache::forget('dashboard_news.active');
    }
}

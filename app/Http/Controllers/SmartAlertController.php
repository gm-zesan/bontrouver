<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSmartAlertRequest;
use App\Models\Category;
use App\Models\SmartAlert;
use App\Services\SmartAlertService;
use Illuminate\Http\Request;

class SmartAlertController extends Controller
{
    public function __construct(
        private SmartAlertService $smartAlertService
    ) {}

    public function index()
    {
        $alerts = auth()->user()->smartAlerts()->with('category')->get();
        return view('frontend.account.alerts.index', compact('alerts'));
    }

    public function create()
    {
        $categories = Category::whereNull('parent_id')->with('children')->get();
        return view('frontend.account.alerts.create', compact('categories'));
    }

    public function store(StoreSmartAlertRequest $request)
    {
        $this->smartAlertService->create($request->validated(), auth()->id());
        return redirect()->route('account.alerts.index')->with('success', 'Smart Alert created successfully!');
    }

    public function destroy(SmartAlert $alert)
    {
        if ($alert->user_id !== auth()->id()) {
            abort(403);
        }
        $this->smartAlertService->delete($alert);
        return redirect()->route('account.alerts.index')->with('success', 'Smart Alert deleted successfully!');
    }
}

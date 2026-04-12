<?php

namespace App\Http\Controllers\Admin\Temple;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Temple\StoreFacilityRequest;
use App\Http\Requests\Admin\Temple\UpdateFacilityRequest;
use App\Models\Temple\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function index(Request $request): View
    {
        $query = Facility::query()->latest('id');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('key', 'like', '%' . $search . '%')
                    ->orWhere('icon', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->integer('is_active'));
        }

        $facilities = $query->paginate(15)->withQueryString();

        return view('admin.facilities.index', [
            'facilities' => $facilities,
        ]);
    }

    public function create(): View
    {
        return view('admin.facilities.create');
    }

    public function store(StoreFacilityRequest $request): RedirectResponse
    {
        $facility = Facility::create($request->validated());

        return redirect()
            ->route('admin.facilities.edit', $facility)
            ->with('success', 'สร้าง facility เรียบร้อยแล้ว');
    }

    public function edit(Facility $facility): View
    {
        return view('admin.facilities.edit', [
            'facility' => $facility,
        ]);
    }

    public function update(UpdateFacilityRequest $request, Facility $facility): RedirectResponse
    {
        $facility->update($request->validated());

        return redirect()
            ->route('admin.facilities.edit', $facility)
            ->with('success', 'อัปเดต facility เรียบร้อยแล้ว');
    }

    public function destroy(Facility $facility): RedirectResponse
    {
        if ($facility->templeFacilities()->exists()) {
            return redirect()
                ->route('admin.facilities.index')
                ->with('error', 'ไม่สามารถลบ facility นี้ได้ เพราะยังถูกใช้งานอยู่');
        }

        $facility->delete();

        return redirect()
            ->route('admin.facilities.index')
            ->with('success', 'ลบ facility เรียบร้อยแล้ว');
    }
}
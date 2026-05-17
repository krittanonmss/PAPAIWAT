<?php

namespace App\Http\Controllers\Admin\Content\Layout;

use App\Http\Controllers\Controller;
use App\Models\Content\Content;
use App\Models\Content\Layout\Template;
use App\Services\Admin\Content\Layout\LayoutVersionService;
use App\Support\TemplateRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function __construct(
        private readonly TemplateRegistry $templateRegistry,
        private readonly LayoutVersionService $versionService,
    ) {
    }

    public function index(): View
    {
        $templates = Template::query()
            ->withCount('pages')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.content.layout.templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('admin.content.layout.templates.create', [
            'registryTemplates' => $this->templateRegistry->templates(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:255', 'unique:templates,key'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_default' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $registered = $this->templateRegistry->template($validated['key']);
        $validated['view_path'] = $registered['view_path'];
        $validated['template_type'] = $registered['template_type'];
        $validated['content_type'] = $registered['content_type'];
        $validated['schema'] = $registered['schema'] ?? null;
        $validated['is_default'] = $request->boolean('is_default');
        $validated['sort_order'] = $validated['sort_order'] ?? $registered['sort_order'] ?? 0;

        DB::transaction(function () use ($validated) {
            if ($validated['is_default']) {
                Template::query()
                    ->where('template_type', $validated['template_type'])
                    ->where('content_type', $validated['content_type'])
                    ->update(['is_default' => false]);
            }

            $template = Template::create($validated);
            $this->versionService->snapshotTemplate($template, 'created');
        });

        return redirect()
            ->route('admin.content.templates.index')
            ->with('success', 'สร้าง Template เรียบร้อยแล้ว');
    }

    public function show(Template $template): View
    {
        $template->loadCount('pages');

        return view('admin.content.layout.templates.show', compact('template'));
    }

    public function edit(Template $template): View
    {
        return view('admin.content.layout.templates.edit', [
            'template' => $template,
            'registryTemplates' => $this->templateRegistry->templates(),
        ]);
    }

    public function update(Request $request, Template $template): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:255', 'unique:templates,key,' . $template->id],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'is_default' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $registered = $this->templateRegistry->template($validated['key']);
        $validated['view_path'] = $registered['view_path'];
        $validated['template_type'] = $registered['template_type'];
        $validated['content_type'] = $registered['content_type'];
        $validated['schema'] = $registered['schema'] ?? null;
        $validated['is_default'] = $request->boolean('is_default');
        $validated['sort_order'] = $validated['sort_order'] ?? $registered['sort_order'] ?? 0;

        DB::transaction(function () use ($template, $validated) {
            $this->versionService->snapshotTemplate($template, 'before_update');

            if ($validated['is_default']) {
                Template::query()
                    ->whereKeyNot($template->id)
                    ->where('template_type', $validated['template_type'])
                    ->where('content_type', $validated['content_type'])
                    ->update(['is_default' => false]);
            }

            $template->update($validated);
            $this->versionService->snapshotTemplate($template, 'updated');
        });

        return redirect()
            ->route('admin.content.templates.index')
            ->with('success', 'อัปเดต Template เรียบร้อยแล้ว');
    }

    public function destroy(Template $template): RedirectResponse
    {
        if ($template->is_default) {
            return back()->withErrors(['template' => 'ไม่สามารถลบ default template ได้']);
        }

        if ($template->pages()->exists() || Content::query()->where('template_id', $template->id)->exists()) {
            return back()->withErrors(['template' => 'ไม่สามารถลบ template ที่ถูกใช้งานอยู่']);
        }

        DB::transaction(function () use ($template) {
            $this->versionService->snapshotTemplate($template, 'before_delete');
            $template->delete();
        });

        return redirect()
            ->route('admin.content.templates.index')
            ->with('success', 'ลบ Template เรียบร้อยแล้ว');
    }
}

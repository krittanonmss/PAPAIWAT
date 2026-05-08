<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\AdminActivityLog;
use App\Models\Admin\AdminSession;
use App\Models\Admin\LoginLog;
use App\Models\Content\Article\Article;
use App\Models\Content\Category;
use App\Models\Content\Content;
use App\Models\Content\Layout\Page;
use App\Models\Content\Media\Media;
use App\Models\Content\Temple\Temple;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $contentStatusCounts = Content::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $contentTypeCounts = Content::query()
            ->select('content_type', DB::raw('count(*) as total'))
            ->groupBy('content_type')
            ->pluck('total', 'content_type');

        $mediaStatusCounts = Media::query()
            ->select('upload_status', DB::raw('count(*) as total'))
            ->groupBy('upload_status')
            ->pluck('total', 'upload_status');

        $loginLogs = LoginLog::query()
            ->with('admin')
            ->latest('created_at')
            ->limit(8)
            ->get();

        $recentActivities = AdminActivityLog::query()
            ->with('admin')
            ->latest('created_at')
            ->limit(8)
            ->get();

        $recentContents = Content::query()
            ->with(['creator', 'updater'])
            ->latest('updated_at')
            ->limit(8)
            ->get();

        $recentMedia = Media::query()
            ->with('uploader')
            ->latest('uploaded_at')
            ->limit(6)
            ->get();

        return view('admin.dashboard.dashboard', [
            'stats' => [
                'admins' => Admin::query()->count(),
                'active_admins' => Admin::query()->where('status', 'active')->count(),
                'active_sessions' => AdminSession::query()
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    })
                    ->count(),
                'temples' => Temple::query()->count(),
                'articles' => Article::query()->count(),
                'pages' => Page::query()->count(),
                'categories' => Category::query()->count(),
                'media' => Media::query()->count(),
                'published_content' => (int) ($contentStatusCounts['published'] ?? 0),
                'draft_content' => (int) ($contentStatusCounts['draft'] ?? 0),
                'archived_content' => (int) ($contentStatusCounts['archived'] ?? 0),
                'failed_logins_24h' => LoginLog::query()
                    ->where('status', 'failed')
                    ->where('created_at', '>=', now()->subDay())
                    ->count(),
                'successful_logins_24h' => LoginLog::query()
                    ->where('status', 'success')
                    ->where('created_at', '>=', now()->subDay())
                    ->count(),
            ],
            'contentStatusCounts' => $contentStatusCounts,
            'contentTypeCounts' => $contentTypeCounts,
            'mediaStatusCounts' => $mediaStatusCounts,
            'loginLogs' => $loginLogs,
            'recentActivities' => $recentActivities,
            'recentContents' => $recentContents,
            'recentMedia' => $recentMedia,
        ]);
    }
}

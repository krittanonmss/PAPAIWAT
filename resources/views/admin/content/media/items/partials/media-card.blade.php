<div class="absolute right-2.5 top-2.5 flex items-center gap-1.5">
    <a
        href="{{ route('admin.media.edit', $media->id) }}"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-950/70 text-white hover:bg-slate-800"
        title="Edit"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.862 4.487a2.1 2.1 0 113.01 2.926L9.75 17.25 6 18l.75-3.75 10.112-9.763z" />
        </svg>
    </a>

    @if ($media->visibility === 'public')
        <a
            href="{{ asset('storage/' . $media->path) }}"
            target="_blank"
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-950/70 text-white hover:bg-slate-950"
            title="View"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 10l4.5-4.5m0 0H16.5m3 0V8.5M9 14l-4.5 4.5m0 0H7.5m-3 0v-3" />
            </svg>
        </a>
    @endif

    <form
        method="POST"
        action="{{ route('admin.media.destroy', $media->id) }}"
        onsubmit="return confirm('Are you sure you want to delete this media file?');"
    >
        @csrf
        @method('DELETE')

        <button
            type="submit"
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-950/70 text-white hover:bg-red-600"
            title="Delete"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 7h12M9 7V5.75A1.75 1.75 0 0110.75 4h2.5A1.75 1.75 0 0115 5.75V7m-7 0l.6 10.2A2 2 0 0010.6 19h2.8a2 2 0 001.997-1.8L16 7M10 10.5v5M14 10.5v5" />
            </svg>
        </button>
    </form>
</div>
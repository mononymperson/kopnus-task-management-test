@extends('layouts.app', ['title' => 'Riwayat Aktivitas'])

@section('content')
<div class="bg-white shadow rounded-lg border border-slate-200 p-6">
    <div class="pb-4 border-b border-slate-200">
        <h1 class="text-xl font-bold text-slate-900">Riwayat Aktivitas Saya</h1>
        <p class="text-sm text-slate-500 mt-1">Aktivitas proyek dan tugas milik Anda.</p>
    </div>

    <form method="GET" action="{{ route('activities.index') }}" class="mt-5 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="action" class="block text-sm font-medium text-slate-700">Jenis Aksi</label>
            <select id="action" name="action"
                class="mt-1 block w-full rounded-md border @error('action') border-red-500 @else border-slate-300 @enderror px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
                <option value="">Semua aksi</option>
                @foreach(App\Models\ActivityLog::ACTIONS as $key => $label)
                    <option value="{{ $key }}" {{ ($filters['action'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('action')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="from" class="block text-sm font-medium text-slate-700">Dari Tanggal</label>
            <input type="date" id="from" name="from" value="{{ $filters['from'] ?? '' }}"
                class="mt-1 block w-full rounded-md border @error('from') border-red-500 @else border-slate-300 @enderror px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
            @error('from')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="to" class="block text-sm font-medium text-slate-700">Sampai Tanggal</label>
            <input type="date" id="to" name="to" value="{{ $filters['to'] ?? '' }}"
                class="mt-1 block w-full rounded-md border @error('to') border-red-500 @else border-slate-300 @enderror px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
            @error('to')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Filter
            </button>
            <a href="{{ route('activities.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                Reset
            </a>
        </div>
    </form>

    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Waktu</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Aksi</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($logs as $log)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-slate-500">
                            {{ $log->created_at->format('d M Y, H:i') }}
                            <div class="text-xs text-slate-400">{{ $log->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                                {{ App\Models\ActivityLog::ACTIONS[$log->action] ?? $log->action }}
                            </span>
                            <div class="mt-1 text-xs text-slate-400">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            @if($log->action === App\Models\ActivityLog::TASK_STATUS_UPDATED)
                                <code class="text-amber-700">{{ $log->payload['old_status'] ?? '-' }}</code>
                                &rarr;
                                <code class="text-green-700">{{ $log->payload['new_status'] ?? '-' }}</code>
                            @else
                                {{ $log->payload['name'] ?? $log->payload['title'] ?? '-' }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-10 text-center text-slate-500">
                            Tidak ada aktivitas yang cocok dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>
</div>
@endsection

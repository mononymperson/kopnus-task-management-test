@extends('layouts.app', ['title' => 'Detail Proyek: ' . $project->name])

@section('content')
<div class="space-y-8">
    <!-- Header Proyek -->
    <div class="bg-white shadow rounded-lg border border-slate-200 p-6">
        <div class="flex items-center justify-between pb-4 border-slate-200">
            <div>
                <a href="{{ route('dashboard') }}" class="text-xs text-indigo-600 hover:text-indigo-500 font-medium">&larr; Kembali ke Dashboard</a>
                <h1 class="text-2xl font-bold text-slate-900 mt-1">{{ $project->name }}</h1>
                <p class="text-xs text-slate-400 mt-0.5">Dibuat pada {{ $project->created_at->format('d M Y, H:i') }}</p>
            </div>
            @can('delete', $project)
                <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Hapus proyek ini beserta seluruh tugasnya?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-md bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100">
                        Hapus Proyek
                    </button>
                </form>
            @endcan
        </div>
        <div class="mt-4 text-sm text-slate-700">
            <h4 class="font-medium text-slate-900">Deskripsi:</h4>
            <p class="mt-1 whitespace-pre-line text-slate-600">{{ $project->description ?: 'Tidak ada deskripsi.' }}</p>
        </div>

        @can('update', $project)
            <details class="mt-5 border-t border-slate-200 pt-4" {{ $errors->has('name') || $errors->has('description') ? 'open' : '' }}>
                <summary class="cursor-pointer text-sm font-semibold text-indigo-600 hover:text-indigo-500">Edit Proyek</summary>
                <form action="{{ route('projects.update', $project) }}" method="POST" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-slate-700">Nama Proyek</label>
                        <input type="text" id="edit_name" name="name" value="{{ old('name', $project->name) }}"
                            class="mt-1 block w-full rounded-md border @error('name') border-red-500 @else border-slate-300 @enderror px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="edit_description" class="block text-sm font-medium text-slate-700">Deskripsi</label>
                        <textarea id="edit_description" name="description" rows="2"
                            class="mt-1 block w-full rounded-md border @error('description') border-red-500 @else border-slate-300 @enderror px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">{{ old('description', $project->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </details>
        @endcan
    </div>

    <!-- Grid Tugas & Form Tambah -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Tambah Tugas -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg border border-slate-200 p-6 sticky top-24">
                <h2 class="text-lg font-bold text-slate-900 mb-4">Tambah Tugas Baru</h2>

                @can('create', [App\Models\Task::class, $project])
                    <form action="{{ route('tasks.store', $project) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="title" class="block text-sm font-medium text-slate-700">Judul Tugas <span class="text-red-500">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}"
                                placeholder="Contoh: Membuat rancangan database"
                                class="mt-1 block w-full rounded-md border @error('title') border-red-500 @else border-slate-300 @enderror px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="due_date" class="block text-sm font-medium text-slate-700">Tenggat Waktu (Opsional)</label>
                            <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}"
                                class="mt-1 block w-full rounded-md border @error('due_date') border-red-500 @else border-slate-300 @enderror px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
                            @error('due_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            Simpan Tugas
                        </button>
                    </form>
                @else
                    <div class="rounded-md bg-amber-50 p-4 border border-amber-200 text-sm text-amber-800">
                        Anda tidak memiliki hak akses untuk menambah tugas pada proyek ini.
                    </div>
                @endcan
            </div>
        </div>

        <!-- Daftar Tugas -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg border border-slate-200 p-6">
                @error('status')
                    <p class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-600 border border-red-200">{{ $message }}</p>
                @enderror

                <div class="flex items-center justify-between mb-4 border-b border-slate-200 pb-3">
                    <h2 class="text-lg font-bold text-slate-900">Daftar Tugas</h2>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                        Total: {{ $project->tasks->count() }}
                    </span>
                </div>

                @if($project->tasks->isEmpty())
                    <div class="text-center py-10">
                        <p class="text-slate-500 text-sm">Belum ada tugas pada proyek ini. Tambahkan tugas pertama melalui form di samping.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($project->tasks as $task)
                            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <h3 class="font-medium text-slate-900 text-base {{ $task->status === 'completed' ? 'line-through text-slate-400' : '' }}">
                                        {{ $task->title }}
                                    </h3>
                                    <div class="flex items-center gap-2 text-xs text-slate-500">
                                        @if($task->due_date)
                                            <span>Tenggat: {{ $task->due_date->format('d M Y') }}</span>
                                            <span>&bull;</span>
                                        @endif
                                        <span>Dibuat: {{ $task->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <!-- Status Badge -->
                                    @if($task->status === 'pending')
                                        <span class="inline-flex items-center rounded-md bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20">
                                            Pending
                                        </span>
                                    @elseif($task->status === 'in_progress')
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                            In Progress
                                        </span>
                                    @elseif($task->status === 'completed')
                                        <span class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                            Completed
                                        </span>
                                    @endif

                                    <!-- Tombol Ubah Status -->
                                    @can('update', $task)
                                        @if($task->status === 'pending')
                                            <form action="{{ route('tasks.updateStatus', $task) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="in_progress">
                                                <button type="submit"
                                                    class="inline-flex items-center rounded bg-blue-600 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-500">
                                                    Mulai &rarr;
                                                </button>
                                            </form>
                                        @elseif($task->status === 'in_progress')
                                            <form action="{{ route('tasks.updateStatus', $task) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit"
                                                    class="inline-flex items-center rounded bg-green-600 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-green-500">
                                                    Selesaikan &#10003;
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                    @can('delete', $task)
                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-slate-400 hover:text-red-600">
                                                Hapus
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

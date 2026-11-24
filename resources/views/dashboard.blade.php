@extends('layouts.app', ['title' => 'Dashboard Proyek'])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Tambah Proyek -->
    <div class="lg:col-span-1">
        <div class="bg-white shadow rounded-lg border border-slate-200 p-6 sticky top-24">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Buat Proyek Baru</h2>
            
            <form action="{{ route('projects.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700">Nama Proyek <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            placeholder="Contoh: Redesign Website"
                            class="mt-1 block w-full rounded-md border @error('name') border-red-500 @else border-slate-300 @enderror px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700">Deskripsi (Opsional)</label>
                        <textarea id="description" name="description" rows="3"
                            placeholder="Deskripsi singkat mengenai proyek..."
                            class="mt-1 block w-full rounded-md border @error('description') border-red-500 @else border-slate-300 @enderror px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Simpan Proyek
                    </button>
                </form>
        </div>
    </div>

    <!-- Daftar Proyek -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow rounded-lg border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4 border-b border-slate-200 pb-3">
                <h2 class="text-lg font-bold text-slate-900">Daftar Proyek Saya</h2>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                    Total: {{ $projects->count() }}
                </span>
            </div>

            @if($projects->isEmpty())
                <div class="text-center py-12">
                    <p class="text-slate-500 text-sm">Belum ada proyek. Silakan buat proyek baru melalui formulir di samping.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($projects as $project)
                        <div class="border border-slate-200 rounded-lg p-5 hover:border-indigo-300 hover:shadow-sm transition flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between">
                                    <h3 class="font-semibold text-slate-900 text-base">
                                        <a href="{{ route('projects.show', $project) }}" class="hover:text-indigo-600">
                                            {{ $project->name }}
                                        </a>
                                    </h3>
                                    @can('delete', $project)
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Hapus proyek ini beserta seluruh tugasnya?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-600 text-xs font-medium">
                                                Hapus
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                                <p class="mt-2 text-sm text-slate-600 line-clamp-2">
                                    {{ $project->description ?: 'Tidak ada deskripsi.' }}
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                <span>{{ $project->tasks_count }} tugas</span>
                                <a href="{{ route('projects.show', $project) }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                                    Lihat Detail &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

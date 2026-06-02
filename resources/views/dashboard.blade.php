<x-app-layout>
    <div class="min-h-screen bg-slate-100 text-slate-900 p-6 font-sans">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        ⚙️ Admin Control Panel <span class="text-xs bg-blue-600 text-white px-2 py-0.5 rounded-md font-bold">v3.0</span>
                    </h1>
                    <p class="text-xs text-gray-500 mt-1">Manage system configurations, folders, and uploaded file assets.</p>
                </div>
                <div class="flex gap-4 text-xs font-semibold bg-slate-50 p-3 rounded-xl border border-gray-200 text-gray-700">
                    <span class="text-emerald-600">● Database: Connected</span>
                    <span class="text-gray-300">|</span>
                    <span class="text-blue-600">Disk Used: {{ round($files->sum('file_size') / 1024, 1) }} KB</span>
                </div>
            </div>

            <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm flex flex-wrap gap-3 items-center">
                <form action="{{ route('media.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <input type="file" name="file" id="fileInput" class="hidden" onchange="this.form.submit()">
                    <button type="button" onclick="document.getElementById('fileInput').click()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold text-xs transition shadow-sm">
                        📤 Upload File
                    </button>
                </form>

                <button type="button" onclick="openModal('folderModal')" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-bold text-xs transition">
                    📂 New Folder
                </button>
                <button type="button" onclick="openModal('fileModal')" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-bold text-xs transition">
                    📄 New File
                </button>
                
                <button type="button" onclick="toggleSelectAll()" class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 px-4 py-2 rounded-lg font-bold text-xs transition ml-auto">
                    ✅ Select All
                </button>
            </div>

            @if (session('success'))
                <div class="p-4 text-xs text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl font-bold">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
                @if($files->isEmpty())
                    <div class="text-center py-16 border-2 border-dashed border-gray-200 rounded-xl bg-slate-50">
                        <div class="text-4xl mb-2">📂</div>
                        <p class="text-sm font-bold text-gray-500">Your file storage system is empty.</p>
                        <p class="text-xs text-gray-400 mt-1">Use the options on the toolbar above to populate files.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($files as $file)
                            @php
                                $isFolder = ($file->name === '.placeholder');
                            @endphp
                            <div class="bg-slate-50 border border-gray-200 p-4 rounded-xl flex flex-col justify-between hover:border-blue-400 transition relative group shadow-xs">
                                
                                <div class="flex items-start gap-3 min-w-0">
                                    <input type="checkbox" class="file-checkbox accent-blue-600 h-4 w-4 rounded bg-white border-gray-300 mt-1 shrink-0">
                                    
                                    @if($isFolder)
                                        <div class="text-3xl text-amber-500 shrink-0">📁</div>
                                    @else
                                        <div class="text-3xl text-blue-500 shrink-0">📄</div>
                                    @endif
                                    
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-gray-900 truncate" title="{{ $file->name }}">
                                            {{ $isFolder ? $file->folder : $file->name }}
                                        </h4>
                                        <p class="text-[10px] text-gray-500 mt-0.5 font-medium uppercase tracking-wider">
                                            📁 {{ $file->folder }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between border-t border-gray-200 pt-3 mt-4 text-[11px] font-bold">
                                    <a href="/storage/{{ $file->file_path }}" target="_blank" class="text-blue-600 hover:underline">View</a>
                                    
                                    <textarea id="file-content-{{ $file->id }}" class="hidden">{{ \Illuminate\Support\Facades\Storage::disk('public')->exists($file->file_path) ? \Illuminate\Support\Facades\Storage::disk('public')->get($file->file_path) : '' }}</textarea>
                                    
                                    <button type="button" onclick="openEditModal({{ $file->id }}, '{{ addslashes($file->name) }}', '{{ addslashes($file->folder) }}')" class="text-amber-600 hover:underline">Edit</button>
                                    
                                    <form action="{{ route('media.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Permanently delete this file asset?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="folderModal" style="display: none;" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 w-full max-w-sm shadow-2xl space-y-4">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2 border-b border-gray-100 pb-2">📂 Create New Folder</h3>
            <form action="{{ route('media.createFile') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="name" value=".placeholder">
                <div>
                    <label class="text-[11px] font-bold text-gray-600 block mb-1">Folder Name Directory Path</label>
                    <input type="text" name="folder" placeholder="e.g., assets, documents, downloads" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 p-2.5 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                </div>
                <div class="flex justify-end gap-2 pt-1 text-xs font-bold">
                    <button type="button" onclick="closeModal('folderModal')" class="text-gray-400 px-3 py-2 hover:text-gray-600">Cancel</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition">Create Folder</button>
                </div>
            </form>
        </div>
    </div>

    <div id="fileModal" style="display: none;" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 w-full max-w-md shadow-2xl space-y-4">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2 border-b border-gray-100 pb-2">📄 New Live File Generator</h3>
            <form action="{{ route('media.createFile') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[11px] font-bold text-gray-600 block mb-1">Filename Target (with extension)</label>
                        <input type="text" name="name" placeholder="index.html, test.txt" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 p-2.5 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-600 block mb-1">Destination Folder Path</label>
                        <input type="text" name="folder" value="root" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 p-2.5 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="text-[11px] font-bold text-gray-600 block mb-1">Raw Content Space</label>
                    <textarea name="content" placeholder="Type text data lines or code structures here..." class="w-full bg-slate-50 border border-gray-300 text-gray-800 p-3 rounded-xl text-xs font-mono h-28 focus:outline-none focus:border-blue-500"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-1 text-xs font-bold">
                    <button type="button" onclick="closeModal('fileModal')" class="text-gray-400 px-3 py-2 hover:text-gray-600">Discard</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition">Save & Compile File</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" style="display: none;" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 w-full max-w-xl shadow-2xl space-y-4">
            <h3 class="text-sm font-bold text-gray-900 border-b border-gray-100 pb-2">✏️ Edit File Attributes & Code Content</h3>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[11px] font-bold text-gray-600 block mb-1">Filename Label</label>
                        <input type="text" name="name" id="editName" class="w-full bg-slate-50 border border-gray-300 text-gray-900 p-2.5 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-600 block mb-1">Target Directory Namespace</label>
                        <input type="text" name="folder" id="editFolder" class="w-full bg-slate-50 border border-gray-300 text-gray-900 p-2.5 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="text-[11px] font-bold text-gray-600 block mb-1">File Code / Content</label>
                    <textarea name="content" id="editContent" placeholder="Modify file code contents here..." class="w-full bg-slate-50 border border-gray-300 text-gray-800 p-3 rounded-xl text-xs font-mono h-48 focus:outline-none focus:border-blue-500"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-1 text-xs font-bold">
                    <button type="button" onclick="closeModal('editModal')" class="text-gray-400 px-3 py-2 hover:text-gray-600">Cancel</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        function openEditModal(id, name, folder) {
            document.getElementById('editForm').action = '/media/' + id;
            document.getElementById('editName').value = name;
            document.getElementById('editFolder').value = folder;
            
            // Extracts current code sequence directly out from target file container workspace tag safely
            const hiddenContent = document.getElementById('file-content-' + id);
            document.getElementById('editContent').value = hiddenContent ? hiddenContent.value : '';
            
            openModal('editModal');
        }
        function toggleSelectAll() {
            const boxes = document.querySelectorAll('.file-checkbox');
            const anyUnchecked = Array.from(boxes).some(cb => !cb.checked);
            boxes.forEach(cb => cb.checked = anyUnchecked);
        }
    </script>
</x-app-layout>
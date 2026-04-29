<div class="media-settings">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form wire:submit="save">
        {{-- Upload Limits --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0 d-flex align-items-center gap-2">
                    {!! tabler_icon('upload', ['class' => 'icon']) !!}
                    Giới hạn Upload
                </h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Dung lượng tối đa (MB)</label>
                        <div class="input-group">
                            <input type="number" wire:model="maxFileSize" class="form-control" min="1" max="500">
                            <span class="input-group-text">MB</span>
                        </div>
                        <small class="text-muted">Tối đa 500 MB. Giá trị hiện tại: {{ $maxFileSize }} MB</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số file tối đa mỗi lần upload</label>
                        <input type="number" wire:model="maxFilesPerUpload" class="form-control" min="1" max="100">
                        <small class="text-muted">Tối đa 100 file/lần</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Allowed Extensions --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0 d-flex align-items-center gap-2">
                    {!! tabler_icon('file-check', ['class' => 'icon']) !!}
                    Định dạng file được phép
                </h4>
            </div>
            <div class="card-body">
                {{-- Images --}}
                <div class="mb-4">
                    <label class="form-label d-flex align-items-center gap-2">
                        {!! tabler_icon('photo', ['class' => 'icon text-success']) !!}
                        Hình ảnh
                    </label>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        @foreach($allowedImages as $ext)
                            <span class="badge bg-success-lt">
                                {{ $ext }}
                                <button type="button" class="btn-close btn-close-sm ms-1"
                                        wire:click="removeExtension('image', '{{ $ext }}')"
                                        style="font-size: 0.6em;"></button>
                            </span>
                        @endforeach
                    </div>
                    <div class="input-group input-group-sm" style="max-width: 200px;">
                        <input type="text" wire:model="newImageExt" class="form-control" placeholder="vd: tiff"
                               wire:keydown.enter.prevent="addExtension('image')">
                        <button type="button" class="btn btn-outline-success"
                                wire:click="addExtension('image')">
                            {!! tabler_icon('plus', ['class' => 'icon']) !!}
                        </button>
                    </div>
                </div>

                {{-- Documents --}}
                <div class="mb-4">
                    <label class="form-label d-flex align-items-center gap-2">
                        {!! tabler_icon('file-text', ['class' => 'icon text-primary']) !!}
                        Tài liệu
                    </label>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        @foreach($allowedDocuments as $ext)
                            <span class="badge bg-primary-lt">
                                {{ $ext }}
                                <button type="button" class="btn-close btn-close-sm ms-1"
                                        wire:click="removeExtension('document', '{{ $ext }}')"
                                        style="font-size: 0.6em;"></button>
                            </span>
                        @endforeach
                    </div>
                    <div class="input-group input-group-sm" style="max-width: 200px;">
                        <input type="text" wire:model="newDocumentExt" class="form-control" placeholder="vd: rtf"
                               wire:keydown.enter.prevent="addExtension('document')">
                        <button type="button" class="btn btn-outline-primary"
                                wire:click="addExtension('document')">
                            {!! tabler_icon('plus', ['class' => 'icon']) !!}
                        </button>
                    </div>
                </div>

                {{-- Videos --}}
                <div class="mb-4">
                    <label class="form-label d-flex align-items-center gap-2">
                        {!! tabler_icon('video', ['class' => 'icon text-info']) !!}
                        Video
                    </label>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        @foreach($allowedVideos as $ext)
                            <span class="badge bg-info-lt">
                                {{ $ext }}
                                <button type="button" class="btn-close btn-close-sm ms-1"
                                        wire:click="removeExtension('video', '{{ $ext }}')"
                                        style="font-size: 0.6em;"></button>
                            </span>
                        @endforeach
                    </div>
                    <div class="input-group input-group-sm" style="max-width: 200px;">
                        <input type="text" wire:model="newVideoExt" class="form-control" placeholder="vd: 3gp"
                               wire:keydown.enter.prevent="addExtension('video')">
                        <button type="button" class="btn btn-outline-info"
                                wire:click="addExtension('video')">
                            {!! tabler_icon('plus', ['class' => 'icon']) !!}
                        </button>
                    </div>
                </div>

                {{-- Audio --}}
                <div class="mb-4">
                    <label class="form-label d-flex align-items-center gap-2">
                        {!! tabler_icon('music', ['class' => 'icon text-warning']) !!}
                        Audio
                    </label>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        @foreach($allowedAudio as $ext)
                            <span class="badge bg-warning-lt">
                                {{ $ext }}
                                <button type="button" class="btn-close btn-close-sm ms-1"
                                        wire:click="removeExtension('audio', '{{ $ext }}')"
                                        style="font-size: 0.6em;"></button>
                            </span>
                        @endforeach
                    </div>
                    <div class="input-group input-group-sm" style="max-width: 200px;">
                        <input type="text" wire:model="newAudioExt" class="form-control" placeholder="vd: flac"
                               wire:keydown.enter.prevent="addExtension('audio')">
                        <button type="button" class="btn btn-outline-warning"
                                wire:click="addExtension('audio')">
                            {!! tabler_icon('plus', ['class' => 'icon']) !!}
                        </button>
                    </div>
                </div>

                {{-- Archives --}}
                <div class="mb-0">
                    <label class="form-label d-flex align-items-center gap-2">
                        {!! tabler_icon('file-zip', ['class' => 'icon text-secondary']) !!}
                        File nén
                    </label>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        @foreach($allowedArchives as $ext)
                            <span class="badge bg-secondary-lt">
                                {{ $ext }}
                                <button type="button" class="btn-close btn-close-sm ms-1"
                                        wire:click="removeExtension('archive', '{{ $ext }}')"
                                        style="font-size: 0.6em;"></button>
                            </span>
                        @endforeach
                    </div>
                    <div class="input-group input-group-sm" style="max-width: 200px;">
                        <input type="text" wire:model="newArchiveExt" class="form-control" placeholder="vd: bz2"
                               wire:keydown.enter.prevent="addExtension('archive')">
                        <button type="button" class="btn btn-outline-secondary"
                                wire:click="addExtension('archive')">
                            {!! tabler_icon('plus', ['class' => 'icon']) !!}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Blocked Extensions (Read-only) --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0 d-flex align-items-center gap-2">
                    {!! tabler_icon('shield-lock', ['class' => 'icon text-danger']) !!}
                    Định dạng bị chặn (Bảo mật)
                </h4>
            </div>
            <div class="card-body">
                <div class="alert alert-danger mb-3">
                    <div class="d-flex gap-2">
                        {!! tabler_icon('alert-triangle', ['class' => 'icon']) !!}
                        <div>
                            <strong>Cảnh báo:</strong> Các định dạng này bị chặn vĩnh viễn vì lý do bảo mật.
                            Không thể thay đổi để ngăn chặn tấn công thực thi mã độc.
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($blockedExtensions as $ext)
                        <span class="badge bg-danger">{{ $ext }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>
                    {!! tabler_icon('device-floppy', ['class' => 'icon']) !!}
                    Lưu cài đặt
                </span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    Đang lưu...
                </span>
            </button>
        </div>
    </form>
</div>

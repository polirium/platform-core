@if($showPreviewModal && $previewMedia)
    <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.9);" @click.self="$wire.closePreviewModal()">
        <div class="modal-dialog modal-full-width modal-dialog-centered">
            <div class="modal-content bg-transparent shadow-none">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4" wire:click="closePreviewModal" style="z-index: 1060;"></button>

                <div class="modal-body p-0 text-center d-flex align-items-center justify-content-center" style="height: 100vh;">
                    @if($previewMedia->is_image)
                        <img src="{{ $previewMedia->getUrl() }}" alt="{{ $previewMedia->name }}" style="max-height: 90vh; max-width: 90vw; object-fit: contain;">
                    @elseif($previewMedia->is_video)
                        <video src="{{ $previewMedia->getUrl() }}" controls autoplay style="max-height: 90vh; max-width: 90vw;"></video>
                    @elseif($previewMedia->is_audio)
                        <audio src="{{ $previewMedia->getUrl() }}" controls autoplay></audio>
                    @else
                        <div class="text-white">
                            {!! tabler_icon('file', ['class' => 'icon-xl mb-3', 'style' => 'width: 64px; height: 64px;']) !!}
                            <h3>{{ $previewMedia->name }}</h3>
                            <div class="mt-3">
                                <a href="{{ $previewMedia->getUrl() }}" target="_blank" class="btn btn-primary">
                                    {{ __('core/media::media.download') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Navigation Controls (Optional for future) --}}
            </div>
        </div>
    </div>
@endif

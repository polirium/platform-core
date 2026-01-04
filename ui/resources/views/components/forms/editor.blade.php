@props([
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
])

@php
    $key_name = 'editor_' . str_replace('.', '_', $name);
@endphp

<div wire:ignore>
    <x-form::textarea
        {{ $attributes->merge([ 'id' => $key_name ]) }}
    />
</div>

@once
    @push('scripts')
        <script src="{{ asset('vendor/polirium/core/ui/libs/hugerte/hugerte.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                hugerte.init({
                    selector: '#{{ $key_name }}',
                    plugins: 'advlist anchor autolink charmap codesample directionality emoticons help image insertdatetime link lists media nonbreaking pagebreak searchreplace table visualblocks visualchars',
                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline forecolor backcolor | link image | align lineheight bullist numlist | indent outdent | removeformat',
                    height: 600,
                    menubar: true,
                    branding: false,
                    promotion: false,
                    relative_urls: false,
                    remove_script_host: false,
                    convert_urls: false,
                    content_style: `
                        body {
                            font-family: "Times New Roman", Times, serif;
                            font-size: 12pt;
                            line-height: 1.5;
                        }
                        table { width: 100%; border-collapse: collapse; }
                        td, th { padding: 5px; border: 1px solid #000; }
                    `,
                    setup: function(editor) {
                        editor.on('init', function() {
                            // Listen for content updates from Livewire
                            Livewire.on('{{ $key_name }}', (event) => {
                                editor.setContent(event.content || '');
                            });
                            
                            // Also listen for the general editor_input_content event
                            Livewire.on('editor_input_content', (event) => {
                                if (editor.getElement().id === '{{ $key_name }}') {
                                    editor.setContent(event.content || '');
                                }
                            });
                        });
                        
                        editor.on('change blur', function() {
                            const wireId = editor.getElement().closest('[wire\\:id]')?.getAttribute('wire:id');
                            if (wireId) {
                                const component = Livewire.find(wireId);
                                if (component) {
                                    // Use Livewire.first() if component.set is not available
                                    try {
                                        component.set('{{ $name }}', editor.getContent());
                                    } catch (e) {
                                        // Fallback to using $wire if available
                                        if (window.$wire) {
                                            window.$wire.set('{{ $name }}', editor.getContent());
                                        }
                                    }
                                }
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
@endonce

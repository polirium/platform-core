@props([
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
])

@php
    $key_name = 'editor_' . str_replace('.', '_', $name);
@endphp

<div 
    wire:ignore
    x-data="editor()"
    {{-- x-data="{
        editor: null,
        async initialize_editor() {
            if (this.editor) {
                this.editor.destroy();
            }

            this.editor = await CKEDITOR.replace( '{{ $key_name }}', {
                on: {
                    change: function (evt) {
                        @this.set('{{ $name }}', evt.editor.getData(), true);
                    }
                }
            });

            $wire.on('{{ $key_name }}', (event) => {
                this.editor.setData(event.content ? event.content : '');
            })
        },
    }"
    x-init="initialize_editor" --}}
>
    <x-form::textarea
        {{ $attributes->merge([ 'id' => $key_name ]) }}
    />
</div>

@once
    @push('scripts')
        <script src="{{ asset('vendor/polirium/core/ui/js/libs/tinymce/tinymce.min.js') }}"></script>
        {{-- <script src="{{ asset('vendor\polirium\core\ui\js\libs\ckeditor\ckeditor4\ckeditor.js') }}"></script> --}}
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('editor', () => ({
                    editor: null,
                    init(){
                        tinymce.init({
                            selector: "#{{ $key_name }}",
                            plugins: "advlist anchor autolink charmap codesample directionality emoticons help image insertdatetime link lists media nonbreaking pagebreak searchreplace table visualblocks visualchars",
                            toolbar: "undo redo spellcheckdialog | blocks fontfamily fontsize | bold italic underline forecolor backcolor | link image | align lineheight checklist bullist numlist | indent outdent | removeformat",
                            height: '800px',
                            toolbar_sticky: true,
                            // autosave_restore_when_empty: true,
                            pagebreak_separator: '<div class="page-break"></div>',
                            pagebreak_split_block: true,
                            visual: false,
                            document_base_url: '{{ env("APP_URL") }}',
                            relative_urls: false,
                            remove_script_host: false,
                            convert_urls: false,
                            forced_root_block: false,
                            force_br_newlines: false,
                            force_p_newlines: true,
                            // paste_as_text: true,
                            paste_data_images: false,
                            content_style: `
                                body * {
                                    margin: 0;
                                    padding: 0;
                                    font-size: 13pt;
                                    line-height: 1.5;
                                }

                                body {
                                    box-sizing: border-box;
                                    font-family: "Times New Roman", Times, serif;
                                    font-size: 12pt;
                                    line-height: 1.5;
                                    -webkit-print-color-adjust:exact !important;
                                    print-color-adjust:exact !important;
                                }

                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                }

                                .tbl td, .tbl th {
                                    padding: 5px;
                                }

                                .tbl thead {
                                    background: #31869B;
                                }

                                .tbl-bordered td, .tbl-bordered th {
                                    border: 1px solid #000;
                                }

                                .blue-bg {
                                    background: #1F4E78;
                                    color: #fff;
                                }
                            `,

                            setup: function (editor) {
                                editor.on('init', function(e) {
                                    Livewire.on('{{ $key_name }}', (event) => {
                                        editor.setContent(event.content ? event.content : '');
                                    });
                                });

                                editor.on('change', function(e) {
                                    @this.set('{{ $name }}', editor.getContent());
                                });
                            }
                        });
                    }
                }))
            })
        </script>
    @endpush
@endonce

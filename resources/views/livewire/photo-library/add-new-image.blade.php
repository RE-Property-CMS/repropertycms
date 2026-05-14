<div>
    <div x-data x-show="$wire.show" x-cloak
         style="position:fixed;inset:0;z-index:9999;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
            <div style="position:absolute;inset:0;background:rgba(0,0,0,0.5);" @click="$wire.closeModal()"></div>
            <div style="position:relative;background:white;border-radius:14px;width:70vw;max-width:900px;min-width:320px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,0.2);overflow:hidden;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                {{-- Header --}}
                <div style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);padding:20px 24px;display:flex;align-items:center;justify-content:space-between;">
                    <h3 style="color:white;font-size:1.125rem;font-weight:600;margin:0;">Photo Library</h3>
                    <button type="button" @click="$wire.closeModal()"
                            style="color:rgba(255,255,255,0.7);background:none;border:none;cursor:pointer;padding:4px;line-height:1;">
                        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                {{-- Body --}}
                <form wire:submit.prevent="save">
                    <div style="padding:0;">
                        <div class="m-0">
                            <div class='content'>
                                <livewire:dropzone
                                        wire:model="thumbnail" :multiple="true"
                                        :rules="['image','mimes:png,jpeg,jpg','max:20480']"
                                        :key="'dropzone-one'"/>
                            </div>
                            @error('thumbnail') <span class="error">{{ $message }}</span> @enderror
                            <div style="padding:16px 24px;">
                                <p class="text-grey-500 text-sm mt-2">You can upload up to 200 photos per property. However, you can
                                    only upload 100 photos simultaneously. If you have more than 100
                                    you'll need to upload them in multiple batches.
                                    The max file size for each individual photo is 20 MB. Photos exceeding
                                    that size may be dropped. Please let us know if you have trouble
                                    uploading.</p>
                                <div class="my-5">
                                    <button type="submit" class="button button-blue p-3">Add Images</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

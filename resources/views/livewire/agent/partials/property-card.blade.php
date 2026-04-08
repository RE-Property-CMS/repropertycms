<div class="ag-prop-card">

    {{-- Image --}}
    <div class="ag-prop-img-wrap">
        @if($row->property_images->first())
            <img src="{{ asset_s3($row->property_images->first()->thumb) }}" alt="{{ $row->name }}">
        @else
            <div class="ag-prop-img-placeholder">
                <i class="fa fa-home" style="font-size:2.2rem;color:#d1d5db;"></i>
            </div>
        @endif

        {{-- Status --}}
        <span class="ag-prop-status"
              style="{{ $row->published ? 'background:rgba(220,252,231,0.95);color:#15803d;' : 'background:rgba(254,249,195,0.95);color:#92400e;' }}">
            {{ $row->published ? 'Published' : 'Draft' }}
        </span>

        {{-- Price --}}
        @if(!empty($row->price))
            <span class="ag-prop-price">${{ $row->price }}</span>
        @endif
    </div>

    {{-- Body --}}
    <div class="ag-prop-body">
        <div class="ag-prop-name">{{ $row->name ?: $row->address_line_1 }}</div>
        <div class="ag-prop-addr">
            <i class="fa fa-map-marker-alt" style="margin-right:3px;"></i>{{ $row->address_line_1 }}{{ !empty($row->city) ? ', ' . $row->city : '' }}
        </div>
    </div>

    {{-- Actions --}}
    <div class="ag-prop-footer">
        <a href="{{ url('agent/property/address/' . $row->id) }}" class="ag-action-btn ag-btn-edit">
            <i class="fa fa-edit"></i> Edit
        </a>
        <a href="{{ url($row->unique_url) }}" target="_blank" class="ag-action-btn ag-btn-preview">
            <i class="fa fa-eye"></i> Preview
        </a>
        @if(!$row->published)
            <a href="{{ url('share/' . $row->unique_url) }}" target="_blank" class="ag-action-btn ag-btn-share">
                <i class="fa fa-share-alt"></i> Share
            </a>
            <button wire:click="publishProperty({{ $row->id }})" class="ag-action-btn ag-btn-publish">
                <i class="fa fa-globe"></i> Publish
            </button>
            <button type="button" @click="confirmDeleteId = {{ $row->id }}" class="ag-action-btn ag-btn-delete">
                <i class="fa fa-trash"></i> Delete
            </button>
        @endif
    </div>

</div>

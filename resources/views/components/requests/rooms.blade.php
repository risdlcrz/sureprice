{{--
    Rooms → scopes → materials of a client quotation request, as a full-width list (one block per room,
    one row per scope). Styles: resources/css/pages/requests.css.
    <x-requests.rooms :rooms="$quotationRequest->rooms" />
    Materials come from the scope's selected_materials (with the client's quantities), falling back to the
    scope type's material list. :quantities="false" shows names only.
--}}
@props(['rooms', 'quantities' => true])
@php
    $materialIds = collect($rooms)->flatMap(fn ($room) => $room->scopes)
        ->flatMap(fn ($scope) => collect(is_array($scope->selected_materials) ? $scope->selected_materials : [])->pluck('material_id'))
        ->filter()->unique();
    $materialsById = $materialIds->isEmpty() ? collect() : \App\Models\Material::whereIn('id', $materialIds)->get()->keyBy('id');
    $num = fn ($v) => rtrim(rtrim(number_format((float) $v, 2), '0'), '.');
@endphp
@if(collect($rooms)->isEmpty())
    <p class="text-muted mb-0">No rooms on this request.</p>
@else
    <ol class="req-rooms">
        @foreach($rooms as $room)
            <li class="req-room">
                <header class="req-room__head">
                    <span class="req-room__index">{{ $loop->iteration }}</span>
                    <div class="req-room__title">
                        <div class="req-room__name">{{ $room->name ?: 'Room ' . $loop->iteration }}</div>
                        <div class="req-room__dims">{{ $num($room->length) }} m × {{ $num($room->width) }} m × {{ $num($room->height) }} m</div>
                    </div>
                    <dl class="req-room__stats">
                        <div><dt>Area</dt><dd>{{ $num($room->area ?? $room->length * $room->width) }} m²</dd></div>
                        <div><dt>Volume</dt><dd>{{ $num($room->volume ?? $room->length * $room->width * $room->height) }} m³</dd></div>
                        <div><dt>Scopes</dt><dd>{{ $room->scopes->count() }}</dd></div>
                    </dl>
                </header>

                @if($room->scopes->isEmpty())
                    <p class="req-room__empty text-muted small mb-0">No scopes selected.</p>
                @else
                    <div class="req-scopes">
                        @foreach($room->scopes as $scope)
                            @php
                                $selected = collect(is_array($scope->selected_materials) ? $scope->selected_materials : []);
                                $scopeName = $scope->scope_name ?: ($scope->scopeType->name ?? 'Scope');
                                $category = $scope->scope_category ?: ($scope->scopeType->category ?? null);
                            @endphp
                            <div class="req-scope">
                                <div class="req-scope__name">
                                    <i class="fas fa-tools" aria-hidden="true"></i>
                                    <span>{{ $scopeName }}@if($category)<span class="req-scope__category">{{ $category }}</span>@endif</span>
                                </div>
                                <div class="req-scope__body">
                                    @if($quantities && $selected->isNotEmpty())
                                        <ul class="req-scope__materials">
                                            @foreach($selected as $mat)
                                                @php $material = $materialsById[$mat['material_id'] ?? null] ?? null; @endphp
                                                <li>
                                                    <span>{{ $material->name ?? 'Material #' . ($mat['material_id'] ?? '?') }}</span>
                                                    <span class="req-scope__qty">{{ $mat['quantity'] ?? '—' }}{{ $mat['coverage_info'] ?? '' }} {{ $mat['unit'] ?? ($material->unit ?? '') }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @elseif($scope->scopeType?->materials?->isNotEmpty())
                                        <div class="req-room__scopes">
                                            @foreach($scope->scopeType->materials as $material)
                                                <span class="req-tag">{{ $material->name }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted small">No materials listed.</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </li>
        @endforeach
    </ol>
@endif

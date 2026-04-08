@extends('layouts.agents.default1')

@section('title', 'Hotspot')

@section('content')

    <script>
        $(document).ready(function() {
            // $(".myDiv").on('load', function (){
            //     $(".added").css("margin-top", $(this).height()+50+'px');
            // })

            $(document).on("click" , ".showHotspots", function() {
                $('#hotspot-sidebar-selected').show();
            });

            $(document).on("click", "#close-hotspot-sidebar-selected", function() {
                $('#hotspot-sidebar-selected').hide();
            });
        });
    </script>

    <div class="flex mt-5">
        <div class="w-full">
            <div class="w-full">
                <a href="{{route('agent.floorplan.index')}}" class="button button-green px-5 font-bold mr-2"><i class="fa-solid fa-arrow-left mr-2"></i> Go to Floor Plans</a>
                <a href="#" id="addhotspot" class="button button-green px-5 font-bold"  data-ripple-light="true"><i class="fa fa-plus mr-2"></i>Add Hotspot</a>
            </div>
            <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
                <h5 class="mb-0">{{ $property_floorplans->name}}</h5>
            </div>
            <div class="flex">
                <div class="w-full drop-append relative">
                    <img src="{{asset_s3($property_floorplans->file_name)}}" ondragend="dragEnd(event)" ondragover="onDragOver(event);" ondrop="onDrop(event);"  id="Drop_Box" class="w-full absolute myDiv" style="padding:20px;" alt="reload">

                    @if($property_floorplan_images->count() > 0)
                        @foreach($property_floorplan_images as $property_floorplan_image)
                            @php $coordinates = explode(",",$property_floorplan_image->coordinates); @endphp
                            <img src="{{asset('images/icon-camera.png')}}" class="absolute showHotspots" style=" margin-top:{{ $coordinates[1] - 25 }}px; margin-left:{{ $coordinates[0] -25 }}px;" id='hotspot{{ $property_floorplan_image->property_image_id }}'/>
                            </span>
                        @endforeach
                    @endif
                </div>
            </div>


            {{--        <div class="w-full added">--}}
            {{--            <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">--}}
            {{--                <h5 class="mb-0">Added Hotspots</h5>--}}
            {{--            </div>--}}
            {{--            <div id="hotspot-data">--}}
            {{--                @foreach($property_floorplan_images as $property_floorplan_image)--}}
            {{--                    @foreach($property_floorplan_image->property_images as $property_image)--}}
            {{--                        <div class="w-full py-8 px-5  inline-block relative mb-3 gallery-image d-flex align-items-center justify-content-between" id="HotspotData{{ $property_floorplan_image->property_image_id }}">--}}
            {{--                            <div class="d-flex align-items-center">--}}
            {{--                                <div class="float-left">--}}
            {{--                                    @if($property_image->thumb && str_contains($property_image->thumb, 's3.amazonaws.com'))--}}
            {{--                                        <img src="{{$property_image->thumb}}" style="max-height:100px;" alt="">--}}
            {{--                                    @else--}}
            {{--                                        @if(str_contains($property_image->file_name, 's3.amazonaws.com'))--}}
            {{--                                            <img src="{{$property_image->file_name}}" style="max-height:100px;" alt="">--}}
            {{--                                        @else--}}
            {{--                                            <img src="{{asset('files/property_images/' . $property_floorplan_image->property_id . '/' . $property_image->file_name)}}" style="max-height:100px;" alt="">--}}
            {{--                                        @endif--}}
            {{--                                    @endif--}}
            {{--                                </div>--}}
            {{--                                <div class="float-left mx-5">--}}
            {{--                                    @php--}}
            {{--                                        //                                        dd($property_image->file_name);--}}
            {{--                                                                                    $fileName = $property_image->file_name;--}}
            {{--                                                                                    if(str_contains($property_image->file_name, 's3.amazonaws.com')){--}}
            {{--                                                                                        $propertyFloorImg = explode('property_images/', $property_image->file_name);--}}
            {{--                                                                                        $fileName = $propertyFloorImg[1];--}}
            {{--                                                                                    }--}}
            {{--                                    @endphp--}}
            {{--                                    <span>{{ $fileName }}</span>--}}
            {{--                                </div>--}}
            {{--                            </div>--}}
            {{--                            <div class="float-right">--}}
            {{--                                <a class="mb-0" href="#" title="Delete hotspot" onclick="deletehotspot('{{ $property_floorplan_image->property_image_id }}','{{$property_image->file_name}}','{{$property_floorplans->id}}','{{$property_floorplan_image->property_id}}')">--}}
            {{--                                    <button class="button button-red p-3 mb-0"  data-ripple-light="true"><i class="fa fa-trash mr-2"></i> Delete</button>--}}
            {{--                                </a>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    @endforeach--}}
            {{--                @endforeach--}}
            {{--            </div>--}}
            {{--        </div>--}}

        </div>

        <div class="w-2/6 float-left h-screen overflow-auto hidden" id="hotspot-sidebar-selected">
            <div class="border relative " id="" style="min-height: 600px;">
                <div class="flex">
                    <div class=" p-2">All marked Hotspot Images appears here</div>
                    <div class=" text-5xl float-right block mx-2 cursor-pointer" id="close-hotspot-sidebar-selected">&times;</div>
                </div>
                <div class="grid grid-cols-1">
                    @foreach($property_floorplan_images as $property_floorplan_image)
                        @foreach($property_floorplan_image->property_images as $property_image)
                            <div class="inline-table relative h-40 m-2">
                                <img src="{{asset_s3($property_image->thumb)}}" style="max-height:100px; float:left;" alt="">

                                <div class="float-right">
                                    <a class="mb-0" href="#" title="Delete hotspot" onclick="deletehotspot('{{ $property_floorplan_image->property_image_id }}','{{$property_image->file_name}}','{{$property_floorplans->id}}','{{$property_floorplan_image->property_id}}')">
                                        <button class="button button-red p-3 mb-0"  data-ripple-light="true"><i class="fa fa-trash mr-2"></i> Delete</button>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>

        <div class="w-2/6 float-right h-screen overflow-auto hidden" id="hotspot-sidebar">
            <div class="border relative " id="Drag_Box" style="min-height: 600px;">
                <div class="flex">
                    <div class=" p-2">Drag images to a desired location on Floor plan image on the left, to mark the hotspot.</div>
                    <div class=" text-5xl float-right block mx-2 cursor-pointer" id="close-hotspot-sidebar">&times;</div>
                </div>
                <div class="grid grid-cols-2">
                    @foreach($property_images as $property_image)
                        <div class="inline-table relative h-40 w-52 m-2">
                            <img src="{{asset_s3($property_image->thumb)}}" class="{{$property_floorplans->id}} hotspot-image card-image-style" data-id="{{$property_image->id}}">

                            <div ondragstart="onDragStart(event);"  id="{{$property_image->id}}" draggable="true"
                                 class="{{$property_floorplans->id}} absolute inset-0 z-30 card-image-style" style=" cursor:crosshair;"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@stop
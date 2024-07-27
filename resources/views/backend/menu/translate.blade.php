@extends('backend.layout.layoutadmin')

@section('content')
    @php
        $title =
            str_replace('{language}', $language->name, $config['seo']['translate']['title']) .
            ' ' .
            $menuCatalogue->name;
    @endphp
    @include('backend.layout.component.breadcrumb', [
        'title' => $title,
    ])
    <section class="content mt-4">
       <form action="{{route('menu.saveTranslate', ['languageId' => $languageId])}}" method="post">
        @csrf
        <div class="row">
            <div class="col-lg-4 px-4">

                <h5 class="text-uppercase text-gray-dark"><strong>Thông tin chung</strong></h5>
                <ul class="text-justify">
                    <li>Hệ thống tự động lấy ra bản dịch của các Menu <span class="text-info">nếu có</span>.</li>
                    <li>Cập nhật thông tin về bản dịch cho các Menu của bạn phía bên phải.</li>
                    <li class="text-danger font-italic">Lưu ý: cập nhật đầy đủ thông tin bản dịch của menu</li>

                </ul>
            </div>
            <div class="col-lg-8">
                <div class="card bg-white shadow-lg rounded-0">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold text-muted mt-1 text-uppercase">Danh sách bản dịch</h3>
                    </div>
                    <div class="card-body" id="dataCatalogue">
                        @if (count($menus))
                            @foreach ($menus as $key => $val)
                                @php
                                    $name = $val->languages->first()->pivot->name;
                                    $canonical = $val->languages->first()->pivot->canonical;
                                @endphp
                                <div class="row mb-3">
                                    <div class="col-lg-12 mb-2 font-weight-bold text-info">
                                        Menu: {{ $val->position }}
                                    </div>
                                    <div class="col-lg-6 ">
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-append w-25">
                                                Tên Menu
                                            </span>
                                            <input type="text" name="" value="{{ $name }}"
                                                class="form-control rounded-0" disabled>

                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control form-control-sm rounded-0"
                                            name="translate[name][]" value="{{ ($val->translate_name) ?? '' }}"
                                            placeholder="Nhập bản dịch cho ngôn ngữ tương ứng.....">
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-6 ">
                                        <div class="input-group input-group-sm mb2">
                                            <span class="input-group-append w-25">
                                                Đường dẫn
                                            </span>
                                            <input type="text" name="{{ $canonical }}" value="{{ $canonical }}"
                                                class="form-control rounded-0" disabled>

                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" value="{{ ($val->translate_canonical) ?? '' }}"
                                            name="translate[canonical][]" class="form-control form-control-sm rounded-0"
                                            placeholder="Nhập bản dịch cho ngôn ngữ tương ứng.....">

                                        <input type="hidden" value="{{ ($val->id) ?? '' }}"
                                            name="translate[id][]" class="form-control form-control-sm rounded-0"
                                            placeholder="Nhập bản dịch cho ngôn ngữ tương ứng.....">
                                    </div>
                                </div>
                                <hr>
                            @endforeach
                        @endif




                    </div><!-- /.card-body -->
                </div>

                </div>
            </div>
            @include('backend.layout.component.btnsubmit')
        </div>
        
       </form>
    </section>
@endsection




{{-- <ol class="dd-list">
    @foreach ($menus as $key => $val)
    @php
      $languageMenu = $val ->languages->first()  
    @endphp
    <li class="dd-item" data-id="1">
        <div class="dd-handle">
            <span class="label"><i class="fa fa-cog"></i></span> {{$languageMenu->pivot->name}}
            
        </div>
        <a href="{{route('menu.children', $val->id)}}" class="float-right create-chilfren-menu">Quản lý menu con</a>
        <ol class="dd-list">
            <li class="dd-item" data-id="2">
                <div class="dd-handle ">                                               
                    <span class="label "><i class="fa fa-laptop"></i></span> Trần Xuân Thành
                    <span class="float-right"> 11:00 pm </span>
                </div>
            </li>
            <li class="dd-item" data-id="3">
                <div class="dd-handle">                                           
                    <span class="label "><i class="fa fa-laptop"></i></span> Xuân Thành
                    <span class="float-right"> 11:00 pm </span>
                </div>
            </li>
            <li class="dd-item" data-id="4">
                <div class="dd-handle">
                    <span class="label"><i class="fa fa-laptop"></i></span> VL Gắt
                    <span class="float-right"> 11:00 pm </span>
                </div>
            </li>
        </ol>
    </li>
    @endforeach
</ol> --}}

@extends('backend.layout.layoutadmin')

@section('content')
    @include('backend.layout.component.breadcrumb', [
        'title' => $config['seo'][$config['method']]['title'],
    ])
    <section class="content mt-4">
        @include('backend.layout.component.formError')
        <div class="row">
            <div class="col-lg-4 px-4">
                <h5 class="text-uppercase text-gray-dark"><strong>Danh sác menu</strong></h5>
                <ul class="text-justify">
                    <li>Danh sách Menu giúp bạn dễ dàng kiểm soát bố cục menu. Bạn có thể thêm mới hoặc cập nhật Menu bằng
                        nút <span class="text-info">Cập nhật Mneu</span>.</li>
                    <li>Bạn có thể thay đổi vị trí hiển thị của Menu bằng cách <span class="text-info">Kéo menu đến vị trí
                            mong muốn</span>.</li>
                    <li>Dễ dàng khởi tạo Menu bằng cách ấn vào nút <span class="text-info">Quản lý menu con</span></li>
                    <li><span class="text-danger">Hỗ trợ tới danh mục cấp 5</span></li>
                </ul>
            </div>
            <div class="col-lg-8">
                <div class="card bg-white shadow-lg rounded-0">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold text-muted mt-1">Menu chính <span
                                class="text-danger">(*)</span></h3>

                        <div class="card-tools">
                            <a href="#" class="btn btn-sm btn-danger rounded-0">Cập nhật Menu</a>
                        </div>
                    </div>
                    <div class="card-body" id="dataCatalogue" data-catalogueId={{$id}}>
                        @php
                            $menus = recursive($menus);
                            $menuString = recursive_menu($menus);
                        @endphp
                         @if(count($menus))
                        <div class="dd" id="nestable2">                          
                          <ol class="dd-list">
                                {!! $menuString !!}
                          </ol>
                        </div>
                        @endif
                        
                    </div><!-- /.card-body -->
                </div>

            </div>
        </div>
        </div>
    </section>
@endsection


{{-- <ol class="dd-list">
    @foreach($menus as $key => $val)
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
<div class="row">
    <div class="col-lg-4 pl-4 mb-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title card-title font-weight-bold text-muted mt-1">Liên kết Menu</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- /.card-header -->
            <div class="card-body">
                <!-- we are adding the accordion ID so Bootstrap's collapse plugin detects it -->
                <div id="accordion">
                    <div class="card">
                        <div class="card-header bg-teal">
                            <h4 class="card-title w-100">
                                <a class="d-block w-100 font-weight-bold text-white" data-toggle="collapse"
                                    href="#collapseOne">
                                    Liên kết tự tạo
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="collapse show" data-parent="#accordion">
                            <div class="card-body">
                                <h5 class="font-weight-bold text-gray-dark">Tạo Menu</h5>
                                <p class="mb-1">Cài đặt Menu mà bạn muốn hiển thị</p>
                                <p class="text-danger mb-1 font-weight-bold">Lưu ý:</p>
                                <ol class="text-danger font-italic text-justify">
                                    <li>Khi khởi tạo menu bạn phải chắc chắn rằng đường dẫn của menu có hoạt động. Đường
                                        dẫn trên website được khởi tạo tại các module: Bài viết, Sản phẩm, Dự án..v.v..
                                    </li>
                                    <li>Tiêu đề và đường dẫn của menu không được để trống.</li>
                                    <li>Hệ thống chỉ hỗ trợ tối đa 5 cấp menu</li>
                                </ol>
                                <a href="" class="add-menu btn btn-sm btn-outline-primary rounded-0 w-100">Thêm
                                    đường dẫn</a>
                            </div>
                        </div>
                    </div>
                    @foreach (__('module.model') as $key => $val)
                        <div class="card">
                            <div class="card-header  bg-teal">
                                <h4 class="card-title w-100">
                                    <a class="d-block w-100 font-weight-bold menu-module text-white"
                                        data-model={{$key}} data-toggle="collapse"
                                        href="#{{ $key }}">
                                        {{ $val }}
                                    </a>
                                </h4>
                            </div>
                            <div id="{{$key}}" class="collapse" data-parent="#accordion">
                                <div class="card-body">
                                    
                                        <input type="text" class="form-control search-menu form-control-sm rounded-0"
                                            value="" name="keyword" placeholder="Nhap 2 ky tu de tiem kiem....">
                                    
                                    <div class="menu-list mt-3">

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title card-title font-weight-bold text-muted mt-1">Cấu hình Menu</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row items-center">
                    <div class="col-lg-4"><Label for="">Tên Menu</Label></div>
                    <div class="col-lg-6"><label for="">Đường dẫn</label></div>
                    <div class="col-lg-1 text-center"><label for="">Vị trí</label></div>
                    <div class="col-lg-1  text-center"><label for="">Xóa</label></div>
                </div>
                <hr class="border border-1 border-info">
                <div class="menu-wrapper">
                    <div
                        class="notification text-center mt-2 {{ is_array(old('menu')) && count(old('menu')) ? 'none' : '' }}">
                        <h5>Danh sách kiên kết này chưa có bất kỳ đường dẫn nào.</h5>
                        <p>Hãy nhấn vào <span class="text-info">"Thêm đường dẫn"</span> để bắt đầu thêm.</p>
                    </div>
                    @if (is_array(old('menu')) && count(old('menu')))
                        @foreach (old('menu')['name'] as $key => $val)
                            <div class="row mb-3 menu-item ">
                                <div class="col-lg-4">
                                    <input type="text" name="menu[name][]" value="{{ $val }}"
                                        class="form-control form-control-sm rounded-0">
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" name="menu[canonical][]"
                                        value="{{ old('menu')[canonical][$key] }}"
                                        class="form-control form-control-sm rounded-0">
                                </div>
                                <div class="col-lg-1">
                                    <input type="text" name="menu[order][]" value="{{ old('menu')[order][$key] }}"
                                        class="form-control form-control-sm rounded-0">
                                </div>
                                <div class="col-lg-1 text-center">
                                    <a href="" class="delete-menu btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
</div>

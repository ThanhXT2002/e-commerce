<div class="modal fade" id="createMenuCatalogue">
    <form action="" method="post" class="modal-dialog modal-dialog-centered modal-lg create-menu-catalogue">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-600 text-gray-dark">Thêm vị trí hiển thị của menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-error alert-dismissible"></div>
                <div class="col-lg-12 mb-3">

                    <label for="" class="control-label text-left">Tên vị trí hiển thị<span
                            class="text-danger">(*)</span></label>
                    <input type="text" name="name"
                        value="{{ is_string(old('menu')) ? old('menu') : $menu->name ?? '' }}"
                        class="form-control form-control-sm rounded-0 shadow border border-info" placeholder=""
                        autocomplete="off">
                    <div class ="text-danger error name text-left"></div>
                </div>
                <div class="col-lg-12">
                    <label for="" class="control-label text-left">Từ khóa </label>
                    <input type="text" name="keyword"
                        value="{{ is_string(old('menu')) ? old('menu') : $menu->keyword ?? '' }}"
                        class="form-control form-control-sm rounded-0 shadow border border-info" placeholder=""
                        autocomplete="off">

                    <div class ="text-danger error keyword text-left"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-0 "
                    data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-sm btn-outline-success rounded-0">Lưu lại</button>
            </div>
        </div>
        <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</form>
</div>

<div class="linkneo section" >
    <button class="btn btn-danger remove-section" type="button">Xóa Section</button>
    <div class="card shadow mb-4" >
        <span class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Section mới</h6>
        </span>
        <div class="card-body">
            <div class="row">
                <div class="col-md-1">
                    <div class="form-group">
                        <label class="">STT</label>
                        <input name="stt[]" placeholder="..." type="text" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="">Tab</label>
                        <input required name="tab[]" placeholder="..." type="text" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="">Heading</label>
                        <input name="heading[]" placeholder="..." type="text" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="">Hình ảnh</label>
                        <input multiple name="img_ss[]" placeholder="..." type="file" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="">Tùy chọn</label>
                        <select class="form-control" name="status[]">
                            <option value="1">Kiểu 1</option>
                            <option value="3">Mặt bằng</option>
                            <option value="4">Căn hộ</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <textarea rows="" name="content[]" class="form-control editor"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>


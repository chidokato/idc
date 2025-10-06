<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">SEO option</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                  <div class="form-group">
                      <label>Title</label>
                      <input value="{{isset($data) ? $data->title : ''}}" name="title" placeholder="..." type="text" class="form-control">
                  </div>
              </div>
              <div class="col-md-12">
                  <div class="form-group">
                      <label>Description</label>
                      <input value="{{isset($data) ? $data->description : ''}}" name="description" placeholder="..." type="text" class="form-control">
                  </div>
              </div>
        </div>
    </div>
</div>
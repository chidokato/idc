@extends('layout.index')

@section('title') {{$data->title ? $data->title : $data->name}} @endsection
@section('description') {{$data->description}} @endsection
@section('robots') index, follow @endsection
@section('url'){{asset('')}}@endsection

@section('css')
<link href="assets/css/contact.css" rel="stylesheet">
@endsection
@section('content')

<!-- <div class="breadcrumbs_area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb_content">
                    <ul>
                        <li><a href="{{asset('')}}">Trang chủ</a></li>
                        <li>{{$data->name}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div> -->

<div class="contact_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Công ty Cổ Phần Bất Động Sản INDOCHINE</h1>
                <div class="review-map">
                    <ul class="nav review-tabs" role="tablist">
                        <li>
                            <button class="active" data-bs-toggle="tab" data-bs-target="#map-type-1" type="button">Hà Nội</button>
                        </li>
                        <li>
                            <button class="" data-bs-toggle="tab" data-bs-target="#map-type-2" type="button">Hồ Chí Minh</button>
                        </li>
                        <li>
                            <button class="" data-bs-toggle="tab" data-bs-target="#map-type-3" type="button">Quảng Ninh</button>
                        </li>
                        <li>
                            <button class="" data-bs-toggle="tab" data-bs-target="#map-type-4" type="button">Bắc Ninh</button>
                        </li>
                        <li>
                            <button class="" data-bs-toggle="tab" data-bs-target="#map-type-5" type="button">Bắc Giang</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="map-type-1">
                            <div class="row">
                                <div class="col-md-5 main-info">
                                    <div class="info">
                                        <h3>Trụ sở chính</h3>
                                        <ul>
                                            <li><i class="icon-building-filled"></i> <strong>Địa chỉ:</strong> {{$setting->address}}</li>
                                            <li><i class="icon-phone-filled"></i> <strong>Điện thoại:</strong> {{$setting->hotline}}</li>
                                            <li><i class="icon-mail-filled"></i> <strong>Email:</strong> {{$setting->email}}</li>
                                            <li><i class="icon-time"></i> <strong>Thời gian làm việc:</strong> Từ thứ Hai đến thứ Bảy. Sáng: 8h00 - 12h00, Chiều: 1h30 - 5h30</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="maps">
                                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d31353.81386504896!2d106.689305!3d10.793939!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317528cdeb13ffff%3A0x8db7b80bb49f4899!2zQ8O0bmcgVHkgQ-G7lSBQaOG6p24gQuG6pXQgxJDhu5luZyBT4bqjbiBJbmRvY2hpbmU!5e0!3m2!1svi!2sus!4v1724829804399!5m2!1svi!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="map-type-2">
                            <div class="row">
                                <div class="col-md-5 main-info">
                                    <div class="info">
                                        <h3>Trụ sở chính</h3>
                                        <ul>
                                            <li><i class="icon-building-filled"></i> <strong>Địa chỉ:</strong> {{$setting->address}}</li>
                                            <li><i class="icon-phone-filled"></i> <strong>Điện thoại:</strong> {{$setting->hotline}}</li>
                                            <li><i class="icon-mail-filled"></i> <strong>Email:</strong> {{$setting->email}}</li>
                                            <li><i class="icon-time"></i> <strong>Thời gian làm việc:</strong> Từ thứ Hai đến thứ Bảy. Sáng: 8h00 - 12h00, Chiều: 1h30 - 5h30</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="maps">
                                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d31353.81386504896!2d106.689305!3d10.793939!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317528cdeb13ffff%3A0x8db7b80bb49f4899!2zQ8O0bmcgVHkgQ-G7lSBQaOG6p24gQuG6pXQgxJDhu5luZyBT4bqjbiBJbmRvY2hpbmU!5e0!3m2!1svi!2sus!4v1724829804399!5m2!1svi!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="map-type-3">
                            <div class="row">
                                <div class="col-md-5 main-info">
                                    <div class="info">
                                        <h3>Trụ sở chính</h3>
                                        <ul>
                                            <li><i class="icon-building-filled"></i> <strong>Địa chỉ:</strong> {{$setting->address}}</li>
                                            <li><i class="icon-phone-filled"></i> <strong>Điện thoại:</strong> {{$setting->hotline}}</li>
                                            <li><i class="icon-mail-filled"></i> <strong>Email:</strong> {{$setting->email}}</li>
                                            <li><i class="icon-time"></i> <strong>Thời gian làm việc:</strong> Từ thứ Hai đến thứ Bảy. Sáng: 8h00 - 12h00, Chiều: 1h30 - 5h30</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="maps">
                                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d31353.81386504896!2d106.689305!3d10.793939!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317528cdeb13ffff%3A0x8db7b80bb49f4899!2zQ8O0bmcgVHkgQ-G7lSBQaOG6p24gQuG6pXQgxJDhu5luZyBT4bqjbiBJbmRvY2hpbmU!5e0!3m2!1svi!2sus!4v1724829804399!5m2!1svi!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="map-type-4">
                            <div class="row">
                                <div class="col-md-5 main-info">
                                    <div class="info">
                                        <h3>Trụ sở chính</h3>
                                        <ul>
                                            <li><i class="icon-building-filled"></i> <strong>Địa chỉ:</strong> {{$setting->address}}</li>
                                            <li><i class="icon-phone-filled"></i> <strong>Điện thoại:</strong> {{$setting->hotline}}</li>
                                            <li><i class="icon-mail-filled"></i> <strong>Email:</strong> {{$setting->email}}</li>
                                            <li><i class="icon-time"></i> <strong>Thời gian làm việc:</strong> Từ thứ Hai đến thứ Bảy. Sáng: 8h00 - 12h00, Chiều: 1h30 - 5h30</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="maps">
                                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d31353.81386504896!2d106.689305!3d10.793939!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317528cdeb13ffff%3A0x8db7b80bb49f4899!2zQ8O0bmcgVHkgQ-G7lSBQaOG6p24gQuG6pXQgxJDhu5luZyBT4bqjbiBJbmRvY2hpbmU!5e0!3m2!1svi!2sus!4v1724829804399!5m2!1svi!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="map-type-5">
                            <div class="row">
                                <div class="col-md-5 main-info">
                                    <div class="info">
                                        <h3>Trụ sở chính</h3>
                                        <ul>
                                            <li><i class="icon-building-filled"></i> <strong>Địa chỉ:</strong> {{$setting->address}}</li>
                                            <li><i class="icon-phone-filled"></i> <strong>Điện thoại:</strong> {{$setting->hotline}}</li>
                                            <li><i class="icon-mail-filled"></i> <strong>Email:</strong> {{$setting->email}}</li>
                                            <li><i class="icon-time"></i> <strong>Thời gian làm việc:</strong> Từ thứ Hai đến thứ Bảy. Sáng: 8h00 - 12h00, Chiều: 1h30 - 5h30</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="maps">
                                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d31353.81386504896!2d106.689305!3d10.793939!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317528cdeb13ffff%3A0x8db7b80bb49f4899!2zQ8O0bmcgVHkgQ-G7lSBQaOG6p24gQuG6pXQgxJDhu5luZyBT4bqjbiBJbmRvY2hpbmU!5e0!3m2!1svi!2sus!4v1724829804399!5m2!1svi!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')

@endsection
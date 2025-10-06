<!DOCTYPE html>
<html>
<head>
    <title>Load More Data on Button Click using JQuery Laravel - ItSolutionStuff.com</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
      
<div class="container mt-5" style="max-width: 750px">
    <div id="data-wrapper">
        @include('data')
    </div>
    <div class="text-center">
        <button class="btn btn-success load-more-data"><i class="fa fa-refresh"></i> Load More Data...</button>
    </div>
</div>
  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    var ENDPOINT = "{{ route('posts.index') }}";
    var page = 1;
  
    $(".load-more-data").click(function(){
        page++;
        infinteLoadMore(page);
    });
  
    /*------------------------------------------
    --------------------------------------------
    call infinteLoadMore()
    --------------------------------------------
    --------------------------------------------*/
    function infinteLoadMore(page) {
        $.ajax({
                url: ENDPOINT + "?page=" + page,
                datatype: "html",
                type: "get",
                beforeSend: function () {
                    $('.auto-load').show();
                }
            })
            .done(function (response) {
                if (response.html == '') {
                    $('.auto-load').html("We don't have more data to display :(");
                    return;
                }
                $('.auto-load').hide();
                $("#data-wrapper").append(response.html);
            })
            .fail(function (jqXHR, ajaxOptions, thrownError) {
                console.log('Server error occured');
            });
    }
</script>
</body>
</html>
<?php
session_start();
if(isset($_SESSION['access_token'])) { ?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Google Drive Service</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h3>File google drive</h3>
        <hr/>
        <div class="d-flex align-items-center">
            <div class="input-group mr-2">
                <input type="text" id="searchInput" class="form-control" placeholder="Search...">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button" id="searchButton" onclick="list()"><i class="fa fa-search"></i> Cari</button>
                </div>
            </div>

            <a href="logout.php" class="btn btn-danger" type="button" id="searchButton">Keluar</a>
        </div>
        
        <div id="fetch_data" class="mt-3 text-center">
            <p>Sedang mengambil data...</p>
        </div>

        <div class="table-responsive" id="show_data">
            <table class="table table-bordered table-hover mt-3">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Mimetype</th>
                        <th>Description</th>
                        <th>Created Time</th>
                        <th>Modified Time</th>
                        <th>Parents</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                </tbody>
            </table>
            <div class="text-center">
                <input type="hidden" id="next_page_token">
                <button id="button_next_page" type="button" class="btn btn-outline-primary mb-5" onclick="load_more()">Muat lebih banyak</button>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        list();

        function list() {
            var keyword = $('#searchInput').val();

            var data = {};
            if (keyword) {
                data.keyword = keyword;
            }

            $('#tbody').empty();
            $("#show_data").hide();
            $("#fetch_data").show();
            $.ajax({
                type: "GET",
                url: "list.php",
                contentType: "application/json",
                dataType: "json",
                data: data,
                success: function(response) {                    
                    if(response.status) {
                        $("#next_page_token").val(response.next_page_token);

                        if(response.next_page_token) {
                            $("#button_next_page").show();
                        } else {
                            $("#button_next_page").hide();
                        }
                        response.data.forEach(element => {
                            $('#tbody').append('<tr>'+
                                '<td>'+element.file_name+'</td>'+
                                '<td>'+element.mime_type+'</td>'+
                                '<td>'+element.description+'</td>'+
                                '<td>'+element.created_time+'</td>'+
                                '<td>'+element.modified_time+'</td>'+
                                '<td>'+element.parents+'</td>'+
                                '<td><a href="'+element.web_link+'" target="_blank">Preview</td>'+
                            '</tr>');
                        });
                    } else {
                        $('#tbody').append('<tr>'+
                                '<td colspan="8">Data tidak tersedia</td>'+
                            '</tr>');
                        $("#button_next_page").hide();
                    }

                    $("#show_data").show();
                    $("#fetch_data").hide();
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }

        function load_more() {
            var pageToken = $('#next_page_token').val();
            var data = {};
            if (pageToken) {
                data.pageToken = pageToken;
            }

            $("#button_next_page").attr("disabled", true);
            $("#button_next_page").text("Loading...");
            $.ajax({
                type: "GET",
                url: "list.php",
                contentType: "application/json",
                dataType: "json",
                data: data,
                success: function(response) {                    
                    if(response.status) {
                        $("#next_page_token").val(response.next_page_token);

                        if(response.next_page_token) {
                            $("#button_next_page").show();
                        } else {
                            $("#button_next_page").hide();
                        }

                        response.data.forEach(element => {
                            $('#tbody').append('<tr>'+
                                '<td>'+element.file_name+'</td>'+
                                '<td>'+element.mime_type+'</td>'+
                                '<td>'+element.description+'</td>'+
                                '<td>'+element.created_time+'</td>'+
                                '<td>'+element.modified_time+'</td>'+
                                '<td>'+element.parents+'</td>'+
                                '<td><a href="'+element.web_link+'" target="_blank">Preview</td>'+
                            '</tr>');                            
                        });
                    }

                    $("#button_next_page").removeAttr("disabled");
                    $("#button_next_page").text("Muat lebih banyak");
                },
                error: function(response) {
                    console.log(response);
                }
            });            
        }
    </script>

</body>

</html>
<?php 
} else {
    $redirect_uri   = 'http://' . $_SERVER['HTTP_HOST'];
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
?>
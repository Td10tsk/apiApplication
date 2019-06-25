<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Document API</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css" integrity="2hfp1SzUoho7/TsGGGDaFdsuuDL0LX2hnUp6VkX3CUQ2K4K+xjboZdsXyp4oUHZj" crossorigin="anonymous">

        <!-- Script -->
        <script>
            var page =  {{$page}};
            var perPage = {{$perPage}};
            var emptyPagination = {{$empty}};
        </script>
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-left {
                position: absolute;
                left: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .title-small {
                font-size: 22px;
            }

            a,a:hover {
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: bold;
                font-size: 1rem;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .modal {
                display: none;
                position: fixed;
                z-index:    1;
                left: 0;
                top: 0;
                width:  100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(255,255,255,0.9);
            }

            .modal-content {
                background-color: #c8cbcf;
                margin: 15% auto;
                padding: 20px;
                border: 1px solid #818182;
                width: 50%;
            }

            input {
                text-align: center;
            }

            #account:hover {
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                    Document API
                </div>
                <div class="links">
                    <a href=".\?page=1&perPage=10">Document List</a>
                    <a id="account">Account</a>
                </div>
                <div id="modal" class="modal">
                    <div class="modal-content">
                        Please enter account name:
                        <input type="text" size="20" id="login">
                        <input type="submit" value="Send" id="send">
                        <input type="submit" value="Delete Token" id="removeToken">
                        <p id="auth"></p>
                    </div>
                </div>
            </div>
            <div id="table-wrap" style="position: absolute;top: 140px;">
                <table class="table table-hover" id="dataList" style="display: none;">
                    <thead>
                    <tr>
                        <th>Document Id</th>
                        <th>Status</th>
                        <th>Payload</th>
                        <th>Created</th>
                        <th>Updated</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
    <script type="text/javascript" src="{{ URL::asset('js/document.js') }}"></script>
</html>

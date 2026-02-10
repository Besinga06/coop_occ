<!DOCTYPE html>
<html>

<head>
    <title>Farmer's Cooperative</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="POS SOFTWARE" />
    <script type="application/x-javascript">
        addEventListener("load", function() {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/font-awesome.css" rel="stylesheet">

    <style type="text/css">
        .isa_info,
        .isa_success,
        .isa_warning,
        .isa_error {
            margin: 10px 0px;
            padding: 12px;
        }

        .isa_error {
            color: #D8000C;
            background-color: #FFD2D2;
        }

        .close-btn {
            position: fixed;
            top: 0px;
            left: 0px;
            background: #F44336;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            cursor: pointer;
        }

        .close-btn:hover {
            opacity: 0.6;
        }


        .logo-img {
            width: 130px;

            height: 130px;

            border-radius: 50%;

            object-fit: cover;

            border: 3px solid #fff;

            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);

            display: block;
            margin: 0 auto 15px auto;

        }
    </style>
</head>

<body>
    <div style="width: 100%;text-align: right;display: none;position: absolute;" id="loader">
        <img style="height:130px;width:130px" src="images/loading-gif.gif">
    </div>

    <div class="main" style="padding-top: 30px!important">
        <div class="main-w3lsrow">
            <div class="login-form login-form-left">
                <div class="agile-row">
                    <div>
                        <img src="images/your_logo.png" class="logo-img" alt="Logo">
                    </div>
                    <br>
                    <h4 style="font-size: 25px;color:#fff">Login Here</h4>
                    <div id="message-show"></div>
                    <div class="login-agileits-top">
                        <form action="#" id="form-login">
                            <input type="hidden" name="check-login" />
                            <p>Username </p>
                            <input type="text" class="name" name="username" required="" />
                            <p>Password</p>
                            <input type="password" class="password" name="password" required="" />
                            <input type="submit" value="Login">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="assets/js/core/libraries/jquery.min.js"></script>
    <script type="text/javascript">
        $('#form-login').on('submit', function(e) {
            e.preventDefault();
            $("#loader").show();
            $("#message-show").html("");
            var data = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: './transaction.php',
                data: data,
                success: function(msg) {
                    msg = $.trim(msg);
                    console.log("Server response:", msg);

                    $("#loader").hide();

                    if (msg === "5") {
                        $("#message-show").html(
                            '<div class="isa_error"><i class="fa fa-times-circle"></i> Invalid Credential. Please try again!</div>'
                        );
                    } else if (msg === "1") {
                        window.location = 'admin/index.php';
                    } else if (msg === "2") {
                        window.location = 'admin/pos.php';
                    } else if (msg === "3") {
                        window.location = 'admin/index.php';
                    } else if (msg === "4") {
                        window.location = 'member/dashboard.php';
                    } else {

                        $("#message-show").html(
                            '<div class="isa_error"><i class="fa fa-times-circle"></i> Unexpected server response: ' + msg + '</div>'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    $("#loader").hide();
                    $("#message-show").html(
                        '<div class="isa_error"><i class="fa fa-times-circle"></i> Error: ' + error + '</div>'
                    );
                }
            });
        });
    </script>
</body>

</html>
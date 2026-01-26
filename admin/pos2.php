<html>
    <head>
        <style>html {
            height: 100%;
            }
            body {
            height: 100%;
            margin: 0;
            font-family: Ubuntu;
            background: linear-gradient(#b3ffab, #67fffc);
            }
            #header {
            height: 100px;
            display: flex;
            align-items: center;
            background: linear-gradient(#444444, #333333);
            color: #bbbbbb;
            }
            #headerContent {
            margin-left: 10px;
            }
            #page {
            display: flex;
            }
            #sideBar {
            min-width: 400px;
            max-width: 400px;
            background: red;
            }
            #content {
            width: 100%;
            background: blue;
            }
        </style>
        <head>
    <body>
        <div id="header">
            <div id="headerContent">Desktop </div>
        </div>
        <div id="page">
            <div id="content">content </div>
            <div id="sideBar">
                <div>box 1 </div>
                <div>box 2 </div>
            </div>
        </div>
    </body>
</html>
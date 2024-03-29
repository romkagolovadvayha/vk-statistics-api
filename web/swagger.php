<?php
if (!empty($_GET['g']) && $_GET['g'] == 'json') {
    require("../vendor/autoload.php");
    @$swagger = \Swagger\scan('../controllers');
    header('Content-Type: application/json');
    exit($swagger);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="https://petstore.swagger.io/swagger-ui.css">
    <link rel="icon" type="image/png" href="https://petstore.swagger.io/favicon-32x32.png" sizes="32x32"/>
    <link rel="icon" type="image/png" href="https://petstore.swagger.io/favicon-16x16.png" sizes="16x16"/>
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }

        *,
        *:before,
        *:after {
            box-sizing: inherit;
        }

        body {
            margin: 0;
            background: #fafafa;
        }
    </style>
</head>

<body>
<div id="swagger-ui"></div>

<script src="https://petstore.swagger.io/swagger-ui-bundle.js"></script>
<script src="https://petstore.swagger.io/swagger-ui-standalone-preset.js"></script>
<script>
    window.onload = function () {

        // Begin Swagger UI call region
        const ui = SwaggerUIBundle({
            "dom_id": "#swagger-ui",
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],
            layout: "StandaloneLayout",
            validatorUrl: "https://validator.swagger.io/validator",
            url: "swagger.php?g=json",
        })
        window.ui = ui
    }
</script>
</body>
</html>

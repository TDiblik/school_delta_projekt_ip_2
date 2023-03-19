</head>
<div class="container">
    <h1>404: Not Found</h1>
    <p>Stránka nenalezena </p>
    <a href="./index.php" style="float: right;">Zpět na index</a>
    <?php
        http_response_code(404);
        die();
    ?>
</div>
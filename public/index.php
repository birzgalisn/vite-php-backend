<?php

require_once __DIR__ . '/helpers.php';

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/svg+xml" href="/assets/vite.svg" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vite PHP backend</title>

  <?= vite('main.tsx') ?>
</head>

<body>
  <div id="root"></div>
</body>

</html>

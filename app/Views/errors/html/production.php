<!doctype html>
<html>

  <head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">

    <title>Whoops!</title>

    <style type="text/css">
    <?=preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
    </style>
  </head>

  <body>

    <div class="container text-center">

      <h1 class="headline">Whoops!:(</h1>

      <p class="lead">We seem to have hit a snag. Please try again later...</p>
      <?php 
        # better if LOCALHOST defined in 'index.php'
        if( 'localhost'===$_SERVER['SERVER_NAME'] ) :
            $today = 'logs/log-' .date('Y-m-d') .'.php';
            $today = highlight_file(WRITEPATH .$today, TRUE);

            echo '<div class="whoops">'
                        .    '<dl>'
                        .    '<dt><b> The last error which must be fixed </b></dt>'
                      .        '<dd><br>' .$today .'</dd>'
                        .'</dl>'
                    . '</div>'
                    ;    
    endif;
    ?>
    </div>

  </body>

</html>
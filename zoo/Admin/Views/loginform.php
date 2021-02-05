<!DOCTYPE html>
<html lang="ko">

  <head>
    <title>Zoo Administrator</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Content-type" type="equiv" content="text/html; charset=utf-8" />
    <meta name="X-UA-Compatible" type="equiv" content="IE=edge" />

    <link href="/assets/css/normalize.css" rel="stylesheet">
    <script src="/assets/jquery/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="/assets/bootstrap5/css/bootstrap-reboot.min.css">
    <link rel="stylesheet" href="/assets/bootstrap5/css/bootstrap.min.css">
    <script src="/assets/bootstrap5/js/bootstrap.bundle.min.js"></script>
    <link href="/assets/css/common.css" rel="stylesheet">
    <?php

  loadjscss($html_head_css_js);
  ?>
  </head>

  <body class="text-center">
    <!--
    CONTENT BODY
  -->
    <div class="form-signin-wrap">
      <?=form_open('', ['id' => 'loginform', 'class' => 'form-signin']) ?>
      <input type='hidden' name='gourl' value='/admin/'>
      <h1 class="h3 mb-3 font-weight-normal">Administrator</h1>
      <?php

        if ($login_status != 'LOGIN_FORM') {

        ?>
      <div class="alert alert-danger" role="alert">
        <?=lang('Login.'.$login_status)?>
      </div>
      <?php
        }
        ?>
      <label for="loginid" class="visually-hidden">ID</label>
      <input type="text" name="loginid" id="loginid" class="login-input" placeholder="ID" required autofocus autocomplete="off">
      <label for="loginpwd" class="visually-hidden">Password</label>
      <input type="password" name="loginpwd" id="loginpwd" class="login-input" placeholder="Password" required>
      <button class="login-input" type="submit">Sign in</button>
      <?= date("Y-m-d") ?>
      </form>
    </div>
  </body>

</html>
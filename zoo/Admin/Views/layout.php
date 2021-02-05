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

  <body>
    <div id="body">
      <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-lg-2 mr-0 px-3" href="/admin/">Zoo Office</a>
        <div id="page_name"><span><?=$page_nm?></span><span><?=join("</span><span>", $arr_page_nm??[])?></span></div>
        <ul class="navbar-nav px-3">
          <li class="nav-item text-nowrap">
            <a class="nav-link" href="/admin/logout"><span data-feather="power"></span> Logout</a>
          </li>
        </ul>
      </nav>
      <!--<i class="fa fa-shield"></i> normal<br>-->
      <div id="main-wrap" class="container-fluid">
        <div class="row main-body">
          <nav id="sidebarMenu" class="d-md-block sidebar collapse show">
            <div class="sidebar-sticky pt-3">
              <ul class="nav flex-column">
                <li class="nav-item">
                  <a class="nav-link <?=isset($arr_request_uri[1])?"":"active"?>" href="/admin">
                    <i class="fa fa-dashboard fa-fw fa-lg"></i>
                    <span class='nav-nm'>Dashboard</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?=$arr_request_uri[1]=="members"?"active":""?>" href="/admin/members/">
                    <i class="fa fa-id-card-o fa-fw fa-lg"></i>

                    <span class='nav-nm'>회원 관리</span>
                  </a>
                  <div class="nav-subitem">
                    <ol>
                      <li><a href="/admin/members/">회원 목록</a></li>
                      <li><a href="/admin/members/department/">회사,부서 설정</a></li>
                      <li><a href="/admin/members/position/">직급/직책 설정</a></li>
                      <li><a href="/admin/managers/">관리자 설정</a></li>
                    </ol>
                  </div>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="/admin/menus">
                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-menu-button-wide-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" d="M14 7H2a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1zM2 6a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H2z" />
                      <path fill-rule="evenodd" d="M15 11H1v-1h14v1zM2 12.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM1.5 0A1.5 1.5 0 0 0 0 1.5v2A1.5 1.5 0 0 0 1.5 5h13A1.5 1.5 0 0 0 16 3.5v-2A1.5 1.5 0 0 0 14.5 0h-13zm1 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3zm9.927.427l.396.396a.25.25 0 0 0 .354 0l.396-.396A.25.25 0 0 0 13.396 2h-.792a.25.25 0 0 0-.177.427z" />
                    </svg>
                    <span class='nav-nm'>메뉴 관리</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="/admin/boards">
                    <span data-feather="database"></span>
                    <span class='nav-nm'>게시판 관리</span>
                  </a>
                </li>
              </ul>
            </div>
          </nav>

          <main role="main" class="col-md-9 col-lg-10 px-md-3">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-1 pb-2 mb-3 border-bottom">
              <h1 id="page_name_content" class="h2">222<?=$page_nm?></h1>
            </div>
          </main>
        </div>
      </div>
    </div>
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js" ></script>-->
    <!--<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>-->
    <script src="https://unpkg.com/feather-icons"></script>

    <?php
    loadJsCssCustom(['_mod_/admin/assets/js/feather_replace.js'],'',1);
?>
  </body>

</html>
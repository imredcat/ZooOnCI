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
    /**
     * production 이면 캐싱된 loadJscss 사용
     * 아니면 <link,  <script 사용 
     * $html_head_css_js 배열|스트링
     */
    loadJscss($html_head_css_js,0);
    ?>
  </head>

  <body>
    <div id="body">
      <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-lg-2 mr-0 px-3" href="/admin/">Zoo Office</a>
        <div id="page_name"><span><?=$page_nm?></span><span><?=join("</span><span>", $arr_page_nm??[])?></span></div>
        <ul class="navbar-nav px-3">
          <li class="nav-item text-nowrap">
            <a class="nav-link nav-icon" href="/admin/logout"><span data-feather="power"></span> Logout</a>
          </li>
        </ul>
      </nav>
      <!--<i class="fa fa-shield"></i> normal<br>-->
      <div id="main-wrap" class="container-fluid">
        <div class="row main-body">
          <nav id="sidebarMenu" class="d-md-block sidebar collapse show">
            <div class="sidebar-sticky sidebar_triggers">
              <ul class="nav flex-column">
                <li class="nav-item <?=isset($arr_request_uri[1])?"":"active"?>" id='nv_dashboard'>
                  <a href='/admin/' class="nav-link nav-icon" href="/admin">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-speedometer" viewBox="0 0 16 16">
                      <path d="M8 2a.5.5 0 0 1 .5.5V4a.5.5 0 0 1-1 0V2.5A.5.5 0 0 1 8 2zM3.732 3.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707zM2 8a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 8zm9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5zm.754-4.246a.389.389 0 0 0-.527-.02L7.547 7.31A.91.91 0 1 0 8.85 8.569l3.434-4.297a.389.389 0 0 0-.029-.518z" />
                      <path fill-rule="evenodd" d="M6.664 15.889A8 8 0 1 1 9.336.11a8 8 0 0 1-2.672 15.78zm-4.665-4.283A11.945 11.945 0 0 1 8 10c2.186 0 4.236.585 6.001 1.606a7 7 0 1 0-12.002 0z" />
                    </svg>
                  </a>
                  <dl class='nav-subitem' for='nv_dashboard'>
                    <dt>Dashboard</dt>
                  </dl>
                </li>
                <li class="nav-item <?=isset($arr_request_uri[1])=='siteconfig'?"active":""?>" id="nv_preferences">
                  <a href="/admin/preferences" class="nav-link nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                      <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z" />
                    </svg>
                  </a>
                  <dl class='nav-subitem' for='nv_preferences'>
                    <dt>기본설정</dt>
                    <dd>사이트 설정</dd>
                    <dd>메뉴관리</dd>
                  </dl>
                </li>
                <li class="nav-item <?=$arr_request_uri[1]=="members"?"active":""?>" id='nv_member'>
                  <a href="/admin/members/" class="nav-link nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                      <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                      <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z" />
                      <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" />
                    </svg>
                  </a>
                  <dl class='nav-subitem' for='nv_member'>
                    <dt>회원관리</dt>
                    <dd><a href="/admin/members/">회원목록</a></dd>
                    <dd><a href="/admin/members/groups/">회원그룹</a></dd>
                    <dd><a href="/admin/members/admins/">설정하기</a></dd>
                  </dl>
                </li>
                <li class="nav-item" id='nv_naviconfig'>
                  <a class="nav-link nav-icon" href="/admin/menus">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-diagram-3-fill" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H14a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 2 7h5.5V6A1.5 1.5 0 0 1 6 4.5v-1zm-6 8A1.5 1.5 0 0 1 1.5 10h1A1.5 1.5 0 0 1 4 11.5v1A1.5 1.5 0 0 1 2.5 14h-1A1.5 1.5 0 0 1 0 12.5v-1zm6 0A1.5 1.5 0 0 1 7.5 10h1a1.5 1.5 0 0 1 1.5 1.5v1A1.5 1.5 0 0 1 8.5 14h-1A1.5 1.5 0 0 1 6 12.5v-1zm6 0a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1a1.5 1.5 0 0 1-1.5-1.5v-1z" />
                    </svg>
                  </a>
                  <dl class='nav-subitem' for='nv_naviconfig'>
                    <dt>메뉴관리</dt>
                    <dd><a href="/admin/members/">메뉴관리</a></dd>
                    <dd><a href="/admin/members/admins/">설정하기</a></dd>
                  </dl>
                </li>
                <li class="nav-item" id='nv_board'>
                  <a class="nav-link nav-icon" href="/admin/boards">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                      <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                      <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
                    </svg>
                  </a>
                  <dl class='nav-subitem' for='nv_naviconfig'>
                    <dt>게시판</dt>
                    <dd><a href="/admin/members/">게시판</a></dd>
                    <dd><a href="/admin/members/admins/">설정하기</a></dd>
                  </dl>
                </li>
                <li class="nav-item">
                  <a class="nav-link nav-icon" href="/admin/boards">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                      <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                      <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
                    </svg>
                  </a>
                  <dl class='nav-subitem' for='nv_naviconfig'>
                    <dt>페이지설정</dt>
                    <dd><a href="/admin/members/">페이지설정</a></dd>
                    <dd><a href="/admin/members/admins/">설정하기</a></dd>
                  </dl>
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
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>

    <?php
    loadJsCssCustom(['_mod_/admin/assets/js/feather_replace.js'],'',1);
?>
  </body>

</html>
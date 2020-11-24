<?php
namespace Zoo;

use CodeIgniter\Controller as Controller;
use \Zoo\Config\Services as ZooServices;
use \Zoo\Config\DmgModelsLoad as DmgModelsLoad;

class BaseController extends Controller
{
    public $is_request_method;
    public function __construct()
    {
    }
    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ["loadjscss","clang","text","cookie","form","jsmessage",'utility'];

    /**
     * Constructor.
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->session = \Config\Services::session();
        $this->clang = DmgServices::clang();
        $this->router = \Config\Services::router();
        //$this->request = \Config\Services::request();

        $this->request_method = $this->request->getMethod();
        $this->data_get = $this->request->getGet();
        $this->data_post = $this->request->getPost();
        $this->data_getpost = $this->request->getGetPost();
        $this->data_server = $this->request->getServer();
        $this->data_cookie = $this->request->getCookie();
        $this->called_method = $this->router->methodName();
        $this->called_controller = $this->router->controllerName();


        $this->data = [
                'get'=>$this->data_get,
                'post'=>$this->data_post,
                'systems'=>[
                    'called_controller' => $this->called_controller  ,
                    'called_method' => $this->called_method
                    
                ]
            ];
        
        $this->mdl_login = DmgModelsLoad::getDmgModelShared('Dmg\Admin\Models\MdlLogin');
        
        //--------------------------------------------------------------------
        // Preload any models, libraries, etc, here.
        //--------------------------------------------------------------------
        // E.g.:
        // $this->session = \Config\Services::session();
        foreach ($this->helpers as $help) {
            helper($help);
        }
        // 로그인 검사
        $this->triggerLoginStat();
        // 상수 설정 로드
        $this->setConstantsReq();
    }

    /**
     * 로그인 상태 확인
     */
    public function triggerLoginStat()
    {
        if ($this->request->uri->getSegment(1) == 'admin' &&
        $this->request->uri->getSegment(2) != 'login' &&
        $this->mdl_login->isLogin() == false) {
            header("location:/admin/login/");
            exit();
        }
        if ($this->request->uri->getSegment(1) == 'admin' &&
        $this->request->uri->getSegment(2) == 'login' &&
        $this->mdl_login->isLogin() == true) {
            header("location:/admin/");
            exit();
        }
    }

    /**
     * 서비스 로드
     */
    public function loadService($service)
    {
        return DmgServices::$service();
    }
    /*
    public function loadedDmgController()
    {
        DmgServices::testService();
    }
    */
    /**
     * 모델 로더
     */
    public function getModel(string $model, ...$params)
    {
        return DmgModelsLoad::getDmgModel($model, ...$params);
    }
    public function getModelShared(string $model, ...$params)
    {
        return DmgModelsLoad::getDmgModelShared($model, ...$params);
    }

    /**
     * 각 모듈별 Constant 상수 로드
     */
    public function setConstantsReq(String $pathConstants='')
    {
        $request = \Config\Services::request();
        $nm_modules = $request->uri->getSegment(1);
        if (file_exists(DMGPATH.$nm_modules."/Config/Constants.php")) {
            require_once(DMGPATH.$nm_modules."/Config/Constants.php");
        }
    }

    /**
     * 뷰 로드
     */
    public function viewload(String $viewpath, array $data=[], array $options=[])
    {
        $options = is_array($options)?$options:[];
        $options['saveData'] = isset($options['saveData'])?$options['saveData']:true;

        $data = is_array($data)?$data:[];
    
        $data['arr_page_nm'] = $this->arr_page_nm;
        $data['page_nm'] = $data['arr_page_nm'][count($data['arr_page_nm'])-1];

        $data['url_get'] = $this->data_get;
        $data['url_post'] = $this->request->getPost();
        $data['html_head_css_js'] = array_merge(
            ['_mod_/admin/assets/css/admin_colors.css',
                '_mod_/admin/assets/css/admin_layout.css',
                '_mod_/admin/assets/css/form.css'],
            $data['html_head_css_js']?:[]
        );
        
        $minifyjscs = DmgServices::libminifyjscss();
        $data['html_head_css_js'] = $minifyjscs->loadjscss($data['html_head_css_js']);
        
        $uri = \Config\Services::uri();
        $data['arr_request_uri'] = explode("/", $uri->getPath());
        
        $view = $this->_view('Dmg\Admin\Views\vu_layout_html', $data, $options);

        if (isset($options['viewpopup'])) {
            $view .= $this->_view('Dmg\Admin\Views\vu_layout_popup_header', $data, $options);
        } else {
            $view .= $this->_view('Dmg\Admin\Views\vu_layout_header', $data, $options);
        }
        if (isset($data['layout_header_sub'])) {
            $view .= $this->_view($data['layout_header_sub'], $data, $options);
        }
        
        if (isset($data['layout_body'])) {
            // layout_body 를 호출하고 실제 $viewpath 는 레이아웃에서 include 됨
            $data['viewpath'] = $viewpath;
            $view .= $this->_view($data['layout_body'], $data, $options);
        } else {
            $view .= $this->_view($viewpath, $data, $options);
        }
        
    
        if (isset($data['layout_footer_sub'])) {
            $view .= $this->_view($data['layout_footer_sub'], $data, $options);
        }
        $view .= $this->_view('Dmg\Admin\Views\vu_layout_footer', $data, $options);
        return $view;
    }

    /**
     * 팝업 뷰
     */
    public function viewpopup(String $viewpath, array $data = [], array $options=[])
    {
        $data['html_head_css_js'][] = "_mod_/admin/assets/css/admin_layout_popup.css";
        $options['viewpopup'] = 1;
        return $this->viewload($viewpath, $data, $options);
    }

    /**
     * 뷰 호출
     */
    public function _view(string $name, array $data = [], array $options = []): string
    {
        $view = \Config\Services::renderer();
        $view->data_get = $this->data_get;
        $view->data_post = $this->data_post;
        $view->data_getpost = $this->data_getpost;
        $view->data_server = $this->data_server;
        $view->data_cookie = $this->data_cookie;
        $view->called_method = $this->called_method;
        $view->called_controller = $this->called_controller;

        $saveData = config(View::class)->saveData;

        if (array_key_exists('saveData', $options)) {
            $saveData = (bool) $options['saveData'];
            unset($options['saveData']);
        }

        return $view->setData($data, 'raw')
                    ->render($name, $options, $saveData);
    }

    
    public function _view_cell(string $library, $params = null, int $ttl = 0, string $cacheName = null): string
    {
        $viewcell = \Config\Services::viewcell();
        $viewcell->data_get = $this->data_get;
        $viewcell->data_post = $this->data_post;
        $viewcell->data_getpost = $this->data_getpost;
        $viewcell->data_server = $this->data_server;
        $viewcell->data_cookie = $this->data_cookie;
        $viewcell->called_method = $this->called_method;
        $viewcell->called_controller = $this->called_controller;
        return $viewcell->render($library, $params, $ttl, $cacheName);
    }
}

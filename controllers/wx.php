<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Wx extends CI_Controller
{
    public function index()
    {
        $this->load->library('wxl');
        if (!isset($_GET['echostr'])) {
            $this->wxl->responseMsg($this);
        } else {
            $this->wxl->valid($this);
        }
        return true;
    }

    //记录微信请求日志
    public function logs()
    {
        $data = array();
        $data['get'] = var_export($_GET, true);
        $data['post'] = var_export($GLOBALS["HTTP_RAW_POST_DATA"], true);
        $this->db->insert('wx_msg', $data);
    }

    public function login_callback()
    {

        $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . WX_APP_ID . '&secret=' . WX_APP_SECRET . '&code=' . $_GET['code'] . '&grant_type=authorization_code';

        $ch         = curl_init();
        curl_setopt($ch, CURLOPT_URL, $get_token_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res        = curl_exec($ch);
        curl_close($ch);
        $json_obj   = json_decode($res, true);
		$this->session->set_userdata(array('openid'=>$json_obj['openid']));
		echo 'callback:<br/>';
        echo $this->session->userdata('openid');

    }

    public function login()
    {
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . WX_APP_ID . '&redirect_uri=' . urlencode('http://jujia-jp.com/wx/login_callback') . '&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect';
        header("Location:" . $url);
    }

    public function redirect()
    {
		$open_id	= $this->session->userdata('openid');
        if ($open_id=='') 
		{
            header("Location:http://jujia-jp.com/wx/login");
        }
		else
		{
            echo "redirect:";
			echo $open_id;
        }
        
    }
}

/* End of file article.php */
/* Location: ./application/controllers/article.php */
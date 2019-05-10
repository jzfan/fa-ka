<?php

namespace app\admin\controller;

use controller\BasicAdmin;
use service\FileService;
use think\Db;

/**
 * 插件助手控制器
 * Class Plugs
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/21
 */
class Plugs extends BasicAdmin
{

    /**
     * 导入用户
     */
    public function importUser()
    {
        if (!$this->request->isPost()) {
            return $this->fetch();
        }

        $file = $this->request->file('file');
        $ext = strtolower(pathinfo($file->getInfo('name'), 4));
        $md5 = [uniqid(), uniqid()];
        $filename = join('/', $md5) . ".{$ext}";
        if (!in_array($ext, ['xlsx'])) {
            $this->error('错误的文件格式');
        }

        // 文件上传处理
        if (($info = $file->move('static' . DS . 'upload' . DS . $md5[0], $md5[1], true))) {
            $file = './static/upload/' . $filename;

            $data = $this->readDataFormExcel($file);

            unset($data[1]);

            foreach ($data as $item) {
                Db::name('user')->insert([
                    'username' => $item['C'],
                    'email' => $item['A'],
                    'password' => $item['B'],
                    'mobile' => $item['C'],
                    'qq' => $item['D'] ? $item['D'] : '',
                    'create_at' => time(),
                ]);
            }
            var_dump($data);
        }
    }

    /**
     * 从上传的 excel 文件中读取数据
     * @param $file string 文件地址
     * @return array|bool
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    protected function readDataFormExcel($file)
    {
        if (!$file) {
            return false;
        }

        vendor('PHPExcel/PHPExcel');
        vendor('PHPExcel/PHPExcel/Reader/Excel2007');
        vendor('PHPExcel/PHPExcel/Reader/Excel5');
        vendor('PHPExcel/PHPExcel/IOFactory');

        //检查文件是否可读
        $excelReader = new \PHPExcel_Reader_Excel2007();
        if (!$excelReader->canRead($file)) {
            $excelReader = new \PHPExcel_Reader_Excel5();
            if (!$excelReader->canRead($file)) {
                return false;
            }
        }

        $obj = $excelReader->load($file);
        //获取默认的 sheet
        $currSheet = $obj->getSheet();
        //取得最大的列号
        $columnH = $currSheet->getHighestColumn();
        //获取列号对应的列数
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($columnH);
        //获取总行数
        $rowCnt = $currSheet->getHighestRow();

        //读取数据
        $data = array();
        for ($_row = 1; $_row <= $rowCnt; $_row++) {
            for ($_column = 0; $_column <= $highestColumnIndex; $_column++) {
                $columnName = \PHPExcel_Cell::stringFromColumnIndex($_column);
                $cellId = $columnName . $_row;
                $cellValue = $currSheet->getCell($cellId)->getValue();
                $data[$_row][$columnName] = $cellValue;
            }
        }

        return $data;

    }

    /**
     * 文件上传
     * @return \think\response\View
     */
    public function upfile()
    {
        $uptype = $this->request->get('uptype');
        if (!in_array($uptype, ['local', 'qiniu', 'oss'])) {
            $uptype = sysconf('storage_type');
        }
        $mode = $this->request->get('mode', 'one');
        $types = $this->request->get('type', 'jpg,png');
        $this->assign('mimes', FileService::getFileMine($types));
        $this->assign('field', $this->request->get('field', 'file'));
        return view('', ['mode' => $mode, 'types' => $types, 'uptype' => $uptype]);
    }

    /**
     * 通用文件上传
     * @return \think\response\Json
     */
    public function upload()
    {
        $file = $this->request->file('file');
        $ext = strtolower(pathinfo($file->getInfo('name'), 4));
        $md5 = str_split($this->request->post('md5'), 16);
        $filename = join('/', $md5) . ".{$ext}";
        if (strtolower($ext) == 'php' || !in_array($ext, explode(',', strtolower(sysconf('storage_local_exts'))))) {
            return json(['code' => 'ERROR', 'msg' => '文件上传类型受限']);
        }
        // 文件上传Token验证
        if ($this->request->post('token') !== md5($filename . session_id())) {
            return json(['code' => 'ERROR', 'msg' => '文件上传验证失败']);
        }
        // 文件上传处理
        if (($info = $file->move('static' . DS . 'upload' . DS . $md5[0], $md5[1], true))) {
            if (($site_url = FileService::getFileUrl($filename, 'local'))) {
                return json(['data' => ['site_url' => $site_url], 'code' => 'SUCCESS', 'msg' => '文件上传成功']);
            }
        }
        return json(['code' => 'ERROR', 'msg' => '文件上传失败']);
    }

    /**
     * 文件状态检查
     */
    public function upstate()
    {
        $post = $this->request->post();
        $filename = join('/', str_split($post['md5'], 16)) . '.' . pathinfo($post['filename'], 4);
        // 检查文件是否已上传
        if (($site_url = FileService::getFileUrl($filename))) {
            $this->result(['site_url' => $site_url], 'IS_FOUND');
        }
        // 需要上传文件，生成上传配置参数
        $config = ['uptype' => $post['uptype'], 'file_url' => $filename];
        switch (strtolower($post['uptype'])) {
            case 'qiniu':
                $config['server'] = FileService::getUploadQiniuUrl(true);
                $config['token'] = $this->_getQiniuToken($filename);
                break;
            case 'local':
                $config['server'] = FileService::getUploadLocalUrl();
                $config['token'] = md5($filename . session_id());
                break;
            case 'oss':
                $time = time() + 3600;
                $policyText = [
                    'expiration' => date('Y-m-d', $time) . 'T' . date('H:i:s', $time) . '.000Z',
                    'conditions' => [['content-length-range', 0, 1048576000]],
                ];
                $config['policy'] = base64_encode(json_encode($policyText));
                $config['server'] = FileService::getUploadOssUrl();
                $config['site_url'] = FileService::getBaseUriOss() . $filename;
                $config['signature'] = base64_encode(hash_hmac('sha1', $config['policy'], sysconf('storage_oss_secret'), true));
                $config['OSSAccessKeyId'] = sysconf('storage_oss_keyid');
        }
        $this->result($config, 'NOT_FOUND');
    }

    /**
     * 生成七牛文件上传Token
     * @param string $key
     * @return string
     */
    protected function _getQiniuToken($key)
    {
        $host = sysconf('storage_qiniu_domain');
        $bucket = sysconf('storage_qiniu_bucket');
        $accessKey = sysconf('storage_qiniu_access_key');
        $secretKey = sysconf('storage_qiniu_secret_key');
        $protocol = sysconf('storage_qiniu_is_https') ? 'https' : 'http';
        $params = [
            "scope" => "{$bucket}:{$key}", "deadline" => 3600 + time(),
            "returnBody" => "{\"data\":{\"site_url\":\"{$protocol}://{$host}/$(key)\",\"file_url\":\"$(key)\"}, \"code\": \"SUCCESS\"}",
        ];
        $data = str_replace(['+', '/'], ['-', '_'], base64_encode(json_encode($params)));
        return $accessKey . ':' . str_replace(['+', '/'], ['-', '_'], base64_encode(hash_hmac('sha1', $data, $secretKey, true))) . ':' . $data;
    }

    /**
     * 字体图标选择器
     * @return \think\response\View
     */
    public function icon()
    {
        $field = $this->request->get('field', 'icon');
        return view('', ['field' => $field]);
    }

}

<?php

namespace app\service\system;

use think\facade\Config;
use think\facade\Request;
use think\facade\Filesystem;
use think\facade\Log;
use maike\trait\ErrorTrait;
use app\model\system\Attachment as AttachmentModel;

class Upload
{
    use ErrorTrait;

    private $fileList = []; //上传成功的文件数组

    /**
     * 图片上传接口
     * 
     * @return array|\think\model\Collection|boolean
     */
    public function image($userId = 0, $field = 'image')
    {
        return $this->upload($field, 'image', $userId);
    }

    /**
     * 视频上传接口
     * 
     * @return array|\think\model\Collection|boolean
     */
    public function video($userId = 0, $field = 'video')
    {
        return $this->upload($field, 'video', $userId);
    }

    /**
     * 文件上传接口
     * 
     * @return array|\think\model\Collection|boolean
     */
    public function file($userId = 0, $field = 'file')
    {
        return $this->upload($field, 'file', $userId);
    }

    /**
     * 文件上传
     *
     * @param string $field
     * @param string $type image图片、video视频、file文件
     * @param integer $uploaderId 上传人ID
     * @return array|\think\model\Collection|boolean
     */
    public function upload($field = 'image', $type = 'image', $uploaderId = 0)
    {
        try {
            // 存储引擎
            $fileConfig = Config::get('filesystem');
            $storage = $fileConfig['default'];
            if (!isset($fileConfig['disks'][$storage]) || !isset($fileConfig['disks'][$storage]['validate'][$type])) {
                $this->setError('上传配置错误');
                return false;
            }
            $config = $fileConfig['disks'][$storage];

            // 接收上传的文件
            $files = Request::file($field);
            if (empty($files)) {
                $this->setError('上传文件无效');
                return false;
            }
            if (!is_array($files)) $files = [$files];

            $groupId = Request::param("group_id", 0);
            $fileName = Request::param("file_name", '');

            $appName = app('http')->getName();

            foreach ($files as $file) {
                //验证上传文件
                validate($config['validate'][$type], [], false, false)->check([$field => $file]);

                $disk = Filesystem::disk($storage);
                $savename = $disk->putFile($type, $file);
                if (!$savename || empty($savename)) {
                    $this->setError("上传失败");
                    return false;
                }
                $fileInfo['file_name'] = !empty($fileName) ? $fileName : $file->getOriginalName();
                $fileInfo['path'] = $savename;
                $fileInfo['size'] = $file->getSize();
                $fileInfo['ext'] = pathinfo($fileInfo['path'], PATHINFO_EXTENSION);
                $fileInfo['type'] = $type;
                $fileInfo['storage'] = $storage;
                $fileInfo['group_id'] = $groupId > -1 ? $groupId : 0;
                $fileInfo['uploader_id'] = $uploaderId;
                $fileInfo['module'] = $appName;
                $this->fileList[] = $fileInfo;
            }
            // 添加文件库记录
            if (is_array($this->fileList) && count($this->fileList) > 0) {
                $list = (new AttachmentModel)->saveAll($this->fileList);
                if ($list) {
                    return $list;
                } else {
                    $this->setError("上传失败");
                    return false;
                }
            } else {
                $this->setError("上传失败");
                return false;
            }
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            Log::write("上传失败" . $e->getMessage(), "error");
            return false;
        }
    }
}

<?php

namespace app\console\controller;

use app\service\system\Upload as UploadService;

class Upload extends Base
{
    /**
     * 图片上传接口
     */
    public function image()
    {
        $service = new UploadService();
        $res = $service->image($this->adminId);
        if ($res) {
            return $this->success($res, "上传成功");
        }
        return $this->error($service->hasError() ? $service->getError() : '上传失败');
    }

    /**
     * 视频上传接口
     */
    public function video()
    {
        try {
            $upService = new UploadService();
            $files = $upService->video($this->adminId);
            if (!$files || $files == null) {
                return $this->error($upService->getError());
            }
            // 视频上传成功
            return $this->success($files[0], "上传成功");
        } catch (\Exception $e) {
            return $this->error('视频上传失败' . $e->getMessage());
        }
    }

    /**
     * 文件上传接口
     */
    public function file()
    {
        try {
            $upService = new UploadService();
            $files = $upService->file($this->adminId);
            if (!$files || $files == null) {
                return $this->error($upService->getError());
            }
            // 文件上传成功
            return $this->success($files[0], "上传成功");
        } catch (\Exception $e) {
            return $this->error('文件上传失败' . $e->getMessage());
        }
    }
}

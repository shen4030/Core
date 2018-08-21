<?php
namespace Core\Tool;

class File{

    private $accessExt = ['img', 'jpg'];

    private $accessSize;

    private $dirRoot = '';

    public function __construct($config)
    {
        if(isset($config['size'])){
            $this->accessSize = $config['size'] ? intval($config['size']) : 2048;
        }
        if(isset($config['ext']) && is_array($config['ext'])){
            $this->accessExt = array_merge($this->accessExt, $config['ext']);
        }
        $this->dirRoot = DOCUMENT_ROOT;
    }

    /**
     * 上传文件
     * @param $key
     * @param string $location
     * @param string $fileName
     * @return mixed
     */
    public function upload($key, $location = '', $fileName = '')
    {
        if(isset($_FILES[$key]) && $_FILES[$key]){
            $file = $_FILES[$key];
            if($file['size'] <= $this->accessSize){
                $realLocation = $this->dirRoot . $location;
                if(!file_exists($realLocation)){
                    # 创建文件夹
                    mkdir($realLocation, 0777, true);
                }

                $ext = $this->getExtByFileName($file['name']);
                if(in_array($ext, $this->accessExt)){
                    if(empty($fileName)){
                        $fileName = rand(0, 1000) . time();
                    }
                    $fileName = $fileName . '.' .$ext;

                    # 保存文件
                    if(move_uploaded_file($file['tmp_name'], $realLocation . $fileName)){

                        $result['status'] = 0;
                        $result['info'] = [
                            'fileName' => $fileName,
                            'realName' => $file['name'],
                            'fileSize' => $file['size'],
                            'fileExt' => $ext,
                            'fileUrl' => $location . $fileName,
                            'fileBaseUrl' => $realLocation . $fileName
                        ];
                    }else{
                        $result['status'] = -1;
                        $result['info'] = '保存失败';
                    }
                }else{
                    $result['status'] = -1;
                    $result['info'] = '文件类型不符合要求';
                }
            }else{
                $result['status'] = -1;
                $result['info'] = '文件过大';
            }

        }else{
            $result['status'] = -1;
            $result['info'] = '上传文件不存在';
        }
        return $result;
    }

    /**
     * 下载文件
     * @param $filePath 网站文件路径
     */
    public function download($filePath)
    {
        $fileRealPath = $this->dirRoot . $filePath;
        if(file_exists($fileRealPath)){

            /* 获取文件大小 */
            $fileSize = filesize($fileRealPath);  
            $fileExtName = $this->getExtByFilePath($fileRealPath);
            /* 返回文件流 */
            header("Content-type: application/octet-stream");
        
            /* 按照字节大小返回 */
            header("Accept-Ranges: bytes");
            header("Accept-Length: $fileSize");
        
            header("Content-Disposition: attachment; filename=".time() .'.'. $fileExtName);
            
            $file = fopen($fileRealPath, "rb" );
            echo fread($file, $fileSize);
            fclose($file);
        }else{
            return '文件不存在';
        }
    }

    /**
     * 删除文件
     * @param $location
     * @param $fileName
     * @return bool
     */
    public function delete($location, $fileName = '')
    {
        if($fileName){
            $realLocation = $this->dirRoot . $location . '/' . $filename;
        }else{
            $realLocation = $this->dirRoot . $location;
        }   
        $result = unlink($realLocation);
        return $result;
    }

    /**
     * 通过文件地址获取文件后缀名
     * @param $filePath
     * @return string
     */
    public function getExtByFilePath($filePath)
    {
        $filePath = realpath($filePath);
        $ext = '';
        if(is_file($filePath)){
            $fileName = basename($filePath);
            $ext = $this->getExtByFileName($fileName);
        }
        return $ext;
    }

    /**
     * 通过文件名称获取文件后缀名
     * @param $fileName
     * @return string
     */
    public function getExtByFileName($fileName)
    {
        $fileName = trim($fileName);
        $ext = '';
        if(strpos($fileName, '.') !== false){
            $extArray = explode('.', $fileName);
            $ext = end($extArray);
        }
        return trim($ext);
    }
}
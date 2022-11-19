<?php
require_once APPPATH."third_party/vendor/autoload.php";
use Aws\Common\Enum\DateFormat;
use Aws\S3\Model\MultipartUpload\UploadId;
use Aws\S3\S3Client;

class Multipartupload
{    
    private $CI;
    private $client;
    private $bucket;
    private $folderpath;
    private $s3_key;
    private $s3_secret;
    private $__s3_path;
    private $__inits;
    function __construct($configs = array())
    {
        $this->s3_keys      = $configs['settings'];
        $this->s3_key       = $this->s3value('s3_access');
        $this->s3_secret    = $this->s3value('s3_secret');
        $this->bucket       = $this->s3value('s3_bucket');
        $this->folderpath   = isset($configs['folderpath']) ? $configs['folderpath'] : '';
        $this->__s3_path    = 'https://'.$this->bucket.'.s3.amazonaws.com/';

        // echo '79jhd'.$this->s3_key.'03893nf'.'---oh1j'.$this->s3_secret.'abhfkkd';die;  
        $this->client       = S3Client::factory(array(
                'key'               => $this->s3_key,
                'secret'            => $this->s3_secret
        ));
    }
	
    function initialize($inits = array())
    {
        $this->__inits = $inits;
        $this->upload();
    }

	/**
     * The public hook 
     * @return array    The response for the front end
     */

	function upload(){
        //Get the action
        $action = isset($this->__inits['action']) ? strtolower($this->__inits['action']) : '';
        
        try {
            //Run it
            switch ($action) {
                case 'multipartstart':
                    $result = $this->multipartStartAction();
                    break;
                case 'multipartsignpart':
                    $result = $this->multipartSignPartAction();
                    break;
                case 'multipartcomplete':
                    $result = $this->multipartCompleteAction();
                    break;
                case 'multipartabort':
                    $result = $this->multipartAbortAction();
                    break;
                default:
                    $result = ['error' => 'Action not found'];
            }
        } catch(Exception $e) {
            $result = [
                'error' => $e->getMessage()
            ];
        }
        $this->sendResult($result);
    }   

    /**
     * This will create the signature call to start the upload
     * @return string   The URL to call next 
     */
    function multipartStartAction() {        
        //Make the upload model details
        $model = $this->client->createMultipartUpload(array(
            'Bucket'        => $this->bucket,
            'Key'           => $this->__inits['fileInfo']['key'],
            'ContentType'   => $this->__inits['fileInfo']['type'],
            'Metadata'      => $this->__inits['fileInfo'],
            'Body'          => '',
        ));

        return array(
            'uploadId'  => $model->get('UploadId'),
            'key'       => $model->get('Key'),
        );
    }

    /**
     * This will create the signature for a file chunk
     * @return string   The URL to call next 
     */
    function multipartSignPartAction() {
        /*echo '<pre>'; 
        print_r($_REQUEST);
        print_r($_FILES);
        die;*/
        $command = $this->client->getCommand('UploadPart',
            array(
                'Bucket'        => $this->bucket,
                'Key'           => $this->__inits['sendBackData']['key'],
                'UploadId'      => $this->__inits['sendBackData']['uploadId'],
                'PartNumber'    => $this->__inits['partNumber'],
                'ContentLength' => $this->__inits['contentLength'],
            )
        );

        $request = $command->prepare();
        // This dispatch commands wasted a lot of my times :'(
        $this->client->dispatch('command.before_send', array('command' => $command));
        $request->removeHeader('User-Agent');
        $amzDate = gmdate(DateFormat::RFC2822);
        $request->setHeader('x-amz-date', $amzDate);
        // This dispatch commands wasted a lot of my times :'(
        $this->client->dispatch('request.before_send', array('request' => $request));

        return [
            'url'           => $request->getUrl(),
            'authHeader'    => (string) $request->getHeader('Authorization'),
            'dateHeader'    => (string) $amzDate
        ];
    }

    /**
     * Completing the upload
     * This call will stitch the file chunks together
     * @return string   The URL to call next 
     */
    private function multipartCompleteAction() {
        $partsModel = $this->client->listParts(array(
            'Bucket'    => $this->bucket,
            'Key'       => $this->__inits['sendBackData']['key'],
            'UploadId'  => $this->__inits['sendBackData']['uploadId'],
        ));

        $model = $this->client->completeMultipartUpload(array(
            'Bucket'    => $this->bucket,
            'Key'       => $this->__inits['sendBackData']['key'],
            'UploadId'  => $this->__inits['sendBackData']['uploadId'],
            'Parts'     => $partsModel['Parts'],
        ));
        
        $file_name = explode('/', $this->__inits['sendBackData']['key']);
        $file_name = $file_name[sizeof($file_name)-1];
        $raw_name = substr($file_name, 0 , (strrpos($file_name, ".")));
        return [
            'url' => $this->__inits['sendBackData']['key']
            ,'full_path' => $this->__s3_path.$this->__inits['sendBackData']['key']
            ,'file_name' => $file_name
            ,'raw_name' => $raw_name
        ];
    }

    /**
     * Abort an upload
     * This will clean up the files on the AWS Bucket
     * @return string   The URL to call next 
     */
    private function multipartAbortAction() {
        $model = $this->client->abortMultipartUpload(array(
            'Bucket'        => $this->bucket,
            'Key'           => $this->__inits['sendBackData']['key'],
            'UploadId'      => $this->__inits['sendBackData']['uploadId']
        ));

        return [
            'success' => true
        ];
    }

    /**
     * Simple Output class
     * @param  array  $result The result to return to the browser
     */
    private function sendResult(array $result) {
        $response = [
            'result' => $result
        ];

        if(!$result) {
            $code = 500;
            $response['ok'] = false;
        } elseif(isset($result['error'])) {
            $code = 500;
            $response = $result;
            $response['ok'] = false;
        } else {
            $code = 200;
            $response['ok'] = true;
        }

        http_response_code($code);
        header('Content-Type: application/json');
        exit(json_encode($response));
    }
    
    function deleteS3Object($param = array()){
        $object             = isset($param['object'])?$param['object']:'';

        $model = $this->client->deleteObject(array(
            'Bucket'  => $this->bucket,
            'Key' => $object
        ));

        if(!$model) {
            $response['success']    = false;
            $response['message']    = 'Internal server error';
        } elseif(isset($model['error'])){
            $response['details']    = $result;
            $response['success']    = false;
        } else {
            $response['message']    = 'Action success.';
            $response['success']    = true;
        }

        return $response;

    }

    private function s3value($key)
    {
        return $this->s3_keys->$key;
    }

    public function copy_s3_file($param = null)
    {
        $s3_copy_param = array(
            'Bucket'            => $this->bucket,
            'CopySource'        => $this->bucket.'/'.$param['source_path'],
            'Key'               => $param['target_path'],
            // 'ACL' => CannedAcl::PUBLIC_READ,
            'CacheControl'      => "max-age=60, public",
            'Metadata'          =>array('updated'=>'1')
        ); 
       
        $result                 = $this->client->copyObject($s3_copy_param); 
      
        return  $result;
    }

    public function s3ObjectInfo($param = null)
    {
        $s3ObjectInfo = array(
            'Bucket'            => $this->bucket,
            'Key'               => 'uploads/SGlearningapp.com/course_backup/backup_119-8/119/documents/07c7af6381f2823845723aeeb6cdc310-12-2019-10-20-55.docx'//$param['object']/documents/07c7af6381f2823845723aeeb6cdc310-12-2019-10-20-55.docx
        ); 
       print_r($s3ObjectInfo);
        //$result                 = $this->client->getObject($s3ObjectInfo); 
        $result                 = $this->client->getObjectInfo($s3ObjectInfo); 
        print_r($result);
        return  $result;
    }
   
}
?>
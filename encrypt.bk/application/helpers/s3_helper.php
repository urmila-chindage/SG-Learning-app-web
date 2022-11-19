<?php
/**
 * THE SOFTWARE.
 *
 * @package	Ofabee
 * @author	Enfin Technologies
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ofabee S3 Helpers
 *
 * @package     Ofabee
 * @subpackage	Helpers
 * @category	Helpers
 * @author      Enfin Technologies
 */

// ------------------------------------------------------------------------

if ( ! function_exists('uploadToS3'))
{
    function uploadToS3($source, $destination)
    {
        $instance       = & get_instance();
        $instance->load->library('s3_upload');
        $instance->s3_upload->upload_file($source, $destination);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('S3copyFile'))
{
    function S3copyFile($params = array())
    {
        $instance       = & get_instance();
        $instance->load->library('s3_upload');
        $instance->s3_upload->objectCopy($params);
    }
}


// ------------------------------------------------------------------------

if ( ! function_exists('deleteFileFromS3'))
{
    function deleteFileFromS3($fileName)
    {
        $instance       = & get_instance();
        $s3_credentials = $instance->settings->setting('has_s3');
        $s3_bucket      = '';
        $s3_access      = '';
        $s3_secret      = '';
        if( $s3_credentials )
        {
            $s3_credentials = (isset($s3_credentials['as_setting_value']) && isset($s3_credentials['as_setting_value']['setting_value']))?$s3_credentials['as_setting_value']['setting_value']:'';
            if($s3_credentials)
            {
                $s3_bucket = $s3_credentials->s3_bucket;
                $s3_access = $s3_credentials->s3_access;
                $s3_secret = $s3_credentials->s3_secret;
            }
        }

        $saveName = $fileName;

        if (!class_exists('S3'))require_once(APPPATH.'helpers/S3.php');

        //AWS access info
        if (!defined('awsAccessKey')) define('awsAccessKey', $s3_access);
        if (!defined('awsSecretKey')) define('awsSecretKey', $s3_secret);

        //instantiate the class
        $s3 = new S3(awsAccessKey, awsSecretKey);

        $value =  $s3->deleteObject( $s3_bucket, $saveName);
    }
}

if ( ! function_exists('uploadToS3Bulk'))
{
    function uploadToS3Bulk($files = array())
    {

        $instance       = & get_instance();
        $instance->load->library('s3_upload');

        if(!empty($files))
        {
            foreach($files as $source => $destination)
            {
                $destination_file = urlencode($destination);
                if(substr($destination, -3, 3) == 'css')
                {
                    $instance->s3_upload->upload_file($source, $destination, 'text/css');
                }
                else
                {
                    $instance->s3_upload->upload_file($source, $destination);
                }
            }
        }
    }
}
?>
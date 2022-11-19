<?php

/**
 * Amazon S3 Upload PHP class
 *
 * @version 0.1
 */
class S3_upload {

	function __construct()
	{
		$this->CI			  =& get_instance();
        $this->s3_bucket      = '';
        $this->s3_access      = '';
        $this->s3_secret      = '';
        $this->s3_region      = '';

        $s3_credentials 	  = $this->CI->settings->setting('has_s3');
        if( $s3_credentials )
        {
            $s3_credentials = (isset($s3_credentials['as_setting_value']) && isset($s3_credentials['as_setting_value']['setting_value']))?$s3_credentials['as_setting_value']['setting_value']:'';
            if($s3_credentials)
            {
                $this->s3_bucket = $s3_credentials->s3_bucket;
                $this->s3_access = $s3_credentials->s3_access;
                $this->s3_secret = $s3_credentials->s3_secret;
                $this->s3_region = $s3_credentials->s3_region;
            }
        }
		$s3_config = array(
			'use_ssl' => 1,
			'verify_peer' => 1,
			'access_key' => $this->s3_access,
			'secret_key' => $this->s3_secret,
			'bucket_name' => $this->s3_bucket,
		);
		$this->CI->load->library('s3', $s3_config);
	}

	function upload_file($source, $destination, $content_type = '')
	{
		$this->CI->s3->setRegion($this->s3_region);	
		if($content_type)
		{
			$this->CI->s3->putObjectFile( $source, $this->s3_bucket, $destination, S3::ACL_PUBLIC_READ, array(), $content_type);
		}	
		else
		{
			$this->CI->s3->putObjectFile( $source, $this->s3_bucket, $destination, S3::ACL_PUBLIC_READ, array());
		}
	}


	function objectCopy($params = array())
	{
		$source				= isset($params['source']) ? $params['source'] : false;
		$destination		= isset($params['destination']) ? $params['destination'] : false;
		if($source && $destination)
		{
			$this->CI->s3->setRegion($this->s3_region);
			$this->CI->s3->copyObject([
				'Bucket'               => $targetBucket,
				'Key'                  => $targetKeyname,
				'CopySource'           => "{$sourceBucket}/{$sourceKeyname}",
				'ServerSideEncryption' => 'AES256',
			]);
			$sourceBucket = $this->s3_bucket;
			$sourceKeyname = '*** Your Source Object Key ***';

			$targetBucket = $this->s3_bucket;
			$targetKeyname = '*** Your Target Object Key ***';

			$s3 = new S3Client([
				'version' => 'latest',
				'region'  => 'us-east-1'
			]);

			
		}
	}
}
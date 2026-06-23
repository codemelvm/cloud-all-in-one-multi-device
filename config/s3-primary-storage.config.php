<?php
$CONFIG = array (
  'objectstore' => array(
    'class' => '\OC\Files\ObjectStore\S3',
    'arguments' => array(
      'bucket' => getenv('S3_BUCKET'),
      'autocreate' => true,
      'key'    => getenv('S3_ACCESS_KEY'),
      'secret' => getenv('S3_SECRET_KEY'),
      'hostname' => getenv('S3_HOSTNAME'),
      'port' => 443,
      'use_ssl' => true,
      'region' => getenv('S3_REGION'),
      'use_path_style' => true
    ),
  ),
);

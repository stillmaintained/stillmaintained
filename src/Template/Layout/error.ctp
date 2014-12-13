<?php
// Force AssetCompressHelper to be loaded (ErrorController does not)
$this->loadHelper('AssetCompress.AssetCompress');

$this->extend('default');

echo $this->fetch('content');

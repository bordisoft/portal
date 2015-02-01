<?php defined('_PORTAL') or die();

class Config
{
	public $server_url = 'https://mybilling3.telinta.com';
	public $api_path = 'ls_json_api';
	public $theme = 'default';
	public $session_lifetime = 1800;
}

// <Directory /home/porta-admin/apache/ls_json_api/>
// RewriteEngine On
// RewriteBase /ls_json_api/
// RewriteCond %{REQUEST_METHOD} ^(POST|GET)
// RewriteCond %{REQUEST_URI} .*
// RewriteCond %{REQUEST_FILENAME} !-f
// RewriteRule .* index.mcomp [L]
// AllowOverride FileInfo Limit AuthConfig
// Options ExecCGI FollowSymlinks
// <FilesMatch "\.mcomp$">
// SetHandler perl-script
// PerlHandler HTML::Mason::ApacheHandler
// </FilesMatch>
// </Directory>

?>
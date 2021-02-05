<?php namespace Zoo\Common\Config;

use CodeIgniter\Config\Services as CoreServices;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */

class Services extends CoreServices
{
    public static function clang($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('clang');
        }
        return new \App\Libraries\LibClang();
    }
    public static function libcache($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('libcache');
        }
        return new \App\Libraries\LibCache();
    }
    

    public static function libminifyjscss($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('libminifyjscss');
        }
        return new \App\Libraries\LibMinifyJsCss();
    }
    public static function MinifyJs($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('minifyjs');
        }
        return new \MatthiasMullie\Minify\JS();
    }

    public static function MinifyCss($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('minifycss');
        }
        return new \MatthiasMullie\Minify\CSS();
    }

    public static function loginstats():bool
    {
      if ($getShared) {
          return static::getSharedInstance('loginstats');
      }
      return new \Zoo\Common\Services\LoginStats();      
    }
}
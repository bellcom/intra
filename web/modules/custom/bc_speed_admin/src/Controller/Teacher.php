<?php
namespace Drupal\bc_speed_admin\Controller;

use Drupal\bc_speed_admin\Controller\Request;

Class Teacher extends Request
{

    public static function get($id=null) {
        return self::getRequest('/v1/teachers' . ($id ? '/' . $id:''));
    }


}
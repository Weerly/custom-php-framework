<?php
/**
 * Created by PhpStorm.
 * User: Weerly
 * Date: 03.05.2018
 * Time: 15:42
 */

namespace Controllers;

use Models\UserModel;
use RenderView;
class DefaultController extends Controller
{
    public function IndexAction()
    {
        $view_data = [];

        if (!empty($this->session['user'])) {
            $view_data['auth'] = true;
            $view_data['user_name'] = $this->session['user']["name"];
            $model = new UserModel();
            $activate = $model->isUserActivated($this->session['user']["login"]);
            $view_data['not_activated'] = !$activate;
        } else {
            $view_data['auth'] = false;
        }

        $view = RenderView::setTemplate("main.html.twig", $view_data);

        return $view;
    }
}
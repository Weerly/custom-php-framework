<?php
/**
 * Created by PhpStorm.
 * User: Weerly
 * Date: 02.05.2018
 * Time: 23:24
 */

namespace Controllers;

use Controllers\Controller;
use Core\Session;
use Models\UserModel;
use RenderView;
class UserController extends Controller
{
    public function LoginAction($post)
    {
        if (!$this->session) {
            $model = new UserModel();

            $user = $model->getUser($post);

            $viewData = [];

            if (!!$user && $user !== "error") {

                if (password_verify($post['password'], $user['password'])) {
                    $psid = $model->logInSession();
                    $logged = $model->loginUser($post['login'], $psid);

                    if ($logged) {
                        header("Location: /");
                        exit;
                    } else {
                        $viewData["sys_error"] = true;
                    }
                } else {
                    $viewData["login_error"] = true;
                }
            } else if ($user === "error") {
                $viewData["sys_error"] = true;
            } else {
                $viewData["login_error"] = true;
            }

            return $this->SigninAction($viewData);
        }
    }

    public function RegisterAction($post)
    {
        $model = new UserModel();

        if (!!$this->session) {
            header("Location: /");
            exit;
        } else if (!$model->isInputDataNotEmpty($post)) {
            $view = RenderView::setTemplate("main.html.twig", ['auth' => false, 'empty_error' => true]);
        } else {
            $check = $model->getUser($post);
            $view_data = [];

            if (empty($check)) {
                $code = md5(date("Y-m-d h:i:sa") . '' . (new \DateTime())->getTimestamp());
                $result = $model->sendEmail($post["login"], $post["name"], $code);


                if (!$result) {
                    $saved = false;
                    $view_data['reg_error'] = true;
                    $view_data['auth'] = false;
                } else if ($result === "not_support") {
                    $saved = false;
                    $view_data['support_error'] = true;
                    $view_data['auth'] = false;
                } else {
                    $password = $post["password"];
                    $post["password"] = $model->createHashString($password);
                    $saved = $model->saveNewUser($post);

                    if ($saved) {
                        $activate = $model->activateUser($code, $post['login']);

                        if ($activate) {
                            $view_data['auth'] = true;
                            $view_data['user_name'] = $post["name"];
                            $psid = $model->logInSession();
                            $logged = $model->loginUser($post['login'], $psid);

                            if (!$logged) {
                                $view_data['auth'] = false;
                                $view_data['login_error'] = true;
                            }

                            $view_data['first_run'] = true;
                            $view_data['not_activated'] = true;
                        } else {
                            $saved = false;
                            $view_data['auth'] = false;
                            $view_data['activate_error'] = true;
                        }
                    }
                }
            } else {
                $saved = false;
                $view_data['login_error'] = true;
                $view_data['auth'] = false;
            }

            if (!$saved) {
                $view_data['login'] = $post['login'];
                $view_data['name'] = $post['name'];
                $view_data['error'] = 'error';
                $view = RenderView::setTemplate("signup.html.twig", $view_data);
            } else {
                $view = RenderView::setTemplate("main.html.twig", $view_data);
            }
        }

        return $view;

    }

    public function ActivateAction($code)
    {
        $login = false;
        $model = new UserModel();

        if ($code !== null) {
           $login = $model->getNotActivatedUser($code);
        }

        if (!$code) {
            $isAuth = !empty($this->session["user"]);
            $view_data = [
                'auth' => $isAuth,
                'false_code' => true,
                'false_activate' => true,
            ];

            if($isAuth) {
                $view_data['user_name'] = $this->session["user"]["name"];
            }
        } else if (!$login) {
            header("Location: /");
        } else {
            $isAuth = !empty($this->session["user"]);
            $view_data = [
                'auth' => $isAuth
            ];

            if($isAuth) {
                $view_data['user_name'] = $this->session["user"]["name"];
            }

            $result = $model->deleteActivateUser($code, $login);
            if (!!$result) {

                $activate = $model->makeUserActive($login);

                if (!$activate) {
                    $view_data['false_activate'] = true;
                }
            } else {
                $view_data['false_activate'] = true;
            }
        }

        $view = RenderView::setTemplate("activate.html.twig", $view_data);

        return $view;
    }

    public function SigninAction($data)
    {
        if (!$this->session) {
            $viewData = ["auth" => false];

            if (!!$data) {
                $viewData["login_error"] = $data["login_error"] ?? false;
                $viewData["sys_error"] = $data["sys_error"] ?? false;
            }

            $view = RenderView::setTemplate("signin.html.twig", $viewData);

            return $view;
        } else {
            header("Location: /");
        }
    }

    public function SignupAction()
    {
        $view = RenderView::setTemplate("signup.html.twig", ['auth' => false]);

        return $view;
    }

    public function EditAction($post)
    {
        $view = "";
        $viewData = ["auth" => true];
        if (!!$post && !!$this->session) {

            if (password_verify($post['old_password'], $this->session['user']['password']) ){
                $model = new UserModel();
                if (!$model->isInputDataFullyEmpty($post)) {
                    $post["login"] = $this->session['user']['login'];
                    $password = $post["new_password"];
                    if (empty($password)) {
                        $post["new_password"] = $this->session['user']['password'];
                    } else {
                        $post["new_password"] = $model->createHashString($password);
                    }
                    $update = $model->updateUserData($post);

                    if (!!$update) {
                        header("Location: /edit");
                        exit;
                        $name = $post['name'];
                        $viewData["user_name"] = $name;
                        $viewData["name"] = $name;
                    } else {
                        $name = $this->session['user']['name'];
                        $viewData["user_name"] = $name;
                        $viewData["name"] = $name;
                        $viewData['sys_error'] = true;
                    }
                } else {
                    $name = $this->session['user']['name'];
                    $viewData["user_name"] = $name;
                    $viewData["name"] = $name;
                    $viewData['empty_error'] = true;
                }
            } else {
                $name = $this->session['user']['name'];
                $viewData["user_name"] = $name;
                $viewData["name"] = $name;
                $viewData['password_error'] = true;
            }

            $view = RenderView::setTemplate("edit.html.twig", $viewData);
        } else {
            if (!!$this->session) {
                $name = $this->session['user']['name'];
                $viewData["user_name"] = $name;
                $viewData["name"] = $name;

                $view = RenderView::setTemplate("edit.html.twig", $viewData);
            } else {
                header("Location: /");
                exit;
            }
        }

        return $view;
    }

    public function LogoutAction()
    {
        if (!!$this->session) {
            $model = new UserModel();
            $login = $this->session["user"]["login"];
            $model->logOutUser($login);
        }
        header("Location: /");
    }

}
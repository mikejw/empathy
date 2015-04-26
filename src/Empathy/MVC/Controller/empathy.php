<?php

namespace Empathy\MVC\Controller;

class empathy extends CustomController
{

    public function default_event()
    {
        $this->assign('about', true);
    }
}

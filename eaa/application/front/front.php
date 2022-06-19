<?php

namespace Empathy\MVC\Controller;

class front extends CustomController
{
    public function default_event()
    {
        $this->assign('centerpage', true);
    }

}

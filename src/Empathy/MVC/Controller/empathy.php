<?php

namespace Empathy\MVC\Controller;

class empathy extends CustomController
{

    public function default_event()
    {
        $this->assign('error',
        	'<span style="font-size: 1.5em;">Congratulaions. You have successfully set up an empathy app.</span>');
    }

}

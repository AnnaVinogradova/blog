<?php

namespace Blogger\BlogBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BloggerBlogBundle extends Bundle
{
	public function getParent()
    {
        return 'FOSUserBundle';
    }
}

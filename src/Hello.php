<?php

namespace Pick\HelperUtils;

class Hello
{
    /**
     * Say hello.
     *
     * @param string $name
     * @return string
     */
    public function say($name = 'World')
    {
        return "Hello, {$name}! Welcome to your custom Packagist package.";
    }
}

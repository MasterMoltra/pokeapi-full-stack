<?php

namespace App;

class Template
{
    /** @var string Full path file to include */
    protected $file;
    /** @var array List of vars to be imported into the template */
    protected $vars = [];

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * set  function.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value): Template
    {
        $this->vars[$key] = $value;

        return $this;
    }

    /**
     * Return a list of setted vars.
     */
    public function getTplVars(): array
    {
        return $this->vars;
    }

    /**
     * Get current buffer contents and delete current output buffer.
     */
    public function render()
    {
        extract($this->vars, EXTR_PREFIX_ALL, 'tpl_');
        ob_start();
        include $this->file;

        return ob_get_clean();
    }
}

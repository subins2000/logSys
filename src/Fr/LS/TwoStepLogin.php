<?php
namespace Fr\LS;

class TwoStepLogin extends \Exception
{

    /**
     * @var mixed
     */
    protected $extraInfo = null;

    /**
     * @var string
     */
    protected $status = null;

    /**
     * @param mixed  extraInfo
     * @param string $status  Status code
     */
    public function __construct($status, $extraInfo = null)
    {
        $this->status = $status;
        $this->extraInfo = $extraInfo;
    }

    /**
     * @return boolean
     */
    public function isError()
    {
        return $this->status !== 'login_success' && $this->status !== 'enter_token_form';
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getBlockInfo()
    {
        return $this->extraInfo;
    }

    /**
     * @param  string $option Option name
     * @return mixed
     */
    public function getOption($option)
    {
        return isset($this->extraInfo[ $option ]) ? $this->extraInfo[ $option ] : false;
    }
}

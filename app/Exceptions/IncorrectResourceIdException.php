<?php

namespace App\Exceptions;

class IncorrectResourceIdException extends \Exception
{
    /**
     * IncorrectResourceIdException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = 'Incorrect resource id given.')
    {
        parent::__construct($message);
    }
}

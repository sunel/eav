<?php
namespace Eav\Attribute;

use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Validation\ValidationException as BaseValidationException;

class ValidationException extends BaseValidationException implements MessageProvider
{
    /**
     * Get the validation errors.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors();
    }
    /**
     * Get the messages for the instance.
     *
     * @return \Illuminate\Contracts\Support\MessageBag
     */
    public function getMessageBag()
    {
        return $this->errors();
    }
}

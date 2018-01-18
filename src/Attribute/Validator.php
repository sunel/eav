<?php

namespace Eav\Attribute;

use Eav\Entity;
use Eav\Attribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\PresenceVerifierInterface;
use Illuminate\Contracts\Translation\Translator;

class Validator
{
    /**
     * The Translator implementation.
     *
     * @var \Illuminate\Contracts\Translation\Translator
     */
    protected $translator;

    /**
     * The Presence Verifier implementation.
     *
     * @var \Illuminate\Validation\PresenceVerifierInterface
     */
    protected $presenceVerifier;

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The failed validation rules.
     *
     * @var array
     */
    protected $failedRules = [];

    /**
     * The message bag instance.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $messages;

    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data;

    /**
     * The files under validation.
     *
     * @var array
     */
    protected $files = [];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    protected $rules;

    /**
     * Create a new Validator instance.
     *
     * @param  \Illuminate\Contracts\Translation\Translator  $translator
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return void
     */
    public function __construct(Translator $translator, array $data, array $rules, MessageBag $messageBag = null, $failedRules = [])
    {
        $this->translator = $translator;
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = ($messageBag)?: new MessageBag;
        $this->failedRules = $failedRules;
    }

    public static function make($attribute, $classRules, $validator)
    {
        return (new static(
            $validator->getTranslator(),
            $attribute,
            $classRules,
            $validator->messages(),
            $validator->failed()
        ))->setPresenceVerifier($validator->getPresenceVerifier());
    }
    
    /**
     * Determine if the data passes the validation rules.
     *
     * @return bool
     */
    public function passes()
    {
        $previouslyValidated = $this->messages->keys();
        // We'll spin through each rule, validating the attributes attached to that
        // rule. Any error messages will be added to the containers with each of
        // the other error messages, returning true if we don't have messages.
        foreach ($this->rules as $attribute => $rule) {
            if (!in_array($attribute, $previouslyValidated)) {
                $this->validate($attribute, $rule);
            }
        }

        return count($this->messages->all()) === 0;
    }

    /**
     * Determine if the data fails the validation rules.
     *
     * @return bool
     */
    public function fails()
    {
        return ! $this->passes();
    }

    /**
     * Get the failed validation rules.
     *
     * @return array
     */
    public function failed()
    {
        return $this->failedRules;
    }

    /**
     * Get the message container for the validator.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function messages()
    {
        if (! $this->messages) {
            $this->passes();
        }

        return $this->messages;
    }

    /**
     * An alternative more semantic shortcut to the message container.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function errors()
    {
        return $this->messages();
    }

    /**
     * Get the messages for the instance.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getMessageBag()
    {
        return $this->messages();
    }

    /**
     * Validate a given attribute against a rule.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @return void
     */
    protected function validate($attribute, $rule)
    {
        $value = $this->getValue($attribute);

        $rule->validate($value, $this);
    }

    /**
     * Get the value of a given attribute.
     *
     * @param  string  $attribute
     * @return mixed
     */
    protected function getValue($attribute)
    {
        if (! is_null($value = Arr::get($this->data, $attribute))) {
            return $value;
        }
    }

    /**
     * Add a failed rule and error message to the collection.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return void
     */
    public function addFailure(Attribute $attribute, $message)
    {
        $this->addError($attribute->getAttributeCode(), $message);

        $this->failedRules[$attribute->getAttributeCode()] = [];
    }

    /**
     * Add an error message to the validator's collection of messages.
     *
     * @param  string  $attribute
     * @param  string  $rule
     * @param  array   $parameters
     * @return void
     */
    protected function addError($attribute, $message)
    {
        $message = $this->getCustomMessageFromTranslator($message);

        $this->messages->add($attribute, $message);
    }

        /**
     * Get the custom error message from translator.
     *
     * @param  string  $customKey
     * @return string
     */
    protected function getCustomMessageFromTranslator($customKey)
    {
        if (!Str::startsWith($customKey, 'validation.custom.')) {
            return $customKey;
        }

        $shortKey = str_replace('validation.custom.', '', $customKey);

        $customMessages = Arr::dot(
            (array) $this->translator->trans('validation.custom')
        );

        foreach ($customMessages as $key => $message) {
            if ($key === $shortKey || (Str::contains($key, ['*']) && Str::is($key, $shortKey))) {
                return $message;
            }
        }

        return $customKey;
    }

    /**
     * Get the Presence Verifier implementation.
     *
     * @return \Illuminate\Validation\PresenceVerifierInterface
     *
     * @throws \RuntimeException
     */
    public function getPresenceVerifier()
    {
        if (! isset($this->presenceVerifier)) {
            throw new RuntimeException('Presence verifier has not been set.');
        }

        return $this->presenceVerifier;
    }

    /**
     * Set the Presence Verifier implementation.
     *
     * @param  \Illuminate\Validation\PresenceVerifierInterface  $presenceVerifier
     * @return void
     */
    public function setPresenceVerifier(PresenceVerifierInterface $presenceVerifier)
    {
        $this->presenceVerifier = $presenceVerifier;
        
        return $this;
    }
}

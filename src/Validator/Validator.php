<?php
namespace Src\Validation;

class Validator {
    private array $data;
    private array $rules;
    private array $errors = [];

    private function __construct($data, $rules) {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make($data, $rules) {
        return new self($data, $rules);
    }

    public function fails() {
        $this->errors = [];

        foreach ($this->rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;
            $rules = explode('|', $ruleString);

            foreach ($rules as $rule) {
                // required
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $this->errors[$field][] = 'required';
                }

                // min length
                elseif (str_starts_with($rule, 'min:') && strlen((string)$value) < (int)substr($rule, 4)) {
                    $this->errors[$field][] = $rule;
                }

                // max length
                elseif (str_starts_with($rule, 'max:') && strlen((string)$value) > (int)substr($rule, 4)) {
                    $this->errors[$field][] = $rule;
                }

                // email
                elseif ($rule === 'email' && $value !== null) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->errors[$field][] = 'email';
                    }
                }

                // enum
                elseif (str_starts_with($rule, 'enum:')) {
                    $opts = explode(',', substr($rule, 5));
                    if ($value !== null && !in_array($value, $opts, true)) {
                        $this->errors[$field][] = 'enum';
                    }
                }

                // numeric
                elseif ($rule === 'numeric') {
                    if ($value !== null) {
                        // pastikan nilainya benar-benar angka valid
                        if (!is_numeric($value) || !preg_match('/^-?[0-9]+(\.[0-9]+)?$/', (string)$value)) {
                            $this->errors[$field][] = 'numeric';
                        }
                    }
                }

                // integer
                elseif ($rule === 'integer') {
                    if ($value !== null) {
                        // validasi bilangan bulat murni (positif/negatif)
                        if (filter_var($value, FILTER_VALIDATE_INT) === false || !preg_match('/^-?[0-9]+$/', (string)$value)) {
                            $this->errors[$field][] = 'integer';
                        }
                    }
                }
            }
        }

        return !empty($this->errors);
    }

    public function errors() {
        return $this->errors;
    }

    public static function sanitize(array $input) {
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $input[$key] = trim($value);
            }
        }
        return $input;
    }
}

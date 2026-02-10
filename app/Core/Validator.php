<?php

namespace App\Core;

/**
 * Input Validation Class
 * Centralized validation logic for all user inputs
 */
class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validate required fields
     */
    public function required(string $field, string $message = null): self
    {
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field] = $message ?? "$field is required";
        }
        return $this;
    }

    /**
     * Validate email format
     */
    public function email(string $field, string $message = null): self
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? "$field must be a valid email";
        }
        return $this;
    }

    /**
     * Validate minimum length
     */
    public function minLength(string $field, int $min, string $message = null): self
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $min) {
            $this->errors[$field] = $message ?? "$field must be at least $min characters";
        }
        return $this;
    }

    /**
     * Validate maximum length
     */
    public function maxLength(string $field, int $max, string $message = null): self
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $max) {
            $this->errors[$field] = $message ?? "$field must not exceed $max characters";
        }
        return $this;
    }

    /**
     * Validate numeric value
     */
    public function numeric(string $field, string $message = null): self
    {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = $message ?? "$field must be a number";
        }
        return $this;
    }

    /**
     * Validate positive number
     */
    public function positive(string $field, string $message = null): self
    {
        if (isset($this->data[$field]) && (float)$this->data[$field] <= 0) {
            $this->errors[$field] = $message ?? "$field must be a positive number";
        }
        return $this;
    }

    /**
     * Validate minimum value
     */
    public function min(string $field, float $min, string $message = null): self
    {
        if (isset($this->data[$field]) && (float)$this->data[$field] < $min) {
            $this->errors[$field] = $message ?? "$field must be at least $min";
        }
        return $this;
    }

    /**
     * Validate maximum value
     */
    public function max(string $field, float $max, string $message = null): self
    {
        if (isset($this->data[$field]) && (float)$this->data[$field] > $max) {
            $this->errors[$field] = $message ?? "$field must not exceed $max";
        }
        return $this;
    }

    /**
     * Validate date format
     */
    public function date(string $field, string $format = 'Y-m-d', string $message = null): self
    {
        if (isset($this->data[$field])) {
            $d = \DateTime::createFromFormat($format, $this->data[$field]);
            if (!$d || $d->format($format) !== $this->data[$field]) {
                $this->errors[$field] = $message ?? "$field must be a valid date ($format)";
            }
        }
        return $this;
    }

    /**
     * Validate value is in allowed list
     */
    public function in(string $field, array $allowed, string $message = null): self
    {
        if (isset($this->data[$field]) && !in_array($this->data[$field], $allowed)) {
            $this->errors[$field] = $message ?? "$field has invalid value";
        }
        return $this;
    }

    /**
     * Validate password strength
     */
    public function strongPassword(string $field, string $message = null): self
    {
        if (!isset($this->data[$field])) {
            return $this;
        }

        $password = $this->data[$field];
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'at least 8 characters';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'one uppercase letter';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'one lowercase letter';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'one number';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'one special character';
        }

        if (!empty($errors)) {
            $this->errors[$field] = $message ?? "Password must contain " . implode(', ', $errors);
        }

        return $this;
    }

    /**
     * Validate unique value in database
     */
    public function unique(string $field, string $table, string $column, $exceptId = null, string $message = null): self
    {
        if (!isset($this->data[$field])) {
            return $this;
        }

        $db = Database::getInstance();
        $query = "SELECT COUNT(*) as count FROM $table WHERE $column = ?";
        $params = [$this->data[$field]];

        if ($exceptId !== null) {
            $query .= " AND id != ?";
            $params[] = $exceptId;
        }

        $result = $db->query($query, $params)->fetch();
        if ($result['count'] > 0) {
            $this->errors[$field] = $message ?? "$field already exists";
        }

        return $this;
    }

    /**
     * Check if validation passed
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Check if validation failed
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Get all errors
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error
     */
    public function firstError(): ?string
    {
        return !empty($this->errors) ? reset($this->errors) : null;
    }

    /**
     * Add custom error
     */
    public function addError(string $field, string $message): self
    {
        $this->errors[$field] = $message;
        return $this;
    }
}

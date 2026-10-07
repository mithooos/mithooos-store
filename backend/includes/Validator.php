<?php
class Validator {
    private array $errors = [];
    private array $data   = [];

    public function __construct(array $data) { $this->data = $data; }

    public static function make(array $data): self { return new self($data); }

    public function required(string $field, string $label = ''): self {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $val   = $this->data[$field] ?? null;

        if (is_array($val)) {
            if (empty($val)) $this->errors[$field] = "$label is required.";
            return $this;
        }

        if (trim((string)$val) === '') $this->errors[$field] = "$label is required.";
        return $this;
    }

    public function email(string $field): self {
        $val = $this->data[$field] ?? '';
        if ($val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL))
            $this->errors[$field] = 'Invalid email address.';
        return $this;
    }

    public function min(string $field, int $len, string $label = ''): self {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $val   = $this->data[$field] ?? '';
        if (strlen($val) < $len)
            $this->errors[$field] = "$label must be at least $len characters.";
        return $this;
    }

    public function numeric(string $field, string $label = ''): self {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $val   = $this->data[$field] ?? '';
        if ($val !== '' && !is_numeric($val))
            $this->errors[$field] = "$label must be a number.";
        return $this;
    }

    public function positive(string $field, string $label = ''): self {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $val   = $this->data[$field] ?? 0;
        if ((float)$val <= 0)
            $this->errors[$field] = "$label must be greater than zero.";
        return $this;
    }

    public function nonNegative(string $field, string $label = ''): self {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $val   = $this->data[$field] ?? 0;
        if ((float)$val < 0)
            $this->errors[$field] = "$label cannot be negative.";
        return $this;
    }

    public function max(string $field, int|float $max, string $label = ''): self {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $val   = $this->data[$field] ?? 0;
        if ((float)$val > $max)
            $this->errors[$field] = "$label must not exceed $max.";
        return $this;
    }

    public function in(string $field, array $allowed, string $label = ''): self {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $val   = $this->data[$field] ?? '';
        // JSON bodies decode numeric fields as int/float (e.g. rating: 5),
        // while $allowed is typically written as strings (['1','2',...]).
        // Compare as strings so both cases match correctly under strict
        // in_array(), rather than silently failing valid numeric input.
        $valStr = is_bool($val) ? ($val ? '1' : '0') : (string)$val;
        if ($val !== '' && !in_array($valStr, array_map('strval', $allowed), true))
            $this->errors[$field] = "$label must be one of: " . implode(', ', $allowed) . '.';
        return $this;
    }

    public function passes(): bool { return empty($this->errors); }
    public function fails():  bool { return !$this->passes(); }
    public function errors(): array { return $this->errors; }

    public function validated(): array {
        if ($this->fails()) throw new RuntimeException('Validation failed.');
        return $this->data;
    }
}

<?php
namespace App\Core;

class Validator
{
    private array $errors = [];

    public function required(string $field, ?string $value, string $label): self
    {
        if ($value === null || trim($value) === '') {
            $this->errors[$field] = "$label est obligatoire.";
        }
        return $this;
    }

    public function minLength(string $field, ?string $value, int $min, string $label): self
    {
        if ($value !== null && mb_strlen(trim($value)) > 0 && mb_strlen(trim($value)) < $min) {
            $this->errors[$field] = "$label doit contenir au moins $min caractères.";
        }
        return $this;
    }

    public function maxLength(string $field, ?string $value, int $max, string $label): self
    {
        if ($value !== null && mb_strlen(trim($value)) > $max) {
            $this->errors[$field] = "$label ne doit pas dépasser $max caractères.";
        }
        return $this;
    }

    public function email(string $field, ?string $value, string $label = 'Email'): self
    {
        if ($value !== null && trim($value) !== '' && !filter_var(trim($value), FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "$label n'est pas une adresse email valide.";
        }
        return $this;
    }

    public function inList(string $field, ?string $value, array $allowed, string $label): self
    {
        if ($value !== null && trim($value) !== '' && !in_array($value, $allowed, true)) {
            $this->errors[$field] = "$label contient une valeur non autorisée.";
        }
        return $this;
    }

    public function intRange(string $field, $value, int $min, int $max, string $label): self
    {
        $v = (int) $value;
        if ($v < $min || $v > $max) {
            $this->errors[$field] = "$label doit être entre $min et $max.";
        }
        return $this;
    }

    public function positiveInt(string $field, $value, string $label): self
    {
        if ((int) $value <= 0) {
            $this->errors[$field] = "$label est obligatoire.";
        }
        return $this;
    }

    public function password(string $field, ?string $value, string $label = 'Mot de passe'): self
    {
        if ($value !== null && trim($value) !== '' && mb_strlen($value) < 8) {
            $this->errors[$field] = "$label doit contenir au moins 8 caractères.";
        }
        return $this;
    }

    public function file(string $field, array $fileData, array $allowedExt, int $maxSizeMb, string $label, bool $required = false): self
    {
        if (empty($fileData['tmp_name'])) {
            if ($required) {
                $this->errors[$field] = "$label est obligatoire.";
            }
            return $this;
        }

        $ext = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            $this->errors[$field] = "$label : format non autorisé. Formats acceptés : " . implode(', ', $allowedExt) . '.';
        }

        if ($fileData['size'] > $maxSizeMb * 1024 * 1024) {
            $this->errors[$field] = "$label ne doit pas dépasser {$maxSizeMb} Mo.";
        }

        return $this;
    }

    public function phone(string $field, ?string $value, string $label = 'Téléphone'): self
    {
        if ($value !== null && trim($value) !== '' && !preg_match('/^[\d\s\+\-\.\(\)]{6,20}$/', trim($value))) {
            $this->errors[$field] = "$label n'est pas un numéro valide.";
        }
        return $this;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

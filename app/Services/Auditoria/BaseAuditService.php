<?php

namespace App\Services\Auditoria;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class BaseAuditService
{
    protected string $modelClass;

    protected array $ignoredKeys = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function logCreate(array $meta, array $newValues): void
    {
        $newValues = $this->filterValues($newValues);

        if (empty($newValues)) {
            return;
        }

        $changedFieldsObj = [];
        foreach ($newValues as $field => $value) {
            $changedFieldsObj[$field] = [
                'old' => null,
                'new' => $value,
            ];
        }

        $data = $this->buildBaseData($meta);
        $data['action'] = 'create';
        $data['changed_fields'] = $changedFieldsObj;
        $data['old_values'] = null;
        $data['new_values'] = $newValues;
        $data['changed_fields_count'] = count($newValues);

        $this->write($data);
    }

    public function logUpdate(array $meta, array $before, array $after): void
    {
        $before = $this->filterValues($before);
        $after = $this->filterValues($after);

        [$changedFields, $oldValues, $newValues] = $this->diff($before, $after);

        if (empty($changedFields)) {
            return;
        }

        $changedFieldsObj = [];
        foreach ($changedFields as $field) {
            $changedFieldsObj[$field] = [
                'old' => $oldValues[$field] ?? null,
                'new' => $newValues[$field] ?? null,
            ];
        }

        $data = $this->buildBaseData($meta);
        $data['action'] = 'update';
        $data['changed_fields'] = $changedFieldsObj;
        $data['old_values'] = $oldValues;
        $data['new_values'] = $newValues;
        $data['changed_fields_count'] = count($changedFields);

        $this->write($data);
    }

    public function logDelete(array $meta, array $before): void
    {
        $before = $this->filterValues($before);

        $changedFieldsObj = [];
        foreach ($before as $field => $value) {
            $changedFieldsObj[$field] = [
                'old' => $value,
                'new' => null,
            ];
        }

        $data = $this->buildBaseData($meta);
        $data['action'] = 'delete';
        $data['changed_fields'] = $changedFieldsObj;
        $data['old_values'] = $before;
        $data['new_values'] = null;
        $data['changed_fields_count'] = count($before);

        $this->write($data);
    }

    protected function buildBaseData(array $meta): array
    {
        $request = request();
        $user = Arr::get($meta, 'user') ?: $request?->user();

        return [
            'user_id' => $user?->id,
            'auditable_type' => Arr::get($meta, 'auditable_type'),
            'auditable_id' => Arr::get($meta, 'auditable_id'),
            'process_type' => Arr::get($meta, 'process_type'),
            'process_id' => Arr::get($meta, 'process_id'),
            'client_id' => Arr::get($meta, 'client_id'),
            'context' => Arr::get($meta, 'context'),
            'request_id' => $this->resolveRequestId(),
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'url' => $request?->fullUrl(),
        ];
    }

    protected function resolveRequestId(): ?string
    {
        $request = request();
        if (!$request) {
            return null;
        }

        $existing = $request->attributes->get('audit_request_id');
        if ($existing) {
            return $existing;
        }

        $headerId = $request->headers->get('X-Request-ID')
            ?? $request->headers->get('X-Request-Id')
            ?? $request->headers->get('X-REQUEST-ID');

        $requestId = $headerId ?: (string) Str::uuid();
        $request->attributes->set('audit_request_id', $requestId);

        return $requestId;
    }

    protected function diff(array $before, array $after): array
    {
        $changedFields = [];
        $oldValues = [];
        $newValues = [];

        foreach ($after as $key => $newValue) {
            $oldValue = $before[$key] ?? null;

            if (!$this->valuesEqual($oldValue, $newValue)) {
                $changedFields[] = $key;
                $oldValues[$key] = $oldValue;
                $newValues[$key] = $newValue;
            }
        }

        return [$changedFields, $oldValues, $newValues];
    }

    protected function valuesEqual($oldValue, $newValue): bool
    {
        return $this->normalizeValue($oldValue) === $this->normalizeValue($newValue);
    }

    protected function normalizeValue($value)
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        if (is_numeric($value)) {
            return $this->normalizeNumber((string) $value);
        }

        if (is_array($value)) {
            return $this->normalizeArray($value);
        }

        if (is_object($value)) {
            return (string) $value;
        }

        return trim((string) $value);
    }

    protected function normalizeArray(array $value): array
    {
        $normalized = [];
        foreach ($value as $key => $val) {
            $normalized[$key] = $this->normalizeValue($val);
        }

        if ($this->isAssoc($normalized)) {
            ksort($normalized);
        } else {
            $normalized = array_values($normalized);
        }

        return $normalized;
    }

    protected function normalizeNumber(string $value): string
    {
        $value = str_replace([' ', ','], ['', '.'], $value);
        if (!is_numeric($value)) {
            return $value;
        }

        $normalized = rtrim(rtrim(number_format((float) $value, 10, '.', ''), '0'), '.');
        return $normalized === '' ? '0' : $normalized;
    }

    protected function filterValues(array $values): array
    {
        foreach ($this->ignoredKeys as $key) {
            unset($values[$key]);
        }

        return $values;
    }

    protected function isAssoc(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    protected function write(array $data): void
    {
        $modelClass = $this->modelClass;
        $modelClass::create($data);
    }
}

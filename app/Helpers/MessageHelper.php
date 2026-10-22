<?php

namespace App\Helpers;

class MessageHelper
{
    private const BASE_SINGULAR = '%s %s correctamente';

    private const BASE_PLURAL = '%s %s correctamente';

    private const ACTIONS = [
        'index' => ['female' => 'obtenida', 'male' => 'obtenido'],
        'store' => ['female' => 'creada', 'male' => 'creado'],
        'show' => ['female' => 'obtenida', 'male' => 'obtenido'],
        'update' => ['female' => 'actualizada', 'male' => 'actualizado'],
        'destroy' => ['female' => 'eliminada', 'male' => 'eliminado'],
    ];

    public static function make(string $entity, string $action, bool $plural = false, bool $isMale = true): string
    {
        $gender = $isMale ? 'male' : 'female';
        $verb = self::ACTIONS[$action][$gender] ?? ($isMale ? 'procesado' : 'procesada');

        if ($plural) {
            $entity = self::pluralize($entity);
            $verb = self::pluralize($verb);
        }

        $template = $plural ? self::BASE_PLURAL : self::BASE_SINGULAR;

        return sprintf($template, $entity, $verb);
    }

    private static function pluralize(string $word): string
    {
        $lastChar = mb_substr($word, -1);

        if (in_array(mb_strtolower($lastChar), ['a', 'e', 'i', 'o', 'u'], true)) {
            return $word.'s';
        }

        return $word.'es';
    }
}

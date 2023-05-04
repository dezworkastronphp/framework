<?php

namespace Astronphp\Components\ErrorReporting;

use Exeption;

class ErrorReportingParser
{
     
    const ERRORS = [
        'E_ERROR'               =>E_ERROR,
        'E_WARNING'             =>E_WARNING,
        'E_PARSE'               =>E_PARSE,
        'E_NOTICE'              =>E_NOTICE,
        'E_CORE_ERROR'          =>E_CORE_ERROR,
        'E_CORE_WARNING'        =>E_CORE_WARNING,
        'E_COMPILE_ERROR'       =>E_COMPILE_ERROR,
        'E_COMPILE_WARNING'     =>E_COMPILE_WARNING,
        'E_USER_ERROR'          =>E_USER_ERROR,
        'E_USER_WARNING'        =>E_USER_WARNING,
        'E_USER_NOTICE'         =>E_USER_NOTICE,
        'E_STRICT'              =>E_STRICT,
        'E_RECOVERABLE_ERROR'   =>E_RECOVERABLE_ERROR,
        'E_DEPRECATED'          =>E_DEPRECATED,
        'E_USER_DEPRECATED'     =>E_USER_DEPRECATED,
        'E_ALL'                 =>E_ALL
    ];
    
    public static $codeErrorReporting  = null;

    public static function calculate(string $expression): int
    {
        
        $expression = preg_replace('/\s+/', '', $expression);

        if (!preg_match('/^\(?(?:~?\(?\w+\)?[&|^]?\(?)+\)?$/', $expression)) {
            throw new Exception('Malformed Expression');
        }
        
        $expression  = self::parseInput($expression);
        while (!is_numeric($next = self::getNextExpression($expression))) {
            $result     = self::calculateExpression($next);
            $replace    = preg_replace('/([^\d])/', '\\\\$1', $next);
            $expression = preg_replace("/{$replace}/", $result, $expression);
        }
        self::$codeErrorReporting = $expression;
        return $expression;
    }
    public static function getCodeErrorReporting() {
        return self::$codeErrorReporting;
    }

    private static function parseInput($input) {
        return strtr($input, self::ERRORS);
    }
    
    private static function getNextExpression($input) {
        if (preg_match('/\([^()]+\)/', $input, $match)) {
            return $match[0];
        }
        return $input;
    }

    private static function calculateExpression($input) {

        // Primeiro calcula todas as operações AND
        if (preg_match('/(\d+)&(~)?(\d+)/', $input, $match)) {
            $result  = (int)$match[1] & ($match[2] ? ~(int)$match[3] : (int)$match[3]);
            $replace = preg_replace('/([^\d])/', '\\\\$1', $match[0]);
            return self::calculateExpression(preg_replace("/{$replace}/", $result, $input));
        }
    
        // Depois calcula todas as operações XOR
        if (preg_match('/(\d+)\^(~)?(\d+)/', $input, $match)) {
            $result  = (int)$match[1] ^ ($match[2] ? ~(int)$match[3] : (int)$match[3]);
            $replace = preg_replace('/([^\d])/', '\\\\$1', $match[0]);
            return self::calculateExpression(preg_replace("/{$replace}/", $result, $input));
        }

        // Por último calcula todas as operações OR
        if (preg_match('/(\d+)\|(~)?(\d+)/', $input, $match)) {
            $result  = (int)$match[1] | ($match[2] ? ~(int)$match[3] : (int)$match[3]);
            $replace = preg_replace('/([^\d])/', '\\\\$1', $match[0]);
            return self::calculateExpression(preg_replace("/{$replace}/", $result, $input));
        }
        
        return preg_replace('/\(|\)/', '', $input);
    }
}
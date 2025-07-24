<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use AdvancedSqlInjectionChecker;


class AdvancedSqlInjectionChecker
{
    /**
     * Проверяет, содержит ли строка потенциально опасные SQL-инъекции.
        */
    public static function hasSqlInjection(string $input): bool
    {
        
        $patterns = [
            '/(?:\b(select|union|insert|update|delete|drop|alter|create|truncate)\b)/i', 
            '/(?:--|\#|\;)/', // Комментарии и точка с запятой
            '/(?:\b(and|or|xor|not)\b\s+[\w\s]+\s*(=|like|>|<|in|is|between)\s+[\w\s]+)/i',
            '/(?:\b(?:exec|execute|sp_executesql|xp_cmdshell)\b)/i', 
            '/(?:\b(select|union)[\s\S]+(from|join|into|load_file|information_schema|mysql)\b)/i', 
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        
        $specialChars = ['\'', '"', ';', '\\', '--', '#'];

        foreach ($specialChars as $char) {
           if (str_contains($input, $char)) {
                return true;
            }
        }

        return false;
    }
}


class CheckForSqlInjection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $allInputs = array_merge($request->all(), $request->route()->parameters());

        foreach ($allInputs as $key => $value) {
            if (is_string($value) && AdvancedSqlInjectionChecker::hasSqlInjection($value)) {
                return response()->json(['error' => 'Potential SQL Injection detected in parameter: ' . $key], 400);
            }
        }

        return $next($request);
    }
}


?>
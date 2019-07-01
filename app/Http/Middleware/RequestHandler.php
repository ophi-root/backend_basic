<?php

namespace App\Http\Middleware;

use App\Traits\KeyNameChangeTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequestHandler
{
    use KeyNameChangeTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $result = $this->recursiveCamelToSnake($request->all());
        $request->merge($result);
        return $next($request);
    }

    private function recursiveCamelToSnake($data)
    {
        foreach ($data as $key => $value) {
            if (is_array($data[$key])) {
                $data[$key] = $this->recursiveCamelToSnake($data[$key]);
            } else {
                $data[Str::snake($key)] = $value;
            }
        }
        return $data;
    }
}

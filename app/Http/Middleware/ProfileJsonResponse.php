<?php

namespace App\Http\Middleware;

use Closure;
use DebugBar\DebugBar;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;
use Response;
use ArrayAccess;

class ProfileJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $enable_debugbar = (app()->bound('debugbar') && app('debugbar')->isEnabled()) ? ['_debugbar' => app('debugbar')->getData()]: [];

        $enable_debugbar = $this->filterDebugInfo($enable_debugbar);

        $code = $response->getStatusCode();
        $response->setStatusCode(200);

        if ($response instanceof JsonResponse) {
            $data = $this->wrapResponse($response->getData(true), $code);
            $response->setData($data + $enable_debugbar);
        } elseif (str_contains($response->headers->get('content-type'), 'application/json')) {
            $data = json_decode($response->getContent(), true) ?? null;
            $data = $this->wrapResponse($data, $code);
            $response->setContent($data + $enable_debugbar);
        }

        return $response;
    }

    private function wrapResponse($data, $code)
    {
        $result['code'] = $code === 200 ? 0: $code;
        $result['message'] = null;
        $result['error'] = null;
        $result['data'] = null;
        if ($data instanceof ArrayAccess || is_array($data)) {
            if (array_has($data, ['message', 'exception', 'file', 'line']) || array_has($data, ['message', 'code', 'file', 'error'])) {
                $result['message'] = $data['message'];
                $result['error'] = isset($data['error']) ? $data['error'] : "{$data['file']}:{$data['line']}";
                $result['code'] = $data['code'] ?? $result['code'];
            } elseif (array_has($data, ['message', 'status_code'])) {
                $result['message'] = $data['message'];
                $result['code'] = $data['code'] ?? $data['status_code'];
                if (array_key_exists('errors', $data)) {
                    if (is_array(head($data['errors']))) {
                        $data['errors'] = head($data['errors']);
                        $result['error'] = $result['message'];
                        $result['message'] = implode(',', $data['errors']);
                    } else {
                        $result['error'] = implode(',', $data['errors']);
                    }
                }
            } elseif (array_has($data, ['code', 'data'])) {
                $result['data'] = $this->emptyToNull($data['data']);
                $result['code'] = $data['code'];
            } else {
                $code = is_numeric($data['code'] ?? '') ? intval($data['code']) : '';
                $result['data'] = $this->emptyToNull($data);
                $result['code'] = (!empty($code) && $code < 1000) ? $code: $result['code'];
            }
        } else {
            $result['data'] = $this->emptyToNull($data);
            $result['code'] = $data['code'] ?? $result['code'];
        }
        if (config('app.env') === 'production') {
            unset($result['error']);
        }

        return $result;
    }

    private function emptyToNull($data)
    {
        if (blank($data)) {
            return null;
        } elseif ($data instanceof ArrayAccess || is_array($data)) {
            foreach ($data as $key => $value) {
                if (blank($value)) {
                    $data[$key] = null;
                } elseif ($value instanceof ArrayAccess || is_array($data)) {
                    $data[$key] = $this->emptyToNull($value);
                }
            }
        }

        return $data;
    }

    private function filterDebugInfo(array $debugbar)
    {
        if (empty($debugbar)) {
            return $debugbar;
        }
        $statements = data_get($debugbar, '_debugbar.queries.statements');

        foreach ($statements as &$state) {
            unset($state['backtrace']);
        }
        data_set($debugbar, '_debugbar.queries.statements', $statements);

        return $debugbar;
    }
}

// app/Http/Middleware/FirebaseCors.php
namespace App\Http\Middleware;

use Closure;

class FirebaseCors
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        // السماح لـ Firebase بتحميل SDK
        $response->headers->set('Content-Security-Policy', 
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.gstatic.com https://cdn.jsdelivr.net; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
            "font-src 'self' https://fonts.gstatic.com; " .
            "img-src 'self' data: https:; " .
            "connect-src 'self' https://fcm.googleapis.com https://www.gstatic.com; " .
            "worker-src 'self' blob:;"
        );
        
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-TOKEN');
        
        return $response;
    }
}
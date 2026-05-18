<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrivateFileController extends Controller
{
    /**
     * Sert un fichier stocké sur le disque 'local' (privé).
     * Vérifie l'authentification de l'utilisateur avant de servir.
     */
    public function show(Request $request): StreamedResponse
    {
        $path = $request->query('path');

        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404, 'Fichier non trouvé.');
        }

        // L'utilisateur doit être authentifié pour accéder aux fichiers projet
        if (!auth()->check()) {
            abort(403, 'Accès non autorisé.');
        }

        $filename = basename($path);

        return Storage::disk('local')->download($path, $filename);
    }
}

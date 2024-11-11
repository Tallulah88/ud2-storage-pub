<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HelloWorldController extends Controller
{
    /**
     * Lista todos los ficheros de la carpeta storage/app.
     *
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: Un array con los nombres de los ficheros.
     */
    public function index()
    {
        // Listar archivos en la carpeta raíz de storage/app
        $files = Storage::files('/');

        return response()->json([
            'mensaje' => 'Listado de ficheros',
            'contenido' => $files,
        ]);
    }

     /**
     * Recibe por parámetro el nombre de fichero y el contenido. Devuelve un JSON con el resultado de la operación.
     * Si el fichero ya existe, devuelve un 409.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @param content Contenido del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */

    public function store(Request $request)
    {
        // Validar que 'filename' y 'content' son obligatorios
        $validated = $request->validate([
            'filename' => 'required|string',
            'content' => 'required|string',
        ]);

        // Verificar si el archivo ya existe; si es así, devolver 409 Conflict
        if (Storage::exists($validated['filename'])) {
            return response()->json([
                'mensaje' => 'El archivo ya existe',
            ], 409);
        }

        // Crear el archivo con el contenido proporcionado
        Storage::put($validated['filename'], $validated['content']);

        return response()->json([
            'mensaje' => 'Guardado con éxito',
        ]);
    }

     /**
     * Recibe por parámetro el nombre de fichero y devuelve un JSON con su contenido
     *
     * @param name Parámetro con el nombre del fichero.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: El contenido del fichero si se ha leído con éxito.
     */
    public function show(string $filename)
    {
        // Comprobar si el archivo existe; si no, devolver 404 Not Found
        if (!Storage::exists($filename)) {
            return response()->json([
                'mensaje' => 'Archivo no encontrado',
            ], 404);
        }

        // Leer el contenido del archivo
        $content = Storage::get($filename);

        return response()->json([
            'mensaje' => 'Archivo leído con éxito',
            'contenido' => $content,
        ]);
    }

    /**
     * Recibe por parámetro el nombre de fichero, el contenido y actualiza el fichero.
     * Devuelve un JSON con el resultado de la operación.
     * Si el fichero no existe devuelve un 404.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @param content Contenido del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function update(Request $request, string $filename)
    {
        // Validar que 'content' es obligatorio
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        // Comprobar si el archivo existe; si no, devolver 404 Not Found
        if (!Storage::exists($filename)) {
            return response()->json([
                'mensaje' => 'El archivo no existe',
            ], 404);
        }

        // Actualizar el archivo con el nuevo contenido
        Storage::put($filename, $validated['content']);

        return response()->json([
            'mensaje' => 'Actualizado con éxito',
        ]);
    }

    /**
     * Recibe por parámetro el nombre de ficher y lo elimina.
     * Si el fichero no existe devuelve un 404.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function destroy(string $filename)
    {
        // Comprobar si el archivo existe; si no, devolver 404 Not Found
        if (!Storage::exists($filename)) {
            return response()->json([
                'mensaje' => 'El archivo no existe',
            ], 404);
        }

        // Eliminar el archivo
        Storage::delete($filename);

        return response()->json([
            'mensaje' => 'Eliminado con éxito',
        ]);
    }
}
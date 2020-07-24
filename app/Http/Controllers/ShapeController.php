<?php

namespace App\Http\Controllers;

use App\models\TabArchivosShape;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ShapeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    public function shapesByCategory(Request $request)
    {
        try {
            $shapes = DB::table('tab_shape')
                ->select(
                    'id_shape',
                    'nombre_shape',
                    'resumen_shape',
                    'autor',
                    'fecha_publicacion',
                    'fecha_creacion_metadato',
                )
                ->where('id_categoria', $request->input('id_categoria'))
                ->orderBy('fecha_publicacion', 'desc')
                ->get();

            if (!$shapes->count()) {
                return response()
                        ->json(
                            [
                                "status" => false,
                                "error" => "No es posible obtener shapes para esta categoría..."
                            ]
                        );
            }

            return response()
                ->json(
                    [
                        "status" => true,
                        "shapes" => $shapes
                    ]
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        "status" => false,
                        "error" => $e->getMessage()
                    ]
                );
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function downloadShape(Request $request)
    {
        try {
            $shape = DB::table('tab_archivos_shape')
                ->select("ruta_archivo_shape")
                ->where('id_shape', $request->input('id_shape'))
                ->get();

            if ($shape->count() > 0) {
                $file = public_path() . $shape[0]->ruta_archivo_shape;

                return response()->download($file);
            }
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                    "status" => false,
                    "error" => $e->getMessage()
                    ]
                );
        }
    }
}

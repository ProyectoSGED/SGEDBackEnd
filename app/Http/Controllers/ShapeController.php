<?php

namespace App\Http\Controllers;

use App\models\TabArchivosShape;
use App\models\TabShape;
use Illuminate\Support\Carbon;
use Exception;
use Facade\FlareClient\Stacktrace\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                    'fecha_creacion_metadato'
                )
                ->where('id_categoria', $request->input('id_categoria'))
                ->orderBy('fecha_publicacion', 'desc')
                ->get();

            if (!$shapes->count()) {
                return response()
                        ->json(
                            [
                                "status" => false,
                                "error" => "No se encuentran archivos shapes para esta categoría..."
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
        try {
            set_time_limit(0);

            $request->validate([
                "shape_file" => 'required|mimes:zip',
                "nombre_shape" => 'required|string',
                "resumen_shape" => 'required|string',
                "autor_shape" => 'required|string',
                "shape_fecha_metadato" => 'required|date',
                "id_categoria" => 'required|integer',
                "nombre_categoria"=>'required|string'
            ]);
              
            $shape = DB::table("tab_shape")
                ->select('id_shape')
                ->where('nombre_shape', $request->input('nombre_shape'))
                ->get();

            if ($shape->count() > 0) {
                return response()
                    ->json(
                        [
                            "status" => false,
                            "error" => "Nombre de shape ya se encuentra registrado..."
                        ]
                    );
            }

            $shape = $request->file("shape_file");

            $categoryName = str_replace(" ", "_", $request->input('nombre_categoria'));

            $path = "downloads/".$categoryName;

            //$path = public_path()."/downloads/".$categoryName;

            $response = file_exists($path);
            if (!$response) {
                Storage::makeDirectory($path);
            }

            $response = file_exists($path."/".$shape->getClientOriginalName());

            if ($response) {
                return response()
                    ->json(
                        [
                            "status" => false,
                            "error" => "Archivo seleccionado ya se encuentra registrado..."
                        ]
                    );
            }

            $newShape = new TabShape();
            $newShape->nombre_shape = $request->input("nombre_shape");
            $newShape->resumen_shape = $request->input("resumen_shape");
            $newShape->autor = $request->input("autor_shape");
            $newShape->fecha_publicacion = Carbon::now();
            $newShape->fecha_creacion_metadato = $request->input("shape_fecha_metadato");
            $newShape->id_categoria = $request->input("id_categoria");

            if ($newShape->save()) {
                $shapeFile = DB::table('tab_archivos_shape')
                    ->insert(
                        [
                            "id_shape" => $newShape->id_shape,
                            "ruta_archivo_shape" => "/downloads/".$categoryName."/".$shape->getClientOriginalName()
                        ]
                    );
            }

            if (!$newShape && !$shapeFile) {
                return response()
                    ->json(
                        [
                            "status" => false,
                            "error" => "No es posible registrar nuevo shape..."
                        ]
                    );
            }

            $shapeFile =  $shape->move($path, $shape->getClientOriginalName());

            return response()
                ->json(
                    [
                        "status" => true,
                        "message" => "Nuevo shape registrado con exito..."
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

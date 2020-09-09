<?php

namespace App\Http\Controllers;

use App\models\TabArchivosShape;
use App\models\TabShape;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ShapeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $shapes = DB::table('tab_shape')
                    ->leftJoin('tab_categorias_shape', 'tab_categorias_shape.id_categoria', '=', 'tab_shape.id_categoria')
                    ->leftJoin('tab_archivos_shape', 'tab_archivos_shape.id_shape', '=', 'tab_shape.id_shape')
                    ->select(
                        'tab_shape.id_shape',
                        'tab_shape.nombre_shape',
                        'tab_shape.resumen_shape',
                        'tab_shape.autor',
                        'tab_shape.fecha_publicacion',
                        'tab_categorias_shape.id_categoria',
                        'tab_categorias_shape.nombre_categoria',
                        'tab_archivos_shape.id_archivo_shape'
                    )
                    ->orderBy('tab_shape.nombre_shape')
                    ->get();

            if (!$shapes) {
                return response()
                        ->json(
                            [
                                "status" => false,
                                "error" => "No es posible obtener capas de información registradas..."
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
            
            DB::transaction(function () use ($request) {
                $shape = DB::table("tab_shape")
                    ->select('id_shape')
                    ->where('nombre_shape', $request->input('nombre_shape'))
                    ->get();

                if ($shape->count() > 0) {
                    throw new Exception("Nombre de shape ya se encuentra registrado...");
                }

                $shape = $request->file("shape_file");

                $categoryName = str_replace(" ", "_", $request->input('nombre_categoria'));

                $path = public_path()."/downloads/{$categoryName}";

                $response = file_exists($path);

                if (!$response) {
                    mkdir($path."/", 0777, true);
                }

                $response = file_exists("{$path}/{$shape->getClientOriginalName()}");

                if ($response) {
                    throw new Exception("Archivo seleccionado ya se encuentra registrado...");
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
                            "ruta_archivo_shape" => "/downloads/{$categoryName}/{$shape->getClientOriginalName()}"
                        ]
                    );
                }

                if (!$newShape && !$shapeFile) {
                    throw new Exception("No es posible registrar nuevo shape...");
                }

                $shapeFile = $shape->move($path, $shape->getClientOriginalName());
            });

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
    public function update(Request $request)
    {
        set_time_limit(0);
            
        try {
            $validator = Validator::make($request->all(), [
                "id_shape" => "required|integer",
                "shape_file" => 'mimes:zip',
                "nombre_shape" => 'required|string',
                "resumen_shape" => 'required|string',
                "autor_shape" => 'required|string',
                "shape_fecha_metadato" => 'required|date',
                "id_categoria" => 'required|integer',
                "nombre_categoria" => 'string',
                "id_archivo" => 'integer',
                "update_shape_file" => 'boolean'
            ]);

            if ($validator->errors()->count() > 0) {
                throw new Exception(
                    $validator
                        ->errors()
                        ->first()
                );
            }
               

            DB::transaction(function () use ($request) {
                DB::table('tab_shape')
                    ->where('id_shape', $request->input('id_shape'))
                    ->update([
                        "nombre_shape" => $request->input('nombre_shape'),
                        "resumen_shape" => $request->input('resumen_shape'),
                        "autor" => $request->input('autor_shape'),
                        "fecha_creacion_metadato" => $request->input('shape_fecha_metadato'),
                        "id_categoria" => $request->input('id_categoria')
                    ]);

                if ($request->input('update_shape_file')) {
                    $shapeFile = DB::table('tab_archivos_shape')
                        ->select('ruta_archivo_shape')
                        ->where('id_archivo_shape', $request->input('id_archivo'))
                        ->get();

                    if ($shapeFile->count()) {
                        $path = public_path().$shapeFile[0]->ruta_archivo_shape;

                        $response = File::exists($path);

                        if ($response) {
                            File::delete($path);
                        }

                        $categoryName = str_replace(" ", "_", $request->input('nombre_categoria'));

                        $shapeFile =  DB::table('tab_archivos_shape')
                            ->where('id_archivo_shape', $request->input('id_archivo'))
                            ->update([
                                'ruta_archivo_shape' => "/downloads/{$categoryName}/{$request->file('shape_file')->getClientOriginalName()}"
                            ]);
                        
                            
                        $path = public_path()."/downloads/{$categoryName}";
                        
                        $file = $request->file('shape_file');
                        
                        if (File::exists("{$path}/{$file->getClientOriginalName()}")) {
                            throw new Exception("Archivo seleccionado ya se encuentra registrado para categoría: {$request->input('nombre_categoria')}");
                        }

                        $file->move($path, $file->getClientOriginalName());
                    }
                }
            });

            return response()
                ->json(
                    [
                        "status" => true,
                        "message" => "Capa de información actualizada con éxito..."
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "id_shape" => "required|integer",
            ]);

            if ($validator->errors()->count() > 0) {
                throw new Exception(
                    $validator
                        ->errors()
                        ->first()
                );
            }

            DB::transaction(function () use ($request) {
                $shapeFiles = DB::table('tab_archivos_shape')
                    ->select('ruta_archivo_shape')
                    ->where('id_shape', $request->input('id_shape'))
                    ->get();

                DB::table('tab_archivos_shape')
                    ->where("id_shape", $request->input('id_shape'))
                    ->delete();

                DB::table('tab_shape')
                    ->where('id_shape', $request->input('id_shape'))
                    ->delete();

                foreach ($shapeFiles as $shapeFile) {
                    $path = public_path().$shapeFile->ruta_archivo_shape;

                    if (File::exists($path)) {
                        File::delete($path);
                    }
                }
            });

            return response()
                ->json(
                    [
                        "status" => true,
                        "message" => "Capa de información fue eliminada con éxito"
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

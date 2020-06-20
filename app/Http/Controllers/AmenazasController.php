<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AmenazasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Amenazas = [
            ['title' => 'Datos de población'],
            ['title' => 'Erupción Volcanica'],
            ['title' => 'Incendio Forestal'],
            ['title' => 'Remoción en Masa'],
            ['title' => 'Sistema Frontal'],
            ['title' => 'Teremotos'],
            ['title' => 'Infraestructura Critica'],
        ];
        return view('Amenazas',compact('Amenazas'));


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
}

<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categorias = Categoria::with('padre')->orderBy('nombre')->paginate(10);
        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        $categoriasPadre = Categoria::orderBy('nombre')->get();
        return view('categorias.create', compact('categoriasPadre'));
    }

    public function store(Request $request)
    {
        $data = $this->validarDatos($request);
        $data['estado'] = $request->boolean('estado', true);
        Categoria::create($data);
        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Categoria $categoria)
    {
        $categoriasPadre = Categoria::where('id_categoria', '!=', $categoria->id_categoria)->orderBy('nombre')->get();
        return view('categorias.edit', compact('categoria', 'categoriasPadre'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $data = $this->validarDatos($request, $categoria->id_categoria);
        $data['estado'] = $request->boolean('estado', true);
        $categoria->update($data);
        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        $this->authorize('eliminar-registros');
        if ($categoria->subcategorias()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene subcategorías asociadas.');
        }
        $categoria->delete();
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
    }

    private function validarDatos(Request $request, $idCategoria = null)
    {
        return $request->validate([
            'id_categoria_padre' => 'nullable|exists:categorias,id_categoria|different:id_actual_dummy',
            'nombre' => 'required|string|max:100',
        ]);
    }
}
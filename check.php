php
App\Models\User::all()->each(function ($u) {
    echo $u->id . ' - ' . $u->email . ' - id_empresa: ' . ($u->id_empresa ?? 'NULL') . PHP_EOL;
});

echo '---EMPRESAS---' . PHP_EOL;

App\Models\Empresa::withoutGlobalScopes()->get()->each(function ($e) {
    echo $e->id_empresa . ' - ' . $e->razon_social . PHP_EOL;
});
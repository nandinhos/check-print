# Licao: Bugs Criticos — Laravel 12 + Livewire 4 + PHP 8.4 + DomPDF + ExportController

**Data**: 2026-02-19
**Stack**: Laravel 12, Livewire 4.1.4, PHP 8.4, Alpine.js, DomPDF (barryvdh/laravel-dompdf), Maatwebsite Excel, Tailwind CSS 4
**Tags**: bug, livewire4, alpine, dompdf, php84, export, download, middleware, config
**Projeto**: check-print (Catalogador de Impressoes GAP)

---

## Bug 1: Alpine.js Duplicado no Bundle (Livewire 4)

### Sintoma
Erros no console do browser: "Alpine has already been initialized" ou comportamento erratico de componentes.

### Causa Raiz
O Livewire 4 INJETA e GERENCIA o Alpine.js automaticamente. Importar o Alpine manualmente no `app.js` causa dupla inicializacao.

### Solucao
```js
// ERRADO — NUNCA faca isso com Livewire 4
import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()

// CORRETO — nao importe Alpine. O Livewire 4 cuida disso.
// app.js deve ser minimo ou vazio para componentes Livewire
```

### Prevencao
- [ ] Em projetos com Livewire 4, NAO importar Alpine manualmente
- [ ] Verificar o `app.js` antes do primeiro build: remover qualquer `import Alpine`
- [ ] Se precisar de plugins Alpine (mask, focus etc), registre via Livewire: `Livewire.plugin(...)`

---

## Bug 2: DomPDF + PHP 8.4 — tempnam() E_WARNING fatal

### Sintoma
Geracao de PDF retorna HTTP 500. Log: `Warning: tempnam(): file created in the system's temporary directory`.

### Causa Raiz
PHP 8.4 elevou um aviso sobre `tempnam()` que o DomPDF usa internamente. O Laravel converte warnings em exceptions por padrao, derrubando a requisicao.

### Solucao
```php
// AppServiceProvider.php — boot()
public function boot(): void
{
    // Suprime E_WARNING do tempnam() usado pelo DomPDF (PHP 8.4 compatibility)
    $previousHandler = set_error_handler(function ($errno, $errstr) use (&$previousHandler) {
        if ($errno === E_WARNING && str_contains($errstr, 'tempnam()')) {
            return true; // suprime o warning
        }
        return $previousHandler ? ($previousHandler)($errno, $errstr) : false;
    });
}
```

### Prevencao
- [ ] Ao usar barryvdh/laravel-dompdf com PHP >= 8.4, SEMPRE adicionar este handler no boot()
- [ ] Verificar em nova instalacao: `php artisan route:list | grep pdf` e testar a rota antes de considerar concluido

---

## Bug 3: ExportController — parametros null via ConvertEmptyStringsToNull

### Sintoma
Exportacao Excel/PDF retorna HTTP 500 quando filtros da URL estao vazios (ex: `?inicio=&fim=`).

### Causa Raiz
O middleware `ConvertEmptyStringsToNull` converte strings vazias para `null` ANTES que a request chegue ao controller. Qualquer `$request->get('chave', 'default')` retorna `null` (nao o default) porque a chave EXISTE na request (so que com valor null).

### Solucao
```php
// ERRADO
$inicio = $request->get('inicio', now()->startOfMonth()->format('Y-m-d'));

// CORRETO
$inicio = $request->get('inicio') ?? now()->startOfMonth()->format('Y-m-d');
```

### Prevencao
- [ ] Em controllers que recebem parametros de query string com defaults, SEMPRE usar `?? valor` ao inves de `get(chave, valor)`
- [ ] Regra: `$request->get()` com segundo parametro e INSEGURO quando `ConvertEmptyStringsToNull` esta ativo

---

## Bug 4: Download de Arquivo Interceptado pelo Router do Livewire

### Sintoma
Clicar em link para download de arquivo modelo CSV nao inicia o download — Livewire intercepta o click e tenta navegar via SPA.

### Causa Raiz
Livewire intercepta eventos de click em links `<a href>` para gerenciar navegacao SPA. Sem sinalizacao explicita, um link para um arquivo tambem e interceptado.

### Solucao
```html
<!-- ERRADO — Livewire intercepta -->
<a href="{{ route('export.modelo-csv') }}">Baixar modelo</a>

<!-- CORRETO — atributo download sinaliza ao browser que e download direto -->
<a href="{{ route('export.modelo-csv') }}" download>Baixar modelo CSV</a>
```

### Prevencao
- [ ] Todo link para download de arquivo deve ter o atributo `download`
- [ ] Alternativamente, adicionar `wire:navigate.disabled` se nao quiser usar `download`

---

## Bug 5: Arquivos de Template no .gitignore (storage vs resources)

### Sintoma
Arquivo CSV de modelo criado em `storage/app/templates/` nao aparece no repositorio apos commit. Usuarios nao conseguem baixar o arquivo.

### Causa Raiz
O `.gitignore` do Laravel ignora o conteudo de `storage/app/` por padrao. Arquivos colocados ali nao sao rastreados pelo git.

### Solucao
Mover templates/assets publicos para `resources/`:
```
resources/
  templates/
    modelo-importacao.csv   ← rastreado pelo git ✓
```

### Prevencao
- [ ] Assets/templates que precisam estar no repositorio: usar `resources/`
- [ ] Arquivos gerados em runtime (uploads, caches): usar `storage/`
- [ ] Nunca colocar arquivo estatico de usuario em `storage/app/` esperando que seja commitado

---

## Tipo de retorno incorreto em controller (ExportController::pdf)

### Sintoma
PHPStan ou runtime error: tipo de retorno declarado nao bate com o retorno real.

### Solucao
```php
// ERRADO
public function pdf(Request $request): ResponseInterface

// CORRETO
public function pdf(Request $request): \Illuminate\Http\Response
```

---

## Referencias

- Commit: `fix(frontend)` — Alpine duplicado
- Commit: `fix(dompdf)` — DomPDF PHP 8.4
- Commit: `fix(export)` — parametros null na exportacao
- Commit: `fix(import)` — download interceptado
- Commit: `feat(sprint-2-3)` — modelo CSV para resources/
- Historico: `.aidev/plans/history/2026-02/sprints-concluidas.md`

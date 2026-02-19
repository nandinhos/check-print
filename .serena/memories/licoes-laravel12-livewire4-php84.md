# Lições Aprendidas — Laravel 12 + Livewire 4 + PHP 8.4

Capturadas no projeto check-print (Catalogador de Impressões GAP) em 2026-02-19.

## ⚠️ CRÍTICO: Alpine.js com Livewire 4

**NUNCA** importar Alpine manualmente no `app.js` quando usar Livewire 4.
O Livewire 4 injeta e gerencia o Alpine automaticamente.
Para plugins Alpine: usar `Livewire.plugin(AlpineMask)` etc.

```js
// PROIBIDO com Livewire 4
import Alpine from 'alpinejs'; window.Alpine = Alpine; Alpine.start();
```

---

## ⚠️ CRÍTICO: DomPDF + PHP 8.4

`barryvdh/laravel-dompdf` com PHP 8.4 gera `E_WARNING` no `tempnam()` que derruba a requisição.
**Sempre** adicionar ao `AppServiceProvider::boot()`:

```php
set_error_handler(function ($errno, $errstr) use (&$prev) {
    if ($errno === E_WARNING && str_contains($errstr, 'tempnam()')) return true;
    return $prev ? ($prev)($errno, $errstr) : false;
});
```

---

## ⚠️ CRÍTICO: ConvertEmptyStringsToNull + request->get()

Middleware do Laravel converte strings vazias para `null` ANTES do controller.
`$request->get('chave', 'default')` retorna `null` se a chave existir com valor null.

```php
// ERRADO
$v = $request->get('chave', 'default');

// CORRETO
$v = $request->get('chave') ?? 'default';
```

---

## Downloads via link no Livewire

Links `<a href>` em apps Livewire são interceptados pelo router SPA.
Para downloads, **sempre** usar atributo `download`:

```html
<a href="{{ route('export.arquivo') }}" download>Baixar</a>
```

---

## storage/ vs resources/ para arquivos estáticos

- `storage/app/` → ignorado pelo `.gitignore` do Laravel. Não versionar assets aqui.
- `resources/templates/` → rastreado pelo git. Usar para arquivos que o usuário pode baixar.

---

## Tipo de retorno — ExportController com PDF

```php
// CORRETO para retorno de PDF no Laravel
public function pdf(Request $request): \Illuminate\Http\Response
```

---

**Arquivo KB completo**: `.aidev/memory/kb/2026-02-19-bugs-laravel12-livewire4-php84.md`
**Projeto**: check-print

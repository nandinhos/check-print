# Sprints Concluidas — Fevereiro 2026

> Registros das 6 sprints executadas no projeto Catalogador de Impressoes GAP
> Periodo: 2026-02-18 a 2026-02-19

---

## Sprint 1 — Fundacao e Infraestrutura
**Concluida em**: 2026-02-18
**Commit principal**: `feat(sprint-1)` (hash: 193effc)

### O que foi entregue
- Docker Compose com PHP 8.4-FPM, Nginx, MySQL 8.0, Redis
- Laravel 12 configurado com Livewire 4.1.4 e Tailwind CSS 4
- Migrations: print_logs e manual_overrides
- Models: PrintLog e ManualOverride com relacionamentos
- Factory: PrintLogFactory
- ClassifierService com TDD (25 testes RED->GREEN)
- CsvParserService com TDD (8 testes RED->GREEN)

### Decisoes tecnicas tomadas
- Livewire 4 (nao 3 como planejado) — usuario solicitou durante execucao
- PHP 8.4 via Docker (nao host PHP 8.3)
- Tailwind CSS 4 com @theme CSS variables (sem tailwind.config.js)
- Alpine.js gerenciado pelo Livewire 4 (sem import manual)

### Metricas
- Testes: 33 passando ao final
- Arquivos criados: 12 novos arquivos de codigo

---

## Sprint 2 — Importacao CSV com Preview e Validacao
**Concluida em**: 2026-02-18 (base) + 2026-02-19 (melhorias)
**Commits principais**: `feat(sprint-2-3)`, `fix(import)`, `feat(import)`

### O que foi entregue
- Livewire ImportCsv com WithFileUploads
- CsvParserService refatorado: validateHeaderDetail() e parseWithValidation()
- Preview das primeiras 10 linhas com badges OK/Dup./Erro
- Painel colapsavel de erros por linha (vermelho)
- Painel colapsavel de duplicatas (amber) com origem banco/arquivo
- 4 contadores: Total / A importar / Duplicatas / Com erro
- Arquivo modelo CSV para download (resources/templates/)
- DuplicataService com TDD (10 testes)
- Indice unico composto no banco (usuario, data_impressao, paginas)
- 47 testes passando ao final

### Escopo expandido (nao estava no ROADMAP original)
- Deteccao de duplicatas contra banco de dados
- Deteccao de duplicatas internas no proprio CSV
- Download do arquivo modelo CSV preenchido como exemplo

---

## Sprint 3 — Dashboard e KPIs
**Concluida em**: 2026-02-18
**Commit principal**: `feat(sprint-2-3)`

### O que foi entregue
- Livewire Dashboard com WithPagination
- 5 KPI cards: total impressoes, custo total, custo pessoal, custo admin, percentual
- Filtros reativos: date range, presets (Este Mes/30dias/Ano/Tudo), usuario, tipo, documento
- Tabela paginada (15/pagina) com badges de classificacao clicaveis
- Indicadores AUTO/MANUAL por registro

---

## Sprint 4 — Override Manual
**Concluida em**: 2026-02-18 (integrado no Sprint 3)
**Commit principal**: `feat(sprint-2-3)`

### O que foi entregue
- Livewire Action alternarClassificacao(printLogId)
- Persiste em manual_overrides com classificacao_anterior e classificacao_nova
- Feedback visual imediato sem reload
- Badge visual: MANUAL vs AUTO

### Nao implementado (backlog)
- Undo/reverter para classificacao original

---

## Sprint 5 — Exportacao Excel e PDF
**Concluida em**: 2026-02-18 (base) + 2026-02-19 (correcoes)
**Commits**: `feat(sprint-2-3)`, `fix(dompdf)`, `fix(export)`

### O que foi entregue
- PrintLogsExport com 2 sheets (Resumo + Detalhado)
- ExportController com metodos excel(), pdf(), modeloCsv()
- Template PDF reports/executive.blade.php com KPI grid e Top 5 ofensores
- Botoes de exportacao no dashboard respeitando filtros ativos

### Bugs corrigidos durante entrega
- DomPDF tempnam() PHP 8.4 — suprimido via set_error_handler no AppServiceProvider
- Tipo de retorno pdf() corrigido para Illuminate\Http\Response
- Parametros null (ConvertEmptyStringsToNull) corrigidos com ?? operator
- Caminho do modelo CSV movido de storage/ para resources/templates/

---

## Sprint 6 — Design System
**Concluida em**: 2026-02-18 (integrado desde o inicio)
**Commit principal**: `feat(sprint-2-3)`

### O que foi entregue
- Paleta "Minimalismo Corporativo" via @theme no Tailwind CSS 4
  - Navy Blue #1E3A8A (primary), Amber #F59E0B (pessoal), Violet #8B5CF6 (admin)
- Tipografia: Inter + JetBrains Mono via Google Fonts
- Layout: sidebar navy + header branco + main off-white
- Cards rounded-xl shadow-sm, tabela com hover e cabecalho bg-slate-50
- Loading states via wire:loading, estados empty state e error state
- Favicon e titulo: "Catalogador de Impressoes GAP"

---

## Bugs Corrigidos Pos-Sprints

| Data | Issue | Solucao | Commit |
|------|-------|---------|--------|
| 2026-02-19 | Alpine duplicado | Remover import manual app.js | fix(frontend) |
| 2026-02-19 | DomPDF tempnam() PHP 8.4 | set_error_handler no AppServiceProvider | fix(dompdf) |
| 2026-02-19 | pdf() tipo de retorno errado | Corrigido para Illuminate\Http\Response | fix(dompdf) |
| 2026-02-19 | Excel 500 com parametros null | Substituir get(k,d) por get(k)??d | fix(export) |
| 2026-02-19 | modelo CSV no .gitignore | Movido para resources/templates/ | feat(sprint-2-3) |
| 2026-02-19 | Download modelo interceptado | Atributo download no elemento <a> | fix(import) |
| 2026-02-19 | /importar retorna 500 | Suprimir E_WARNING tempnam() no boot | fix(dompdf) |

---

## Metricas Finais do Projeto

| Metrica | Valor |
|---------|-------|
| Testes passando | 47 |
| Sprints entregues | 6/6 (100%) |
| Bugs corrigidos | 7 |
| Commits | 8 atomicos em portugues |
| Cobertura TDD | ClassifierService + CsvParserService + DuplicataService |
| Stack | Laravel 12.52 + Livewire 4.1.4 + Tailwind CSS 4 + PHP 8.4 |

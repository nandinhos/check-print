# ROADMAP DE IMPLEMENTACAO - Catalogador de Impressoes GAP

> Fonte unica de verdade para o projeto check-print
> Stack: Laravel 12 + TALL Stack + Docker + MySQL 8.0
> Padrao: TDD obrigatorio | Commits em portugues | YAGNI | DRY

---

## VISAO GERAL

### Objetivo
Sistema web para auditoria e gestao de custos de impressao corporativa.
Classifica automaticamente documentos entre "Administrativo" e "Pessoal"
com base em heuristicas de nomenclatura e gera relatorios gerenciais.

### Stack Definitiva (Atualizada)
- Framework: Laravel 12.52
- UI: Livewire 4.1.4 + Alpine.js (gerenciado pelo Livewire) + Tailwind CSS 4
- Banco: MySQL 8.0
- Container: Docker (docker-compose)
- PHP: 8.4
- Servidor: Nginx

---

## SPRINT 1 - Fundacao e Infraestrutura
**Objetivo**: Ambiente funcionando, banco modelado, ClassifierService com TDD.
**Status**: CONCLUIDA em 2026-02-18
**Testes**: 33 passando

### 1.1 - Setup Docker + Laravel 12
**Prioridade**: CRITICA

- [x] Criar docker-compose.yml (PHP 8.4, MySQL 8.0, Nginx, Redis)
- [x] Inicializar projeto Laravel 12 via Composer
- [x] Instalar Livewire 4.1.4
- [x] Instalar e configurar Tailwind CSS 4 + Alpine.js
- [x] Configurar .env com credenciais do banco
- [x] Verificar: `docker-compose up` sobe tudo

### 1.2 - Modelagem do Banco de Dados
**Prioridade**: CRITICA

- [x] Migration: print_logs
- [x] Migration: manual_overrides
- [x] Model PrintLog com fillable e casts
- [x] Model ManualOverride com relacionamento
- [x] Factory para PrintLog (dados de teste)

### 1.3 - ClassifierService (TDD - Core do Sistema)
**Prioridade**: CRITICA

- [x] Criar ClassifierServiceTest (RED)
- [x] Criar ClassifierService (GREEN)
- [x] Testar: "Boleto Nubank" -> PESSOAL
- [x] Testar: "Ficha S1 Caetano" -> ADMINISTRATIVO
- [x] Testar: "Curriculo" -> PESSOAL
- [x] Testar: documento sem keyword -> ADMINISTRATIVO
- [x] Cobertura 100% das heuristicas (25 testes)

### Decisoes tecnicas tomadas na Sprint 1
- Livewire 4 (nao 3 como planejado) — decisao durante execucao
- Tailwind CSS 4 com @theme CSS variables (sem tailwind.config.js)
- Alpine.js gerenciado pelo Livewire 4 (sem import manual no app.js)

---

## SPRINT 2 - Importacao de CSV
**Objetivo**: Upload de CSV, validacao, persistencia no banco.
**Status**: CONCLUIDA em 2026-02-18/19
**Testes**: 47 passando ao final

### 2.1 - Modulo de Upload
**Prioridade**: CRITICA

- [x] Livewire Component: ImportCsvComponent (ImportCsv.php)
- [x] Validacao de colunas obrigatorias (Data, Hora, Usuario, Documento, Paginas, Custo)
- [x] Suporte a separador ; e ,
- [x] Parsing de data formato DD/MM/YYYY
- [x] Parsing de custo (virgula -> ponto)
- [x] ClassifierService aplicado em cada linha
- [x] Arquivo modelo CSV para download (resources/templates/)

### 2.2 - Feedback de Importacao
**Prioridade**: ALTA

- [x] 4 contadores: Total / A importar / Duplicatas / Com erro
- [x] Preview das primeiras 10 linhas com badges OK/Dup./Erro
- [x] Painel colapsavel de erros por linha
- [x] Painel colapsavel de duplicatas (amber) com origem banco/arquivo
- [x] Validacao: arquivo vazio, colunas faltando, formato invalido

### 2.3 - Deteccao de Duplicatas (escopo expandido)
**Prioridade**: ALTA (nao estava no ROADMAP original)

- [x] DuplicataService com TDD (10 testes)
- [x] Deteccao de duplicatas contra banco de dados
- [x] Deteccao de duplicatas internas no proprio CSV
- [x] Indice unico composto no banco (usuario, data_impressao, paginas)

---

## SPRINT 3 - Dashboard e KPIs
**Objetivo**: Interface principal com filtros e cards de KPI.
**Status**: CONCLUIDA em 2026-02-18

### 3.1 - Cards de KPI
**Prioridade**: CRITICA

- [x] Livewire Component: Dashboard (Dashboard.php)
- [x] 5 KPI cards: total impressoes, custo total, custo pessoal, custo admin, percentual
- [x] Calculo reativo ao filtro de datas

### 3.2 - Filtros (Date Range + Outros)
**Prioridade**: CRITICA

- [x] Date Range Picker (campos inicio-fim)
- [x] Presets: "Ultimos 30 dias", "Este Mes", "Ano Atual", "Tudo"
- [x] Dropdown de usuario (select)
- [x] Toggle: Todos / Apenas Pessoais / Apenas Administrativos
- [x] Campo de busca global por nome do documento
- [x] Recalculo instantaneo ao alterar qualquer filtro

### 3.3 - Tabela Principal
**Prioridade**: CRITICA

- [x] Colunas: Data | Usuario | Documento | Paginas | Custo | Classificacao | Acoes
- [x] Paginacao (15 por pagina)
- [x] Destaque visual: PESSOAL = amber, ADMINISTRATIVO = violet
- [x] Badge clicavel para override manual
- [x] Indicadores AUTO / MANUAL por registro

---

## SPRINT 4 - Override Manual e Persistencia
**Objetivo**: Usuarios podem alterar classificacoes; historico auditado.
**Status**: CONCLUIDA em 2026-02-18 (integrado no Sprint 3)

### 4.1 - Override Manual
**Prioridade**: ALTA

- [x] Livewire Action: alternarClassificacao(printLogId)
- [x] Persistir em manual_overrides com classificacao_anterior e classificacao_nova
- [x] Feedback visual imediato (sem reload)
- [x] Indicador visual: classificacao manual vs automatica
- [ ] Undo: possibilidade de reverter para classificacao original (backlog)

---

## SPRINT 5 - Exportacao (Excel + PDF)
**Objetivo**: Geracao de relatorios para contabilidade e gestao.
**Status**: CONCLUIDA em 2026-02-18/19

### 5.1 - Exportacao Excel (.xlsx)
**Prioridade**: ALTA

- [x] Instalar maatwebsite/excel
- [x] PrintLogsExport com 2 abas: Resumo (por usuario) + Detalhado (todas impressoes)
- [x] ResumoExport class
- [x] DetalhadoExport class
- [x] Botao "Exportar Excel" no dashboard
- [x] Respeitar filtros ativos na exportacao

### 5.2 - Exportacao PDF (Relatorio Executivo)
**Prioridade**: ALTA

- [x] Instalar barryvdh/laravel-dompdf
- [x] Blade template: reports/executive.blade.php
- [x] ExportController com metodos excel(), pdf(), modeloCsv()
- [x] KPI grid + Top 5 ofensores no PDF
- [x] Botao "Exportar PDF" no dashboard
- [x] Respeitar filtros ativos

---

## SPRINT 6 - Design System e Polimento
**Objetivo**: Aplicar design "Minimalismo Corporativo" em toda a interface.
**Status**: CONCLUIDA em 2026-02-18 (integrado desde o inicio)

### 6.1 - Design System
**Prioridade**: MEDIA

- [x] Paleta via @theme no Tailwind CSS 4
  - Primary (Navy Blue): #1E3A8A
  - Pessoal: #F59E0B (amber)
  - Administrativo: #8B5CF6 (violet)
  - Background (Off-White): #F8FAFC
- [x] Tipografia: Inter + JetBrains Mono via Google Fonts
- [x] Layout base (sidebar navy + header) responsivo
- [x] Cards com rounded-xl, shadow-sm
- [x] Tabela clean com hover e cabecalho bg-slate-50
- [x] Loading states (wire:loading) e empty states
- [x] Favicon e titulo correto

---

## SPRINT 7 - Edicao de Classificacao Manual (Modal)
**Objetivo**: Substituir toggle cego por modal de confirmacao explicito.
**Status**: CONCLUIDA em 2026-02-19
**Commit**: `feat(sprint-7): modal de edicao de classificacao com indicador MANUAL inteligente`

### 7.1 - Modal de Confirmacao
**Prioridade**: ALTA

- [x] Livewire: `abrirModalEdicao(int $id)` — substitui alternarClassificacao()
- [x] Livewire: `salvarClassificacao(string $novaClassificacao)` — persiste se diferente
- [x] Livewire: `fecharModal()` — reseta propriedades do modal
- [x] Propriedades: `$modalAberto`, `$modalPrintLogId`, `$modalDocumento`, `$modalUsuario`, `$modalClassificacaoAtual`
- [x] Modal controlado por `$modalAberto` (wire:if) com backdrop blur
- [x] Dois botoes: [PESSOAL] amber | [ADMINISTRATIVO] violet
- [x] Registro em manual_overrides apenas quando classificacao muda
- [x] Indicador MANUAL inteligente: exibe apenas quando classificacao difere da automatica
- [x] 4 testes novos (TDD) + 47 anteriores continuam passando

---

## RESUMO DE PRIORIDADES

| Sprint | Modulo | Prioridade | Status |
|--------|--------|------------|--------|
| 1 | Setup Docker + Laravel | CRITICA | CONCLUIDA |
| 1 | Modelagem do Banco | CRITICA | CONCLUIDA |
| 1 | ClassifierService (TDD) | CRITICA | CONCLUIDA |
| 2 | Importacao CSV + Duplicatas | CRITICA | CONCLUIDA |
| 3 | Dashboard + KPIs | CRITICA | CONCLUIDA |
| 3 | Filtros e Tabela | CRITICA | CONCLUIDA |
| 4 | Override Manual | ALTA | CONCLUIDA |
| 5 | Exportacao Excel | ALTA | CONCLUIDA |
| 5 | Exportacao PDF | ALTA | CONCLUIDA |
| 6 | Design System | MEDIA | CONCLUIDA |
| 7 | Modal de Edicao de Classificacao | ALTA | CONCLUIDA |

---

## BACKLOG (Proximas Sprints)

| # | Ideia | Prioridade | Complexidade |
|---|-------|------------|--------------| 
| 1 | Queue/Job para CSVs grandes (>1000 linhas) com barra de progresso | ALTA | Media |
| 2 | Autocomplete de usuario nos filtros do dashboard | MEDIA | Baixa |
| 3 | Ordenacao por coluna na tabela do dashboard | MEDIA | Baixa |
| 4 | Autenticacao de usuarios (login/logout/perfis) | ALTA | Alta |
| 5 | Undo: reverter classificacao manual para original | MEDIA | Baixa |

---

## DEFINITION OF DONE (por sprint)

- [x] Todos os testes passando
- [x] Sem erros no `php artisan test`
- [x] Commit atomico em portugues sem emojis
- [x] Feature marcada como concluida neste ROADMAP

---

## BUGS CORRIGIDOS POS-SPRINTS

> Registro critico de bugs encontrados e corrigidos apos a entrega inicial das sprints.
> Esta tabela e FONTE DE VERDADE para evitar retrabalho e consulta rapida em bugs futuros.

| Data | Issue | Sintoma | Solucao | Commit |
|------|-------|---------|---------|--------|
| 2026-02-19 | Alpine.js duplicado no bundle | Erros de "already initialized" no console | Remover import manual do app.js — Livewire 4 ja gerencia o Alpine internamente | fix(frontend) |
| 2026-02-19 | DomPDF tempnam() PHP 8.4 | Warning fatal ao gerar PDF, rota /relatorio retorna 500 | `set_error_handler` no AppServiceProvider suprimindo E_WARNING antes do boot do DomPDF | fix(dompdf) |
| 2026-02-19 | pdf() tipo de retorno errado | PHPStan / runtime error no retorno do controller | Tipo de retorno corrigido para `Illuminate\Http\Response` no ExportController | fix(dompdf) |
| 2026-02-19 | Excel 500 com parametros null | Exportacao quebra quando filtros de URL estao vazios | Middleware `ConvertEmptyStringsToNull` converte strings vazias; substituir `get(k,d)` por `get(k) ?? d` | fix(export) |
| 2026-02-19 | modelo CSV no .gitignore | Arquivo de modelo nao disponivel para download | Movido de `storage/app/` (ignorado) para `resources/templates/` (rastreado) | feat(sprint-2-3) |
| 2026-02-19 | Download modelo interceptado pelo Livewire | Click no link nao iniciava download — Livewire capturava o evento | Adicionar atributo `download` no elemento `<a>` para sinalizar ao browser que e download direto | fix(import) |
| 2026-02-19 | /importar retorna 500 em producao | Rota de importacao quebra mesmo com DomPDF corrigido | Suprimir E_WARNING do tempnam() no boot do AppServiceProvider (solucao definitiva) | fix(dompdf) |

### Licoes criticas para proximas sprints
- **Livewire 4 + Alpine.js**: NUNCA importar Alpine manualmente no app.js. O Livewire 4 injeta e gerencia o Alpine.
- **DomPDF + PHP 8.4**: Sempre suprimir E_WARNING do tempnam() no AppServiceProvider ao usar dompdf.
- **ExportController com filtros**: Sempre usar `?? operador` ao receber parametros da query string — o middleware `ConvertEmptyStringsToNull` converte strings vazias para null antes da request chegar ao controller.
- **Downloads via Livewire**: Links de download devem ter atributo `download` para evitar interceptacao pelo router do Livewire.
- **resources/templates vs storage**: Arquivos de template para usuario final devem ficar em `resources/` (rastreado pelo git), nao em `storage/` (frequentemente no .gitignore).

---

## METRICAS FINAIS (Sprint 7)

| Metrica | Valor |
|---------|-------|
| Testes passando | 51 (47 + 4 novos na Sprint 7) |
| Sprints entregues | 7/7 (100%) |
| Bugs corrigidos | 7 |
| Stack | Laravel 12.52 + Livewire 4.1.4 + Tailwind CSS 4 + PHP 8.4 |
| Cobertura TDD | ClassifierService + CsvParserService + DuplicataService + Dashboard |

---

**Versao**: 3.0
**Status**: Ativo
**Atualizado**: 2026-02-19

# ROADMAP DE IMPLEMENTACAO - Catalogador de Impressoes GAP

> Fonte unica de verdade para o projeto check-print
> Stack: Laravel 12 + Livewire 4 + Alpine.js + Tailwind CSS 4 + Docker + MySQL 8.0
> Padrao: TDD obrigatorio | Commits em portugues | YAGNI | DRY
> Ultima atualizacao: 2026-02-19

---

## VISAO GERAL

### Objetivo
Sistema web para auditoria e gestao de custos de impressao corporativa.
Classifica automaticamente documentos entre "Administrativo" e "Pessoal"
com base em heuristicas de nomenclatura e gera relatorios gerenciais.

### Stack Definitiva (revisada)
- Framework: Laravel 12.52
- UI: Livewire 4.1.4 + Alpine.js (gerenciado pelo Livewire) + Tailwind CSS 4
- Banco: MySQL 8.0
- Container: Docker (PHP 8.4-FPM + Nginx + Redis)
- PHP: 8.4.18
- Servidor: Nginx 1.29.5
- Exports: maatwebsite/excel + barryvdh/laravel-dompdf

---

## SPRINT 1 - Fundacao e Infraestrutura
**Objetivo**: Ambiente funcionando, banco modelado, ClassifierService com TDD.
**Status**: CONCLUIDA

### 1.1 - Setup Docker + Laravel 12
- [x] Criar docker-compose.yml (PHP 8.4, MySQL 8.0, Nginx, Redis)
- [x] Instalar Livewire 4 (nao Livewire 3 — decisao revisada)
- [x] Instalar Tailwind CSS 4 via @tailwindcss/vite
- [x] Instalar Alpine.js (gerenciado internamente pelo Livewire 4)
- [x] Configurar .env com credenciais do banco
- [x] Verificar: `docker compose up` sobe tudo (porta 8081)
- [x] Instalar dependencias: maatwebsite/excel, barryvdh/laravel-dompdf, predis/predis

### 1.2 - Modelagem do Banco de Dados
- [x] Migration: print_logs (usuario, documento, data_impressao, paginas, custo, aplicativo, classificacao, classificacao_origem)
- [x] Migration: manual_overrides (print_log_id FK, classificacao_anterior, classificacao_nova, alterado_por)
- [x] Migration: indice unico composto (usuario, data_impressao, paginas) — adicionado no Sprint 2+
- [x] Model PrintLog com fillable, casts, hasMany ManualOverride
- [x] Model ManualOverride com relacionamento belongsTo
- [x] Factory PrintLogFactory para dados de teste

### 1.3 - ClassifierService (TDD - Core do Sistema)
- [x] Criar ClassifierServiceTest (RED) — 25 testes
- [x] Criar ClassifierService (GREEN) — keywords PESSOAL e fallback ADMINISTRATIVO
- [x] Testar: "Boleto Nubank" -> PESSOAL
- [x] Testar: "Ficha S1 Caetano" -> ADMINISTRATIVO
- [x] Testar: "Curriculo" -> PESSOAL
- [x] Testar: documento sem keyword -> ADMINISTRATIVO
- [x] classifyWithConfidence() retorna classificacao + confianca (ALTA/MEDIA)
- [x] 25 testes passando, cobertura completa das heuristicas

---

## SPRINT 2 - Importacao de CSV
**Objetivo**: Upload de CSV, validacao linha a linha, preview, deteccao de duplicatas.
**Status**: CONCLUIDA (escopo ampliado)

### 2.1 - Modulo de Upload e Parsing
- [x] CsvParserService com TDD (8 testes)
- [x] Suporte a separador ; e ,
- [x] Parsing de data formato DD/MM/YYYY HH:MM:SS
- [x] Parsing de custo (virgula -> ponto)
- [x] Validacao de colunas obrigatorias com validateHeaderDetail()
- [x] Parsing com validacao linha a linha parseWithValidation()
- [x] Metadata por linha: _linha, _valido, _erros
- [x] Livewire Component: ImportCsv com WithFileUploads

### 2.2 - Preview e Feedback
- [x] Preview das primeiras 10 linhas antes de confirmar
- [x] Contadores: Total / A importar / Duplicatas / Com erro
- [x] Painel colapsavel de erros por linha (em vermelho)
- [x] Painel colapsavel de duplicatas (em amber) com origem (banco/arquivo)
- [x] Badges OK/Dup./Erro na tabela de preview
- [x] Loading spinner durante analise do arquivo
- [x] Mensagem de sucesso com resumo completo

### 2.3 - Arquivo Modelo CSV
- [x] Criar resources/templates/modelo-impressoes.csv
- [x] Rota exportar/modelo-csv
- [x] Botao "Baixar Modelo" no banner da pagina de importacao

### 2.4 - Deteccao de Duplicatas (adicionado)
- [x] DuplicataService com TDD (10 testes)
- [x] Deteccao contra o banco: usuario + documento + data_impressao + paginas
- [x] Deteccao interna no CSV (duplicatas no proprio arquivo)
- [x] Indice unico no banco (usuario, data_impressao, paginas)
- [x] Importacao pula duplicatas, registra contagem separada
- [x] 47 testes passando no total

---

## SPRINT 3 - Dashboard e KPIs
**Objetivo**: Interface principal com filtros e cards de KPI.
**Status**: CONCLUIDA

### 3.1 - Cards de KPI
- [x] Total de impressoes + paginas
- [x] Custo total (R$)
- [x] Custo pessoal (R$) — destaque em amber
- [x] Custo administrativo (R$)
- [x] Percentual pessoal/total
- [x] Calculo reativo via SQL aggregate no Livewire Dashboard

### 3.2 - Filtros
- [x] Date Range (data_inicio, data_fim) com inputs
- [x] Presets: "Este Mes", "Ultimos 30 dias", "Este Ano", "Tudo"
- [x] Filtro por usuario (campo de busca)
- [x] Toggle: Todos / Apenas Pessoais / Apenas Administrativos
- [x] Campo de busca por nome do documento
- [x] Recalculo reativo ao alterar qualquer filtro (wire:model.live)

### 3.3 - Tabela Principal
- [x] Colunas: Data | Usuario | Documento | Paginas | Custo | Classificacao | Origem
- [x] Paginacao (15 por pagina) com WithPagination
- [x] Destaque visual: PESSOAL = amber, ADMINISTRATIVO = violet
- [x] Badge clicavel para override manual (alternarClassificacao)
- [x] Indicador visual: classificacao AUTO vs MANUAL

---

## SPRINT 4 - Override Manual e Persistencia
**Objetivo**: Usuarios podem alterar classificacoes; historico auditado.
**Status**: CONCLUIDA (integrado no Sprint 3)

### 4.1 - Override Manual
- [x] Livewire Action: alternarClassificacao(printLogId)
- [x] Persiste em manual_overrides com classificacao_anterior e classificacao_nova
- [x] Feedback visual imediato via Livewire (sem reload de pagina)
- [x] Indicador: badge "MANUAL" vs "AUTO" na coluna classificacao_origem

> Nota: Undo/reverter nao implementado (backlog futuro)

---

## SPRINT 5 - Exportacao (Excel + PDF)
**Objetivo**: Geracao de relatorios para contabilidade e gestao.
**Status**: CONCLUIDA

### 5.1 - Exportacao Excel (.xlsx)
- [x] PrintLogsExport com WithMultipleSheets
- [x] Aba 1 (Resumo): Por usuario — total paginas, custo total, custo pessoal, custo admin
- [x] Aba 2 (Detalhado): Todas as impressoes filtradas com classificacao final
- [x] Botao "Exportar Excel" no dashboard com filtros ativos
- [x] Respeita filtros: data_inicio, data_fim, usuario, tipo, documento
- [x] Correcao: parametros null via ConvertEmptyStringsToNull tratados com ??

### 5.2 - Exportacao PDF (Relatorio Executivo)
- [x] Template reports/executive.blade.php (CSS inline, compativel DomPDF)
- [x] Sumario Executivo: 4 KPI cards (total, custo total, pessoal, admin)
- [x] Top 5 ofensores: tabela usuario x custo pessoal x percentual
- [x] Botao "Exportar PDF" no dashboard
- [x] Correcao PHP 8.4: tempnam() warning suprimido via AppServiceProvider
- [x] Correcao: tipo de retorno pdf() corrigido para Illuminate\Http\Response

---

## SPRINT 6 - Design System e Polimento
**Objetivo**: Aplicar design "Minimalismo Corporativo" em toda a interface.
**Status**: CONCLUIDA (integrado desde o inicio)

### 6.1 - Design System
- [x] Cores customizadas via @theme no Tailwind CSS 4 (sem tailwind.config.js)
  - Primary Navy Blue: #1E3A8A
  - Pessoal Amber: #F59E0B
  - Administrativo Violet: #8B5CF6
  - Background Off-White: #F8FAFC
- [x] Tipografia: Inter (corpo) + JetBrains Mono (numeros financeiros) via Google Fonts
- [x] Layout base: sidebar navy + header branco + main content
- [x] Cards com rounded-xl, shadow-sm, border
- [x] Tabela clean com hover, cabecalho bg-slate-50
- [x] Loading states (wire:loading) na importacao e dashboard
- [x] Favicon: titulo "Catalogador de Impressoes GAP"

---

## BUGS CORRIGIDOS (pos-sprints)

| Data | Bug | Correcao |
|------|-----|----------|
| 2026-02-19 | Alpine duplicado — Livewire 4 ja gerencia Alpine | Removido import manual do app.js e alpinejs do package.json |
| 2026-02-19 | DomPDF tempnam() PHP 8.4 warning vira excecao | set_error_handler no AppServiceProvider suprime E_WARNING de tempnam() |
| 2026-02-19 | pdf() retornava StreamedResponse mas DomPDF retorna Response | Tipo de retorno corrigido para Illuminate\Http\Response |
| 2026-02-19 | ExportController parametros null (ConvertEmptyStringsToNull) | Substituido $request->get('k', 'd') por $request->get('k') ?? 'd' |
| 2026-02-19 | modelo-impressoes.csv em storage/ (no .gitignore) | Movido para resources/templates/, ExportController usa resource_path() |
| 2026-02-19 | Botao download modelo interceptado pelo Livewire | Adicionado atributo download no elemento <a> |

---

## RESUMO DE STATUS

| Sprint | Modulo | Prioridade | Status |
|--------|--------|------------|--------|
| 1 | Setup Docker + Laravel | CRITICA | CONCLUIDA |
| 1 | Modelagem do Banco | CRITICA | CONCLUIDA |
| 1 | ClassifierService (TDD) | CRITICA | CONCLUIDA |
| 2 | Importacao CSV com preview | CRITICA | CONCLUIDA |
| 2 | Deteccao de Duplicatas (TDD) | CRITICA | CONCLUIDA |
| 2 | Arquivo Modelo CSV para download | ALTA | CONCLUIDA |
| 3 | Dashboard + KPIs | CRITICA | CONCLUIDA |
| 3 | Filtros e Tabela | CRITICA | CONCLUIDA |
| 4 | Override Manual | ALTA | CONCLUIDA |
| 5 | Exportacao Excel | ALTA | CONCLUIDA |
| 5 | Exportacao PDF | ALTA | CONCLUIDA |
| 6 | Design System | MEDIA | CONCLUIDA |

---

---

## SPRINT 7 - Edicao de Classificacao Manual
**Objetivo**: Substituir toggle binario por modal de confirmacao com escolha explicita.
**Status**: EM EXECUCAO

### 7.1 - Modal de Edicao
- [ ] Testes: abre_modal, salva_nova_classificacao, nao_cria_override_sem_mudanca, cancela_modal
- [ ] Dashboard.php: propriedades do modal + abrirModalEdicao() + salvarClassificacao() + fecharModal()
- [ ] View: modal wire:if com overlay, badge colorido, botoes [PESSOAL] [ADMINISTRATIVO] [Cancelar]
- [ ] Badge da tabela agora abre modal (nao altera direto)
- [ ] Indicador visual: badge "MANUAL" visivel apenas quando classificacao difere da original (AUTO)

---

## BACKLOG FUTURO (nao implementado)

- [ ] Queue/Job para arquivos CSV grandes (>1000 linhas) com progresso
- [ ] Autocomplete de usuario nos filtros do dashboard
- [ ] Ordenacao por coluna na tabela do dashboard
- [ ] Autenticacao de usuarios (login/logout)

---

## METRICAS DO PROJETO

| Metrica | Valor |
|---------|-------|
| Testes unitarios/feature | 47 passando |
| PHP | 8.4.18 |
| Laravel | 12.52.0 |
| Livewire | 4.1.4 |
| Tailwind CSS | 4.x |
| Commits em portugues | Sim |
| TDD aplicado | ClassifierService, CsvParserService, DuplicataService |

---

## DEFINITION OF DONE (aplicado)

- [x] Todos os testes passando (47 testes)
- [x] Sem erros no `php artisan test`
- [x] Sem warnings criticos no console do browser
- [x] Commits atomicos em portugues sem emojis
- [x] Features marcadas como concluidas neste ROADMAP

---

**Versao**: 3.1
**Status**: Sprint 7 em execucao
**Atualizado**: 2026-02-19

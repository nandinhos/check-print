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

### Stack Definitiva
- Framework: Laravel 12
- UI: Livewire 3 + Alpine.js + Tailwind CSS
- Banco: MySQL 8.0
- Container: Docker (docker-compose)
- PHP: 8.4
- Servidor: Nginx

---

## SPRINT 1 - Fundacao e Infraestrutura
**Objetivo**: Ambiente funcionando, banco modelado, ClassifierService com TDD.
**Status**: Pendente

### 1.1 - Setup Docker + Laravel 12
**Prioridade**: CRITICA

- [ ] Criar docker-compose.yml (PHP 8.4, MySQL 8.0, Nginx)
- [ ] Inicializar projeto Laravel 12 via Composer
- [ ] Instalar Livewire 3
- [ ] Instalar e configurar Tailwind CSS + Alpine.js
- [ ] Configurar .env com credenciais do banco
- [ ] Verificar: `docker-compose up` sobe tudo

### 1.2 - Modelagem do Banco de Dados
**Prioridade**: CRITICA

Tabelas:
- `print_logs`: id, usuario, documento, data_impressao, paginas, custo, aplicativo, classificacao, created_at
- `manual_overrides`: id, print_log_id, classificacao_original, classificacao_nova, alterado_por, created_at

- [ ] Migration: print_logs
- [ ] Migration: manual_overrides
- [ ] Model PrintLog com fillable e casts
- [ ] Model ManualOverride com relacionamento
- [ ] Factory para PrintLog (dados de teste)

### 1.3 - ClassifierService (TDD - Core do Sistema)
**Prioridade**: CRITICA

Heuristicas PESSOAL (palavras-chave):
- boleto, nubank, fatura, banco, cpf, cnh, passaporte
- netflix, spotify, amazon, mercado livre, shopee
- curriculo, atestado medico, receita, exame
- cpf, rg, identidade

Heuristicas ADMINISTRATIVO (default):
- ficha, relatorio, oficio, memorando, portaria
- escala, boletim, ata, ordem, instrucao

Regras:
- Match case-insensitive no nome do documento
- Sem match = ADMINISTRATIVO (default seguro)
- Confianca: ALTA (keyword match) | MEDIA (default)

- [ ] Criar ClassifierServiceTest (RED)
- [ ] Criar ClassifierService (GREEN)
- [ ] Testar: "Boleto Nubank" -> PESSOAL
- [ ] Testar: "Ficha S1 Caetano" -> ADMINISTRATIVO
- [ ] Testar: "Curriculo" -> PESSOAL
- [ ] Testar: documento sem keyword -> ADMINISTRATIVO
- [ ] Cobertura 100% das heuristicas

---

## SPRINT 2 - Importacao de CSV
**Objetivo**: Upload de CSV, validacao, persistencia no banco.
**Status**: Pendente

### 2.1 - Modulo de Upload
**Prioridade**: CRITICA

Formato CSV esperado:
```
Data;Hora;Usuario;Documento;Paginas;Custo;Aplicativo
27/02/2025;13:32:24;1S Brasil;Ficha S1 Caetano;1;0.02;PDF
```

- [ ] Livewire Component: ImportCsvComponent
- [ ] Validacao de colunas obrigatorias (Data, Hora, Usuario, Documento, Paginas, Custo)
- [ ] Suporte a separador ; e ,
- [ ] Parsing de data formato DD/MM/YYYY
- [ ] Parsing de custo (virgula -> ponto)
- [ ] ClassifierService aplicado em cada linha
- [ ] Job/Queue para arquivos grandes (>1000 linhas)
- [ ] Barra de progresso no front

### 2.2 - Feedback de Importacao
**Prioridade**: ALTA

- [ ] Mensagem de sucesso: "X registros importados"
- [ ] Mensagem de erro com linha problematica
- [ ] Preview das primeiras 5 linhas antes de confirmar
- [ ] Validacao: arquivo vazio, colunas faltando, formato invalido

---

## SPRINT 3 - Dashboard e KPIs
**Objetivo**: Interface principal com filtros e cards de KPI.
**Status**: Pendente

### 3.1 - Cards de KPI
**Prioridade**: CRITICA

Cards necessarios:
- Total de impressoes (paginas)
- Custo total (R$)
- Custo pessoal (R$) - destaque em ambar
- Custo administrativo (R$)
- Percentual pessoal/total

- [ ] Livewire Component: DashboardComponent
- [ ] KpiCardComponent reutilizavel
- [ ] Calculo reativo ao filtro de datas

### 3.2 - Filtros (Date Range + Outros)
**Prioridade**: CRITICA

- [ ] Date Range Picker (calendario duplo inicio-fim)
- [ ] Presets: "Ultimos 30 dias", "Este Mes", "Ano Atual"
- [ ] Dropdown de usuario com busca (autocomplete)
- [ ] Toggle: Todos / Apenas Pessoais / Apenas Administrativos
- [ ] Campo de busca global por nome do documento
- [ ] Recalculo instantaneo ao alterar qualquer filtro

### 3.3 - Tabela Principal
**Prioridade**: CRITICA

Colunas: Data | Usuario | Documento | Paginas | Custo | Classificacao | Acoes

- [ ] Paginacao (15 por pagina)
- [ ] Ordenacao por coluna
- [ ] Destaque visual: PESSOAL = ambar, ADMINISTRATIVO = violeta/esmeralda
- [ ] Badge clicavel para override manual
- [ ] Confirmacao de alteracao manual

---

## SPRINT 4 - Override Manual e Persistencia
**Objetivo**: Usuarios podem alterar classificacoes; historico auditado.
**Status**: Pendente

### 4.1 - Override Manual
**Prioridade**: ALTA

- [ ] Livewire Action: toggleClassification(printLogId)
- [ ] Persistir em manual_overrides com user e timestamp
- [ ] Feedback visual imediato (sem reload)
- [ ] Undo: possibilidade de reverter para classificacao original
- [ ] Indicador visual: classificacao manual vs automatica

---

## SPRINT 5 - Exportacao (Excel + PDF)
**Objetivo**: Geracao de relatorios para contabilidade e gestao.
**Status**: Pendente

### 5.1 - Exportacao Excel (.xlsx)
**Prioridade**: ALTA

Biblioteca: Laravel Excel (Maatwebsite)

Aba 1 - Resumo:
- Por usuario: total paginas, total custo, custo pessoal, custo administrativo
- Totais gerais

Aba 2 - Detalhado:
- Todas as impressoes filtradas com classificacao final (incluindo overrides)

- [ ] Instalar maatwebsite/excel
- [ ] ResumoExport class
- [ ] DetalhadoExport class
- [ ] Botao "Exportar Excel" no dashboard
- [ ] Respeitar filtros ativos na exportacao

### 5.2 - Exportacao PDF (Relatorio Executivo)
**Prioridade**: ALTA

Biblioteca: barryvdh/laravel-dompdf

Estrutura do PDF:
- Cabecalho: Logo da organizacao + Titulo + Periodo analisado
- Sumario Executivo: Custo total | Custo pessoal | Percentual
- Top 5 Ofensores: tabela resumida (usuario, custo pessoal)
- Layout: limpo, minimalista, A4 vertical

- [ ] Instalar barryvdh/laravel-dompdf
- [ ] Blade template: reports/executive.blade.php
- [ ] AuditReportPdf class
- [ ] Botao "Exportar PDF" no dashboard
- [ ] Respeitar filtros ativos

---

## SPRINT 6 - Design System e Polimento
**Objetivo**: Aplicar design "Minimalismo Corporativo" em toda a interface.
**Status**: Pendente

### 6.1 - Design System
**Prioridade**: MEDIA

Paleta:
- Primary (Navy Blue): #1E3A8A
- Secondary (Slate): #64748B
- Background (Off-White): #F8FAFC
- Pessoal: #F59E0B (ambar)
- Administrativo: #8B5CF6 (violeta) ou #10B981 (esmeralda)
- Erro: #EF4444

Tipografia:
- Corpo: Inter / Roboto
- Numeros financeiros: JetBrains Mono / Roboto Mono

- [ ] Configurar cores customizadas no tailwind.config.js
- [ ] Layout base (sidebar + header) responsivo
- [ ] Cards com rounded-xl, shadow-sm
- [ ] Tabela clean com hover e cabecalho bg-slate-50
- [ ] Loading states e empty states
- [ ] Favicon e titulo correto

---

## RESUMO DE PRIORIDADES

| Sprint | Modulo | Prioridade | Status |
|--------|--------|------------|--------|
| 1 | Setup Docker + Laravel | CRITICA | Pendente |
| 1 | Modelagem do Banco | CRITICA | Pendente |
| 1 | ClassifierService (TDD) | CRITICA | Pendente |
| 2 | Importacao CSV | CRITICA | Pendente |
| 3 | Dashboard + KPIs | CRITICA | Pendente |
| 3 | Filtros e Tabela | CRITICA | Pendente |
| 4 | Override Manual | ALTA | Pendente |
| 5 | Exportacao Excel | ALTA | Pendente |
| 5 | Exportacao PDF | ALTA | Pendente |
| 6 | Design System | MEDIA | Pendente |

---

## DEFINITION OF DONE (por sprint)

- [ ] Todos os testes passando
- [ ] Sem erros no `php artisan test`
- [ ] Sem warnings no console do browser
- [ ] Commit atomico em portugues sem emojis
- [ ] Feature marcada como concluida neste ROADMAP

---

**Versao**: 2.0
**Status**: Ativo
**Atualizado**: 2026-02-18

# Product Requirements Document (PRD)
# Catalogador de Impressoes GAP

| Campo | Valor |
|-------|-------|
| Versao | 1.1 |
| Data | 18/02/2026 |
| Status | Aprovado |
| Autor | Product Team |
| Stack | Laravel 12 + TALL Stack + Docker + MySQL 8.0 |

---

## 1. Visao Geral do Produto

O **Catalogador de Impressoes GAP** e uma aplicacao web focada na auditoria e gestao de custos de impressao corporativa. O sistema processa logs de impressao (originarios de sistemas como NDDPrint), classifica automaticamente os documentos entre "Administrativo" e "Pessoal" baseando-se em heuristicas de nomenclatura, e gera relatorios gerenciais para tomada de decisao e ressarcimento de custos.

### 1.1 Objetivos Principais

| Objetivo | Descricao |
|----------|-----------|
| Automatizar a Classificacao | Reduzir o tempo manual de analise de relatorios de impressao |
| Auditoria de Custos | Identificar gastos com impressoes pessoais nao autorizadas |
| Visualizacao Clara | Oferecer dashboards intuitivos para gestores |
| Portabilidade | Importacao de dados brutos (CSV) e exportacao de relatorios (PDF/Excel) |

---

## 2. Design System & UX

**Filosofia**: "Minimalismo Corporativo". O design deve transmitir seriedade, limpeza e eficiencia. Usar cores apenas para indicar status ou acoes criticas.

### 2.1 Paleta de Cores

| Token | Hex | Uso |
|-------|-----|-----|
| Primary (Navy Blue) | `#1E3A8A` | Cabecalhos, botoes primarios, branding |
| Secondary (Slate) | `#64748B` | Textos secundarios, bordas sutis, icones inativos |
| Background (Off-White) | `#F8FAFC` | Fundo da aplicacao (evitar #FFFFFF em grandes areas) |
| Administrativo | `#8B5CF6` (Violeta) ou `#10B981` (Esmeralda) | Badge de status |
| Pessoal (Warning) | `#F59E0B` (Ambar) | Destaque para atencao, sem ser alarmista |
| Erro / Acao Destrutiva | `#EF4444` (Vermelho) | Confirmacoes destrutivas |

### 2.2 Tipografia

| Uso | Familia | Peso |
|-----|---------|------|
| Corpo de texto | Inter ou Roboto | Regular (400) |
| Labels e cabecalhos de tabela | Inter ou Roboto | Medium (500) |
| Titulos principais e valores financeiros | Inter ou Roboto | Bold (700) |
| Dados numericos em tabelas | JetBrains Mono ou Roboto Mono | Regular (400) |

> Fontes monoespaco garantem alinhamento decimal perfeito em tabelas financeiras.

### 2.3 Componentes de UI

**Cards**
- Bordas arredondadas: `rounded-xl`
- Sombra suave: `shadow-sm`
- Fundo branco

**Botoes**
- Exportar: Outline ou Ghost button com icone
- Acao Principal: Solid fill com `rounded-lg`

**Tabelas**
- Linhas divisorias sutis
- Cabecalho com fundo `bg-slate-50`
- Efeito hover nas linhas

---

## 3. Especificacoes Funcionais

### 3.1 Modulo de Importacao (CSV)

O sistema aceita arquivos estruturados no formato CSV (separador `;` ou `,`).

**Input**: Botao de upload com suporte a drag-and-drop "Arraste ou Clique para enviar CSV".

**Validacao**: O sistema verifica se o cabecalho contem as colunas minimas:
- `Data`, `Hora`, `Usuario`, `Documento`, `Paginas`, `Custo`

**Feedback**:
- Barra de progresso durante o processamento de grandes arquivos
- Mensagem de sucesso: "X registros importados"
- Mensagem de erro com identificacao da linha problematica

### 3.2 Seletor de Datas (Date Range Picker)

**Funcionalidade**: Filtrar o dataset importado por periodo.

**Componente**: Calendario duplo (Inicio - Fim).

**Comportamento**: Ao alterar a data, todos os cards de KPI e a tabela sao recalculados instantaneamente.

**Presets rapidos**:
- "Ultimos 30 dias"
- "Este Mes"
- "Ano Atual"

### 3.3 Motor de Classificacao (Core)

**Logica**: Algoritmo de palavras-chave (keywords) aplicado ao nome do documento.

| Categoria | Exemplos de Keywords |
|-----------|---------------------|
| PESSOAL | boleto, nubank, fatura, banco, cpf, cnh, passaporte, netflix, spotify, curriculo, atestado, receita, exame |
| ADMINISTRATIVO (default) | ficha, relatorio, oficio, memorando, portaria, escala, boletim, ata, ordem |

**Regras**:
- Match case-insensitive no nome do documento
- Sem match = ADMINISTRATIVO (default seguro)

**Override Manual**: O usuario pode clicar na classificacao na tabela e alternar manualmente entre PESSOAL/ADMINISTRATIVO. Essa alteracao e persistida no banco de dados com registro de auditoria.

### 3.4 Filtros Avancados

| Filtro | Tipo | Descricao |
|--------|------|-----------|
| Por Usuario | Dropdown com busca | Autocomplete para selecionar um militar/funcionario especifico |
| Por Tipo | Toggle | "Todos" / "Apenas Pessoais" / "Apenas Administrativos" |
| Busca Global | Campo de texto | Filtra por nome do documento |

---

## 4. Modulo de Exportacao (Reporting)

### 4.1 Exportacao para Excel (.xlsx)

Gera arquivo estruturado para contabilidade.

**Aba 1 - Resumo**:
- Totais por usuario: total paginas, total custo, custo pessoal, custo administrativo
- Linha de totais gerais

**Aba 2 - Detalhado**:
- Lista completa de todas as impressoes filtradas na tela atual
- Classificacao final (incluindo overrides manuais do operador)

### 4.2 Exportacao para PDF (Relatorio Executivo)

PDF com qualidade de impressao, pronto para anexar a oficios ou memorandos.

**Estrutura**:
- **Cabecalho**: Logotipo da organizacao + Titulo "Relatorio de Auditoria de Impressao" + Periodo analisado
- **Sumario Executivo**: Cards com Custo Total e Custo "Pessoal" identificado
- **Top 5 Ofensores**: Tabela resumida dos 5 usuarios com maior custo em impressoes pessoais
- **Layout**: Limpo, minimalista, A4 vertical, fundo branco, fontes escuras

---

## 5. Requisitos Nao-Funcionais

| Requisito | Detalhes |
|-----------|----------|
| Performance | Processamento de CSV com ate 10.000 linhas sem degradacao (uso de Jobs/Queue no Laravel) |
| Arquitetura | Server-side com Laravel 12. Dados persistidos em MySQL 8.0 |
| Compatibilidade | Chrome, Edge e Firefox (Desktop). Mobile nao e prioridade |
| Auditoria | Todas as alteracoes manuais de classificacao sao logadas com usuario e timestamp |
| Isolamento | Ambiente em Docker (PHP 8.4 + MySQL 8.0 + Nginx) |

---

## 6. Estrutura de Dados (Modelo CSV Esperado)

Separador aceito: `;` ou `,`

```
Data;Hora;Usuario;Documento;Paginas;Custo;Aplicativo
27/02/2025;13:32:24;1S Brasil;Ficha S1 Caetano;1;0.02;PDF
04/08/2025;14:15:00;Ten Franco;Boleto Nubank;1;0.02;Chrome
```

### Modelo de Banco de Dados

**print_logs**

| Coluna | Tipo | Descricao |
|--------|------|-----------|
| id | bigint PK | |
| usuario | string | Nome do militar/funcionario |
| documento | string | Nome do documento impresso |
| data_impressao | datetime | Data e hora da impressao |
| paginas | integer | Numero de paginas |
| custo | decimal(8,2) | Custo em reais |
| aplicativo | string | Aplicativo de origem (PDF, Chrome, etc) |
| classificacao | enum(PESSOAL, ADMINISTRATIVO) | Resultado da classificacao |
| classificacao_origem | enum(AUTO, MANUAL) | Origem da classificacao |
| created_at | timestamp | |

**manual_overrides**

| Coluna | Tipo | Descricao |
|--------|------|-----------|
| id | bigint PK | |
| print_log_id | bigint FK | Referencia ao registro de impressao |
| classificacao_anterior | enum | Valor antes da alteracao |
| classificacao_nova | enum | Valor depois da alteracao |
| alterado_por | string | Identificacao do operador |
| created_at | timestamp | |

---

## 7. Criterios de Aceite (Definition of Done)

- [ ] Usuario consegue carregar um CSV via upload
- [ ] Dashboard exibe numeros corretos baseados no CSV importado
- [ ] Classificacao automatica identifica "Boleto" como PESSOAL
- [ ] Classificacao automatica identifica "Ficha S1" como ADMINISTRATIVO
- [ ] Usuario consegue alterar classificacao manualmente
- [ ] Alteracao manual e persistida e auditada
- [ ] Filtro de datas recalcula KPIs instantaneamente
- [ ] Exportacao Excel gera duas abas estruturadas
- [ ] Exportacao PDF gera relatorio executivo com top 5 ofensores
- [ ] `docker-compose up` sobe o ambiente completo sem erros

---

*PRD v1.1 - Atualizado em 18/02/2026*
*Decisao de arquitetura: Laravel 12 + MySQL + Docker (server-side)*

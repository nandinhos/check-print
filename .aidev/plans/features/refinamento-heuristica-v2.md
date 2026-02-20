# Feature: Refinamento da Heurística de Classificação (v2)

**Status**: CONCLUIDA
**Prioridade**: ALTA
**Sprint**: 2
**Concluida em**: 2026-02-19

## Descrição
Refinar o motor de classificação para reduzir falsos positivos (como laudos técnicos sendo marcados como pessoais) e garantir que documentos de projetos estratégicos e fiscais sejam marcados corretamente como ADMINISTRATIVO.

## Contexto
Implementada lógica de "Veto Administrativo" prioritário e normalização de caracteres (acentos), garantindo que siglas de projetos (KC-390, LINK-BR2) e termos patrimoniais (Notebook, Cautela) não sejam classificados como pessoais indevidamente.

## Requisitos de Negócio
- [x] Documentos contendo siglas de aeronaves (KC-390, AM-X, etc.) devem ser SEMPRE ADMINISTRATIVOS.
- [x] "Laudo" sozinho não deve ser suficiente para marcar como PESSOAL; deve-se preferir "Laudo Médico" ou "Laudo de Exame".
- [x] Termos fiscais (NF, DANFE, Nota Fiscal, Guia de Remessa) devem ser explicitamente ADMINISTRATIVOS.
- [x] Implementar lógica de "Veto Administrativo": se um termo administrativo forte for encontrado, a classificação PESSOAL é descartada.

## Tarefas Técnicas

### 1. Preparação de Testes (TDD - RED)
- [x] Atualizar `ClassifierServiceTest.php` com novos casos:
    - [x] `test_laudo_tecnico_e_administrativo`
    - [x] `test_projeto_aeronave_e_classificado_como_administrativo` (KC-390, F5-BR, etc.)
    - [x] `test_documento_fiscal_e_classificado_como_administrativo` (NF, DANFE, Guia)
    - [x] `test_veto_administrativo_vence_termo_pessoal` (ex: "Fatura Projeto KC-390")

### 2. Implementação (GREEN)
- [x] Criar constante `KEYWORDS_ADMINISTRATIVO_FORTE` no `ClassifierService`.
- [x] Modificar `classifyWithConfidence` para verificar primeiro os vetos administrativos.
- [x] Ajustar `KEYWORDS_PESSOAL` (remover "laudo", adicionar variações).

### 3. Refatoração e Polimento (REFACTOR)
- [x] Organizar as keywords por categorias (Finanças, Saúde, Projetos, Fiscal).
- [x] Melhorar os níveis de confiança (MUITO ALTA para match administrativo explícito).
- [x] Adicionar normalização de acentos (`removerAcentos`) para aumentar a precisão dos matches.

## Critérios de Aceite
- [x] Todos os novos testes passando (32/32).
- [x] Dry Run validou 61 reclassificações corretas no banco real.
- [x] Projetos KC-390 e LINK-BR2 agora são 100% ADMINISTRATIVOS.

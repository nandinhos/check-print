# Feature: Edicao de Classificacao Manual

**Status**: CONCLUIDA
**Prioridade**: ALTA
**Sprint**: 7
**Iniciada em**: 2026-02-19

## Descricao

Substituir o toggle binario (PESSOAL <-> ADMINISTRATIVO) por um modal de confirmacao
onde o operador ve a classificacao atual, escolhe explicitamente a nova classificacao
e confirma a acao. Evita cliques acidentais e torna a intencao do operador explicita.

## Contexto Atual

`alternarClassificacao(int $id)` no `Dashboard.php` faz um toggle cego:
- Nao ha confirmacao
- O operador nao escolhe — apenas alterna para o oposto
- Um clique errado exige outro clique para desfazer

## Requisitos de Negocio

- [x] Ao clicar no badge de classificacao, abre um modal (nao altera imediatamente)
- [x] O modal mostra: nome do documento, usuario, classificacao atual
- [x] O modal oferece botoes [PESSOAL] [ADMINISTRATIVO] para escolha explicita
- [x] Confirmar salva a nova classificacao (mesmo que igual a atual — sem alteracao)
- [x] Cancelar fecha o modal sem nenhuma alteracao
- [x] Registro em manual_overrides deve ser criado apenas quando a classificacao muda

## Tarefas Tecnicas

### 1. Livewire Dashboard — novo metodo e propriedades (TDD)
- [x] Criar teste: `abre_modal_ao_clicar_badge` (verifica propriedades do modal)
- [x] Criar teste: `salva_nova_classificacao_via_modal`
- [x] Criar teste: `nao_cria_override_quando_classificacao_nao_muda`
- [x] Criar teste: `cancela_modal_sem_alterar_registro`
- [x] Adicionar propriedades: `$modalAberto`, `$modalPrintLogId`, `$modalDocumento`, `$modalUsuario`, `$modalClassificacaoAtual`
- [x] Renomear `alternarClassificacao()` para `abrirModalEdicao(int $id)` — preenche propriedades e abre modal
- [x] Novo metodo `salvarClassificacao(string $novaClassificacao)` — persiste se diferente, fecha modal
- [x] Novo metodo `fecharModal()` — reseta propriedades

### 2. View — modal Livewire (sem Alpine.js)
- [x] Modal controlado por `$modalAberto` (wire:if) — overlay com backdrop blur
- [x] Exibir: documento, usuario, classificacao atual (badge colorido)
- [x] Dois botoes de selecao: [PESSOAL] amber | [ADMINISTRATIVO] violet
- [x] Botao [Cancelar] (wire:click="fecharModal")
- [x] Badge da tabela passa de `wire:click="alternarClassificacao"` para `wire:click="abrirModalEdicao"`

## Criterios de Aceite

- [x] Clicar no badge abre modal sem alterar o registro
- [x] Modal mostra informacoes corretas do registro clicado
- [x] Selecionar a mesma classificacao atual: fecha modal, nao cria override
- [x] Selecionar classificacao diferente: atualiza badge, cria override, fecha modal
- [x] Cancelar: fecha modal, registro inalterado
- [x] Todos os testes novos passando
- [x] Testes anteriores (47) continuam passando

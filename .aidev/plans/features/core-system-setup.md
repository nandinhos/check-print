# Feature: Core System Setup

**Status**: 🟡 Pendente
**Prioridade**: 🔴 CRÍTICA
**Sprint**: 1

## Descrição
Configuração inicial do ambiente de desenvolvimento usando Docker, Laravel 12 e TALL Stack, incluindo a implementação do motor de classificação de documentos (heurísticas GAC-PAC).

## Requisitos de Negócio
- [ ] O sistema deve rodar isolado em Docker.
- [ ] Deve utilizar Laravel 12.
- [ ] A interface deve seguir o design "Minimalismo Corporativo" com Tailwind.

## Tarefas Técnicas

### 1. Setup do Ambiente (Docker/Laravel)
- [ ] Inicializar projeto Laravel 12.
- [ ] Configurar `docker-compose.yml` (PHP 8.4, MySQL 8.0, Nginx).
- [ ] Instalar e configurar TALL Stack (Livewire, Tailwind CSS, Alpine.js).
- [ ] Configurar conexão com Banco de Dados.

### 2. Motor de Classificação (Core)
- [ ] Criar `ClassifierService`.
- [ ] Implementar heurísticas para categoria PESSOAL.
- [ ] Implementar heurísticas para categoria ADMINISTRATIVO.
- [ ] Criar testes unitários para validar a classificação de termos (TDD).

### 3. Persistência e Modelagem
- [ ] Criar Migration para `print_logs`.
- [ ] Criar Migration para `manual_overrides`.
- [ ] Implementar Models e Factories.

## Critérios de Aceite
- [ ] `docker-compose up` sobe o ambiente completo.
- [ ] Testes do `ClassifierService` passam com 100% de cobertura nos termos fornecidos.
- [ ] Home page do Laravel está visível e com Tailwind configurado.

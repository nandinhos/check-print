# Feature: Core System Setup

**Status**: CONCLUIDA
**Prioridade**: CRITICA
**Sprint**: 1
**Concluida em**: 2026-02-18

## Descricao
Configuracao inicial do ambiente de desenvolvimento usando Docker, Laravel 12 e TALL Stack,
incluindo a implementacao do motor de classificacao de documentos (heuristicas GAC-PAC).

**Decisao de arquitetura**: Livewire 4 (nao Livewire 3) e PHP 8.4 via Docker
(nao PHP 8.3 do host). Alpine.js gerenciado internamente pelo Livewire 4.

## Requisitos de Negocio
- [x] O sistema roda isolado em Docker (PHP 8.4-FPM + Nginx + MySQL + Redis)
- [x] Utiliza Laravel 12.52
- [x] A interface segue o design "Minimalismo Corporativo" com Tailwind CSS 4

## Tarefas Tecnicas

### 1. Setup do Ambiente (Docker/Laravel)
- [x] Configurar `docker-compose.yml` (PHP 8.4, MySQL 8.0, Nginx 1.29.5, Redis)
- [x] Configurar `docker/php/Dockerfile` (PHP 8.4-FPM + Node.js 20 + Composer)
- [x] Configurar `docker/nginx/default.conf`
- [x] Instalar Livewire 4 (^4.0, instalado v4.1.4)
- [x] Instalar Tailwind CSS 4 via @tailwindcss/vite (sem tailwind.config.js)
- [x] Configurar Alpine.js — gerenciado pelo Livewire 4 (sem import manual)
- [x] Configurar .env com DB_HOST=db, REDIS_HOST=redis, APP_URL=http://localhost:8081
- [x] Instalar dependencias: maatwebsite/excel, barryvdh/laravel-dompdf, predis/predis
- [x] Verificar: `docker compose up` sobe tudo na porta 8081

### 2. Motor de Classificacao (Core) — TDD
- [x] Criar ClassifierServiceTest (RED) — 25 testes
- [x] Criar ClassifierService (GREEN) — keywords PESSOAL + fallback ADMINISTRATIVO
- [x] Testar: "Boleto Nubank" -> PESSOAL
- [x] Testar: "Ficha S1 Caetano" -> ADMINISTRATIVO
- [x] Testar: "Curriculo" -> PESSOAL
- [x] classifyWithConfidence() — confianca ALTA (keyword match) | MEDIA (default)
- [x] 25 testes passando

### 3. Persistencia e Modelagem
- [x] Migration: print_logs
- [x] Migration: manual_overrides (com FK cascadeOnDelete)
- [x] Model PrintLog com fillable, casts, hasMany ManualOverride
- [x] Model ManualOverride com belongsTo PrintLog
- [x] Factory PrintLogFactory com faker

## Criterios de Aceite
- [x] `docker compose up` sobe o ambiente completo
- [x] Testes do ClassifierService passam (25/25)
- [x] Dashboard visivel com Tailwind CSS 4 configurado
- [x] Sidebar + layout base funcionando

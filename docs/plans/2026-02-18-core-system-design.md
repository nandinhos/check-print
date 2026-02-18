# Core System Design - Catalogador de Impressões GAP

**Data**: 2026-02-18
**Autor**: AI Dev Superpowers
**Status**: Aprovado

## Declaração do Problema
Gestão e auditoria de custos de impressão corporativa para a organização GAC-PAC. O sistema precisa automatizar a classificação de logs brutos de impressão entre "Pessoal" e "Administrativo" usando heurísticas de nomenclatura.

## Solução Proposta
Abordagem Server-Side utilizando **Laravel 12** com **TALL Stack** (Tailwind, Alpine.js, Laravel, Livewire). O processamento será feito via PHP para garantir persistência e robustez na geração de relatórios, mantendo uma interface reativa.

## Detalhes Técnicos

### Modelo de Dados
- **Users**: Gestores do sistema.
- **PrintLogs**: Registros brutos importados (Usuário, Documento, Data, Páginas, Custo).
- **Classifications**: Tabela de referência para as heurísticas (Palavra-chave, Categoria, Confiança).
- **AuditActions**: Log de alterações manuais feitas pelos usuários.

### API/Interface
- **Dashboard**: Componentes Livewire para filtros em tempo real e KPIs.
- **ImportView**: Upload de CSV com validação de schema.
- **ExportService**: Drivers para PDF e Excel.

### Stack e Bibliotecas
- **Framework**: Laravel 12
- **UI**: Livewire + Alpine.js + Tailwind CSS
- **Banco de Dados**: MySQL 8.0
- **Container**: Docker (Laravel Sail ou Custom Dockerfile)

## Próximos Passos
1. Configurar ambiente Docker com Laravel 12.
2. Implementar `ClassifierService` com as heurísticas fornecidas.
3. Criar módulo de importação de CSV.

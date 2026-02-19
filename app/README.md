# Catalogador de Impressoes GAP

Sistema web para auditoria e gestao de custos de impressao corporativa, desenvolvido com Laravel 12 + TALL Stack + Docker.

## Sobre o Projeto

O **Catalogador de Impressoes GAP** processa logs de impressao oriundos de sistemas como NDDPrint, classifica automaticamente os documentos entre **Administrativo** e **Pessoal** com base em heuristicas de nomenclatura, e gera relatorios gerenciais para tomada de decisao e ressarcimento de custos.

## Stack

| Componente | Tecnologia |
|------------|------------|
| Backend | Laravel 12 (PHP 8.4) |
| Frontend | Livewire 4 + Alpine.js + Tailwind CSS |
| Banco de Dados | MySQL 8.0 |
| Infraestrutura | Docker + Nginx |
| Exportacao | DomPDF (PDF) + PhpSpreadsheet/Maatwebsite Excel (XLSX) |
| Testes | PHPUnit + Feature Tests (TDD) |

## Funcionalidades

- **Importacao de CSV** — Upload com validacao por linha, deteccao e bloqueio de duplicatas, feedback de progresso
- **Motor de Classificacao** — Algoritmo de palavras-chave (case-insensitive) para categorizar impressoes como PESSOAL ou ADMINISTRATIVO
- **Override Manual** — Edicao de classificacao diretamente na tabela, com registro de auditoria
- **Filtros Avancados** — Por usuario, por tipo (Pessoal/Administrativo) e busca global por documento
- **Seletor de Datas** — Filtro por periodo com presets rapidos (Ultimos 30 dias, Este Mes, Ano Atual)
- **Dashboard com KPIs** — Cards de resumo com custo total, custo pessoal, custo administrativo e totais de paginas
- **Exportacao PDF** — Relatorio executivo com top 5 usuarios por custo pessoal, pronto para anexar a oficios
- **Exportacao Excel** — Duas abas: resumo por usuario e detalhamento completo das impressoes

## Requisitos

- Docker e Docker Compose
- Make (opcional, para atalhos)

## Instalacao e Execucao

```bash
# Clonar o repositorio
git clone <url-do-repositorio>
cd check-print

# Subir o ambiente Docker
docker-compose up -d

# Instalar dependencias e configurar
docker-compose exec app composer install
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate

# Acessar a aplicacao
# http://localhost
```

## Formato do CSV Aceito

Separador: `;` ou `,`

```
Data;Hora;Usuario;Documento;Paginas;Custo;Aplicativo
27/02/2025;13:32:24;1S Brasil;Ficha S1 Caetano;1;0.02;PDF
04/08/2025;14:15:00;Ten Franco;Boleto Nubank;1;0.02;Chrome
```

Colunas obrigatorias: `Data`, `Hora`, `Usuario`, `Documento`, `Paginas`, `Custo`

## Classificacao Automatica

| Categoria | Exemplos de palavras-chave |
|-----------|---------------------------|
| PESSOAL | boleto, nubank, fatura, banco, cpf, cnh, passaporte, netflix, curriculo, atestado, receita, exame |
| ADMINISTRATIVO (default) | ficha, relatorio, oficio, memorando, portaria, escala, boletim, ata, ordem |

Documentos sem correspondencia sao classificados como **ADMINISTRATIVO** (regra segura).

## Estrutura do Projeto

```
app/
├── app/
│   ├── Livewire/
│   │   ├── Dashboard.php      # Dashboard principal com KPIs e tabela
│   │   └── ImportCsv.php      # Componente de importacao de CSV
│   ├── Models/
│   │   ├── PrintLog.php       # Registro de impressao
│   │   └── ManualOverride.php # Auditoria de alteracoes manuais
│   └── Services/
│       ├── ClassifierService.php  # Motor de classificacao por keywords
│       ├── CsvParserService.php   # Parser e validador de CSV
│       └── DuplicataService.php   # Deteccao de registros duplicados
├── database/migrations/       # Migrations do banco de dados
├── resources/views/livewire/  # Templates Blade dos componentes
└── tests/Feature/             # Testes de feature (TDD)
```

## Testes

```bash
docker-compose exec app php artisan test
```

## Versao

**v4.4.2** — Veja o arquivo `VERSION` para o historico de versoes.

## Licenca

Software proprietario — Uso restrito ao GAC-PAC e seus membros.

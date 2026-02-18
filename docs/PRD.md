Product Requirements Document (PRD)

Catalogador de Impressões GAP

| Versão | Data | Status | Autor |
| 1.0 | 18/02/2026 | Draft | Product Team |

1. Visão Geral do Produto

O Catalogador de Impressões GAP é uma aplicação web focada na auditoria e gestão de custos de impressão corporativa. O sistema processa logs de impressão (originários de sistemas como NDDPrint), classifica automaticamente os documentos entre "Administrativo" e "Pessoal" baseando-se em heurísticas de nomenclatura, e gera relatórios gerenciais para tomada de decisão e ressarcimento de custos.

1.1 Objetivos Principais

Automatizar a Classificação: Reduzir o tempo manual de análise de relatórios de impressão.

Auditoria de Custos: Identificar gastos com impressões pessoais não autorizadas.

Visualização Clara: Oferecer dashboards intuitivos para gestores.

Portabilidade: Permitir importação de dados brutos (CSV) e exportação de relatórios finais (PDF/Excel).

2. Design System & UX

Filosofia: "Minimalismo Corporativo". O design deve transmitir seriedade, limpeza e eficiência. Evitar excesso de cores; usar cores apenas para indicar status ou ações críticas.

2.1 Paleta de Cores

Primary (Navy Blue): #1E3A8A - Cabeçalhos, botões primários, branding.

Secondary (Slate): #64748B - Textos secundários, bordas sutis, ícones inativos.

Background (Off-White): #F8FAFC - Fundo da aplicação para evitar fadiga ocular (evitar branco absoluto #FFFFFF em grandes áreas).

Semantic Colors:

Administrativo (Success/Neutral): #8B5CF6 (Violeta suave) ou #10B981 (Esmeralda).

Pessoal (Warning): #F59E0B (Âmbar) - Destaque para atenção, mas sem ser alarmista como vermelho.

Erro/Ação Destrutiva: #EF4444 (Vermelho).

2.2 Tipografia

Família Principal: Inter ou Roboto (Sans-serif).

Pesos:

Regular (400): Corpo de texto.

Medium (500): Labels e cabeçalhos de tabela.

Bold (700): Números financeiros e Títulos principais.

Dados Numéricos: JetBrains Mono ou Roboto Mono para tabelas financeiras (alinhamento decimal perfeito).

2.3 Componentes de UI

Cards: Bordas arredondadas (rounded-xl), sombra suave (shadow-sm), fundo branco.

Botões:

Exportar: Outline ou Ghost button com ícone.

Ação Principal: Solid fill com cantos levemente arredondados (rounded-lg).

Tabelas: Design "Clean". Linhas divisórias sutis, cabeçalho com fundo cinza muito claro (bg-slate-50), efeito hover nas linhas.

3. Especificações Funcionais

3.1 Módulo de Importação (CSV)

O sistema deve abandonar o processamento de texto cru e aceitar arquivos estruturados.

Input: Botão de upload "Arraste ou Clique para enviar CSV".

Validação: O sistema deve verificar se o cabeçalho do CSV contém as colunas mínimas: Usuário, Nome do Documento, Data, Páginas, Custo.

Feedback: Barra de progresso durante o processamento de grandes arquivos e mensagem de sucesso/erro.

3.2 Seletor de Datas (Date Range Picker)

Funcionalidade: Permitir filtrar o dataset importado por período.

Componente: Calendário duplo (Início - Fim).

Comportamento: Ao alterar a data, todos os cards de KPI e a tabela devem ser recalculados instantaneamente.

Presets: Botões rápidos para "Últimos 30 dias", "Este Mês", "Ano Atual".

3.3 Motor de Classificação (Core)

Lógica: Manter o algoritmo de palavras-chave (keywords) definido no protótipo.

Override Manual: O usuário deve poder clicar na classificação na tabela e alternar manualmente entre PESSOAL/ADMINISTRATIVO. Essa alteração deve persistir na sessão atual.

3.4 Filtros Avançados

Por Usuário: Dropdown com busca (autocomplete) para selecionar um militar/funcionário específico.

Por Tipo: Checkbox ou Toggle para ver "Apenas Pessoais", "Apenas Administrativos" ou "Todos".

Busca Global: Campo de texto que filtra por nome do documento.

4. Módulo de Exportação (Reporting)

4.1 Exportação para Excel (.xlsx)

Deve gerar um arquivo estruturado para contabilidade.

Aba 1 (Resumo): Totais por usuário, total gasto, total pessoal vs administrativo.

Aba 2 (Detalhado): Lista completa de todas as impressões filtradas na tela atual, com suas respectivas classificações (incluindo as alterações manuais feitas pelo operador).

4.2 Exportação para PDF (Relatório Executivo)

O PDF deve ser gerado com qualidade de impressão, pronto para ser anexado a ofícios ou memorandos.

Cabeçalho: Logotipo da organização, Título "Relatório de Auditoria de Impressão", Período analisado.

Sumário Executivo: Cards visuais com Custo Total e Custo "Pessoal" identificado.

Top Ofensores: Tabela resumida dos 5 usuários com maior custo em impressões pessoais.

Layout: Limpo, minimalista, fundo branco, fontes escuras.

5. Requisitos Não-Funcionais

Performance: O processamento de um CSV com até 10.000 linhas não deve travar o navegador (uso de Web Workers se necessário).

Privacidade: Todo o processamento deve ser Client-Side (no navegador). Nenhum dado de impressão deve ser enviado para servidores externos.

Compatibilidade: Funcionar perfeitamente em Chrome, Edge e Firefox (Desktop). Mobile não é prioridade (ferramenta administrativa).

6. Estrutura de Dados (Modelo CSV Esperado)

Para garantir a importação, o CSV deve seguir o padrão (separador ; ou ,):

Data;Hora;Usuario;Documento;Paginas;Custo;Aplicativo
27/02/2025;13:32:24;1S Brasil;Ficha S1 Caetano;1;0.02;PDF
04/08/2025;14:15:00;Ten Franco;Boleto Nubank;1;0.02;Chrome
...



7. Critérios de Aceite (Definition of Done)

* Usuário consegue carregar um CSV.

* Dashboard exibe números corretos baseados no CSV.

* Classificação automática identifica "Boleto" como Pessoal.

* Usuário consegue alterar classificação manualmente.

* Exportação PDF gera arquivo legível e bem formatado.

* Exportação Excel contém dados brutos editáveis.
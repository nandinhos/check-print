# Generic Stack Rules

## Core Principles
These rules apply to ALL projects regardless of stack.

## 1. TDD is Mandatory
- **RED**: Write failing test first
- **GREEN**: Minimal code to pass
- **REFACTOR**: Improve without breaking

## 2. YAGNI (You Aren't Gonna Need It)
- Don't add functionality until needed
- Avoid premature optimization  
- Build only what's requested

## 3. DRY (Don't Repeat Yourself)
- Each piece of knowledge has single source
- Extract when repeated 3+ times
- But don't over-abstract early

## 4. Clean Code
- Meaningful names
- Small functions (≤20 lines)
- Single responsibility
- Clear separation of concerns

## 5. Error Handling
- Fail fast
- Clear error messages
- Proper exception types
- Log appropriately

## 6. Controle de Versão
- Commits atômicos
- Mensagens descritivas em PORTUGUÊS (Brasil)
- Branch por feature
- Review antes de merge

## Formato de Commit

**REGRAS OBRIGATÓRIAS**:
- Idioma: PORTUGUÊS Brasil (obrigatório)
- Emojis: PROIBIDOS
- Co-autoria: PROIBIDA (sem Co-Authored-By)
- Estilo: Commit Conventions

### Padrão
```
(Escopo): Descrição detalhada do que foi feito
```

### Exemplos Corretos
```
(Auth): Adiciona autenticação JWT para rotas de API
(API): Corrige validação de e-mail no cadastro de usuários
(Utils): Extrai função de formatação de moeda para helper global
```

### NÃO FAÇA
```
# ERRADO - emoji
(Auth): ✨ Adiciona autenticação

# ERRADO - inglês
(Auth): Add authentication

# ERRADO - co-autoria
(Auth): Adiciona auth

Co-Authored-By: Claude <noreply@anthropic.com>
```

## File Organization
- Group by feature, not type
- Clear naming conventions
- Consistent structure
- Separate config from code

## Documentation
- README for every project
- Inline comments for "why"
- API documentation
- Architecture decisions


## Project: check-print
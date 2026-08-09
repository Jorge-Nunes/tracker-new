# Changelog

Todas as mudanças notáveis neste projeto são documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/).

---

## [Unreleased] - 2026-08-09

### ✨ Adicionado

- **ThemeController** (`tarkan-api/app/Http/Controllers/ThemeController.php`)
  - Novo controller para customização de branding por cliente
  - Endpoints `PUT /theme` e `/theme/upload` para gravar cores, logos e configurações
- **Factory pattern Vuex** (`tarkan-desktop-src/src/store/modules/factory.js`)
  - Módulo reutilizável para criar CRUD genérico no Vuex
  - Reduz duplicação de código nos módulos calendars, commands, drivers, etc.

### 🔧 Modificado

#### Backend (tarkan-api)
- **Controller base** (`app/Http/Controllers/Controller.php`)
  - Novos métodos auxiliares: `traccar()`, `cookieAuth()`, `authedTraccar()`
  - Reduz duplicação de código de autenticação em todos os controllers
- **Refatoração de Controllers**
  - `CommandsController`, `DeviceController`, `DriverController`, `EventController`
  - `GeofenceController`, `LogsController`, `PermissionsController`, `ReportsController`
  - `ServerController`, `SessionController`, `ShareController`, `UserController`
  - `RegisterReportingsController`, `OMC/SessionController`
  - Substituição de `new traccarConnector($request)` por `self::traccar($request)`
  - Substituição de blocos `if($me->status()===200)` por early return com `self::authedTraccar()`
  - Uso de `self::cookieAuth($request)` ao invés de `['h'=>['Cookie'=>$request->headers->get('cookie')]]`
- **UserLog** (`app/Models/UserLog.php`)
  - Novo método estático `record()` para simplificar criação de logs
  - Reduz boilerplate nos controllers (de ~10 linhas para 1 chamada)
- **traccarConnector** (`app/Tarkan/traccarConnector.php`)
  - Limpeza e refatoração do código
- **Imports** removidos em todos os controllers
  - `App\Tarkan\traccarConnector`, `Illuminate\Support\Str`, `Ramsey\Uuid\Uuid`

#### Frontend (tarkan-desktop-src)
- **devices.internal.vue**
  - Correção de fotos de dispositivos: `new Image()` substituído por `fetch HEAD`
  - Elimina erros 404 na console do navegador para devices sem foto
  - Verifica existência da imagem antes de renderizar
- **Paginate.vue** (`src/components/base/Paginate.vue`)
  - Componente de paginação melhorado
- **traccarConnector.js** (`src/tarkan/traccarConnector/traccarConnector.js`)
  - Atualização e limpeza do código
- **Módulos Vuex** atualizados:
  - `calendars.js`, `commands.js`, `computedAttributes.js`, `devices.js`
  - `drivers.js`, `groups.js`, `maintenance.js`, `shares.js`, `users.js`
  - Refatorados para usar factory pattern
- **kore-map.vue** (`src/tarkan/components/kore-map.vue`)
  - Ajustes no seletor de mapas e处理 de erros
- **Views atualizadas**:
  - `edit-calendars.vue`, `edit-drivers.vue`, `edit-notification.vue`
  - `edit-notifications.vue`, `log-objects.vue`, `tab-groups.vue`

#### Idiomas
- **pt-BR.js**: Atualização de traduções
- **en-US.js**: Atualização de traduções
- **es-ES.js**: Atualização de traduções

### 🐛 Corrigido

- **Console errors no navegador**
  - Fotos de dispositivos 404 não poluem mais a console
  - Uso de `fetch HEAD` ao invés de `new Image()` para pre-check silencioso
- **Login quebrado após alteração nginx**
  - `proxy_intercept_errors on` restaurado (necessário para `error_page 405 = @traccar`)
- **Imagens de dispositivos**
  - Criado diretório `storage/app/assets/default/assets/images/` para assets

### 📝 Documentação

- **AGENTS.md** atualizado com:
  - Instruções de build com `ANALYZER_MODE=disabled` para evitar trava no terminal
  - Documentação do novo ThemeController
  - Seção sobre chaves de API externalizadas
- **CHANGELOG.md** criado

---

## [1.0.0] - 2026-08-01

### Inicial

- Versão inicial do monorepo tracker-new
- Backend Laravel 8 (tarkan-api)
- Frontend Vue3 (tarkan-desktop-src)
- Configuração nginx para multi-tenant
